<?php echo form_open(admin_url('ma/save_gereral_setting')); ?>
<div class="form-group">
	<label for="mail_engine"><?php echo _l('ma_lead_required_phone'); ?></label><br />
	<div class="radio radio-inline radio-primary">
		<input type="radio" name="ma_lead_required_phone" id="yes" value="1" <?php if(get_option('ma_lead_required_phone') == '1'){echo 'checked';} ?>>
		<label for="yes"><?php echo _l('settings_yes'); ?></label>
	</div>

	<div class="radio radio-inline radio-primary">
		<input type="radio" name="ma_lead_required_phone" id="no" value="0" <?php if(get_option('ma_lead_required_phone') != '1'){echo 'checked';} ?>>
		<label for="no"><?php echo _l('settings_no'); ?></label>
	</div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
</div>
<?php echo form_close(); ?>