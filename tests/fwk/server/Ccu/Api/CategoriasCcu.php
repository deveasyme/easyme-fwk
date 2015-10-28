<?php

namespace Ccu\Api;

class CategoriasCcu extends \Easyme\Mvc\CcuRest implements \Easyme\Mvc\RestInterface{
    
    
    public function deleteAction($id) {
        
    }

    public function getAction($id = null, $query = null) {
        
        echo 'GET:';
        echo $id;
        
    }

    public function postAction() {
        
    }

    public function putAction($id) {
        
    }
    
    public function getTesteAction(){
        echo "Teste action";
    }

}