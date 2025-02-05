<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-6">
        <div class="panel_s">
          <div class="panel-body">
             <?php $csrf = array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash(),
              );
              ?>
              <input type="hidden" id="csrf_token_name" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
          	   <?php if($payment_invoice->approval_status == 1){ ?>
                    <div class="ribbon info"><span class="fontz9" ><?php echo _l('purchase_not_yet_approve'); ?></span></div>
                <?php }elseif($payment_invoice->approval_status == 2){ ?>
                  <div class="ribbon success"><span><?php echo _l('purchase_approved'); ?></span></div>
                <?php }elseif($payment_invoice->approval_status == 3){ ?>  
                  <div class="ribbon danger"><span><?php echo _l('purchase_reject'); ?></span></div>
                <?php } ?>

          	<h4 class="pull-left "><?php echo _l('payment_for').' '; ?><a href="<?php echo admin_url('purchase/purchase_invoice/'. $payment_invoice->pur_invoice); ?>"><?php echo pur_html_entity_decode($invoice->invoice_number); ?></a></h4>
					<div class="clearfix"></div>
				<hr class="hr-panel-heading" />
          	<div class="col-md-12">
          		
				<div class="row">
					<div class="col-md-6 col-sm-6">
						<address>
							<?php echo format_organization_info(); ?>
						</address>
					</div>
				
					</div>
					<div class="col-md-12 text-center">
						<h3 class="text-uppercase"><?php echo _l('payment_receipt'); ?></h3>
					</div>
					<div class="col-md-12 mtop30">
						<div class="row">
							<div class="col-md-6">
								<p><?php echo _l('payment_date'); ?> <span class="pull-right bold"><?php echo _d($payment_invoice->date); ?></span></p>
								<hr />
								<p><?php echo _l('payment_view_mode'); ?>
								<span class="pull-right bold">
									
									<?php if(!empty($payment_invoice->paymentmode)){
										echo  get_payment_mode_name_by_id($payment_invoice->paymentmode);
									}
									?>
								</span></p>
								<?php if(!empty($payment_invoice->transactionid)) { ?>
									<hr />
									<p><?php echo _l('payment_transaction_id'); ?>: <span class="pull-right bold"><?php echo pur_html_entity_decode($payment_invoice->transactionid); ?></span></p>
								<?php } ?>
							</div>
							<div class="clearfix"></div>
							<div class="col-md-6">
								<div class="payment-preview-wrapper">
									<?php echo _l('payment_total_amount'); ?><br />
									<?php echo app_format_money($payment_invoice->amount,$base_currency->name); ?>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-12 mtop30">
					<h4><?php echo _l('payment_for_string'); ?></h4>
					<div class="table-responsive">
					<table class="table table-borderd table-hover">
						<thead>
							<tr>
								<th><?php echo _l('payment_table_invoice_number'); ?></th>
								<th><?php echo _l('payment_table_invoice_date'); ?></th>
								<th><?php echo _l('payment_table_invoice_amount_total'); ?></th>
								<th><?php echo _l('payment_table_payment_amount_total'); ?></th>
								<?php if($invoice->payment_status != 'paid') { ?>
										<th><span class="text-danger"><?php echo _l('invoice_amount_due'); ?></span></th>
									<?php } ?>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><?php echo get_pur_invoice_number($payment_invoice->pur_invoice); ?></td>
									<td><?php echo _d($invoice->invoice_date); ?></td>
									<td><?php echo app_format_money($invoice->total, $base_currency->name); ?></td>
									<td><?php echo app_format_money($payment_invoice->amount, $base_currency->name); ?></td>
									<?php if($invoice->payment_status != 'paid') { ?>
											<td class="text-danger">
												<?php echo app_format_money(purinvoice_left_to_pay($invoice->id), $base_currency->name); ?>
											</td>
										<?php } ?>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
			</div>

			

        </div>
      </div>
    </div>

    <div class="col-md-6">
     <div class="panel_s">
      <div class="panel-body">
      	<h4 class="pull-left "><?php echo _l('pur_approval_infor'); ?></h4>
					<div class="clearfix"></div>
				<hr class="hr-panel-heading" />

      <div class="project-overview-right">
        <?php if(count($list_approve_status) > 0){ ?>
          
         <div class="row">
           <div class="col-md-12 project-overview-expenses-finance">
            <?php 
              $this->load->model('staff_model');
              $enter_charge_code = 0;
            foreach ($list_approve_status as $value) {
              $value['staffid'] = explode(', ',$value['staffid'] ?? '');
              if($value['action'] == 'sign'){
             ?>
             <div class="col-md-6 apr_div">
                 <p class="text-uppercase text-muted no-mtop bold">
                  <?php
                  $staff_name = '';
                  $st = _l('status_0');
                  $color = 'warning';
                  foreach ($value['staffid'] as $key => $val) {
                    if($staff_name != '')
                    {
                      $staff_name .= ' or ';
                    }
                    $staff_name .= $this->staff_model->get($val)->firstname;
                  }
                  echo pur_html_entity_decode($staff_name); 
                  ?></p>
                 <?php if($value['approve'] == 2){ 
                  ?>
                  <img src="<?php echo site_url(PURCHASE_PATH.'payment_invoice/signature/'.$payment_invoice->id.'/signature_'.$value['id'].'.png'); ?>" class="img_style">
                   <br><br>
                 <p class="bold text-center text-success"><?php echo _l('signed').' '._dt($value['date']); ?></p>
                 <?php } ?> 
                    
            </div>
            <?php }else{ ?>
            <div class="col-md-6 apr_div">
                 <p class="text-uppercase text-muted no-mtop bold">
                  <?php
                  $staff_name = '';
                  foreach ($value['staffid'] as $key => $val) {
                    if($staff_name != '')
                    {
                      $staff_name .= ' or ';
                    }
                    $staff_name .= $this->staff_model->get($val)->firstname;
                  }
                  echo pur_html_entity_decode($staff_name); 
                  ?></p>
                 <?php if($value['approve'] == 2){ 
                  ?>
                  <img src="<?php echo site_url(PURCHASE_PATH.'approval/approved.png'); ?>" class="img_style">
                 <?php }elseif($value['approve'] == 3){ ?>
                    <img src="<?php echo site_url(PURCHASE_PATH.'approval/rejected.png'); ?>" class="img_style">
                <?php } ?> 
                <br><br>  
                 <p><?php echo pur_html_entity_decode($value['note']) ?></p>  
                <p class="bold text-center text-<?php if($value['approve'] == 2){ echo 'success'; }elseif($value['approve'] == 3){ echo 'danger'; } ?>"><?php echo _dt($value['date']); ?></p> 
            </div>
            <?php }
            } ?>
           </div>
        </div>
        
        <?php } ?>
        </div>
        <div class="pull-right">
            <?php 
            if($check_appr && $check_appr != false){
            if($payment_invoice->approval_status != 2 && ($check_approve_status == false || $check_approve_status == 'reject')){ ?>
        <a data-toggle="tooltip" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-success lead-top-btn lead-view" data-placement="top" href="#" onclick="send_request_approve(<?php echo pur_html_entity_decode($payment_invoice->id); ?>); return false;"><?php echo _l('send_request_approve_pur'); ?></a>
      <?php } }
        if(isset($check_approve_status['staffid'])){
            ?>
            <?php 
        if(in_array(get_staff_user_id(), $check_approve_status['staffid']) && !in_array(get_staff_user_id(), $get_staff_sign)){ ?>
            <div class="btn-group" >
                   <a href="#" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo _l('approve'); ?><span class="caret"></span></a>
                   <ul class="dropdown-menu dropdown-menu-right ul_style">
                    <li>
                      <div class="col-md-12">
                        <?php echo render_textarea('reason', 'reason'); ?>
                      </div>
                    </li>
                      <li>
                        <div class="row text-right col-md-12">
                          <a href="#" data-loading-text="<?php echo _l('wait_text'); ?>" onclick="approve_request(<?php echo pur_html_entity_decode($payment_invoice->id); ?>); return false;" class="btn btn-success mright15"><?php echo _l('approve'); ?></a>
                         <a href="#" data-loading-text="<?php echo _l('wait_text'); ?>" onclick="deny_request(<?php echo pur_html_entity_decode($payment_invoice->id); ?>); return false;" class="btn btn-warning"><?php echo _l('deny'); ?></a></div>
                      </li>
                   </ul>
                </div>
          <?php }
            ?>
            
          <?php
           if(in_array(get_staff_user_id(), $check_approve_status['staffid']) && in_array(get_staff_user_id(), $get_staff_sign)){ ?>
            <button onclick="accept_action();" class="btn btn-success pull-right action-button"><?php echo _l('e_signature_sign'); ?></button>
          <?php }
            ?>
            <?php 
             }
            ?>
          </div>
         </div>
       </div>
     </div>

  </div>
</div>

<div class="modal fade" id="add_action" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         
        <div class="modal-body">
         <p class="bold" id="signatureLabel"><?php echo _l('signature'); ?></p>
            <div class="signature-pad--body">
              <canvas id="signature" height="130" width="550"></canvas>
            </div>
            <input type="text" class="ip_style" tabindex="-1" name="signature" id="signatureInput">
            <div class="dispay-block">
              <button type="button" class="btn btn-default btn-xs clear" tabindex="-1" onclick="signature_clear();"><?php echo _l('clear'); ?></button>
            
            </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('cancel'); ?></button>
           <button onclick="sign_request(<?php echo pur_html_entity_decode($payment_invoice->id); ?>);" data-loading-text="<?php echo _l('wait_text'); ?>" autocomplete="off" class="btn btn-success"><?php echo _l('e_signature_sign'); ?></button>
          </div>

      </div><!-- /.modal-content -->
   </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php init_tail(); ?>
</body>
</html>
<?php require 'modules/purchase/assets/js/payment_invoice_js.php';?>