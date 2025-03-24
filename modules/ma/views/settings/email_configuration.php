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
<div class="modal-footer">
    <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
</div>
<?php echo form_close(); ?>
<div class="div_other_smtp <?php if(get_option('ma_smtp_type') == 'system_default_smtp'){echo 'hide';} ?>">
	<a class="btn btn-primary add_smtp_config" href="javascript:void(0);"><?php echo _l('add_smtp_config'); ?></a>
    <div class="horizontal-scrollable-tabs preview-tabs-top mtop25">
      <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
        <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
        <div class="horizontal-tabs">
          <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
            <?php foreach($smtp_configs as $key => $config){ ?>
              <li role="presentation" class="<?php echo ($key == 0 ? 'active' : '') ?>">
                 <a href="#tab_<?php echo html_entity_decode($config['id']) ?>" aria-controls="tab_<?php echo html_entity_decode($config['id']) ?>" role="tab" id="tab_out_of_stock" data-toggle="tab">
                    <?php if($config['is_default'] == 1){ ?>
                 			<span class="req text-danger">* </span>
                    <?php } ?>
                    <?php echo ucfirst($config['name']) ?>
                 </a>
              </li>
            <?php } ?>
          </ul>
          </div>
      </div>
      <div class="tab-content mtop15">
          <?php foreach($smtp_configs as $key => $config){ ?>
            <div role="tabpanel" class="tab-pane <?php echo ($key == 0 ? 'active' : '') ?>" id="tab_<?php echo html_entity_decode($config['id']) ?>">
					<?php echo form_open(admin_url('ma/save_smtp_config/'.$config['id'])); ?>

            <?php 
            $config_option = json_decode($config['configs'] ?? '', true);
            $ma_mail_engine = isset($config_option['ma_mail_engine']) ? $config_option['ma_mail_engine'] : '';

            $ma_email_protocol = isset($config_option['ma_email_protocol']) ? $config_option['ma_email_protocol'] : '';
            $ma_smtp_encryption = isset($config_option['ma_smtp_encryption']) ? $config_option['ma_smtp_encryption'] : '';
            $ma_smtp_host = isset($config_option['ma_smtp_host']) ? $config_option['ma_smtp_host'] : '';
            $ma_smtp_port = isset($config_option['ma_smtp_port']) ? $config_option['ma_smtp_port'] : '';
            $ma_smtp_email = isset($config_option['ma_smtp_email']) ? $config_option['ma_smtp_email'] : '';

            $ma_smtp_username = isset($config_option['ma_smtp_username']) ? $config_option['ma_smtp_username'] : '';
            $ma_smtp_password = isset($config_option['ma_smtp_password']) ? $config_option['ma_smtp_password'] : '';
            $ma_smtp_email_charset = isset($config_option['ma_smtp_email_charset']) ? $config_option['ma_smtp_email_charset'] : '';
            $ma_bcc_emails = isset($config_option['ma_bcc_emails']) ? $config_option['ma_bcc_emails'] : '';
            $ma_smtp_type = isset($config_option['ma_smtp_type']) ? $config_option['ma_smtp_type'] : '';

            echo form_hidden('id', $config['id']);
            echo render_input('name', 'name', $config['name']); ?>


				<div class="form-group">
					<label for="mail_engine"><?php echo _l('mail_engine'); ?></label><br />
					<div class="radio radio-inline radio-primary">
						<input type="radio" name="ma_mail_engine" id="phpmailer" value="phpmailer" <?php if($ma_mail_engine == 'phpmailer'){echo 'checked';} ?>>
						<label for="phpmailer">PHPMailer</label>
					</div>

					<div class=" radio radio-inline radio-primary mtop15">
						<input type="radio" name="ma_mail_engine" id="codeigniter" value="codeigniter" <?php if($ma_mail_engine == 'codeigniter'){echo 'checked';} ?>>
						<label for="codeigniter">CodeIgniter</label>
					</div>
					<hr />

					<?php if($ma_email_protocol == 'mail'){ ?>
						<div class="alert alert-warning">
							The "mail" protocol is not the recommended protocol to send emails, you should strongly consider configuring the "SMTP" protocol to avoid any distruptions and delivery issues.
						</div>
					<?php } ?>
					<label for="email_protocol"><?php echo _l('email_protocol'); ?></label><br />
					<div class="radio radio-inline radio-primary">
						<input type="radio" name="ma_email_protocol" id="smtp" value="smtp" <?php if($ma_email_protocol == 'smtp'){echo 'checked';} ?>>
						<label for="smtp">SMTP</label>
					</div>

					<div class="radio radio-inline radio-primary">
						<input type="radio" name="ma_email_protocol" id="sendmail" value="sendmail" <?php if($ma_email_protocol == 'sendmail'){echo 'checked';} ?>>
						<label for="sendmail">Sendmail</label>
					</div>

					<div class="radio radio-inline radio-primary">
						<input type="radio" name="ma_email_protocol" id="mail" value="mail" <?php if($ma_email_protocol == 'mail'){echo 'checked';} ?>>
						<label for="mail">Mail</label>
					</div>
				</div>
				<div class="smtp-fields<?php if($ma_email_protocol == 'mail'){echo ' hide'; } ?>">
				<div class="form-group mtop15">
						<label for="smtp_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
						<select name="ma_smtp_encryption" class="selectpicker" data-width="100%">
							<option value="" <?php if($ma_smtp_encryption == ''){echo 'selected';} ?>><?php echo _l('smtp_encryption_none'); ?></option>
							<option value="ssl" <?php if($ma_smtp_encryption == 'ssl'){echo 'selected';} ?>>SSL</option>
							<option value="tls" <?php if($ma_smtp_encryption == 'tls'){echo 'selected';} ?>>TLS</option>
						</select>
					</div>
				<?php echo render_input('ma_smtp_host','settings_email_host',$ma_smtp_host); ?>
				<?php echo render_input('ma_smtp_port','settings_email_port',$ma_smtp_port); ?>
				</div>
				<?php echo render_input('ma_smtp_email','settings_email',$ma_smtp_email); ?>
				<div class="smtp-fields<?php if($ma_email_protocol == 'mail'){echo ' hide'; } ?>">
				<i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('smtp_username_help'); ?>"></i>
				<?php echo render_input('ma_smtp_username','smtp_username',$ma_smtp_username); ?>
				<?php
				$ps = $ma_smtp_password;
				
				echo render_input('ma_smtp_password','settings_email_password',$ps,'password',array('autocomplete'=>'off')); ?>
				</div>
				<?php echo render_input('ma_smtp_email_charset','settings_email_charset',$ma_smtp_email_charset); ?>
				<?php echo render_input('ma_bcc_emails','bcc_all_emails',$ma_bcc_emails); ?>
               
						<div class="modal-footer">
						    <a href="<?php echo admin_url('ma/delete_smtp_config/'.$config['id']) ?>" class="btn btn-danger _delete" ><?php echo _l('delete'); ?></a>
						    <a href="<?php echo admin_url('ma/set_smtp_config_default/'.$config['id']) ?>" class="btn btn-success" ><?php echo _l('set_default'); ?></a>
						    <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
						</div>
						<?php echo form_close(); ?>
						<div class="div_test_email <?php if($ma_smtp_type == 'system_default_smtp'){echo 'hide';} ?>">
						<hr />
						<h4><?php echo _l('settings_send_test_email_heading'); ?></h4>
						<p class="text-muted"><?php echo _l('settings_send_test_email_subheading'); ?></p>
						<div class="form-group">
							<div class="input-group">
								<input type="email" class="form-control" name="test_email_<?php echo html_entity_decode($config['id']); ?>" data-ays-ignore="true" placeholder="<?php echo _l('settings_send_test_email_string'); ?>">
								<div class="input-group-btn">
									<a type="button" class="btn btn-default p7" onclick="ma_test_email(<?php echo html_entity_decode($config['id']); ?>)">Test</a>
								</div>
							</div>
						</div>
						</div>
            </div>

          <?php } ?>
      </div>
</div>

<div class="modal fade" id="smtp-config-modal">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?php echo _l('smtp_config')?></h4>
         </div>

         <?php echo form_open_multipart(admin_url('ma/add_smtp_config'),array('id'=>'smtp-config-form'));?>
         <div class="modal-body">
            <?php echo render_input('name', 'name'); ?>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-primary btn-submit"><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>  
      </div>
   </div>
</div>