<?php

require_once __DIR__.'/../config/defines.php';

require_once EFWK_CONFIG_DIR.'/autoloader.php';

try{
    $di = \Easyme\DI\DefaultDI::getDI();
    
//    $di->router->setRoutes( EFWK_CONFIG_DIR.'/routes.yml' );

    
    $di->router->add('/categorias/{idCategorias}/faturas/{idFatura}',[
        '_ccu' => 'categorias',
        '_action' => 'get'
    ],[],['GET']);
    
    $app = new Easyme\Mvc\Application($di);
    
    $app->run();
} catch (\Exception $ex) {
    
    echo "<p style='background:red;color:#fff;'>".$ex->getMessage().'</p>';
    
}