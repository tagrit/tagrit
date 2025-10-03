<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
	<div class="content">
		<div class="row">

			<div class="col-md-3">
				<ul class="nav navbar-pills navbar-pills-flat nav-tabs nav-stacked">
					<?php
					$i = 0;
					foreach($tab as $gr){
						?>
						<li<?php if($i == 0){echo " class='active'"; } ?>>
						<a href="<?php echo admin_url('manufacturing/setting?group='.$gr); ?>" data-group="<?php echo new_html_entity_decode($gr); ?>">
							<?php
								$icon['working_hour'] = '<span class="fa fa-area-chart"></span>';
								$icon['unit_of_measure_categories'] = '<span class="fa fa-certificate"></span>';
								$icon['unit_of_measure'] = '<span class="fa fa-list-alt"></span>';
								$icon['prefix_number'] = '<span class="fa fa-bars menu-icon"></span>';

								if($gr == 'prefix_number'){
									echo new_html_entity_decode($icon[$gr] .' '. _l('mrp_general_setting')); 

								}else{

									echo new_html_entity_decode($icon[$gr] .' '. _l($gr)); 
								}
							
							

							?>
						</a>
					</li>
					<?php $i++; } ?>
				</ul>
			</div>
			<div class="col-md-9">
				<div class="panel_s">
					<div class="panel-body">

						<?php $this->load->view($tabs['view']); ?>

					</div>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
		<?php echo form_close(); ?>
		<div class="btn-bottom-pusher"></div>
	</div>
</div>
<div id="new_version"></div>
<?php init_tail(); ?>

<?php 
$viewuri = $_SERVER['REQUEST_URI'];
 ?>

<?php if(!(strpos($viewuri,'admin/manufacturing/setting?group=working_hour') === false)){ 
	require 'modules/manufacturing/assets/js/settings/working_hour_js.php';
}elseif(!(strpos($viewuri,'admin/manufacturing/setting?group=unit_of_measure_categories') === false)){
	require 'modules/manufacturing/assets/js/settings/add_edit_categories_js.php';
}elseif(!(strpos($viewuri,'admin/manufacturing/setting?group=unit_of_measure') === false)){
	require 'modules/manufacturing/assets/js/settings/unit_of_measure_js.php';
}

 ?>
</body>
</html>
