<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12" id="small-table">
				<div class="panel_s">
					<div class="panel-body">
						<div class="row">
		                 <div class="col-md-12">
		                  <h4 class="no-margin font-bold"><i class="fa fa-file-text-o" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
		                  <hr />
		                 </div>
		              	</div>
		              	<div class="row">
		              		<div class="col-md-12">    
		                        <div class="_buttons">
		                        	<?php if (has_permission('purchase_contracts', '', 'create') || is_admin()) { ?>
			                        <a href="<?php echo admin_url('purchase/contract'); ?>"class="btn btn-info pull-left mright10 display-block">
			                            <?php echo _l('new_pur_order'); ?>
			                        </a>
			                        <?php } ?>
			                    </div>
			                    <div class="col-md-3">
			                    	<select name="vendor[]" id="vendor" class="selectpicker" multiple data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('vendor'); ?>" >
				                    
				                    <?php foreach($vendors as $or){ ?>
				                      <option value="<?php echo pur_html_entity_decode($or['userid']); ?>"><?php echo pur_html_entity_decode($or['company']); ?></option>
				                    <?php } ?>
				                  	</select>
			                    </div>
			                    <div class="col-md-3">
			                    	<select name="department[]" readonly="true" id="department" class="selectpicker" multiple data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('department'); ?>" >
					                   
					                    <?php foreach($departments as $dpm){ ?>
					                      <option value="<?php echo pur_html_entity_decode($dpm['departmentid']); ?>" ><?php echo pur_html_entity_decode($dpm['name']); ?></option>
					                    <?php } ?>
					                </select>
			                    </div>
			                    <div class="col-md-3">
			                    	<select name="project[]" id="project" class="selectpicker" multiple  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('project'); ?>" >
					                    
					                    <?php foreach($projects as $pj){ ?>
					                      <option value="<?php echo pur_html_entity_decode($pj['id']); ?>" ><?php echo pur_html_entity_decode($pj['name']); ?></option>
					                    <?php } ?>
					                </select>
			                    </div>
		                    </div>
                    	</div>
                    	

                    <br><br>
                    
                    <?php render_datatable(array(
                    	_l('department'),
                    	_l('project'),
                    	_l('service_category'),
                        _l('vendor'),
                        _l('contract_description'),
                        _l('contract_value'),
                        _l('payment_amount'),
                        _l('payment_cycle'),
                        _l('payment_terms'),
                        _l('start_date'),
                        _l('end_date'),
                        _l('status'),
                        ),'table_contracts'); ?>
						
					</div>
				</div>
			</div>
			<div class="col-md-7 small-table-right-col">
			    <div id="pur_order" class="hide">
			    </div>
			 </div>
		</div>
	</div>
</div>

<?php init_tail(); ?>
</body>
</html>