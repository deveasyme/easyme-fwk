<?php

namespace Easyme\Mvc;

use Exception;
use Easyme\Error\FatalErrorException;
use Easyme\DI\Injector;
use Easyme\DI\Injectable;

class Application extends Injectable{
    
    private $onShutDownHandler;
    
    public $router;
    
    public function __construct() {
        
        $this->router = new Router();
        
    }
    
    /**
     * Executa a rota 
     */
    public function run(){
        
        register_shutdown_function(function(){
            
            $error = error_get_last();

            if(($error['type'] === E_ERROR) || ($error['type'] === E_USER_ERROR)){
                $this->dispatcher->handleException(new FatalErrorException($error));
            }

        });
        
        
        try{
            $route = $this->router->getRoute($this->request->getQuery('_url'));
            $this->dispatcher->addRoute($route);
            $this->dispatcher->start();
        } catch (Exception $ex) {
            $this->dispatcher->handleException($ex);
        }
    }
    
    /**
     * Imprime as saidas (cabecalho + corpo) 
     */
    public function flush(){
        $this->response->sendHeaders();
        $this->response->sendContent();
    }
    
}