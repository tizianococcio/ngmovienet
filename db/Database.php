<?php

require_once('DatabaseConfig.php');
require_once('PDOFactory.php');

class Database {
    public static function Load($newName=null) {
        $d = new DatabaseConfig();
        if  ($newName) { //se nuovo db
            $dbname = $newName;
        } else {
            $dbname = $d->default['database'];
        }
        $v = 'mysql:host=' . $d->default['host'] . ';dbname='.$d->default['prefix'].$dbname;
        return PDOFactory::GetPDO($v, $d->default['login'], $d->default['password'], array(PDO::ATTR_PERSISTENT)); 
    }
}
?>