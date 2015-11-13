<?php

namespace Easyme\Mvc;

use Easyme\DI\Injectable;

use Easyme\Mvc\Model;

class Dao extends Injectable{
    
    /**
     * Precisa ser static pois o db eh compartilhado entre todas as classes
     * @var boolean 
     */
    private static $inTransaction = false;
    
    public function begin(){
        $this->db->getConnection()->beginTransaction();
        self::$inTransaction = true;
    }
    public function commit(){
        $this->db->flush();
        $this->db->getConnection()->commit();
        self::$inTransaction = false;
    }
    public function rollback(){
        $this->db->getConnection()->rollBack();
        self::$inTransaction = false;
    }
    
    public function insert(Model $model){
        
        $this->db->persist($model);
        
        // Soh faz o flush se nao estiver com transacao aberta
        if(self::$inTransaction === false)
            $this->db->flush();
    }
    
    public function replace(Model $model){
        
        $this->db->merge($model);
        // Soh faz o flush se nao estiver com transacao aberta
        if(self::$inTransaction === false)
            $this->db->flush();
    }
    
    public function update(Model $model){
        
        // Soh faz o flush se nao estiver com transacao aberta
        if(self::$inTransaction === false)
            $this->db->flush();
    }
    
    public function delete(Model $model){
        
        $this->db->remove($model);
        
        // Soh faz o flush se nao estiver com transacao aberta
        if(self::$inTransaction === false)
            $this->db->flush();
    }
}
