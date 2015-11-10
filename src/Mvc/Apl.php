<?php

namespace Easyme\Mvc;

use Easyme\Mvc\Dao;
use Exception;

/**
 * @property Dao $dao 
 */
abstract class Apl extends \Easyme\DI\Injectable{
    
    private $dao;
    
    public function __get($name) {
        
        if($name == 'dao'){
            
            if($this->dao) return $this->dao;
            
            $daoName = preg_replace("/Apl$/", "Dao", get_called_class());

            if(class_exists($daoName)){
                return $this->dao = new $daoName;
            }
            
            throw new Exception("$daoName does not exists");
            
        }
        
        return parent::__get($name);
    }
}