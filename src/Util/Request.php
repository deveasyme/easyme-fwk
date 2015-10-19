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
        return $this->isContentType("application/json") ? json_decode( $this->rawBody ,$associative) : array();
    }
    
    
    private function _get($ar,$key,$filter = null,$defaultValue = null){
        if($ar[$key] !== NULL && $ar[$key] !== ''){
            if($filter !== null) return $this->sanitizer->sanitize($ar[$key],$filter);
            return $ar[$key];
        }else if($defaultValue && !array_key_exists($key, $ar)){
            return $defaultValue;
        }
    }

    
    
    public function get($key = null,$filter = null,$defaultValue = null){
        return $key ? $this->_get($_REQUEST,$key,$filter,$defaultValue) : $_REQUEST;
    }
    public function getQuery($key = null,$filter = null,$defaultValue = null){
        return $key ? $this->_get($this->getData,$key,$filter,$defaultValue) : $this->getData;
    }
    public function getPost($key = null,$filter = null,$defaultValue = null){
        return $key ? $this->_get($this->postData,$key,$filter,$defaultValue) : $this->postData;
    }
    public function getPut($key = null,$filter = null,$defaultValue = null){
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
    
    public function isAjax(){
        return !empty($this->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
    
    public function __toString() {
        return print_r($this->request,true);
    }
    
    public function getUri($removeQuery = false){
        return $removeQuery ? preg_replace('/\/?(\?.*)?$/', '', $this->server["REQUEST_URI"]) : $this->server["REQUEST_URI"];
                            
    }
    public function from(){
        return $this->server["HTTP_REFERER"];
    }
    
    
    public function fail($msg,$extraData = array()){
        http_response_code ( 400 );
        echo json_encode(array(
            'text' => $msg,
            'data' => $extraData
        ));
    }
    
    public function notFound(){
        http_response_code ( 404 );
    }
    
    /*FILES HANDLE*/
    
    public function hasFiles(){
        return sizeof($this->files) > 0;
    }
    
    public function getFiles(){
        $files = array();
        foreach($_FILES as $_FILE){
            $files[] = new Request\File($_FILE);
        }
        return $files;
    }
    
    public function getFile($key){
        return $_FILES[$key] ? new Request\File($_FILES[$key]) : null;
    }
    
    public function getContentType(){
        return $this->server['CONTENT_TYPE'];
    }
    
    public function isContentType($type){
        return strpos($this->getContentType(), $type) !== false;
    }
    
    public function getServerName(){
        return $this->server['SERVER_NAME'];
    }
    public function getPort(){
        return $this->server['SERVER_PORT'];
    }
}
