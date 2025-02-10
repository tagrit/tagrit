<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();?>
<div id="wrapper">
  <div class="content">
      <div class="panel_s">
        <div class="panel-body">
          <div class="_buttons">
            <?php if(has_permission('ma_campaigns', '', 'create')){ ?>
               <a href="<?php echo admin_url('ma/campaign'); ?>" class="btn btn-primary mbot15"><?php echo _l('ma_add_new', _l('campaign')); ?></a>
            <?php } ?>
            <?php echo form_hidden('csrf_token_name', $this->security->get_csrf_token_name()); ?>
            <?php echo form_hidden('csrf_token_hash', $this->security->get_csrf_hash()); ?>
             <div class="visible-xs">
                <div class="clearfix"></div>
             </div>
             <div class="_filters _hidden_inputs hidden" id="kanban-params">
                <?php
                   foreach($categories as $category){
                      echo form_hidden('campaign_category_'.$category['id']);
                   }
                   
                   ?>
             </div>
             <div class="btn-group pull-right btn-with-tooltip-group _filter_data mleft5" data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-filter" aria-hidden="true"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-left width-300">
                   <li class="active"><a href="#" data-cview="all" onclick="dt_campaign_custom_view('','.table-campaigns',''); return false;"><?php echo _l('customers_sort_all'); ?></a>
                   </li>
                   <li class="divider"></li>
                   <?php if(count($categories) > 0){ ?>
                   <li class="dropdown-submenu pull-left categories">
                      <a href="#" tabindex="-1"><?php echo _l('campaign_categories'); ?></a>
                      <ul class="dropdown-menu dropdown-menu-left">
                         <?php foreach($categories as $category){ ?>
                         <li>
                          <a href="#" data-cview="campaign_category_<?php echo html_entity_decode($category['id']); ?>" onclick="dt_campaign_custom_view('campaign_category_<?php echo html_entity_decode($category['id']); ?>','.table-campaigns','campaign_category_<?php echo html_entity_decode($category['id']); ?>'); return false;"><?php echo html_entity_decode($category['name']); ?></a>
                         </li>
                         <?php } ?>
                      </ul>
                   </li>
                    <?php } ?>
                </ul>
             </div>
             <a href="<?php echo admin_url('ma/campaigns?group=kanban'); ?>" class="btn pull-right mleft5 <?php echo ($group == 'kanban' ? 'btn-success disabled' : 'btn-default'); ?>"><i class="fa fa-th-large" aria-hidden="true"></i> <?php echo _l('kanban'); ?></a>
             <a href="<?php echo admin_url('ma/campaigns?group=chart'); ?>" class="btn pull-right mleft5 <?php echo ($group == 'chart' ? 'btn-success disabled' : 'btn-default'); ?>"><i class="fa fa-area-chart" aria-hidden="true"></i> <?php echo _l('chart'); ?></a>
             <a href="<?php echo admin_url('ma/campaigns?group=list'); ?>" class="btn pull-right <?php echo ($group == 'list' ? 'btn-success disabled' : 'btn-default'); ?>"><i class="fa fa-th-list" aria-hidden="true"></i> <?php echo _l('list'); ?></a>
          </div>
          <div class="clearfix"></div>
          <hr class="hr-panel-heading" />
          <div class="row mbot15">
             <div class="col-md-12">
                <h4 class="no-margin"><?php echo _l('campaigns_summary'); ?></h4>
             </div>
             <div class="col-md-2 col-xs-6 border-right">
                <h3 class="bold"><?php echo total_rows(db_prefix().'ma_campaigns'); ?></h3>
                <span class="text-dark"><?php echo _l('campaigns_summary_total'); ?></span>
             </div>
             <?php foreach($categories as $category){ ?>
             <div class="col-md-2 col-xs-6 border-right">
                <h3 class="bold"><?php echo total_rows(db_prefix().'ma_campaigns','category='.$category['id']); ?></h3>
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
<div class="modal fade campaign" id="clone_campaign_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <?php echo form_open(admin_url('ma/clone_campaign'), array('id' => 'clone-campaign-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo _l('clone_template'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                     <div class="col-md-12">
                        <?php echo form_hidden('id'); ?>
                        <?php echo render_input('name', 'name'); ?>
                     </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-primary"><?php echo _l('submit'); ?></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<?php init_tail(); ?>
<?php require 'modules/ma/assets/js/campaigns/manage_js.php';?>
