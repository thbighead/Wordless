<?php

namespace Wordless\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wordless\Abstractions\StubMounters\WpLoadMuPluginsStubMounter;
use Wordless\Adapters\WordlessCommand;
use Wordless\Exception\FailedToCopyStub;
use Wordless\Exception\PathNotFoundException;
use Wordless\Helpers\DirectoryFiles;
use Wordless\Helpers\ProjectPath;
use Wordless\Helpers\Str;

class GenerateMustUsePluginsLoader extends WordlessCommand
{
    protected static $defaultName = 'mup:loader';
    private const WP_LOAD_MU_PLUGINS_FILENAME = 'wp-load-mu-plugins.php';

    protected function arguments(): array
    {
        return [];
    }

    protected function description(): string
    {
        return 'Generate WordPress Must Use Plugins loader from stub.';
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws FailedToCopyStub
     * @throws PathNotFoundException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->write('Generating MU Plugins autoloader...');

        $mu_plugins_directory_path = ProjectPath::wpMustUsePlugins();
        $wp_load_mu_plugins_destiny_path = Str::finishWith($mu_plugins_directory_path, DIRECTORY_SEPARATOR)
            . self::WP_LOAD_MU_PLUGINS_FILENAME;
        $include_files_script = '';

        foreach (DirectoryFiles::recursiveRead($mu_plugins_directory_path) as $filepath) {
            if (!Str::endsWith($filepath, '.php')) {
                continue;
            }

            try {
                ProjectPath::wpMustUsePlugins(basename($filepath));
                continue;
            } catch (PathNotFoundException $exception) {
                $relative_file_path = Str::startWith(
                    Str::after($filepath, $mu_plugins_directory_path),
                    DIRECTORY_SEPARATOR
                );
                $include_once_file_partial_script = "include_once __DIR__ . $relative_file_path;";

                $include_files_script .= empty($include_files_script) ?
                    $include_once_file_partial_script :
                    PHP_EOL . $include_once_file_partial_script;
            }
        }

        (new WpLoadMuPluginsStubMounter($wp_load_mu_plugins_destiny_path))->setReplaceContentDictionary([
            '// {include plugins script}' => $include_files_script,
        ])->mountNewFile();

        $output->writeln(' Done!');

        return Command::SUCCESS;
    }

    protected function help(): string
    {
        return 'This is better than looping through directories everytime the project runs.';
    }

    protected function options(): array
    {
        return [];
    }
}