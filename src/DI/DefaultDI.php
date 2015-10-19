<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Easyme\DI;

/**
 * Description of DefaultDI
 *
 * @author Binow
 */
class DefaultDI {
    
    public static function getDI(){
        
        $di = new Injector();
            
        $di->set('router',function(){
            $router = new \Easyme\Mvc\Router();
            return $router;

        },true);

        $di->set('dispatcher',function(){
            return new \Easyme\Mvc\Dispatcher();
        },true);

        $di->set('view',function(){
            return new \Easyme\Mvc\View();
        },true);
        $di->set('db',function(){
            return new \Easyme\Db\Database();
        },true);

        $di->set('request',function(){
            $request = new \Easyme\Util\Request();
            return $request;
        },true);

        $di->set('response',function(){
            $response = new \Easyme\Util\Response();

            return $response;
        },true);

        $di->set('session',function(){
           return new \Easyme\Util\SessionStorage(); 
        },true);
        $di->set('logger',function(){
            $log = new \Easyme\Util\Logger();
            return $log;
        },true);


        $di->set('flash',function(){
            return new \Easyme\Util\Flash();
        },true);
        
        return $di;
    }
    
}
