<?php

namespace Wordless\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wordless\Adapters\WordlessCommand;
use Wordless\Exception\PathNotFoundException;
use Wordless\Helpers\ProjectPath;

class WpCliCaller extends WordlessCommand
{
    public const COMMAND_NAME = 'wp:run';
    public const WP_CLI_FULL_COMMAND_STRING_ARGUMENT_NAME = 'wp_cli_full_command_string';

    protected static $defaultName = self::COMMAND_NAME;

    protected function arguments(): array
    {
        return [
            [
                self::ARGUMENT_DEFAULT_FIELD => '',
                self::ARGUMENT_DESCRIPTION_FIELD => 'A string containing exactly what you want to run with "wp"',
                self::ARGUMENT_MODE_FIELD => InputArgument::OPTIONAL,
                self::ARGUMENT_NAME_FIELD => self::WP_CLI_FULL_COMMAND_STRING_ARGUMENT_NAME,
            ],
        ];
    }

    protected function description(): string
    {
        return 'Runs a WP-CLI command automatically choosing script according to OS.';
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws PathNotFoundException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $wp_cli_full_command_string = $input->getArgument(self::WP_CLI_FULL_COMMAND_STRING_ARGUMENT_NAME);
        $wp_cli_filepath = $this->chooseWpCliScriptByOperationalSystem();
        $full_command = "$wp_cli_filepath $wp_cli_full_command_string --path=public_html/wp-cms/wp-core";

        if ($output->isVerbose()) {
            $output->writeln("Executing $full_command...");
        }

        passthru($full_command, $return_var);

        return $return_var;
    }

    protected function help(): string
    {
        return 'Instead of choosing the right script according to your OS and executing it with absolute path inside vendor/bin folder, just run php console wp:run "{command}".';
    }

    protected function options(): array
    {
        return [];
    }

    /**
     * @return string
     * @throws PathNotFoundException
     */
    private function chooseWpCliScriptByOperationalSystem(): string
    {
        $script_filename = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? 'wp.bat' : 'wp';

        return ProjectPath::vendor("bin/$script_filename");
    }
}
