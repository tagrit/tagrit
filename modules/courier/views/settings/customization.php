<div class="row">
    <?php echo form_open('admin/courier/settings/dimensional_factor', ['id' => 'set-dimensional-factor-form']); ?>
    <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-body">
                <div class="mb-3">
                    <label for="formFile" class="form-label">Domestic/Courier Dimensional Factor</label>
                    <input class="form-control" value="<?php echo $dimensional_factor[0]->value; ?>" name="default"
                           type="number" id="formFile">
                </div>
                <div style="margin-top:20px;" class="mb-3">
                    <label for="formFile" class="form-label">Air Consolidation Dimensional Factor</label>
                    <input class="form-control" value="<?php echo $dimensional_factor[1]->value; ?>"
                           name="air_consolidation" type="number" id="formFile">
                </div>
                <div style="margin-top:20px;" class="mb-3">
                    <label for="formFile" class="form-label">Air Freight Dimensional Factor</label>
                    <input class="form-control" value="<?php echo $dimensional_factor[2]->value; ?>" name="air_freight"
                           type="number" id="formFile">
                </div>
                <div style="margin-top:20px;" class="mb-3">
                    <label for="formFile" class="form-label">Sea LCL Dimensional Factor</label>
                    <input class="form-control" value="<?php echo $dimensional_factor[3]->value; ?>" name="sea_lcl"
                           type="number" id="formFile">
                </div>
                <button style="margin-top:20px;" type="submit"
                        class="btn btn-success">Save
                </button>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>
</div
