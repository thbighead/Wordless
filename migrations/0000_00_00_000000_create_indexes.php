<?php declare(strict_types=1);

use Wordless\Application\Commands\Traits\RunWpCliCommand;
use Wordless\Infrastructure\Migration;

return new class extends Migration {
    use RunWpCliCommand;

    public function up(): void
    {
        $this->runWpCliCommand('index-mysql enable --all');
    }

    public function down(): void
    {
        $this->runWpCliCommand('index-mysql disable --all');
    }
};
