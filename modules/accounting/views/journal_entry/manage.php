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
            <a href="<?php echo admin_url('accounting/new_journal_entry'); ?>" class="btn btn-info mbot15"><?php echo _l('add'); ?></a>
          </div>
          <div class="row">
            <div class="col-md-3">
              <?php echo render_date_input('from_date','from_date'); ?>
            </div>
            <div class="col-md-3">
              <?php echo render_date_input('to_date','to_date'); ?>
            </div>
          </div>
          <a href="#" data-toggle="modal" data-target="#journal_entry_bulk_actions" class="hide bulk-actions-btn table-btn" data-table=".table-journal-entry"><?php echo _l('bulk_actions'); ?></a>
          <table class="table table-journal-entry scroll-responsive">
           <thead>
              <tr>
                <th><span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="journal-entry"><label></label></div></th>
                 <th><?php echo _l('journal_date'); ?></th>
                 <th><?php echo _l('number').' - '._l('description'); ?></th>
                 <th><?php echo _l('reference'); ?></th>
                 <th><?php echo _l('acc_amount'); ?></th>
              </tr>
           </thead>
        </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade bulk_actions" id="journal_entry_bulk_actions" tabindex="-1" role="dialog" data-table=".table-journal-entry">
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
