<?php

namespace Easyme\DI;

class Injectable {
    
    protected $di;
    
    public function __construct() {
        $this->di = Injector::getDefault();
    }
    
    public function __get($name) {
        return $this->di->get($name);
    }
    
    public function setDi(Injector $di){
        $this->di = $di;
    }
    
    public function getDi(){
        return $this->di;
    }
    
}