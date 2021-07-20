<?php

namespace Wordless\Exception;

use Exception;
use Throwable;

class FailedToDeletePath extends Exception
{
    public function __construct(string $path, Throwable $previous = null)
    {
        parent::__construct("Couldn't delete $path.", 1, $previous);
    }
}