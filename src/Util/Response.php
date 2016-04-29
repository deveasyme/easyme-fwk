<?php

namespace Easyme\Util;

class Response {
    
    const CONTENT_TYPE_PDF = 'application/pdf';
    const CONTENT_TYPE_JSON = 'application/json';
    const CONTENT_TYPE_XML = 'application/xml';
    const CONTENT_TYPE_TEXT_PLAIN = 'text/plain';
    const CONTENT_TYPE_TEXT_HTML = 'text/html';
    
    const CHARSET_UTF8 = 'utf-8';
    const CHARSET_LATIN_1 = 'latin-1';
    
    const RESPONSE_CODE_OK = 200;
    const RESPONSE_CODE_CREATED = 201;
    
    const RESPONSE_CODE_BAD_REQUEST = 400;
    const RESPONSE_CODE_UNAUTHORIZED = 401;
    const RESPONSE_CODE_PAYMENT_REQUIRED = 402;
    const RESPONSE_CODE_FORBIDDEN = 403;
    const RESPONSE_CODE_NOT_FOUND = 404;
    const RESPONSE_CODE_UNPROCESSABLE_ENTITY = 422;
    
    private $rawHeaders = array();
    private $headers = array();
    private $cookies = array();
    
    private $responseBody;
    
    private $statusCode = self::RESPONSE_CODE_OK;
    
    
    private $expires;
    
    public function __construct() {
        
        if($this->expectsHtml())
            $this->setContentType(self::CONTENT_TYPE_TEXT_HTML);
        else if($this->expectsJson())
            $this->setContentType(self::CONTENT_TYPE_JSON);
    }


    public function send(){
        $this->sendHeaders();
        $this->sendContent();
    }
    public function sendHeaders(){
        if(!$this->isSent()){
            http_response_code($this->statusCode);
            /*Aplicando os rawHeaders*/
            array_walk($this->rawHeaders, header);
            /*Aplicando os headers*/
            foreach($this->headers as $key=>$value) header("$key: $value");
        }
    }
    public function isSent(){
        return headers_sent();
    }
    public function sendContent(){
        
        if($_GET['__bjson']){
            echo "<pre>{$this->responseBody}</pre>";
        }else{
            echo $this->responseBody;
        }
    }
    
    public function setStatusCode($code) {
        $this->statusCode = $code;
    }
    
    public function getHeaders() {
        return $this->headers;
    }

    public function setRawHeader($header) {
        $this->rawHeaders[] = $header;
    }
    public function setHeader($key,$value) {
        $this->headers[$key] = $value;
    }
    public function setHeaders($headers) {
        $this->headers = $headers;
    }
    public function resetHeaders(){
        $this->headers = array();
        $this->rawHeaders = array();
    }

    public function getCookies() {
        return $this->cookies;
    }

    public function setCookies($cookies) {
        $this->cookies = $cookies;
    }

    public function getExpires() {
        return $this->expires;
    }

    public function setExpires($expires) {
        $this->expires = $expires;
    }

    public function getContentType() {
        return $this->contentType;
    }

    public function setContentType($contentType, $charset = self::CHARSET_UTF8) {
        $this->setHeader('Content-Type', "$contentType; charset=$charset" );
    }

    public function setJsonContent($dataArray){
        $this->responseBody = json_encode($dataArray, $_GET['__bjson'] ? JSON_PRETTY_PRINT : 0);
    }
    public function setXmlContent($dataArray){
        throw new \Exception('Método Response::setXmlContent() não implementado');
    }
    public function setContent($content){
        $this->responseBody = $content;
    }
    
    public function redirect($url,$statusCode = 302){
        header('Location: ' . $url , true, $statusCode);
        die();
    }
    
    
    
    public function setFinalHeader($key,$value){
        header("$key: $value");
        die();
    }
    
    
    public function setUTF8(){
        $this->setCharset('utf-8');
        return $this;
    }
    

    /**
     * Se a requisicao espera como retorno html
     * @return boolean
     */
    public function expectsHtml(){
        return strpos($_SERVER['HTTP_ACCEPT'], self::CONTENT_TYPE_TEXT_HTML ) !== FALSE;
    }
    
    /**
     * Se a requisicao espera como retorno json
     * @return boolean
     */
    public function expectsJson(){
        return strpos($_SERVER['HTTP_ACCEPT'], self::CONTENT_TYPE_JSON ) !== FALSE;
    }
    
}
