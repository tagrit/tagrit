<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="tw-max-w-4xl tw-mx-auto">
            <h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700">
                <?= e($title); ?>
            </h4>
            <?= form_open($this->uri->uri_string()); ?>
            <div class="panel_s">
                <div class="panel-body">

                    <div class="form-group select-placeholder">
                        <label
                            for="export_type"><?= _l('bulk_pdf_export_select_type'); ?></label>
                        <select name="export_type" id="export_type" class="selectpicker" data-width="100%"
                            data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>">
                            <option value=""></option>
                            <?php foreach ($bulk_pdf_export_available_features as $feature) { ?>
                            <option
                                value="<?= e($feature['feature']); ?>"
                                <?= $this->input->get('feature') === $feature['feature'] ? 'selected' : ''; ?>>
                                <?= e($feature['name']); ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>
                    <?= render_date_input('date-from', 'zip_from_date'); ?>
                    <?= render_date_input('date-to', 'zip_to_date'); ?>
                    <?= render_input('tag', 'bulk_export_include_tag', '', 'text', ['data-toggle' => 'tooltip', 'title' => 'bulk_export_include_tag_help']); ?>
                    <div class="form-group hide shifter estimates_shifter">
                        <label
                            for="estimate_zip_status"><?= _l('bulk_export_status'); ?></label>
                        <div class="radio radio-primary">
                            <input type="radio" value="all" checked name="estimates_export_status">
                            <label
                                for="all"><?= _l('bulk_export_status_all'); ?></label>
                        </div>
                        <?php foreach ($estimate_statuses as $status) { ?>
                        <div class="radio radio-primary">
                            <input type="radio"
                                id="<?= format_estimate_status($status, '', false); ?>"
                                value="<?= e($status); ?>"
                                name="estimates_export_status">
                            <label
                                for="<?= format_estimate_status($status, '', false); ?>"><?= format_estimate_status($status, '', false); ?></label>
                        </div>
                        <?php } ?>
                    </div>

                    <div class="form-group hide shifter credit_notes_shifter">
                        <label
                            for="credit_notes_export_status"><?= _l('bulk_export_status'); ?></label>
                        <div class="radio radio-primary">
                            <input type="radio" id="all" value="all" checked name="credit_notes_export_status">
                            <label
                                for="all"><?= _l('bulk_export_status_all'); ?></label>
                        </div>
                        <?php foreach ($credit_notes_statuses as $status) { ?>
                        <div class="radio radio-primary">
                            <input type="radio"
                                id="credit_note_<?= e($status['id']); ?>"
                                value="<?= e($status['id']); ?>"
                                name="credit_notes_export_status">
                            <label
                                for="credit_note_<?= e($status['id']); ?>"><?= e($status['name']); ?></label>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="form-group hide shifter invoices_shifter">
                        <label
                            for="invoices_export_status"><?= _l('bulk_export_status'); ?></label>
                        <div class="radio radio-primary">
                            <input type="radio" id="all" value="all" checked name="invoices_export_status">
                            <label
                                for="all"><?= _l('bulk_export_status_all'); ?></label>
                        </div>
                        <?php foreach ($invoice_statuses as $status) { ?>
                        <div class="radio radio-primary">
                            <input type="radio"
                                id="invoice_<?= format_invoice_status($status, '', false); ?>"
                                value="<?= e($status); ?>"
                                name="invoices_export_status">
                            <label
                                for="invoice_<?= format_invoice_status($status, '', false); ?>"><?= format_invoice_status($status, '', false); ?></label>
                        </div>
                        <?php } ?>
                        <hr />
                        <div class="radio radio-primary">
                            <input type="radio" id="invoice_not_send" value="not_send" name="invoices_export_status">
                            <label
                                for="invoice_not_send"><?= _l('not_sent_indicator'); ?></label>
                        </div>
                    </div>
                    <div class="form-group hide shifter proposals_shifter">
                        <label
                            for="proposals_export_status"><?= _l('bulk_export_status'); ?></label>
                        <div class="radio radio-primary">
                            <input type="radio" value="all" checked name="proposals_export_status">
                            <label
                                for="all"><?= _l('bulk_export_status_all'); ?></label>
                        </div>
                        <?php foreach ($proposal_statuses as $status) {
                            if ($status == 0) {
                                continue;
                            } ?>
                        <div class="radio radio-primary">
                            <input type="radio"
                                value="<?= e($status); ?>"
                                name="proposals_export_status"
                                id="proposal_<?= format_proposal_status($status, '', false); ?>">
                            <label
                                for="proposal_<?= format_proposal_status($status, '', false); ?>"><?= format_proposal_status($status, '', false); ?></label>
                        </div>
                        <?php
                        } ?>
                    </div>
                    <div class="form-group hide shifter payments_shifter expenses_shifter">
                        <?php
                                                    array_unshift($payment_modes, ['id' => '', 'name' => _l('bulk_export_status_all')]);
echo render_select('paymentmode', $payment_modes, ['id', 'name'], 'payment_modes');
?>
                    </div>
                    <?php hooks()->do_action('after_bulk_pdf_export_options'); ?>
                </div>
                <div class="panel-footer text-right">
                    <button class="btn btn-primary"
                        type="submit"><?= _l('bulk_pdf_export_button'); ?></button>
                </div>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function() {
        appValidateForm($('form'), {
            export_type: 'required'
        });
        $('#export_type').on('change', function() {
            var val = $(this).val();
            $('.shifter').addClass('hide');
            $('.' + val + '_shifter').removeClass('hide');
        });
    });
</script>
</body>

</html>