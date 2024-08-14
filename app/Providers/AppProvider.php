<?php declare(strict_types=1);

namespace App\Providers;

use Wordless\Application\Helpers\ProjectPath;
use Wordless\Application\Helpers\ProjectPath\Exceptions\PathNotFoundException;
use Wordless\Infrastructure\Provider;

class AppProvider extends Provider
{
    /**
     * @return string[]
     * @throws PathNotFoundException
     */
    public function registerMigrations(): array
    {
        return [
            ProjectPath::migrations(),
        ];
    }
}
