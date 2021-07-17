<?php

namespace Wordless\Exception;

use Exception;
use Throwable;

class PathNotFoundException extends Exception
{
    /**
     * PathNotFoundException constructor.
     *
     * @param string $path
     * @param Throwable|null $previous
     */
    public function __construct(string $path, Throwable $previous = null)
    {
        parent::__construct("'$path' not found.", 1, $previous);
    }
}