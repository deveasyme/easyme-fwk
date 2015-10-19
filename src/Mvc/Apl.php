<?php

namespace Easyme\Mvc;

abstract class Apl extends \Easyme\DI\Injectable{
    
    protected $dao;

    public function __construct( $createDao = true ) {
        
        parent::__construct();
        
        if($createDao){
            $aplName = get_class($this);

            /*Removendo o Apl do inicio*/
            $aplName = substr_replace($aplName, 'Dao', -3);
            /*Removendo o Apl do final*/
            $aplName = substr_replace($aplName, 'Dao', 0, 3);

            if(class_exists($aplName)){
                $this->dao = new $aplName;
            }
        }
    }
}