<?php

use Wordless\Abstractions\Migrations\Script;
use Wordless\Commands\WordlessInstall;

class CreateFirstAdminUser implements Script
{
    private const USERNAME = 'admin';
    private const PASSWORD = 'wordless_admin';
    private const EMAIL = 'admin@mail.com';

    public function up(): void
    {
        if (($temp_admin = get_user_by('email', WordlessInstall::TEMP_MAIL)) instanceof WP_User) {
            wp_delete_user($temp_admin->ID);
        }

        $admin_users_count = (new WP_User_Query(['role' => 'administrator']))->get_total();

        if ($admin_users_count <= 0) {
            wp_create_user(self::USERNAME, self::PASSWORD, self::EMAIL);
        }
    }

    public function down(): void
    {
        if (($admin_created = get_user_by('email', self::EMAIL)) instanceof WP_User) {
            wp_delete_user($admin_created->ID);
        }
    }
}
