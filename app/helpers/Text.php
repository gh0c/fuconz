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
            return preg_replace($reg_exUrl, '<a href="'.$url[0].'" rel="nofollow" target="_blank">'.$url[0].'</a>', $text);
        } else {
            // if no urls in the text just return the text
            return $text;

        }
    }


    public static function toChatString($text)
    {
        $t = self::emoticons($text);
        $t = self::textTagUrls($t);
        return $t;
    }


    private static function emoticons($text)
    {
        $e1 = self::standardEmoticons($text);
        $e2 = self::bigEmoticons($e1);
        $e3 = self::memeEmoticons($e2);
        return $e3;
    }

    private static function standardEmoticons($text)
    {
        $emoticons = Configuration::read("emoticons.std");
        return self::replaceEmoticonsWithSpanTags($text, $emoticons,
            "std-chat", "f-emoticon");
    }


    private static function bigEmoticons($text)
    {
        $emoticons = Configuration::read("emoticons.big");
        return self::replaceEmoticonsWithSpanTags($text, $emoticons,
            "big-chat", "f-b-emoticon");
    }

    private static function memeEmoticons($text)
    {
        $emoticons = Configuration::read("emoticons.meme");
        return self::replaceEmoticonsWithSpanTags($text, $emoticons,
            "meme-chat", "f-m-emoticon");
    }


    private static function replaceEmoticonsWithSpanTags($text, $emoticons,
        $main_class, $class_template)
    {
        // emoticons are in format:
        //  array(
        //      'smile' => array(
        //          'codes' => array(
        //              ':-)', '=)', ':)', ':=)'
        //          )
        //      )
        //  )

        foreach(array_keys($emoticons) as $emoticon) {

            foreach($emoticons[$emoticon]['codes'] as $code) {
                $pattern = '/(?<=\s|^)('
                    . preg_quote($code, "/")
                    . ')(?=\s|$)/im';
                $replacement = ' <span '.
                    ' title = "' .$emoticon .'"'.
                    ' class = "fuconz-emoticon ' .
                    $main_class . ' ' .$class_template . '-' .
                    $emoticon . '"' .
                    ' >' .
                    $code .
                    '</span>';
                $text = preg_replace($pattern, $replacement, $text);
            }
        }
        return $text;
    }


    public static function wrapEmoticonWithTags($code, $emoticon, $main_class, $class_template)
    {
        $replacement = ' <span '.
            ' title = "' .$emoticon .'"'.
            ' class = "fuconz-emoticon ' .
            $main_class . ' ' .$class_template . '-' .
            $emoticon . '"' .
            ' >' .
            $code .
            '</span>';

        $pattern = '/(?<=\s|^)('
            . preg_quote($code, "/")
            . ')(?=\s|$)/im';

        $text = preg_replace($pattern, $replacement, $code);

        return $text;
    }

    public static function wrapBigEmoticonWithTags($code, $emoticon)
    {
        return self::wrapEmoticonWithTags($code, $emoticon, "big-chat", "f-b-emoticon");
    }

    public static function wrapMemeEmoticonWithTags($code, $emoticon)
    {
        return self::wrapEmoticonWithTags($code, $emoticon, "meme-chat", "f-m-emoticon");
    }
}
