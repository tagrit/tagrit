<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12" id="small-table">
				<div class="panel_s">
					<div class="panel-body">
						 <?php echo form_hidden('internal_id',$internal_id); ?>
						<div class="row">
		                 <div class="col-md-12 ">
		                  <h4 class="no-margin font-bold"><i class="fa fa-shopping-basket" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
		                  <hr />
		                 </div>
		              	</div>
		              	<div class="row">    
	                        <div class="_buttons col-md-3">
	                        	
	                        	<?php if (has_permission('wh_internal_delivery_note', '', 'create') || is_admin()) { ?>
		                        <a href="<?php echo admin_url('warehouse/add_update_internal_delivery'); ?>"class="btn btn-info pull-left mright10 display-block">
		                            <?php echo _l('_new'); ?>
		                        </a>
		                        <?php } ?>
			               

		                    </div>
		                     <div class="col-md-1 pull-right">
		                        <a href="#" class="btn btn-default pull-right btn-with-tooltip toggle-small-view hidden-xs" onclick="toggle_small_view_proposal('.internal_delivery_sm','#internal_delivery_sm_view'); return false;" data-toggle="tooltip" title="<?php echo _l('invoices_toggle_table_tooltip'); ?>"><i class="fa fa-angle-double-left"></i></a>
		                    </div>
                    	</div>

                    <br/>
                    <?php render_datatable(array(
                        _l('internal_delivery_note'),
                        _l('deliver_name'),
                        _l('addedfrom'),
                        _l('datecreated'),
                        _l('total_amount'),
                        _l('status_label')
                        ),'table_internal_delivery',['internal_delivery_sm' => 'internal_delivery_sm']); ?>
						

					</div>
				</div>
			</div>
		<div class="col-md-7 small-table-right-col">
            <div id="internal_delivery_sm_view" class="hide">
            </div>
        </div>

		</div>
	</div>
</div>
<script>var hidden_columns = [];</script>
<?php init_tail(); ?>
<?php require 'modules/warehouse/assets/js/manage_internal_delivery_js.php';?>
</body>
</html>
