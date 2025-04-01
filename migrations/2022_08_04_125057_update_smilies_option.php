<?php declare(strict_types=1);

use Wordless\Application\Helpers\Config;
use Wordless\Application\Helpers\Option;
use Wordless\Application\Providers\RemoveEmojiProvider;
use Wordless\Infrastructure\Migration;

return new class extends Migration {
    private const OPTION_USE_SMILIES_KEY = 'use_smilies';
    private const OPTION_USE_SMILIES_DEFAULT_VALUE = '1';
    private const OPTION_USE_SMILIES_DESIRED_VALUE = '0';

    public function up(): void
    {
        if ($this->checkConfig()) {
            return;
        }

        if (Option::get(
                self::OPTION_USE_SMILIES_KEY,
                self::OPTION_USE_SMILIES_DESIRED_VALUE
            ) !== self::OPTION_USE_SMILIES_DESIRED_VALUE) {
            Option::createOrUpdate(
                self::OPTION_USE_SMILIES_KEY,
                self::OPTION_USE_SMILIES_DESIRED_VALUE
            );
        }
    }

    public function down(): void
    {
        if ($this->checkConfig()) {
            return;
        }

        if (Option::get(
                self::OPTION_USE_SMILIES_KEY,
                self::OPTION_USE_SMILIES_DEFAULT_VALUE
            ) !== self::OPTION_USE_SMILIES_DEFAULT_VALUE) {
            Option::createOrUpdate(
                self::OPTION_USE_SMILIES_KEY,
                self::OPTION_USE_SMILIES_DEFAULT_VALUE
            );
        }
    }

    private function checkConfig(): bool
    {
        return (bool)Config::wordpressAdmin(RemoveEmojiProvider::CONFIG_KEY_REMOVE_WP_EMOJIS, false);
    }
};
