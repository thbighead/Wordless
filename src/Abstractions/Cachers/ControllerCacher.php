<?php

namespace Wordless\Abstractions\Cachers;

use ReflectionException;
use ReflectionMethod;
use Wordless\Adapters\WordlessController;
use Wordless\Exception\PathNotFoundException;
use Wordless\Helpers\DirectoryFiles;
use Wordless\Helpers\ProjectPath;
use Wordless\Helpers\Str;

class ControllerCacher extends BaseCacher
{
    protected function cacheFilename(): string
    {
        return 'controllers.php';
    }

    /**
     * @throws ReflectionException
     * @throws PathNotFoundException
     */
    protected function mountCacheArray(): array
    {
        $controllers_cache_array = [];
        $controllers_directory_path = ProjectPath::controllers();

        foreach (DirectoryFiles::recursiveRead($controllers_directory_path) as $controller_path) {
            if (is_dir($controller_path)) {
                continue;
            }

            if (Str::endsWith($controller_path, 'Controller.php')) {
                $controller_relative_filepath_without_extension = trim(Str::after(
                    substr($controller_path, 0, -4), // Removes '.php'
                    $controllers_directory_path
                ), DIRECTORY_SEPARATOR);
                $controller_full_namespace = '\\Controllers';

                foreach (explode(
                             DIRECTORY_SEPARATOR,
                             $controller_relative_filepath_without_extension
                         ) as $controller_pathing) {
                    $controller_full_namespace .= "\\$controller_pathing";
                }

                require_once $controller_path;
                /** @var WordlessController $controller */
                $controller = new $controller_full_namespace;

                $controllers_cache_array[$controller_full_namespace] = $this
                    ->extractNamespaceAndVersionFromController($controller, $controller_full_namespace);
            }
        }

        return $controllers_cache_array;
    }

    /**
     * @throws ReflectionException
     */
    private function extractNamespaceAndVersionFromController(
        WordlessController $controller,
        string             $controller_class_namespace
    ): array
    {
        $controllerNamespaceMethod = new ReflectionMethod($controller_class_namespace, 'namespace');
        $controllerVersionMethod = new ReflectionMethod($controller_class_namespace, 'version');
        $controllerNamespaceMethod->setAccessible(true);
        $controllerVersionMethod->setAccessible(true);

        return [
            'namespace' => $controllerNamespaceMethod->invoke($controller),
            'version' => $controllerVersionMethod->invoke($controller),
        ];
    }
}