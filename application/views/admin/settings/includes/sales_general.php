<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label
                for="decimal_separator"><?= _l('settings_sales_decimal_separator'); ?></label>
            <select id="decimal_separator" class="selectpicker" name="settings[decimal_separator]" data-width="100%">
                <option value="," <?= get_option('decimal_separator') == ',' ? ' selected' : ''; ?>>,
                </option>
                <option value="." <?= get_option('decimal_separator') == '.' ? ' selected' : ''; ?>>.
                </option>
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label
                for="thousand_separator"><?= _l('settings_sales_thousand_separator'); ?></label>
            <select id="thousand_separator" class="selectpicker" name="settings[thousand_separator]" data-width="100%"
                data-show-subtext="true">
                <option value="," <?= get_option('thousand_separator') == ',' ? ' selected' : ''; ?>>,
                </option>
                <option value="." <?= get_option('thousand_separator') == '.' ? ' selected' : ''; ?>>.
                </option>
                <option value="'" data-subtext="apostrophe" <?= get_option('thousand_separator') == "'" ? ' selected' : ''; ?>>'
                </option>
                <option value="" data-subtext="none" <?= get_option('thousand_separator') == '' ? ' selected' : ''; ?>>&nbsp;
                </option>
                <option value=" " data-subtext="space" <?= get_option('thousand_separator') == ' ' ? ' selected' : ''; ?>>&nbsp;
                </option>
            </select>
        </div>
    </div>
</div>
<hr class="no-mtop" />
<i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
    data-title="<?= _l('invoices') . ', ' . _l('estimates') . ', ' . _l('proposals') ?>"></i>
<?= render_input('settings[number_padding_prefixes]', 'settings_number_padding_prefix', get_option('number_padding_prefixes'), 'number', ['required' => true]); ?>
<hr />
<?php render_yes_no_option('automatically_set_logged_in_staff_sales_agent', 'automatically_set_logged_in_staff_sales_agent', 'automatically_set_logged_in_staff_sales_agent_help'); ?>
<hr />
<?php render_yes_no_option('show_tax_per_item', 'settings_show_tax_per_item'); ?>
<hr />
<?php render_yes_no_option('remove_tax_name_from_item_table', 'remove_tax_name_from_item_table', 'remove_tax_name_from_item_table_help'); ?>
<hr />
<?php render_yes_no_option('items_table_amounts_exclude_currency_symbol', 'items_table_amounts_exclude_currency_symbol'); ?>
<hr />
<?php $default_tax = unserialize(get_option('default_tax')); ?>
<div class="form-group">
    <label
        for="default_tax"><?= _l('settings_default_tax'); ?></label>
    <?= $this->misc_model->get_taxes_dropdown_template('settings[default_tax][]', $default_tax); ?>
</div>
<div class="clearfix"></div>
<hr />
<?php render_yes_no_option('remove_decimals_on_zero', 'remove_decimals_on_zero'); ?>
<hr />
<h4 class="bold">
    <?= _l('settings_amount_to_words'); ?>
</h4>
<p class="text-muted">
    <?= _l('settings_amount_to_words_desc') . '/' . mb_strtolower(_l('proposal')); ?>
</p>
<div class="row">
    <div class="col-md-6">
        <?php render_yes_no_option('total_to_words_enabled', 'settings_amount_to_words_enabled'); ?>
    </div>
    <div class="col-md-6">
        <?php render_yes_no_option('total_to_words_lowercase', 'settings_total_to_words_lowercase'); ?>
    </div>
</div>