<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Easyme\Mvc;

/**
 * Description of ResourceInterface
 *
 * @author Binow
 */
interface ResourceInterface {
    
    public function getId();
    
    public function toArray();
    
}
