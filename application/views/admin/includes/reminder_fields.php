<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?= form_hidden('rel_id', $id); ?>
<?= form_hidden('rel_type', $name); ?>
<?= render_datetime_input('date', 'set_reminder_date', '', ['data-date-min-date' => _d(date('Y-m-d')), 'data-step' => 30]); ?>
<?= render_select('staff', $members, ['staffid', ['firstname', 'lastname']], 'reminder_set_to', get_staff_user_id(), ['data-current-staff' => get_staff_user_id()]); ?>
<?= render_textarea('description', 'reminder_description'); ?>
<?php if (is_email_template_active('reminder-email-staff')) { ?>
<div class="form-group">
  <div class="checkbox checkbox-primary">
    <input type="checkbox" name="notify_by_email" id="notify_by_email">
    <label
      for="notify_by_email"><?= _l('reminder_notify_me_by_email'); ?></label>
  </div>
</div>
<?php } ?>