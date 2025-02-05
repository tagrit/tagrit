<div class="row">
    <?php echo form_open('admin/imprest/settings/set_custom_ids', ['id' => 'set-custom-ids']); ?>
    <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-body">
                <div class="mb-3">
                    <label for="staff_custom_id" class="form-label">Staff Custom Field id<span
                                style="color:red;">*</span></label>
                    <input class="form-control"
                           value="<?php echo $staff_custom_id; ?>"
                           name="staff_custom_id"
                           type="number" id="staff_custom_id">

                </div>
                <div style="margin-top:20px;">
                    <label for="event_custom_id" class="form-label">Event Custom Field id<span
                                style="color:red;">*</span></label>
                    <input class="form-control"
                           value="<?php echo $event_custom_id; ?>"
                           name="event_custom_id"
                           type="number"
                           id="event_custom_id">
                </div>
                <button style="margin-top:20px;" type="submit"
                        class="btn btn-success">Save Changes
                </button>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>
</div
