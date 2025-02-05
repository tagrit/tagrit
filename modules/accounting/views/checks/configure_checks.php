<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php 


$routing_number_icon_a = 'a';
$routing_number_icon_b = 'a';

$bank_account_icon_a = 'a';
$bank_account_icon_b = 'a';

$current_check_no_icon_a = 'a';
$current_check_no_icon_b = 'a';

$check_type = 'type_1';


$acc_routing_number_icon_a = get_option('acc_routing_number_icon_a');
if($acc_routing_number_icon_a != ''){
	$routing_number_icon_a = $acc_routing_number_icon_a;
}
$acc_routing_number_icon_b = get_option('acc_routing_number_icon_b');
if($acc_routing_number_icon_b != ''){
	$routing_number_icon_b = $acc_routing_number_icon_b;
}

$acc_bank_account_icon_a = get_option('acc_bank_account_icon_a');
if($acc_bank_account_icon_a != ''){
	$bank_account_icon_a = $acc_bank_account_icon_a;
}
$acc_bank_account_icon_b = get_option('acc_bank_account_icon_b');
if($acc_bank_account_icon_b != ''){
	$bank_account_icon_b = $acc_bank_account_icon_b;
}

$acc_current_check_no_icon_a = get_option('acc_current_check_no_icon_a');
if($acc_current_check_no_icon_a != ''){
	$current_check_no_icon_a = $acc_current_check_no_icon_a;
}
$acc_current_check_no_icon_b = get_option('acc_current_check_no_icon_b');
if($acc_current_check_no_icon_b != ''){
	$current_check_no_icon_b = $acc_current_check_no_icon_b;
}

$acc_check_type = get_option('acc_check_type');

if($acc_check_type != ''){
	$check_type = $acc_check_type;
}

