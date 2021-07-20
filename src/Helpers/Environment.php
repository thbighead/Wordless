<?php

namespace Wordless\Helpers;

class Environment
{
    public const COMMONLY_DOT_ENV_DEFAULT_VALUES = [
        'APP_NAME' => 'Wordless App',
        'APP_ENV' => 'local',
        'APP_URL' => 'http://wordless-app.dev.br',
        'FRONT_END_URL' => 'http://wordless-front.dev.br',
        'DB_NAME' => 'wordless',
        'DB_USER' => 'root',
        'DB_HOST' => '127.0.0.1',
        'DB_CHARSET' => 'null',
        'DB_COLLATE' => 'null',
        'DB_TABLE_PREFIX' => 'null',
        'WP_VERSION' => 'null',
        'WP_DEBUG' => 'true',
        'WP_ADMIN_EMAIL' => 'admin@mail.com',
        'WP_ADMIN_PASSWORD' => 'wordless_admin',
        'WP_ADMIN_USER' => 'admin',
    ];
    public const LOCAL = 'local';
    public const PRODUCTION = 'production';
    public const STAGING = 'staging';

    public static function get(string $key, $default = null)
    {
        return $_ENV[$key] ?? $default;
    }
}