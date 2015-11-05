<?php

namespace Apl;

use Easyme\Mvc\Apl;
use Dao\CategoriasDao;

/**
 * @property CategoriasDao $dao
 */
class CategoriasApl extends Apl{
    
    public function teste(){
        echo 'CategoriasApl::teste()';
        return $this->dao->teste();
    }
    
}
