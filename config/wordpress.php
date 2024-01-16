<?php declare(strict_types=1);

use Wordless\Application\Helpers\Environment;
use Wordless\Application\Listeners\ChooseImageEditor;
use Wordless\Application\Listeners\CustomAdminUrl\Contracts\BaseListener as CustomAdminUrlListener;
use Wordless\Application\Listeners\DoNotLoadWpAdminBarOutsidePanel;
use Wordless\Application\Listeners\HideDiagnosticsFromUserRoles;
use Wordless\Application\Providers\RemoveEmojiProvider;
use Wordless\Application\Providers\RestApiProvider;
use Wordless\Application\Providers\WpSpeedUpProvider;
use Wordless\Core\Bootstrapper;
use Wordless\Wordpress\Enums\StartOfWeek;
use Wordless\Wordpress\Models\Role\Enums\DefaultRole;

return [
    'languages' => [],
    'theme' => 'wordless',
    'version' => 'latest',
    'permalink' => '/%postname%/',
    'admin' => [
        RemoveEmojiProvider::CONFIG_KEY_REMOVE_WP_EMOJIS => false,
        WpSpeedUpProvider::CONFIG_KEY_SPEED_UP_WP => true,
        DoNotLoadWpAdminBarOutsidePanel::SHOW_WP_ADMIN_BAR_OUTSIDE_PANEL_CONFIG_KEY => true,
        ChooseImageEditor::IMAGE_LIBRARY_CONFIG_KEY => ChooseImageEditor::IMAGE_LIBRARY_CONFIG_VALUE_IMAGICK,
        HideDiagnosticsFromUserRoles::SHOW_DIAGNOSTICS_CONFIG_KEY => [
            DefaultRole::admin->value => true,
            DefaultRole::author->value => false,
        ],
        CustomAdminUrlListener::REDIRECT_FROM_DEFAULTS_TO_URL_KEY => null,
        CustomAdminUrlListener::CUSTOM_ADMIN_URL_KEY => null,
        'enable_comments' => false,
        Bootstrapper::ERROR_REPORTING_KEY => Environment::isProduction()
            ? E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED
            : E_ALL,
        StartOfWeek::KEY => StartOfWeek::sunday->value,
        'datetime' => [
            'timezone' => 'UTC+0',
            'date_format' => 'F j, Y',
            'time_format' => 'H:i',
        ],
    ],
    'permissions' => [
//        'custom-admin' => [
//            'custom_cap_1' => true,
//            'custom_cap_2' => true,
//        ],
//        DefaultRole::editor->value => [
//            'moderate_comments' => true,
//            'upload_files' => false,
//            'custom_capability' => true,
//            'another_custom_capability' => false,
//        ],
    ],
    'rest-api' => [
        RestApiProvider::CONFIG_KEY_ROUTES => [
            RestApiProvider::CONFIG_ROUTES_KEY_PUBLIC => [
//                '/wp/v2',
//                '/wp/v2/pages',
//                '/wp/v2/posts',
//                '/wp/v2/users',
            ],
//            RestApiProvider::CONFIG_ROUTES_KEY_ALLOW => [],
            RestApiProvider::CONFIG_ROUTES_KEY_DISALLOW => [],
        ],
    ],
];
