<?php

namespace Wordless\Helpers;

use Wordless\Exception\PathNotFoundException;

class ProjectPath
{
    private const SLASH = '/';

    /**
     * @param string $additional_path
     * @return string
     * @throws PathNotFoundException
     */
    public static function publicHtml(string $additional_path = ''): string
    {
        return self::root("public_html/$additional_path");
    }

    /**
     * @param string $additional_path
     * @return string
     * @throws PathNotFoundException
     */
    public static function root(string $additional_path = ''): string
    {
        return self::full($additional_path);
    }

    /**
     * @param string $additional_path
     * @return string
     * @throws PathNotFoundException
     */
    public static function src(string $additional_path = ''): string
    {
        return self::root("src/$additional_path");
    }

    /**
     * @param string $additional_path
     * @return string
     * @throws PathNotFoundException
     */
    public static function vendor(string $additional_path = ''): string
    {
        return self::root("vendor/$additional_path");
    }

    /**
     * @param string $additional_path
     * @return string
     * @throws PathNotFoundException
     */
    public static function wpCms(string $additional_path = ''): string
    {
        return self::publicHtml("wp-cms/$additional_path");
    }

    /**
     * @param string $additional_path
     * @return string
     * @throws PathNotFoundException
     */
    public static function wpCore(string $additional_path = ''): string
    {
        return self::wpCms("wp-core/$additional_path");
    }

    /**
     * @param string $additional_path
     * @return string
     * @throws PathNotFoundException
     */
    public static function wpContent(string $additional_path = ''): string
    {
        return self::wpCms("wp-content/$additional_path");
    }

    /**
     * @param string $path
     * @return string
     * @throws PathNotFoundException
     */
    private static function full(string $path = ''): string
    {
        $path = ROOT_PROJECT_PATH . self::SLASH . trim($path, self::SLASH);

        if (($real_path = realpath($path)) === false) {
            throw new PathNotFoundException($path);
        }

        return $real_path;
    }
}