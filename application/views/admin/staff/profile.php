<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="tw-max-w-4xl tw-mx-auto">
            <h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700">
                <?= e($title); ?>
            </h4>
            <?= form_open_multipart($this->uri->uri_string(), ['id' => 'staff_profile_table', 'autocomplete' => 'off']); ?>

            <div class="panel_s">
                <div class="panel-body">
                    <?php if ($current_user->profile_image == null) { ?>
                    <div class="form-group">
                        <label for="profile_image"
                            class="profile-image"><?= _l('staff_edit_profile_image'); ?></label>
                        <input type="file" name="profile_image" class="form-control" id="profile_image">
                    </div>
                    <?php } ?>
                    <?php if ($current_user->profile_image != null) { ?>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-9">
                                <?= staff_profile_image($current_user->staffid, ['img', 'img-responsive', 'staff-profile-image-thumb'], 'thumb'); ?>
                            </div>
                            <div class="col-md-3 text-right">
                                <a
                                    href="<?= admin_url('staff/remove_staff_profile_image'); ?>"><i
                                        class="fa fa-remove"></i></a>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="form-group">
                        <label for="firstname"
                            class="control-label"><?= _l('staff_add_edit_firstname'); ?></label>
                        <input type="text" class="form-control" name="firstname"
                            value="<?= isset($member) ? e($member->firstname) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="lastname"
                            class="control-label"><?= _l('staff_add_edit_lastname'); ?></label>
                        <input type="text" class="form-control" name="lastname"
                            value="<?= isset($member) ? e($member->lastname) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="email"
                            class="control-label"><?= _l('staff_add_edit_email'); ?></label>
                        <input type="email"
                            <?php if (staff_can('edit', 'staff')) { ?>
                        name="email"
                        <?php } else { ?> disabled="true"
                        <?php } ?> class="form-control"
                        value="<?= e($member->email); ?>"
                        id="email">
                    </div>
                    <?php $value = (isset($member) ? $member->phonenumber : ''); ?>
                    <?= render_input('phonenumber', 'staff_add_edit_phonenumber', $value); ?>
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
                            <?php foreach ($this->app->get_available_languages() as $availableLanguage) {
                                $selected = '';
                                if (isset($member)) {
                                    if ($member->default_language == $availableLanguage) {
                                        $selected = 'selected';
                                    }
                                } ?>
                            <option
                                value="<?= e($availableLanguage); ?>"
                                <?= e($selected); ?>>
                                <?= e(ucfirst($availableLanguage)); ?>
                            </option>
                            <?php
                            } ?>
                        </select>
                    </div>
                    <?php } ?>
                    <div class="form-group select-placeholder">
                        <label
                            for="direction"><?= _l('document_direction'); ?></label>
                        <select class="selectpicker"
                            data-none-selected-text="<?= _l('system_default_string'); ?>"
                            data-width="100%" name="direction" id="direction">
                            <option value="" <?= isset($member) && empty($member->direction) ? ' selected' : '' ?>>
                            </option>
                            <option value="ltr" <?= isset($member) && $member->direction == 'ltr' ? ' selected' : '' ?>>
                                LTR
                            </option>
                            <option value="rtl" <?= isset($member) && $member->direction == 'rtl' ? ' selected' : '' ?>>
                                RTL
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="facebook" class="control-label"><i class="fa-brands fa-facebook-f"></i>
                            <?= _l('staff_add_edit_facebook'); ?></label>
                        <input type="text" class="form-control" name="facebook"
                            value="<?= isset($member) ? e($member->facebook) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="linkedin" class="control-label"><i class="fa-brands fa-linkedin-in"></i>
                            <?= _l('staff_add_edit_linkedin'); ?></label>
                        <input type="text" class="form-control" name="linkedin"
                            value="<?= isset($member) ? e($member->linkedin) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="skype" class="control-label"><i class="fa-brands fa-skype"></i>
                            <?= _l('staff_add_edit_skype'); ?></label>
                        <input type="text" class="form-control" name="skype"
                            value="<?= isset($member) ? e($member->skype) : ''; ?>">
                    </div>
                    <i class="fa-regular fa-circle-question" data-toggle="tooltip"
                        data-title="<?= _l('staff_email_signature_help'); ?>"></i>
                    <?php $value = (isset($member) ? $member->email_signature : ''); ?>
                    <?= render_textarea('email_signature', 'settings_email_signature', $value, ['data-entities-encode' => 'true']); ?>
                    <?php if (count($staff_departments) > 0) { ?>
                    <div class="form-group">
                        <label
                            for="departments"><?= _l('staff_edit_profile_your_departments'); ?></label>
                        <div class="clearfix"></div>
                        <?php foreach ($departments as $department) { ?>
                        <?php foreach ($staff_departments as $staff_department) { ?>
                        <?php if ($staff_department['departmentid'] == $department['departmentid']) { ?>
                        <div class="label label-primary">
                            <?= e($staff_department['name']); ?>
                        </div>
                        <?php } ?>
                        <?php } ?>
                        <?php } ?>
                    </div>
                    <?php } ?>
                </div>
                <div class="panel-footer text-right">
                    <button type="submit" class="btn btn-primary">
                        <?= _l('submit'); ?>
                    </button>
                </div>
            </div>
            <?= form_close(); ?>

            <h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700">
                <?= _l('staff_edit_profile_change_your_password'); ?>
            </h4>

            <?= form_open('admin/staff/change_password_profile', ['id' => 'staff_password_change_form']); ?>

            <div class="panel_s">
                <div class="panel-body">
                    <div class="form-group">
                        <label for="oldpassword"
                            class="control-label"><?= _l('staff_edit_profile_change_old_password'); ?></label>
                        <input type="password" class="form-control" name="oldpassword" id="oldpassword">
                    </div>
                    <div class="form-group">
                        <label for="newpassword"
                            class="control-label"><?= _l('staff_edit_profile_change_new_password'); ?></label>
                        <input type="password" class="form-control" id="newpassword" name="newpassword">
                    </div>
                    <div class="form-group">
                        <label for="newpasswordr"
                            class="control-label"><?= _l('staff_edit_profile_change_repeat_new_password'); ?></label>
                        <input type="password" class="form-control" id="newpasswordr" name="newpasswordr">
                    </div>
                </div>

                <div class="panel-footer">
                    <div class="tw-flex tw-justify-between">
                        <span>
                            <?php if ($member->last_password_change != null) { ?>
                            <?= _l('staff_add_edit_password_last_changed'); ?>:
                            <span class="text-has-action" data-toggle="tooltip"
                                data-title="<?= e(_dt($member->last_password_change)); ?>">
                                <?= e(time_ago($member->last_password_change)); ?>
                            </span>
                            <?php } ?>
                        </span>
                        <button type="submit"
                            class="btn btn-primary"><?= _l('submit'); ?></button>
                    </div>
                </div>
            </div>
            <?= form_close(); ?>


            <h4 class="tw-font-semibold tw-text-lg tw-text-neutral-700 mtop30">
                <?= _l('staff_two_factor_authentication'); ?>
            </h4>

            <?= form_open('admin/staff/update_two_factor', ['id' => 'two_factor_auth_form']); ?>
            <div class="panel_s">
                <div class="panel-body">
                    <div class="radio radio-primary">
                        <input type="radio" id="two_factor_auth_disabled" name="two_factor_auth" value="off"
                            class="custom-control-input"
                            <?= ($current_user->two_factor_auth_enabled == 0) ? 'checked' : '' ?>>
                        <label class="custom-control-label"
                            for="two_factor_auth_disabled"><?= _l('two_factor_authentication_disabed'); ?></label>
                    </div>
                    <?php if (is_email_template_active('two-factor-authentication')) { ?>
                    <div class="radio radio-primary">
                        <input type="radio" id="two_factor_auth_enabled" name="two_factor_auth" value="email"
                            class="custom-control-input"
                            <?= ($current_user->two_factor_auth_enabled == 1) ? 'checked' : '' ?>>
                        <label for="two_factor_auth_enabled">
                            <i class="fa-regular fa-circle-question" data-placement="right" data-toggle="tooltip"
                                data-title="<?= _l('two_factor_authentication_info'); ?>"></i>
                            <?= _l('enable_two_factor_authentication'); ?>
                        </label>
                    </div>
                    <?php } ?>
                    <div class="radio radio-primary">
                        <input type="radio" id="google_two_factor_auth_enabled" name="two_factor_auth" value="google"
                            class="custom-control-input"
                            <?= ($current_user->two_factor_auth_enabled == 2) ? 'checked' : '' ?>>
                        <label class="custom-control-label"
                            for="google_two_factor_auth_enabled"><?= _l('enable_google_two_factor_authentication'); ?></label>
                    </div>
                    <div id="qr_image" class=" mtop30 card">
                    </div>


                </div>
                <div class="panel-footer text-right">
                    <button id="submit_2fa" type="submit" class="btn btn-primary">
                        <?= _l('submit'); ?>
                    </button>
                </div>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
    <?php init_tail(); ?>
    <script>
        $(function() {
            var qr_loaded = 0;
            var is_g2fa_enabled =
                "<?= $current_user->two_factor_auth_enabled ?>"
            $('input[type=radio][name="two_factor_auth"]').change(function() {
                if (this.value == 'google') {
                    if (is_g2fa_enabled == 2) {
                        return;
                    }

                    if (qr_loaded == 0) {
                        $('#qr_image').load(admin_url + 'authentication/get_qr', {}, function(response,
                            status) {
                            qr_loaded = 1;
                            $('#qr_image').show();
                        });
                    } else {
                        $('#qr_image').show();
                    }
                    $('#submit_2fa').prop("disabled", true);
                } else {
                    $('#qr_image').hide();
                    $('#submit_2fa').prop("disabled", false);
                }
            });
            appValidateForm($('#staff_profile_table'), {
                firstname: 'required',
                lastname: 'required',
                email: 'required'
            });
            appValidateForm($('#staff_password_change_form'), {
                oldpassword: 'required',
                newpassword: 'required',
                newpasswordr: {
                    equalTo: "#newpassword"
                }
            });
            appValidateForm($('#two_factor_auth_form'), {
                two_factor_auth: 'required'
            });
        });
    </script>
    </body>

    </html>