<?php

namespace Easyme\Db;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

use Doctrine\ORM\Decorator\EntityManagerDecorator;

class Database extends EntityManagerDecorator{

    public function __construct($conn = null) {
        
        $config = Setup::createAnnotationMetadataConfiguration([EFWK_MODEL_DIR], !EFWK_IN_PRODUCTION, EFWK_PROXIES_DIR);
        
        $default = [
            'driver'   => 'pdo_mysql',
            'user'     => EFWK_DB_USER,
            'password' => EFWK_DB_PASSWORD,
            'dbname'   => EFWK_DB_DATABASE,
            'host'     => EFWK_DB_HOST,
            'charset'  => 'utf8'
        ];
        parent::__construct(EntityManager::create($conn ? array_merge($default,$conn) : $default, $config));
        
    }
    
    public function commit(){
        $this->getConnection()->commit();
    }
    public function rollback(){
        $this->getConnection()->rollback();
    }
    
    
}