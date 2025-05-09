<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row section-heading section-profile">
    <div class="col-md-8">
        <?= form_open_multipart('clients/profile', ['autocomplete' => 'off']); ?>
        <?= form_hidden('profile', true); ?>
        <?php hooks()->do_action('before_client_profile_form_loaded'); ?>
        <h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700 section-text">
            <?= _l('clients_profile_heading'); ?>
        </h4>
        <div class="panel_s">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <?php if ($contact->profile_image == null) { ?>
                            <div class="form-group profile-image-upload-group">
                                <label for="profile_image"
                                    class="profile-image"><?= _l('client_profile_image'); ?></label>
                                <input type="file" name="profile_image" class="form-control" id="profile_image">
                            </div>
                            <?php } ?>
                            <?php if ($contact->profile_image != null) { ?>
                            <div class="form-group profile-image-group">
                                <div class="row">
                                    <div class="col-md-9">
                                        <img src="<?= e(contact_profile_image_url($contact->id, 'thumb')); ?>
" class="client-profile-image-thumb">
                                    </div>
                                    <div class="col-md-3 text-right">
                                        <a
                                            href="<?= site_url('clients/remove_profile_image'); ?>"><i
                                                class="fa fa-remove text-danger"></i></a>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>

                        </div>
                        <div class="form-group profile-firstname-group">
                            <label
                                for="firstname"><?= _l('clients_firstname'); ?></label>
                            <input type="text" class="form-control" name="firstname" id="firstname"
                                value="<?= set_value('firstname', $contact->firstname); ?>">
                            <?= form_error('firstname'); ?>
                        </div>
                        <div class="form-group profile-lastname-group">
                            <label
                                for="lastname"><?= _l('clients_lastname'); ?></label>
                            <input type="text" class="form-control" name="lastname" id="lastname"
                                value="<?= set_value('lastname', $contact->lastname); ?>">
                            <?= form_error('lastname'); ?>
                        </div>
                        <div class="form-group profile-positon-group">
                            <label
                                for="title"><?= _l('contact_position'); ?></label>
                            <input type="text" class="form-control" name="title" id="title"
                                value="<?= e($contact->title); ?>">
                        </div>
                        <div class="form-group profile-email-group">
                            <label
                                for="email"><?= _l('clients_email'); ?></label>
                            <input type="email" name="email" class="form-control" id="email"
                                value="<?= e($contact->email); ?>">
                            <?= form_error('email'); ?>
                        </div>
                        <div class="form-group profile-phone-group">
                            <label
                                for="phonenumber"><?= _l('clients_phone'); ?></label>
                            <input type="text" class="form-control" name="phonenumber" id="phonenumber"
                                value="<?= e($contact->phonenumber); ?>">
                        </div>
                        <div class="form-group contact-direction-option profile-direction-group">
                            <label
                                for="direction"><?= _l('document_direction'); ?></label>
                            <select
                                data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>"
                                class="form-control" name="direction" id="direction">
                                <option value="" <?php if (empty($contact->direction)) {
                                    echo 'selected';
                                } ?>><?= _l('system_default_string'); ?>
                                </option>
                                <option value="ltr" <?php if ($contact->direction == 'ltr') {
                                    echo 'selected';
                                } ?>>LTR</option>
                                <option value="rtl" <?php if ($contact->direction == 'rtl') {
                                    echo 'selected';
                                } ?>>RTL</option>
                            </select>
                        </div>
                        <?= render_custom_fields('contacts', get_contact_user_id(), ['show_on_client_portal' => 1]); ?>
                        <?php if (can_contact_view_email_notifications_options()) { ?>
                        <hr />
                        <p class="bold email-notifications-label">
                            <?= _l('email_notifications'); ?>
                        </p>
                        <?php if (has_contact_permission('invoices')) { ?>
                        <div class="checkbox checkbox-info email-notifications-invoices">
                            <input type="checkbox" value="1" id="invoice_emails" name="invoice_emails" <?php if ($contact->invoice_emails == 1) {
                                echo ' checked';
                            } ?>>
                            <label
                                for="invoice_emails"><?= _l('invoice'); ?></label>
                        </div>
                        <div class="checkbox checkbox-info email-notifications-credit-notes">
                            <input type="checkbox" value="1" id="credit_note_emails" name="credit_note_emails" <?php if ($contact->credit_note_emails == 1) {
                                echo ' checked';
                            } ?>>
                            <label
                                for="credit_note_emails"><?= _l('credit_note'); ?></label>
                        </div>
                        <?php } ?>
                        <?php if (has_contact_permission('estimates')) { ?>
                        <div class="checkbox checkbox-info email-notifications-estimates">
                            <input type="checkbox" value="1" id="estimate_emails" name="estimate_emails" <?php if ($contact->estimate_emails == 1) {
                                echo ' checked';
                            } ?>>
                            <label
                                for="estimate_emails"><?= _l('estimate'); ?></label>
                        </div>
                        <?php } ?>
                        <?php if (has_contact_permission('support')) { ?>
                        <div class="checkbox checkbox-info email-notifications-tickets">
                            <input type="checkbox" value="1" id="ticket_emails" name="ticket_emails" <?php if ($contact->ticket_emails == 1) {
                                echo ' checked';
                            } ?>>
                            <label
                                for="ticket_emails"><?= _l('tickets'); ?></label>
                        </div>
                        <?php } ?>
                        <?php if (has_contact_permission('contracts')) { ?>
                        <div class="checkbox checkbox-info email-notifications-contracts">
                            <input type="checkbox" value="1" id="contract_emails" name="contract_emails" <?php if ($contact->contract_emails == 1) {
                                echo ' checked';
                            } ?>>
                            <label
                                for="contract_emails"><?= _l('contract'); ?></label>
                        </div>
                        <?php } ?>
                        <?php if (has_contact_permission('projects')) { ?>
                        <div class="checkbox checkbox-info email-notifications-projects">
                            <input type="checkbox" value="1" id="project_emails" name="project_emails" <?php if ($contact->project_emails == 1) {
                                echo ' checked';
                            } ?>>
                            <label
                                for="project_emails"><?= _l('project'); ?></label>
                        </div>
                        <div class="checkbox checkbox-info email-notifications-tasks">
                            <input type="checkbox" value="1" id="task_emails" name="task_emails" <?php if ($contact->task_emails == 1) {
                                echo ' checked';
                            } ?>>
                            <label
                                for="task_emails"><?= _l('task'); ?></label>
                        </div>
                        <?php } ?>
                        <?php } ?>
                        <?php hooks()->do_action('after_client_profile_form_loaded'); ?>
                    </div>
                </div>
            </div>
            <div class="panel-footer text-right contact-profile-save-section">
                <button type="submit" class="btn btn-primary contact-profile-save">
                    <?= _l('clients_edit_profile_update_btn'); ?>
                </button>
            </div>
        </div>
        <?= form_close(); ?>
    </div>
    <div class="col-md-4 contact-profile-change-password-section">
        <h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700 section-text">
            <?= _l('clients_edit_profile_change_password_heading'); ?>
        </h4>
        <div class="panel_s">
            <div class="panel-body">
                <?= form_open('clients/profile'); ?>
                <?= form_hidden('change_password', true); ?>
                <div class="form-group">
                    <label
                        for="oldpassword"><?= _l('clients_edit_profile_old_password'); ?></label>
                    <input type="password" class="form-control" name="oldpassword" id="oldpassword">
                    <?= form_error('oldpassword'); ?>
                </div>
                <div class="form-group">
                    <label
                        for="newpassword"><?= _l('clients_edit_profile_new_password'); ?></label>
                    <input type="password" class="form-control" name="newpassword" id="newpassword">
                    <?= form_error('newpassword'); ?>
                </div>
                <div class="form-group">
                    <label
                        for="newpasswordr"><?= _l('clients_edit_profile_new_password_repeat'); ?></label>
                    <input type="password" class="form-control" name="newpasswordr" id="newpasswordr">
                    <?= form_error('newpasswordr'); ?>
                </div>
                <div class="form-group">
                    <button type="submit"
                        class="btn btn-primary btn-block"><?= _l('clients_edit_profile_change_password_btn'); ?></button>
                </div>
                <?= form_close(); ?>
            </div>
            <?php if ($contact->last_password_change !== null) { ?>
            <div class="panel-footer last-password-change">
                <?= e(_l('clients_profile_last_changed_password', time_ago($contact->last_password_change))); ?>
            </div>
            <?php } ?>
        </div>
        <?php hooks()->do_action('after_client_profile_password_form_loaded'); ?>
    </div>

</div>