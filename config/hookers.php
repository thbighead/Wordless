<?php

use Wordless\Abstractions\Bootstrapper;
use Wordless\Hookers\BootControllers;
use Wordless\Hookers\BootHttpRemoteCallsLog;
use Wordless\Hookers\HideDiagnosticsFromUserRoles;
use Wordless\Hookers\HooksDebugLog;

return [
    Bootstrapper::HOOKERS_BOOT_CONFIG_KEY => [
        BootControllers::class,
        BootHttpRemoteCallsLog::class,
        HideDiagnosticsFromUserRoles::class,
        HooksDebugLog::class,
    ],

    /**
     * Defines what hooked functions should be removed before they take action.
     * You may define only the hook flag to remove all hooked functions from it
     * (be careful when doing that with WordPress native hooks because its own core uses it):
     *      Bootstrapper::HOOKERS_REMOVE_FILTER_CONFIG_KEY => ['wp_nav_menu_container_allowedtags'],
     * If instead you want to remove a specific function from a hook you may specify it with an array.
     * You may or not specify the priority level. Wordless defaults to 10 if not specified.
     * Just remember that WordPress asks you to specify the exact priority level you used when
     * added the function to action/filter:
     *      Bootstrapper::HOOKERS_REMOVE_FILTER_CONFIG_KEY => [
     *          'content_save_pre' => [
     *              Bootstrapper::HOOKERS_REMOVE_TYPE_FUNCTION_CONFIG_KEY => 'acf_parse_save_blocks',
     *              Bootstrapper::HOOKERS_REMOVE_TYPE_PRIORITY_CONFIG_KEY => 5,
     *          ],
     *      ],
     * You may also define a Wordless\Abstractions\AbstractHooker class here. When doing it,
     * Wordless is going to avoid the class initialization instead of removing it through any
     * WordPress function:
     *      Bootstrapper::HOOKERS_REMOVE_ACTION_CONFIG_KEY => [BootControllers::class],
     */
    Bootstrapper::HOOKERS_REMOVE_CONFIG_KEY => [
        Bootstrapper::HOOKERS_REMOVE_ACTION_CONFIG_KEY => [],
        Bootstrapper::HOOKERS_REMOVE_FILTER_CONFIG_KEY => [],
    ],
];
