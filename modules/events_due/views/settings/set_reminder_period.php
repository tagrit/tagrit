<div class="row">
    <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-body">
                <p style="font-size: 13px; font-weight: bold; margin-bottom: 15px;">
                    Set Reminder Period
                </p>
                <?php echo form_open('admin/events_due/settings/set_reminder_period', [
                    'id' => 'set-reminder-form'
                ]); ?>
                <div style="margin-bottom: 15px;">
                    <label for="reminder_days"
                           style="font-size: 14px; vertical-align: middle; display: block; margin-bottom: 5px;">
                        Set Reminder Days Before Event:
                    </label>
                    <input type="number" name="reminder_days" id="reminder_days"
                           min="1" max="30" value="<?php echo $reminder_days; ?>"
                           required
                           style="width: 100%; padding: 8px; font-size: 16px; border: 1px solid #ccc; border-radius: 5px;">
                </div>
                <button style="margin-top: 20px;" type="submit" class="btn btn-success">Save</button>
                <?php echo form_close(); ?>

            </div>
        </div>
    </div>
</div>