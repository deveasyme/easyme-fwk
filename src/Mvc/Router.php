<?php 


namespace Easyme\Mvc;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

class Router{
    
    
    
    private static $namespace_ph = "([a-zA-Z0-9_-]+)";
    private static $ccu_ph = "([a-zA-Z0-9_-]+)";
    private static $action_ph = "([a-zA-Z0-9_]+)";
    
    private $routes;
    
    public function __construct() {
        
        $namespace = self::$namespace_ph;
        $ccu = self::$ccu_ph;
        $action = self::$action_ph;
        
        $this->add("/",[
            '_ccu' => 'index',
            '_action' => 'index'
        ]);
        
        $this->add("/{_namespace}/{_ccu}/{_action}/{_param}",[],[
            '_namespace' => $namespace,
            '_ccu' => $ccu,
            '_action' => $action
        ]);
        
        $this->add("/{_namespace}/{_ccu}/{_param}",[],[
            '_requirements' => $namespace,
            '_ccu' => $ccu
        ]);
        $this->add("/{_namespace}/{_ccu}",[],[
            '_requirements' => $namespace,
            '_ccu' => $ccu
        ]);
        $this->add("/{_namespace}",[],[
            '_requirements' => $namespace
        ]);
        
        
        $this->add("/{_ccu}/{_action}/{_param}",[],[
            '_ccu' => $ccu,
            '_action' => $action
        ]);
        $this->add("/{_ccu}/{_action}",[],[
            '_ccu' => $ccu,
            '_action' => $action
        ]);
        $this->add("/{_ccu}/{_param}",[],[
            '_ccu' => $ccu
        ]);
        $this->add("/{_ccu}",[],[
            '_ccu' => $ccu
        ]);
        
        $this->add("/{_action}/{_param}",[],[
            '_action' => $action
        ]);
        
        $this->add("/{_action}",[],[
            '_action' => $action
        ]);
    }
    
    public function add($path,$defaults = array(),$requirements=array(),$methods=array()){
        $this->routes[] = [
            'path' => $path,
            'req' => $requirements,
            'def' => $defaults,
            'methods' => $methods
        ];
    }
    
    public function setRoutes($pathToFile){

        $loader = new YamlFileLoader(new FileLocator());
        
        $this->routes = $loader->load($pathToFile);        
        
    }
    
    public function getRoute($uri){
        
        if(!$uri) $uri = '/';
        
        $rc = new RouteCollection();
        $base_route = new Route('/');
        $rc->add('route', $base_route);
        $context = new RequestContext("/");
        
        $matcher = new UrlMatcher($rc, $context);

        
        foreach($this->routes as $_route){
            
            $base_route->setPath($_route['path']);
            $base_route->setDefaults($_route['def']);
            $base_route->setRequirements($_route['req']);
            $base_route->setMethods($_route['methods']);
            
            $route = new Router\Route();
            
            try{
                
                $route_config = $matcher->match($uri);
                
//                print_r($route_config); echo '<BR>';
                
                if(array_key_exists('_namespace', $route_config)){
                    $route->setNamespace($route_config['_namespace']);
                }
                if(array_key_exists('_ccu', $route_config)){
                    $route->setCcuName($route_config['_ccu']);
                }
                if(array_key_exists('_action', $route_config)){
                    $route->setAction($route_config['_action']);
                }
                
                $params = array();
                if(array_key_exists('_param', $route_config)){
                    $params[] = $route_config['_param'];
                }
                foreach($route_config as $k=>$v){
                    if($k[0] == '_') continue;
                        $params[] = $v;
                }
                $route->setParams($params);
                
                if($route->exists())
                    return $route;
                
            }catch(\Exception $ex){
//                echo $ex->getMessage();
            }
            
        }
        
        throw new \Easyme\Error\NotFoundException("Route for $uri not found");
        
        
//        $ccu = explode(":", $route_config["_controller"]);
//        
//        $route = new Router\Route();
//        $route->setCcuName($ccu[sizeof($ccu) - 2]);
//        $route->setAction($ccu[sizeof($ccu) - 1]);
//        if(sizeof($ccu) > 1){
//            $ns = array();
//            for($i = 0 ; $i < sizeof($ccu) - 2 ; $i++){
//                $ns[] = $ccu[$i];
//            } 
//            $ns = implode("\\", $ns);
//            $route->setNamespace($ns);
//        }
//        
//        $params = array();
//        foreach($route_config as $k=>$v){
//            //Ignorando valores default
//            if($k[0]=="_") continue;
//            $params[] = $v;
//        }
//        
//        $route->setParams($params);
//        
//        return $route;
        
    }
    
    
}