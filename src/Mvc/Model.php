<?php
namespace Easyme\Mvc;

use Easyme\Util\SessionStorage as Store;

class Model extends \Easyme\DI\Injectable{

    private $codigo;
            
    public function __construct() {
        parent::__construct();
    }
    
    public function getCodigo() {
        return $this->codigo;
    }

    public function setCodigo($codigo) {
        $this->codigo = $codigo;
    }

    public function getId() {
        return $this->codigo;
    }
    
    public function setId($codigo) {
        $this->codigo = $codigo;
    }
    
    public static function loadFromStore(){
        /*Nome completo da classe que chamou o metodo*/
        $classFullName = get_called_class();
        /*Parte final do nome da classe: Model\Usuarios\Login = login*/
        $sesKey = strtolower(end(explode('\\', $classFullName )));
        /*Existe sessao criada*/
        if(Store::bagExists($sesKey)){
            $obj = new $classFullName;
            $ses = new Store($sesKey);  

            $ses->each(function($key,$value) use(&$obj){
                $methodName = 'set' . ucfirst($key);
                if(method_exists($obj, $methodName)){
                    $obj->$methodName($value);
                }
            });
            
            return $obj;
            
        }else{
            throw new \Exception('Não foi possível carregar '.$sesKey.' a partir do Storage.');
        }
    }
    
    public static function getTableName(){
        return null;
    }
    
    /**
     * Recebe o nome de cada campo como parametro
     */
    public static function getFields( ){
        
        $args = func_get_args();
        
        $fields = array();
        
        $formata = end($args) !== false;
        
        foreach($args as $arg){
            if($arg !== false){
                $fields[] = self::getField($arg,$formata);
            }
        }
        
        return $fields;
    }
    
    public static function getField($name,$format = false){
        $classFullName = get_called_class();
        
        $tn = call_user_func(array($classFullName,'getTableName'));
        $field = '';
        if($tn){
            $field = "{$tn}.{$name}";
            if($format) $field .= " as {$tn}_{$name}";
        }
        return $field;
    }
    
    public static function parseField($daoArray,$fieldName){
        $classFullName = get_called_class();
        $tn = call_user_func(array($classFullName,'getTableName'));
        
        if($tn){
            $key = "{$tn}_{$fieldName}";
            if($daoArray[$key]){
                return $daoArray[$key];
            }
        }
        return $daoArray[$fieldName];
    }
    
}

?>
