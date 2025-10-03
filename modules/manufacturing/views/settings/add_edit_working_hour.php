<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php 
			$id = '';
			$title = '';
			if(isset($working_hour)){
				$title .= _l('update_working_hour');
				$id    = $working_hour->id;
			}else{
				$title .= _l('add_working_hour');
			}

			?>

			<?php echo form_open_multipart(admin_url('manufacturing/add_edit_working_hour/'.$id), array('id' => 'add_update_working_hour','autocomplete'=>'off')); ?>

			<div class="col-md-12" >
				<div class="panel_s">
					
					<div class="panel-body">
						<div class="row ">
							<div class="col-md-12">
								<h4 class="no-margin"><?php echo new_html_entity_decode($title); ?> 
							</div>
						</div>
						<hr class="hr-color">

						<!-- start tab -->
						<div class="modal-body">
							<div class="tab-content">
								<!-- start general infor -->
								<div class="row">
									<div class="row">
										<div class="col-md-6">
											<?php 
											$working_hour_name = isset($working_hour) ? $working_hour->working_hour_name : '';
											$hours_per_day = isset($working_hour) ? $working_hour->hours_per_day : '';
											
											?>

											<?php echo render_input('working_hour_name','working_hour_name',$working_hour_name,'text'); ?>   
										</div>
										<div class="col-md-6">
											<?php echo render_input('hours_per_day','hours_per_day',$hours_per_day,'text'); ?>   
										</div>
									</div>
								</div>


								<div class="row">
									<h5 class="h5-color"><?php echo _l('working_hour_info'); ?></h5>
									<hr class="hr-color">

									<div class="form"> 
										<div id="working_hour_hs" class="working_hour handsontable htColumnHeaders">
										</div>
										<?php echo form_hidden('working_hour_hs'); ?>
									</div>
								</div>

								<br>
								<br>
								<div class="row">
									<h5 class="h5-color"><?php echo _l('global_time_off_info'); ?></h5>
									<hr class="hr-color">

									<div class="form"> 
										<div id="global_time_off_hs" class="global_time_off handsontable htColumnHeaders">
										</div>
										<?php echo form_hidden('global_time_off_hs'); ?>
									</div>
								</div>

							</div>

							<div class="modal-footer">
								<a href="<?php echo admin_url('manufacturing/setting?group=working_hour'); ?>"  class="btn btn-default mr-2 "><?php echo _l('hr_close'); ?></a>
								<?php if(has_permission('manufacturing', '', 'create') || has_permission('manufacturing', '', 'edit')){ ?>

									<a href="#"class="btn btn-info pull-right mright10 display-block add_working_hours" ><?php echo _l('submit'); ?></a>


								<?php } ?>
							</div>

						</div>
					</div>
				</div>

				<?php echo form_close(); ?>
			</div>
		</div>
		<?php init_tail(); ?>
		<?php 
		require('modules/manufacturing/assets/js/settings/add_edit_working_hour_js.php');
		?>
	</body>
	</html>
