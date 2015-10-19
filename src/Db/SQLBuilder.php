<?php

namespace Easyme\Db;

use Easyme\Util\Logger;

class Sanitizer {
    
    public static function sanitize($value){
//        return mysql_real_escape_string($value);
        return addslashes($value);
    }
    
    public static function prepareValue($value){

        if( is_array($value) ){
            switch($value[1]){
                case 'string': return "'".self::sanitize ($value[0])."'";
                case 'json' : return "'".self::_toJson($value[0])."'";
                case 'raw'  : return self::sanitize($value[0]);
                default : $value = $value[0]; break;
            }
        }
        
        if ( is_null($value) ) return 'NULL';
        if ( is_numeric($value) ) return self::sanitize($value);
        if ( is_bool($value) ) return $value ? 1 : 0;

        else return "'".self::sanitize ($value)."'";
    }

    
    public static function bind($template,$values){
        
        $reg = "/:\w+:|\?[0-9]+/";
        
        if(preg_match($reg, $template)){
            $str = preg_replace_callback($reg, function($matches) use($values){
                $key = preg_replace("/:|\?/", '', $matches[0]);
                return Sanitizer::prepareValue($values[0][$key]);
            }, $template);
            
        }else{
            
            $values = array_map(array('self','prepareValue'),$values);
            $str = $template;
            foreach($values as $v){
                $str = preg_replace('/\?/', $v, $str,1);
            }
        }
        
        return $str;
    }
    
    
    private static function _toJson($data){
        
        if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
            return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        return json_encode($data);
    }
}

class Condition {
    
    const COND_AND = 1;
    const COND_OR = 2;
    
    private $type;
    private $template;
    private $values;
    
    function __construct($type) {
        $this->type = $type;
    }
    
    public function getType() {
        return $this->type;
    }
    
    public function setTemplate($template) {
        $this->template = $template;
    }

    public function setValues($values) {
        $this->values = $values;
    }
    
    public function assembly(){
        if($this->template && $this->values)
            return '(' . Sanitizer::bind($this->template, $this->values) . ')';   
        return '(' .$this->template . ')';
    }

}


class Statement {
    
    const SELECT = 'SELECT';
    const INSERT = 'INSERT';
    const UPDATE = 'UPDATE';
    const DELETE = 'DELETE';
    const RAW    = 1;
    
    private $type;
    private $multipleInsert = false;
    private $responseFields = array();
    private $selectFields = array();
    private $updateInsertArray = array();
    private $joins = array();
    private $table;
    private $conditions = array();
    private $groupBy = array();
    private $having = array();
    private $orderBy = array();
    private $limit = array();
    
    private $model;
    private $modelsPrefix;
    
    private $raw;
    
    /*Se foi usado um SELECT * */
    private $selectionStar = false;
    
    /*Se a tabela foi renomeada*/
    private $tableRenamed = false;
    
    public function __construct($type) {
        $this->type = $type;
    }
    
    function __clone(){ 
        foreach($this as $name => $value){ 
            if(gettype($value)=='object'){ 
                $this->$name= clone($value); 
            } 
        } 
    } 
    
    function setFromConfigArray(array $config){

        $c = new Condition(Condition::COND_AND);
        $hasCondition = false;
        foreach($config as $k=>$v){
            
            if($k === 0){
                $c->setTemplate($v);
                $hasCondition = true;
            }else if($k === 'bind'){
                $c->setValues(array($v));
            }else if($k === 'limit'){
                if(is_array($v)){
                    $start = $v[0];
                    $end = $v[1];
                }else{
                    $start = $v;
                }
                $this->limit($start,$end);
            }else if($k === 'groupBy'){
                $this->groupBy(array($v));
            }else if($k === 'orderBy'){
                $this->orderBy(array($v));
            }else if($k === 'having'){
                $this->having(array($v));
            }
        }
        if($hasCondition)
            $this->addCondition($c);
    }
    
    public function isInsert(){
        return $this->type == self::INSERT;
    }
    public function isSelect(){
        return $this->type == self::SELECT;
    }
    public function isUpdate(){
        return $this->type == self::UPDATE;
    }
    public function isDelete(){
        return $this->type == self::DELETE;
    }
    
    public function hasSelectionStar(){
        return $this->selectionStar;
    }
    
    public function raw($template,$values){
        $this->raw = Sanitizer::bind($template, $values);
    }
    
    public function getResponseFields() {
        return $this->responseFields;
    }
    
    public function setMultipleInsert($multipleInsert) {
        $this->multipleInsert = $multipleInsert;
    }
    
    public function addSelectField($field, $table = null){
        $field = trim($field);
        if($field == '*' && !$this->selectionStar){
            $this->selectionStar = true;
        }
        $field = !$table ? $field : "$table.$field";
        $this->selectFields[] = $field;
        $this->responseFields[] = preg_replace('/^.+\s+(AS\s)?\s*/i', '', $field);
        
    }
    
    public function addSelectFields($fields, $table = null){
        
        foreach ($fields as $field){
            $this->addSelectField($field,$table);
        }
        
    }
    
