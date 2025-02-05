<div class="form-group">
    <label class="control-label"
        for="invoice_prefix"><?= _l('settings_sales_invoice_prefix'); ?></label>
    <input type="text" name="settings[invoice_prefix]" class="form-control"
        value="<?= get_option('invoice_prefix'); ?>">
</div>
<hr />
<i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
    data-title="<?= _l('settings_sales_next_invoice_number_tooltip'); ?>"></i>
<?= render_input('settings[next_invoice_number]', 'settings_sales_next_invoice_number', get_option('next_invoice_number'), 'number', ['min' => 1]); ?>
<hr />
<i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
    data-title="<?= _l('invoice_due_after_help'); ?>"></i>
<?= render_input('settings[invoice_due_after]', 'settings_sales_invoice_due_after', get_option('invoice_due_after')); ?>
<hr />
<?= render_yes_no_option('allow_staff_view_invoices_assigned', 'allow_staff_view_invoices_assigned'); ?>
<hr />
<?php render_yes_no_option('view_invoice_only_logged_in', 'settings_sales_require_client_logged_in_to_view_invoice'); ?>
<hr />
<?php render_yes_no_option('delete_only_on_last_invoice', 'settings_delete_only_on_last_invoice'); ?>
<hr />
<?php render_yes_no_option('invoice_number_decrement_on_delete', 'settings_sales_decrement_invoice_number_on_delete', 'settings_sales_decrement_invoice_number_on_delete_tooltip'); ?>
<hr />
<?php render_yes_no_option('exclude_invoice_from_client_area_with_draft_status', 'exclude_invoices_draft_from_client_area'); ?>
<hr />
<?php render_yes_no_option('show_sale_agent_on_invoices', 'settings_show_sale_agent_on_invoices'); ?>
<hr />
<?php render_yes_no_option('show_project_on_invoice', 'show_project_on_invoice'); ?>
<hr />
<?php render_yes_no_option('show_total_paid_on_invoice', 'show_total_paid_on_invoice'); ?>
<hr />
<?php render_yes_no_option('show_credits_applied_on_invoice', 'show_credits_applied_on_invoice'); ?>
<hr />
<?php render_yes_no_option('show_amount_due_on_invoice', 'show_amount_due_on_invoice'); ?>
<hr />
<?php render_yes_no_option('attach_invoice_to_payment_receipt_email', 'attach_invoice_to_payment_receipt_email'); ?>
<hr />
<div class="form-group">
    <label for="invoice_number_format"
        class="control-label clearfix"><?= _l('settings_sales_invoice_number_format'); ?></label>
    <div class="radio radio-primary radio-inline">
        <input type="radio" id="number_based" name="settings[invoice_number_format]" value="1"
            <?= get_option('invoice_number_format') == '1' ? 'checked' : '' ?>>
        <label
            for="number_based"><?= _l('settings_sales_invoice_number_format_number_based'); ?></label>
    </div>
    <div class="radio radio-primary radio-inline">
        <input type="radio" name="settings[invoice_number_format]" value="2" id="year_based"
            <?= get_option('invoice_number_format') == '2' ? 'checked' : '' ?>>
        <label
            for="year_based"><?= _l('settings_sales_invoice_number_format_year_based'); ?>
            (YYYY/000001)</label>
    </div>
    <div class="radio radio-primary radio-inline">
        <input type="radio" name="settings[invoice_number_format]" value="3" id="short_year_based"
            <?= get_option('invoice_number_format') == '3' ? 'checked' : '' ?>>
        <label for="short_year_based">000001-YY</label>
    </div>
    <div class="radio radio-primary radio-inline">
        <input type="radio" name="settings[invoice_number_format]" value="4" id="year_month_based"
            <?= get_option('invoice_number_format') == '4' ? 'checked' : '' ?>>
        <label for="year_month_based">000001/MM/YYYY</label>
    </div>
    <hr />
</div>
<?= render_textarea('settings[predefined_clientnote_invoice]', 'settings_predefined_clientnote', get_option('predefined_clientnote_invoice'), ['rows' => 6]); ?>
<?= render_textarea('settings[predefined_terms_invoice]', 'settings_predefined_predefined_term', get_option('predefined_terms_invoice'), ['rows' => 6]); ?>