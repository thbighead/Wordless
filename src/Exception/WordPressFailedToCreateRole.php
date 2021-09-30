<?php

namespace Wordless\Exception;

use Exception;
use Throwable;
use Wordless\Helpers\Str;

class WordPressFailedToCreateRole extends Exception
{
    /**
     * PathNotFoundException constructor.
     *
     * @param string $failed_role_string_identifier
     * @param Throwable|null $previous
     */
    public function __construct(string $failed_role_string_identifier, Throwable $previous = null)
    {
        parent::__construct("'$failed_role_string_identifier' role couldn't be created as "
            . Str::titleCase($failed_role_string_identifier) . '.', 1, $previous);
    }
}