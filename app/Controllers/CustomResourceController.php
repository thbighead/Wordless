<?php

namespace App\Controllers;

use Wordless\Adapters\WordlessController;
use Wordless\Contracts\Controller\Guesser;

class CustomResourceController extends WordlessController
{
    use Guesser;

    protected const HAS_PERMISSIONS = true;
}
