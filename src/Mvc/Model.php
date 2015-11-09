<?php
namespace Easyme\Mvc;

use Easyme\Util\SessionStorage as Store;
use Easyme\DI\Injectable;

abstract class Model extends Injectable{
    
    public static function entityName(){
        return get_called_class();
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
}
