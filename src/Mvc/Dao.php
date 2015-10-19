<?php

namespace Easyme\Mvc;

use Easyme\Db\Database,
    Easyme\Db\SQLBuilder;

class Dao extends \Easyme\DI\Injectable{
    
//    protected $bd;
    protected $sql;

    private $fields = array();
    
    public function __construct() {
        parent::__construct();
        
//        $this->setBd( $bd ? $bd : new Database() );
        $this->sql = new SQLBuilder($this->db);
        $this->sql->setModelsPrefix('Model\\');
        
        $childClass = get_called_class();
        foreach(get_class_vars($childClass) as $f=>$v){
            if(!property_exists(__CLASS__, $f)){
                $tableName = '';
                $const = "$childClass::TABLE_NAME";
                if(defined($const)){
                    $tableName = constant($const) . '.';
                }
                $this->$f = $tableName.$f;
                $this->fields[] = $tableName.$f;
            }
        }
        
    }

    protected function getFields(){
                
        return array_diff($this->fields, func_get_args());
    }
    
//    public function getBd() {
//        return $this->bd;
//    }
//
//    public function setBd($bd) {
//        $this->bd = $bd;
//        $this->sql = new SQLBuilder($this->bd);
//        $this->sql->setModelsPrefix('Model\\');
//    }
        
//    public function begin(){
//        return $this->bd->begin();
//    }
//    
//    public function rollback(){
//        return $this->bd->rollback();
//    }
//    
//    public function commit(){
//        return $this->bd->commit();
//    }
}
