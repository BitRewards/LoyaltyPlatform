<?php

class HHtml
{
    public static function link($text, $url = '#', $htmlOptions = array())
    {
        $result = '<a href="'.$url.'" ';

        foreach ($htmlOptions as $key => $value) {
            $result .= $key.'="'.$value.'"';
        }

        return $result.">$text</a>";
    }

    public static function linkBlank($text, $url = '#', $htmlOptions = array())
    {
        $htmlOptions['target'] = '_blank';

        return self::link($text, $url, $htmlOptions);
    }

    public static function preventUrlFromEmailAutoUnderline($url)
    {
        if (preg_match("/[^\/](\/)[^\/]/i", $url, $matches, PREG_OFFSET_CAPTURE)) {
            $stopIndex = $matches[1][1];
        } else {
            $stopIndex = strlen($url);
        }
        $result = '';

        for ($i = 0; $i < $stopIndex; ++$i) {
            $result .= '<span>'.$url[$i].'</span>';
        }
        $result .= substr($url, $stopIndex);

        return $result;
    }

    public static function stripSingleTag($html, $tag)
    {
        $html = preg_replace('/<'.$tag.'[^>]*>/i', '', $html);

        $html = preg_replace('/<\/'.$tag.'\s+>/i', '', $html);

        return $html;
    }

    public static function getSymbolByCharCode($charCode)
    {
        return mb_convert_encoding('&#'.intval($charCode).';', 'UTF-8', 'HTML-ENTITIES');
    }
}
