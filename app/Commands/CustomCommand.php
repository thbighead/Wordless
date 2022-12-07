<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Wordless\Adapters\ConsoleCommand;

class CustomCommand extends ConsoleCommand
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

    protected function runIt(): int
    {
        $this->output->write('Your input arguments: ');
        dump($this->input->getArguments());
        $this->output->write('Your input options: ');
        dump($this->input->getOptions());

        return Command::SUCCESS;
    }
}
