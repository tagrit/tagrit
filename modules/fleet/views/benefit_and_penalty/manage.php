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
            <?php if(is_admin() || has_permission('fleet_benefit_and_penalty', '', 'create')){ ?>
              <a href="#" class="btn btn-info add-new-benefit_and_penalty mbot15"><?php echo _l('add'); ?></a>
            <?php } ?>
          </div>
          <div class="row">
            <div class="col-md-3">
             <?php 
             $type = [
               ['id' => 'benefit', 'name' => _l('benefit')],
               ['id' => 'penalty', 'name' => _l('penalty')],
             ];
             echo render_select('_type', $type, array('id', 'name'), 'type');
             ?>
           </div>
            <div class="col-md-3">
              <?php echo render_date_input('from_date','from_date'); ?>
            </div>
            <div class="col-md-3">
              <?php echo render_date_input('to_date','to_date'); ?>
            </div>
          </div>
          <hr>
          
          <table class="table table-benefit_and_penalty scroll-responsive">
           <thead>
              <tr>
                 <th><?php echo _l('id'); ?></th>
                 <th><?php echo _l('subject'); ?></th>
                 <th><?php echo _l('driver'); ?></th>
                 <th><?php echo _l('type'); ?></th>
                 <th><?php echo _l('date'); ?></th>
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

      $formality = [
               ['id' => 'commend', 'name' => _l('commend')],
               ['id' => 'bonus_money', 'name' => _l('bonus_money')],
             ];

      $formality2 = [
               ['id' => 'remind', 'name' => _l('remind')],
               ['id' => 'indemnify', 'name' => _l('indemnify')],
             ];
?>
<div class="modal fade" id="benefit_and_penalty-modal">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?php echo _l('benefit_and_penalty')?></h4>
         </div>
         <?php echo form_open_multipart(admin_url('fleet/add_benefit_and_penalty'),array('id'=>'benefit_and_penalty-form'));?>
         <?php echo form_hidden('id'); ?>
         
         <div class="modal-body">
               <?php echo render_select('type', $type, array('id', 'name'), 'type', '', [], [], '', '', false); ?>
                <?php echo render_input('subject', 'subject') ?>
                <?php echo render_select('criteria_id',$criterias,array('id','name'),'criteria'); ?>
                <?php echo render_date_input('date','date'); ?>
               <?php echo render_select('driver_id',$drivers, array('staffid', array('firstname', 'lastname')),'driver'); ?>
               <div class="benefit_type">
                <?php echo render_select('benefit_formality', $formality, array('id', 'name'), 'formality', '', [], [], '', '', false); ?>
                  <div class="benefit_amount_div hide">
                   <?php echo render_input('reward', 'reward', '', 'text', $arrAtt) ?>
                  </div>
               </div>

               <div class="penalty_type hide">
                <?php echo render_select('penalty_formality', $formality2, array('id', 'name'), 'formality', '', [], [], '', '', false); ?>
                  <div class="penalty_amount_div hide row">
                     <div class="col-md-6">
                     <?php echo render_input('amount_of_damage', 'amount_of_damage', '', 'text', $arrAtt) ?>
                     </div>
                     <div class="col-md-6">
                     <?php echo render_input('amount_of_compensation', 'amount_of_compensation', '', 'text', $arrAtt) ?>
                     </div>
                  </div>
               </div>

                <?php echo render_textarea('notes','notes') ?>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info btn-submit"><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>  
      </div>
   </div>
</div>

<!-- /.modal -->
<?php init_tail(); ?>
</body>
</html>
<?php require 'modules/fleet/assets/js/benefit_and_penalty/manage_js.php'; ?>
