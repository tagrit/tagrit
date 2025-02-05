<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body backdrop">
                <h4 class="no-margin font-bold"><?php echo _l($title); ?></h4>
                <hr />
                <div class="row">
                  <div class="col-md-4">
                    <div class="_buttons">
                       
                       <a href="<?php echo admin_url('accounting/vendor'); ?>" class="btn btn-info mright5 test pull-left display-block">
                       <?php echo _l('new_vendor'); ?></a>

                       <a href="<?php echo admin_url('accounting/vendor_import'); ?>" class="btn btn-info mright5 test pull-left display-block">
                     <?php echo _l('import_vendors'); ?></a>
                    </div>
                  </div>
                    <div class="col-md-12"><hr/></div>
                  </div>

                  <a href="#" data-toggle="modal" data-target="#customers_bulk_action" class="bulk-actions-btn table-btn hide" data-table=".table-vendors"><?php echo _l('bulk_actions'); ?></a>
                 
                 
                  <?php
                     $table_data = array();
                     $_table_data = array(
                       '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="vendors"><label></label></div>',
                        
                         array(
                         'name'=>_l('vendor_name'),
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-company')
                        ),
                        array(
                         'name'=>_l('clients_list_phone'),
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-phonenumber')
                        ), 
                        array(
                         'name'=>_l('client_address'),
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-address')
                        ), 
                        array(
                         'name'=>_l('addedfrom'),
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-add-from')
                        ),
                        array(
                         'name'=>_l('date_created'),
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-date-created')
                        ),
                      );
                     foreach($_table_data as $_t){
                      array_push($table_data,$_t);
                     }

                     render_datatable($table_data,'vendors',[],[
                           'data-last-order-identifier' => 'vendors',
                           'data-default-order'         => get_table_last_order('vendors'),
                     ]);
                     ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
 <div class="modal fade bulk_actions" id="customers_bulk_action" tabindex="-1" role="dialog">
<div class="modal-dialog" role="document">
   <div class="modal-content">
      <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
         <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
      </div>
      <div class="modal-body">
         <?php if(has_permission('customers','','delete')){ ?>
         <div class="checkbox checkbox-danger">
            <input type="checkbox" name="mass_delete" id="mass_delete">
            <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
         </div>
         <?php } ?>
      </div>
      <div class="modal-footer">
         <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
         <a href="#" class="btn btn-info" onclick="vendors_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
      </div>
   </div>
   <!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<?php init_tail(); ?>
</body>
</html>
