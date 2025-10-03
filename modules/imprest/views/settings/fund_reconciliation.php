<div class="row">
    <?php echo form_open('admin/imprest/settings/set_max_unreconciled_amount', ['id' => 'set-max-unreconciled-amount']); ?>
    <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-body">
                <div class="mb-3">
                    <label for="formFile" class="form-label">Unreconciled Amount Limit<span style="color:red;">*</span></label>
                    <input class="form-control" value="<?php echo $max_unreconciled_amount; ?>"
                           name="max_unreconciled_amount"
                           type="number" id="formFile">
                </div>
                <button style="margin-top:20px;" type="submit"
                        class="btn btn-success">Save Changes
                </button>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>
</div
