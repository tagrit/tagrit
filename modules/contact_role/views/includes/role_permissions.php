<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="permissions">
    <p class="bold"><?php echo _l('customer_permissions'); ?></p>
    <?php
    $default_contact_permissions = [];
    if (!isset($role)) {
        $default_contact_permissions = @unserialize(get_option('default_contact_permissions'));
    }
    ?>
    <?php foreach ($customer_permissions as $permission) { ?>
    <div class="col-md-6 row">
        <div class="row">
            <div class="col-md-6 mtop10 border-right">
                <span><?php echo $permission['name']; ?></span>
            </div>
            <div class="col-md-6 mtop10">
                <div class="onoffswitch">
                    <input type="checkbox" id="<?php echo $permission['id']; ?>" class="onoffswitch-checkbox"
                        <?php if (isset($contact) && has_contact_permission($permission['short_name'], $contact->id) || isset($role) && in_array($permission['id'], $role_permissions) || is_array($default_contact_permissions) && in_array($permission['id'], $default_contact_permissions)) {
                                                                                                                        echo 'checked';
                                                                                                                    } ?> value="<?php echo $permission['id']; ?>" name="permissions[]">
                    <label class="onoffswitch-label" for="<?php echo $permission['id']; ?>"></label>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <?php } ?>
</div>

<div id="contact_email_notifications">
    <hr />
    <p class="bold"><?php echo _l('email_notifications'); ?><?php if (is_sms_trigger_active()) {
                                                                echo '/SMS';
                                                            } ?></p>

    <?php
    $checkboxes = [
        ['id' => 'invoice_emails', 'label' => _l('invoice'), 'value' => 'invoice_emails', 'perm_id' => 1],
        ['id' => 'estimate_emails', 'label' => _l('estimate'), 'value' => 'estimate_emails', 'perm_id' => 2],
        ['id' => 'credit_note_emails', 'label' => _l('credit_note'), 'value' => 'credit_note_emails', 'perm_id' => 1],
        ['id' => 'project_emails', 'label' => _l('project'), 'value' => 'project_emails', 'perm_id' => 6],
        ['id' => 'ticket_emails', 'label' => _l('tickets'), 'value' => 'ticket_emails', 'perm_id' => 5],
        ['id' => 'task_emails', 'label' => _l('task'), 'value' => 'task_emails', 'perm_id' => 6, 'hint' => _l('only_project_tasks'),],
        ['id' => 'contract_emails', 'label' => _l('contract'), 'value' => 'contract_emails', 'perm_id' => 3],
    ];

    foreach ($checkboxes as $checkbox) :
    ?>
    <div class="col-md-6 row">
        <div class="row">
            <div class="col-md-6 mtop10 border-right">
                <span>
                    <?php if (isset($checkbox['hint'])) : ?>
                    <i class="fa-regular fa-circle-question" data-toggle="tooltip"
                        data-title="<?php echo _l('only_project_tasks'); ?>"></i>
                    <?php endif; ?>
                    <?php echo $checkbox['label']; ?>
                </span>
            </div>
            <div class="col-md-6 mtop10">
                <div class="onoffswitch">
                    <input type="checkbox" id="<?php echo $checkbox['id']; ?>"
                        data-perm-id="<?php echo $checkbox['perm_id']; ?>" class="onoffswitch-checkbox"
                        <?php                                                                                                                                   ?>
                        value="<?php echo $checkbox['value']; ?>"
                        name="<?php echo (isset($contact) || isset($adding_new_contact)) && isset($contact_role) ? $checkbox['value'] : 'email_notifications[' . $checkbox['value'] . ']'; ?>"
                        <?= (isset($contact) && $contact->{$checkbox['value']} == '1') ||
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    (isset($email_notifications) && in_array($checkbox['value'], $email_notifications)) ||
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    isset($adding_new_contact) ? 'checked' : ''; ?>>
                    <label class="onoffswitch-label" for="<?php echo $checkbox['id']; ?>"></label>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>