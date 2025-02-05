<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<h4 class="customer-profile-group-heading"><?php echo _l('client_reminders_tab'); ?></h4>
<?php if (isset($vehicle)) { ?>
<a href="#" data-toggle="modal" data-target=".reminder-modal-vehicle-<?php echo new_html_entity_decode($vehicle->id); ?>"
    class="btn btn-primary mbot15">
    <i class="fa-regular fa-bell"></i> <?php echo _l('set_reminder'); ?>
</a>
<div class="clearfix"></div>

<?php render_datatable([ _l('reminder_description'), _l('reminder_date'), _l('reminder_staff'), _l('reminder_is_notified')], 'reminders');
$this->load->view('admin/includes/modals/reminder', ['id' => $vehicle->id, 'name' => 'vehicle', 'members' => $members, 'reminder_title' => _l('set_reminder')]);
} ?>
