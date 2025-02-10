<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();?>

<div id="wrapper">
  <div class="content">
      <div class="panel_s mtop15">
        <div class="panel-body">
          <h4 class="customer-profile-group-heading"><?php echo _l($title); ?></h4>
            <?php echo form_hidden('csrf_token_hash', $this->security->get_csrf_hash()); ?>
          
            <?php echo form_open(admin_url('ma/workflow_builder_save'),array('id'=>'workflow-form','autocomplete'=>'off')); ?>
            <?php echo form_hidden('campaign_id',(isset($campaign) ? $campaign->id : '') ); ?>
            <?php echo form_hidden('workflow',(isset($campaign) ? $campaign->workflow : '')); ?>
            <?php echo form_close(); ?>
          <div class="row wrapper">
          <div class="col-md-2 action-tab">
            <div class="drag-drawflow" draggable="true" ondragstart="drag(event)" data-node="flow_start">
              <span class="text-success glyphicon glyphicon-log-in"> </span><span class="text-success"> <?php echo _l('flow_start'); ?></span>
            </div>
            <div class="drag-drawflow" draggable="true" ondragstart="drag(event)" data-node="condition">
              <span class="text-danger glyphicon glyphicon-fullscreen"> </span><span class="text-danger"> <?php echo _l('condition'); ?></span>
            </div>
            <div class="drag-drawflow" draggable="true" ondragstart="drag(event)" data-node="action">
              <span class="text-info glyphicon glyphicon-retweet"> </span><span class="text-info"> <?php echo _l('action'); ?></span>
            </div>
            <div class="drag-drawflow" draggable="true" ondragstart="drag(event)" data-node="filter">
              <span class="text-warning glyphicon glyphicon-random"> </span><span class="text-warning"> <?php echo _l('filter'); ?></span>
            </div>
            <div class="drag-drawflow" draggable="true" ondragstart="drag(event)" data-node="sms">
              <span class="text-default glyphicon glyphicon-phone"> </span><span class="text-default"> <?php echo _l('sms'); ?></span>
            </div>
            <div class="drag-drawflow" draggable="true" ondragstart="drag(event)" data-node="email">
              <span class="text-primary glyphicon glyphicon-envelope"> </span><span class="text-primary"> <?php echo _l('email'); ?></span>
            </div>
          </div>
          <div class="col-md-10">
            <div id="drawflow" ondrop="drop(event)" ondragover="allowDrop(event)">
              <div class="btn-export" onclick="save_workflow(); return false;"><?php echo _l('save'); ?></div>
              <div class="btn-clear" onclick="editor.clearModuleSelected()">Clear</div>
            </div>
          </div>
        </div>
        </div>
      </div>
  </div>
</div>

<?php init_tail(); ?>
<?php require 'modules/ma/assets/js/campaigns/workflow_builder_js.php';?>

