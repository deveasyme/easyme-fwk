<?php

namespace Easyme\Mvc;

interface RestInterface {
    
    public function getAction( $id = null , $query = null );
    public function postAction();
    public function putAction($id);
    public function deleteAction($id);
    
}
