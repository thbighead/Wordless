<?php

use Wordless\Abstractions\Bootstrapper;
use Wordless\Abstractions\WpSpeedUp;
use Wordless\Helpers\Roles;
use Wordless\Hookers\HideDiagnosticsFromUserRoles;
use Wordless\Hookers\DoNotLoadWpAdminBarOutsidePanel;

return [
    WpSpeedUp::REMOVE_WP_EMOJIS_CONFIG_KEY => false,
    WpSpeedUp::SPEED_UP_WP_CONFIG_KEY => true,
    DoNotLoadWpAdminBarOutsidePanel::SHOW_WP_ADMIN_BAR_OUTSIDE_PANEL_CONFIG_KEY => true,
    HideDiagnosticsFromUserRoles::SHOW_DIAGNOSTICS_CONFIG_KEY => [
        Roles::ADMIN => true,
        Roles::AUTHOR => false,
    ],
    Bootstrapper::MENUS_CONFIG_KEY => [],
];
