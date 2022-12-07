<?php

namespace App\Controllers;

use Wordless\Adapters\ApiController;
use Wordless\Contracts\Controller\Guesser;

class CustomResourceController extends ApiController
{
    use Guesser;

    protected const HAS_PERMISSIONS = true;
}
