
<h4 class="customer-profile-group-heading"><?php echo _l('insurances'); ?></h4>
    <div class="row">
      <div class="col-md-12">
        <?php 
        if(is_admin() || has_permission('fleet_inspection', '', 'create')){
         ?>
         <button class="btn btn-primary mbot20" onclick="add_insurances(); return false;"><?php echo _l('add'); ?></button>          
         <div class="clearfix"></div>
       <?php } ?>

       <div class="row">
        <div class="col-md-3">
          <?php echo render_date_input('from_date_filter', 'from_date'); ?>
        </div>

        <div class="col-md-3">
          <?php echo render_date_input('to_date_filter', 'to_date'); ?>
        </div>
        <div class="col-md-3"></div>
      </div>

      <div class="clearfix"></div>
      <br>
      <div class="clearfix"></div>
      <table class="table table-insurances scroll-responsive">
           <thead>
              <tr>
                 <th><?php echo _l('name'); ?></th>
                 <th><?php echo _l('vehicle'); ?></th>
                 <th><?php echo _l('insurance_company'); ?></th>
                 <th><?php echo _l('status'); ?></th>
                 <th><?php echo _l('start_date'); ?></th>
                 <th><?php echo _l('end_date'); ?></th>
                 <th><?php echo _l('amount'); ?></th>
              </tr>
           </thead>
        </table>

  </div>
</div>

<?php $arrAtt = array();
      $arrAtt['data-type']='currency';
?>
<div class="modal fade" id="insurance-modal">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?php echo _l('insurance')?></h4>
         </div>
         <?php echo form_open_multipart(admin_url('fleet/insurance'),array('id'=>'insurance-form'));?>
         <?php echo form_hidden('id'); ?>
         
         <div class="modal-body">
                <?php echo render_select('vehicle_id', $vehicles, array('id', 'name'), 'vehicle'); ?>
                <?php echo render_input('name', 'name'); ?>
                <?php echo render_select('insurance_company_id', $insurance_company, array('id', 'name'), 'insurance_company'); ?>
                <?php echo render_select('insurance_status_id', $insurance_status, array('id', 'name'), 'insurance_status'); ?>
                <?php echo render_select('insurance_category_id', $insurance_categorys, array('id', 'name'), 'insurance_category'); ?>
                <?php echo render_select('insurance_type_id', $insurance_types, array('id', 'name'), 'insurance_type'); ?>
                <div class="row">
                   <div class="col-md-6">
                     <?php echo render_date_input('start_date', 'start_date'); ?>
                   </div>
                   <div class="col-md-6">
                     <?php echo render_date_input('end_date', 'end_date'); ?>
                   </div>
                </div>
                <?php echo render_input('amount', 'amount', '', 'text', $arrAtt); ?>
                <?php echo render_textarea('description','description'); ?>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info btn-submit"><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>  
      </div>
   </div>
</div>

