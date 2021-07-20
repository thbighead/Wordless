<?php

namespace Wordless\Exception;

use Exception;
use Throwable;

class FailedToCopyStub extends Exception
{
    public function __construct(string $stub_filepath, string $new_filepath, Throwable $previous = null)
    {
        parent::__construct(
            "Couldn't copy $stub_filepath into $new_filepath.",
            1,
            $previous
        );
    }
}