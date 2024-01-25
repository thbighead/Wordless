<?php

namespace App\Providers;

use Wordless\Application\Helpers\ProjectPath;
use Wordless\Application\Helpers\ProjectPath\Exceptions\PathNotFoundException;
use Wordless\Infrastructure\Provider;

class AppProvider extends Provider
{
    /**
     * @throws PathNotFoundException
     */
    public function registerMigrations(): array
    {
        return [
            ProjectPath::migrations('2020_02_21_000000_create_first_admin_user.php'),
            ProjectPath::migrations('2022_08_04_125057_update_smilies_option.php'),
        ];
    }
}
