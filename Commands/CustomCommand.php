<?php

namespace Wordless\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wordless\Adapters\WordlessCommand;

class CustomCommand extends WordlessCommand
{
    protected static $defaultName = 'custom:command';

    protected function arguments(): array
    {
        return [];
    }

    protected function description(): string
    {
        return 'A custom command.';
    }

    protected function help(): string
    {
        return 'A custom command. More info at https://symfony.com/doc/current/console.html';
    }

    protected function options(): array
    {
        return [];
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->write('Your input arguments: ');
        dump($input->getArguments());
        $output->write('Your input options: ');
        dump($input->getOptions());

        return Command::SUCCESS;
    }
}