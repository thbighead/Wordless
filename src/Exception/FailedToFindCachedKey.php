<?php

namespace Wordless\Exception;

use Exception;
use Throwable;

class FailedToFindCachedKey extends Exception
{
    public function __construct(string $full_key_string, string $partial_key_which_failed, Throwable $previous = null)
    {
        parent::__construct(
            "Failed to retrieve '$full_key_string' key from INTERNAL_WORDLESS_CACHE at '$partial_key_which_failed'.",
            0,
            $previous
        );
    }
}