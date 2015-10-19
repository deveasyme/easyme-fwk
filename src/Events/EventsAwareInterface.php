<?php

namespace Easyme\Events;

interface EventsAwareInterface {
    
    public function getEventManager();
    
    public function setEventManager(Manager $eventManager);
    
}

?>
