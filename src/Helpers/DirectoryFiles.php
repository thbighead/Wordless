<?php

namespace Wordless\Helpers;

use Generator;
use Wordless\Exception\FailedToDeletePath;
use Wordless\Exception\PathNotFoundException;

class DirectoryFiles
{
    /**
     * @param string $path
     * @param string[] $except
     * @param bool $delete_root
     * @throws FailedToDeletePath
     * @throws PathNotFoundException
     */
    public static function recursiveDelete(string $path, array $except = [], bool $delete_root = true)
    {
        if (($real_path = realpath($path)) === false) {
            throw new PathNotFoundException($path);
        }

        if ($except[$real_path] ?? in_array($real_path, $except)) {
            return;
        }

        if (is_dir($real_path)) {
            $files = array_diff(scandir($real_path), ['.', '..']);

            foreach ($files as $file) {
                self::recursiveDelete("$real_path/$file", $except);
            }

            if (!$delete_root) {
                return;
            }

            if (!rmdir($real_path)) {
                throw new FailedToDeletePath($real_path);
            }

            return;
        }

        if (!unlink($real_path)) {
            throw new FailedToDeletePath($real_path);
        }
    }

    /**
     * @param string $path
     * @return Generator|void
     * @throws PathNotFoundException
     */
    public static function recursiveRead(string $path)
    {
        if (($real_path = realpath($path)) === false) {
            throw new PathNotFoundException($path);
        }

        if (is_dir($real_path)) {
            $files = array_diff(scandir($real_path), ['.', '..']);

            foreach ($files as $file) {
                yield from static::recursiveRead("$real_path/$file");
            }

            return;
        }

        yield $real_path;
    }

    /**
     * @param string $path
     * @throws FailedToDeletePath
     * @throws PathNotFoundException
     */
    public static function delete(string $path)
    {
        if (($path = realpath($path)) === false) {
            throw new PathNotFoundException($path);
        }

        if (is_dir($path)) {
            if (!rmdir($path)) {
                throw new FailedToDeletePath($path);
            }

            return;
        }

        if (!unlink($path)) {
            throw new FailedToDeletePath($path);
        }
    }
}