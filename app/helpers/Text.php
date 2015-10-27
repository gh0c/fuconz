<?php
namespace app\helpers;

class Text
{
    public static function html($text)
    {

        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    public static function htmlout($text)
    {
        echo self::html($text);
    }

    public static function output($text)
    {
        echo nl2br(htmlspecialchars($text, ENT_QUOTES, 'UTF-8'));
    }
}
