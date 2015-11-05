<?php

namespace Easyme\Db;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use \Doctrine\ORM\Configuration;
use \Doctrine\Common\EventManager;
use Doctrine\ORM\ORMException;
use Doctrine\DBAL\DriverManager;

use Doctrine\ORM\Decorator\EntityManagerDecorator;

class Database extends EntityManagerDecorator{

    public function __construct($conn = null) {
        
        $config = Setup::createAnnotationMetadataConfiguration([EFWK_MODEL_DIR], false);
        
        parent::__construct(EntityManager::create($conn ?: [
            'driver'   => 'pdo_mysql',
            'user'     => EFWK_DB_USER,
            'password' => EFWK_DB_PASSWORD,
            'dbname'   => EFWK_DB_DATABASE,
            'host'     => EFWK_DB_HOST
        ], $config));
        
    }
    
//    public function __get($name) {
//        
//        if($name == 'query'){
//            
//        }
//        
//    }

    
}