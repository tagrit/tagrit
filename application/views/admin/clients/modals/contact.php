<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- Modal Contact -->
<div class="modal fade" id="contact" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?= form_open(admin_url('clients/form_contact/' . $customer_id . ($contactid ? '/' . $contactid : '')), ['id' => 'contact-form', 'autocomplete' => 'off']); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <div class="tw-flex">
                    <div class="tw-mr-4 tw-flex-shrink-0 tw-relative">
                        <?php if (isset($contact)) { ?>
                        <img src="<?= e(contact_profile_image_url($contact->id, 'small')); ?>"
                            id="contact-img" class="client-profile-image-small">
                        <?php if (! empty($contact->profile_image)) { ?>
                        <a href="#"
                            onclick="delete_contact_profile_image(<?= e($contact->id); ?>); return false;"
                            class="tw-bg-neutral-500/30 tw-text-neutral-600 hover:tw-text-neutral-500 tw-h-8 tw-w-8 tw-inline-flex tw-items-center tw-justify-center tw-rounded-full tw-absolute tw-inset-0"
                            id="contact-remove-img"><i class="fa fa-remove tw-mt-1"></i></a>
                        <?php } ?>
                        <?php } ?>
                    </div>
                    <div>
                        <h4 class="modal-title tw-mb-0">
                            <?= e($title); ?>
                        </h4>
                        <p class="tw-mb-0">
                            <?= e(get_company_name($customer_id, true)); ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">

                        <div id="contact-profile-image"
                            class="form-group<?= (isset($contact) && ! empty($contact->profile_image)) ? ' hide' : ''; ?>">
                            <label for="profile_image"
                                class="profile-image"><?= _l('client_profile_image'); ?></label>
                            <input type="file" name="profile_image" class="form-control" id="profile_image">
                        </div>
                        <?php if (isset($contact)) { ?>
                        <div class="alert alert-warning hide" role="alert" id="contact_proposal_warning">
                            <?= _l('proposal_warning_email_change', [_l('contact_lowercase'), _l('contact_lowercase'), _l('contact_lowercase')]); ?>
                            <hr />
                            <a href="#" id="contact_update_proposals_emails" class="alert-link" data-original-email=""
                                onclick="update_all_proposal_emails_linked_to_contact(<?= e($contact->id); ?>); return false;">
                                <?= _l('update_proposal_email_yes'); ?>
                            </a>
                            <br />
                            <a href="#" class="alert-link" onclick="close_modal_manually('#contact'); return false;">
                                <?= _l('update_proposal_email_no'); ?>
                            </a>
                        </div>
                        <?php } ?>
                        <!-- // For email exist check -->
                        <?= form_hidden('contactid', $contactid); ?>
                        <?php $value = (isset($contact) ? $contact->firstname : ''); ?>
                        <?= render_input('firstname', 'client_firstname', $value); ?>
                        <?php $value = (isset($contact) ? $contact->lastname : ''); ?>
                        <?= render_input('lastname', 'client_lastname', $value); ?>
                        <?php $value = (isset($contact) ? $contact->title : ''); ?>
                        <?= render_input('title', 'contact_position', $value); ?>
                        <?php $value = (isset($contact) ? $contact->email : ''); ?>
                        <?= render_input('email', 'client_email', $value, 'email'); ?>
                        <?php if (! isset($contact)) {
                            $value = $calling_code ?: '';
                        } else {
                            $value = empty($contact->phonenumber) ? $calling_code : $contact->phonenumber;
                        } ?>
                        <?= render_input('phonenumber', 'client_phonenumber', $value, 'text', ['autocomplete' => 'off']); ?>
                        <div class="form-group contact-direction-option">
                            <label
                                for="direction"><?= _l('document_direction'); ?></label>
                            <select class="selectpicker"
                                data-none-selected-text="<?= _l('system_default_string'); ?>"
                                data-width="100%" name="direction" id="direction">
                                <option value="" <?= (isset($contact) && empty($contact->direction)) ? 'selected' : ''; ?>>
                                </option>
                                <option value="ltr" <?= (isset($contact) && $contact->direction == 'ltr') ? 'selected' : ''; ?>>
                                    LTR
                                </option>
                                <option value="rtl" <?= (isset($contact) && $contact->direction == 'rtl') ? 'selected' : ''; ?>>
                                    RTL
                                </option>
                            </select>
                        </div>
                        <?php $rel_id = (isset($contact) ? $contact->id : false); ?>
                        <?= render_custom_fields('contacts', $rel_id); ?>


                        <!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
                        <input type="text" class="fake-autofill-field" name="fakeusernameremembered" value=''
                            tabindex="-1" />
                        <input type="password" class="fake-autofill-field" name="fakepasswordremembered" value=''
                            tabindex="-1" />

                        <div class="client_password_set_wrapper">
                            <label for="password" class="control-label">
                                <?= _l('client_password'); ?>
                            </label>
                            <div class="input-group">

                                <input type="password" class="form-control password" name="password"
                                    autocomplete="false">
                                <span class="input-group-addon tw-border-l-0">
                                    <a href="#password" class="show_password"
                                        onclick="showPassword('password'); return false;"><i class="fa fa-eye"></i></a>
                                </span>
                                <span class="input-group-addon">
                                    <a href="#" class="generate_password"
                                        onclick="generatePassword(this);return false;"><i class="fa fa-refresh"></i></a>
                                </span>
                            </div>
                            <?php if (isset($contact)) { ?>
                            <p class="text-muted tw-mt-2">
                                <?= _l('client_password_change_populate_note'); ?>
                            </p>
                            <?php if ($contact->last_password_change != null) { ?>
                            <?= _l('client_password_last_changed'); ?>
                            <span class="text-has-action" data-toggle="tooltip"
                                data-title="<?= e(_dt($contact->last_password_change)); ?>">
                                <?= e(time_ago($contact->last_password_change)); ?>
                            </span>
                            <?php } ?>
                            <?php } ?>
                        </div>
                        <hr />
                        <div class="checkbox">
                            <input type="checkbox" name="is_primary" id="contact_primary"
                                <?= ((! isset($contact) && total_rows(db_prefix() . 'contacts', ['is_primary' => 1, 'userid' => $customer_id]) == 0) || (isset($contact) && $contact->is_primary == 1)) ? 'checked' : ''; ?><?= ((isset($contact) && total_rows(db_prefix() . 'contacts', ['is_primary' => 1, 'userid' => $customer_id]) == 1 && $contact->is_primary == 1)) ? ' disabled' : ''; ?>>
                            <label for="contact_primary">
                                <?= _l('contact_primary'); ?>
                            </label>
                        </div>
                        <?php if (! isset($contact) && is_email_template_active('new-client-created')) { ?>
                        <div class="checkbox">
                            <input type="checkbox" name="donotsendwelcomeemail" id="donotsendwelcomeemail">
                            <label for="donotsendwelcomeemail">
                                <?= _l('client_do_not_send_welcome_email'); ?>
                            </label>
                        </div>
                        <?php } ?>
                        <?php if (is_email_template_active('contact-set-password')) { ?>
                        <div class="checkbox">
                            <input type="checkbox" name="send_set_password_email" id="send_set_password_email">
                            <label for="send_set_password_email">
                                <?= _l('client_send_set_password_email'); ?>
                            </label>
                        </div>
                        <?php } ?>
                        <hr />
                        <p class="bold">
                            <?= _l('customer_permissions'); ?>
                        </p>
                        <p class="text-danger">
                            <?= _l('contact_permissions_info'); ?>
                        </p>
                        <?php $default_contact_permissions = []; ?>
                        <?php if (! isset($contact)) {
                            $default_contact_permissions = @unserialize(get_option('default_contact_permissions'));
                        }?>
                        <?php foreach ($customer_permissions as $permission) { ?>
                        <div class="col-md-6 row">
                            <div class="row">
                                <div class="col-md-6 mtop10 border-right">
                                    <span><?= e($permission['name']); ?></span>
                                </div>
                                <div class="col-md-6 mtop10">
                                    <div class="onoffswitch">
                                        <input type="checkbox"
                                            id="<?= e($permission['id']); ?>"
                                            class="onoffswitch-checkbox"
                                            <?= (isset($contact) && has_contact_permission($permission['short_name'], $contact->id) || is_array($default_contact_permissions) && in_array($permission['id'], $default_contact_permissions)) ? 'checked' : ''; ?>
                                        value="<?= e($permission['id']); ?>"
                                        name="permissions[]">
                                        <label class="onoffswitch-label"
                                            for="<?= e($permission['id']); ?>"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <?php } ?>
                        <hr />
                        <p class="bold">
                            <?= _l('email_notifications'); ?><?php if (is_sms_trigger_active()) {
                                echo '/SMS';
                            } ?>
                        </p>
                        <div id="contact_email_notifications">
                            <div class="col-md-6 row">
                                <div class="row">
                                    <div class="col-md-6 mtop10 border-right">
                                        <span><?= _l('invoice'); ?></span>
                                    </div>
                                    <div class="col-md-6 mtop10">
                                        <div class="onoffswitch">
                                            <input type="checkbox" id="invoice_emails" data-perm-id="1"
                                                class="onoffswitch-checkbox"
                                                <?= (isset($contact) && $contact->invoice_emails == '1') ? 'checked' : ''; ?>
                                            value="invoice_emails" name="invoice_emails">
                                            <label class="onoffswitch-label" for="invoice_emails"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 row">
                                <div class="row">
                                    <div class="col-md-6 mtop10 border-right">
                                        <span><?= _l('estimate'); ?></span>
                                    </div>
                                    <div class="col-md-6 mtop10">
                                        <div class="onoffswitch">
                                            <input type="checkbox" id="estimate_emails" data-perm-id="2"
                                                class="onoffswitch-checkbox"
                                                <?= (isset($contact) && $contact->estimate_emails == '1') ? 'checked' : ''; ?>
                                            value="estimate_emails"
                                            name="estimate_emails">
                                            <label class="onoffswitch-label" for="estimate_emails"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 row">
                                <div class="row">
                                    <div class="col-md-6 mtop10 border-right">
                                        <span><?= _l('credit_note'); ?></span>
                                    </div>
                                    <div class="col-md-6 mtop10">
                                        <div class="onoffswitch">
                                            <input type="checkbox" id="credit_note_emails" data-perm-id="1"
                                                class="onoffswitch-checkbox"
                                                <?= (isset($contact) && $contact->credit_note_emails == '1') ? 'checked' : ''; ?>
                                            value="credit_note_emails"
                                            name="credit_note_emails">
                                            <label class="onoffswitch-label" for="credit_note_emails"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 row">
                                <div class="row">
                                    <div class="col-md-6 mtop10 border-right">
                                        <span><?= _l('project'); ?></span>
                                    </div>
                                    <div class="col-md-6 mtop10">
                                        <div class="onoffswitch">
                                            <input type="checkbox" id="project_emails" data-perm-id="6"
                                                class="onoffswitch-checkbox"
                                                <?= (isset($contact) && $contact->project_emails == '1') ? 'checked' : ''; ?>
                                            value="project_emails" name="project_emails">
                                            <label class="onoffswitch-label" for="project_emails"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 row">
                                <div class="row">
                                    <div class="col-md-6 mtop10 border-right">
                                        <span><?= _l('tickets'); ?></span>
                                    </div>
                                    <div class="col-md-6 mtop10">
                                        <div class="onoffswitch">
                                            <input type="checkbox" id="ticket_emails" data-perm-id="5"
                                                class="onoffswitch-checkbox"
                                                <?= (isset($contact) && $contact->ticket_emails == '1') ? 'checked' : ''; ?>
                                            value="ticket_emails" name="ticket_emails">
                                            <label class="onoffswitch-label" for="ticket_emails"></label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mtop10 border-right">
                                        <span><i class="fa-regular fa-circle-question" data-toggle="tooltip"
                                                data-title="<?= _l('only_project_tasks'); ?>"></i>
                                            <?= _l('task'); ?></span>
                                    </div>
                                    <div class="col-md-6 mtop10">
                                        <div class="onoffswitch">
                                            <input type="checkbox" id="task_emails" data-perm-id="6"
                                                class="onoffswitch-checkbox"
                                                <?= (isset($contact) && $contact->task_emails == '1') ? 'checked' : ''; ?>
                                            value="task_emails" name="task_emails">
                                            <label class="onoffswitch-label" for="task_emails"></label>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-6 row">
                                <div class="row">
                                    <div class="col-md-6 mtop10 border-right">
                                        <span><?= _l('contract'); ?></span>
                                    </div>
                                    <div class="col-md-6 mtop10">
                                        <div class="onoffswitch">
                                            <input type="checkbox" id="contract_emails" data-perm-id="3"
                                                class="onoffswitch-checkbox"
                                                <?= (isset($contact) && $contact->contract_emails == '1') ? 'checked' : ''; ?>
                                            value="contract_emails"
                                            name="contract_emails">
                                            <label class="onoffswitch-label" for="contract_emails"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php hooks()->do_action('after_contact_modal_content_loaded'); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                    data-dismiss="modal"><?= _l('close'); ?></button>
                <button type="submit" class="btn btn-primary"
                    data-loading-text="<?= _l('wait_text'); ?>"
                    autocomplete="off"
                    data-form="#contact-form"><?= _l('submit'); ?></button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>
<?php if (! isset($contact)) { ?>
<script>
    $(function() {
        // Guess auto email notifications based on the default contact permissios
        var permInputs = $('input[name="permissions[]"]');
        $.each(permInputs, function(i, input) {
            input = $(input);
            if (input.prop('checked') === true) {
                $('#contact_email_notifications [data-perm-id="' + input.val() + '"]').prop('checked',
                    true);
            }
        });
    });
</script>
<?php } ?>