<?php

namespace Easyme\Mvc;

use Easyme\Util\Flash;
use Exception;
use \Easyme\DI\Injectable;

class View extends Injectable{

    private static $TEMPLATE_DIR = '_templates';
    private static $PARTIALS_DIR = '_partials';

    private $vars = array();

    private $stack = [];
    private $template;

    private $fresh = true;
    private $disabled = true;

//    private

    public function reset(){
        $this->fresh = true;
        $this->disabled = true;
        $this->stack = [];
    }

    public function setTemplate($filename){

        $path = EFWK_APP_DIR.'/'.self::$TEMPLATE_DIR.'/'.$filename.'.php';

        if(!file_exists($path)){
            throw new Exception("Template $filename not found");
        }

        if($this->fresh) $this->disabled = false;
        $this->fresh = false;
        $this->template = $path;

        return $this;
    }

    public function setContent($filename){
        // Caminhos relativos
        if($filename[0] == '/'){
            $path = EFWK_PUBLIC_DIR.'/'.$filename;
        }else{

            $path = EFWK_APP_DIR.'/'.$this->dispatcher->getRoute()->getCcuPath().'/'.$filename.'.php';
        }


        if(!file_exists($path)){
            throw new Exception("Content $filename not found");
        }

        if($this->fresh) $this->disabled = false;
        $this->fresh = false;
        $this->stack[] = $path;

        return $this;
    }

    public function isDisabled(){
        return $this->disabled;
    }

    /**
     *
     * @return boolean
     */
    public function run(){

        ob_start();


        extract($this->vars);
//
        if($this->template){
            include $this->template;
        }else{
            // Nao posso chamar o $this->show por causa do extract acima
//            $this->show();
            include array_shift($this->stack);
        }
        return ob_get_clean();
    }

    /**
     * [Deve ser chamado apenas de dentro de uma View]
     * Exibe o conteudo da proxima view na pilha
     */
    public function show(){
//        print_r($this->stack);
        include array_shift($this->stack);
    }

    /**
     * [Deve ser chamado apenas de dentro de uma View]
     * Exibe o conteudo de um partial
     */
    public function partial($name,array $vars = array()){

        $file = EFWK_APP_DIR.'/'.self::$PARTIALS_DIR.'/'.$name.'.php';

        if(!file_exists($file))
            throw new Exception("Partial {$name} not found");

        extract($vars);
        include $file;
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

}
