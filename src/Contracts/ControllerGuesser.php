<?php

namespace Wordless\Contracts;

use Wordless\Abstractions\Guessers\ControllerNamespaceGuesser;
use Wordless\Abstractions\Guessers\ControllerVersionGuesser;
use Wordless\Abstractions\InternalCache;
use Wordless\Exception\FailedToFindCachedKey;

trait ControllerGuesser
{
    private ?ControllerNamespaceGuesser $namespaceGuesser;
    private ?ControllerVersionGuesser $versionGuesser;

    protected function namespace(): string
    {
        $controller_namespace_class = static::class;

        try {
            return InternalCache::getValueOrFail("controllers.$controller_namespace_class.namespace");
        } catch (FailedToFindCachedKey $exception) {
            if (!isset($this->namespaceGuesser)) {
                $this->namespaceGuesser = new ControllerNamespaceGuesser($controller_namespace_class);
            }

            return $this->namespaceGuesser->getValue();
        }
    }

    protected function version(): ?string
    {
        $controller_namespace_class = static::class;

        try {
            return InternalCache::getValueOrFail("controllers.$controller_namespace_class.version");
        } catch (FailedToFindCachedKey $exception) {
            if (!isset($this->namespaceGuesser)) {
                $this->versionGuesser = new ControllerVersionGuesser($controller_namespace_class);
            }

            $version_number = $this->versionGuesser->getValue();

            return empty($version_number) ? null : "v$version_number";
        }
    }
}