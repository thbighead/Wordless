<?php

namespace Wordless\Adapters;

use Symfony\Component\Console\Command\Command;

abstract class WordlessCommand extends Command
{
    protected const ARGUMENT_DEFAULT_FIELD = 'default';
    protected const ARGUMENT_DESCRIPTION_FIELD = 'description';
    protected const ARGUMENT_MODE_FIELD = 'mode';
    protected const ARGUMENT_NAME_FIELD = 'name';
    protected const OPTION_DEFAULT_FIELD = 'default';
    protected const OPTION_DESCRIPTION_FIELD = 'description';
    protected const OPTION_MODE_FIELD = 'mode';
    protected const OPTION_NAME_FIELD = 'name';
    protected const OPTION_SHORTCUT_FIELD = 'shortcut';

    abstract protected function arguments(): array;
    abstract protected function description(): string;
    abstract protected function help(): string;
    abstract protected function options(): array;

    protected function configure(): void
    {
        $this->setDescription($this->description())
            ->setHelp($this->help());

        foreach ($this->arguments() as $argument) {
            $this->addArgument(
                $argument[self::ARGUMENT_NAME_FIELD],
                $argument[self::ARGUMENT_MODE_FIELD] ?? null,
                $argument[self::ARGUMENT_DESCRIPTION_FIELD] ?? '',
                $argument[self::ARGUMENT_DEFAULT_FIELD] ?? null
            );
        }

        foreach ($this->options() as $option) {
            $this->addOption(
                $option[self::OPTION_NAME_FIELD],
                $option[self::OPTION_SHORTCUT_FIELD] ?? null,
                $option[self::OPTION_MODE_FIELD] ?? null,
                $option[self::OPTION_DESCRIPTION_FIELD] ?? '',
                $option[self::OPTION_DEFAULT_FIELD] ?? null
            );
        }
    }
}