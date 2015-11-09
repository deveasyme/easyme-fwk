<?php

require_once __DIR__.'/defines.php';

$loader = require_once EFWK_VENDOR_DIR.'/autoload.php';

$files = scandir(EFWK_APP_DIR);

foreach($files as $filename){
    if( is_dir(EFWK_APP_DIR."/$filename") && !in_array($filename, ['.','..']) && $filename[0] != '_' ){
        $loader->addPsr4(ucfirst($filename).'\\',EFWK_APP_DIR.'/'.$filename);
    }
}

//$paths = array(
//    'Ccu',
//    'Dao',
//    'Apl',
//    'Model',
//    'Libs',
//);
//
//foreach($paths as $path){
//    $loader->addPsr4($path.'\\',EFWK_APP_DIR.'/'.$path);
//}