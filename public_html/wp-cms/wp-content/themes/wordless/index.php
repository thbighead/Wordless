<?php

use Wordless\Helpers\Environment;

$front_end_url = Environment::get('FRONT_END_URL', Environment::get('APP_URL'));
?>

<meta content="0; URL='<?= $front_end_url ?>''" http-equiv="refresh">

<!-- just in case the meta tag is not read properly, here is plan B: a JS redirect -->
<script type="text/javascript">
    window.location = '<?= $front_end_url ?>';
</script>