<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade email-template"
    data-editor-id=".<?= 'tinymce-' . $invoice->id; ?>"
    id="invoice_send_to_client_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <?= form_open('admin/invoices/send_to_email/' . $invoice->id); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <?= _l('invoice_send_to_client_modal_heading'); ?>
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
$contacts = $this->clients_model->get_contacts($invoice->clientid, ['active' => 1, 'invoice_emails' => 1]);

foreach ($contacts as $contact) {
    array_push($selected, $contact['id']);
}
if (count($selected) == 0) {
    echo '<p class="text-danger">' . _l('sending_email_contact_permissions_warning', _l('customer_permission_invoice')) . '</p><hr />';
}
echo render_select('sent_to[]', $contacts, ['id', 'email', 'firstname,lastname'], 'invoice_estimate_sent_to_email', $selected, ['multiple' => true], [], '', '', false);
?>
                        </div>
                        <?= render_input('cc', 'CC'); ?>
                        <hr />
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="attach_pdf" id="attach_pdf" checked>
                            <label
                                for="attach_pdf"><?= _l('invoice_send_to_client_attach_pdf'); ?></label>
                        </div>
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="attach_statement" id="attach_statement">
                            <label
                                for="attach_statement"><?= _l('attach_statement'); ?>
                                <a href="<?= admin_url('clients/client/' . $invoice->clientid . '?group=statement'); ?>"
                                    target="_blank">
                                    <i class="fa-regular fa-file-lines"></i>
                                </a>
                            </label>
                        </div>
                        <div id="statement-period" class="hide">
                            <?php $this->load->view('admin/clients/groups/_statement_period_select'); ?>
                            <input type="hidden" name="statement_from">
                            <input type="hidden" name="statement_to">
                        </div>
                        <hr />
                        <h5 class="bold">
                            <?= _l('invoice_send_to_client_preview_template'); ?>
                        </h5>
                        <hr />
                        <?= render_textarea('email_template_custom', '', $template->message, [], [], '', 'tinymce-' . $invoice->id); ?>
                        <?= form_hidden('template_name', $template_name); ?>
                    </div>
                </div>
                <?php if (count($invoice->attachments) > 0) { ?>
                <hr />
                <div class="row">
                    <div class="col-md-12">
                        <h5 class="bold no-margin">
                            <?= _l('include_attachments_to_email'); ?>
                        </h5>
                        <hr />
                        <?php foreach ($invoice->attachments as $attachment) {
                            ?>
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox"
                                <?= ! empty($attachment['external']) ? 'disabled' : ''; ?>
                            value="<?= e($attachment['id']); ?>"
                            name="email_attachments[]">
                            <label for="">
                                <a
                                    href="<?= site_url('download/file/sales_attachment/' . $attachment['attachment_key']); ?>">
                                    <?= e($attachment['file_name']); ?>
                                </a>
                            </label>
                        </div>
                        <?php
                        } ?>
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

<script>
    $(function() {

        $('#attach_statement').on('change', function() {
            if ($(this).prop('checked') === false) {
                $('#statement-period').addClass('hide');
            } else {
                $('#statement-period').removeClass('hide');
            }
        })

        $('#invoice_send_to_client_modal form').submit(function() {

            if ($('#attach_statement').prop('checked') === false) {
                return true;
            }

            var $statementPeriod = $('#range');
            var value = $statementPeriod.selectpicker('val');
            var period = new Array();
            if (value != 'period') {
                period = JSON.parse(value);
            } else {
                period[0] = $('input[name="period-from"]').val();
                period[1] = $('input[name="period-to"]').val();
            }

            $(this).find('input[name="statement_from"]').val(period[0]);
            $(this).find('input[name="statement_to"]').val(period[1]);

            return true;
        })
    })
</script>