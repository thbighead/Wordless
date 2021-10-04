<?php

namespace Wordless\Controllers;

use Wordless\Adapters\WordlessController;
use Wordless\Contracts\ControllerGuesser;

class CustomResourceController extends WordlessController
{
    use ControllerGuesser;
}