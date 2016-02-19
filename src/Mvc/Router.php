<?php 


namespace Easyme\Mvc;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Easyme\Mvc\Router\Route;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

use Exception;

class Router{
    
    /**
     * 
     * @param string $uri
     * @return \Easyme\Mvc\Router\Route|boolean
     * @throws \Easyme\Error\NotFoundException
     */
    public function getRoute($uri){
        
        if(!$uri) $uri = '/';
        
        $parts = $uri == '/' ? [''] : explode('/', $uri);
        
        $path = "";
        $searchDirs = [];

        foreach($parts as $i=>$part){
            
            
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
        
        $context = new RequestContext();
        $context->fromRequest(SymfonyRequest::createFromGlobals());
        
        foreach($searchDirs as $dir){

            try{
                
                $locator = new FileLocator($dir['path']);
                $loader = new YamlFileLoader($locator);
                $collection = $loader->load('routes.yml');

                $matcher = new UrlMatcher($collection, $context);

                $parameters = $matcher->match($uri);
                
                $route = new Route();
                
                $ccu = explode(":", $parameters['_ccu']);
                
                // A ultima posicao do array eh sempre o metodo a ser executado
                $route->setAction(array_pop($ccu));
                // A proxima, eh sempre o nome da ccu
                $route->setCcu(array_pop($ccu));
                
                $route->setNamespace(  $dir['namespace'] . '/' . implode('/', $ccu) );
                
                foreach($parameters as $k=>$v){
                    if($k[0] != '_'){
                        $route->setParam($k,$v);
                    }
                }
                
                return $route;
                
            } catch (Exception $ex) {
                // Sem saida..
            }
            
        }
        
         throw new \Easyme\Error\NotFoundException("Route for $uri not found");
        
    }
    
    
}