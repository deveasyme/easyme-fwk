<?php
namespace Easyme\Util;

class Flash{
    
    private static $_types = array(
        'error' => 'alert alert-danger',
        'info' => 'alert alert-info',
        'success' => 'alert alert-success',
        'warning' => 'alert alert-warning'
    );
    
    private $_output;
    
    public function output(){
        echo $this->_output;
    }
    
    private function _flash($type,$msg){
        $this->_output = "<div class='".self::$_types[$type]."'>$msg</div>";
    }
    
    public function error($msg){ $this->_flash('error', $msg); }
    public function info($msg){ $this->_flash('info', $msg); }
    public function success($msg){ $this->_flash('success', $msg); }
    public function warning($msg){ $this->_flash('warning', $msg); }
    
}