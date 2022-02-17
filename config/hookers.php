<?php

use Wordless\Hookers\BootControllers;
use Wordless\Hookers\BootHttpRemoteCallsLog;
use Wordless\Hookers\HideDiagnosticsFromUserRoles;

return [
    BootControllers::class,
    BootHttpRemoteCallsLog::class,
    HideDiagnosticsFromUserRoles::class,
];
