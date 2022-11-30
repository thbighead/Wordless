<?php

namespace App\Hookers\Ajax;

use Wordless\Abstractions\AbstractAjaxHooker;

class WordPressHookCustomFrontendAjax extends AbstractAjaxHooker
{
    /**
     * WordPress action|filter number of arguments accepted by function
     */
    protected const ACCEPTED_NUMBER_OF_ARGUMENTS = 1;
    /**
     * Define wp_ajax_ prefixed hook to let AJAX be called from admin panel (only logged in)
     */
    protected const AVAILABLE_TO_ADMIN_PANEL = false;
    /**
     * Define wp_ajax_nopriv_ prefixed hook to let AJAX be called from frontend application (log in not an obligation)
     */
    protected const AVAILABLE_TO_FRONTEND = true;
    /**
     * The function which shall be executed during hook
     */
    protected const FUNCTION = 'myCustomFunction';
    /**
     * WordPress action|filter hook identification
     */
    protected const HOOK = 'my_custom_frontend_ajax_hook';

    public static function myCustomFunction()
    {
        // Do something. This is only called if you add this class to config/hookers.php.
    }
}
