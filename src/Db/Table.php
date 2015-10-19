<?php

namespace Easyme\Db; 

interface Table{
     public static function loadFromDao($daoArray);
     public static function tableName();
}
?>
