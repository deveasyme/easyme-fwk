<?php

namespace Easyme\Error;

class FatalErrorException extends \Exception{
//    
//    private $file; 
    private $error;

    public function __construct($error) {

        $this->error = $error;
        $message =  $error['message'];
        $this->file = $error['file'];
        $this->line = $error['line'];

        parent::__construct($message, 500, null);
    }


    public function getError(){
        return $this->error;
    }


}