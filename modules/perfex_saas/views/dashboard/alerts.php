<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="col-md-12 tw-mb-4">
    <?php if ((time() - (int)get_instance()->perfex_saas_cron_model->get_settings('cron_last_success_runtime') ?? 0) > (60 * 60 * 24)) : ?>
        <div class="alert alert-danger">
            <?= _l("perfex_saas_cron_has_not_run_for_a_while"); ?>
        </div>
    <?php endif; ?>
</div>