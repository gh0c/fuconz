<?php


namespace app\helpers;

use RandomLib\Factory as RandomLib;


class Hash
{
    protected $config;
    private static $msg;

    public static function password($password) {
        return password_hash($password, PASSWORD_BCRYPT, array("cost" => 10));
    }

    public static function passwordCheck($password, $hash) {
        return password_verify($password, $hash);
    }

    public static function hash($input)  {
        return hash('sha256', $input);
    }

    public static function hashCheck($know, $user) {
        return hash_equals($know, $user);
    }




    public static function getMSG()
    {

        if (!self::$msg)
        {
            $factory = new RandomLib();
            self::$msg = $factory->getMediumStrengthGenerator();
        }

        return self::$msg;
    }
}


?>