<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="horizontal-scrollable-tabs panel-full-width-tabs">
    <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
    <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
    <div class="horizontal-tabs">
        <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
            <li role="presentation" class="active">
                <a href="#set_tickets_general" aria-controls="set_tickets_general" role="tab"
                    data-toggle="tab"><?= _l('settings_group_general'); ?></a>
            </li>
            <li role="presentation">
                <a href="#set_tickets_piping" aria-controls="set_tickets_piping" role="tab"
                    data-toggle="tab"><?= _l('tickets_piping'); ?></a>
            </li>
            <li role="presentation">
                <a href="#ticket_form" aria-controls="ticket_form" role="tab"
                    data-toggle="tab"><?= _l('ticket_form'); ?></a>
            </li>
        </ul>
    </div>
</div>
<div class="tab-content mtop15">
    <div role="tabpanel" class="tab-pane active" id="set_tickets_general">
        <?php render_yes_no_option('services', 'settings_tickets_use_services'); ?>
        <hr />
        <?php render_yes_no_option('disable_ticket_public_url', 'disable_ticket_public_url'); ?>
        <hr />
        <?php render_yes_no_option('staff_access_only_assigned_departments', 'settings_tickets_allow_departments_access'); ?>
        <hr />
        <?php render_yes_no_option('staff_related_ticket_notification_to_assignee_only', 'staff_related_ticket_notification_to_assignee_only', 'staff_related_ticket_notification_to_assignee_only_help'); ?>
        <hr />
        <?php render_yes_no_option('receive_notification_on_new_ticket', 'receive_notification_on_new_ticket', 'receive_notification_on_new_ticket_help'); ?>
        <hr />
        <?php render_yes_no_option('receive_notification_on_new_ticket_replies', 'receive_notification_on_new_ticket_replies', 'receive_notification_on_new_ticket_reply_help'); ?>
        <hr />
        <?php render_yes_no_option('staff_members_open_tickets_to_all_contacts', 'staff_members_open_tickets_to_all_contacts', 'staff_members_open_tickets_to_all_contacts_help'); ?>
        <hr />
        <?php render_yes_no_option('automatically_assign_ticket_to_first_staff_responding', 'automatically_assign_ticket_to_first_staff_responding'); ?>
        <hr />
        <?php render_yes_no_option('access_tickets_to_none_staff_members', 'access_tickets_to_none_staff_members'); ?>
        <hr />
        <?php render_yes_no_option('allow_non_admin_staff_to_delete_ticket_attachments', 'allow_non_admin_staff_to_delete_ticket_attachments'); ?>
        <hr />
        <?php render_yes_no_option('allow_non_admin_members_to_delete_tickets_and_replies', 'allow_non_admin_members_to_delete_tickets_and_replies'); ?>
        <hr />
        <?php render_yes_no_option('allow_non_admin_members_to_edit_ticket_messages', 'allow_non_admin_members_to_edit_ticket_messages'); ?>
        <hr />
        <?php render_yes_no_option('allow_customer_to_change_ticket_status', 'allow_customer_to_change_ticket_status'); ?>
        <hr />
        <?php render_yes_no_option('only_show_contact_tickets', 'only_show_contact_tickets'); ?>
        <hr />
        <?php render_yes_no_option('ticket_replies_order', 'ticket_replies_order', 'ticket_replies_order_notice', _l('order_ascending'), _l('order_descending'), 'asc', 'desc'); ?>
        <hr />
        <?php render_yes_no_option('enable_support_menu_badges', 'enable_support_menu_badges'); ?>
        <hr />
        <?php
      $this->load->model('tickets_model');
