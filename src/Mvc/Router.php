<?php


namespace Easyme\Mvc;

use Easyme\Util\Request;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Easyme\Mvc\Router\Route;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

use Exception;

class Router{

    const CACHE_PREFIX = 'ROUTE';

    private $context;

    private $cache;

    /**
     * Router constructor.
     */
    public function __construct(Request $request)
    {

        $this->context = new RequestContext();
        $this->context->fromRequest($request->getRequest());

        if (EFWK_IN_PRODUCTION && extension_loaded('redis')) {
            $this->cache = new \Redis();
            $this->cache->connect('127.0.0.1');
        }
    }


    private function setCache($uri, Route $route){
        // Ambientes de desenvolvimento
        if(!$this->cache) return;

        $this->cache->set(self::CACHE_PREFIX . $this->context->getMethod() .$uri , $route->serialize());
    }

    /**
     * @param $uri
     * @return Route|null
     */
    private function getCache($uri){
        // Ambientes de desenvolvimento
        if(!$this->cache) return;

        $cached = $this->cache->get(self::CACHE_PREFIX . $this->context->getMethod() .$uri );

        if(!$cached) return;

        $route = new Route();
        $route->unserialize($cached);
        return $route;
    }


    /**
     *
     * @param string $uri
     * @return \Easyme\Mvc\Router\Route|boolean
     * @throws \Easyme\Error\NotFoundException
     */
    public function getRoute($uri){

        if($route = $this->getCache($uri)){
            return $route;
        }


        if(!$uri) $uri = '/';

        if($uri != '/'){
            $uri = preg_replace('/\/+$/', '', $uri);
        }

        $parts = $uri == '/' ? [''] : explode('/', $uri);

        $path = "";
        $searchDirs = [];

        foreach($parts as $i=>$part){

            $part = ucfirst($part);
            $relPath = $i > 0 ? "$path/$part" : "";
            $absPath = EFWK_APP_DIR . $relPath;

            if(is_dir($absPath) && file_exists($absPath.'/routes.yml')){

                array_unshift($searchDirs, [
                    'path' => $absPath,
                    'namespace' => $relPath
                ]);
            }
            $path .= $i > 0 ? "/$part" : $part;
        }


        foreach($searchDirs as $dir){

            try{

                $locator = new FileLocator($dir['path']);
                $loader = new YamlFileLoader($locator);
                $collection = $loader->load('routes.yml');

//                echo '<pre>';
//                foreach($collection as $route){
//                    print_r($route->serialize());
//                }
//                    die();
//                $collection->

                $matcher = new UrlMatcher($collection, $this->context);

                $parameters = $matcher->match($uri);
//
//                // Adicionando na cache
//                foreach($collection as $name => $route){
//                    if($name == $parameters['_route']){
//                        $symfonyRoute = $route;
//                        break;
//                    }
//                }

                $route = $this->_getRoute($parameters, $dir['namespace']);

                $this->setCache($uri , $route);

                return $route;

            } catch (Exception $ex) {
                // Sem saida..
            }

        }

        throw new \Easyme\Error\NotFoundException("Route for $uri not found");

    }

    /**
     *
     * @param type $parameters
     * @return Route
     */
    private function _getRoute($parameters , $namespace = ''){

        $route = new Route();

        $ccu = explode(":", $parameters['_ccu']);

        // A ultima posicao do array eh sempre o metodo a ser executado
        $route->setAction(array_pop($ccu));
        // A proxima, eh sempre o nome da ccu
        $route->setCcu(array_pop($ccu));

        $route->setNamespace(  $namespace . '/' . implode('/', $ccu) );

        foreach($parameters as $k=>$v){
            if($k[0] != '_'){
                $route->setParam($k,$v);
            }
        }

        return $route;
    }

}