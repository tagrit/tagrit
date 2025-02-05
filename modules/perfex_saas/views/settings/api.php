<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();
?>

<div class="tw-flex tw-flex-col">

    <?php
    $key = 'perfex_saas_enable_api';
    render_yes_no_option($key, _l($key));
    ?>

    <div class="tw-mt-4 tw-mb-4">
        <hr />
    </div>

    <?php
    $key = 'perfex_saas_api_allow_public_access_to_doc';
    $apiDocLink = base_url(PERFEX_SAAS_ROUTE_NAME . '/api/docs');
    ?>
    <?= render_yes_no_option($key, $key, ''); ?>

</div>