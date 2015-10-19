<?php

$loader = require_once EFWK_VENDOR_DIR.'/autoload.php';

$paths = array(
    'Ccu',
    'Dao',
    'Apl',
    'Model',
    'Libs',
);

foreach($paths as $path){
    $loader->addPsr4($path.'\\',EFWK_APP_DIR.'/'.$path);
}