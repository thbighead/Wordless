<?php

use Wordless\Application\Helpers\Environment;
use Wordless\Application\Helpers\Str;

const URL_ROUTE_SEPARATOR = '/';

$front_end_url = rtrim(Environment::get('FRONT_END_URL'), URL_ROUTE_SEPARATOR);
$wp_home = rtrim(WP_HOME, URL_ROUTE_SEPARATOR);
?>

<?php if ($front_end_url !== $wp_home): ?>
    <meta content="0; URL='<?= $front_end_url ?>''" http-equiv="refresh">

    <!-- just in case the meta tag is not read properly, here is plan B: a JS redirect -->
    <script type="text/javascript">
        window.location = '<?= $front_end_url ?>';
    </script>
<?php else: ?>
    <!doctype html>
    <html <?php language_attributes(); ?>>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <?php wp_head(); ?>
        <title><?= Str::titleCase(Environment::get('APP_NAME')); ?></title>
    </head>
    <body>
    <span>Hello World!</span>
    <?php wp_footer(); ?>
    </body>
    </html>
<?php endif; ?>
