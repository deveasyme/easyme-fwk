<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Easyme\Db;

use Doctrine\ORM\AbstractQuery;

/**
 * Description of SqlConfig
 *
 * @author Binow
 */
abstract class SqlConfig {
    
    const RESULT_AS_ARRAY = AbstractQuery::HYDRATE_ARRAY;
    const RESULT_AS_OBJECT = AbstractQuery::HYDRATE_OBJECT;
    const RESULT_AS_SCALAR = AbstractQuery::HYDRATE_SCALAR;
    const RESULT_AS_SINGLE_SCALAR = AbstractQuery::HYDRATE_SINGLE_SCALAR;
    const RESULT_AS_SIMPLE_OBJECT = AbstractQuery::HYDRATE_SIMPLEOBJECT;
    
    const MIN_PAGE_SIZE = 5;
    const MAX_PAGE_SIZE = 50;
    
    /**
     *
     * @var Filter
     */
    public $filter;
    
    private $resultAs = self::RESULT_AS_OBJECT;
    
    /**
     * @var boolean Se eh para paginar o resultado ou nao
     */
    private $paginate = false;
    
    /**
     * @var number O tamanho da pagina. Se for setado, automaticamente o atributo 
     * $paginate sera setado para true
     */
    private $pageSize = self::MIN_PAGE_SIZE;
    
    /**
     * @var number Qual pagina carregar. Depende se o atributo $paginate esta setado
     */
    private $page = 1;
    
    function __construct() {
        $this->filter = new Filter();
    }
    
    
    protected function setFilter($name , $value){
        $this->filter->$name = $value;
    }

    public function hasFilter($name){
        return $this->filter->has($name);
    }
    
    public function resultAs($resultAs){
        $this->resultAs = $resultAs;
    }
    public function getResultAs(){
        return $this->resultAs;
    }
    
    public function getPageSize() {
        if( !is_numeric($this->pageSize) || $this->pageSize < self::MIN_PAGE_SIZE ){
            return self::MIN_PAGE_SIZE;
        }else if($this->pageSize > self::MAX_PAGE_SIZE){
            return self::MAX_PAGE_SIZE;
        }
        
        return $this->pageSize;
    }

    public function getPage() {
        return $this->page;
    }

    public function setPageSize($pageSize) {
        $this->setPaginate(true);
        $this->pageSize = $pageSize;
    }

    public function setPage($page) {
        $this->page = $page;
    }
    
    public function getPaginate() {
        // A paginacao soh pode acontecer com objetos
        $this->resultAs(self::RESULT_AS_OBJECT);
        return $this->paginate;
    }

    public function setPaginate($paginate) {
        $this->paginate = $paginate;
    }




}
