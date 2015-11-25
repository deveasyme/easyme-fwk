<?php

namespace Easyme\Db;

use Countable;
use IteratorAggregate;
use ArrayIterator;

use \Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator as QueryPaginator;

class Paginator implements Countable, IteratorAggregate{
    
    /**
     * O paginador para paginacao de queries
     * @var QueryPaginator 
     */
    private $queryPaginator;


    /**
     * Array de objetos para paginacao estatica
     * @var array 
     */
    private $items;
    
    /**
     * Total de elementos para paginacao estatica
     * @var int 
     */
    private $count;
    
    public static function createFromQuery(Query $query){
        
        $paginator = new Paginator();
        
        $paginator->queryPaginator = new QueryPaginator($query);
        
        return $paginator;
        
    }
    
    /**
     * Cria um paginator a partir de um array de dados. 
     * @param array $items O array de dados da pagina
     * @param int $count A quantidade total de items existentes
     * @return \Easyme\Db\Paginator
     */
    public static function createFromArray(array $items , $count){
        
        $paginator = new Paginator();
        
        $paginator->items = $items;
        $paginator->count = $count;
        
        return $paginator;
        
    }
    
    public function count() {
        
        if($this->queryPaginator){
            return count($this->queryPaginator);
        }
        
        return $this->count;
    }

    public function getIterator() {
        
        if($this->queryPaginator){
            return $this->queryPaginator->getIterator();
        }
        
        return new ArrayIterator($this->items);
        
        
    }
    

}
