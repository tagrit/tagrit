<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="tw-max-w-5xl tw-mx-auto">
            <?php if (isset($member)) { ?>
            <?php $this->load->view('admin/staff/stats'); ?>
            <div class="member">
                <?= form_hidden('isedit'); ?>
                <?= form_hidden('memberid', $member->staffid); ?>
            </div>
            <?php } ?>
            <div class="clearfix"></div>

            <?php if (isset($member) && total_rows(db_prefix() . 'departments', ['email' => $member->email]) > 0) { ?>
            <div class="alert alert-danger tw-mt-1">
                The staff member email exists also as support department email, according to the docs, the
                support
                department email must be unique email in the system, you must change the staff email or the
                support
                department email in order all the features to work properly.
            </div>
            <?php } ?>

            <div class="clearfix"></div>

            <h4
                class="tw-font-bold tw-text-lg<?= isset($member) ? ' tw-mt-4' : ''; ?>">
                <?php if (isset($member)) { ?>
                <?= e($member->firstname . ' ' . $member->lastname); ?>
                <?php if ($member->last_activity && $member->staffid != get_staff_user_id()) { ?>
                <small> -
                    <?= _l('last_active'); ?>:
                    <span class="text-has-action" data-toggle="tooltip"
                        data-title="<?= e(_dt($member->last_activity)); ?>">
                        <?= e(time_ago($member->last_activity)); ?>
                    </span>
                </small>
                <?php } ?>
                <?php } else { ?>
                <?= e($title); ?>
                <?php } ?>
            </h4>


            <?php if (isset($member)) { ?>
            <div class="horizontal-scrollable-tabs">
                <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                <div class="horizontal-tabs">
                    <ul class="nav nav-tabs nav-tabs-horizontal nav-tabs-segmented tw-mb-3" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#tab_staff_member" aria-controls="tab_staff_member" role="tab" data-toggle="tab">
                                <?= _l('staff'); ?>
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#staff_notes" aria-controls="staff_notes" role="tab" data-toggle="tab">
                                <?= _l('staff_add_edit_notes'); ?>
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#staff_timesheets" aria-controls="staff_timesheets" role="tab" data-toggle="tab">
                                <?= _l('task_timesheets'); ?>
                                &
                                <?= _l('als_reports'); ?>
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#staff_projects" aria-controls="staff_projects" role="tab" data-toggle="tab">
                                <?= _l('projects'); ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <?php } ?>

            <div class="panel_s">
                <div class="panel-body">
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="tab_staff_member">
                            <?= form_open_multipart($this->uri->uri_string(), ['class' => 'staff-form', 'autocomplete' => 'off']); ?>

                            <div class="panel-full-width-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal tw-mb-6 !tw-bg-white" role="tablist">
                                    <li role="presentation" class="active -tw-ml-1">
                                        <a href="#tab_profile" aria-controls="tab_profile" role="tab" data-toggle="tab"
                                            class="!tw-bg-white">
                                            <?= _l('staff_profile_string'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#staff_permissions" aria-controls="staff_permissions" role="tab"
                                            data-toggle="tab" class="!tw-bg-white">
                                            <?= _l('staff_add_edit_permissions'); ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="tab_profile">
                                    <?php if (is_admin()) { ?>
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" name="administrator" id="administrator"
                                            <?= isset($member) && ($member->staffid == get_staff_user_id() || is_admin($member->staffid)) ? 'checked' : ''; ?>>
                                        <label
                                            for="administrator"><?= _l('staff_add_edit_administrator'); ?></label>
                                    </div>
                                    <?php } ?>
                                    <div
                                        class="is-not-staff<?= isset($member) && $member->admin == 1 ? ' hide' : ''; ?>">
                                        <div class="checkbox checkbox-primary">
                                            <?php $checked = isset($member) && $member->is_not_staff == 1 ? ' checked' : ''; ?>
                                            <input type="checkbox" value="1" name="is_not_staff" id="is_not_staff"
                                                <?= e($checked); ?>>
                                            <label for="is_not_staff">
                                                <?= _l('is_not_staff_member'); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <hr />
                                    <?php if ((isset($member) && $member->profile_image == null) || ! isset($member)) { ?>
                                    <div class="form-group">
                                        <label for="profile_image"
                                            class="profile-image"><?= _l('staff_edit_profile_image'); ?></label>
                                        <input type="file" name="profile_image" class="form-control" id="profile_image">
                                    </div>
                                    <?php } ?>
                                    <?php if (isset($member) && $member->profile_image != null) { ?>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-9">
                                                <?= staff_profile_image($member->staffid, ['img', 'img-responsive', 'staff-profile-image-thumb'], 'thumb'); ?>
                                            </div>
                                            <div class="col-md-3 text-right">
                                                <a
                                                    href="<?= admin_url('staff/remove_staff_profile_image/' . $member->staffid); ?>"><i
                                                        class="fa fa-remove"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php } ?>
                                    <?php $value = (isset($member) ? $member->firstname : ''); ?>
                                    <?php $attrs = (isset($member) ? [] : ['autofocus' => true]); ?>
                                    <?= render_input('firstname', 'staff_add_edit_firstname', $value, 'text', $attrs); ?>
                                    <?php $value = (isset($member) ? $member->lastname : ''); ?>
                                    <?= render_input('lastname', 'staff_add_edit_lastname', $value); ?>
                                    <?php $value = (isset($member) ? $member->email : ''); ?>
                                    <?= render_input('email', 'staff_add_edit_email', $value, 'email', ['autocomplete' => 'off']); ?>
                                    <div class="form-group">
                                        <label
                                            for="hourly_rate"><?= _l('staff_hourly_rate'); ?></label>
                                        <div class="input-group">
                                            <input type="number" name="hourly_rate"
                                                value="<?= isset($member) ? $member->hourly_rate : 0; ?>"
                                                id="hourly_rate" class="form-control">
                                            <span class="input-group-addon">
                                                <?= e($base_currency->symbol); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <?php $value = (isset($member) ? $member->phonenumber : ''); ?>
                                    <?= render_input('phonenumber', 'staff_add_edit_phonenumber', $value); ?>
                                    <div class="form-group">
                                        <label for="facebook" class="control-label"><i
                                                class="fa-brands fa-facebook-f"></i>
                                            <?= _l('staff_add_edit_facebook'); ?></label>
                                        <input type="text" class="form-control" name="facebook" value="<?php if (isset($member)) {
                                            echo e($member->facebook);
                                        } ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="linkedin" class="control-label"><i
                                                class="fa-brands fa-linkedin-in"></i>
                                            <?= _l('staff_add_edit_linkedin'); ?></label>
                                        <input type="text" class="form-control" name="linkedin" value="<?php if (isset($member)) {
                                            echo e($member->linkedin);
                                        } ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="skype" class="control-label"><i class="fa-brands fa-skype"></i>
                                            <?= _l('staff_add_edit_skype'); ?></label>
                                        <input type="text" class="form-control" name="skype" value="<?php if (isset($member)) {
                                            echo e($member->skype);
                                        } ?>">
                                    </div>
                                    <?php if (! is_language_disabled()) { ?>
                                    <div class="form-group select-placeholder">
                                        <label for="default_language"
                                            class="control-label"><?= _l('localization_default_language'); ?></label>
                                        <select name="default_language" data-live-search="true" id="default_language"
                                            class="form-control selectpicker"
                                            data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>">
                                            <option value="">
                                                <?= _l('system_default_string'); ?>
                                            </option>
                                            <?php foreach ($this->app->get_available_languages() as $availableLanguage) { ?>
                                            <option
                                                value="<?= e($availableLanguage); ?>"
                                                <?= isset($member) && $member->default_language == $availableLanguage ? 'selected' : ''; ?>>
                                                <?= e(ucfirst($availableLanguage)); ?>
                                            </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <?php } ?>
                                    <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1"
                                        data-toggle="tooltip"
                                        data-title="<?= _l('staff_email_signature_help'); ?>"></i>
                                    <?php $value = (isset($member) ? $member->email_signature : ''); ?>
                                    <?= render_textarea('email_signature', 'settings_email_signature', $value, ['data-entities-encode' => 'true']); ?>
                                    <div class="form-group select-placeholder">
                                        <label
                                            for="direction"><?= _l('document_direction'); ?></label>
                                        <select class="selectpicker"
                                            data-none-selected-text="<?= _l('system_default_string'); ?>"
                                            data-width="100%" name="direction" id="direction">
                                            <option value="" <?= isset($member) && empty($member->direction) ? 'selected' : ''; ?>>
                                            </option>
                                            <option value="ltr" <?= isset($member) && $member->direction == 'ltr' ? 'selected' : ''; ?>>LTR
                                            </option>
                                            <option value="rtl" <?= isset($member) && $member->direction == 'rtl' ? 'selected' : ''; ?>>RTL
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <?php if (count($departments) > 0) { ?>
                                        <label
                                            for="departments"><?= _l('staff_add_edit_departments'); ?></label>
                                        <?php } ?>
                                        <?php foreach ($departments as $department) { ?>
                                        <div class="checkbox checkbox-primary">
                                            <input type="checkbox"
                                                id="dep_<?= e($department['departmentid']); ?>"
                                                name="departments[]"
                                                value="<?= e($department['departmentid']); ?>"
                                                <?= isset($member) && in_array($department['departmentid'], array_column($staff_departments, 'departmentid')) ? 'checked' : ''; ?>>
                                            <label
                                                for="dep_<?= e($department['departmentid']); ?>"><?= e($department['name']); ?></label>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <?php $rel_id = (isset($member) ? $member->staffid : false); ?>
                                    <?= render_custom_fields('staff', $rel_id); ?>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <?php if (! isset($member) && is_email_template_active('new-staff-created')) { ?>
                                            <div class="checkbox checkbox-primary">
                                                <input type="checkbox" name="send_welcome_email" id="send_welcome_email"
                                                    checked>
                                                <label
                                                    for="send_welcome_email"><?= _l('staff_send_welcome_email'); ?></label>
                                            </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <?php if (! isset($member) || is_admin() || ! is_admin() && $member->admin == 0) { ?>
                                    <!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
                                    <input type="text" class="fake-autofill-field" name="fakeusernameremembered"
                                        value='' tabindex="-1" />
                                    <input type="password" class="fake-autofill-field" name="fakepasswordremembered"
                                        value='' tabindex="-1" />
                                    <div class="clearfix form-group"></div>
                                    <label for="password"
                                        class="control-label"><?= _l('staff_add_edit_password'); ?></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control password" name="password"
                                            autocomplete="off">
                                        <span class="input-group-addon tw-border-l-0">
                                            <a href="#password" class="show_password"
                                                onclick="showPassword('password'); return false;"><i
                                                    class="fa fa-eye"></i></a>
                                        </span>
                                        <span class="input-group-addon">
                                            <a href="#" class="generate_password"
                                                onclick="generatePassword(this);return false;"><i
                                                    class="fa fa-refresh"></i></a>
                                        </span>
                                    </div>
                                    <?php if (isset($member)) { ?>
                                    <p class="text-muted tw-mt-2">
                                        <?= _l('staff_add_edit_password_note'); ?>
                                    </p>
                                    <?php if ($member->last_password_change != null) { ?>
                                    <?= _l('staff_add_edit_password_last_changed'); ?>:
                                    <span class="text-has-action" data-toggle="tooltip"
                                        data-title="<?= e(_dt($member->last_password_change)); ?>">
                                        <?= e(time_ago($member->last_password_change)); ?>
                                    </span>
                                    <?php }
                                    } ?>
                                    <?php } ?>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="staff_permissions">
                                    <?php
                        hooks()->do_action('staff_render_permissions');
$selected = '';

foreach ($roles as $role) {
    if (isset($member) && $member->role == $role['roleid']) {
        $selected = $role['roleid'];
        break;
    }
    if (! isset($member) && get_option('default_staff_role') == $role['roleid']) {
        $selected = $role['roleid'];
    }
}
?>
                                    <?= render_select('role', $roles, ['roleid', 'name'], 'staff_add_edit_role', $selected); ?>
                                    <hr />
                                    <h4 class="tw-mb-4 tw-text-lg tw-font-bold">
                                        <?= _l('staff_add_edit_permissions'); ?>
                                    </h4>
                                    <?php $this->load->view('admin/staff/permissions', [
                                        'funcData' => ['staff_id' => $member->staffid ?? null],
                                        'member'   => $member ?? null,
                                    ]); ?>

                                </div>
                            </div>
                            <div class="text-right tw-mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <?= _l('submit'); ?>
                                </button>
                            </div>
                            <?= form_close(); ?>
                        </div>
                        <?php if (isset($member)) { ?>
                        <div role="tabpanel" class="tab-pane" id="staff_notes">
                            <div class="tw-text-right">
                                <a href="#" class="btn btn-primary"
                                    onclick="slideToggle('.usernote'); return false;"><?= _l('new_note'); ?></a>
                            </div>
                            <div class="mbot15 usernote hide inline-block full-width">
                                <?= form_open(admin_url('misc/add_note/' . $member->staffid . '/staff')); ?>
                                <?= render_textarea('description', 'staff_add_edit_note_description', '', ['rows' => 5]); ?>
                                <button
                                    class="btn btn-primary pull-right mbot15"><?= _l('submit'); ?></button>
                                <?= form_close(); ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="mtop15">
                                <table class="table dt-table" data-order-col="2" data-order-type="desc">
                                    <thead>
                                        <tr>
                                            <th width="50%">
                                                <?= _l('staff_notes_table_description_heading'); ?>
                                            </th>
                                            <th><?= _l('staff_notes_table_addedfrom_heading'); ?>
                                            </th>
                                            <th><?= _l('staff_notes_table_dateadded_heading'); ?>
                                            </th>
                                            <th><?= _l('options'); ?>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($user_notes as $note) { ?>
                                        <tr>
                                            <td width="50%">
                                                <div
                                                    data-note-description="<?= e($note['id']); ?>">
                                                    <?= process_text_content_for_display($note['description']); ?>
                                                </div>
                                                <div data-note-edit-textarea="<?= e($note['id']); ?>"
                                                    class="hide inline-block full-width">
                                                    <textarea name="description" class="form-control"
                                                        rows="4"><?= clear_textarea_breaks($note['description']); ?></textarea>
                                                    <div class="text-right mtop15">
                                                        <button type="button" class="btn btn-default"
                                                            onclick="toggle_edit_note(<?= e($note['id']); ?>);return false;"><?= _l('cancel'); ?></button>
                                                        <button type="button" class="btn btn-primary"
                                                            onclick="edit_note(<?= e($note['id']); ?>);"><?= _l('update_note'); ?></button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= e($note['firstname'] . ' ' . $note['lastname']); ?>
                                            </td>
                                            <td
                                                data-order="<?= e($note['dateadded']); ?>">
                                                <?= e(_dt($note['dateadded'])); ?>
                                            </td>
                                            <td>
                                                <div class="tw-flex tw-items-center tw-space-x-2">
                                                    <?php if ($note['addedfrom'] == get_staff_user_id() || staff_can('delete', 'staff')) { ?>
                                                    <a href="#"
                                                        onclick="toggle_edit_note(<?= e($note['id']); ?>);return false;"
                                                        class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700">
                                                        <i class="fa-regular fa-pen-to-square fa-lg"></i>
                                                    </a>
                                                    <a href="<?= admin_url('misc/delete_note/' . $note['id']); ?>"
                                                        class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 _delete">
                                                        <i class="fa-regular fa-trash-can fa-lg"></i>
                                                    </a>
                                                    <?php } ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="staff_timesheets">
                            <?= form_open($this->uri->uri_string(), ['method' => 'GET', 'class' => 'tw-w-1/2']); ?>
                            <?= form_hidden('filter', 'true'); ?>
                            <div class="tw-flex tw-items-center tw-gap-2 tw-mb-4">
                                <div class="select-placeholder tw-grow">
                                    <select name="range" id="range" class="selectpicker" data-width="100%">
                                        <option value="this_month" <?= ! $this->input->get('range') || $this->input->get('range') == 'this_month' ? 'selected' : ''; ?>>
                                            <?= _l('staff_stats_this_month_total_logged_time'); ?>
                                        </option>
                                        <option value="last_month" <?= $this->input->get('range') == 'last_month' ? 'selected' : ''; ?>>
                                            <?= _l('staff_stats_last_month_total_logged_time'); ?>
                                        </option>
                                        <option value="this_week" <?= $this->input->get('range') == 'this_week' ? 'selected' : ''; ?>>
                                            <?= _l('staff_stats_this_week_total_logged_time'); ?>
                                        </option>
                                        <option value="last_week" <?= $this->input->get('range') == 'last_week' ? 'selected' : ''; ?>>
                                            <?= _l('staff_stats_last_week_total_logged_time'); ?>
                                        </option>
                                        <option value="period" <?= $this->input->get('range') == 'period' ? 'selected' : ''; ?>>
                                            <?= _l('period_datepicker'); ?>
                                        </option>
                                    </select>
                                </div>
                                <button type="submit"
                                    class="btn btn-primary apply-timesheets-filters"><?= _l('apply'); ?></button>
                            </div>
                            <div class="tw-w-[416px] -tw-space-y-2">
                                <div
                                    class="period <?= $this->input->get('range') != 'period' ? 'hide' : ''; ?>">
                                    <?= render_date_input('period-from', '', $this->input->get('period-from')); ?>
                                </div>
                                <div
                                    class="period <?= $this->input->get('range') != 'period' ? 'hide' : ''; ?>">
                                    <?= render_date_input('period-to', '', $this->input->get('period-to')); ?>
                                </div>
                            </div>
                            <?= form_close(); ?>
                            <hr class="hr-panel-separator" />
                            <table class="table dt-table">
                                <thead>
                                    <th><?= _l('task'); ?>
                                    </th>
                                    <th><?= _l('timesheet_start_time'); ?>
                                    </th>
                                    <th><?= _l('timesheet_end_time'); ?>
                                    </th>
                                    <th><?= _l('task_relation'); ?>
                                    </th>
                                    <th><?= _l('staff_hourly_rate'); ?>
                                        (<?= _l('als_staff'); ?>)
                                    </th>
                                    <th><?= _l('time_h'); ?>
                                    </th>
                                    <th><?= _l('time_decimal'); ?>
                                    </th>
                                    <th data-sortable="false"></th>
                                </thead>
                                <tbody>
                                    <?php
                                              $total_logged_time = [];

                            foreach ($timesheets as $t) { ?>
                                    <tr>
                                        <td><a href="#"
                                                onclick="init_task_modal(<?= e($t['task_id']); ?>); return false;">
                                                <?= e($t['name']); ?>
                                            </a>
                                        </td>
                                        <td
                                            data-order="<?= e($t['start_time']); ?>">
                                            <?= e(_dt($t['start_time'], true)); ?>
                                        </td>
                                        <td
                                            data-order="<?= e($t['end_time']); ?>">
                                            <?php if ($t['not_finished'] && (is_admin() || $t['staff_id'] === get_staff_user_id())) { ?>
                                            <a href="#" <?php
                                           // Do not show the note popover when there is no associated task
                                           // The user will be able to add note and select task in the popup window that will open
                                           if ($t['task_id'] != 0) { ?>
                                                data-toggle="popover" data-placement="bottom"
                                                data-html="true" data-trigger="manual"
                                                data-title="<?= _l('note'); ?>"
                                                data-content='<?= render_textarea('timesheet_note'); ?><button
                                                    type="button"
                                                    onclick="timer_action(this, <?= e($t['task_id']); ?>, <?= e($t['id']); ?>, 1);"
                                                    class="btn btn-primary btn-sm"><?= _l('save'); ?></button>'
                                                onclick="return false;"
                                                <?php } else { ?>
                                                onclick="timer_action(this,
                                                <?= e($t['task_id']); ?>,
                                                <?= e($t['id']); ?>,
                                                1); return false;"
                                                <?php } ?>
                                                class="text-danger">
                                                <i class="fa-regular fa-clock"></i>
                                                <?= _l('task_stop_timer'); ?>
                                            </a>
                                            <?php
                                            } elseif ($t['not_finished']) {
                                                echo '<b>' . _l('timer_not_stopped_yet') . '</b>';
                                            } else {
                                                echo e(_dt($t['end_time'], true));
                                            }
                                ?>
                                        </td>
                                        <td>
                                            <?php
                                   $rel_data = get_relation_data($t['rel_type'], $t['rel_id']);
                                $rel_values  = get_relation_values($rel_data, $t['rel_type']);
                                echo '<a href="' . e($rel_values['link']) . '">' . e($rel_values['name']) . '</a>';
                                ?>
                                        </td>
                                        <td><?= e(app_format_money($t['hourly_rate'], $base_currency)); ?>
                                        </td>
                                        <td>
                                            <?= '<b>' . e(seconds_to_time_format($t['end_time'] - $t['start_time'])) . '</b>'; ?>
                                        </td>
                                        <td
                                            data-order="<?= e(sec2qty($t['total'])); ?>">
                                            <?php
                                $total_logged_time[] = ['total' => $t['total'], 'hourly_rate' => $t['hourly_rate']];
                                echo '<b>' . e(sec2qty($t['total'])) . '</b>';
                                ?>
                                        </td>
                                        <td>
                                            <?php
                                if (! $t['billed']) {
                                    if (staff_can('delete', 'tasks')
                                      || (staff_can('delete', 'projects') && $t['rel_type'] == 'project')
                                      || $t['staff_id'] == get_staff_user_id()) {
                                        echo '<a href="' . admin_url('tasks/delete_timesheet/' . $t['id']) . '" class="pull-right text-danger mtop5"><i class="fa fa-remove"></i></a>';
                                    }
                                }
                                ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td align="right"><?= '<b>' . _l('total_by_hourly_rate') . ':</b> ' . e(app_format_money(
                                            collect($total_logged_time)->reduce(function ($carry, $item) {
                                                return $carry + (sec2qty($item['total']) * (float) $item['hourly_rate']);
                                            }, 0),
                                            $base_currency
                                        )); ?>
                                        </td>
                                        <td align="right">
                                            <?= '<b>' . _l('total_logged_hours_by_staff') . ':</b> ' . e(seconds_to_time_format(
                                                collect($total_logged_time)->pluck('total')->sum()
                                            )); ?>
                                        </td>
                                        <td align="right">
                                            <?= '<b>' . _l('total_logged_hours_by_staff') . ':</b> ' . e(sec2qty(
                                                collect($total_logged_time)->pluck('total')->sum()
                                            )); ?>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="staff_projects">

                            <div class="_filters _hidden_inputs hidden staff_projects_filter">
                                <?= form_hidden('staff_id', $member->staffid); ?>
                            </div>
                            <?php render_datatable([
                                _l('project_name'),
                                _l('project_start_date'),
                                _l('project_deadline'),
                                _l('project_status'),
                            ], 'staff-projects'); ?>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php init_tail(); ?>
    <script>
        $(function() {
            $('select[name="role"]').on('change', function() {
                var roleid = $(this).val();
                init_roles_permissions(roleid, true);
            });

            $('input[name="administrator"]').on('change', function() {
                var checked = $(this).prop('checked');
                var isNotStaffMember = $('.is-not-staff');
                if (checked == true) {
                    isNotStaffMember.addClass('hide');
                    $('.roles').find('input').prop('disabled', true).prop('checked', false);
                } else {
                    isNotStaffMember.removeClass('hide');
                    isNotStaffMember.find('input').prop('checked', false);
                    $('.roles').find('.capability').not('[data-not-applicable="true"]').prop('disabled',
                        false)
                }
            });

            $('#is_not_staff').on('change', function() {
                var checked = $(this).prop('checked');
                var row_permission_leads = $('tr[data-name="leads"]');
                if (checked == true) {
                    row_permission_leads.addClass('hide');
                    row_permission_leads.find('input').prop('checked', false);
                } else {
                    row_permission_leads.removeClass('hide');
                }
            });

            init_roles_permissions();

            appValidateForm($('.staff-form'), {
                firstname: 'required',
                lastname: 'required',
                username: 'required',
                password: {
                    required: {
                        depends: function(element) {
                            return ($('input[name="isedit"]').length == 0) ? true : false
                        }
                    }
                },
                email: {
                    required: true,
                    email: true,
                    remote: {
                        url: admin_url + "misc/staff_email_exists",
                        type: 'post',
                        data: {
                            email: function() {
                                return $('input[name="email"]').val();
                            },
                            memberid: function() {
                                return $('input[name="memberid"]').val();
                            }
                        }
                    }
                }
            });
        });
    </script>
    </body>

    </html>