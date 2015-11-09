<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Easyme\Db;

use Doctrine\ORM\AbstractQuery;

/**
 * Description of SqlConfig
 *
 * @author Binow
 */
abstract class SqlConfig {
    
    const RESULT_AS_ARRAY = AbstractQuery::HYDRATE_ARRAY;
    const RESULT_AS_OBJECT = AbstractQuery::HYDRATE_OBJECT;
    const RESULT_AS_SCALAR = AbstractQuery::HYDRATE_SCALAR;
    const RESULT_AS_SINGLE_SCALAR = AbstractQuery::HYDRATE_SINGLE_SCALAR;
    const RESULT_AS_SIMPLE_OBJECT = AbstractQuery::HYDRATE_SIMPLEOBJECT;
    
    /**
     *
     * @var Filter
     */
    public $filter;
    
    private $resultAs = self::RESULT_AS_OBJECT;
    
    function __construct() {
        $this->filter = new Filter();
    }
    
    protected function setFilter($name , $value){
        $this->filter->$name = $value;
    }

    public function hasFilter($name){
        return $this->filter->has($name);
    }
    
    public function resultAs($resultAs){
        $this->resultAs = $resultAs;
    }
    public function getResultAs(){
        return $this->resultAs;
    }
    
}
