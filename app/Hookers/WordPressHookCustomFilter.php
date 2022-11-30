<?php

namespace App\Hookers;

use Wordless\Abstractions\AbstractHooker;

class WordPressHookCustomFilter extends AbstractHooker
{
    /**
     * WordPress action|filter number of arguments accepted by function
     */
    protected const ACCEPTED_NUMBER_OF_ARGUMENTS = 1;
    /**
     * The function which shall be executed during hook
     */
    protected const FUNCTION = 'myCustomFunction';
    /**
     * WordPress action|filter hook identification
     */
    protected const HOOK = 'after_setup_theme';
    /**
     * WordPress action|filter hook priority
     */
    protected const HOOK_PRIORITY = 20;
    /**
     * action or filter type (defines which method will be called: add_action or add_filter)
     */
    protected const TYPE = 'filter';

    public static function myCustomFunction($someArgument)
    {
        // Do something. This is only called if you add this class to config/hookers.php.
    }
}
