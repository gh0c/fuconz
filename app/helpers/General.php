<?php

namespace app\helpers;

class General
{
//    public static function rrmdir($dir) {
//        foreach(glob($dir . '/*') as $file) {
//            if(is_dir($file)) {
//                self::rrmdir($file);
//            }
//            else {
//                unlink($file);
//            }
//        }
//        rmdir($dir);
//    }

    public static function rrmdir($dirname)
    {
        if (is_dir($dirname))
            $dir_handle = opendir($dirname);
        if (!isset($dir_handle))
            return false;
        while($file = readdir($dir_handle)) {
            if ($file != "." && $file != "..") {
                if (!is_dir($dirname."/".$file))
                    unlink($dirname."/".$file);
                else
                {
                    $a=$dirname.'/'.$file;
                    self::rrmdir($a);
                }
            }
        }
        closedir($dir_handle);
        rmdir($dirname);
        sleep(.2);
        return true;
    }



}