    public function setUpdateArray(array $fieldValues){
        
        $this->updateInsertArray = $fieldValues;
    }
    public function setInsertArray(array $fieldValues){
        
        $this->updateInsertArray = $fieldValues;
    }
    
    public function tableRenamed(){
        return $this->tableRenamed;
    }
    
    public function setTable($table){
        
        $table = trim($table);
        
        $this->table = $table;
        
        $this->tableRenamed = preg_match('/^\w+\s+(AS\s)?\s*\w+/i', $table) == 1;
    }
    
    public function addJoin($table,$on,$type = ''){
        
        $this->joins[] = array(
            'table' => $table,
            'on' => $on,
            'type' => $type
        );
    }
    
    public function addCondition(Condition $c){
        $this->conditions[] = $c;
    }
    
    public function groupBy($groupBy){
        $this->groupBy = $groupBy;
    }
    public function having($having){
        $this->having = $having;
    }
    public function orderBy($orderBy){
        $this->orderBy = $orderBy;
    }
    public function limit($start,$end = null){
        
        if($end === null){
            $end = $start;
            $start = 0;
        }
        
        $this->limit = array($start,$end);
        
    }
    
    public function assembly($config = array()){
        
        if($this->type == self::RAW){
            return $this->raw;
        }
        
        /*
         * Caso exista uma configuracao diferente de montagem, clona antes para nao 
          perder os dados originais
         */
        if(!$config || (sizeof($config) == 0) )
            $stat = $this ;
        else {
            $stat = clone $this;
            $stat->setFromConfigArray($config);
        }
        
        $str = $stat->type;
        
        switch($stat->type){
            case self::SELECT: 
                $str .= ' ' . implode(",", $stat->selectFields) . ' FROM ' . $stat->table;
                
                if(sizeof($stat->joins)){
                    $str .= ' '.implode(' ' , array_map(function($join){
                        return "{$join['type']} JOIN {$join['table']} ON {$join['on']}";
                    },$stat->joins));
                }
        
                break;
            
            case self::DELETE:
                $str .= ' FROM ' . $stat->table;
                break;
            
            case self::UPDATE:
                
                $temp = array();
                foreach($stat->updateInsertArray as $field=>$value){
                    $temp[] = $field."=".Sanitizer::prepareValue($value);
                }
                $str .= ' ' . $stat->table . ' SET ' . implode(',', $temp);
                
                break;
                
            case self::INSERT:
                
                if(!$stat->multipleInsert){
                    
                    $fields = implode(',',array_keys($stat->updateInsertArray));

                    $values = '('. implode(',',array_map(array('\Easyme\Db\Sanitizer','prepareValue'),array_values($stat->updateInsertArray))) . ')';
                }else{
                 
                    $fields = implode(',',$stat->updateInsertArray[0]);

                    foreach($stat->updateInsertArray[1] as $v)
                        $temp[] = '(' . implode(',',array_map(array('\Easyme\Db\Sanitizer','prepareValue'),$v)) . ')';

                    $values = implode(',',$temp);   
                }

                $str .= ' INTO ' . $stat->table .' (' .$fields. ') VALUES '. $values;
                
                break;
        }
        
        foreach($stat->conditions as $i=>$cond){
            $str .= ' ';
            if($i > 0){
                $str .= $cond->getType() == Condition::COND_AND ? 'AND ' : 'OR ';
            }else{
                $str .= 'WHERE ';
            }
            $str .= $cond->assembly();
        }
        
        if($stat->type == self::SELECT){
            
            if(sizeof($stat->groupBy) > 0){
                $str .= ' GROUP BY ' .implode(',', $stat->groupBy);
            }
            if(sizeof($stat->having) > 0){
                $str .= ' HAVING ' .implode(',', $stat->having);
            }
            if(sizeof($stat->orderBy) > 0){
                $str .= ' ORDER BY ' .implode(',', $stat->orderBy);
            }
            if(sizeof($stat->limit) > 0){
                $str .= ' LIMIT ' .implode(',', $stat->limit);
            }
        }
                
        return $str;
        
    }
    
    public function setModel($model) {
        $this->model = $model;
    }
    
    public function getModel(){
        if($this->model){
            /*Ja eh uma instancia..*/
            if($this->model instanceof \Easyme\Mvc\Model) return $this->model;
            
            $model = $this->modelsPrefix . $this->model;

            if(class_exists($model) && method_exists($model, 'loadFromDao'))
                return $model;
        }
        return false;
    }
    
    public function setModelsPrefix($prefix){
        $this->modelsPrefix = $prefix;
    }
}



class SQLBuilder {
    
    private $bd;
    
    private $statement;
    
    private $modelPrefix;
    
    private $config = array();
    
    public function __construct(Database $bd = null) {
        
        $this->bd  = $bd ? $bd : new Database() ;
    }
    
    public function sanitize($value){
        $s = new Sanitizer();
        return $s->sanitize($value);
    }
    
    public function config($config){
        $this->config = $config;
        return $this;
    }
    
