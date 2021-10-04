<?php

namespace Wordless\Abstractions\Guessers;

abstract class ControllerGuesser extends BaseGuesser
{
    protected string $controller_namespace_class;

    public function __construct(string $controller_namespace_class)
    {
        $this->controller_namespace_class = $controller_namespace_class;
    }
}