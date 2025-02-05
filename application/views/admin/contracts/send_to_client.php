<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade email-template"
    data-editor-id=".<?= 'tinymce-' . $contract->id; ?>"
    id="contract_send_to_client_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <?= form_open('admin/contracts/send_to_email/' . $contract->id); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <?= _l('contract_send_to_client_modal_heading'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <?php
                            if ($template_disabled) {
                                echo '<div class="alert alert-danger">';
                                echo 'The email template <b><a href="' . admin_url('emails/email_template/' . $template_id) . '" class="alert-link" target="_blank">' . $template_system_name . '</a></b> is disabled. Click <a href="' . admin_url('emails/email_template/' . $template_id) . '" class="alert-link" target="_blank">here</a> to enable the email template in order to be sent successfully.';
                                echo '</div>';
                            }
$selected = [];
$contacts = $this->clients_model->get_contacts($contract->client, ['active' => 1, 'contract_emails' => 1]);

foreach ($contacts as $contact) {
    array_push($selected, $contact['id']);
}
if (count($selected) == 0) {
    echo '<p class="text-danger">' . _l('sending_email_contact_permissions_warning', _l('customer_permission_contract')) . '</p><hr />';
}
echo render_select('sent_to[]', $contacts, ['id', 'email', 'firstname,lastname'], 'contract_send_to', $selected, ['multiple' => true], [], '', '', false);

?>
                        </div>
                        <?= render_input('cc', 'CC'); ?>
                        <hr />
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox"
                                <?= empty($contract->content) ? 'disabled' : 'checked'; ?>
                            name="attach_pdf" id="attach_pdf">
                            <label for="attach_pdf">
                                <?= _l('contract_send_to_client_attach_pdf'); ?>
                            </label>
                        </div>
                        <h5 class="bold">
                            <?= _l('contract_send_to_client_preview_template'); ?>
                        </h5>
                        <hr />
                        <?= render_textarea('email_template_custom', '', $template->message, [], [], '', 'tinymce-' . $contract->id); ?>
                        <?= form_hidden('template_name', $template_name); ?>
                    </div>
                </div>
                <?php if (count($contract->attachments) > 0) { ?>
                <hr />
                <div class="row">
                    <div class="col-md-12">
                        <h5 class="bold no-margin">
                            <?= _l('include_attachments_to_email'); ?>
                        </h5>
                        <hr />
                        <?php foreach ($contract->attachments as $attachment) { ?>
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox"
                                <?= ! empty($attachment['external']) ? 'disabled' : ''; ?>
                            value="<?= e($attachment['id']); ?>"
                            name="email_attachments[]">
                            <label for="">
                                <a
                                    href="<?= site_url('download/file/contract/' . $attachment['attachment_key']); ?>">
                                    <?= e($attachment['file_name']); ?>
                                </a>
                            </label>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>
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