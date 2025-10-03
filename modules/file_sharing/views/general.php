<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php 
	$fs_permisstion_staff_view = get_option('fs_permisstion_staff_view');
	$fs_permisstion_staff_upload_and_override = get_option('fs_permisstion_staff_upload_and_override');
	$fs_permisstion_staff_delete = get_option('fs_permisstion_staff_delete');
	$fs_permisstion_staff_upload = get_option('fs_permisstion_staff_upload');
	$fs_permisstion_staff_download = get_option('fs_permisstion_staff_download');
	$fs_permisstion_staff_share = get_option('fs_permisstion_staff_share');
	$fs_permisstion_staff_share_to_client = get_option('fs_permisstion_staff_share_to_client');

	$fs_permisstion_client_view = get_option('fs_permisstion_client_view');
	$fs_permisstion_client_upload_and_override = get_option('fs_permisstion_client_upload_and_override');
	$fs_permisstion_client_delete = get_option('fs_permisstion_client_delete');
	$fs_permisstion_client_upload = get_option('fs_permisstion_client_upload');
	$fs_permisstion_client_download = get_option('fs_permisstion_client_download');

	$fs_global_notification = get_option('fs_global_notification');
	$fs_client_visible = get_option('fs_client_visible');
	$fs_global_email = get_option('fs_global_email');
	$fs_global_max_size = get_option('fs_global_max_size');
	$fs_global_extension = get_option('fs_global_extension');
	$fs_global_amount_expiration = get_option('fs_global_amount_expiration');

	$fs_the_administrator_of_the_public_folder = get_option('fs_the_administrator_of_the_public_folder');
	$fs_allow_file_editing = get_option('fs_allow_file_editing');
	$fs_hidden_files = get_option('fs_hidden_files');
 ?>
