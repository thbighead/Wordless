<?php

namespace Wordless\Adapters;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Request extends SymfonyRequest
{
    public const EDITABLE = 'PUT, PATCH';
}