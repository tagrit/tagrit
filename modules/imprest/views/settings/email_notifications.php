<div class="row">
    <?php echo form_open('admin/imprest/settings/set_email_notification_statuses', ['id' => 'set-email-notification-statuses']); ?>
    <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-body">
                <div>
                    <p style="display: block; font-size:16px;  font-weight: bold; margin-bottom: 10px;">
                        Email Notification Preferences
                    </p>
                    <div style="margin-bottom: 10px;">
                        <input type="checkbox"
                               name="notification_statuses[]"
                               id="funds_requested"
                               value="Funds Requested"
                               style="width:20px; height:20px; vertical-align: top;"
                            <?php echo in_array('Funds Requested', $notification_statuses ?? []) ? 'checked' : ''; ?>>
                           <label for="approved"
                               style="margin-top:5px; font-size:16px; vertical-align: middle;">Funds Requested (Sent to
                            Admin)</label>
                    </div>

                    <div style="margin-bottom: 10px;">
                        <input type="checkbox"
                               name="notification_statuses[]"
                               id="approved"
                               value="Approved"
                               style="width:20px; height:20px; vertical-align: top;"
                            <?php echo in_array('Approved', $notification_statuses ?? []) ? 'checked' : ''; ?>>
                        <label for="approved"
                               style="margin-top:5px; font-size:16px; vertical-align: middle;">Approved (Sent to
                            Staff)</label>
                    </div>

                    <div style="margin-bottom: 10px;">
                        <input type="checkbox"
                               name="notification_statuses[]"
                               id="rejected"
                               value="Rejected"
                               style="width:20px; height:20px; vertical-align: top;"
                            <?php echo in_array('Rejected', $notification_statuses ?? []) ? 'checked' : ''; ?>>
                        <label for="rejected"
                               style="margin-top:5px; font-size:16px; vertical-align: middle;">Rejected (Sent to
                            Staff)</label>
                    </div>

                    <div style="margin-bottom: 10px;">
                        <input type="checkbox"
                               name="notification_statuses[]"
                               id="reconciliation_rejected"
                               value="Reconciliation Rejected"
                               style="width:20px; height:20px; vertical-align: top;"
                            <?php echo in_array('Reconciliation Rejected', $notification_statuses ?? []) ? 'checked' : ''; ?>>
                        <label for="reconciliation_rejected"
                               style="margin-top:5px; font-size:16px; vertical-align: middle;">Reconciliation
                            Rejected (Sent to Staff)</label>
                    </div>

                    <div style="margin-bottom: 10px;">
                        <input type="checkbox"
                               name="notification_statuses[]"
                               id="cleared"
                               value="Cleared"
                               style="width:20px; height:20px; vertical-align: top;"
                            <?php echo in_array('Cleared', $notification_statuses ?? []) ? 'checked' : ''; ?>>
                        <label for="cleared"
                               style="margin-top:5px; font-size:16px; vertical-align: bottom;">Cleared (Sent to
                            Staff)</label>
                    </div>
                </div>
                <button style="margin-top:20px;" type="submit" class="btn btn-success">Save Changes</button>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
