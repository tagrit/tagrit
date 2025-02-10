<?php defined('BASEPATH') or exit('No direct script access allowed');?>

<?php init_head();?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
              <div class="panel-body">
                <div class="border-right">
                  <h4 class="no-margin font-bold"><?php echo _l($title); ?></h4>
                  <hr />
                </div>
                <div class="row">
                  <div class="col-md-3">
                    <?php $transaction_type = [ 
                          1 => ['id' => 'customer', 'name' => _l('sync_customer')],
                          2 => ['id' => 'invoice', 'name' => _l('sync_invoice')],
                          3 => ['id' => 'payment', 'name' => _l('sync_payment')],
                          4 => ['id' => 'expense', 'name' => _l('sync_expense')],
                        ]; 
                        ?>
                        <?php echo render_select('transaction_type',$transaction_type,array('id','name'),'transaction_type', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
                  </div>
                  <div class="col-md-3">
                    <?php $status = [ 
                          1 => ['id' => '0', 'name' => _l('fail')],
                          2 => ['id' => '1', 'name' => _l('success')],
                        ]; 
                        ?>
                        <?php echo render_select('status',$status,array('id','name'),'status', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
                  </div>
                  <div class="col-md-3">
                    <?php $type = [ 
                          1 => ['id' => 'sync_down', 'name' => _l('sync_down')],
                          2 => ['id' => 'sync_up', 'name' => _l('sync_up')],
                        ]; 
                        ?>
                        <?php echo render_select('type',$type,array('id','name'),'type', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
                        <?php echo form_hidden('software','sage_accounting'); ?>
                  </div>
                  <div class="col-md-3">
                    <?php echo render_date_input('from_date','from_date'); ?>
                  </div>
                  <div class="col-md-3">
                    <?php echo render_date_input('to_date','to_date'); ?>
                  </div>
                </div>
                <table class="table table-sync-logs">
                  <thead>
                    <th><?php echo _l('transaction_type'); ?></th>
                    <th><?php echo _l('transaction'); ?></th>
                    <th><?php echo _l('type'); ?></th>
                    <th><?php echo _l('status'); ?></th>
                    <th><?php echo _l('date_sync'); ?></th>
                  </thead>
                  <tbody>
                    
                  </tbody>
                </table>
              </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php init_tail();?>
</body>
</html>
