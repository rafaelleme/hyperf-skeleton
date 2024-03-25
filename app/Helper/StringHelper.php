<?php

namespace App\Helper;

class StringHelper
{
    public static function toCamelCase(string $input): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $input))));
    }

    public static function getJson(?string $str): ?string
    {
        $pattern = '/\{.*?}/s';

        if (preg_match($pattern, $str, $matches)) {
            return $matches[0];
        }

        return null;
    }
}