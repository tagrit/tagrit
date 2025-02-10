<!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
<input type="text" class="fake-autofill-field" name="fakeusernameremembered" value='' tabindex="-1" />
<input type="password" class="fake-autofill-field" name="fakepasswordremembered" value='' tabindex="-1" />
<h4 class="no-margin"><?php echo _l('settings_smtp_settings_heading'); ?></h4>
<hr />
<?php echo form_open(admin_url('ma/save_smtp_setting')); ?>
<div class="form-group">
	<label for="mail_engine"><?php echo _l('ma_unsubscribe'); ?></label><br />
	<div class="radio radio-inline radio-primary">
		<input type="radio" name="settings[ma_unsubscribe]" id="settings_yes" value="1" <?php if(get_option('ma_unsubscribe') == '1'){echo 'checked';} ?>>
		<label for="settings_yes"><?php echo _l('settings_yes'); ?></label>
	</div>

	<div class="radio radio-inline radio-primary">
		<input type="radio" name="settings[ma_unsubscribe]" id="settings_no" value="0" <?php if(get_option('ma_unsubscribe') != '1'){echo 'checked';} ?>>
		<label for="settings_no"><?php echo _l('settings_no'); ?></label>
	</div>
</div>
<div class="div_unsubscribe <?php if(get_option('ma_unsubscribe') != '1'){echo 'hide';} ?>">
<?php echo render_input('settings[ma_unsubscribe_text]','unsubscribe_text',get_option('ma_unsubscribe_text')); ?>
</div>
<div class="form-group">
	<label for="mail_engine"><?php echo _l('ma_smtp_type'); ?></label><br />
	<div class="radio radio-inline radio-primary">
		<input type="radio" name="settings[ma_smtp_type]" id="system_default_smtp" value="system_default_smtp" <?php if(get_option('ma_smtp_type') == 'system_default_smtp'){echo 'checked';} ?>>
		<label for="system_default_smtp"><?php echo _l('system_default_smtp'); ?></label>
	</div>

	<div class="radio radio-inline radio-primary">
		<input type="radio" name="settings[ma_smtp_type]" id="other_smtp" value="other_smtp" <?php if(get_option('ma_smtp_type') == 'other_smtp'){echo 'checked';} ?>>
		<label for="other_smtp"><?php echo _l('other_smtp'); ?></label>
	</div>
</div>
<div class="div_other_smtp <?php if(get_option('ma_smtp_type') == 'system_default_smtp'){echo 'hide';} ?>">
<div class="form-group">
	<hr />
	<label for="mail_engine"><?php echo _l('mail_engine'); ?></label><br />
	<div class="radio radio-inline radio-primary">
		<input type="radio" name="settings[ma_mail_engine]" id="phpmailer" value="phpmailer" <?php if(get_option('ma_mail_engine') == 'phpmailer'){echo 'checked';} ?>>
		<label for="phpmailer">PHPMailer</label>
	</div>

	<div class="radio radio-inline radio-primary">
		<input type="radio" name="settings[ma_mail_engine]" id="codeigniter" value="codeigniter" <?php if(get_option('ma_mail_engine') == 'codeigniter'){echo 'checked';} ?>>
		<label for="codeigniter">CodeIgniter</label>
	</div>
	<hr />
	<?php if(get_option('ma_email_protocol') == 'mail'){ ?>
		<div class="alert alert-warning">
			The "mail" protocol is not the recommended protocol to send emails, you should strongly consider configuring the "SMTP" protocol to avoid any distruptions and delivery issues.
		</div>
	<?php } ?>
	<label for="email_protocol"><?php echo _l('email_protocol'); ?></label><br />
	<div class="radio radio-inline radio-primary">
		<input type="radio" name="settings[ma_email_protocol]" id="smtp" value="smtp" <?php if(get_option('ma_email_protocol') == 'smtp'){echo 'checked';} ?>>
		<label for="smtp">SMTP</label>
	</div>

	<div class="radio radio-inline radio-primary">
		<input type="radio" name="settings[ma_email_protocol]" id="sendmail" value="sendmail" <?php if(get_option('ma_email_protocol') == 'sendmail'){echo 'checked';} ?>>
		<label for="sendmail">Sendmail</label>
	</div>

	<div class="radio radio-inline radio-primary">
		<input type="radio" name="settings[ma_email_protocol]" id="mail" value="mail" <?php if(get_option('ma_email_protocol') == 'mail'){echo 'checked';} ?>>
		<label for="mail">Mail</label>
	</div>
</div>
<div class="smtp-fields<?php if(get_option('ma_email_protocol') == 'mail'){echo ' hide'; } ?>">
<div class="form-group mtop15">
		<label for="smtp_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
		<select name="settings[ma_smtp_encryption]" class="selectpicker" data-width="100%">
			<option value="" <?php if(get_option('ma_smtp_encryption') == ''){echo 'selected';} ?>><?php echo _l('smtp_encryption_none'); ?></option>
			<option value="ssl" <?php if(get_option('ma_smtp_encryption') == 'ssl'){echo 'selected';} ?>>SSL</option>
			<option value="tls" <?php if(get_option('ma_smtp_encryption') == 'tls'){echo 'selected';} ?>>TLS</option>
		</select>
	</div>
<?php echo render_input('settings[ma_smtp_host]','settings_email_host',get_option('ma_smtp_host')); ?>
<?php echo render_input('settings[ma_smtp_port]','settings_email_port',get_option('ma_smtp_port')); ?>
</div>
<?php echo render_input('settings[ma_smtp_email]','settings_email',get_option('ma_smtp_email')); ?>
<div class="smtp-fields<?php if(get_option('ma_email_protocol') == 'mail'){echo ' hide'; } ?>">
<i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('smtp_username_help'); ?>"></i>
<?php echo render_input('settings[ma_smtp_username]','smtp_username',get_option('ma_smtp_username')); ?>
<?php
$ps = get_option('ma_smtp_password');
if(!empty($ps)){
	if(false == $this->encryption->decrypt($ps)){
		$ps = $ps;
	} else {
		$ps = $this->encryption->decrypt($ps);
	}
}
echo render_input('settings[ma_smtp_password]','settings_email_password',$ps,'password',array('autocomplete'=>'off')); ?>
</div>
<?php echo render_input('settings[ma_smtp_email_charset]','settings_email_charset',get_option('ma_smtp_email_charset')); ?>
<?php echo render_input('settings[ma_bcc_emails]','bcc_all_emails',get_option('ma_bcc_emails')); ?>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
</div>
<?php echo form_close(); ?>
<div class="div_test_email <?php if(get_option('ma_smtp_type') == 'system_default_smtp'){echo 'hide';} ?>">
<hr />
<h4><?php echo _l('settings_send_test_email_heading'); ?></h4>
<p class="text-muted"><?php echo _l('settings_send_test_email_subheading'); ?></p>
<div class="form-group">
	<div class="input-group">
		<input type="email" class="form-control" name="test_email" data-ays-ignore="true" placeholder="<?php echo _l('settings_send_test_email_string'); ?>">
		<div class="input-group-btn">
			<button type="button" class="btn btn-default ma_test_email p7">Test</button>
		</div>
	</div>
</div>
</div>
