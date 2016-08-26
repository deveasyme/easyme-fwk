<?php

namespace Easyme\Mvc\Router;

use \Easyme\Error\NotFoundException;


class Route implements \Serializable{


    private $namespace;

    private $ccu;

    private $action;

    private $params = array();

    /**
     * O caminho onde esta a Ccu
     * @var string
     */
    private $ccuPath;

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize([
            'namespace' => $this->getNamespace(),
            'ccu' => $this->getCcu(),
            'params' => $this->getParams(),
            'action' => $this->getAction()
        ]);
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        $this->namespace = $data['namespace'];
        $this->ccu = $data['ccu'];

        if(isset($data['params'])){
            $this->params = $data['params'];
        }
        if(isset($data['action'])) {
            $this->action = $data['action'];
        }
    }


    public function getNamespace() {
        return $this->namespace;
    }

    public function getCcu() {
        return $this->ccu;
    }

    public function getAction() {
        return $this->action;
    }

    public function getParams() {
        return $this->params;
    }

    public function setNamespace($ccuPath) {
        $this->ccuPath = $ccuPath;
        // Removendo /`s no inicio da string
        $this->namespace = preg_replace("/^\/*/", '', $ccuPath);
        // Removendo /`s no final da string
        $this->namespace = preg_replace("/\/*$/", '', $ccuPath);
    }

    public function setCcu($ccu) {
        $this->ccu = $ccu;
    }

    public function setAction($action) {
        $this->action = $action;
    }

    public function setParams($params) {
        $this->params = $params;
    }

    public function getParam($key){
        return $this->params[$key];
    }

    public function setParam($key,$value){
        return $this->params[$key] = $value;
    }

    public function run(){

        $ns = $this->getNamespace();

        /**
         * Converte uma string abc/def/ghi em Abc\\Def\\Ghi
         */
        $ns = implode('\\',array_map(function($part){
            return ucfirst($part);
        },explode('/', $ns)));

        /**
         * Converte dfg em DfgCcu
         */
        $ccu = ucfirst($this->getCcu()).'Ccu';

        $action = strtolower($this->getAction()).'Action';

        $params = $this->getParams();

        $className = $ns.'\\'.$ccu;

        if(!class_exists($className))
            throw new NotFoundException("Class $className not found");

        $instance = new $className;

        if(method_exists($instance, 'init')){
            if($instance->init() === FALSE){
                return;
            }
        }

        if(!method_exists($instance,$action)){
            throw new NotFoundException("Method $className::$action not found");
        }

        return call_user_method_array($action, $instance, $params);

    }

    public function getCcuPath() {
        return $this->ccuPath;
    }




}
