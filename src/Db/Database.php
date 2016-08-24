<?php

namespace Easyme\Db;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

use Doctrine\ORM\Decorator\EntityManagerDecorator;
use Doctrine\Common\Proxy\AbstractProxyFactory;

class Database extends EntityManagerDecorator{

    public function __construct($conn = null) {

        if (EFWK_IN_PRODUCTION && extension_loaded('redis')) {
            $redis = new \Redis();
            $redis->connect('127.0.0.1');
            $cache = new \Doctrine\Common\Cache\RedisCache();
            $cache->setRedis($redis);
        }

        $config = Setup::createAnnotationMetadataConfiguration([], !EFWK_IN_PRODUCTION, EFWK_PROXIES_DIR, $cache);


        $config->addCustomStringFunction('COLLATE' , '\Easyme\Db\CollateFunction');

        if(EFWK_IN_PRODUCTION){
            $config->setAutoGenerateProxyClasses(AbstractProxyFactory::AUTOGENERATE_FILE_NOT_EXISTS);
        }

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