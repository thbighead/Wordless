<?php

namespace Wordless\Exception;

use Exception;
use Throwable;

class FailedToCopyDotEnvExampleIntoNewDotEnv extends Exception
{
    public function __construct(
        string $dot_env_example_filepath,
        string $new_dot_env_filepath,
        Throwable $previous = null
    )
    {
        parent::__construct(
            "Couldn't copy $dot_env_example_filepath into $new_dot_env_filepath.",
            1,
            $previous
        );
    }
}