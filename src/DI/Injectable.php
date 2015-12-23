<?php

namespace Easyme\DI;

use Easyme\Mvc\View;
use Easyme\Mvc\Dispatcher;
use Easyme\Db\Database;
use Easyme\Util\Request;
use Easyme\Util\Response;
use Easyme\Util\SessionStorage;
use Easyme\Util\Logger;
use Easyme\Util\Flash;
use Easyme\Util\Date;


/**
 * @property View $view
 * @property Dispatcher $dispatcher
 * @property Database $db
 * @property Request $request
 * @property Response $response
 * @property SessionStorage $session
 * @property Logger $logger
 * @property Flash $flash
 */
abstract class Injectable {
    
    private static $di;
    
    public static function setDi(Injector $di){
        self::$di = $di;
    }
    
    public function __get($name) {
        return $this->getDi()->get($name);
    }
    
    public function __set($name,$definition) {
        $di = $this->getDi();
        
        $shared = false;
        if($di->has($name)){
            $shared = $di->getService($name)->getIsShared();
        }
        return $di->set($name, $definition, $shared);
    }
    
    /**
     * @return Injector
     */
    public static function getInstance(){
        return self::$di ?: self::create();
    }
    
    /**
     * @return Injector
     */
    public static function create(){
        
        $di = new Injector();

        $di->set('dispatcher',function(){
            return new Dispatcher();
        },true);

        $di->set('view',function(){
            return new View();
        },true);
        $di->set('db',function(){
            
            return new Database();
            
        },true);

        $di->set('request',function(){
            $request = new Request();
            return $request;
        },true);

        $di->set('response',function(){
            $response = new Response();

            return $response;
        },true);

        $di->set('session',function(){
           return new SessionStorage(); 
        },true);
        $di->set('logger',function(){
            $log = new Logger();
            return $log;
        },true);


        $di->set('flash',function(){
            return new Flash();
        },true);
        
        return $di;
        
    }
    
    /**
     * @return Injector
     */
    public function getDi(){

        if(!self::$di) self::$di = self::create();

        return self::$di;
    }

    
}