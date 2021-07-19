<?php

namespace Wordless\Helpers;

class Str
{
    public static function finishWith(string $string, string $finish_with): string
    {
        $quoted = preg_quote($finish_with, '/');

        return preg_replace('/(?:' . $quoted . ')+$/u', '', $string) . $finish_with;
    }
}