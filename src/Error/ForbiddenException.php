<?php

namespace Easyme\Error;

class ForbiddenException extends \Exception{
    
    public function __construct($message = '') {
         parent::__construct($message, 403, null);
    }



}