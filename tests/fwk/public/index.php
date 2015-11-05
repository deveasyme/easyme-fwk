<?php


//Variavel apenas para o ambiente de desenvolvimento da framework
$testing = true;
require_once __DIR__.'/../../../src/autoloader.php';

try{
    $app = new Easyme\Mvc\Application();
    $app->run();
} catch (\Exception $ex) {
    
    echo "<p style='background:red;color:#fff;'>".$ex->getMessage().'</p>';
    
}