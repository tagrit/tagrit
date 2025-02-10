<!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
<?php echo form_open(admin_url('ma/save_email_limit_setting')); ?>
<div class="form-group">
	<label for="mail_engine"><?php echo _l('ma_email_sending_limit'); ?></label><br />
	<div class="radio radio-inline radio-primary">
		<input type="radio" name="settings[ma_email_sending_limit]" id="email_sending_limit_yes" value="1" <?php if(get_option('ma_email_sending_limit') == '1'){echo 'checked';} ?>>
		<label for="email_sending_limit_yes"><?php echo _l('settings_yes'); ?></label>
	</div>

	<div class="radio radio-inline radio-primary">
		<input type="radio" name="settings[ma_email_sending_limit]" id="email_sending_limit_no" value="0" <?php if(get_option('ma_email_sending_limit') != '1'){echo 'checked';} ?>>
		<label for="email_sending_limit_no"><?php echo _l('settings_no'); ?></label>
	</div>
</div>
<div class="div_email_sending_limit <?php if(get_option('ma_email_sending_limit') != '1'){echo 'hide';} ?>">
	<div class="row">
		<div class="col-md-4">
			<?php echo render_input('settings[ma_email_limit]','ma_email_limit',get_option('ma_email_limit'), 'number'); ?>
	  	</div>
		<div class="col-md-4">
			<?php echo render_input('settings[ma_email_interval]','ma_email_interval',get_option('ma_email_interval'), 'number'); ?>
	  	</div>
	  	<div class="col-md-4">
	      <?php 
		      $units = [
		         ['id' => 'minutes', 'name' => _l('minutes')],
		         ['id' => 'hours', 'name' => _l('hours')],
		         ['id' => 'day', 'name' => _l('day')],
		         ['id' => 'week', 'name' => _l('week')],
		         ['id' => 'month', 'name' => _l('month')],
		      ];
		   ?>
	   	<?php echo render_select('settings[ma_email_repeat_every]',$units, array('id', 'name'),'ma_repeat_every',get_option('ma_email_repeat_every'), [], [], '', '', false); ?>
	  	</div>
	</div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
</div>

<?php if(get_option('ma_email_sending_limit') == '1'){ ?>
	<table class="table table-email_sending_limit mtop25">
		<thead>
		  <th><?php echo _l('campaign'); ?></th>
		  <th><?php echo _l('ma_progress'); ?></th>
		  <th><?php echo _l('ma_email_stats'); ?></th>
		</thead>
		<tbody>
		</tbody>
	</table>
<?php } ?>
<?php echo form_close(); ?>