    public function run($cb = null){
        
        $query = $this->getQuery();
        
        if($this->statement->isSelect()){
            $model = $this->statement->getModel();
            if($model){
                $ds = array();
                $fetchType = $this->statement->hasSelectionStar() || $this->statement->tableRenamed() ? MYSQLI_ASSOC : MYSQLI_NUM;
                $this->bd->query($query,function($d) use($model,&$ds,$fetchType,$cb){          
                    $v = $fetchType == MYSQLI_NUM ? array_combine($this->statement->getResponseFields(), $d) : $d;
                    $obj = $model::loadFromDao($v);
                    if(is_callable($cb)) $cb($obj);
                    $ds[] = $obj;
                },$fetchType);
                
                return $ds;
            }
            
            if($cb){
                return $this->bd->query($query,$cb);
            }
        }
        
        $r = $this->bd->query($query);
        
        if($r === FALSE){
            $log = new Logger();
            $log->log('SQLBuilder', array(
                'error' => $this->bd->error(),
                'query' => $query
            ));
            throw new \Exception($this->bd->error());
        }
        
        if($this->statement->isInsert()){
            $id = $this->bd->lastInsertID();
            
            $model = $this->statement->getModel();
            
            if($model && $model instanceof \Easyme\Mvc\Model)
                $model->setCodigo($id);
            
            return $id;
        }
        
        return $r;
    }
    
    public function getQuery(){
        return $this->statement->assembly($this->config);
    }
    
    public function raw(){
        
        $this->config = array();
        $this->statement = new Statement(Statement::RAW);
        
        $args = func_get_args();
        $sql = array_shift($args);
        
        $this->statement->raw($sql,$args);
        
        return $this;
    }
    
    public function insert($table,array $fieldValues){
        
        $this->config = array();
        $this->statement = new Statement(Statement::INSERT);
        $this->statement->setTable($table);
        $this->statement->setInsertArray($fieldValues);
        
        return $this;
    }
    
    public function minsert($table,array $fields, array $values){
        
        $this->config = array();
        $this->statement = new Statement(Statement::INSERT);
        $this->statement->setMultipleInsert(true);
        $this->statement->setTable($table);
        $this->statement->setInsertArray(array($fields,$values));
        
        
        return $this;
    }
    
    public function update($table,array $fieldValues){
        
        $this->config = array();
        $this->statement = new Statement(Statement::UPDATE);
        $this->statement->setTable($table);
        $this->statement->setUpdateArray($fieldValues);
        
        return $this;
    }
    
    public function delete($table){
        
        $this->config = array();
        $this->statement = new Statement(Statement::DELETE);
        $this->statement->setTable($table);
        
        return $this;
    }
    
    public function select(){
        
        $this->config = array();
        $this->statement = new Statement(Statement::SELECT);
        
        foreach(func_get_args() as $arg){
            if(is_array($arg)){
                if($arg['fields']){
                    $this->statement->addSelectFields($arg['fields'] , $arg['table']);
                }else{
                    $this->statement->addSelectFields($arg);
                }
            }else{
                $this->statement->addSelectField($arg);
            }
        }
        
        return $this;
    }
    
    public function from($table){
        
        $this->statement->setTable($table);
        
        return $this;
    }
    
    private function _join($table,$on,$type=''){
        
        $this->statement->addJoin($table,$on,$type);
        
        return $this;
    }

    public function join($table,$on){
        return $this->_join($table, $on);
    }
    
    public function leftJoin($table,$on){
        return $this->_join($table, $on,'LEFT');
    }
    
    private function _where($type,$args){
        
        $c = new Condition($type);
        
        $str = array_shift($args);
        
        $c->setTemplate($str);
        $c->setValues($args);
        
        $this->statement->addCondition($c);
        
        return $this;
    }
    
    public function where(){
        return $this->_where(Condition::COND_AND, func_get_args());
    }
    
    public function andWhere(){
        return call_user_func_array(array($this,'where'), func_get_args());
    }
    
    public function orWhere(){
        return call_user_func_array(array($this,'_where'), array(Condition::COND_OR,func_get_args()));
    }

    public function groupBy(){
        $this->statement->groupBy(func_get_args());
        return $this;
    }
    
    public function having(){
        $this->statement->having(func_get_args());
        return $this;
    }
    
    public function orderBy(){
        $this->statement->orderBy(func_get_args());
        return $this;
    }
    
    public function limit($start,$end = null){
        
        $this->statement->limit($start,$end);
        return $this;
    }
    
    /**
     * Associa um objeto ou uma classe
     * @param \Easyme\Mvc\Model | String $model
     * @return \Easyme\Db\SQLBuilder
     */
    public function model($model){
        $this->statement->setModelsPrefix($this->modelPrefix);
        $this->statement->setModel($model);
        return $this;
    }
    
    public function setModelsPrefix($prefix){
        $this->modelPrefix = $prefix;
        return $this;
    }
    
    public function __toString() {
        return $this->getQuery();
    }
    
}


