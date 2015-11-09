<?php

namespace Easyme\Mvc;

use Easyme\DI\Injector;
use Easyme\DI\Injectable;

class Application extends Injectable{
    
    private $onShutDownHandler;
    
    private $router;
    
    public function __construct() {
        
        $this->router = new Router();
        
        // Setando DI para a aplicacao
        $that = $this;
        
        register_shutdown_function(function() use ($that){
            
            $error = error_get_last();
                
            if(($error['type'] === E_ERROR) || ($error['type'] === E_USER_ERROR))
            {
                $handler = $that->getOnShutDownHandler();
                if(is_callable($handler)) $handler($error);
            }
        });
        
    }
    
    /**
     * Executa a rota 
     */
    public function run(){
        $route = $this->router->getRoute($this->request->getQuery('_url'));
        $this->dispatcher->addRoute($route);
        $this->dispatcher->start();
    }
    
    /**
     * Imprime as saidas (cabecalho + corpo) 
     */
    public function flush(){
        $this->response->sendHeaders();
        $this->response->sendContent();
    }
    
    public function getOnShutDownHandler() {
        return $this->onShutDownHandler;
    }

    public function setOnShutDownHandler($onShutDownHandler) {
        $this->onShutDownHandler = $onShutDownHandler;
    }
    
}