<?php

use Wordless\Abstractions\Bootstrapper;
use Wordless\Helpers\Roles;
use Wordless\Hookers\HideDiagnosticsFromUserRoles;

return [
    HideDiagnosticsFromUserRoles::SHOW_DIAGNOSTICS_CONFIG_KEY => [
        Roles::ADMIN => true,
        Roles::AUTHOR => false,
    ],
    Bootstrapper::MENUS_CONFIG_KEY => [],
];
