<?php

require_once __DIR__.'/defines.php';
require_once __DIR__.'/autoloader.php';


return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);