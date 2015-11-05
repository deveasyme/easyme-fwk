<?php

namespace Easyme\Mvc\Router;


class Route extends \Easyme\DI\Injectable{
    
    private $namespace;
    
    private $ccuName = 'index';
    
    private $action = 'index';
    
    private $params = array();
    
    
    private $_ccu;
    
    
    public function getNamespace() {
        return $this->namespace;
    }

    public function getCcuName() {
        return $this->ccuName;
    }
    public function getCcuFullName() {
        $ccuName = '\\Ccu\\';
        if($ns = $this->getNamespace()){
            $ccuName .= ucfirst($ns)."\\";
        }
        $ccuName .= ucfirst($this->getCcuName()) . 'Ccu';
        return $ccuName;
    }

    public function getAction() {
        return $this->action;
    }

    public function getParams() {
        return $this->params;
    }

    public function setNamespace($namespace) {
        $this->namespace = $namespace;
    }

    public function setCcuName($ccuName) {
        $this->ccuName = $ccuName;
    }

    public function setAction($action) {
        $this->action = $action;
    }

    public function setParams($params) {
        $this->params = $params;
    }
    
    private function _assembly(){
        
        if(!$this->_ccu){
            
            $ccuName = $this->getCcuFullName();

            if(!class_exists($ccuName)){
                throw new \Exception("Class '$ccuName' not found");
            }
            
            $this->_ccu = new $ccuName;
            
            $action = $this->getAction();
            
            $isRestCall = $this->_ccu instanceof \Easyme\Mvc\CcuRest;
            
            if($isRestCall){
                if($action == 'index') $action = '';
                
                $action = ucfirst($action);
                $method = strtolower($this->request->getMethod());
                $actionName = $method .$action. 'Action';
                
                $this->setAction($method.$action);
            }else{
                
                $actionName = $action.'Action';
            }

            
                
            /*O metodo nao existe*/
            if(!method_exists($ccuName, $actionName)){
                throw new \Exception("Method '$actionName' not found in '$ccuName'");
            }
            
//            if( !$isRestCall ){

                $reflection = new \ReflectionMethod ($ccuName, $actionName);
                $params = $reflection->getParameters();
                $paramsObg = array_filter($params,function($arg){
                    return !$arg->isOptional();
                });

                if( ! (sizeof($this->getParams()) == sizeof($params) || ( (sizeof($this->getParams()) >= sizeof($paramsObg)) && (sizeof($params) > 0) ) )){
                    throw new \Exception("Method '$actionName' not found in '$ccuName'");
                }
//            }


        }
        
        return true;
    }
    
    public function run(){
        $this->_assembly();
        /*Tenta chamar metodo inicializador da classe*/
        if(method_exists($this->_ccu,'init')) $this->_ccu->init();
        
        if( $this->_ccu instanceof \Easyme\Mvc\CcuRest ){
            return call_user_method_array('_dispatch', $this->_ccu,array($this->getAction().'Action',$this->getParams()));
        }else{
            return call_user_method_array($this->getAction().'Action', $this->_ccu, $this->getParams());
        }
    }
    
    public function exists(){
        
        try{
            return $this->test();
        } catch (\Exception $ex) {
//            echo $ex->getMessage();
            return false;
        }
    }
    public function test(){
        return $this->_assembly();
    }
    
    
}
