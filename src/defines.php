<?php

if(!defined(EFWK_ROOT_DIR))
    define(EFWK_ROOT_DIR,$_SERVER['DOCUMENT_ROOT']);

define(EFWK_VENDOR_DIR,$testing ? __DIR__.'/../vendor' : EFWK_ROOT_DIR.'/vendor');

define(EFWK_CONFIG_DIR,EFWK_ROOT_DIR.'/config');

define(EFWK_LOGS_DIR,EFWK_ROOT_DIR.'/logs');

define(EFWK_PROXIES_DIR,EFWK_ROOT_DIR.'/proxies');

define(EFWK_UPLOADS_DIR,EFWK_ROOT_DIR.'/uploads');

define(EFWK_APP_DIR,EFWK_ROOT_DIR.'/server');

define(EFWK_PUBLIC_DIR,EFWK_ROOT_DIR.'/public');

$ini = parse_ini_file(EFWK_CONFIG_DIR.'/app.ini',true);
define(EFWK_IN_PRODUCTION,!!$ini['Info']['production']);

$db = parse_ini_file(EFWK_CONFIG_DIR.'/database.ini',true);
$key = EFWK_IN_PRODUCTION ? 'production' : 'development';
define(EFWK_DB_HOST, $db[$key]['host']);
define(EFWK_DB_USER, $db[$key]['user']);
define(EFWK_DB_PASSWORD, $db[$key]['password']);
define(EFWK_DB_DATABASE, $db[$key]['database']);


