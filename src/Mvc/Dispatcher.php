<?php

namespace Easyme\Mvc;

use Easyme\Util\Response;
use Exception;
use Easyme\Mvc\ResourceInterface;
use Easyme\Mvc\Router\Route;


class Dispatcher extends \Easyme\DI\Injectable implements \Easyme\Events\EventsAwareInterface{
    
    private $_eventManager;
    
    private $_forwarded;
    
    private $_routes;
    private $_currentRoute;
    
    private $forwardsCount = 0;
    
    private $onExceptionHandler;
    
    public function start(){
        
        while(sizeof($this->_routes) > 0){
            
            $this->_currentRoute = array_shift($this->_routes);
            
            $this->view->reset();

            /*Executa o controlador associado a rota*/
            $this->_forwarded = false;
            
            try {
                
                $resp = $this->_currentRoute->run();
            
                if(!$this->_forwarded){ 
                    
                    if($resp instanceof ResourceInterface){
                        $this->response->setJsonContent($resp->toArray());
                    }
                    else if(is_array($resp)){

                        if($resp[0] instanceof ResourceInterface){
                            $this->response->setJsonContent(array_map(function(ResourceInterface $resource){
                                return $resource->toArray();
                            }, $resp));
                        }else{
                            $this->response->setJsonContent($resp);
                        }

                    }
                    
                    if(!$this->view->isDisabled()){
                        $this->response->setContent($this->view->run());
                    }

                }
                
            } catch (Exception $ex) {
                
                if(is_callable($this->onExceptionHandler)){
                    call_user_func($this->onExceptionHandler, $ex);
                }else{
                    throw $ex;
                }
                
            }
            
        }
    }
    
    public function getNamespace(){
        return $this->_currentRoute->getNamespace();
    }
    public function getCcuName(){
        return $this->_currentRoute->getCcuName();
    }
    public function getAction(){
        return $this->_currentRoute->getAction();
    }
    public function getParams(){
        return $this->_currentRoute->getParams();
    }
    
    /**
     * 
     * @return Route
     */
    public function getRoute(){
        return $this->_currentRoute;
    }
    
    public function addRoute(Route $route){
        $this->_routes[] = $route;
    }

    public function forward($config){
        
        if(++$this->forwardsCount > 10){
            throw new Exception("Possible infinite loop detected.");
        }
        
//        $route = new Route;
//        
//        if(is_array($config)){
//            
//            if($config['namespace'])
//                $route->setNamespace ($config['namespace']);
//            if($config['ccu'])
//                $route->setCcu($config['ccu']);
//            if($config['action'])
//                $route->setAction($config['action']);
//            if($config['params'])
//                $route->setParams($config['params']);
//            
//            /*Dispara uma excecao caso nao exista*/
//            $route->test();
//
//        }else{
//            $route = $this->router->getRoute(url);
//        }
//        $this->_forwarded = true;
//        $this->addRoute($route);
        
    }
    
    public function onException($callable){
        $this->onExceptionHandler = $callable;
    }
    
    public function getEventManager() {
        
    }

    public function setEventManager(\Easyme\Events\Manager $eventManager) {
        
    }

}
