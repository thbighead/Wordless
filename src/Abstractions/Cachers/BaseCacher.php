<?php

namespace Wordless\Abstractions\Cachers;

use Wordless\Abstractions\StubMounters\SimpleCacheStubMounter;
use Wordless\Exception\FailedToCopyStub;
use Wordless\Exception\PathNotFoundException;
use Wordless\Helpers\Arr;
use Wordless\Helpers\ProjectPath;

abstract class BaseCacher
{
    private SimpleCacheStubMounter $simpleCacheStubMounter;

    abstract protected function cacheFilename(): string;

    abstract protected function mountCacheArray(): array;

    /**
     * @throws FailedToCopyStub failed to create cache file.
     * @throws PathNotFoundException 'cache' directory was not found.
     */
    public function cache()
    {
        try {
            $cache_file_path = ProjectPath::cache($this->cacheFilename());
            $cached_values = include $cache_file_path;
            $array_to_cache = $this->mountCacheArray();

            if (Arr::isAssociative($array_to_cache)) {
                Arr::recursiveJoin($cached_values, $array_to_cache);
            } else {
                Arr::recursiveJoin($cached_values, ...$array_to_cache);
            }
        } catch (PathNotFoundException $exception) {
            $cache_file_path = ProjectPath::cache() . DIRECTORY_SEPARATOR . $this->cacheFilename();
            $array_to_cache = $this->mountCacheArray();

            if (!Arr::isAssociative($array_to_cache)) {
                $array_to_cache = Arr::recursiveJoin(...$array_to_cache);
            }
        }

        $stubMounterClass = $this->stubMounterClass();
        $this->simpleCacheStubMounter = new $stubMounterClass($cache_file_path);
        $this->simpleCacheStubMounter->setReplaceContentDictionary($array_to_cache)
            ->mountNewFile();
    }

    public function getSimpleCacheStubMounter(): ?SimpleCacheStubMounter
    {
        return $this->simpleCacheStubMounter;
    }

    protected function stubMounterClass(): string
    {
        return SimpleCacheStubMounter::class;
    }
}