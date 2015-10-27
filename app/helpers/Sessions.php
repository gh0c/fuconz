<?php

namespace app\helpers;

class Sessions
{



    public static function init()
    {
        if (session_id() == '') {
            session_start();
        }
    }


    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function unset_for($key) {
        unset($_SESSION[$key]);
    }

    public static function get($key)
    {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return null;
    }


    public static function add($key, $value)
    {
        $_SESSION[$key][] = $value;
    }

    public static function destroy()
    {
        session_destroy();
    }


}
