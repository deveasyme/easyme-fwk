<?php

namespace Easyme\DI;

interface DIInterface {
    
    public function getDI();
    
    public function setDI(Injector $di);
    
}
