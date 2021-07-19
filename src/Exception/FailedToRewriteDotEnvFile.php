<?php

namespace Wordless\Exception;

use Exception;
use Throwable;

class FailedToRewriteDotEnvFile extends Exception
{
    public function __construct(string $dot_env_filepath, string $dot_env_content, Throwable $previous = null)
    {
        parent::__construct(
            "Couldn't write the following content into $dot_env_filepath:\n$dot_env_content",
            1,
            $previous
        );
    }
}