<div class="row">
  <div class="col-md-3">
    <?php $status = [ 
          1 => ['id' => 'converted', 'name' => _l('acc_converted')],
          2 => ['id' => 'has_not_been_converted', 'name' => _l('has_not_been_converted')],
        ]; 
        ?>
        <?php echo render_select('status',$status,array('id','name'),'status', $_status, array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
  </div>
  <div class="col-md-3">
    <?php echo render_date_input('from_date','from_date'); ?>
  </div>
  <div class="col-md-3">
    <?php echo render_date_input('to_date','to_date'); ?>
  </div>
</div>
<a href="#" data-toggle="modal" data-target="#sales_invoice_bulk_actions" class="hide bulk-actions-btn table-btn" data-table=".table-sales-invoice"><?php echo _l('bulk_actions'); ?></a>
<table class="table table-sales-invoice">
  <thead>
    <th><span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="sales-invoice"><label></label></div></th>
    <th><?php echo _l('invoice'); ?></th>
    <th><?php echo _l('invoice_payments_table_date_heading'); ?></th>
    <th><?php echo _l('acc_amount'); ?></th>
    <th><?php echo _l('customer'); ?></th>
    <th><?php echo _l('mapping_status'); ?></th>
    <th><?php echo _l('acc_invoice_status'); ?></th>
    <th><?php echo _l('acc_convert'); ?></th>
  </thead>
  <tbody>
    
  </tbody>
</table>
<?php $arrAtt = array();
      $arrAtt['data-type']='currency';
?>
<div class="modal fade" id="convert-modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?php echo _l('acc_convert')?></h4>
      </div>
      <?php echo form_open_multipart(admin_url('accounting/convert'),array('id'=>'convert-form'));?>
      <?php echo form_hidden('id'); ?>
      <?php echo form_hidden('type'); ?>
      <?php echo form_hidden('amount'); ?>
      <div class="modal-body">
        <div id="div_info" class="mbot25"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button id="btn_account_history" type="submit" class="btn btn-info intext-btn"><?php echo _l('submit'); ?></button>
      </div>
      <?php echo form_close(); ?>  
    </div>
  </div>
</div>

<div class="modal fade bulk_actions" id="sales_invoice_bulk_actions" tabindex="-1" role="dialog" data-table=".table-sales-invoice">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
         </div>
         <div class="modal-body">
          <?php echo form_hidden('bulk_actions_type', 'invoice'); ?>
            <?php if(has_permission('accounting_transaction','','create')){ ?>
               <div class="checkbox checkbox-info">
                  <input type="checkbox" name="mass_convert" id="mass_convert" checked>
                  <label for="mass_convert"><?php echo _l('mass_convert'); ?></label>
               </div>
            <?php } ?>
            <?php if(has_permission('accounting_transaction','','detele')){ ?>
               <div class="checkbox checkbox-danger">
                  <input type="checkbox" name="mass_delete_convert" id="mass_delete_convert">
                  <label for="mass_delete_convert"><?php echo _l('mass_delete_convert'); ?></label>
               </div>
            <?php } ?>
      </div>
      <div class="modal-footer">
         <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
         <a href="#" class="btn btn-info" onclick="bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
      </div>
   </div>
   <!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->
</div>
<!-- /.modal -->
