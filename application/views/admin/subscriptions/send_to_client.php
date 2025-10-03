<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade email-template"
    data-editor-id=".<?= 'tinymce-' . $subscription->id; ?>"
    id="subscription_send_to_client_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <?= form_open('admin/subscriptions/send_to_email/' . $subscription->id); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <?= _l('send_subscription'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <?php
                            if ($template_disabled) {
                                echo '<div class="alert alert-danger">';
                                echo 'The email template <b><a href="' . admin_url('emails/email_template/' . $template_id) . '" target="_blank" class="alert-link">' . $template_system_name . '</a></b> is disabled. Click <a href="' . admin_url('emails/email_template/' . $template_id) . '" class="alert-link" target="_blank">here</a> to enable the email template in order to be sent successfully.';
                                echo '</div>';
                            }
$selected = [];
$contact  = get_primary_contact_user_id($subscription->clientid);
if (! $contact) {
    echo '<p class="text-danger">The system is unable to find primary contact for this customer. Make sure that this customer have active primary contact.</p><hr />';
} else { ?>
                            <div
                                class="tw-bg-neutral-50 tw-rounded-md tw-p-6 tw-border tw-border-solid tw-border-neutral-200 tw-mb-4 bold text-center">
                                <?= _l('subscription_will_send_to_primary_contact'); ?>
                            </div>
                            <?php } ?>
                        </div>
                        <?= render_input('cc', 'CC'); ?>
                        <hr />
                        <h5 class="bold">
                            <?= _l('invoice_send_to_client_preview_template'); ?>
                        </h5>
                        <hr />
                        <?= render_textarea('email_template_custom', '', $template->message, [], [], '', 'tinymce-' . $subscription->id); ?>
                        <?= form_hidden('template_name', $template_name); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                    data-dismiss="modal"><?= _l('close'); ?></button>
                <button type="submit" autocomplete="off"
                    data-loading-text="<?= _l('wait_text'); ?>"
                    class="btn btn-primary"><?= _l('send'); ?></button>
            </div>
        </div>
        <?= form_close(); ?>
    </div>
</div>