<?php

namespace Easyme\Mvc;

class Application{
    
    private $onShutDownHandler;
    
    private $di;
    
    public function __construct(\Easyme\DI\Injector $di) {
        
        $this->di = $di;
        
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
    
    public function run(){
        
        $route = $this->di->router->getRoute($this->di->request->getQuery('_url'));
        $this->di->dispatcher->addRoute($route);
        $this->di->dispatcher->start();
    }
    
    public function getOnShutDownHandler() {
        return $this->onShutDownHandler;
    }

    public function setOnShutDownHandler($onShutDownHandler) {
        $this->onShutDownHandler = $onShutDownHandler;
    }


    
}

?>
