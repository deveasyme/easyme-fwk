<?php

require_once __DIR__.'/../config/defines.php';

require_once EFWK_CONFIG_DIR.'/autoloader.php';

try{
    $di = \Easyme\DI\DefaultDI::getDI();
    
    $di->router->add("/api/categorias/teste",[
        '_namespace' => 'api',
        '_ccu' => 'categorias',
        '_action' => 'teste',
    ]);
    
//    $di->router->add("/cartoes/{idCartao}/plasticos/{idPlastico}",[
//        '_ccu' => 'categorias',
//        '_action' => 'faturas'
//    ],[ 
//        'idCartao' => '\d+',
//        'idPlastico' => '\d+'
//    ]);
    
    
    $app = new Easyme\Mvc\Application($di);
    
    $app->run();
} catch (\Exception $ex) {
    
    echo "<p style='background:red;color:#fff;'>".$ex->getMessage().'</p>';
    
}