<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="panel_s">
        <div class="panel-body">
          <h4 class="no-margin font-bold"><?php echo _l($title); ?></h4>
          <hr />
          <div>
            <a href="#" class="btn btn-info add-new-transfer mbot15"><?php echo _l('add'); ?></a>
          </div>
          <div class="row">
            <div class="col-md-3">
              <?php echo render_select('ft_transfer_funds_from',$accounts,array('id','name', 'account_type_name'),'transfer_funds_from', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
            </div>
            <div class="col-md-3">
              <?php echo render_select('ft_transfer_funds_to',$accounts,array('id','name', 'account_type_name'),'transfer_funds_to', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
            </div>
            <div class="col-md-3">
              <?php echo render_date_input('from_date','from_date'); ?>
            </div>
            <div class="col-md-3">
              <?php echo render_date_input('to_date','to_date'); ?>
            </div>
          </div>
          <hr>
          <a href="#" data-toggle="modal" data-target="#transfer_bulk_actions" class="hide bulk-actions-btn table-btn" data-table=".table-transfer"><?php echo _l('bulk_actions'); ?></a>
          <table class="table table-transfer scroll-responsive">
           <thead>
              <tr>
                <th><span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="transfer"><label></label></div></th>
                 <th><?php echo _l('transfer_funds_from'); ?></th>
                 <th><?php echo _l('transfer_funds_to'); ?></th>
                 <th><?php echo _l('transfer_amount'); ?></th>
                 <th><?php echo _l('expense_dt_table_heading_date'); ?></th>
              </tr>
           </thead>
        </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $arrAtt = array();
      $arrAtt['data-type']='currency';
?>
<div class="modal fade" id="transfer-modal">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?php echo _l('transfer')?></h4>
         </div>
         <?php echo form_open_multipart(admin_url('accounting/add_transfer'),array('id'=>'transfer-form'));?>
         <?php echo form_hidden('id'); ?>
         
         <div class="modal-body">
              <?php echo render_select('transfer_funds_from',$accounts,array('id','name','account_type_name'),'transfer_funds_from'); ?>
              <?php echo render_select('transfer_funds_to',$accounts,array('id','name','account_type_name'),'transfer_funds_to'); ?>
              <?php echo render_date_input('date', 'expense_dt_table_heading_date') ?>
              <?php echo render_input('transfer_amount', 'transfer_amount', '', 'text', $arrAtt) ?>
              <div class="row">
                <div class="col-md-12">
                  <p class="bold"><?php echo _l('dt_expense_description'); ?></p>
                  <?php echo render_textarea('description','','',array(),array(),'','tinymce'); ?>
                </div>
              </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info btn-submit"><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>  
      </div>
   </div>
</div>
<div class="modal fade bulk_actions" id="transfer_bulk_actions" tabindex="-1" role="dialog" data-table=".table-transfer">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
         </div>
         <div class="modal-body">
            <?php if(has_permission('accounting_journal_entry','','detele')){ ?>
               <div class="checkbox checkbox-danger">
                  <input type="checkbox" name="mass_delete" id="mass_delete">
                  <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
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
<?php init_tail(); ?>
</body>
</html>
<?php require 'modules/accounting/assets/js/transfer/manage_js.php'; ?>
