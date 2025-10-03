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
            <li class="<?php if($key == 0){echo 'active ';} ?>transaction_tab_<?php echo html_entity_decode($key); ?>">
              <a data-group="<?php echo html_entity_decode($gr); ?>" href="<?php echo admin_url('sage_accounting_integration/manage?group='.$gr); ?>">
                <?php if ($gr == 'customers') {
                    echo '<i class="fa fa-user" aria-hidden="true"></i>';
                }elseif ($gr == 'invoices') {
                    echo '<i class="fa fa-file-text" aria-hidden="true"></i>';
                }elseif ($gr == 'expenses') {
                    echo '<i class="fa fa-file-invoice-dollar" aria-hidden="true"></i>';
                }elseif ($gr == 'payments') {
                    echo '<i class="fa fa-credit-card" aria-hidden="true"></i>';
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
<!-- box loading -->
<div id="box-loadding"></div>
<?php init_tail(); ?>
