<?php

use Wordless\Hookers\BootControllers;
use Wordless\Hookers\BootHttpRemoteCallsLog;
use Wordless\Hookers\HideDiagnosticsFromUserRoles;
use Wordless\Hookers\HooksDebugLog;

return [
    BootControllers::class,
    BootHttpRemoteCallsLog::class,
    HideDiagnosticsFromUserRoles::class,
    HooksDebugLog::class,
];