$statuses                       = $this->tickets_model->get_ticket_status();
$statuses['callback_translate'] = 'ticket_status_translate';
echo render_select('settings[default_ticket_reply_status]', $statuses, ['ticketstatusid', 'name'], 'default_ticket_reply_status', get_option('default_ticket_reply_status'), [], [], '', '', false); ?>
        <hr />
        <?= render_input('settings[maximum_allowed_ticket_attachments]', 'settings_tickets_max_attachments', get_option('maximum_allowed_ticket_attachments'), 'number'); ?>
        <hr />
        <?= render_input('settings[ticket_attachments_file_extensions]', 'settings_tickets_allowed_file_extensions', get_option('ticket_attachments_file_extensions')); ?>
    </div>
    <div role="tabpanel" class="tab-pane" id="set_tickets_piping">
        cPanel forwarder path:
        <code><?= hooks()->apply_filters('cpanel_tickets_forwarder_path', FCPATH . 'pipe.php'); ?></code>
        <hr />
        <?php render_yes_no_option('email_piping_only_registered', 'email_piping_only_registered'); ?>
        <hr />
        <?php render_yes_no_option('email_piping_only_replies', 'email_piping_only_replies'); ?>
        <hr />
        <?php render_yes_no_option('ticket_import_reply_only', 'ticket_import_reply_only'); ?>
        <hr />
        <?= render_select('settings[email_piping_default_priority]', $ticket_priorities, ['priorityid', 'name'], 'email_piping_default_priority', get_option('email_piping_default_priority')); ?>
    </div>
    <div role="tabpanel" class="tab-pane" id="ticket_form">
        <h4 class="bold">Form Info</h4>
        <p><b>Form url:</b>
            <span class="label label-default">
                <a href="<?= site_url('forms/ticket'); ?>"
                    target="_blank">
                    <?= site_url('forms/ticket'); ?>
                </a>
            </span>
        </p>
        <p><b>Form file location:</b>
            <code><?= hooks()->apply_filters('ticket_form_file_location_settings', VIEWPATH . 'forms/ticket.php'); ?></code>
        </p>
        <hr />
        <h4 class="bold font-medium">Embed form</h4>
        <p><?= _l('form_integration_code_help'); ?>
        </p>
        <textarea class="form-control"
            rows="2"><iframe width="600" height="850" src="<?= site_url('forms/ticket'); ?>" frameborder="0" allowfullscreen></iframe></textarea>
        <h4 class="mtop15 font-medium bold">Share direct link</h4>
        <p>
            <span class="label label-default">
                <a href="<?= site_url('forms/ticket') . '?styled=1'; ?>"
                    target="_blank">
                    <?= site_url('forms/ticket') . '?styled=1'; ?>
                </a>
            </span>
            <br />
            <br />
            <span class="label label-default">
                <a href="<?= site_url('forms/ticket') . '?styled=1&with_logo=1'; ?>"
                    target="_blank">
                    <?= site_url('forms/ticket') . '?styled=1&with_logo=1'; ?>
                </a>
            </span>
        </p>
        <hr />
        <p class="bold mtop15">When placing the iframe snippet code consider the following:</p>
        <p class="<?php if (strpos(site_url(), 'http://') !== false) {
            echo 'bold text-success';
        } ?>">1. If the protocol of your installation is http use a http page inside the iframe.
        </p>
        <p class="<?php if (strpos(site_url(), 'https://') !== false) {
            echo 'bold text-success';
        } ?>">2. If the protocol of your installation is https use a https page inside the iframe.
        </p>
        <p>None SSL installation will need to place the link in non ssl eq. landing page and backwards.</p>
        <hr />
        <h4 class="bold">Change form container column (Bootstrap)</h4>
        <p>
            <span class="label label-default">
                <a href="<?= site_url('forms/ticket?col=col-md-8'); ?>"
                    target="_blank">
                    <?= site_url('forms/ticket?col=col-md-8'); ?>
                </a>
            </span>
        </p>
        <p>
            <span class="label label-default">
                <a href="<?= site_url('forms/ticket?col=col-md-8+col-md-offset-2'); ?>"
                    target="_blank"><?= site_url('forms/ticket?col=col-md-8+col-md-offset-2'); ?></a>
            </span>
        </p>
        <p>
            <span class="label label-default">
                <a href="<?= site_url('forms/ticket?col=col-md-5'); ?>"
                    target="_blank">
                    <?= site_url('forms/ticket?col=col-md-5'); ?>
                </a>
            </span>
        </p>
        <hr />
        <h4 class="bold">Specifiy language</h4>
        <p>
            <span class="label label-default">
                <a href="<?= site_url('forms/ticket?language=english'); ?>"
                    target="_blank">
                    <?= site_url('forms/ticket?language=english'); ?>
                </a>
            </span>
    </div>
</div>