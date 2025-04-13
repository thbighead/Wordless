<?php declare(strict_types=1);

use Wordless\Application\Commands\WpCliCaller;
use Wordless\Infrastructure\Migration;

return new class extends Migration {
    public function up(): void
    {
        $this->callConsoleCommand(WpCliCaller::COMMAND_NAME, ['"index-mysql enable --all"']);
    }

    public function down(): void
    {
        $this->callConsoleCommand(WpCliCaller::COMMAND_NAME, ['"index-mysql disable --all"']);
    }
};
