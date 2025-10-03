<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12" id="small-table">
				<div class="panel_s">
					<div class="panel-body">
						 <?php echo form_hidden('stock_take_id',$stock_take_id); ?>
						<div class="row">
		                 <div class="col-md-4 border-right">
		                  <h4 class="no-margin font-bold"><i class="fa fa-database menu-icon menu-icon" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
		                  <hr />
		                 </div>
		              	</div>
		              	<div class="row">    
	                        <div class="_buttons col-md-3">
	                        	<?php if (has_permission('warehouse', '', 'create') || is_admin()) { ?>
		                        <a href="<?php echo admin_url('warehouse/stock_take'); ?>"class="btn btn-info pull-left mright10 display-block">
		                            <?php echo _l('Kiểm kê kho'); ?>
		                        </a>
		                        <?php } ?>
		                    </div>
		                     <div class="col-md-1 pull-right">
		                        <a href="#" class="btn btn-default pull-right btn-with-tooltip toggle-small-view hidden-xs" onclick="toggle_small_view_proposal('.purchase_sm','#purchase_sm_view'); return false;" data-toggle="tooltip" title="<?php echo _l('invoices_toggle_table_tooltip'); ?>"><i class="fa fa-angle-double-left"></i></a>
		                    </div>
                    	</div>
                    <br><br>
                    <?php render_datatable(array(
                        _l('inventory_ticket_code'),
                        _l('warehouse_inventory'),
                        _l('_inventory'),
                        _l('_member_inventory'),
                        _l('_inventory_results'),
                        _l('status'),
                        ),'table_manage_stock_take',['purchase_sm' => 'purchase_sm']); ?>
						
					</div>
				</div>
			</div>

		<div class="col-md-7 small-table-right-col">
            <div id="purchase_sm_view" class="hide">
            </div>
        </div>

		</div>
	</div>
</div>
<script>var hidden_columns = [3,4,5];</script>
<?php init_tail(); ?>
</body>
</html>