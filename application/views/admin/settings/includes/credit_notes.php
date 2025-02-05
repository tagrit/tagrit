<div class="form-group">
    <label class="control-label"
        for="credit_note_prefix"><?= _l('credit_note_number_prefix'); ?></label>
    <input type="text" name="settings[credit_note_prefix]" id="credit_note_prefix" class="form-control"
        value="<?= get_option('credit_note_prefix'); ?>">
</div>
<hr />
<i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
    data-title="<?= _l('settings_sales_next_invoice_number_tooltip'); ?>"></i>
<?= render_input('settings[next_credit_note_number]', 'settings_sales_next_credit_note_number', get_option('next_credit_note_number'), 'number', ['min' => 1]); ?>
<hr />
<div class="form-group">
    <label for="credit_note_number_format"
        class="control-label clearfix"><?= _l('settings_sales_credit_note_number_format'); ?></label>
    <div class="radio radio-primary radio-inline">
        <input type="radio" name="settings[credit_note_number_format]" value="1" id="cn_number_based"
            <?= get_option('credit_note_number_format') == '1' ? 'checked' : '' ?>>
        <label
            for="cn_number_based"><?= _l('settings_sales_invoice_number_format_number_based'); ?></label>
    </div>
    <div class="radio radio-primary radio-inline">
        <input type="radio" name="settings[credit_note_number_format]" value="2" id="cn_year_based"
            <?= get_option('credit_note_number_format') == '2' ? 'checked' : '' ?>>
        <label
            for="cn_year_based"><?= _l('settings_sales_invoice_number_format_year_based'); ?>
            (YYYY/000001)</label>
    </div>
    <div class="radio radio-primary radio-inline">
        <input type="radio" name="settings[credit_note_number_format]" value="3" id="cn_short_year_based"
            <?= get_option('credit_note_number_format') == '3' ? 'checked' : '' ?>>
        <label for="cn_short_year_based">000001-YY</label>
    </div>
    <div class="radio radio-primary radio-inline">
        <input type="radio" name="settings[credit_note_number_format]" value="4" id="cn_year_month_based"
            <?= get_option('credit_note_number_format') == '4' ? 'checked' : '' ?>>
        <label for="cn_year_month_based">000001/MM/YYYY</label>
    </div>
</div>
<hr />
<?php render_yes_no_option('credit_note_number_decrement_on_delete', 'credit_note_number_decrement_on_delete', 'credit_note_number_decrement_on_delete_help'); ?>
<hr />
<?php render_yes_no_option('show_project_on_credit_note', 'show_project_on_credit_note'); ?>
<hr />
<?= render_textarea('settings[predefined_clientnote_credit_note]', 'settings_predefined_clientnote', get_option('predefined_clientnote_credit_note'), ['rows' => 6]); ?>
<?= render_textarea('settings[predefined_terms_credit_note]', 'settings_predefined_predefined_term', get_option('predefined_terms_credit_note'), ['rows' => 6]); ?>