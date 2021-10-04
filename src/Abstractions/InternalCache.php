<?php

namespace Wordless\Abstractions;

use Wordless\Abstractions\Cachers\ControllerCacher;
use Wordless\Exception\FailedToCopyStub;
use Wordless\Exception\FailedToFindCachedKey;
use Wordless\Exception\InvalidCache;
use Wordless\Exception\PathNotFoundException;
use Wordless\Helpers\DirectoryFiles;
use Wordless\Helpers\ProjectPath;
use Wordless\Helpers\Str;

class InternalCache
{
    private const INTERNAL_WORDLESS_CACHE_CONSTANT_NAME = 'INTERNAL_WORDLESS_CACHE';
    private const PHP_EXTENSION = '.php';

    /**
     * @throws FailedToCopyStub
     * @throws PathNotFoundException
     */
    public static function generate()
    {
        (new ControllerCacher)->cache();
    }

    public static function getValue(string $key_pathing, $default = null)
    {
        try {
            return self::getValueOrFail($key_pathing);
        } catch (FailedToFindCachedKey $exception) {
            return $default;
        }
    }

    /**
     * @param string $key_pathing_string
     * @return mixed
     * @throws FailedToFindCachedKey
     */
    public static function getValueOrFail(string $key_pathing_string)
    {
        $key_pathing = explode('.', $key_pathing_string);
        $first_key = array_shift($key_pathing);

        if (!isset(INTERNAL_WORDLESS_CACHE[$first_key])) {
            throw new FailedToFindCachedKey($key_pathing_string, $first_key);
        }

        $pointer = INTERNAL_WORDLESS_CACHE[$first_key];

        foreach ($key_pathing as $key) {
            if (!isset($pointer[$key])) {
                throw new FailedToFindCachedKey($key_pathing_string, $key);
            }

            $pointer = $pointer[$key];
        }

        return $pointer;
    }

    /**
     * @throws InvalidCache
     * @throws PathNotFoundException
     */
    public static function load()
    {
        if (!defined(self::INTERNAL_WORDLESS_CACHE_CONSTANT_NAME)) {
            define(self::INTERNAL_WORDLESS_CACHE_CONSTANT_NAME, self::retrieveCachedValues());
        }
    }

    /**
     * @return array
     * @throws InvalidCache
     * @throws PathNotFoundException
     */
    private static function retrieveCachedValues(): array
    {
        $internal_wordless_cache = [];

        foreach (DirectoryFiles::recursiveRead(ProjectPath::cache()) as $cache_file_path) {
            if (Str::endsWith($cache_file_path, '.gitignore')) {
                continue;
            }

            if (is_dir($cache_file_path) || !Str::endsWith($cache_file_path, self::PHP_EXTENSION)) {
                throw new InvalidCache($cache_file_path, 'Cache directory must have only PHP files.');
            }

            $internal_wordless_cache[Str::before(
                Str::afterLast($cache_file_path, DIRECTORY_SEPARATOR),
                self::PHP_EXTENSION
            )] = include $cache_file_path;
        }

        return $internal_wordless_cache;
    }
}