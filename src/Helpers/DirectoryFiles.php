<?php

namespace Wordless\Helpers;

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
        if (($path = realpath($path)) === false) {
            throw new PathNotFoundException($path);
        }

        if ($except[$path] ?? in_array($path, $except)) {
            return;
        }

        if (is_dir($path)) {
            $files = array_diff(scandir($path), ['.', '..']);

            foreach ($files as $file) {
                self::recursiveDelete(realpath("$path/$file"), $except);
            }

            if (!$delete_root) {
                return;
            }

            if (!rmdir($path)) {
                throw new FailedToDeletePath($path);
            }

            return;
        }

        if (!unlink($path)) {
            throw new FailedToDeletePath($path);
        }
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