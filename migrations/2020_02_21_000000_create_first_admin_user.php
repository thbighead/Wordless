<?php declare(strict_types=1);

use Wordless\Application\Commands\WordlessInstall;
use Wordless\Infrastructure\Migration;
use Wordless\Wordpress\Models\Role\Enums\DefaultRole;
use Wordless\Wordpress\Models\User;
use Wordless\Wordpress\Models\User\WordlessUser;

return new class extends Migration {
    public function up(): void
    {
        User::findByEmail(WordlessInstall::TEMP_MAIL)?->delete();

        if (count(User::getByRole(DefaultRole::admin)) <= 0) {
            WordlessUser::create();
        }
    }

    public function down(): void
    {
        WordlessUser::find()?->delete();
    }
};
