<?php

namespace Ccu;

class CategoriasCcu extends \Easyme\Mvc\CcuRest implements \Easyme\Mvc\RestInterface{
    
    

    public function deleteAction($id) {
        
    }

    public function postAction() {
        
    }

    public function putAction($id) {
        
    }

    public function getAction($id = null, $query = null) {
        
        echo 'GET::' . $id;
        
    }
    
    public function getAllAction($userId){
        echo "getAll::$userId";
    }

}