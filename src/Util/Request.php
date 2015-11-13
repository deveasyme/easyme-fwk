<?php

namespace Easyme\Util;

class Request {
    
    private $server;
    private $request;
    private $sanitizer;
    
    private $files;
    
    private $rawBody;
    
    private $getData  = array();
    private $postData = array();
    private $putData  = array();
    
    public function __construct() {
        $this->server = $_SERVER;
        $this->request = $_REQUEST;
        $this->files = $_FILES;
        $this->sanitizer = new Sanitizer();
        
//        if(isset($_SERVER["CONTENT_TYPE"]) && ) {
//            $this->postData = 
//        }
        
        $this->rawBody = trim(file_get_contents('php://input'));
        
        $this->getData = $_GET;
        if($this->isPost()){
            $this->postData = array_merge($_POST, $this->getJsonRawBody());
        }else if($this->isPut()){
            $this->putData = $this->getJsonRawBody();
        }
        
    }
    
    public function getRawBody(){
        return $this->rawBody;
    }
    public function getJsonRawBody($associative = true){
        return $this->expectsJson() && $this->rawBody ? json_decode( $this->rawBody ,$associative) : array();
    }
    
    
    private function _get($ar,$key,$filter = null,$defaultValue = null){
        if($ar[$key] !== NULL && $ar[$key] !== ''){
            if($filter !== null) return $this->sanitizer->sanitize($ar[$key],$filter);
            return $ar[$key];
        }else if($defaultValue && !array_key_exists($key, $ar)){
            return $defaultValue;
        }
    }

    public function get($key = null,$defaultValue = null,$filter = null){
        return $key ? $this->_get($_REQUEST,$key,$filter,$defaultValue) : $_REQUEST;
    }
    public function getQuery($key = null,$defaultValue = null,$filter = null){
        return $key ? $this->_get($this->getData,$key,$filter,$defaultValue) : $this->getData;
    }
    public function getPost($key = null,$defaultValue = null,$filter = null){
        return $key ? $this->_get($this->postData,$key,$filter,$defaultValue) : $this->postData;
    }
    public function getPut($key = null,$defaultValue = null,$filter = null){
        return $key ? $this->_get($this->putData,$key,$filter,$defaultValue) : $this->putData;
    }
    
    public function getFullRequestUrl($getParams = true){
        $get = $this->getData; //Copiando array para nao mexer no global
        $url = array_shift($get);
        if($getParams){
            $params = implode('&', array_map(function($v,$k){ return "$k=$v"; },$get,array_keys($get)));
            if(strlen($params) > 0)
                $url .= "?$params";
        }
        return $url;
    }
    
    public function getMethod(){
        return $this->server['REQUEST_METHOD'];
    }
    public function isMethod($method){
        return $this->getMethod() == $method;
    }
    public function isGet(){
        return $this->isMethod('GET');
    }
    
    public function isPost(){
        return $this->isMethod('POST');
    }
    
    public function isPut(){
        return $this->isMethod('PUT');
    }
    
    public function isDelete(){
        return $this->isMethod('DELETE');
    }
    
    public function getReferer(){
        return $_SERVER['HTTP_REFERER'];
    }
 
    public function getIp(){
        return $_SERVER['REMOTE_ADDR'];
    }
    
    /**
     * Se a requisicao espera como retorno html
     * @return boolean
     */
    public function expectsHtml(){
        return strpos($_SERVER['HTTP_ACCEPT'], 'text/html' ) !== FALSE;
    }
    
    /**
     * Se a requisicao espera como retorno json
     * @return boolean
     */
    public function expectsJson(){
        return strpos($_SERVER['HTTP_ACCEPT'], 'application/json' ) !== FALSE;
    }
}
