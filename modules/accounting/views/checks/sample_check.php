<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();

$routing_number_icon_a = 'a';
$routing_number_icon_b = 'a';

$bank_account_icon_a = 'a';
$bank_account_icon_b = 'a';

$current_check_no_icon_a = 'a';
$current_check_no_icon_b = 'a';

$check_type = 'type_1';


$acc_routing_number_icon_a = acc_get_option('acc_routing_number_icon_a', $company_id);
if($acc_routing_number_icon_a != ''){
	$routing_number_icon_a = $acc_routing_number_icon_a;
}
$acc_routing_number_icon_b = acc_get_option('acc_routing_number_icon_b', $company_id);
if($acc_routing_number_icon_b != ''){
	$routing_number_icon_b = $acc_routing_number_icon_b;
}

$acc_bank_account_icon_a = acc_get_option('acc_bank_account_icon_a', $company_id);
if($acc_bank_account_icon_a != ''){
	$bank_account_icon_a = $acc_bank_account_icon_a;
}
$acc_bank_account_icon_b = acc_get_option('acc_bank_account_icon_b', $company_id);
if($acc_bank_account_icon_b != ''){
	$bank_account_icon_b = $acc_bank_account_icon_b;
}

$acc_current_check_no_icon_a = acc_get_option('acc_current_check_no_icon_a', $company_id);
if($acc_current_check_no_icon_a != ''){
	$current_check_no_icon_a = $acc_current_check_no_icon_a;
}
$acc_current_check_no_icon_b = acc_get_option('acc_current_check_no_icon_b', $company_id);
if($acc_current_check_no_icon_b != ''){
	$current_check_no_icon_b = $acc_current_check_no_icon_b;
}