$list = [
	['id' => 'a', 'name' => 'A'],
	['id' => 'b', 'name' => 'B'],
	['id' => 'c', 'name' => 'C'],
	['id' => 'd', 'name' => 'D'],
	['id' => 'e', 'name' => 'E']
];
?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s mbot10">
					<div class="panel-body _buttons">
						
						<?php echo form_hidden('type',$type); 
						?>
		                  <div class="horizontal-scrollable-tabs preview-tabs-top">
		                   <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
		                   <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
		                   <div class="horizontal-tabs">

		                     <ul class="nav nav-tabs nav-tabs-horizontal no-margin" role="tablist">
		                           <?php if(has_permission('accounting_bills','','create')){ ?>
		                               <li class="<?php echo ($type == 'new_bill' ? 'active' : '') ?>">
		                                 <a href="<?php echo admin_url('accounting/bill'); ?>"><?php echo _l('add_new_bill'); ?></a>
		                               </li>
		                           <?php } ?>
		                         <li class="<?php echo ($type == 'unpaid' ? 'active' : '') ?>">
		                           <a href="<?php echo admin_url('accounting/bills?type=unpaid'); ?>"><?php echo _l('unpaid_bills'); ?></a>
		                         </li>
		                         <li class="<?php echo ($type == 'approved' ? 'active' : '') ?>">
		                              <a href="<?php echo admin_url('accounting/bills?type=approved'); ?>"><?php echo _l('approved_bills'); ?></a>
		                         </li>
		                         <li class="<?php echo ($type == 'check' ? 'active' : '') ?>">
		                           <a href="<?php echo admin_url('accounting/checks'); ?>"><?php echo _l('write_checks'); ?></a>
		                         </li>
		                         <li class="<?php echo ($type == 'paid' ? 'active' : '') ?>">
		                           <a href="<?php echo admin_url('accounting/bills?type=paid'); ?>"><?php echo _l('paid_bills'); ?></a>
		                         </li>
		                         <li class="<?php echo ($type == 'check_register' ? 'active' : '') ?>">
		                              <a href="<?php echo admin_url('accounting/check_register'); ?>"><?php echo _l('check_register'); ?></a>
		                         </li>
		                         <li class="<?php echo ($type == 'configure_checks' ? 'active' : '') ?>">
		                              <a href="<?php echo admin_url('accounting/configure_checks'); ?>"><?php echo _l('configure_checks'); ?></a>
		                         </li>
		                     </ul>
		                   </div>
		                 </div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 <?php if(isset($check)){ echo 'no-padding';} ?>">
							<div class="panel_s">
								<div class="panel-body">
									<div class="panel-heading dflexy">
										<span><?php echo _l('configure_checks'); ?></span>
									</div>
									<div class="row mtop25">
										<div class="col-md-6">
										</div>
										<div class="col-md-6">
											<h5><?php echo _l('configure_check_note_1'); ?></h5>
											<h5><?php echo _l('configure_check_note_2'); ?></h5>
										</div>
									</div>
									<?php echo form_open_multipart(admin_url('accounting/update_configure_checks'),array('id'=>'check-form')) ;?>
									<div class="col-12 bordered">
										<br>
										<div class="row">
											<div class="col-lg-6 col-sm-12 template-box text-center mb-3">
												<img src="<?php echo site_url('modules/accounting/assets/images/check_style1.png'); ?>" alt="img">
												<h6 class="d-flex align-items-center justify-content-center">
													<input type="radio" class="ui-radio" id="type_1" name="acc_check_type" <?php echo ($check_type == 'type_1') ? 'checked' : ''; ?>  value="type_1">
													<label for="type_1"><?php echo _l('type_1') ?></label>
												</h6>
											</div>
											<div class="col-lg-6 col-sm-12 template-box text-center mb-3">
												<img src="<?php echo site_url('modules/accounting/assets/images/check_style2.png'); ?>" alt="img">
												<h6 class="d-flex align-items-center justify-content-center">
													<input type="radio" class="ui-radio" id="type_2" name="acc_check_type" <?php echo ($check_type == 'type_2') ? 'checked' : ''; ?>  value="type_2">
													<label for="type_2"><?php echo _l('type_2') ?></label>
												</h6>
											</div>
										</div>
										<div class="row">
											<div class="col-lg-6 col-sm-12 template-box text-center mb-3">
												<img src="<?php echo site_url('modules/accounting/assets/images/check_style3.png'); ?>" alt="img">
												<h6 class="d-flex align-items-center justify-content-center">
													<input type="radio" class="ui-radio" id="type_3" name="acc_check_type" <?php echo ($check_type == 'type_3') ? 'checked' : ''; ?>  value="type_3">
													<label for="type_3"><?php echo _l('type_3') ?></label>
												</h6>
											</div>
											<div class="col-lg-6 col-sm-12 template-box text-center mb-3">
												<img src="<?php echo site_url('modules/accounting/assets/images/check_style4.png'); ?>" alt="img">
												<h6 class="d-flex align-items-center justify-content-center">
													<input type="radio" class="ui-radio" id="type_4" name="acc_check_type" <?php echo ($check_type == 'type_4') ? 'checked' : ''; ?>  value="type_4">
													<label for="type_4"><?php echo _l('type_4') ?></label>
												</h6>
											</div>
										</div>
									</div>	
									<div class="col-12">
										<div class="check-sample-setting">
											<div class="row">
												<div class="col-md-6">
													<table class="table">
														<tbody>
															<tr>
																<td><?php echo _l('current_check_no') ?></td>
																<td>
																	<div class="d-flex">
																		<?php 																		
																		echo render_select('acc_current_check_no_icon_a',$list,array('id','name'), '', $current_check_no_icon_a, ['data-live-search' => false], [], '', '', false);
																		?>
																		<div class="px-5">
																			<input type="text" class="text-center" disabled value="1234">																			
																		</div>
																		<?php 
																		echo render_select('acc_current_check_no_icon_b',$list,array('id','name'), '', $current_check_no_icon_b, ['data-live-search' => false], [], '', '', false);
																		?>
																	</div>
																</td>
															</tr>
															<tr>
																<td><?php echo _l('routing_number') ?></td>
																<td>
																	<div class="d-flex">
																		<?php 																	
																		echo render_select('acc_routing_number_icon_a',$list,array('id','name'), '', $routing_number_icon_a, ['data-live-search' => false], [], '', '', false);
																		?>
																		<div class="px-5">
																			<input type="text" class="text-center" disabled value="123456789">																			
																		</div>
																		<?php 
																		echo render_select('acc_routing_number_icon_b',$list,array('id','name'), '', $routing_number_icon_b, ['data-live-search' => false], [], '', '', false);
																		?>
																	</div>
																</td>
															</tr>
															<tr>
																<td><?php echo _l('bank_account') ?></td>
																<td>
																	<div class="d-flex">
																		<?php 																	
																		echo render_select('acc_bank_account_icon_a',$list,array('id','name'), '', $bank_account_icon_a, ['data-live-search' => false], [], '', '', false);
																		?>
																		<div class="px-5">
																			<input type="text" class="text-center" disabled value="1234567890">																			
																		</div>
																		<?php 
																		echo render_select('acc_bank_account_icon_b',$list,array('id','name'), '', $bank_account_icon_b, ['data-live-search' => false], [], '', '', false);
																		?>
																	</div>
																</td>
															</tr>
															
														</tbody>
													</table>
												</div>
												<div class="col-md-6">
													<div class="row">
														<div class="col-md-4">
															<fieldset class="fieldset">
																<legend><?php echo _l('check').' #'; ?></legend>
																<div class="row">
																	<div class="col-12 text-center">
																		<div class="d-flex justify-content-center current_check_no_icon">
																			<img width="18" class="exam-icon exam-icon-a<?php echo ($current_check_no_icon_a == 'a' ? '' : ' hide') ?>" data-value="a" src="<?php echo site_url('modules/accounting/assets/images/icon_a.svg'); ?>" alt="img">
																			<img width="18" class="exam-icon exam-icon-a<?php echo ($current_check_no_icon_a == 'b' ? '' : ' hide') ?>" data-value="b" src="<?php echo site_url('modules/accounting/assets/images/icon_b.svg'); ?>" alt="img">
																			<img width="18" class="exam-icon exam-icon-a<?php echo ($current_check_no_icon_a == 'c' ? '' : ' hide') ?>" data-value="c" src="<?php echo site_url('modules/accounting/assets/images/icon_c.svg'); ?>" alt="img">
																			<img width="18" class="exam-icon exam-icon-a<?php echo ($current_check_no_icon_a == 'd' ? '' : ' hide') ?>" data-value="d" src="<?php echo site_url('modules/accounting/assets/images/icon_d.svg'); ?>" alt="img">
																			<span class="h3 px-5 unset-top check-font-style1">1234</span>
																			<img width="18" class="exam-icon exam-icon-b<?php echo ($current_check_no_icon_b == 'a' ? '' : ' hide') ?>" data-value="a" src="<?php echo site_url('modules/accounting/assets/images/icon_a.svg'); ?>" alt="img">
																			<img width="18" class="exam-icon exam-icon-b<?php echo ($current_check_no_icon_b == 'b' ? '' : ' hide') ?>" data-value="b" src="<?php echo site_url('modules/accounting/assets/images/icon_b.svg'); ?>" alt="img">
																			<img width="18" class="exam-icon exam-icon-b<?php echo ($current_check_no_icon_b == 'c' ? '' : ' hide') ?>" data-value="c" src="<?php echo site_url('modules/accounting/assets/images/icon_c.svg'); ?>" alt="img">
																			<img width="18" class="exam-icon exam-icon-b<?php echo ($current_check_no_icon_b == 'd' ? '' : ' hide') ?>" data-value="d" src="<?php echo site_url('modules/accounting/assets/images/icon_d.svg'); ?>" alt="img">
																		</div>
																	</div>
																</div>
															</fieldset>
														</div>
														<div class="col-md-4">
															<fieldset class="fieldset">
																<legend><?php echo _l('routing').' #'; ?></legend>
																<div class="row">
																	<div class="col-12 text-center">
																		<div class="d-flex justify-content-center routing_number_icon">
																			<img width="18" class="exam-icon exam-icon-a<?php echo ($routing_number_icon_a == 'a' ? '' : ' hide') ?>" data-value="a" src="<?php echo site_url('modules/accounting/assets/images/icon_a.svg'); ?>" alt="img">
																			<img width="18" class="exam-icon exam-icon-a<?php echo ($routing_number_icon_a == 'b' ? '' : ' hide') ?>" data-value="b" src="<?php echo site_url('modules/accounting/assets/images/icon_b.svg'); ?>" alt="img">
																			<img width="18" class="exam-icon exam-icon-a<?php echo ($routing_number_icon_a == 'c' ? '' : ' hide') ?>" data-value="c" src="<?php echo site_url('modules/accounting/assets/images/icon_c.svg'); ?>" alt="img">
																			<img width="18" class="exam-icon exam-icon-a<?php echo ($routing_number_icon_a == 'd' ? '' : ' hide') ?>" data-value="d" src="<?php echo site_url('modules/accounting/assets/images/icon_d.svg'); ?>" alt="img">
																			<span class="h3 px-5 unset-top check-font-style1">123456789</span>
																			<img width="18" class="exam-icon exam-icon-b<?php echo ($routing_number_icon_b == 'a' ? '' : ' hide') ?>" data-value="a" src="<?php echo site_url('modules/accounting/assets/images/icon_a.svg'); ?>" alt="img">
																			<img width="18" class="exam-icon exam-icon-b<?php echo ($routing_number_icon_b == 'b' ? '' : ' hide') ?>" data-value="b" src="<?php echo site_url('modules/accounting/assets/images/icon_b.svg'); ?>" alt="img">
																			<img width="18" class="exam-icon exam-icon-b<?php echo ($routing_number_icon_b == 'c' ? '' : ' hide') ?>" data-value="c" src="<?php echo site_url('modules/accounting/assets/images/icon_c.svg'); ?>" alt="img">
																			<img width="18" class="exam-icon exam-icon-b<?php echo ($routing_number_icon_b == 'd' ? '' : ' hide') ?>" data-value="d" src="<?php echo site_url('modules/accounting/assets/images/icon_d.svg'); ?>" alt="img">
																		</div>
																	</div>
																</div>
															</fieldset>
														</div>
														<div class="col-md-4">
															<fieldset class="fieldset">
																<legend><?php echo _l('account').' #'; ?></legend>
																<div class="row">
																	<div class="col-12 text-center">
																		<div class="d-flex justify-content-center bank_account_icon">
																			<img width="18" class="exam-icon exam-icon-a<?php echo ($bank_account_icon_a == 'a' ? '' : ' hide') ?>" data-value="a" src="<?php echo site_url('modules/accounting/assets/images/icon_a.svg'); ?>" alt="img">
																			<img width="18" class="exam-icon exam-icon-a<?php echo ($bank_account_icon_a == 'b' ? '' : ' hide') ?>" data-value="b" src="<?php echo site_url('modules/accounting/assets/images/icon_b.svg'); ?>" alt="img">
																			<img width="18" class="exam-icon exam-icon-a<?php echo ($bank_account_icon_a == 'c' ? '' : ' hide') ?>" data-value="c" src="<?php echo site_url('modules/accounting/assets/images/icon_c.svg'); ?>" alt="img">
																			<img width="18" class="exam-icon exam-icon-a<?php echo ($bank_account_icon_a == 'd' ? '' : ' hide') ?>" data-value="d" src="<?php echo site_url('modules/accounting/assets/images/icon_d.svg'); ?>" alt="img">
																			<span class="h3 px-5 unset-top check-font-style1">1234567890</span>
																			<img width="18" class="exam-icon exam-icon-b<?php echo ($bank_account_icon_b == 'a' ? '' : ' hide') ?>" data-value="a" src="<?php echo site_url('modules/accounting/assets/images/icon_a.svg'); ?>" alt="img">
																			<img width="18" class="exam-icon exam-icon-b<?php echo ($bank_account_icon_b == 'b' ? '' : ' hide') ?>" data-value="b" src="<?php echo site_url('modules/accounting/assets/images/icon_b.svg'); ?>" alt="img">
																			<img width="18" class="exam-icon exam-icon-b<?php echo ($bank_account_icon_b == 'c' ? '' : ' hide') ?>" data-value="c" src="<?php echo site_url('modules/accounting/assets/images/icon_c.svg'); ?>" alt="img">
																			<img width="18" class="exam-icon exam-icon-b<?php echo ($bank_account_icon_b == 'd' ? '' : ' hide') ?>" data-value="d" src="<?php echo site_url('modules/accounting/assets/images/icon_d.svg'); ?>" alt="img">
																		</div>
																	</div>
																</div>
															</fieldset>
														</div>
													</div>
													<fieldset class="fieldset">
														<legend><?php echo _l('micr_font_equivalent') ?></legend>
														<div class="row">
															<div class="col-md-2">
																<span class="micr_font_equivalent">A=</span>
																<img width="18" src="<?php echo site_url('modules/accounting/assets/images/icon_a.svg'); ?>" alt="img">
															</div>
															<div class="col-md-2">
																<span class="micr_font_equivalent">B=</span>
																<img width="18" src="<?php echo site_url('modules/accounting/assets/images/icon_b.svg'); ?>" alt="img">
															</div>
															<div class="col-md-2">
																<span class="micr_font_equivalent">C=</span>
																<img width="18" src="<?php echo site_url('modules/accounting/assets/images/icon_c.svg'); ?>" alt="img">
															</div>
															<div class="col-md-2">
																<span class="micr_font_equivalent">D=</span>
																<img width="18" src="<?php echo site_url('modules/accounting/assets/images/icon_d.svg'); ?>" alt="img">
															</div>
															<div class="col-md-4">
																<span class="micr_font_equivalent">E=Leave Empty</span>
															</div>
														</div>
													</fieldset>
													<fieldset class="fieldset">
														<legend><?php echo _l('preview') ?></legend>
														<div class="row">
															<div class="col-12 d-flex justify-content-center">

																<div class="d-flex justify-content-center px-5 current_check_no_icon">
																	<img width="18" class="exam-icon exam-icon-a<?php echo ($current_check_no_icon_a == 'a' ? '' : ' hide') ?>" data-value="a" src="<?php echo site_url('modules/accounting/assets/images/icon_a.svg'); ?>" alt="img">
																	<img width="18" class="exam-icon exam-icon-a<?php echo ($current_check_no_icon_a == 'b' ? '' : ' hide') ?>" data-value="b" src="<?php echo site_url('modules/accounting/assets/images/icon_b.svg'); ?>" alt="img">
																	<img width="18" class="exam-icon exam-icon-a<?php echo ($current_check_no_icon_a == 'c' ? '' : ' hide') ?>" data-value="c" src="<?php echo site_url('modules/accounting/assets/images/icon_c.svg'); ?>" alt="img">
																	<img width="18" class="exam-icon exam-icon-a<?php echo ($current_check_no_icon_a == 'd' ? '' : ' hide') ?>" data-value="d" src="<?php echo site_url('modules/accounting/assets/images/icon_d.svg'); ?>" alt="img">
																	<span class="h3 px-5 unset-top check-font-style1">1234</span>
																	<img width="18" class="exam-icon exam-icon-b<?php echo ($current_check_no_icon_b == 'a' ? '' : ' hide') ?>" data-value="a" src="<?php echo site_url('modules/accounting/assets/images/icon_a.svg'); ?>" alt="img">
																	<img width="18" class="exam-icon exam-icon-b<?php echo ($current_check_no_icon_b == 'b' ? '' : ' hide') ?>" data-value="b" src="<?php echo site_url('modules/accounting/assets/images/icon_b.svg'); ?>" alt="img">
																	<img width="18" class="exam-icon exam-icon-b<?php echo ($current_check_no_icon_b == 'c' ? '' : ' hide') ?>" data-value="c" src="<?php echo site_url('modules/accounting/assets/images/icon_c.svg'); ?>" alt="img">
																	<img width="18" class="exam-icon exam-icon-b<?php echo ($current_check_no_icon_b == 'd' ? '' : ' hide') ?>" data-value="d" src="<?php echo site_url('modules/accounting/assets/images/icon_d.svg'); ?>" alt="img">
																</div>

																<div class="d-flex justify-content-center px-5 routing_number_icon">
																	<img width="18" class="exam-icon exam-icon-a<?php echo ($routing_number_icon_a == 'a' ? '' : ' hide') ?>" data-value="a" src="<?php echo site_url('modules/accounting/assets/images/icon_a.svg'); ?>" alt="img">
																	<img width="18" class="exam-icon exam-icon-a<?php echo ($routing_number_icon_a == 'b' ? '' : ' hide') ?>" data-value="b" src="<?php echo site_url('modules/accounting/assets/images/icon_b.svg'); ?>" alt="img">
																	<img width="18" class="exam-icon exam-icon-a<?php echo ($routing_number_icon_a == 'c' ? '' : ' hide') ?>" data-value="c" src="<?php echo site_url('modules/accounting/assets/images/icon_c.svg'); ?>" alt="img">
																	<img width="18" class="exam-icon exam-icon-a<?php echo ($routing_number_icon_a == 'd' ? '' : ' hide') ?>" data-value="d" src="<?php echo site_url('modules/accounting/assets/images/icon_d.svg'); ?>" alt="img">
																	<span class="h3 px-5 unset-top check-font-style1">123456789</span>
																	<img width="18" class="exam-icon exam-icon-b<?php echo ($routing_number_icon_b == 'a' ? '' : ' hide') ?>" data-value="a" src="<?php echo site_url('modules/accounting/assets/images/icon_a.svg'); ?>" alt="img">
																	<img width="18" class="exam-icon exam-icon-b<?php echo ($routing_number_icon_b == 'b' ? '' : ' hide') ?>" data-value="b" src="<?php echo site_url('modules/accounting/assets/images/icon_b.svg'); ?>" alt="img">
																	<img width="18" class="exam-icon exam-icon-b<?php echo ($routing_number_icon_b == 'c' ? '' : ' hide') ?>" data-value="c" src="<?php echo site_url('modules/accounting/assets/images/icon_c.svg'); ?>" alt="img">
																	<img width="18" class="exam-icon exam-icon-b<?php echo ($routing_number_icon_b == 'd' ? '' : ' hide') ?>" data-value="d" src="<?php echo site_url('modules/accounting/assets/images/icon_d.svg'); ?>" alt="img">
																</div>


																<div class="d-flex justify-content-center px-5 bank_account_icon">
																	<img width="18" class="exam-icon exam-icon-a<?php echo ($bank_account_icon_a == 'a' ? '' : ' hide') ?>" data-value="a" src="<?php echo site_url('modules/accounting/assets/images/icon_a.svg'); ?>" alt="img">
																	<img width="18" class="exam-icon exam-icon-a<?php echo ($bank_account_icon_a == 'b' ? '' : ' hide') ?>" data-value="b" src="<?php echo site_url('modules/accounting/assets/images/icon_b.svg'); ?>" alt="img">
																	<img width="18" class="exam-icon exam-icon-a<?php echo ($bank_account_icon_a == 'c' ? '' : ' hide') ?>" data-value="c" src="<?php echo site_url('modules/accounting/assets/images/icon_c.svg'); ?>" alt="img">
																	<img width="18" class="exam-icon exam-icon-a<?php echo ($bank_account_icon_a == 'd' ? '' : ' hide') ?>" data-value="d" src="<?php echo site_url('modules/accounting/assets/images/icon_d.svg'); ?>" alt="img">
																	<span class="h3 px-5 unset-top check-font-style1">1234567890</span>
																	<img width="18" class="exam-icon exam-icon-b<?php echo ($bank_account_icon_b == 'a' ? '' : ' hide') ?>" data-value="a" src="<?php echo site_url('modules/accounting/assets/images/icon_a.svg'); ?>" alt="img">
																	<img width="18" class="exam-icon exam-icon-b<?php echo ($bank_account_icon_b == 'b' ? '' : ' hide') ?>" data-value="b" src="<?php echo site_url('modules/accounting/assets/images/icon_b.svg'); ?>" alt="img">
																	<img width="18" class="exam-icon exam-icon-b<?php echo ($bank_account_icon_b == 'c' ? '' : ' hide') ?>" data-value="c" src="<?php echo site_url('modules/accounting/assets/images/icon_c.svg'); ?>" alt="img">
																	<img width="18" class="exam-icon exam-icon-b<?php echo ($bank_account_icon_b == 'd' ? '' : ' hide') ?>" data-value="d" src="<?php echo site_url('modules/accounting/assets/images/icon_d.svg'); ?>" alt="img">
																</div>


															</div>
														</div>
													</fieldset>
												</div>
												<div class="col-md-6">
													
												</div>
												<div class="col-md-6">
													
												</div>
											</div>
										</div>
									</div>
									<div class="col-12 text-right">
										<hr>
										<button class="btn btn-primary"><?php echo _l('submit'); ?></button>
									</div>
									<?php echo form_close(); ?>
								</div>
							</div>
						</div>


					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="content_cart hide"></div>

	<?php init_tail(); ?>
	<?php require 'modules/accounting/assets/js/checks/sample_check_js.php';?>
</body>
</html>