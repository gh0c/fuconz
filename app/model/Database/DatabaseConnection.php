<?php

namespace app\model\Database;
use app\helpers\Configuration;

use \PDO;

class DatabaseConnection
{

    private static $dbh;

    public static function getInstance() {
        if (!isset(self::$dbh))
        {
            // building data source name from config
            $dsn = '' . Configuration::read('db.type') . ':host=' . Configuration::read('db.host') .
                ';dbname='    . Configuration::read('db.basename') .
                ';port='      . Configuration::read('db.port') .
                ';connect_timeout=15;charset=UTF8';

            // getting DB user from config
            $user = Configuration::read('db.user');
            // getting DB password from config
            $password = Configuration::read('db.password');

            // note the PDO::FETCH_OBJ, returning object ($result->id) instead of array ($result["id"])
            // @see http://php.net/manual/de/pdo.construct.php
            $options = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING);

            self::$dbh = new PDO($dsn, $user, $password, $options);
        }
        return self::$dbh;
    }

}