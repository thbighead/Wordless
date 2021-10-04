<?php

namespace Wordless\Exception;

use Exception;
use Throwable;

class InvalidCache extends Exception
{
    public function __construct(string $cache_file_path, string $reason, Throwable $previous = null)
    {
        parent::__construct("Failed to read cache at $cache_file_path. $reason", 0, $previous);
    }
}