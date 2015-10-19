<?php
namespace Easyme\Mvc;

abstract class Ccu extends \Easyme\DI\Injectable{
        
    protected $apl;
    
    private $_isBuilt;
    
    public function _build(){
        if(!$this->_isBuilt){
            
            $this->_isBuilt = true;
            /*Tentando montar a view padrao*/
            $ccuName = explode('\\', get_called_class());

            /*Removendo o primeiro termo (Ccu\\)*/
            array_shift($ccuName);

            $name = end($ccuName);//[sizeof($ccuName) - 1];
            /*Nome base da classe. Ex: AdminCcu = Admin*/
            $name = substr($name,0,strrpos($name,'Ccu'));

            /*Nome base da Apl*/
            $ccuName[sizeof($ccuName) - 1] = $name.'Apl';

            $aplName = 'Apl\\'.implode('\\',$ccuName);
            /*Existe Apl com este nome?*/
            if(class_exists($aplName)){
                $this->apl = new $aplName;
            }
        }
    }

}