<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ccu;

use Apl\CategoriasApl;

/**
 * @property CategoriasApl $apl Related apl
 */
class CategoriasCcu extends \Easyme\Mvc\Ccu{
    
    public function indexAction(){
        $this->view->disable();
        
        $this->apl->teste();
        
//        $this->view->
        
//        $this->apl->teste();
        
        echo 'Index';
    }
    
}
