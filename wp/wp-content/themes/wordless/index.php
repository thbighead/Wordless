<?php

use Wordless\Application\Helpers\Environment;

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
    <span>Hello World!</span>
<?php endif; ?>
