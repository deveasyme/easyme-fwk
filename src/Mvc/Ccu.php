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

            $aplName = preg_replace("/Ccu$/", "Apl", get_called_class());
            
            /*Existe Apl com este nome?*/
            if(class_exists($aplName)){
                return $this->apl = new $aplName;
            }
            
            throw new Exception("$aplName does not exists");
            
        }
        
        return parent::__get($name);
    }


}