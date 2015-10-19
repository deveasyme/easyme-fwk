<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Easyme\Util\Request;

/**
 * Description of Curl
 *
 * @author Binow
 */
class Curl {
    
    
    private $url;
    private $ssl = false;
    private $connectTimeout = 10;
    private $timeout = 50;
    
    
    private $connectionType = CURLOPT_HTTPGET;
    private $connectionValue = true;
    
    private $postFields = array();
    
    private $responseBody;
    private $responseHeaders;
    private $responseStatus;
    
    public function __construct($url) {
        
        $this->url = $url;
        
    }


    public function get(){
        $this->connectionType = CURLOPT_HTTPGET;
        return $this->_request();
    }
    public function post($values){
        $this->connectionType = CURLOPT_POST;
        $this->postFields = $values;
        return $this->_request();
        
    }
    public function put($values){
//        $this->connectionType = CURLOPT_PUT;
        $this->connectionType = CURLOPT_CUSTOMREQUEST;
        $this->connectionValue = 'PUT';
        $this->postFields = $values;
        return $this->_request();
        
    }
    public function delete(){
        $this->connectionType = CURLOPT_CUSTOMREQUEST;
        $this->connectionValue = 'DELETE';
        return $this->_request();
        
    }
    
    public function isPut(){
        return ($this->connectionType === CURLOPT_CUSTOMREQUEST) &&
                ($this->connectionValue == 'PUT');
    }
    public function isPost(){
        return $this->connectionType === CURLOPT_POST;
    }
        
    private function _request(){
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        
        curl_setopt($ch, $this->connectionType, $this->connectionValue);
        
        
        curl_setopt($ch, CURLOPT_URL, $this->url );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->ssl);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        
        if($this->isPost() || $this->isPut()){
            
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->postFields));
            
        }
       
        $response = curl_exec($ch);
        
        $info = curl_getinfo($ch);
      
        $header = substr($response, 0, $info['header_size']);
        $body = substr($response, $info['header_size']);
        
        $this->responseBody = $body;
        $this->responseHeaders = $header;
        
        $this->responseStatus = $info['http_code'];
        
        return $body;

    }
    
    
    public function getUrl() {
        return $this->url;
    }

    public function getSsl() {
        return $this->ssl;
    }

    public function getConnectTimeout() {
        return $this->connectTimeout;
    }

    public function getTimeout() {
        return $this->timeout;
    }

    public function getConnectionType() {
        return $this->connectionType;
    }

    public function getConnectionValue() {
        return $this->connectionValue;
    }

    public function getPostFields() {
        return $this->postFields;
    }

    public function getResponseBody() {
        return $this->responseBody;
    }

    public function getResponseHeaders() {
        return $this->responseHeaders;
    }

    public function setUrl($url) {
        $this->url = $url;
    }

    public function setSsl($ssl) {
        $this->ssl = $ssl;
    }

    public function setConnectTimeout($connectTimeout) {
        $this->connectTimeout = $connectTimeout;
    }

    public function setTimeout($timeout) {
        $this->timeout = $timeout;
    }

    public function setConnectionType($connectionType) {
        $this->connectionType = $connectionType;
    }

    public function setConnectionValue($connectionValue) {
        $this->connectionValue = $connectionValue;
    }

    public function setPostFields($postFields) {
        $this->postFields = $postFields;
    }

    public function setResponseBody($responseBody) {
        $this->responseBody = $responseBody;
    }

    public function setResponseHeaders($responseHeaders) {
        $this->responseHeaders = $responseHeaders;
    }

    public function getResponseStatus() {
        return $this->responseStatus;
    }

    public function setResponseStatus($responseStatus) {
        $this->responseStatus = $responseStatus;
    }
}
