<?php

namespace Easyme\Events;

interface ManagerInterface {
    
    abstract public function attach ($eventType, $handler);

    abstract public function detachAll ($type);

    abstract public function fire ($eventType, $source, $data);

    abstract public function getListeners ($type);

}

?>
