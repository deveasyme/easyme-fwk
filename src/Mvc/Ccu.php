<?php
namespace Easyme\Mvc;

use Exception;
use Easyme\DI\Injectable;

/**
 * @property-read Apl $apl Related apl
 */
abstract class Ccu extends Injectable{
        
    private $apl;
    
    public function __get($name) {
        
        if($name == 'apl'){
            
            if($this->apl) return $this->apl;
            
            $ccuName = explode('\\', get_called_class());

            /*Removendo o primeiro termo (Ccu\\)*/
            array_shift($ccuName);

            $name = end($ccuName);//[sizeof($ccuName) - 1];
            /*Nome base da classe. Ex: AdminCcu = Admin*/
            $name = substr($name,0,strrpos($name,'Ccu'));

            /*Nome base da Apl*/
            $ccuName[sizeof($ccuName) - 1] = $name.'Apl';

            $aplName = 'Apl\\'.implode('\\',$ccuName);
            /*Existe Apl com este nome?*/
            if(class_exists($aplName)){
                return $this->apl = new $aplName;
            }
            
            throw new Exception("$aplName does not exists");
            
        }
        
        return parent::__get($name);
    }


}