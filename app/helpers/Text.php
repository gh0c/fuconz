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

    public static function wrapContentWithTags($haystack, $needle, $class, $tag_name = "div", $casesensitive = false)
    {
        $modifier=($casesensitive) ? 'i' : '';
        //quote search-string, cause preg_replace wouldn't work correctly if chars like $?. were in search-string
        $quotedSearch=preg_quote($needle,'/');
        //generate regex-search-pattern
        $checkPattern='/'.$quotedSearch.'/'.$modifier;
        //generate regex-replace-pattern
        $strReplacement='<' . $tag_name . ' class = "' . $class . '">$0</'. $tag_name . '>';
        return preg_replace($checkPattern,$strReplacement,$haystack);
    }
}
