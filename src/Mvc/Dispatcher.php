<?php

namespace Easyme\Mvc;

class Dispatcher extends \Easyme\DI\Injectable implements \Easyme\Events\EventsAwareInterface{
    
    private $_eventManager;
    
    private $_forwarded;
    
    private $_routes;
    private $_currentRoute;
    
    public function start(){
        
        while(sizeof($this->_routes) > 0){
            
            $this->_currentRoute = array_shift($this->_routes);
            
            
            $this->view->reset();

            $this->view->setRoot($this->_currentRoute->getCcuName());
            $this->view->setDefaultContent($this->_currentRoute->getAction());
            /*Executa o controlador associado a rota*/
            $this->_forwarded = false;
            $resp = $this->_currentRoute->run();
            
            if(!$this->_forwarded){
                
                $hasResp = $resp instanceof \Easyme\Util\Response;
                if( $hasResp ) $this->response = $resp;

                $this->response->sendHeaders();

                if( !$hasResp ){
                    $this->view->run();
                }

                //Executo views associadas
                $this->response->sendContent();
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
    
    public function addRoute(Router\Route $route){
        $this->_routes[] = $route;
    }

    public function forward($config){
        
        $route = new Router\Route;
        
        if(is_array($config)){
            
            if($config['namespace'])
                $route->setNamespace ($config['namespace']);
            if($config['ccu'])
                $route->setCcuName($config['ccu']);
            if($config['action'])
                $route->setAction($config['action']);
            if($config['params'])
                $route->setParams($config['params']);
            
            /*Dispara uma excecao caso nao exista*/
            $route->test();

        }else{
            $route = $this->router->getRoute(url);
        }
        $this->_forwarded = true;
        $this->addRoute($route);
        
    }
    
    public function getEventManager() {
        
    }

    public function setEventManager(\Easyme\Events\Manager $eventManager) {
        
    }

}
