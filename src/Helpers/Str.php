<?php

namespace Wordless\Helpers;

class Str
{
    public static function after(string $string, string $delimiter): string
    {
        $substring_position = strpos($string, $delimiter);

        if ($substring_position === false) {
            return $string;
        }

        $substring = substr($string, $substring_position + strlen($delimiter));

        return $substring !== false ? $substring : $string;
    }

    public static function afterLast(string $string, string $delimiter)
    {
        $substring_position = strrpos($string, $delimiter);

        if ($substring_position === false) {
            return $string;
        }

        return substr($string, $substring_position + strlen($delimiter));
    }

    public static function before(string $string, string $delimiter): string
    {
        $result = strstr($string, $delimiter, true);

        return $result === false ? $string : $result;
    }

    /**
     * @param string $haystack
     * @param string|string[] $needles
     * @param bool $any
     * @return bool
     */
    public static function contains(string $haystack, $needles, bool $any = true): bool
    {
        $contains = true;

        foreach ((array)$needles as $needle) {
            if (($contains &= ($needle !== '' && mb_strpos($haystack, $needle) !== false)) && $any) {
                return true;
            }

            if (!$contains && !$any) {
                return false;
            }
        }

        return $contains;
    }

    public static function endsWith(string $string, string $substring): bool
    {
        return substr($string, -strlen($substring)) === $substring;
    }

    public static function finishWith(string $string, string $finish_with): string
    {
        $quoted = preg_quote($finish_with, '/');

        return preg_replace('/(?:' . $quoted . ')+$/u', '', $string) . $finish_with;
    }

    public static function snakeCase(string $string, string $delimiter = '_'): string
    {
        return ltrim(
            mb_strtolower(
                preg_replace(
                    '/([A-z])([0-9])/',
                    '$1_$2',
                    preg_replace(
                        '/([A-Z])/',
                        '_$1',
                        preg_replace(
                            '/\W+/',
                            $delimiter,
                            trim($string)
                        )
                    )
                )
            ),
            $delimiter
        );
    }

    public static function startWith(string $string, string $start_with): string
    {
        $quoted = preg_quote($start_with, '/');

        return $start_with . preg_replace('/^(?:' . $quoted . ')+/u', '', $string);
    }

    public static function titleCase(string $string)
    {
        return mb_convert_case($string, MB_CASE_TITLE, 'UTF-8');
    }
}