<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
        <div class="_filters _hidden_inputs">
           <?php
            foreach($statuses as $status) {
               echo form_hidden('debit_notes_status_'.$status['id'],isset($status['filter_default'])
                  && $status['filter_default'] ? 'true' : '');
            }
           foreach($years as $year){
              echo form_hidden('year_'.$year['year'],$year['year']);
           }
        ?>
     </div>
     <div class="col-md-12">
      <div class="panel_s mbot10">
         <div class="panel-body _buttons">
            <?php if(has_permission('purchase_debit_notes','','create')){ ?>
            <a href="<?php echo admin_url('purchase/debit_note'); ?>" class="btn btn-info pull-left display-block">
               <?php echo _l('new_debit_note'); ?>
            </a>
            <?php } ?>
            <div class="display-block text-right">
             <div class="btn-group pull-right mleft4 btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>">
               <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="fa fa-filter" aria-hidden="true"></i>
               </button>
               <ul class="dropdown-menu width300">
                  <li>
                     <a href="#" data-cview="all" onclick="dt_custom_view('','.table-debit-notes',''); return false;">
                        <?php echo _l('debit_notes_list_all'); ?>
                     </a>
                  </li>
                  <li class="divider"></li>
                  <?php foreach($statuses as $status){ ?>
                  <li class="<?php if(isset($status['filter_default']) && $status['filter_default']){echo 'active';} ?>">
                     <a href="#" data-cview="debit_notes_status_<?php echo $status['id']; ?>" onclick="dt_custom_view('debit_notes_status_<?php echo $status['id']; ?>','.table-debit-notes','debit_notes_status_<?php echo $status['id']; ?>'); return false;">
                        <?php echo format_credit_note_status($status['id'],true); ?>
                     </a>
                  </li>
                  <?php } ?>
                  <div class="clearfix"></div>
                  <?php
                  if(count($years) > 0){ ?>
                  <li class="divider"></li>
                  <?php foreach($years as $year){ ?>
                  <li class="active">
                     <a href="#" data-cview="year_<?php echo $year['year']; ?>" onclick="dt_custom_view(<?php echo $year['year']; ?>,'.table-debit-notes','year_<?php echo $year['year']; ?>'); return false;"><?php echo $year['year']; ?>
                     </a>
                  </li>
                  <?php } ?>
                  <?php } ?>
               </ul>
            </div>
            <a href="#" class="btn btn-default btn-with-tooltip toggle-small-view hidden-xs" onclick="toggle_small_view('.table-debit-notes','#debit_note'); return false;" data-toggle="tooltip" title="<?php echo _l('invoices_toggle_table_tooltip'); ?>"><i class="fa fa-angle-double-left"></i></a>
         </div>
      </div>
   </div>
   <div class="row">
      <div class="col-md-12" id="small-table">
         <div class="panel_s">
            <div class="panel-body">
               <!-- if credit not id found in url -->
               <?php echo form_hidden('debit_note_id',$debit_note_id); ?>
               <?php $this->load->view('debit_notes/table_html'); ?>
            </div>
         </div>
      </div>
      <div class="col-md-7 small-table-right-col">
         <div id="debit_note" class="hide">
         </div>
      </div>
   </div>
</div>
</div>
</div>
</div>
<?php $this->load->view('admin/includes/modals/sales_attach_file'); ?>
<script>
   var hidden_columns = [4,5,6];
</script>
<?php init_tail(); ?>
<?php require 'modules/purchase/assets/js/manage_debit_note_js.php';?>  
</body>
</html>
