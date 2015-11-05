<?php

namespace Easyme\Mvc;
        
use Easyme\Util\Flash;

class View extends \Easyme\DI\Injectable{
    
    const TEMPLATE_DIR = 'View/template';
    const PARTIAL_DIR = 'View/shared';
    
    /*Raiz de arquivos dessa View*/
    private $root;
    
    /*A View esta desabilitada ?*/
    private $disabled = false;
    
    /*Variaveis dessa View*/
    private $vars = array();
    
    private $template;
    private $templateVars = array();
    private $content = array();
    private $defaultContent;
    
    /**
     * Titulo da pagina
     * @var string 
     */
    private $title;
    
    public function __construct() {
//        parent::__construct();
        
        if(!defined('EFWK_APP_DIR'))
            die("Por favor, defina a constante EFWK_APP_DIR para apontar para a raiz dos arquivos de servidor");
    }
    
    public function reset(){
//        $this->disabled = false;
//        $this->vars = array();
//        $this->content = array();
//        $this->defaultContent = array();
//        $this->template = null;
//        $this->templateVar = array();
    }
    
    public function setRoot($root){
        $this->root = EFWK_APP_DIR."/View". ($root ? "/$root" : "");
        return $this;
    }

    public function setTemplate($file,$vars = array()){
        $this->template = $file;
        $this->templateVars = $vars;
        return $this;
    }
    
    public function getTemplate() {
        return $this->template;
    }
    public function setDefaultContent($defaultContent) {
        $this->defaultContent = $defaultContent;
    }

    public function setContent($file){
        if(is_array($file))
            $this->content = $file;
        else 
            $this->content[] = $file;
        
        return $this;
    }
    
    public function getContent(){
        return $this->content;
    }

    public function run(){
        
        /*View esta desabilitada*/
        if(!$this->template || $this->disabled) return $this->show();
        
        $file = EFWK_APP_DIR.'/'.self::TEMPLATE_DIR.'/'.$this->template.'.php';

        if(!file_exists($file)){
            throw new \Exception("Template {$this->template} não encontrado");
        }else{
            extract($this->templateVars);
            include $file;
        }
    }
    
    public function show(){
        
        if( ((!$this->content || sizeof($this->content) < 1) && !$this->defaultContent) || $this->disabled) return;
        
        if(!$this->disabled){
            
            if($this->content && sizeof($this->content) > 0)
                $file = array_shift($this->content);
            else
                $file = $this->defaultContent;

            if($file){
                
                $file = $this->root."/$file.php";

                if(!file_exists($file)){
                    throw new \Exception("Conteúdo '{$file}' não encontrado");
                }else{
                    extract($this->vars);
                    include $file;
                }
            }
        }
        
        
        return $this;
    }
    
    public function disable(){
        $this->disabled = true;
        return $this;
    }
    
    public function enable(){
        $this->disabled = false;
        return $this;
    }
    
    public function setVar($key,$value){
        $this->vars[$key] = $value;
        return $this;
    }
    
    public function setVars(array $vars){
        foreach($vars as $key=>$value){
            $this->setVar($key,$value);
        }
        return $this;
    }
    
    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }
    
    public function partial($name,array $vars = array()){
        
        $file = EFWK_APP_DIR.'/'.self::PARTIAL_DIR.'/'.$name.'.php';
        
        if(!file_exists($file)){
            throw new \Exception("Partial '{$file}' não encontrado");
        }else{
            extract($vars);
            include $file;
        }
    }
}
