<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
	
<?php hooks()->do_action('fs_public_head'); ?>
<div class="mtop40">
   	<div class="col-md-4 col-md-offset-4 col-sm-8 col-sm-offset-2">
      	<div class="panel_s">
         	<div class="panel-body view_file">
	            	<?php 
	            	if(!$file || isset($hidden)){
						echo _l('file_or_folder_does_not_exist');
	            	}else{ ?>
						<?php echo form_hidden('hash_share', $file->hash_share); ?>
						<?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
	            		<?php if($file->mime != 'directory'){ ?>
	            		<i class="fa fa-file" aria-hidden="true"></i>
		            	<?php }else{ ?>
	            		<i class="fa fa-folder" aria-hidden="true"></i>
		            	<?php } ?>

	            		<?php echo html_entity_decode($file->name); ?>
	            		<br>
	            		<?php if($file->mime != 'directory'){ ?>
		            		<i class="fa fa-floppy-o" aria-hidden="true"></i>
		            		<?php 
		            		$bytes = $file->size;
		            		if ($bytes >= 1073741824)
					        {
					            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
					        }
					        elseif ($bytes >= 1048576)
					        {
					            $bytes = number_format($bytes / 1048576, 2) . ' MB';
					        }
					        elseif ($bytes >= 1024)
					        {
					            $bytes = number_format($bytes / 1024, 2) . ' KB';
					        }
					        elseif ($bytes > 1)
					        {
					            $bytes = $bytes . ' bytes';
					        }
					        elseif ($bytes == 1)
					        {
					            $bytes = $bytes . ' byte';
					        }
					        else
					        {
					            $bytes = '0 bytes';
					        }
		            		echo html_entity_decode($bytes);
		            		?>
		            	<?php } ?>
	            		<?php if($file->type == 'fs_public' && $file->is_download == 1){ ?>
							<br>
		            		<hr>
							<button type="button" class="btn btn-success btn-block <?php if($file->password != ''){echo 'confirm_password';}else{echo 'download_file';} ?>"><i class="fa fa-download" aria-hidden="true"></i> <?php echo _l('download'); ?></button>
	            		<?php } ?>
	            	<?php }  ?>
         	</div>
      	</div>
   	</div>
</div>

<div class="modal fade" id="confirm-password-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title add-title"><?php echo _l('confirm_password')?></h4>
			</div>
			<div class="modal-body">
				<div class="row" id="div_password">
					<div class="col-md-12">
		               <label for="password" class="control-label"><?php echo _l('staff_add_edit_password'); ?></label>
		               <div class="input-group">
		                  <input type="password" class="form-control" name="password" autocomplete="off">
		                  <span class="input-group-addon">
		                  <a href="#password" class="show_password" onclick="showPassword('password'); return false;"><i class="fa fa-eye"></i></a>
		                  </span>
		               </div>
		            </div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
				<button type="button" class="btn btn-success btn-submit download_file"><i class="fa fa-download" aria-hidden="true"></i> <?php echo _l('download'); ?></button>
			</div>
		</div>
	</div>
</div>
<?php hooks()->do_action('fs_public_footer'); ?>
<?php require 'modules/file_sharing/assets/js/public_js.php';?>