$acc_check_type = acc_get_option('acc_check_type', $company_id);
if($acc_check_type != ''){
	$check_type = $acc_check_type;
}
?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s mbot10">
					<div class="panel-body _buttons">
						<div class="panel-heading dflexy">
                     <span><?php echo _l($title); ?></span>
                  </div>
                  <hr />
						<?php echo form_hidden('type',$type); ?>
		                  <div class="horizontal-scrollable-tabs preview-tabs-top">
		                   <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
		                   <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
		                   <div class="horizontal-tabs">

		                     <ul class="nav nav-tabs nav-tabs-horizontal no-margin" role="tablist">
		                         <!-- <li class="">
		                           <a href="<?php echo admin_url('accounting/vendor'); ?>"><?php echo _l('add_new_vendor'); ?></a>
		                         </li> -->
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
		                         <!-- <li class="<?php echo ($type == 'voided' ? 'active' : '') ?>">
		                           <a href="<?php echo admin_url('accounting/bills?type=voided'); ?>"><?php echo _l('voided_bills'); ?></a>
		                         </li> -->
		                         <li class="<?php echo ($type == 'voided_checks' ? 'active' : '') ?>">
		                           <a href="<?php echo admin_url('accounting/voided_checks'); ?>"><?php echo _l('voided_checks'); ?></a>
		                         </li>
		                         <li class="<?php echo ($type == 'sample_check' ? 'active' : '') ?>">
		                           <a href="<?php echo admin_url('accounting/sample_check'); ?>"><?php echo _l('sample_check'); ?></a>
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
						<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
						<div class="col-md-12 <?php if(isset($check)){ echo 'no-padding';} ?>">
							<div class="panel_s">
								<div class="panel-body">
									<input type="hidden" name="max_check_number" value="4">
									<?php echo form_hidden('bank_account_id', isset($check) ? $check->bank_account : ''); ?>
									<?php echo form_open_multipart(admin_url('accounting/sample_check'),array('id'=>'check-form')) ;?>
									<?php $check_id = isset($check) ? $check->id : '';
									echo form_hidden('id', $check_id); ?>
									<div class="panel-heading dflexy">
										<span><?php echo _l('sample_check'); ?></span>
									</div>
									<div class="row">
										<div class="col-md-12">
											<hr class="hr-panel-heading" />
										</div>
									</div>
									<?php if(!isset($check) || (isset($check) && isset($is_edit))){ ?>
										<div class="row">
											<div class="col-md-12">
												<a href="#" class="btn btn-default pull-right" onclick="print_multiple_sample_check(); return false;" data-toggle="tooltip" data-title="<?php echo _l('print_multiple_saved_checks_note'); ?>"><?php echo _l('print_multiple_checks'); ?></a>
												<a href="#" class="btn btn-default mright5 pull-right" onclick="print_sample_check(); return false;" data-toggle="tooltip" data-title="<?php echo _l('save_print_now_note'); ?>"><?php echo _l('print_check'); ?></a>
											</div>
										</div>
										<hr class="hr-panel-heading" />

										<br>
									<?php } ?>

									<div class="row text-center">
										<div class="col-md-12">
											<h3>Sample Check</h3>
											<p>Print this sample check on plain paper to confirm the correct way to load your checks into your printer.</p
											>
										</div>
									</div>

									<div class="col-md-12">
										<div class="row frcard">
											<?php $card_image = site_url('modules/accounting/assets/images/check_card.png') ?>
											<div class="col-md-10 check-card" style="background: #f0f0f0 url('<?php echo new_html_entity_decode($card_image); ?>')">
												<div class="row mbot15">
													<div class="col-md-5">
														<div class="address<?php echo ((isset($check) && $check->include_company_name_address != 1) ? ' hide' : '') ?>">
															<h4 class="no-margin">
																XXXXX
															</h4>
															<strong>
																XXXXX
															</strong>
															<br>
															<strong>
																XXXXX
															</strong>
														</div>
													</div>
													<div class="col-md-4">
														<div class="bank-name">
															<h4 class="no-margin">
																XXXXX
															</h4>
															<strong>
																XXXXX
															</strong>
															<br>
															<strong>
																XXXXX
															</strong>
														</div>
													</div>
													<div class="col-md-3">
														<div class="row">
															<div class="col-md-12 mbot25">
															
														<h4 class="pull-right bold check_number_label no-margin">XXXX</h4>
														</div>
														</div>

														<div class="row mtop25">
															<div class="col-md-3">
																<strong class="mtop10 mright10">Date</strong>
															</div>
															<div class="col-md-9">
															<div class="col-md-12 check-border-bottom no-padding">
																<strong class="pull-right mbot5">
																	XXXXX
																</strong>
															</div>
															</div>
														</div>
													</div>
												</div>
												<div class="row mtop5">
													<div class="col-md-9">
														<div class="row">
															<div class="col-md-3">
																<strong class="mtop10 mright10"><?php echo _l('pay_to_the_order_of'); ?></strong>
															</div>
															<div class="col-md-9 check-border-bottom">
																<strong class="mbot5">
																	XXXXXXXXXX
																</strong>
															</div>
														</div>
													</div>
													<div class="col-md-3">
														<div class="col-md-3">
															<strong class="mtop10 mright10"><?php echo new_html_entity_decode($currency->symbol); ?></strong>
														</div>
														<div class="col-md-9 check-border-bottom no-padding">
															<strong class="pull-right mbot5">
																XXXXX
															</strong>
														</div>
													</div>
												</div>
												<div class="row mtop15 mbot5">
													<div class="col-md-1">
													</div>
													<div class="col-md-8 check-border-bottom bold">
															<strong>
																XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
															</strong>
													</div>
													<div class="col-md-3 bold">
														<?php echo 'Dollars'; ?>
													</div>
												</div>

												<div class="row mtop15">
													<div class="col-md-1"></div>
													<div class="col-md-8" style="height:50px">
														<?php if($check_type == 'type_3' || $check_type == 'type_1' || $check_type == ''){ ?>
															<h4 class="no-margin bold">
																XXXXX
															</h4>
															<b>
																XXXXXXXXXXXXXXXXXXXXXX<br>XXXXXXXXXXXXXXXXXXXXXX
															</b>
												      	<?php } ?>
													</div>
													<div class="col-md-3">
														<?php if($check_type == 'type_3' || $check_type == 'type_4'){ ?>
															<div class="col-md-12 check-border-bottom check-sign">
																<h4 class=" ">
																	<b>
																		XXXXXXXXXXXXXX
																	</b>
																</h4>
															</div>
												      	<?php } ?>
													</div>
												</div>
												<div class="row" style="height: 50px;">
													<div class="col-md-9">
														<div class="row">
															<div class="col-md-12">
																<div class="form-inline mtop20"  style="display: flex; width: 100%;">
																	<label for="memo" class=" mright10"><?php echo _l('acc_memo') ?></label>
																	<div class="col-md-12 check-border-bottom check-sign">
																			<strong>
																				XXXXX
																			</strong>
																	</div>
																</div>
															</div>
															
														</div>
													</div>
													<div class="col-md-3">
														<div class="col-md-12 check-border-bottom check-sign">
															<h4 class=" ">
																<b>
																	XXXXXXXXXXXXXX
																</b>
															</h4>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-md-12 mtop25">
																<h4 class="card-number mtop5 bold d-flex">
																	<div class="px-10">
																		<?php if($current_check_no_icon_a != ''){ ?>
																			<img width="17" class="exam-icon exam-icon-a" data-value="a" src="<?php echo site_url('modules/accounting/assets/images/icon_'.$current_check_no_icon_a.'.svg'); ?>" alt="img">
																		<? } ?>
																		<span style="">XXXX</span>   
																		<?php if($current_check_no_icon_b != ''){ ?>
																			<img width="17" class="exam-icon exam-icon-b" data-value="d" src="<?php echo site_url('modules/accounting/assets/images/icon_'.$current_check_no_icon_b.'.svg'); ?>" alt="img">
																		<? } ?>
																	</div>

																	<div class="px-10">
																		<?php if($routing_number_icon_a != ''){ ?>
																			<img width="17" class="exam-icon exam-icon-a" data-value="a" src="<?php echo site_url('modules/accounting/assets/images/icon_'.$routing_number_icon_a.'.svg'); ?>" alt="img">
																		<? } ?>
																		<span style="">XXXXXXXXX</span>
																		<?php if($routing_number_icon_b != ''){ ?>
																			<img width="17" class="exam-icon exam-icon-b" data-value="d" src="<?php echo site_url('modules/accounting/assets/images/icon_'.$routing_number_icon_b.'.svg'); ?>" alt="img">
																		<? } ?>
																	</div>
																	<div class="px-10">
																		<?php if($bank_account_icon_a != ''){ ?>
																			<img width="17" class="exam-icon exam-icon-a" data-value="b" src="<?php echo site_url('modules/accounting/assets/images/icon_'.$bank_account_icon_a.'.svg'); ?>" alt="img">
																		<? } ?>
																		<span style="">XXXXXXXXXX</span>
																		<?php if($bank_account_icon_b != ''){ ?>
																			<img width="17" class="exam-icon exam-icon-b" data-value="d" src="<?php echo site_url('modules/accounting/assets/images/icon_'.$bank_account_icon_b.'.svg'); ?>" alt="img">
																		<? } ?>
																	</div>
																</h4>
															</div>
												</div>
												<div class="row mbot15"></div>
											</div>
										</div>

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