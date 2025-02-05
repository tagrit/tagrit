<div class="row">
    <?php echo form_open('admin/imprest/settings/set_event_mandatory_fields', ['id' => 'set-event-mandatory-fields']); ?>
    <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-body">
                <div>
                    <p style="display: block; font-size:16px;  font-weight: bold; margin-bottom: 10px;">
                        Mandatory Fields
                    </p>
                    <div style="margin-bottom: 10px;">
                        <input type="checkbox"
                               name="mandatory_fields[]"
                               id="venue"
                               value="venue"
                               style="width:20px; height:20px; vertical-align: top;"
                            <?php echo in_array('venue', $mandatory_fields ?? []) ? 'checked' : ''; ?>>
                        <label for="venue"
                               style="margin-top:5px; font-size:16px; vertical-align: middle;">Venue</label>
                    </div>

                    <div style="margin-bottom: 10px;">
                        <input type="checkbox"
                               name="mandatory_fields[]"
                               id="organization"
                               value="organization"
                               style="width:20px; height:20px; vertical-align: top;"
                            <?php echo in_array('organization', $mandatory_fields ?? []) ? 'checked' : ''; ?>>
                        <label for="venue"
                               style="margin-top:5px; font-size:16px; vertical-align: middle;">Organization</label>
                    </div>

                    <div style="margin-bottom: 10px;">
                        <input type="checkbox"
                               name="mandatory_fields[]"
                               id="dates"
                               value="dates"
                               style="width:20px; height:20px; vertical-align: top;"
                            <?php echo in_array('dates', $mandatory_fields ?? []) ? 'checked' : ''; ?>>
                        <label for="dates"
                               style="margin-top:5px; font-size:16px; vertical-align: middle;">Start and End
                            Dates</label>
                    </div>

                    <div style="margin-bottom: 10px;">
                        <input type="checkbox"
                               name="mandatory_fields[]"
                               id="delegates_details"
                               value="delegates_details"
                               style="width:20px; height:20px; vertical-align: top;"
                            <?php echo in_array('delegates_details', $mandatory_fields ?? []) ? 'checked' : ''; ?>>
                        <label for="delegates_details"
                               style="margin-top:5px; font-size:16px; vertical-align: bottom;">Number and Charges Per
                            Delegate</label>
                    </div>

                    <div style="margin-bottom: 10px;">
                        <input type="checkbox"
                               name="mandatory_fields[]"
                               id="trainers"
                               value="trainers"
                               style="width:20px; height:20px; vertical-align: top;"
                            <?php echo in_array('trainers', $mandatory_fields ?? []) ? 'checked' : ''; ?>>
                        <label for="trainers"
                               style="margin-top:5px; font-size:16px; vertical-align: bottom;">Trainers</label>
                    </div>

                    <div style="margin-bottom: 10px;">
                        <input type="checkbox"
                               name="mandatory_fields[]"
                               id="facilitator"
                               value="facilitator"
                               style="width:20px; height:20px; vertical-align: top;"
                            <?php echo in_array('facilitator', $mandatory_fields ?? []) ? 'checked' : ''; ?>>
                        <label for="trainers"
                               style="margin-top:5px; font-size:16px; vertical-align: bottom;">Facilitator</label>
                    </div>

                    <div style="margin-bottom: 10px;">
                        <input type="checkbox"
                               name="mandatory_fields[]"
                               id="revenue"
                               value="revenue"
                               style="width:20px; height:20px; vertical-align: top;"
                            <?php echo in_array('revenue', $mandatory_fields ?? []) ? 'checked' : ''; ?>>
                        <label for="revenue"
                               style="margin-top:5px; font-size:16px; vertical-align: bottom;">Revenue</label>
                    </div>
                </div>
                <button style="margin-top:20px;" type="submit" class="btn btn-success">Save Changes</button>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
