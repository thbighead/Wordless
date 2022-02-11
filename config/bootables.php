<?php

use Wordless\Bootables\BootControllers;
use Wordless\Bootables\BootHttpRemoteCallsLog;
use Wordless\Bootables\HideDiagnosticsFromUserRoles;

return [
    BootControllers::class,
    BootHttpRemoteCallsLog::class,
    HideDiagnosticsFromUserRoles::class,
];
