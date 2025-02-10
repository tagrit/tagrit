<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="panel_s">
        <div class="panel-body">
          <div>
<?php if(has_permission('ma_stages', '', 'create')){ ?>

            <a href="#" class="btn btn-primary add-new-stage mbot15"><?php echo _l('ma_add_new', _l('stage')); ?></a>
<?php } ?>

            <?php echo form_hidden('csrf_token_name', $this->security->get_csrf_token_name()); ?>
              <?php echo form_hidden('csrf_token_hash', $this->security->get_csrf_hash()); ?>
               <div class="visible-xs">
                  <div class="clearfix"></div>
               </div>
               <div class="_filters _hidden_inputs hidden" id="kanban-params">
                  <?php
                     foreach($categories as $category){
                        echo form_hidden('stage_category_'.$category['id']);
                     }
                     
                     ?>
               </div>
               <div class="btn-group pull-right btn-with-tooltip-group _filter_data mleft5" data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>">
                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="fa fa-filter" aria-hidden="true"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-left width-300">
                     <li class="active"><a href="#" data-cview="all" onclick="dt_stage_custom_view('','.table-stages',''); return false;"><?php echo _l('customers_sort_all'); ?></a>
                     </li>
                     <li class="divider"></li>
                     <?php if(count($categories) > 0){ ?>
                     <li class="dropdown-submenu pull-left categories">
                        <a href="#" tabindex="-1"><?php echo _l('stage_categories'); ?></a>
                        <ul class="dropdown-menu dropdown-menu-left">
                           <?php foreach($categories as $category){ ?>
                           <li>
                            <a href="#" data-cview="stage_category_<?php echo html_entity_decode($category['id']); ?>" onclick="dt_stage_custom_view('stage_category_<?php echo html_entity_decode($category['id']); ?>','.table-stages','stage_category_<?php echo html_entity_decode($category['id']); ?>'); return false;"><?php echo html_entity_decode($category['name']); ?></a>
                           </li>
                           <?php } ?>
                        </ul>
                     </li>
                      <?php } ?>
                  </ul>
               </div>
               <a href="<?php echo admin_url('ma/stages?group=kanban'); ?>" class="btn pull-right mleft5 <?php echo ($group == 'kanban' ? 'btn-success disabled' : 'btn-default'); ?>"><i class="fa fa-th-large" aria-hidden="true"></i> <?php echo _l('kanban'); ?></a>
               <a href="<?php echo admin_url('ma/stages?group=chart'); ?>" class="btn pull-right mleft5 <?php echo ($group == 'chart' ? 'btn-success disabled' : 'btn-default'); ?>"><i class="fa fa-area-chart" aria-hidden="true"></i> <?php echo _l('chart'); ?></a>
               <a href="<?php echo admin_url('ma/stages?group=list'); ?>" class="btn pull-right <?php echo ($group == 'list' ? 'btn-success disabled' : 'btn-default'); ?>"><i class="fa fa-th-list" aria-hidden="true"></i> <?php echo _l('list'); ?></a>
          </div>
          <hr class="hr-panel-heading" />
          <div class="row mbot15">
             <div class="col-md-12">
                <h4 class="no-margin"><?php echo _l('stages_summary'); ?></h4>
             </div>
             <div class="col-md-2 col-xs-6 border-right">
                <h3 class="bold"><?php echo total_rows(db_prefix().'ma_stages'); ?></h3>
                <span class="text-dark"><?php echo _l('stages_summary_total'); ?></span>
             </div>
             <?php foreach($categories as $category){ ?>
             <div class="col-md-2 col-xs-6 border-right">
                <h3 class="bold"><?php echo total_rows(db_prefix().'ma_stages','category='.$category['id']); ?></h3>
                <span style="color: <?php echo html_entity_decode($category['color']); ?>;"><?php echo html_entity_decode($category['name']); ?></span>
             </div>
             <?php } ?>
           </div>
          <hr class="hr-panel-heading" />
            <?php $this->load->view($view); ?>
          
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="stage-modal">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?php echo _l('stages')?></h4>
         </div>
         <?php echo form_open_multipart(admin_url('ma/stage'),array('id'=>'stage-form'));?>
         <?php echo form_hidden('id'); ?>
         
         <div class="modal-body">
              <?php echo render_input('name', 'name') ?>
              <?php echo render_input('weight', 'ma_weight', '', 'number') ?>
              <?php echo render_color_picker('color',_l('color')); ?>
              <?php echo render_select('category',$categories,array('id','name'), 'category'); ?>
              <div class="row">
                <div class="col-md-12">
                  <p class="bold"><?php echo _l('dt_expense_description'); ?></p>
                  <?php echo render_textarea('description','',''); ?>
                </div>
              </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-primary btn-submit"><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>  
      </div>
   </div>
</div>

<?php init_tail(); ?>

<?php require 'modules/ma/assets/js/stages/manage_js.php';?>
