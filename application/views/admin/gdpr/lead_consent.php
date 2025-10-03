<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<h4 class="no-mtop">
    <a href="<?= lead_consent_url($lead->id); ?>"
        target="_blank">
        <small>
            <?= _l('view_consent'); ?>
        </small>
    </a>
</h4>
<div class="row">
    <?php $this->load->view('admin/gdpr/consent_user_info', ['form_url' => 'gdpr/lead_consent_opt_action', 'lead_id' => $lead->id]); ?>
</div>