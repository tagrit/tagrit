<?= render_input('settings[proposal_number_prefix]', 'proposal_number_prefix', get_option('proposal_number_prefix')); ?>
<hr />
<i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip"
    data-title="<?= _l('invoice_due_after_help'); ?>"></i>
<?= render_input('settings[proposal_due_after]', 'proposal_due_after', get_option('proposal_due_after'), 'number'); ?>
<hr />
<div class="row">
    <div class="col-md-12">
        <?= render_input('settings[proposals_pipeline_limit]', 'pipeline_limit_status', get_option('proposals_pipeline_limit')); ?>
        <hr />
    </div>
    <div class="col-md-7">
        <label for="default_proposals_pipeline_sort"
            class="control-label"><?= _l('default_pipeline_sort'); ?></label>
        <select name="settings[default_proposals_pipeline_sort]" id="default_proposals_pipeline_sort"
            class="selectpicker" data-width="100%"
            data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>">
            <option value="datecreated" <?= get_option('default_proposals_pipeline_sort') == 'datecreated' ? 'selected' : '' ?>>
                <?= _l('proposals_sort_datecreated'); ?>
            </option>
            <option value="date" <?= get_option('default_proposals_pipeline_sort') == 'date' ? 'selected' : '' ?>>
                <?= _l('proposals_sort_proposal_date'); ?>
            </option>
            <option value="pipeline_order" <?= get_option('default_proposals_pipeline_sort') == 'pipeline_order' ? 'selected' : '' ?>>
                <?= _l('proposals_sort_pipeline'); ?>
            </option>
            <option value="open_till" <?= get_option('default_proposals_pipeline_sort') == 'open_till' ? 'selected' : '' ?>>
                <?= _l('proposals_sort_open_till'); ?>
            </option>

        </select>
    </div>
    <div class="col-md-5">
        <div class="mtop30 text-right">
            <div class="radio radio-inline radio-primary">
                <input type="radio" id="k_desc_proposal" name="settings[default_proposals_pipeline_sort_type]"
                    value="asc"
                    <?= get_option('default_proposals_pipeline_sort_type') == 'asc' ? 'checked' : '' ?>>
                <label
                    for="k_desc_proposal"><?= _l('order_ascending'); ?></label>
            </div>
            <div class="radio radio-inline radio-primary">
                <input type="radio" id="k_asc_proposal" name="settings[default_proposals_pipeline_sort_type]"
                    value="desc"
                    <?= get_option('default_proposals_pipeline_sort_type') == 'desc' ? 'checked' : '' ?>>
                <label
                    for="k_asc_proposal"><?= _l('order_descending'); ?></label>
            </div>

        </div>
    </div>
    <div class="clearfix"></div>
</div>
<hr />
<?php render_yes_no_option('show_project_on_proposal', 'show_project_on_proposal'); ?>
<hr />
<?php render_yes_no_option('exclude_proposal_from_client_area_with_draft_status', 'exclude_proposal_from_client_area_with_draft_status'); ?>
<hr />
<?php render_yes_no_option('proposal_auto_convert_to_invoice_on_client_accept', 'proposal_auto_convert_to_invoice_on_client_accept'); ?>
<hr />
<?php render_yes_no_option('allow_staff_view_proposals_assigned', 'allow_staff_view_proposals_assigned'); ?>
<hr />
<?= render_textarea('settings[proposal_info_format]', 'proposal_info_format', clear_textarea_breaks(get_option('proposal_info_format')), ['rows' => 8, 'style' => 'line-height:20px;']); ?>
<p>
    <a href="#" class="settings-textarea-merge-field" data-to="proposal_info_format">{proposal_to}</a>,
    <a href="#" class="settings-textarea-merge-field" data-to="proposal_info_format">{address}</a>,
    <a href="#" class="settings-textarea-merge-field" data-to="proposal_info_format">{city}</a>,
    <a href="#" class="settings-textarea-merge-field" data-to="proposal_info_format">{state}</a>,
    <a href="#" class="settings-textarea-merge-field" data-to="proposal_info_format">{zip_code}</a>,
    <a href="#" class="settings-textarea-merge-field" data-to="proposal_info_format">{country_code}</a>,
    <a href="#" class="settings-textarea-merge-field" data-to="proposal_info_format">{country_name}</a>,
    <a href="#" class="settings-textarea-merge-field" data-to="proposal_info_format">{phone}</a>,
    <a href="#" class="settings-textarea-merge-field" data-to="proposal_info_format">{email}</a>
</p>
<?php $custom_fields = get_custom_fields('proposal');
if (count($custom_fields) > 0) {
    echo '<hr />';
    echo '<p class="no-mbot font-medium"><b>' . _l('custom_fields') . '</b></p>';
    if (total_rows(db_prefix() . 'customfields', ['fieldto' => 'proposal', 'show_on_client_portal' => 1]) == 0) {
        echo '<p>' . _l('custom_field_pdf_html_help') . '</p>';
        echo '<hr />';
    }
    echo '<ul class="list-group">';

    foreach ($custom_fields as $field) {
        echo '<li class="list-group-item"><b>' . $field['name'] . '</b>: <a href="#" class="settings-textarea-merge-field" data-to="proposal_info_format">{cf_' . $field['id'] . '}</a></li>';
    }
    echo '</ul>';
    echo '<hr />';
}
?>