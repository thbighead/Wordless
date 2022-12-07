<?php

namespace App\Hookers\Ajax;

use Wordless\Abstractions\AjaxHooker;

class WordPressHookCustomAdminAjax extends AjaxHooker
{
    /**
     * WordPress action|filter number of arguments accepted by function
     */
    protected const ACCEPTED_NUMBER_OF_ARGUMENTS = 1;
    /**
     * Define wp_ajax_ prefixed hook to let AJAX be called from admin panel (only logged in)
     */
    protected const AVAILABLE_TO_ADMIN_PANEL = true;
    /**
     * Define wp_ajax_nopriv_ prefixed hook to let AJAX be called from frontend application (log in not an obligation)
     */
    protected const AVAILABLE_TO_FRONTEND = false;
    /**
     * The function which shall be executed during hook
     */
    protected const FUNCTION = 'myCustomFunction';
    /**
     * WordPress action|filter hook identification
     */
    protected const HOOK = 'my_custom_admin_ajax_hook';

    public static function myCustomFunction()
    {
        // Do something. This is only called if you add this class to config/hookers.php.
    }
}
