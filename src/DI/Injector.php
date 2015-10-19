<?php

namespace Easyme\DI;

class Injector {
    
    private $services;
    
    private static $default;
    
    public function __construct() {
        $this->services = array();
        self::$default = $this;
    }
    
    public function __get($name) {
        return $this->get($name);
    }
    
    public function __set($name, $value) {
        $this->set($name,$value);
    }
    
    public function __call($name, $args) {
        $prefix = substr($name, 0, 3);
        $name = strtolower(substr($name, 3));
        array_unshift($args, $name);
        return call_user_method_array ($prefix, $this, $args);
    }
    
    public function has($name){
        return array_key_exists($name, $this->services);
    }
    
    public function get($name){
        $args = func_get_args();
        /*Nome do servico*/
        array_shift($args);
        $service = $this->services[$name];
        /*Dependencia nao encontrada*/
        if(is_null($service)) throw new \Exception('Service ' . $name. ' not found');
        /*Resolve o servico*/
        return $service->resolve($args);
    }
    
    public function set($name,$definition,$shared = false){
        $this->services[$name] = new Service($name,$definition,$shared);
    }
    
    public static function getDefault(){
        return self::$default;
    }
    
    public static function setDefault($di){
        self::$default = $di;
    }
    
}

?>
