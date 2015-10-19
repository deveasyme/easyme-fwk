<?php

namespace Easyme\Error;

class ApiException extends \Exception{
    
     
    private $id;
//    private $message;
//    private $code;

    public function __construct($id, $message,$code) {
        parent::__construct($message,$code,null);
        $this->id = $id;
//        $this->message = $message;
//        $this->message = $message;
    }
    
    public function getId(){
        return $this->id;
    }

}