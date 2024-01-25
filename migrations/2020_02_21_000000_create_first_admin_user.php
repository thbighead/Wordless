<?php declare(strict_types=1);

use Wordless\Application\Commands\WordlessInstall;
use Wordless\Application\Helpers\ProjectPath;
use Wordless\Infrastructure\Migration;
use Wordless\Wordpress\Models\Role\Enums\DefaultRole;

return new class extends Migration {
    private const USERNAME = 'admin';
    private const PASSWORD = 'wordless_admin';
    private const EMAIL = 'admin@mail.com';

    public function __construct()
    {
        require_once ProjectPath::wpCore('wp-admin/includes/user.php');
    }

    public function up(): void
    {
        if (($temp_admin = get_user_by('email', WordlessInstall::TEMP_MAIL)) instanceof WP_User) {
            wp_delete_user($temp_admin->ID);
        }

        $admin_users_count = (new WP_User_Query(['role' => DefaultRole::admin->name]))->get_total();

        if ($admin_users_count <= 0) {
            $user_id = wp_create_user(self::USERNAME, self::PASSWORD, self::EMAIL);

            $user = get_user_by('id', $user_id);
            $user->remove_role(DefaultRole::subscriber->name);
            $user->add_role(DefaultRole::admin->name);
        }
    }

    public function down(): void
    {
        if (($admin_created = get_user_by('email', self::EMAIL)) instanceof WP_User) {
            wp_delete_user($admin_created->ID);
        }
    }
};
