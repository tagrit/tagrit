<?php init_head();?>
<div id="wrapper" class="commission">
  <div class="row content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <h4 class="no-margin font-bold"><?php echo _l($title); ?></h4>
            <hr />
            <a href="<?php echo admin_url('purchase/new_vendor_items'); ?>" class="btn btn-info mbot10"><?php echo _l('new'); ?></a>
            <div class="row">
              <div class="col-md-3">
                <?php echo render_select('vendor_filter', $vendors, array('userid', 'company'), 'vendors', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
              </div>
              <div class="col-md-3">
                <?php 
                echo render_select('group_items_filter', $commodity_groups, array('id','name'), 'group_item', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
              </div>
              <div class="col-md-3">
                <label for="item_select"><?php echo _l('pur_item'); ?></label>
                <?php $this->load->view('purchase/item_include/main_item_select'); ?>
              </div>
              
              <div class="clearfix"></div>
            </div>

            <div class="modal bulk_actions" id="table_vendors_items_list_bulk_actions" tabindex="-1" role="dialog">
              <div class="modal-dialog" role="document">
                 <div class="modal-content">
                    <div class="modal-header">
                       <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
                       <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                       <?php if(is_admin() || has_permission('purchase_vendor_items', '', 'delete')){ ?>
                       <div class="checkbox checkbox-danger">
                          <input type="checkbox" name="mass_delete" id="mass_delete">
                          <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
                       </div>
                      
                       <?php } ?>
                    </div>
                    <div class="modal-footer">
                       <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>

                       <?php if(has_permission('purchase_vendor_items','','delete') || is_admin()){ ?>
                       <a href="#" class="btn btn-info" onclick="purchase_delete_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
                        <?php } ?>
                    </div>
                 </div>
              </div>
            </div>

              <a href="#"  onclick="staff_bulk_actions(); return false;" data-toggle="modal" data-table=".table-vendor-items" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo _l('bulk_actions'); ?></a>
              <?php render_datatable(array(
                '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="vendor-items"><label></label></div>',
                _l('vendors'),
                _l('items'),
                _l('date_create'),
                ),'vendor-items',[],
                  array(
                     'id'=>'table-vendor-items',
                     'data-last-order-identifier'=>'table-vendor-items',
                     'data-default-order'=>get_table_last_order('table-vendor-items'),
                   )); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
</body>
</html>
<?php require 'modules/purchase/assets/js/manage_vendor_items_js.php';?>