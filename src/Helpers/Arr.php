<?php

namespace Wordless\Helpers;

class Arr
{
    public static function isAssociative(array $array): bool
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }

    public static function recursiveJoin(array ...$arrays): array
    {
        $joined_array = [];

        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                if (!isset($joined_array[$key])) {
                    $joined_array[$key] = $value;
                    continue;
                }

                if (!is_array($joined_array[$key])) {
                    $joined_array[$key] = $value;
                    continue;
                }

                if (!is_array($value)) {
                    $joined_array[$key][] = $value;
                    continue;
                }

                $joined_array[$key] = static::recursiveJoin($joined_array[$key], $value);
            }
        }

        return $joined_array;
    }
}