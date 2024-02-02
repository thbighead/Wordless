<?php declare(strict_types=1);

use App\Providers\AppProvider;
use Wordless\Application\Commands\GeneratePublicWordpressSymbolicLinks;
use Wordless\Application\Commands\Utility\DatabaseOverwrite\DTO\UserDTO;
use Wordless\Application\Helpers\Config;
use Wordless\Application\Helpers\Environment;
use Wordless\Application\Libraries\JWT\Enums\CryptoAlgorithm;
use Wordless\Application\Libraries\JWT\Token;
use Wordless\Application\Libraries\LogManager\Logger;
use Wordless\Application\Libraries\LogManager\Logger\LogFormatter;
use Wordless\Application\Providers\CommentsProvider;
use Wordless\Application\Providers\CoreProvider;
use Wordless\Application\Providers\MigrationsProvider;
use Wordless\Application\Providers\RemoveEmojiProvider;
use Wordless\Infrastructure\Provider;

$current_wp_theme = Config::get('wordpress.theme', 'wordless');
/** @var Provider[] $providers */
$providers = [
    CoreProvider::class,
    CommentsProvider::class,
    RemoveEmojiProvider::class,
    MigrationsProvider::class,
//    SeedersProvider::class,
    AppProvider::class,
];

return [
    Config::KEY_CSP => [
        'default-src' => ['self' => true],
        'font-src' => [
            'self' => true,
            'data' => true,
            'allow' => [
                'https://fonts.gstatic.com',
            ]
        ],
        'frame-src' => [
            'blob' => true,
        ],
        'frame-ancestors' => [],
        'img-src' => [
            'data' => true,
            'self' => true,
            'allow' => [
                'https://secure.gravatar.com',
                'https://s.w.org',
            ]
        ],
        'script-src' => [
            'blob' => true,
            'self' => true,
            'unsafe-inline' => true,
            'unsafe-eval' => true,
        ],
        'style-src' => [
            'self' => true,
            'unsafe-inline' => true,
            'allow' => [
                'https://fonts.googleapis.com',
            ],
        ],
        'upgrade-insecure-requests' => true,
    ],
    'database' => [
        UserDTO::USER_DEFAULT_PASSWORD_KEY => 'password',
    ],
    'jwt' => [
        Token::CONFIG_DEFAULT_CRYPTO => CryptoAlgorithm::SYMMETRIC_HMAC_SHA256,
        Token::CONFIG_SIGN_KEY => Environment::get(Token::ENVIRONMENT_SIGN_VARIABLE),
    ],
    Logger::CONFIG_KEY_LOG => [
        Logger::CONFIG_KEY_FILENAME => 'wordless.log',
        LogFormatter::CONFIG_KEY_DATETIME_FORMAT => 'd-M-Y H:i:s',
        LogFormatter::CONFIG_KEY_LINE_FORMAT => '[%datetime%] %channel%.%level_name%: %message% %context% %extra%',
        Logger::CONFIG_KEY_MAX_FILES_LIMIT => 10,
        Logger::CONFIG_KEY_WORDLESS_LINE_PREFIX => Environment::get('APP_NAME', 'wordless')
            . '.' . Environment::get('APP_ENV')
    ],
    'providers' => $providers,
    GeneratePublicWordpressSymbolicLinks::PUBLIC_SYMLINK_KEY => [
        'index.php' => '../wp/index.php',
        'wp-content/plugins' => '../wp/wp-content/plugins!.gitignore',
        "wp-content/themes/$current_wp_theme/public" => "../wp/wp-content/themes/$current_wp_theme/public",
        'wp-content/uploads' => '../wp/wp-content/uploads',
        Config::get('wordpress.admin.custom_admin_uri', 'wp-core') =>
            '../wp/wp-core!wp-config.php,wp-cron.php,xmlrpc.php',
    ],
];
