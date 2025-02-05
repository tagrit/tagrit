<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="mtop40">
    <div class="col-md-4 col-md-offset-4 text-center forgot-password-heading">
        <h1 class="tw-font-semibold mbot20">
            <?= _l('customer_forgot_password_heading'); ?>
        </h1>
    </div>
    <div class="col-md-4 col-md-offset-4">
        <div class="panel_s">
            <div class="panel-body">
                <?= form_open($this->uri->uri_string(), ['id' => 'forgot-password-form']); ?>
                <?= validation_errors('<div class="alert alert-danger text-center">', '</div>'); ?>
                <?php if ($this->session->flashdata('message-danger')) { ?>
                <div class="alert alert-danger">
                    <?= $this->session->flashdata('message-danger'); ?>
                </div>
                <?php } ?>
                <?= render_input('email', 'customer_forgot_password_email', '', 'email'); ?>
                <div class="form-group">
                    <button type="submit"
                        class="btn btn-primary btn-block"><?= _l('customer_forgot_password_submit'); ?></button>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>