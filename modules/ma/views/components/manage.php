<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-3">
        <ul class="nav navbar-pills navbar-pills-flat nav-tabs nav-stacked customer-tabs" role="tablist">
          <?php
          foreach($tab as $key => $gr){
            ?>
            <li class="<?php if($key == 0){echo 'active ';} ?>setting_tab_<?php echo html_entity_decode($key); ?>">
              <a data-group="<?php echo html_entity_decode($gr); ?>" href="<?php echo admin_url('ma/components?group='.$gr); ?>">
                <?php if ($gr == 'assets') {
                    echo '<i class="fa fa-cube" aria-hidden="true"></i>';
                }elseif ($gr == 'forms') {
                    echo '<i class="fa fa-window-maximize" aria-hidden="true"></i>';
                } ?>
                <?php echo _l($gr); ?>
              </a>
            </li>
          <?php } ?>
        </ul>
      </div>
      <div class="col-md-9">
        <div class="panel_s">
           <div class="panel-body">
                <div class="tab-content">
                    <?php $this->load->view($tabs['view']); ?>
                </div>
           </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>