<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="lead-wrapper<?= $openEdit == true ? ' open-edit' : ''; ?>"
    <?= isset($lead) && ($lead->junk == 1 || $lead->lost == 1) ? 'lead-is-just-or-lost' : ''; ?>>

    <?php if (isset($lead)) { ?>
    <div class="tw-flex tw-items-center tw-justify-end tw-space-x-1.5">

        <?php
                       $client                  = false;
        $convert_to_client_tooltip_email_exists = '';
        if (total_rows(db_prefix() . 'contacts', ['email' => $lead->email]) > 0 && total_rows(db_prefix() . 'clients', ['leadid' => $lead->id]) == 0) {
            $convert_to_client_tooltip_email_exists = _l('lead_email_already_exists');
            $text                                   = _l('lead_convert_to_client');
        } elseif (total_rows(db_prefix() . 'clients', ['leadid' => $lead->id])) {
            $client = true;
        } else {
            $text = _l('lead_convert_to_client');
        }
        ?>

        <?php if ($lead_locked == false) { ?>
        <div
            class="lead-edit<?= isset($lead) ? ' hide' : ''; ?>">
            <button type="button" class="btn btn-primary lead-top-btn lead-save-btn"
                onclick="document.getElementById('lead-form-submit').click();">
                <?= _l('submit'); ?>
            </button>
        </div>
        <?php } ?>
        <?php if ($client && (staff_can('view', 'customers') || is_customer_admin(get_client_id_by_lead_id($lead->id)))) { ?>
        <a data-toggle="tooltip" class="btn btn-primary lead-top-btn lead-view" data-placement="top"
            title="<?= _l('lead_converted_edit_client_profile'); ?>"
            href="<?= admin_url('clients/client/' . get_client_id_by_lead_id($lead->id)); ?>">
            <i class="fa-regular fa-user"></i>
        </a>
        <?php } ?>
        <?php if (total_rows(db_prefix() . 'clients', ['leadid' => $lead->id]) == 0) { ?>
        <a href="#" data-toggle="tooltip"
            data-title="<?= e($convert_to_client_tooltip_email_exists); ?>"
            class="btn btn-primary lead-convert-to-customer lead-top-btn lead-view"
            onclick="convert_lead_to_customer(<?= e($lead->id); ?>); return false;">
            <i class="fa-regular fa-user"></i>
            <?= e($text); ?>
        </a>
        <?php } ?>

        <div
            class="<?= $lead_locked == true ? ' hide' : ''; ?>">
            <a href="#" lead-edit data-toggle="tooltip"
                data-title="<?= _l('edit'); ?>"
                class="btn btn-default lead-top-btn">

                <i class="fa-regular fa-pen-to-square"></i>
            </a>
        </div>

        <div class="btn-group" id="lead-more-btn">
            <a href="#" class="btn btn-default dropdown-toggle lead-top-btn" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                <?= _l('more'); ?>
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-left" id="lead-more-dropdown">
                <?php if ($lead->junk == 0) {
                    if ($lead->lost == 0 && (total_rows(db_prefix() . 'clients', ['leadid' => $lead->id]) == 0)) { ?>
                <li>
                    <a href="#"
                        onclick="lead_mark_as_lost(<?= e($lead->id); ?>); return false;">
                        <i class="fa fa-mars"></i>
                        <?= _l('lead_mark_as_lost'); ?>
                    </a>
                </li>
                <?php } elseif ($lead->lost == 1) { ?>
                <li>
                    <a href="#"
                        onclick="lead_unmark_as_lost(<?= e($lead->id); ?>); return false;">
                        <i class="fa fa-smile-o"></i>
                        <?= _l('lead_unmark_as_lost'); ?>
                    </a>
                </li>
                <?php } ?>
                <?php
                } ?>
                <!-- mark as junk -->
                <?php if ($lead->lost == 0) {
                    if ($lead->junk == 0 && (total_rows(db_prefix() . 'clients', ['leadid' => $lead->id]) == 0)) { ?>
                <li>
                    <a href="#"
                        onclick="lead_mark_as_junk(<?= e($lead->id); ?>); return false;">
                        <i class="fa fa fa-times"></i>
                        <?= _l('lead_mark_as_junk'); ?>
                    </a>
                </li>
                <?php } elseif ($lead->junk == 1) { ?>
                <li>
                    <a href="#"
                        onclick="lead_unmark_as_junk(<?= e($lead->id); ?>); return false;">
                        <i class="fa fa-smile-o"></i>
                        <?= _l('lead_unmark_as_junk'); ?>
                    </a>
                </li>
                <?php } ?>
                <?php } ?>
                <?php if ((staff_can('delete', 'leads') && $lead_locked == false) || is_admin()) { ?>
                <li>
                    <a href="<?= admin_url('leads/delete/' . $lead->id); ?>"
                        class="text-danger delete-text _delete" data-toggle="tooltip" title="">
                        <i class="fa-regular fa-trash-can"></i>
                        <?= _l('lead_edit_delete_tooltip'); ?>
                    </a>
                </li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <?php } ?>

    <div class="clearfix no-margin"></div>

    <?php if (isset($lead)) { ?>

    <div class="row mbot15" style="margin-top:12px;">
        <hr class="no-margin" />
    </div>

    <div class="alert alert-warning hide mtop20" role="alert" id="lead_proposal_warning">
        <?= _l('proposal_warning_email_change', [_l('lead_lowercase'), _l('lead_lowercase'), _l('lead_lowercase')]); ?>
        <hr />
        <a href="#"
            onclick="update_all_proposal_emails_linked_to_lead(<?= e($lead->id); ?>); return false;"
            class="alert-link">
            <?= _l('update_proposal_email_yes'); ?>
        </a>
        <br />
        <a href="#"
            onclick="init_lead_modal_data(<?= e($lead->id); ?>); return false;"
            class="alert-link">
            <?= _l('update_proposal_email_no'); ?>
        </a>
    </div>
    <?php } ?>
    <?= form_open((isset($lead) ? admin_url('leads/lead/' . $lead->id) : admin_url('leads/lead')), ['id' => 'lead_form']); ?>
    <div class="row">
        <div class="lead-view<?= ! isset($lead) ? ' hide' : ''; ?>"
            id="leadViewWrapper">
            <div class="col-md-4 col-xs-12 lead-information-col">
                <div class="lead-info-heading">
                    <h4>
                        <?= _l('lead_info'); ?>
                    </h4>
                </div>
                <dl>
                    <dt class="lead-field-heading tw-font-medium tw-text-neutral-500">
                        <?= _l('lead_add_edit_name'); ?>
                    </dt>
                    <dd class="tw-text-neutral-900 tw-mt-1 lead-name">
                        <?= isset($lead) && $lead->name != '' ? e($lead->name) : '-' ?>
                    </dd>
                    <dt class="lead-field-heading tw-font-medium tw-text-neutral-500">
                        <?= _l('lead_title'); ?>
                    </dt>
                    <dd class="tw-text-neutral-900 tw-mt-1">
                        <?= isset($lead) && $lead->title != '' ? e($lead->title) : '-' ?>
                    </dd>
                    <dt class="lead-field-heading tw-font-medium tw-text-neutral-500">
                        <?= _l('lead_add_edit_email'); ?>
                    </dt>
                    <dd class="tw-text-neutral-900 tw-mt-1">
                        <?= isset($lead) && $lead->email != '' ? '<a href="mailto:' . e($lead->email) . '">' . e($lead->email) . '</a>' : '-' ?>
                    </dd>
                    <dt class="lead-field-heading tw-font-medium tw-text-neutral-500">
                        <?= _l('lead_website'); ?>
                    </dt>
                    <dd class="tw-text-neutral-900 tw-mt-1">
                        <?= isset($lead) && $lead->website != '' ? '<a href="' . e(maybe_add_http($lead->website)) . '" target="_blank">' . e($lead->website) . '</a>' : '-' ?>
                    </dd>
                    <dt class="lead-field-heading tw-font-medium tw-text-neutral-500">
                        <?= _l('lead_add_edit_phonenumber'); ?>
                    </dt>
                    <dd class="tw-text-neutral-900 tw-mt-1">
                        <?= isset($lead) && $lead->phonenumber != '' ? '<a href="tel:' . e($lead->phonenumber) . '">' . e($lead->phonenumber) . '</a>' : '-' ?>
                    </dd>
                    <dt class="lead-field-heading tw-font-medium tw-text-neutral-500">
                        <?= _l('lead_value'); ?>
                    </dt>
                    <dd class="tw-text-neutral-900 tw-mt-1">
                        <?= isset($lead) && $lead->lead_value != 0 ? e(app_format_money($lead->lead_value, $base_currency->id)) : '-' ?>
                    </dd>
                    <dt class="lead-field-heading tw-font-medium tw-text-neutral-500">
                        <?= _l('lead_company'); ?>
                    </dt>
                    <dd class="tw-text-neutral-900 tw-mt-1">
                        <?= isset($lead) && $lead->company != '' ? e($lead->company) : '-' ?>
                    </dd>
                    <dt class="lead-field-heading tw-font-medium tw-text-neutral-500">
                        <?= _l('lead_address'); ?>
                    </dt>
                    <dd class="tw-text-neutral-900 tw-mt-1">
                        <?= isset($lead) && $lead->address != '' ? process_text_content_for_display($lead->address) : '-' ?>
                    </dd>
                    <dt class="lead-field-heading tw-font-medium tw-text-neutral-500">
                        <?= _l('lead_city'); ?>
                    </dt>
                    <dd class="tw-text-neutral-900 tw-mt-1">
                        <?= isset($lead) && $lead->city != '' ? e($lead->city) : '-' ?>
                    </dd>
                    <dt class="lead-field-heading tw-font-medium tw-text-neutral-500">
                        <?= _l('lead_state'); ?>
                    </dt>
                    <dd class="tw-text-neutral-900 tw-mt-1">
                        <?= isset($lead) && $lead->state != '' ? e($lead->state) : '-' ?>
                    </dd>
                    <dt class="lead-field-heading tw-font-medium tw-text-neutral-500">
                        <?= _l('lead_country'); ?>
                    </dt>
                    <dd class="tw-text-neutral-900 tw-mt-1">
                        <?= isset($lead) && $lead->country != 0 ? e(get_country($lead->country)->short_name) : '-' ?>
                    </dd>
                    <dt class="lead-field-heading tw-font-medium tw-text-neutral-500">
                        <?= _l('lead_zip'); ?>
                    </dt>
                    <dd class="tw-text-neutral-900 tw-mt-1">
                        <?= isset($lead) && $lead->zip != '' ? e($lead->zip) : '-' ?>
                    </dd>
                </dl>
            </div>
            <div class="col-md-4 col-xs-12 lead-information-col">
                <div class="lead-info-heading">
                    <h4>
                        <?= _l('lead_general_info'); ?>
                    </h4>
                </div>
                <dl>
                    <dt class="lead-field-heading tw-font-medium tw-text-neutral-500 no-mtop">
                        <?= _l('lead_add_edit_status'); ?>
                    </dt>
                    <dd class="tw-text-neutral-900 tw-mt-2 mbot15">
                        <?php if (isset($lead)) {
                            echo $lead->status_name != '' ? ('<span class="lead-status-' . e($lead->status) . ' label' . (empty($lead->color) ? ' label-default' : '') . '" style="color:' . e($lead->color) . ';border:1px solid ' . adjust_hex_brightness($lead->color, 0.4) . ';background: ' . adjust_hex_brightness($lead->color, 0.04) . ';">' . e($lead->status_name) . '</span>') : '-';
                        } else {
                            echo '-';
                        } ?>
                    </dd>
                    <dt class="lead-field-heading tw-font-medium tw-text-neutral-500">
                        <?= _l('lead_add_edit_source'); ?>
                    </dt>
                    <dd class="tw-text-neutral-900 tw-mt-1 mbot15">
                        <?= isset($lead) && $lead->source_name != '' ? e($lead->source_name) : '-' ?>
                    </dd>
                    <?php if (! is_language_disabled()) { ?>
                    <dt class="lead-field-heading tw-font-medium tw-text-neutral-500">
                        <?= _l('localization_default_language'); ?>
                    </dt>
                    <dd class="tw-text-neutral-900 tw-mt-1 mbot15">
                        <?= isset($lead) && $lead->default_language != '' ? e(ucfirst($lead->default_language)) : _l('system_default_string') ?>
                    </dd>
                    <?php } ?>
                    <dt class="lead-field-heading tw-font-medium tw-text-neutral-500">
                        <?= _l('lead_add_edit_assigned'); ?>
                    </dt>
                    <dd class="tw-text-neutral-900 tw-mt-1 mbot15">
                        <?= isset($lead) && $lead->assigned != 0 ? e(get_staff_full_name($lead->assigned)) : '-' ?>
                    </dd>
                    <dt class="lead-field-heading tw-font-medium tw-text-neutral-500">
                        <?= _l('tags'); ?>
                    </dt>
                    <dd class="tw-text-neutral-900 tw-mt-1 mbot10">
                        <?php if (isset($lead)) {
                            $tags = get_tags_in($lead->id, 'lead');
                            if (count($tags) > 0) {
                                echo render_tags($tags);
                                echo '<div class="clearfix"></div>';
                            } else {
                                echo '-';
                            }
                        } ?>
                    </dd>
                    <dt class="lead-field-heading tw-font-medium tw-text-neutral-500">
                        <?= _l('leads_dt_datecreated'); ?>
                    </dt>
                    <dd class="tw-text-neutral-900 tw-mt-1">
                        <?= isset($lead) && $lead->dateadded != '' ? '<span class="text-has-action" data-toggle="tooltip" data-title="' . e(_dt($lead->dateadded)) . '">' . e(time_ago($lead->dateadded)) . '</span>' : '-' ?>
                    </dd>
                    <dt class="lead-field-heading tw-font-medium tw-text-neutral-500">
                        <?= _l('leads_dt_last_contact'); ?>
                    </dt>
                    <dd class="tw-text-neutral-900 tw-mt-1">
                        <?= isset($lead) && $lead->lastcontact != '' ? '<span class="text-has-action" data-toggle="tooltip" data-title="' . e(_dt($lead->lastcontact)) . '">' . e(time_ago($lead->lastcontact)) . '</span>' : '-' ?>
                    </dd>
                    <dt class="lead-field-heading tw-font-medium tw-text-neutral-500">
                        <?= _l('lead_public'); ?>
                    </dt>
                    <dd class="tw-text-neutral-900 tw-mt-1 mbot15">
                        <?php if (isset($lead)) {
                            if ($lead->is_public == 1) {
                                echo _l('lead_is_public_yes');
                            } else {
                                echo _l('lead_is_public_no');
                            }
                        } else {
                            echo '-';
                        } ?>
                    </dd>
                    <?php if (isset($lead) && $lead->from_form_id != 0) { ?>
                    <dt class="lead-field-heading tw-font-medium tw-text-neutral-500">
                        <?= _l('web_to_lead_form'); ?>
                    </dt>
                    <dd class="tw-text-neutral-900 tw-mt-1 mbot15">
                        <?= e($lead->form_data->name); ?>
                    </dd>
                    <?php } ?>
                </dl>
            </div>
            <div class="col-md-4 col-xs-12 lead-information-col">
                <?php if (total_rows(db_prefix() . 'customfields', ['fieldto' => 'leads', 'active' => 1]) > 0 && isset($lead)) { ?>
                <div class="lead-info-heading">
                    <h4>
                        <?= _l('custom_fields'); ?>
                    </h4>
                </div>
                <dl>
                    <?php foreach (get_custom_fields('leads') as $field) { ?>
                    <?php $value = get_custom_field_value($lead->id, $field['id'], 'leads'); ?>
                    <dt class="lead-field-heading tw-font-medium tw-text-neutral-500 no-mtop">
                        <?= e($field['name']); ?>
                    </dt>
                    <dd class="tw-text-neutral-900 tw-mt-1 tw-break-words">
                        <?= $value != '' ? $value : '-' ?>
                    </dd>
                    <?php } ?>
                    <?php } ?>
                </dl>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-12">
                <dl>
                    <dt class="lead-field-heading tw-font-medium tw-text-neutral-500">
                        <?= _l('lead_description'); ?>
                    </dt>
                    <dd class="tw-text-neutral-900 tw-mt-1">
                        <?= process_text_content_for_display((isset($lead) && $lead->description != '' ? $lead->description : '-')); ?>
                    </dd>
                </dl>
            </div>
        </div>
        <div class="clearfix"></div>
        <div
            class="lead-edit<?= isset($lead) ? ' hide' : ''; ?>">
            <div class="col-md-4">
                <?php
        $selected = '';
if (isset($lead)) {
    $selected = $lead->status;
} elseif (isset($status_id)) {
    $selected = $status_id;
}
echo render_leads_status_select($statuses, $selected, 'lead_add_edit_status');
?>
            </div>
            <div class="col-md-4">
                <?= render_leads_source_select($sources, (isset($lead) ? $lead->source : get_option('leads_default_source')), 'lead_add_edit_source'); ?>
            </div>
            <div class="col-md-4">
                <?php
               $assigned_attrs = [];
$selected                      = (isset($lead) ? $lead->assigned : get_staff_user_id());
if (isset($lead)
   && $lead->assigned == get_staff_user_id()
   && $lead->addedfrom != get_staff_user_id()
   && ! is_admin($lead->assigned)
   && staff_cant('view', 'leads')
) {
    $assigned_attrs['disabled'] = true;
}
echo render_select('assigned', $members, ['staffid', ['firstname', 'lastname']], 'lead_add_edit_assigned', $selected, $assigned_attrs); ?>
            </div>
            <div class="clearfix"></div>
            <hr class="mtop5 mbot10" />
            <div class="col-md-12">
                <div class="form-group no-mbot" id="inputTagsWrapper">
                    <label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i>
                        <?= _l('tags'); ?></label>
                    <input type="text" class="tagsinput" id="tags" name="tags"
                        value="<?= isset($lead) ? prep_tags_input(get_tags_in($lead->id, 'lead')) : ''; ?>"
                        data-role="tagsinput">
                </div>
            </div>
            <div class="clearfix"></div>
            <hr class="no-mtop mbot15" />
            <div class="col-md-6">
                <?php $value = (isset($lead) ? $lead->name : ''); ?>
                <?= render_input('name', 'lead_add_edit_name', $value); ?>
                <?php $value = (isset($lead) ? $lead->title : ''); ?>
                <?= render_input('title', 'lead_title', $value); ?>
                <?php $value = (isset($lead) ? $lead->email : ''); ?>
                <?= render_input('email', 'lead_add_edit_email', $value); ?>
                <?php if ((isset($lead) && empty($lead->website)) || ! isset($lead)) {
                    $value = (isset($lead) ? $lead->website : '');
                    echo render_input('website', 'lead_website', $value);
                } else { ?>
                <div class="form-group">
                    <label
                        for="website"><?= _l('lead_website'); ?></label>
                    <div class="input-group">
                        <input type="text" name="website" id="website"
                            value="<?= e($lead->website); ?>"
                            class="form-control">
                        <div class="input-group-addon">
                            <span>
                                <a href="<?= e(maybe_add_http($lead->website)); ?>"
                                    target="_blank" tabindex="-1">
                                    <i class="fa fa-globe"></i>
                                </a>
                            </span>
                        </div>
                    </div>
                </div>
                <?php }
                $value = (isset($lead) ? $lead->phonenumber : ''); ?>
                <?= render_input('phonenumber', 'lead_add_edit_phonenumber', $value); ?>
                <div class="form-group">
                    <label
                        for="lead_value"><?= _l('lead_value'); ?></label>
                    <div class="input-group" data-toggle="tooltip"
                        title="<?= _l('lead_value_tooltip'); ?>">
                        <input type="number" class="form-control" name="lead_value" value="<?php if (isset($lead)) {
                            echo $lead->lead_value;
                        }?>">
                        <div class="input-group-addon">
                            <?= e($base_currency->symbol); ?>
                        </div>
                    </div>
                    </label>
                </div>
                <?php $value = (isset($lead) ? $lead->company : ''); ?>
                <?= render_input('company', 'lead_company', $value); ?>
            </div>
            <div class="col-md-6">
                <?php $value = (isset($lead) ? $lead->address : ''); ?>
                <?= render_textarea('address', 'lead_address', $value, ['rows' => 1, 'style' => 'height:36px;font-size:100%;']); ?>
                <?php $value = (isset($lead) ? $lead->city : ''); ?>
                <?= render_input('city', 'lead_city', $value); ?>
                <?php $value = (isset($lead) ? $lead->state : ''); ?>
                <?= render_input('state', 'lead_state', $value); ?>
                <?php
                           $countries = get_all_countries();
