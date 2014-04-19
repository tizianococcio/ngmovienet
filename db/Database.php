<?php

require_once('../../credenziali/ngMovieNet.php');
require_once('PDOFactory.php');

class Database {
    public static function Load($newName=null) {
        $d = new DatabaseConfig();
        if  ($newName) { //se nuovo db
            $dbname = $newName;
        } else {
            $dbname = $d->default['database'];
        }
        $v = 'mysql:host=' . $d->default['host'] . ';dbname='.$d->default['prefix'].$dbname . ';charset=utf8';
        $db = PDOFactory::GetPDO($v, $d->default['login'], $d->default['password'], array(PDO::ATTR_PERSISTENT)); 
        $db->query('SET NAMES utf8');
        return $db;
    }
}
?>