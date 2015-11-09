<?php

namespace Easyme\Mvc\Router;

use \Easyme\Error\NotFoundException;

class Route {
    
    private $namespace;
    
    private $ccu;
    
    private $action;
    
    private $params = array();
    
    /**
     * O caminho onde esta a Ccu
     * @var string
     */
    private $ccuPath;

    public function getNamespace() {
        return $this->namespace;
    }

    public function getCcu() {
        return $this->ccu;
    }

    public function getAction() {
        return $this->action;
    }

    public function getParams() {
        return $this->params;
    }

    public function setNamespace($ccuPath) {
        $this->ccuPath = $ccuPath;
        // Removendo /`s no inicio da string
        $this->namespace = preg_replace("/^\/*/", '', $ccuPath);
        // Removendo /`s no final da string
        $this->namespace = preg_replace("/\/*$/", '', $ccuPath);
    }

    public function setCcu($ccu) {
        $this->ccu = $ccu;
    }

    public function setAction($action) {
        $this->action = $action;
    }

    public function setParams($params) {
        $this->params = $params;
    }

    public function getParam($key){
        return $this->params[$key];
    }
    
    public function setParam($key,$value){
        return $this->params[$key] = $value;
    }
    
    public function run(){
        
        $ns = $this->getNamespace();
        
        /**
         * Converte uma string abc/def/ghi em Abc\\Def\\Ghi
         */
        $ns = implode('\\',array_map(function($part){
            return ucfirst($part);
        },explode('/', $ns)));
        
        /**
         * Converte dfg em DfgCcu
         */
        $ccu = ucfirst($this->getCcu()).'Ccu';
        
        $action = strtolower($this->getAction()).'Action';
        
        $params = $this->getParams();
        
        $className = $ns.'\\'.$ccu;
        
        if(!class_exists($className))
            throw new NotFoundException("Class $className not found");
        
        $instance = new $className;
        
        if(method_exists($instance, 'init')){
            if($instance->init() === FALSE){
                return;
            }
        }
        
        if(!method_exists($instance,$action)){
            throw new NotFoundException("Method $className::$action not found");
        }
        
        return call_user_method_array($action, $instance, $params);
        
    }
    
    public function getCcuPath() {
        return $this->ccuPath;
    }
    
//    public function getNamespace() {
//        return $this->namespace;
//    }
//
//    public function getCcuName() {
//        return $this->ccuName;
//    }
//    public function getCcuFullName() {
//        $ccuName = '\\Ccu\\';
//        if($ns = $this->getNamespace()){
//            $ccuName .= ucfirst($ns)."\\";
//        }
//        $ccuName .= ucfirst($this->getCcuName()) . 'Ccu';
//        return $ccuName;
//    }
//
//    public function getAction() {
//        return $this->action;
//    }
//
//    public function getParams() {
//        return $this->params;
//    }
//
//    public function setNamespace($namespace) {
//        $this->namespace = $namespace;
//    }
//
//    public function setCcuName($ccuName) {
//        $this->ccuName = $ccuName;
//    }
//
//    public function setAction($action) {
//        $this->action = $action;
//    }
//
//    public function setParams($params) {
//        $this->params = $params;
//    }
//    
//    private function _assembly(){
//        
//        if(!$this->_ccu){
//            
//            $ccuName = $this->getCcuFullName();
//
//            if(!class_exists($ccuName)){
//                throw new \Exception("Class '$ccuName' not found");
//            }
//            
//            $this->_ccu = new $ccuName;
//            
//            $action = $this->getAction();
//            
//            $isRestCall = $this->_ccu instanceof \Easyme\Mvc\CcuRest;
//            
//            if($isRestCall){
//                if($action == 'index') $action = '';
//                
//                $action = ucfirst($action);
//                $method = strtolower($this->request->getMethod());
//                $actionName = $method .$action. 'Action';
//                
//                $this->setAction($method.$action);
//            }else{
//                
//                $actionName = $action.'Action';
//            }
//
//            
//                
//            /*O metodo nao existe*/
//            if(!method_exists($ccuName, $actionName)){
//                throw new \Exception("Method '$actionName' not found in '$ccuName'");
//            }
//            
////            if( !$isRestCall ){
//
//                $reflection = new \ReflectionMethod ($ccuName, $actionName);
//                $params = $reflection->getParameters();
//                $paramsObg = array_filter($params,function($arg){
//                    return !$arg->isOptional();
//                });
//
//                if( ! (sizeof($this->getParams()) == sizeof($params) || ( (sizeof($this->getParams()) >= sizeof($paramsObg)) && (sizeof($params) > 0) ) )){
//                    throw new \Exception("Method '$actionName' not found in '$ccuName'");
//                }
////            }
//
//
//        }
//        
//        return true;
//    }
//    
//    public function run(){
//        $this->_assembly();
//        /*Tenta chamar metodo inicializador da classe*/
//        if(method_exists($this->_ccu,'init')) $this->_ccu->init();
//        
//        if( $this->_ccu instanceof \Easyme\Mvc\CcuRest ){
//            return call_user_method_array('_dispatch', $this->_ccu,array($this->getAction().'Action',$this->getParams()));
//        }else{
//            return call_user_method_array($this->getAction().'Action', $this->_ccu, $this->getParams());
//        }
//    }
//    
//    public function exists(){
//        
//        try{
//            return $this->test();
//        } catch (\Exception $ex) {
////            echo $ex->getMessage();
//            return false;
//        }
//    }
//    public function test(){
//        return $this->_assembly();
//    }
    
    
}