$customer_default_country             = get_option('customer_default_country');
$selected                             = (isset($lead) ? $lead->country : $customer_default_country);
echo render_select('country', $countries, ['country_id', ['short_name']], 'lead_country', $selected, ['data-none-selected-text' => _l('dropdown_non_selected_tex')]);
?>
                <?php $value = (isset($lead) ? $lead->zip : ''); ?>
                <?= render_input('zip', 'lead_zip', $value); ?>
                <?php if (! is_language_disabled()) { ?>
                <div class="form-group">
                    <label for="default_language"
                        class="control-label"><?= _l('localization_default_language'); ?></label>
                    <select name="default_language" data-live-search="true" id="default_language"
                        class="form-control selectpicker"
                        data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>">
                        <option value="">
                            <?= _l('system_default_string'); ?>
                        </option>
                        <?php foreach ($this->app->get_available_languages() as $availableLanguage) {
                            $selected = '';
                            if (isset($lead)) {
                                if ($lead->default_language == $availableLanguage) {
                                    $selected = 'selected';
                                }
                            } ?>
                        <option value="<?= e($availableLanguage); ?>"
                            <?= e($selected); ?>>
                            <?= e(ucfirst($availableLanguage)); ?>
                        </option>
                        <?php
                        } ?>
                    </select>
                </div>
                <?php } ?>
            </div>
            <div class="col-md-12">
                <?php $value = (isset($lead) ? $lead->description : ''); ?>
                <?= render_textarea('description', 'lead_description', $value); ?>
                <div class="row">
                    <div class="col-md-12">
                        <?php if (! isset($lead)) { ?>
                        <div class="lead-select-date-contacted hide">
                            <?= render_datetime_input('custom_contact_date', 'lead_add_edit_datecontacted', '', ['data-date-end-date' => date('Y-m-d')]); ?>
                        </div>
                        <?php } else { ?>
                        <?= render_datetime_input('lastcontact', 'leads_dt_last_contact', _dt($lead->lastcontact), ['data-date-end-date' => date('Y-m-d')]); ?>
                        <?php } ?>
                        <div
                            class="checkbox-inline checkbox<?= isset($lead) ? ' hide' : ''; ?><?= isset($lead) && (is_lead_creator($lead->id) || staff_can('edit', 'leads')) ? ' lead-edit' : ''; ?>">
                            <input type="checkbox" name="is_public"
                                <?= isset($lead) ? 'checked' : ''; ?>
                            id="lead_public">
                            <label for="lead_public">
                                <?= _l('lead_public'); ?>
                            </label>
                        </div>
                        <?php if (! isset($lead)) { ?>
                        <div class="checkbox-inline checkbox checkbox-primary">
                            <input type="checkbox" name="contacted_today" id="contacted_today" checked>
                            <label for="contacted_today">
                                <?= _l('lead_add_edit_contacted_today'); ?>
                            </label>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-md-12 mtop15">
                <?php $rel_id = (isset($lead) ? $lead->id : false); ?>
                <?= render_custom_fields('leads', $rel_id); ?>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <?php if (isset($lead)) { ?>
    <div class="lead-latest-activity tw-mb-3 lead-view">
        <div class="lead-info-heading">
            <h4><?= _l('lead_latest_activity'); ?>
            </h4>
        </div>
        <div id="lead-latest-activity" class="pleft5"></div>
    </div>
    <?php } ?>
    <?php if ($lead_locked == false) { ?>
    <div
        class="lead-edit<?= isset($lead) ? ' hide' : ''; ?>">
        <hr class="-tw-mx-5 tw-border-neutral-200" />
        <button type="submit" class="btn btn-primary pull-right lead-save-btn" id="lead-form-submit">
            <?= _l('submit'); ?>
        </button>
        <button type=" button" class="btn btn-default pull-right mright5" data-dismiss="modal">
            <?= _l('close'); ?>
        </button>
    </div>
    <?php } ?>
    <div class="clearfix"></div>
    <?= form_close(); ?>
</div>
<?php if (isset($lead) && $lead_locked == true) { ?>
<script>
    $(function() {
        // Set all fields to disabled if lead is locked
        $.each($('.lead-wrapper').find('input, select, textarea'), function() {
            $(this).attr('disabled', true);
            if ($(this).is('select')) {
                $(this).selectpicker('refresh');
            }
        });
    });
</script>
<?php } ?>