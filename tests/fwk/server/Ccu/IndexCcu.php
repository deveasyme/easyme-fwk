<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ccu;

/**
 * Description of IndexCcu
 *
 * @author Binow
 */
class IndexCcu extends \Easyme\Mvc\Ccu{
    
    public function indexAction(){
        $this->view->disable();
        echo 'Index';
    }
    
}
