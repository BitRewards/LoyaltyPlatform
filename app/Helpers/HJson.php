<?php

class HJson
{
    public static function encode($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public static function decode($json)
    {
        return json_decode($json, true);
    }
}
