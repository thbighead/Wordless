<?php

namespace Wordless\Abstractions\StubMounters;

class WpLoadMuPluginsStubMounter extends BaseStubMounter
{
    protected function relativeStubFilename(): string
    {
        return 'wp-load-mu-plugins.php';
    }
}