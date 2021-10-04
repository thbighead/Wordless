<?php

namespace Wordless\Adapters;

use WP_User;

class User
{
    public ?WP_User $wp_user;

    public function __construct(?WP_User $wp_user = null)
    {
        $this->wp_user = $wp_user ?? wp_get_current_user();
    }

    public function can($capability, ...$args): bool
    {
        if (is_null($this->wp_user)) {
            return false;
        }

        return $this->wp_user->has_cap($capability, ...$args);
    }
}