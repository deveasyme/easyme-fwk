<?php

namespace Easyme\Util;

use Exception;

class Request {

    private $server;
    private $request;
    private $cookies;
    private $sanitizer;

    private $files;

    private $rawBody;

    private $getData  = array();
    private $postData = array();
    private $putData  = array();

    public function __construct() {
        $this->server = $_SERVER;
        $this->request = $_REQUEST;
        $this->cookies = $_COOKIE;
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

    public function setRequestData($data){

        if($this->isGet()){
            $this->getData = $data;
        }
        if($this->isPost()){
            $this->postData = $data;
        }
        if($this->isPut()){
            $this->putData = $data;
        }

    }

    public function getRawBody(){
        return $this->rawBody;
    }
    public function getJsonRawBody($associative = true){
        return $this->expectsJson() && $this->rawBody ? json_decode( $this->rawBody ,$associative) : array();
    }


    private function _get($ar,$key,$filter = null,$defaultValue = null){

        if(array_key_exists($key, $ar)){

            if( $ar[$key] === null || $ar[$key] === '') return null;

            if($filter !== null ) return $this->sanitizer->sanitize($ar[$key],$filter);
            return $ar[$key];
        }

        return $defaultValue;
    }

    public function get($key = null,$defaultValue = null,$filter = null){

        if($this->isGet()){
            return $this->getQuery($key , $defaultValue , $filter);
        }
        if($this->isPost()){
            return $this->getPost($key , $defaultValue , $filter);
        }
        if($this->isPut()){
            return $this->getPut($key , $defaultValue , $filter);
        }
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


    private function _require($ar,$key,$filter = null){

        if(!array_key_exists($key, $ar) || $ar[$key] === null || $ar[$key] == '')
            throw new Exception("Parâmetro '$key' obrigatório");

        if($filter !== null) return $this->sanitizer->sanitize($ar[$key],$filter);

        return $ar[$key];
    }
    public function requirep($key,$filter = null){

        if($this->isGet()){
            return $this->requireQuery($key , $filter);
        }
        if($this->isPost()){
            return $this->requirePost($key, $filter);
        }
        if($this->isPut()){
            return $this->requirePut($key , $filter);
        }
    }

    public function requireQuery($key, $filter = null){
        return $key ? $this->_require($this->getData,$key,$filter) : $this->getData;
    }
    public function requirePost($key = null, $filter = null){
        return $key ? $this->_require($this->postData,$key,$filter) : $this->postData;
    }
    public function requirePut($key = null, $filter = null){
        return $key ? $this->_require($this->putData,$key,$filter) : $this->putData;
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
        return $this->server['HTTP_REFERER'];
    }

    public function getIp(){
        return $this->server['REMOTE_ADDR'];
    }

    public function getHeader($header){
        $headers = apache_request_headers();
        return $headers[$header];
    }


    public function getCookie($key){
        return $this->cookies[$key];
    }

    /**
     * Se a requisicao espera como retorno html
     * @return boolean
     */
    public function expectsHtml(){
        return strpos($this->server['HTTP_ACCEPT'], 'text/html' ) !== FALSE;
    }

    /**
     * Se a requisicao espera como retorno json
     * @return boolean
     */
    public function expectsJson(){
        return strpos($this->server['HTTP_ACCEPT'], 'application/json' ) !== FALSE;
    }
}
