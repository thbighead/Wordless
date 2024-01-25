<?php /** @noinspection PhpUnused */


use Wordless\Application\Helpers\ProjectPath;
use Wordless\Application\Providers\RemoveEmojiProvider;
use Wordless\Infrastructure\Migration;

return new class extends Migration {
    private const OPTION_USE_SMILIES_KEY = 'use_smilies';
    private const OPTION_USE_SMILIES_DEFAULT_VALUE = '1';
    private const OPTION_USE_SMILIES_DESIRED_VALUE = '0';

    public function __construct()
    {
        include_once ProjectPath::wpCore('wp-includes/option.php');
    }

    /**
     * @return void
     */
    public function up(): void
    {
        if ($this->checkConfig()) {
            return;
        }

        if (get_option(self::OPTION_USE_SMILIES_KEY) !== self::OPTION_USE_SMILIES_DESIRED_VALUE) {
            update_option(self::OPTION_USE_SMILIES_KEY, self::OPTION_USE_SMILIES_DESIRED_VALUE);
        }
    }

    /**
     * @return void
     */
    public function down(): void
    {
        if ($this->checkConfig()) {
            return;
        }

        if (get_option(self::OPTION_USE_SMILIES_KEY) !== self::OPTION_USE_SMILIES_DEFAULT_VALUE) {
            update_option(self::OPTION_USE_SMILIES_KEY, self::OPTION_USE_SMILIES_DEFAULT_VALUE);
        }
    }

    /**
     * @return bool
     */
    private function checkConfig(): bool
    {
        return (include_once ProjectPath::config('wordpress.php'))[RemoveEmojiProvider::CONFIG_KEY_REMOVE_WP_EMOJIS]
            ?? false;
    }
};
