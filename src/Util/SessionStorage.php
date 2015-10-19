<?php

namespace Easyme\Util;

use \Exception;

class SessionStorage {
    
    private $key;
    
    public static function bagExists($bagKey){
        return array_key_exists($bagKey, $_SESSION);
    }
    
    public function __construct($key = 'SessionStorage'){
        session_start();
        $this->key = $key;
    }
    
    public function has($key){
        return array_key_exists($this->key,$_SESSION) && array_key_exists($key, $_SESSION[$this->key]);
    }
    
    public function each($cb){
        foreach($_SESSION[$this->key] as $k=>$v){
            $cb($k,$v);
        }
    }
    
    public function __set($key,$value){
        
//        error_log("{$this->key} => $key => $value");
        
        if(is_null($value)){
            $has = $this->has($key);
            if($has) unset ($_SESSION[$this->key][$key]);
            return;
        }
        $_SESSION[$this->key][$key] = $value;
    }
    
    public function __get($key){
        if($this->has($key)){
            return $_SESSION[$this->key][$key];
        }
        throw new Exception("Chave de sessão '{$this->key}=>$key' não encontrada");
    }
    
    public function delete($key){
        if($this->has($key)){
            $_SESSION[$this->key][$key] = NULL;
            return true;
        }
        throw new Exception("Chave de sessão '$key' não encontrada");
    }
    
    public function bag($key){
        return new SessionStorage($key);
    }
    
    public function __toString() {
        return print_r($_SESSION[$this->key],true);
    }
    
    
}

?>
