<?php

namespace Wordless\Helpers;

class Environment
{
    public const LOCAL = 'local';
    public const PRODUCTION = 'production';
    public const STAGING = 'staging';

    public static function get(string $key, $default = null)
    {
        return $_ENV[$key] ?? $default;
    }
}