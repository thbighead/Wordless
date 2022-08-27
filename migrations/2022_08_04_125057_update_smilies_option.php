<?php

use Wordless\Abstractions\Migrations\Script;
use Wordless\Abstractions\WpSpeedUp;
use Wordless\Exceptions\PathNotFoundException;
use Wordless\Helpers\ProjectPath;

final class UpdateSmiliesOption implements Script
{
    private const OPTION_USE_SMILIES_KEY = 'use_smilies';
    private const OPTION_USE_SMILIES_DEFAULT_VALUE = '1';
    private const OPTION_USE_SMILIES_DESIRED_VALUE = '0';

    /**
     * @throws PathNotFoundException
     */
    public function __construct()
    {
        include_once ProjectPath::wpCore('wp-includes/option.php');
    }

    /**
     * @return void
     * @throws PathNotFoundException
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
     * @throws PathNotFoundException
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
     * @throws PathNotFoundException
     */
    private function checkConfig(): bool
    {
        return (include_once ProjectPath::config('admin.php'))[WpSpeedUp::REMOVE_WP_EMOJIS_CONFIG_KEY]
            ?? false;
    }
}
