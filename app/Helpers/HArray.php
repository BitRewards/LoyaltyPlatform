<?php

class HArray
{
    public static function convertToKeyValueStr($array, $keyValueSeparator = ' — ', $rowSeparator = "\n")
    {
        $result = [];

        foreach ($array as $key => $value) {
            $result[] = $key.$keyValueSeparator.$value;
        }

        return implode($rowSeparator, $result);
    }
}
