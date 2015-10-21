<?php

namespace Easyme\Db;

class Filter{
    
    private $filters = array();
    
    
    public function __get($name) {
        return $this->filters[$name];
    }
    
    public function __set($name, $value) {
        $this->filters[$name] = $value;
    }
    
    public function has($key){
        return array_key_exists($key, $this->filters) &&
               !!$this->filters[$key];
    }

}