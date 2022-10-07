<?php

use Wordless\Abstractions\Migrations\Script;
use Wordless\Adapters\Role;
use Wordless\Commands\WordlessInstall;
use Wordless\Exceptions\PathNotFoundException;
use Wordless\Helpers\ProjectPath;

final class CreateFirstAdminUser implements Script
{
    private const USERNAME = 'admin';
    private const PASSWORD = 'wordless_admin';
    private const EMAIL = 'admin@mail.com';

    /**
     * @throws PathNotFoundException
     */
    public function __construct()
    {
        require_once ProjectPath::wpCore('wp-admin/includes/user.php');
    }

    public function up(): void
    {
        if (($temp_admin = get_user_by('email', WordlessInstall::TEMP_MAIL)) instanceof WP_User) {
            wp_delete_user($temp_admin->ID);
        }

        $admin_users_count = (new WP_User_Query([Role::KEY => Role::ADMIN]))->get_total();

        if ($admin_users_count <= 0) {
            $user_id = wp_create_user(self::USERNAME, self::PASSWORD, self::EMAIL);

            $user = get_user_by('id', $user_id);
            $user->remove_role(Role::SUBSCRIBER);
            $user->add_role(Role::ADMIN);
        }
    }

    public function down(): void
    {
        if (($admin_created = get_user_by('email', self::EMAIL)) instanceof WP_User) {
            wp_delete_user($admin_created->ID);
        }
    }
}
