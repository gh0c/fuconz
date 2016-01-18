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


    public static function textTagUrls($text)
    {
        // The Regular Expression filter
        $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";


        // Check if there is a url in the text
        if(preg_match($reg_exUrl, $text, $url)) {
            // make the urls hyper links
            return preg_replace($reg_exUrl, '<a href="'.$url[0].'" rel="nofollow">'.$url[0].'</a>', $text);
        } else {
            // if no urls in the text just return the text
            return $text;

        }
    }
}
