<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div id="list_po_report" class="hide">
   <div class="row">
      <div class="col-md-4">
         <div class="form-group">
            
         </div>
      </div>
      <div class="clearfix"></div>
   </div>
<table class="table table-po-report scroll-responsive">
   <thead>
      <tr>
         <th><?php echo _l('purchase_order'); ?></th>
         <th><?php echo _l('date'); ?></th>
         <th><?php echo _l('department'); ?></th>
         <th><?php echo _l('vendor'); ?></th>
         <th><?php echo _l('approval_status'); ?></th>
         <th><?php echo _l('po_value'); ?></th>
         <th><?php echo _l('tax_value'); ?></th>
         <th><?php echo _l('po_value_included_tax'); ?></th>
      </tr>
   </thead>
   <tbody></tbody>
   <tfoot>
      <tr>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td class="total_value"></td>
         <td class="total_tax"></td>
         <td class="total"></td>
      </tr>
   </tfoot>
</table>
</div>
