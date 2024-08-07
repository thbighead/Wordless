<?php declare(strict_types=1);

namespace App\Controllers;

use Wordless\Infrastructure\Wordpress\ApiController;
use Wordless\Infrastructure\Wordpress\ApiController\Traits\Guesser;

class CustomResourceController extends ApiController
{
    use Guesser;

    protected const HAS_PERMISSIONS = true;
}
