

<div class="widget" id="widget-<?php echo basename(__FILE__,".php"); ?>" data-name="<?php echo _l('purchase_widget'); ?>">
<div class="panel_s user-data">
  <div class="panel-body">
    <div class="widget-dragger"></div>

      <div class="row">
       <div class="col-md-12">
          <div class="row">
            <div class="col-md-6">
              <h4 class="no-margin font-bold"><i class="fa fa-shopping-cart" aria-hidden="true"></i> <?php echo _l('orders_are_about_to_be_delivered'); ?></h4>
            </div>
            <div class="col-md-6">
              <a href="<?php echo admin_url('purchase/purchase_order'); ?>" class="btn btn-info pull-right"><?php echo _l('view_all'); ?></a>
            </div>
          </div>
          <hr />
        </div>
                </div>
                 <?php 
                 $arr_table = [];
                 $arr_table[] = _l('pur_order_number');
                 $arr_table[] = _l('order_date');
                 $arr_table[] = _l('vendor');
                 $arr_table[] = _l('po_value');
                 $arr_table[] = _l('tax_value');
                 $arr_table[] = _l('delivery_date');
                 $arr_table[] = _l('delivery_status');
                    
                  ?>                   
                <?php render_datatable($arr_table,'table_purorder_wg'); ?>                   
      
    </div>
  </div>
   
</div>