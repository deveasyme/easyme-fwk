<?php

namespace Easyme\DI;

class Service {
    
    /**
     * O nome do servico
     * @var string
     */
    private $name;
    
    /**
     * A acao deste servico. Objeto ou Funcao
     * @var mixed
     */
    private $definition;
    
    /**
     * O resultado da definicao
     * @var mixed 
     */
    private $value = null;
    
    /**
     * Eh estatico ?
     * @var boolean 
     */
    private $isShared;
    
    /**
     * Ja foi resolvido ?
     * @var boolean 
     */
    private $isResolved = false;
    
    function __construct($name, $definition, $isShared = false) {
        $this->setName($name);
        $this->setDefinition($definition);
        $this->setIsShared($isShared);
    }

    
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }
    
    public function getDefinition() {
        return $this->definition;
    }

    public function setDefinition($definition) {
        $this->isResolved = false;
        $this->definition = $definition;
    }
    
    public function getIsShared() {
        return $this->isShared;
    }

    public function setIsShared($isShared) {
        $this->isShared = $isShared;
    }

    private function _resolve($args){
        
        $this->isResolved = true;
        
        if(is_callable($this->definition)) 
            return $this->deps[$name] = call_user_func_array ($this->definition, $args);
        else
            return $this->definition;
    }
    
    public function resolve($args){
        
        if($this->isShared){
            if(!$this->isResolved){
                $this->value = $this->_resolve($args);
            }
        }else{
            $this->value = $this->_resolve($args);
        }
        
        return $this->value;
    }
}