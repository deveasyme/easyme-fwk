<?php

namespace Easyme\Mvc;

use Easyme\DI\Injectable;

use Easyme\Mvc\Model;

class Dao extends Injectable{
    
    public function insert(Model $model){
        
        $this->db->persist($model);
        $this->db->flush();
    }
    
    public function update(Model $model){
        
//        $this->db->persist($model);
        $this->db->flush();
    }
    
    public function delete(Model $model){
        
        $this->db->remove($model);
        $this->db->flush();
    }
}