<?php echo form_open(admin_url('file_sharing/update_setting'),array('id'=>'general-settings-form')); ?>
<div class="row">
	<div class="col-md-6">
		<?php echo render_input('fs_global_extension', 'allowed_extension', $fs_global_extension); ?>
	</div>
	<div class="col-md-6">
		<div class="form-group">
	       	<label for="fs_global_max_size"><?php echo _l('fs_max_size_per_upload'); ?></label>
	       	<div class="input-group">
	          <input type="number" name="fs_global_max_size" class="form-control" value="<?php echo html_entity_decode($fs_global_max_size); ?>" data-isedit="false" data-original-number="false" aria-invalid="false">
	            <span class="input-group-addon">
	             <?php echo _l('fs_mb'); ?>
	          	</span>
	        </div>
	    </div>
	</div>

	<div class="col-md-6">
		<?php echo render_input('fs_global_amount_expiration', _l('maximum_number_of_storage_months') .' <i class="fa fa-question-circle" data-toggle="tooltip" data-title="'. _l('maximum_number_of_storage_months_note').'"></i> ', $fs_global_amount_expiration); ?>
	</div>
	<div class="col-md-6">
		<?php $fs_the_administrator_of_the_public_folder = explode(',', $fs_the_administrator_of_the_public_folder); ?>
        <?php echo render_select('fs_the_administrator_of_the_public_folder[]',$staffs,array('staffid',array('firstname', 'lastname')),'the_administrator_of_the_public_folder', $fs_the_administrator_of_the_public_folder, array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
	</div>

	<div class="col-md-6">
		<?php echo render_input('fs_hidden_files', 'fs_hidden_files', $fs_hidden_files); ?>
	</div>
	<div class="col-md-12">
		<div class="col-md-6 row">
            <div class="row">
                <div class="col-md-6 mtop10 border-right">
                    <span><?php echo _l('allow_file_editing'); ?></span>
                </div>
                <div class="col-md-6 mtop10">
                    <div class="onoffswitch">
                        <input type="checkbox" id="fs_allow_file_editing" data-perm-id="3" class="onoffswitch-checkbox" <?php if($fs_allow_file_editing == '1'){echo 'checked';} ?>  value="1" name="fs_allow_file_editing">
                        <label class="onoffswitch-label" for="fs_allow_file_editing"></label>
                    </div>
                </div>
            </div>
        </div>
	</div>
	

	<div class="col-md-12">
        <hr />
		<div class="col-md-6 row">
			<h5 class="title mbot5"><?php echo _l('fs_permisstion_of_staff') ?></h5>
		    <div class="row">
		        <div class="col-md-6 mtop10 border-right">
		            <span><?php echo _l('view'); ?></span>
		        </div>
		        <div class="col-md-6 mtop10">
		            <div class="onoffswitch">
		                <input type="checkbox" id="fs_permisstion_staff_view" data-perm-id="5" class="onoffswitch-checkbox" <?php if($fs_permisstion_staff_view == '1'){echo 'checked';} ?>  value="1" name="fs_permisstion_staff_view">
		                <label class="onoffswitch-label" for="fs_permisstion_staff_view"></label>
		            </div>
		        </div>
		        <div class="col-md-6 mtop10 border-right">
		            <span><?php echo _l('upload_and_override'); ?></span>
		        </div>
		        <div class="col-md-6 mtop10">
		            <div class="onoffswitch">
		                <input type="checkbox" id="fs_permisstion_staff_upload_and_override" data-perm-id="6" class="onoffswitch-checkbox" <?php if($fs_permisstion_staff_upload_and_override == '1'){echo 'checked';} ?>  value="1" name="fs_permisstion_staff_upload_and_override">
		                <label class="onoffswitch-label" for="fs_permisstion_staff_upload_and_override"></label>
		            </div>
		        </div>
		        <div class="col-md-6 mtop10 border-right">
		            <span><?php echo _l('delete'); ?></span>
		        </div>
		        <div class="col-md-6 mtop10">
		            <div class="onoffswitch">
		                <input type="checkbox" id="fs_permisstion_staff_delete" data-perm-id="5" class="onoffswitch-checkbox" <?php if($fs_permisstion_staff_delete == '1'){echo 'checked';} ?>  value="1" name="fs_permisstion_staff_delete">
		                <label class="onoffswitch-label" for="fs_permisstion_staff_delete"></label>
		            </div>
		        </div>
		        <div class="col-md-6 mtop10 border-right">
		            <span><?php echo _l('fs_is_upload'); ?></span>
		        </div>
		        <div class="col-md-6 mtop10">
		            <div class="onoffswitch">
		                <input type="checkbox" id="fs_permisstion_staff_upload" data-perm-id="6" class="onoffswitch-checkbox" <?php if($fs_permisstion_staff_upload == '1'){echo 'checked';} ?>  value="1" name="fs_permisstion_staff_upload">
		                <label class="onoffswitch-label" for="fs_permisstion_staff_upload"></label>
		            </div>
		        </div>
		        <div class="col-md-6 mtop10 border-right">
		            <span><?php echo _l('download'); ?></span>
		        </div>
		        <div class="col-md-6 mtop10">
		            <div class="onoffswitch">
		                <input type="checkbox" id="fs_permisstion_staff_download" data-perm-id="5" class="onoffswitch-checkbox" <?php if($fs_permisstion_staff_download == '1'){echo 'checked';} ?>  value="1" name="fs_permisstion_staff_download">
		                <label class="onoffswitch-label" for="fs_permisstion_staff_download"></label>
		            </div>
		        </div>
		        <div class="col-md-6 mtop10 border-right">
		            <span><?php echo _l('fs_share'); ?></span> <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('only_apply_to_staff'); ?>"></i>
		        </div>
		        <div class="col-md-6 mtop10">
		            <div class="onoffswitch">
		                <input type="checkbox" id="fs_permisstion_staff_share" data-perm-id="5" class="onoffswitch-checkbox" <?php if($fs_permisstion_staff_share == '1'){echo 'checked';} ?>  value="1" name="fs_permisstion_staff_share">
		                <label class="onoffswitch-label" for="fs_permisstion_staff_share"></label>
		            </div>
		        </div>
		        <div class="col-md-6 mtop10 border-right">
		            <span><?php echo _l('fs_share_to_client'); ?></span> <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('only_apply_to_staff'); ?>"></i>
		        </div>
		        <div class="col-md-6 mtop10">
		            <div class="onoffswitch">
		                <input type="checkbox" id="fs_permisstion_staff_share_to_client" data-perm-id="5" class="onoffswitch-checkbox" <?php if($fs_permisstion_staff_share_to_client == '1'){echo 'checked';} ?>  value="1" name="fs_permisstion_staff_share_to_client">
		                <label class="onoffswitch-label" for="fs_permisstion_staff_share_to_client"></label>
		            </div>
		        </div>
		    </div>
		</div>
		<div class="col-md-6 row">
				<h5 class="title mbot5"><?php echo _l('fs_permisstion_of_client') ?></h5>
		    <div class="row">
		        <div class="col-md-6 mtop10 border-right">
		            <span><?php echo _l('view'); ?></span>
		        </div>
		        <div class="col-md-6 mtop10">
		            <div class="onoffswitch">
		                <input type="checkbox" id="fs_permisstion_client_view" data-perm-id="5" class="onoffswitch-checkbox" <?php if($fs_permisstion_client_view == '1'){echo 'checked';} ?>  value="1" name="fs_permisstion_client_view">
		                <label class="onoffswitch-label" for="fs_permisstion_client_view"></label>
		            </div>
		        </div>
		        <div class="col-md-6 mtop10 border-right">
		            <span><?php echo _l('upload_and_override'); ?></span>
		        </div>
		        <div class="col-md-6 mtop10">
		            <div class="onoffswitch">
		                <input type="checkbox" id="fs_permisstion_client_upload_and_override" data-perm-id="6" class="onoffswitch-checkbox" <?php if($fs_permisstion_client_upload_and_override == '1'){echo 'checked';} ?>  value="1" name="fs_permisstion_client_upload_and_override">
		                <label class="onoffswitch-label" for="fs_permisstion_client_upload_and_override"></label>
		            </div>
		        </div>
		        <div class="col-md-6 mtop10 border-right">
		            <span><?php echo _l('delete'); ?></span>
		        </div>
		        <div class="col-md-6 mtop10">
		            <div class="onoffswitch">
		                <input type="checkbox" id="fs_permisstion_client_delete" data-perm-id="5" class="onoffswitch-checkbox" <?php if($fs_permisstion_client_delete == '1'){echo 'checked';} ?>  value="1" name="fs_permisstion_client_delete">
		                <label class="onoffswitch-label" for="fs_permisstion_client_delete"></label>
		            </div>
		        </div>
		        <div class="col-md-6 mtop10 border-right">
		            <span><?php echo _l('fs_is_upload'); ?></span>
		        </div>
		        <div class="col-md-6 mtop10">
		            <div class="onoffswitch">
		                <input type="checkbox" id="fs_permisstion_client_upload" data-perm-id="6" class="onoffswitch-checkbox" <?php if($fs_permisstion_client_upload == '1'){echo 'checked';} ?>  value="1" name="fs_permisstion_client_upload">
		                <label class="onoffswitch-label" for="fs_permisstion_client_upload"></label>
		            </div>
		        </div>
		        <div class="col-md-6 mtop10 border-right">
		            <span><?php echo _l('download'); ?></span>
		        </div>
		        <div class="col-md-6 mtop10">
		            <div class="onoffswitch">
		                <input type="checkbox" id="fs_permisstion_client_download" data-perm-id="5" class="onoffswitch-checkbox" <?php if($fs_permisstion_client_download == '1'){echo 'checked';} ?>  value="1" name="fs_permisstion_client_download">
		                <label class="onoffswitch-label" for="fs_permisstion_client_download"></label>
		            </div>
		        </div>
		    </div>
		</div>
    </div>
	<div class="col-md-12">
        <hr />
		<h5><?php echo _l('event_notification') ?></h5>
		<div class="col-md-6 row">
            <div class="row">
                <div class="col-md-6 mtop10 border-right">
                    <span><?php echo _l('fs_is_notification'); ?></span>
                </div>
                <div class="col-md-6 mtop10">
                    <div class="onoffswitch">
                        <input type="checkbox" id="fs_global_notification" data-perm-id="3" class="onoffswitch-checkbox" <?php if($fs_global_notification == '1'){echo 'checked';} ?>  value="1" name="fs_global_notification">
                        <label class="onoffswitch-label" for="fs_global_notification"></label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mtop10 border-right">
                    <span><?php echo _l('fs_is_email'); ?></span>
                </div>
                <div class="col-md-6 mtop10">
                    <div class="onoffswitch">
                        <input type="checkbox" id="fs_global_email" data-perm-id="3" class="onoffswitch-checkbox" <?php if($fs_global_email == '1'){echo 'checked';} ?>  value="1" name="fs_global_email">
                        <label class="onoffswitch-label" for="fs_global_email"></label>
                    </div>
                </div>
            </div>
        </div>
	</div>
	<div class="col-md-12">
        <hr />
		<h5><?php echo _l('project_discussion_visible_to_customer_yes') ?></h5>
		<div class="col-md-6 row">
            <div class="row">
                <div class="col-md-6 mtop10 border-right">
                    <span><?php echo _l('task_visible_to_client'); ?></span>
                </div>
                <div class="col-md-6 mtop10">
                    <div class="onoffswitch">
                        <input type="checkbox" id="fs_client_visible" data-perm-id="3" class="onoffswitch-checkbox" <?php if($fs_client_visible == '1'){echo 'checked';} ?>  value="1" name="fs_client_visible">
                        <label class="onoffswitch-label" for="fs_client_visible"></label>
                    </div>
                </div>
            </div>
        </div>
	</div>
	<div class="col-md-12">
		<button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
	</div>
<?php echo form_close(); ?>
<div class="clearfix"></div>