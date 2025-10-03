<div class="form-group">
    <label class="control-label"
        for="estimate_prefix"><?= _l('settings_sales_estimate_prefix'); ?></label>
    <input type="text" name="settings[estimate_prefix]" class="form-control"
        value="<?= get_option('estimate_prefix'); ?>">
</div>
<hr />
<i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
    data-title="<?= _l('settings_sales_next_estimate_number_tooltip'); ?>"></i>
<?= render_input('settings[next_estimate_number]', 'settings_sales_next_estimate_number', get_option('next_estimate_number'), 'number', ['min' => 1]); ?>
<hr />

<i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
    data-title="<?= _l('invoice_due_after_help'); ?>"></i>
<?= render_input('settings[estimate_due_after]', 'estimate_due_after', get_option('estimate_due_after')); ?>
<hr />
<?php render_yes_no_option('delete_only_on_last_estimate', 'settings_delete_only_on_last_estimate'); ?>
<hr />
<?php render_yes_no_option('estimate_number_decrement_on_delete', 'settings_sales_decrement_estimate_number_on_delete', 'settings_sales_decrement_estimate_number_on_delete_tooltip'); ?>
<hr />
<?= render_yes_no_option('allow_staff_view_estimates_assigned', 'allow_staff_view_estimates_assigned'); ?>
<hr />

<?php render_yes_no_option('view_estimate_only_logged_in', 'settings_sales_require_client_logged_in_to_view_estimate'); ?>
<hr />
<?php render_yes_no_option('show_sale_agent_on_estimates', 'settings_show_sale_agent_on_estimates'); ?>
<hr />
<?php render_yes_no_option('show_project_on_estimate', 'show_project_on_estimate'); ?>
<hr />
<?php render_yes_no_option('estimate_auto_convert_to_invoice_on_client_accept', 'settings_estimate_auto_convert_to_invoice_on_client_accept'); ?>
<hr />
<?php render_yes_no_option('exclude_estimate_from_client_area_with_draft_status', 'settings_exclude_estimate_from_client_area_with_draft_status'); ?>
<hr />
<div class="form-group">
    <label for="estimate_number_format"
        class="control-label clearfix"><?= _l('settings_sales_estimate_number_format'); ?></label>
    <div class="radio radio-primary radio-inline">
        <input type="radio" name="settings[estimate_number_format]" value="1" id="e_number_based"
            <?= get_option('estimate_number_format') == '1' ? 'checked' : '' ?>>
        <label
            for="e_number_based"><?= _l('settings_sales_estimate_number_format_number_based'); ?></label>
    </div>
    <div class="radio radio-primary radio-inline">
        <input type="radio" name="settings[estimate_number_format]" value="2" id="e_year_based"
            <?= get_option('estimate_number_format') == '2' ? 'checked' : '' ?>>
        <label
            for="e_year_based"><?= _l('settings_sales_estimate_number_format_year_based'); ?>
            (YYYY/000001)</label>
    </div>
    <div class="radio radio-primary radio-inline">
        <input type="radio" name="settings[estimate_number_format]" value="3" id="e_short_year_based"
            <?= get_option('estimate_number_format') == '3' ? 'checked' : '' ?>>
        <label for="e_short_year_based">000001-YY</label>
    </div>
    <div class="radio radio-primary radio-inline">
        <input type="radio" name="settings[estimate_number_format]" value="4" id="e_year_month_based"
            <?= get_option('estimate_number_format') == '4' ? 'checked' : '' ?>>
        <label for="e_year_month_based">000001/MM/YYYY</label>
    </div>
    <hr />
</div>
<div class="row">
    <div class="col-md-12">
        <?= render_input('settings[estimates_pipeline_limit]', 'pipeline_limit_status', get_option('estimates_pipeline_limit')); ?>
    </div>
    <div class="col-md-7">
        <label for="default_proposals_pipeline_sort"
            class="control-label"><?= _l('default_pipeline_sort'); ?></label>
        <select name="settings[default_estimates_pipeline_sort]" id="default_estimates_pipeline_sort"
            class="selectpicker" data-width="100%"
            data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>">
            <option value="datecreated" <?= get_option('default_estimates_pipeline_sort') == 'datecreated' ? 'selected' : '' ?>>
                <?= _l('estimates_sort_datecreated'); ?>
            </option>
            <option value="date" <?= get_option('default_estimates_pipeline_sort') == 'date' ? 'selected' : '' ?>>
                <?= _l('estimates_sort_estimate_date'); ?>
            </option>
            <option value="pipeline_order" <?= get_option('default_estimates_pipeline_sort') == 'pipeline_order' ? 'selected' : '' ?>>
                <?= _l('estimates_sort_pipeline'); ?>
            </option>
            <option value="expirydate" <?= get_option('default_estimates_pipeline_sort') == 'expirydate' ? 'selected' : '' ?>>
                <?= _l('estimates_sort_expiry_date'); ?>
            </option>
        </select>
    </div>
    <div class="col-md-5">
        <div class="mtop30 text-right">
            <div class="radio radio-inline radio-primary">
                <input type="radio" id="k_desc_estimate" name="settings[default_estimates_pipeline_sort_type]"
                    value="asc"
                    <?= get_option('default_estimates_pipeline_sort_type') == 'asc' ? 'checked' : '' ?>>
                <label
                    for="k_desc_estimate"><?= _l('order_ascending'); ?></label>
            </div>
            <div class="radio radio-inline radio-primary">
                <input type="radio" id="k_asc_estimate" name="settings[default_estimates_pipeline_sort_type]"
                    value="desc"
                    <?= get_option('default_estimates_pipeline_sort_type') == 'desc' ? 'checked' : '' ?>>
                <label
                    for="k_asc_estimate"><?= _l('order_descending'); ?></label>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</div>
<hr />
<?= render_textarea('settings[predefined_clientnote_estimate]', 'settings_predefined_clientnote', get_option('predefined_clientnote_estimate'), ['rows' => 6]); ?>
<?= render_textarea('settings[predefined_terms_estimate]', 'settings_predefined_predefined_term', get_option('predefined_terms_estimate'), ['rows' => 6]); ?>