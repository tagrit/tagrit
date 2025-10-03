<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
	<div class="content">
		<div class="row">  
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div>
							<div class="row mbot10">
							<div class=" col-md-3">
									<?php if (has_permission('wh_loss_adjustment', '', 'create') || is_admin()) { ?>
										<a href="<?php echo admin_url('warehouse/add_loss_adjustment') ?>" class="btn btn-info pull-left display-block">
											<?php echo _l('add'); ?>
										</a>
									<?php } ?>
							</div>
							</div>

							<div class="row">
							<div class="col-md-3 ">
								<div class="form-group" app-field-wrapper="time">
									<label for="time" class="control-label"><?php echo _l('_time'); ?></label>
									<div class="input-group date">
										<input type="text" id="time_filter" onchange="filter_date(this);return false;" name="time_filter" class="form-control datepicker" value="" autocomplete="off" aria-invalid="false">
										<div class="input-group-addon">
											<i class="fa fa-calendar calendar-icon"></i>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group" app-field-wrapper="date_create">
									<label for="date_create" class="control-label"><?php echo _l('datecreator'); ?></label>
									<div class="input-group date">
										<input type="text" id="date_create" onchange="filter_date(this);return false;" name="date_create" class="form-control datepicker" value="" autocomplete="off" aria-invalid="false">
										<div class="input-group-addon">
											<i class="fa fa-calendar calendar-icon"></i>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="date_create" class="control-label"><?php echo _l('status_label'); ?></label>
									<select name="status_filter" class="selectpicker" id="status_filter" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
										<option value=""></option>
										<option value="0"><?php echo _l('invoice_status_draft'); ?></option>
										<option value="1"><?php echo _l('adjusted'); ?></option>
										<option value="-1"><?php echo _l('reject'); ?></option>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="date_create" class="control-label"><?php echo _l('type_label'); ?></label>
									<select name="type_filter" class="selectpicker" id="patient" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
										<option value=""></option>
										<option value="loss"><?php echo _l('loss'); ?></option>
										<option value="adjustment"><?php echo _l('adjustment'); ?></option>
									</select>
								</div>
							</div>
							</div>
							<div class="clearfix"></div>
							<hr class="hr-panel-heading" />
							<div class="clearfix"></div>
							<table class="table table-loss_adjustment scroll-responsive">
								<thead>
									<th><?php echo _l('type_label'); ?></th>
									<th><?php echo _l('_time'); ?></th>
									<th><?php echo _l('datecreator'); ?></th>
									<th><?php echo _l('status_label'); ?></th>
									<th><?php echo _l('reason'); ?></th>
									<th><?php echo _l('creator'); ?></th>
									<th><?php echo _l('options'); ?></th>
								</thead>
								<tbody></tbody>
								<tfoot>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>        
								</tfoot>
							</table>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
