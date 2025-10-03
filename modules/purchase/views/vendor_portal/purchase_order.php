<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php hooks()->do_action('app_admin_head'); ?>
<div class="row">
	
	<div class="col-md-12">
		<div class="panel_s">
			<div class="panel-body">
				<h4><?php echo pur_html_entity_decode($title) ?></h4>
				<hr class="mtop5">
				<table class="table dt-table" >
		            <thead>
		               <tr>
		                  <th ><?php echo _l('purchase_order'); ?></th>
		                  <th ><?php echo _l('order_value'); ?></th>
		                  <th ><?php echo _l('payment_status'); ?></th>
		                  <th ><?php echo _l('order_date'); ?></th>
		                  <th ><?php echo _l('delivery_date'); ?></th>
		                  <th ><?php echo _l('pur_order_status'); ?></th>
		                  <th ><?php echo _l('options'); ?></th>
		               </tr>
		            </thead>
		            <tbody>
		            	<?php foreach($pur_order as $p){ ?>
		            		<?php
		            			$base_currency = get_base_currency_pur(); 
		            			if($p['currency'] != 0){
		            				$base_currency = pur_get_currency_by_id($p['currency']);
		            			}
		            		?>
		            		<tr>
		            			<td><a href="<?php echo site_url('purchase/vendors_portal/pur_order/'.$p['id']); ?>"><?php echo pur_html_entity_decode($p['pur_order_number']); ?></a>
		            			</td>
		            			<td><?php echo pur_html_entity_decode(app_format_money($p['total'],$base_currency->symbol)); ?></td>
		            			<td><?php 
		            				$paid = $p['total'] - purorder_inv_left_to_pay($p['id']);

						            $percent = 0;

						            if($p['total'] > 0){

						                $percent = ($paid / $p['total'] ) * 100;

						            }
						            
						           $_data = '<div class="progress-vendor"><div class="progress-bar bg-secondary task-progress-bar-ins-31" id="31" style="width: '.round($percent).'%; border-radius: 1em;">'.round($percent).'%</div></div>';

						            echo pur_html_entity_decode($_data);

		            			 ?></td>
		            			<td>
		            				<span class="label label-primary"><?php echo pur_html_entity_decode(_d($p['order_date'])); ?></span>
		            			</td>
		            			<td>
		            				<span class="label label-warning"><?php echo pur_html_entity_decode(_d($p['delivery_date'])); ?></span>
		            			</td>

		            			<td>
		            				<?php 
		            					if($p['order_status'] == 'new'){
		            						echo '<span class="label label-info">'._l('new_order').'</span>';
		            					}else if($p['order_status'] == 'delivered'){
		            						echo '<span class="label label-success">'._l('delivered').'</span>';
		            					}else if($p['order_status'] == 'confirmed'){
		            						echo '<span class="label label-warning">'._l('confirmed').'</span>';
		            					}else if($p['order_status'] == 'cancelled'){
		            						echo '<span class="label label-danger">'._l('cancelled').'</span>';
		            					}
		            				?>
		            			</td>
		            			<td> 
		            				<a href="<?php echo site_url('purchase/vendors_portal/pur_order/'.$p['id']); ?>" class="btn btn-icon btn-info"><i class="fa fa-eye"></i></a>
		            				<?php if($p['order_status'] == 'new'){ ?>
		            					<a href="javascript:void(0)" onclick="confirm_order(this); return false;" class="btn btn-icon btn-success"  data-order_id="<?php echo pur_html_entity_decode($p['id']); ?>" data-toggle="tooltip" data-placement="top" title="<?php echo _l('pur_confirm_order_note'); ?>"><i class="fa fa-check"></i></a>
		            				<?php } ?>
		            				<a href="javascript:void(0)" onclick="update_delivery_date(this); return false;" class="btn btn-icon btn-warning" data-order_id="<?php echo pur_html_entity_decode($p['id']); ?>" data-toggle="tooltip" data-placement="top" title="<?php echo _l('update_delivery_date'); ?>" ><i class="fa fa-truck"></i></a>
		            			</td>
		            		</tr>
		            	<?php } ?>
		            </tbody>
		         </table>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="update_delivery_date_modal" tabindex="-1" role="dialog">
<div class="modal-dialog dialog_30" >
    <?php echo form_open(site_url('purchase/vendors_portal/update_delivery_date_on_list'),array('id'=>'purorder-update_delivery_date-form')); ?>
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
                <span class="add-title"><?php echo _l('update_delivery_date'); ?></span>
            </h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                 <div id="order_additional"></div>

                <?php echo pur_render_date_input('delivery_date', 'delivery_date'); ?>	

                </div>
            </div>
        </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
        </div><!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php hooks()->do_action('app_admin_footer'); ?>
</body>
</html>
<?php require 'modules/purchase/assets/js/manage_order_vendor_js.php';?>