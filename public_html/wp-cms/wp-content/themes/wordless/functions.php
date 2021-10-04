<?php

use Wordless\Adapters\WordlessController;
use Wordless\Exception\PathNotFoundException;
use Wordless\Exception\WordPressFailedToCreateRole;
use Wordless\Helpers\DirectoryFiles;
use Wordless\Helpers\ProjectPath;
use Wordless\Helpers\Str;

/**
 * @throws PathNotFoundException
 * @throws WordPressFailedToCreateRole
 */
function boot_controllers()
{
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

            $controller->register_routes();
            $controller->registerCapabilitiesToRoles();
        }
    }
}

add_action('rest_api_init', 'boot_controllers');