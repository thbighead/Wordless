<?php

namespace Wordless\Abstractions\Guessers;

use Wordless\Helpers\Str;

class ControllerNamespaceGuesser extends ControllerGuesser
{
    /**
     * @return string
     */
    protected function guessValue()
    {
        return Str::snakeCase(
            Str::before(
                Str::afterLast($this->controller_namespace_class, '\\'),
                'Controller'
            )
        );
    }
}