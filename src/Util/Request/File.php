<?php
namespace Easyme\Util\Request;

class File {
    
    private $size;
    
    private $name;
    
    private $tempName;
    
    private $type;
    
    private $extension;
    
    private $error;
    
    /*O caminho atual para o arquivo*/
    private $fullPath;
    
    function __construct($file) {
        $this->size = $file['size'];
        $this->name = $file['name'];
        $this->type = $file['type'];
        $this->tempName = $file['tmp_name'];
        $this->error = $file['error'];
        
        $this->extension = end(explode('.',$this->name));
        
        /*Inicia como o caminho para a pasta temporaria*/
        $this->fullPath = $this->tempName;
        
    }

    
    public function getSize() {
        return $this->size;
    }

    public function getName() {
        return $this->name;
    }
    
    public function getUniqueName(){
        
    }
    
    public function getTempName() {
        return $this->tempName;
    }

    public function getType() {
        return $this->type;
    }

    public function getExtension() {
        return $this->extension;
    }
    
    public function getError() {
        return $this->error;
    }
    
    public function getFullPath() {
        return $this->fullPath;
    }

    public function moveTo($destination, $useUniqueName = true ){
        
        $destination .= '/' . ($useUniqueName ? uniqid(time()) : $this->name);
        
        if(!move_uploaded_file($this->tempName,$destination)){
            throw new \Exception('Falha ao salvar arquivo em ' . $destination);
        }
        
        /*Alterando o caminho do arquivo*/
        $this->fullPath = $destination;
        
    }
    
    public function delete(){
        unlink($this->fullPath);
    }


    
}
