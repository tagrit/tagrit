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
            <li class="<?php if($key == 0){echo 'active ';} ?>setting_tab_<?php echo new_html_entity_decode($key); ?>">
              <a data-group="<?php echo new_html_entity_decode($gr); ?>" href="<?php echo admin_url('fleet/settings?group='.$gr); ?>">
                <?php if ($gr == 'general') {
                    echo '<i class="fa fa-th" aria-hidden="true"></i>';
                }elseif ($gr == 'vehicle_groups') {
                    echo '<i class="fa fa-list-alt" aria-hidden="true"></i>';
                }elseif ($gr == 'vehicle_types') {
                    echo '<i class="fa fa-list-ul" aria-hidden="true"></i>';
                }elseif ($gr == 'inspection_forms') {
                    echo '<i class="fa fa-file-text" aria-hidden="true"></i>';
                }elseif ($gr == 'criterias') {
                    echo '<i class="fa fa-file-text" aria-hidden="true"></i>';
                }elseif ($gr == 'insurance_categories') {
                    echo '<i class="fa fa-file-text" aria-hidden="true"></i>';
                }elseif ($gr == 'insurance_types') {
                    echo '<i class="fa fa-file-text" aria-hidden="true"></i>';
                }elseif ($gr == 'insurance_company') {
                    echo '<i class="fa fa-file-text" aria-hidden="true"></i>';
                }elseif ($gr == 'insurance_status') {
                    echo '<i class="fa fa-file-text" aria-hidden="true"></i>';
                }elseif ($gr == 'part_groups') {
                    echo '<i class="fa fa-list-alt" aria-hidden="true"></i>';
                }elseif ($gr == 'part_types') {
                    echo '<i class="fa fa-list-ul" aria-hidden="true"></i>';
                }   ?>
                <?php echo _l($gr); ?>
              </a>
            </li>
          <?php } ?>
        </ul>
      </div>
      <div class="col-md-9">
        <div class="panel_s">
           <div class="panel-body">
              <div>
                 <div class="tab-content">
                    <?php $this->load->view($tabs['view']); ?>
                 </div>
              </div>
           </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>