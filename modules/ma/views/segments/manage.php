<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
 <div class="content">
    <div class="panel_s">
     <div class="panel-body">
      <div class="_buttons">
<?php if(has_permission('ma_segments', '', 'create')){ ?>

        <a href="<?php echo admin_url('ma/segment'); ?>" class="btn btn-primary mbot10"><?php echo _l('ma_add_new', _l('segment')); ?></a>
<?php } ?>

        <?php echo form_hidden('csrf_token_name', $this->security->get_csrf_token_name()); ?>
        <?php echo form_hidden('csrf_token_hash', $this->security->get_csrf_hash()); ?>
         <div class="visible-xs">
            <div class="clearfix"></div>
         </div>
         <div class="_filters _hidden_inputs hidden" id="kanban-params">
            <?php
               foreach($categories as $category){
                  echo form_hidden('segment_category_'.$category['id']);
               }
               
               ?>
         </div>
         <div class="btn-group pull-right btn-with-tooltip-group _filter_data mleft5" data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-filter" aria-hidden="true"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-left width-300">
               <li class="active"><a href="#" data-cview="all" onclick="dt_segment_custom_view('','.table-segments',''); return false;"><?php echo _l('customers_sort_all'); ?></a>
               </li>
               <li class="divider"></li>
               <?php if(count($categories) > 0){ ?>
               <li class="dropdown-submenu pull-left categories">
                  <a href="#" tabindex="-1"><?php echo _l('segment_categories'); ?></a>
                  <ul class="dropdown-menu dropdown-menu-left">
                     <?php foreach($categories as $category){ ?>
                     <li>
                      <a href="#" data-cview="segment_category_<?php echo html_entity_decode($category['id']); ?>" onclick="dt_segment_custom_view('segment_category_<?php echo html_entity_decode($category['id']); ?>','.table-segments','segment_category_<?php echo html_entity_decode($category['id']); ?>'); return false;"><?php echo html_entity_decode($category['name']); ?></a>
                     </li>
                     <?php } ?>
                  </ul>
               </li>
                <?php } ?>
            </ul>
         </div>
         <a href="<?php echo admin_url('ma/segments?group=kanban'); ?>" class="btn pull-right mleft5 <?php echo ($group == 'kanban' ? 'btn-success disabled' : 'btn-default'); ?>"><i class="fa fa-th-large" aria-hidden="true"></i> <?php echo _l('kanban'); ?></a>
         <a href="<?php echo admin_url('ma/segments?group=chart'); ?>" class="btn pull-right mleft5 <?php echo ($group == 'chart' ? 'btn-success disabled' : 'btn-default'); ?>"><i class="fa fa-area-chart" aria-hidden="true"></i> <?php echo _l('chart'); ?></a>
         <a href="<?php echo admin_url('ma/segments?group=list'); ?>" class="btn pull-right <?php echo ($group == 'list' ? 'btn-success disabled' : 'btn-default'); ?>"><i class="fa fa-th-list" aria-hidden="true"></i> <?php echo _l('list'); ?></a>
      </div>
      <div class="clearfix"></div>
      <hr class="hr-panel-heading" />
      <div class="row mbot15">
         <div class="col-md-12">
            <h4 class="no-margin"><?php echo _l('segments_summary'); ?></h4>
         </div>
         <div class="col-md-2 col-xs-6 border-right">
            <h3 class="bold"><?php echo total_rows(db_prefix().'ma_segments'); ?></h3>
            <span class="text-dark"><?php echo _l('segments_summary_total'); ?></span>
         </div>
         <?php foreach($categories as $category){ ?>
         <div class="col-md-2 col-xs-6 border-right">
            <h3 class="bold"><?php echo total_rows(db_prefix().'ma_segments','category='.$category['id']); ?></h3>
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
<?php init_tail(); ?>
<?php require 'modules/ma/assets/js/segments/manage_js.php';?>

