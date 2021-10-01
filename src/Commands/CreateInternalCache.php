<?php

namespace Wordless\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wordless\Abstractions\InternalCache;
use Wordless\Adapters\WordlessCommand;
use Wordless\Exception\FailedToCopyStub;
use Wordless\Exception\PathNotFoundException;
use Wordless\Helpers\ProjectPath;

class CreateInternalCache extends WordlessCommand
{
    protected static $defaultName = 'cache:create';

    protected function arguments(): array
    {
        return [];
    }

    protected function description(): string
    {
        return 'Generate Wordless internal cache files.';
    }

    protected function help(): string
    {
        return 'Generate Wordless internal cache files to avoid some uses of reflections and calculations throughout system booting.';
    }

    protected function options(): array
    {
        return [];
    }

    /**
     * @throws PathNotFoundException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        include_once ProjectPath::wpCore('wp-config.php');

        try {
            InternalCache::generate();
            return Command::SUCCESS;
        } catch (FailedToCopyStub | PathNotFoundException $exception) {
            $output->writeln($exception->getMessage());
            return Command::FAILURE;
        }
    }
}