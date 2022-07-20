<?php

use Wordless\Commands\GeneratePublicWordpressSymbolicLinks;
use Wordless\Helpers\Environment;

$current_wp_theme = Environment::get('WP_THEME', 'wordless');

return [
    'index.php' => '../wp/index.php',
    'wp-content/plugins' => '../wp/wp-content/plugins',
    "wp-content/themes/$current_wp_theme/public" => "../wp/wp-content/themes/$current_wp_theme/public",
    'wp-content/uploads' => '../wp/wp-content/uploads',
    'wp-core' => '../wp/wp-core!wp-config.php',
];
