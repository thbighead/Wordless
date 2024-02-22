<?php declare(strict_types=1);

use Wordless\Application\Commands\ConfigureDateOptions;
use Wordless\Application\Commands\SyncRoles;
use Wordless\Application\Helpers\Config;
use Wordless\Application\Helpers\Environment;
use Wordless\Application\Helpers\Timezone;
use Wordless\Application\Listeners\ChooseImageEditor;
use Wordless\Application\Listeners\DisableComments\Contracts\DisableCommentsActionListener;
use Wordless\Application\Listeners\DoNotLoadWpAdminBarOutsidePanel;
use Wordless\Application\Listeners\HideDiagnosticsFromUserRoles;
use Wordless\Application\Providers\AdminCustomUrlProvider;
use Wordless\Application\Providers\RemoveEmojiProvider;
use Wordless\Application\Providers\RestApiProvider;
use Wordless\Application\Providers\WpSpeedUpProvider;
use Wordless\Core\Bootstrapper;
use Wordless\Wordpress\Enums\StartOfWeek;
use Wordless\Wordpress\Models\Role\Enums\DefaultRole;

return [
    Config::KEY_LANGUAGES => [],
    Config::KEY_THEME => 'wordless',
    Config::KEY_PERMALINK => '/%postname%/',
    Config::KEY_ADMIN => [
        RemoveEmojiProvider::CONFIG_KEY_REMOVE_WP_EMOJIS => false,
        WpSpeedUpProvider::CONFIG_KEY_SPEED_UP_WP => true,
        DoNotLoadWpAdminBarOutsidePanel::SHOW_WP_ADMIN_BAR_OUTSIDE_PANEL_CONFIG_KEY => true,
        ChooseImageEditor::CONFIG_KEY_IMAGE_LIBRARY => ChooseImageEditor::IMAGE_LIBRARY_CONFIG_VALUE_IMAGICK,
        HideDiagnosticsFromUserRoles::SHOW_DIAGNOSTICS_CONFIG_KEY => [
            DefaultRole::admin->value => true,
            DefaultRole::author->value => false,
        ],
        AdminCustomUrlProvider::CONFIG_KEY_CUSTOM_ADMIN_URI => 'blastoise',
        DisableCommentsActionListener::CONFIG_KEY_ENABLE_COMMENTS => false,
        Bootstrapper::CONFIG_KEY_ERROR_REPORTING => Environment::isProduction()
            ? E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED
            : E_ALL,
        ConfigureDateOptions::CONFIG_KEY_ADMIN_DATETIME => [
            Timezone::CONFIG_KEY => 'UTC-3',
            ConfigureDateOptions::CONFIG_KEY_ADMIN_DATETIME_DATE_FORMAT => 'F j, Y',
            ConfigureDateOptions::CONFIG_KEY_ADMIN_DATETIME_TIME_FORMAT => 'H:i',
            StartOfWeek::KEY => StartOfWeek::sunday->value,
        ],
    ],
    SyncRoles::CONFIG_KEY_PERMISSIONS => [
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
    RestApiProvider::CONFIG_KEY => [
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
