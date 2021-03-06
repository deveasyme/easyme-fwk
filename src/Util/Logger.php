<?php

namespace Easyme\Util;

use \Exception;

class Logger {
    
    /**
     * Caminho para o nome do arquivo
     * @var string
     */
    private $file;
    
    private $flags = 0;
    
    public function __construct($append = true) {
        if($append)
            $this->flags = FILE_APPEND;
           
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
        file_put_contents(EFWK_LOGS_DIR.'/'.$fileName,print_r($log,true),$this->flags);   
    }
    
    private function createFile($file_name){
        $parts = explode('/', $file_name);
        array_pop($parts);
        
        $lastPath = EFWK_LOGS_DIR.'/';
        foreach($parts as $folder){
            $lastPath .= "/$folder";
            if(!file_exists($lastPath)){
                mkdir($lastPath);
            }
        }
        
        $this->file =  EFWK_LOGS_DIR.'/'.$file_name;
        
        if(!file_exists($this->file)) 
            touch($this->file);
    }
}