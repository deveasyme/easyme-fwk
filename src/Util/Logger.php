<?php

namespace Easyme\Util;

use \Exception;

class Logger {
    
    /**
     * Caminho para o nome do arquivo
     * @var string
     */
    private $file;
    
    public function __construct() {
        
    }
    
    public function log($fileName , $data){
     
        $log = array(
            'date' => date('Y-m-d H:i:s'),
            'message' => ''
        );
        
        if($data instanceof \Exception){
            $log['message'] = array(
                'Message' => $data->getMessage(),
                'File' => $data->getFile(),
                'Line' => $data->getLine(),
                'Trace' => $data->getTraceAsString()
            );
        }else {
            $log['message'] = $data;
        }
        
        $fileName .= '.log';
        $this->createFile($fileName);
        file_put_contents(LOGS_DIR.'/'.$fileName,print_r($log,true),FILE_APPEND);   
    }
    
    private function createFile($file_name){
        $parts = explode('/', $file_name);
        array_pop($parts);
        
        $lastPath = LOGS_DIR.'/';
        foreach($parts as $folder){
            $lastPath .= "/$folder";
            if(!file_exists($lastPath)){
                mkdir($lastPath);
            }
        }
        
        $this->file =  LOGS_DIR.'/'.$file_name;
        
        if(!file_exists($this->file)) 
            touch($this->file);
    }
}