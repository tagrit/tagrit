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
            <li class="<?php if($key == 0){echo 'active ';} ?>transaction_tab_<?php echo new_html_entity_decode($key); ?>">
              <a data-group="<?php echo new_html_entity_decode($gr); ?>" href="<?php echo admin_url('accounting/banking?group='.$gr); ?>">
                <?php if ($gr == 'bank_accounts') {
                    echo '<i class="fa fa-list-alt" aria-hidden="true"></i>';
                }elseif ($gr == 'banking_feeds') {
                    echo '<i class="fa fa-university" aria-hidden="true"></i>';
                }elseif ($gr == 'plaid_new_transaction') {
                    echo '<i class="fa fa-cogs" aria-hidden="true"></i>';
                }elseif ($gr == 'posted_bank_transactions') {
                    echo '<i class="fa fa-file-text" aria-hidden="true"></i>';
                }elseif ($gr == 'reconcile_bank_account') {
                    echo '<i class="fa fa-money-bill" aria-hidden="true"></i>';
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

<?php init_tail(); ?>
<?php require 'modules/accounting/assets/js/banking/bank_account_js.php';?>
