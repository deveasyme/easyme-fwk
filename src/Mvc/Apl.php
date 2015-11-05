<?php

namespace Easyme\Mvc;

use Easyme\Mvc\Dao;

/**
 * @property Dao $dao 
 */
abstract class Apl extends \Easyme\DI\Injectable{
    
    private $dao;
    
    public function __get($name) {
        
        if($name == 'dao'){
            
            if($this->dao) return $this->dao;
            
            $aplName = get_class($this);

            /*Removendo o Apl do inicio*/
            $aplName = substr_replace($aplName, 'Dao', -3);
            /*Removendo o Apl do final*/
            $aplName = substr_replace($aplName, 'Dao', 0, 3);

            if(class_exists($aplName)){
                return $this->dao = new $aplName;
            }
            
            throw new Exception("$aplName does not exists");
            
        }
        
        return parent::__get($name);
    }
}