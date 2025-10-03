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
				
					<div class="row">
						<div class="col-md-12 <?php if(isset($check)){ echo 'no-padding';} ?>">
							<div class="panel_s">
								<div class="panel-body">
									<div class="panel-heading dflexy">
										<h4><?php echo _l('configure_checks'); ?></h4>
									</div>

									<?php echo form_open_multipart(admin_url('accounting/update_configure_checks'),array('id'=>'check-form')) ;?>
									<div class="col-12 bordered hide">
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
										<div class="row">
											<div class="col-md-3">

												<div class="checkbox checkbox-primary">
											        <input type="checkbox" id="show_bank_address" name="show_bank_address" value="show_bank_address" <?php if($check_type == 'type_1' || $check_type == 'type_3'){ echo 'checked'; } ?> >
											        <label for="show_bank_address"><?php echo _l('acc_show_bank_address'); ?>

											        </label>
											    </div>

											    <div class="checkbox checkbox-primary">
											        <input type="checkbox" id="show_2_signatures" name="show_2_signatures" value="show_2_signatures" <?php if($check_type == 'type_3' || $check_type == 'type_4'){ echo 'checked'; } ?>>
											        <label for="show_2_signatures"><?php echo _l('acc_show_2_signatures'); ?>

											        </label>
											    </div>
											</div>

											<div class="col-md-8" id="review_check_div">
												<img id="check_style_1" src="<?php echo site_url('modules/accounting/assets/images/check_style1.png'); ?>" class="<?php if($check_type != 'type_1'){ echo 'hide'; } ?>" alt="img">
												<img id="check_style_2" src="<?php echo site_url('modules/accounting/assets/images/check_style2.png'); ?>" class="<?php if($check_type != 'type_2'){ echo 'hide'; } ?>" alt="img">
												<img id="check_style_3" src="<?php echo site_url('modules/accounting/assets/images/check_style3.png'); ?>" class="<?php if($check_type != 'type_3'){ echo 'hide'; } ?>" alt="img">
												<img id="check_style_4" src="<?php echo site_url('modules/accounting/assets/images/check_style4.png'); ?>" class="<?php if($check_type != 'type_4'){ echo 'hide'; } ?>" alt="img">
											</div>
										</div>
									</div>

									<hr>

									<div class="col-md-12">
										<div class="row"> 

											<!--check #  -->
											<div class="col-md-4 ">
												<div class="col-md-12 text-center"><h4><?php echo _l('current_check_no'); ?></h4><hr class="mtop5 mbot10"></div>

												<div class="col-md-5">
													<div class="row">
														<div class="col-md-6">
															<strong class="text-uppercase"><?php echo _l('acc_left'); ?></strong>
														</div>

														<div class="col-md-6 text-right">
															<strong class="text-uppercase"><?php echo _l('acc_right'); ?></strong>
														</div>


														<div class="col-md-6">
															<input type="radio" class="ui-radio" id="acc_current_check_no_icon_a_a" name="acc_current_check_no_icon_a" <?php if($current_check_no_icon_a == 'a'){ echo 'checked';} ?>  value="a">
															<label for="acc_current_check_no_icon_a_a"><img width="18" data-value="a" src="<?php echo site_url('modules/accounting/assets/images/icon_a.svg'); ?>" alt="img"></label>
														</div>

														<div class="col-md-6 text-right">
															<input type="radio" class="ui-radio" id="acc_current_check_no_icon_b_a" name="acc_current_check_no_icon_b" <?php if($current_check_no_icon_b == 'a'){ echo 'checked'; } ?> value="a">
															<label for="acc_current_check_no_icon_b_a"><img width="18" data-value="a" src="<?php echo site_url('modules/accounting/assets/images/icon_a.svg'); ?>" alt="img"></label>
														</div>
													</div>

													<div class="row">
														<div class="col-md-6">
															<input type="radio" class="ui-radio" id="acc_current_check_no_icon_a_b" name="acc_current_check_no_icon_a" <?php if($current_check_no_icon_a == 'b'){ echo 'checked';} ?> value="b">
															<label for="acc_current_check_no_icon_a_b"><img width="18" data-value="b" src="<?php echo site_url('modules/accounting/assets/images/icon_b.svg'); ?>" alt="img"></label>
														</div>

														<div class="col-md-6 text-right">
															<input type="radio" class="ui-radio" id="acc_current_check_no_icon_b_b" name="acc_current_check_no_icon_b" <?php if($current_check_no_icon_b == 'b'){ echo 'checked'; } ?> value="b">
															<label for="acc_current_check_no_icon_b_b"><img width="18"  data-value="b" src="<?php echo site_url('modules/accounting/assets/images/icon_b.svg'); ?>" alt="img"></label>
														</div>
													</div>

													<div class="row">
														<div class="col-md-6">
															<input type="radio" class="ui-radio" id="acc_current_check_no_icon_a_c" name="acc_current_check_no_icon_a" <?php if($current_check_no_icon_a == 'c'){ echo 'checked';} ?> value="c">
															<label for="acc_current_check_no_icon_a_c"><img width="18"  data-value="c" src="<?php echo site_url('modules/accounting/assets/images/icon_c.svg'); ?>" alt="img"></label>
														</div>

														<div class="col-md-6 text-right">
															<input type="radio" class="ui-radio" id="acc_current_check_no_icon_b_c" name="acc_current_check_no_icon_b" <?php if($current_check_no_icon_b == 'c'){ echo 'checked'; } ?> value="c">
															<label for="acc_current_check_no_icon_b_c"><img width="18"  data-value="c" src="<?php echo site_url('modules/accounting/assets/images/icon_c.svg'); ?>" alt="img"></label>
														</div>
													</div>

													<div class="row">
														<div class="col-md-6">
															<input type="radio" class="ui-radio" id="acc_current_check_no_icon_a_d" name="acc_current_check_no_icon_a" <?php if($current_check_no_icon_a == 'd'){ echo 'checked';} ?> value="d">
															<label for="acc_current_check_no_icon_a_d"><img width="18"  data-value="d" src="<?php echo site_url('modules/accounting/assets/images/icon_d.svg'); ?>" alt="img"></label>
														</div>

														<div class="col-md-6 text-right">
															<input type="radio" class="ui-radio" id="acc_current_check_no_icon_b_d" name="acc_current_check_no_icon_b" <?php if($current_check_no_icon_b == 'd'){ echo 'checked'; } ?> value="d">
															<label for="acc_current_check_no_icon_b_d"><img width="18"  data-value="d" src="<?php echo site_url('modules/accounting/assets/images/icon_d.svg'); ?>" alt="img"></label>
														</div>
													</div>

													<div class="row">
														<div class="col-md-6">
															<input type="radio" class="ui-radio" id="acc_current_check_no_icon_a_e" name="acc_current_check_no_icon_a" <?php if($current_check_no_icon_a == 'e'){ echo 'checked';} ?> value="e">
															<label for="acc_current_check_no_icon_a_e"><?php echo _l('empty'); ?></label>
														</div>

														<div class="col-md-6 text-right">
															<input type="radio" class="ui-radio" id="acc_current_check_no_icon_b_e" name="acc_current_check_no_icon_b" <?php if($current_check_no_icon_b == 'e'){ echo 'checked'; } ?> value="e">
															<label for="acc_current_check_no_icon_b_e"><?php echo _l('empty'); ?></label>
														</div>
													</div>
												</div>

												<div class="col-md-7">
													<div class="col-md-12 text-center text-uppercase"><?php echo _l('acc_review').' '._l('check').' #'; ?></div>
													<div class="col-md-12">
															<fieldset class="fieldset">
																
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

												</div>

												
											</div>

											<!--routing #  -->
											<div class="col-md-4 ">
												<div class="col-md-12 text-center"><h4><?php echo _l('routing_number'); ?></h4><hr class="mtop5 mbot10"></div>

												<div class="col-md-5">
													<div class="row">
														<div class="col-md-6">
															<strong class="text-uppercase"><?php echo _l('acc_left'); ?></strong>
														</div>

														<div class="col-md-6 text-right">
															<strong class="text-uppercase"><?php echo _l('acc_right'); ?></strong>
														</div>


														<div class="col-md-6">
															<input type="radio" class="ui-radio" id="acc_routing_number_icon_a_a" name="acc_routing_number_icon_a" <?php if($routing_number_icon_a == 'a'){ echo 'checked';} ?>  value="a">
															<label for="acc_routing_number_icon_a_a"><img width="18" data-value="a" src="<?php echo site_url('modules/accounting/assets/images/icon_a.svg'); ?>" alt="img"></label>
														</div>

														<div class="col-md-6 text-right">
															<input type="radio" class="ui-radio" id="acc_routing_number_icon_b_a" name="acc_routing_number_icon_b" <?php if($routing_number_icon_b == 'a'){ echo 'checked'; } ?> value="a">
															<label for="acc_routing_number_icon_b_a"><img width="18" data-value="a" src="<?php echo site_url('modules/accounting/assets/images/icon_a.svg'); ?>" alt="img"></label>
														</div>
													</div>

													<div class="row">
														<div class="col-md-6">
															<input type="radio" class="ui-radio" id="acc_routing_number_icon_a_b" name="acc_routing_number_icon_a" <?php if($routing_number_icon_a == 'b'){ echo 'checked';} ?> value="b">
															<label for="acc_routing_number_icon_a_b"><img width="18" data-value="b" src="<?php echo site_url('modules/accounting/assets/images/icon_b.svg'); ?>" alt="img"></label>
														</div>

														<div class="col-md-6 text-right">
															<input type="radio" class="ui-radio" id="acc_routing_number_icon_b_b" name="acc_routing_number_icon_b" <?php if($routing_number_icon_b == 'b'){ echo 'checked'; } ?> value="b">
															<label for="acc_routing_number_icon_b_b"><img width="18"  data-value="b" src="<?php echo site_url('modules/accounting/assets/images/icon_b.svg'); ?>" alt="img"></label>
														</div>
													</div>

													<div class="row">
														<div class="col-md-6">
															<input type="radio" class="ui-radio" id="acc_routing_number_icon_a_c" name="acc_routing_number_icon_a" <?php if($routing_number_icon_a == 'c'){ echo 'checked';} ?> value="c">
															<label for="acc_routing_number_icon_a_c"><img width="18"  data-value="c" src="<?php echo site_url('modules/accounting/assets/images/icon_c.svg'); ?>" alt="img"></label>
														</div>

														<div class="col-md-6 text-right">
															<input type="radio" class="ui-radio" id="acc_routing_number_icon_b_c" name="acc_routing_number_icon_b" <?php if($routing_number_icon_b == 'c'){ echo 'checked'; } ?> value="c">
															<label for="acc_routing_number_icon_b_c"><img width="18"  data-value="c" src="<?php echo site_url('modules/accounting/assets/images/icon_c.svg'); ?>" alt="img"></label>
														</div>
													</div>

													<div class="row">
														<div class="col-md-6">
															<input type="radio" class="ui-radio" id="acc_routing_number_icon_a_d" name="acc_routing_number_icon_a" <?php if($routing_number_icon_a == 'd'){ echo 'checked';} ?> value="d">
															<label for="acc_routing_number_icon_a_d"><img width="18"  data-value="d" src="<?php echo site_url('modules/accounting/assets/images/icon_d.svg'); ?>" alt="img"></label>
														</div>

														<div class="col-md-6 text-right">
															<input type="radio" class="ui-radio" id="acc_routing_number_icon_b_d" name="acc_routing_number_icon_b" <?php if($routing_number_icon_b == 'd'){ echo 'checked'; } ?> value="d">
															<label for="acc_routing_number_icon_b_d"><img width="18"  data-value="d" src="<?php echo site_url('modules/accounting/assets/images/icon_d.svg'); ?>" alt="img"></label>
														</div>
													</div>

													<div class="row">
														<div class="col-md-6">
															<input type="radio" class="ui-radio" id="acc_routing_number_icon_a_e" name="acc_routing_number_icon_a" <?php if($routing_number_icon_a == 'e'){ echo 'checked';} ?> value="e">
															<label for="acc_routing_number_icon_a_e"><?php echo _l('empty'); ?></label>
														</div>

														<div class="col-md-6 text-right">
															<input type="radio" class="ui-radio" id="acc_routing_number_icon_b_e" name="acc_routing_number_icon_b" <?php if($routing_number_icon_b == 'e'){ echo 'checked'; } ?> value="e">
															<label for="acc_routing_number_icon_b_e"><?php echo _l('empty'); ?></label>
														</div>
													</div>
												</div>

												<div class="col-md-7">
													<div class="col-md-12 text-center text-uppercase"><?php echo _l('acc_review').' '._l('routing').' #'; ?></div>
													<div class="col-md-12">
														<fieldset class="fieldset">
															
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
												</div>

												
											</div>


											<!--account #  -->
											<div class="col-md-4 ">
												<div class="col-md-12 text-center"><h4><?php echo _l('bank_account'); ?></h4><hr class="mtop5 mbot10"></div>

												<div class="col-md-5">
													<div class="row">
														<div class="col-md-6">
															<strong class="text-uppercase"><?php echo _l('acc_left'); ?></strong>
														</div>

														<div class="col-md-6 text-right">
															<strong class="text-uppercase"><?php echo _l('acc_right'); ?></strong>
														</div>


														<div class="col-md-6">
															<input type="radio" class="ui-radio" id="acc_bank_account_icon_a_a" name="acc_bank_account_icon_a" <?php if($bank_account_icon_a == 'a'){ echo 'checked';} ?>  value="a">
															<label for="acc_bank_account_icon_a_a"><img width="18" data-value="a" src="<?php echo site_url('modules/accounting/assets/images/icon_a.svg'); ?>" alt="img"></label>
														</div>

														<div class="col-md-6 text-right">
															<input type="radio" class="ui-radio" id="acc_bank_account_icon_b_a" name="acc_bank_account_icon_b" <?php if($bank_account_icon_b == 'a'){ echo 'checked'; } ?> value="a">
															<label for="acc_bank_account_icon_b_a"><img width="18" data-value="a" src="<?php echo site_url('modules/accounting/assets/images/icon_a.svg'); ?>" alt="img"></label>
														</div>
													</div>

													<div class="row">
														<div class="col-md-6">
															<input type="radio" class="ui-radio" id="acc_bank_account_icon_a_b" name="acc_bank_account_icon_a" <?php if($bank_account_icon_a == 'b'){ echo 'checked';} ?> value="b">
															<label for="acc_bank_account_icon_a_b"><img width="18" data-value="b" src="<?php echo site_url('modules/accounting/assets/images/icon_b.svg'); ?>" alt="img"></label>
														</div>

														<div class="col-md-6 text-right">
															<input type="radio" class="ui-radio" id="acc_bank_account_icon_b_b" name="acc_bank_account_icon_b" <?php if($bank_account_icon_b == 'b'){ echo 'checked'; } ?> value="b">
															<label for="acc_bank_account_icon_b_b"><img width="18"  data-value="b" src="<?php echo site_url('modules/accounting/assets/images/icon_b.svg'); ?>" alt="img"></label>
														</div>
													</div>

													<div class="row">
														<div class="col-md-6">
															<input type="radio" class="ui-radio" id="acc_bank_account_icon_a_c" name="acc_bank_account_icon_a" <?php if($bank_account_icon_a == 'c'){ echo 'checked';} ?> value="c">
															<label for="acc_bank_account_icon_a_c"><img width="18"  data-value="c" src="<?php echo site_url('modules/accounting/assets/images/icon_c.svg'); ?>" alt="img"></label>
														</div>

														<div class="col-md-6 text-right">
															<input type="radio" class="ui-radio" id="acc_bank_account_icon_b_c" name="acc_bank_account_icon_b" <?php if($bank_account_icon_b == 'c'){ echo 'checked'; } ?> value="c">
															<label for="acc_bank_account_icon_b_c"><img width="18"  data-value="c" src="<?php echo site_url('modules/accounting/assets/images/icon_c.svg'); ?>" alt="img"></label>
														</div>
													</div>

													<div class="row">
														<div class="col-md-6">
															<input type="radio" class="ui-radio" id="acc_bank_account_icon_a_d" name="acc_bank_account_icon_a" <?php if($bank_account_icon_a == 'd'){ echo 'checked';} ?> value="d">
															<label for="acc_bank_account_icon_a_d"><img width="18"  data-value="d" src="<?php echo site_url('modules/accounting/assets/images/icon_d.svg'); ?>" alt="img"></label>
														</div>

														<div class="col-md-6 text-right">
															<input type="radio" class="ui-radio" id="acc_bank_account_icon_b_d" name="acc_bank_account_icon_b" <?php if($bank_account_icon_b == 'd'){ echo 'checked'; } ?> value="d">
															<label for="acc_bank_account_icon_b_d"><img width="18"  data-value="d" src="<?php echo site_url('modules/accounting/assets/images/icon_d.svg'); ?>" alt="img"></label>
														</div>
													</div>

													<div class="row">
														<div class="col-md-6">
															<input type="radio" class="ui-radio" id="acc_bank_account_icon_a_e" name="acc_bank_account_icon_a" <?php if($bank_account_icon_a == 'e'){ echo 'checked';} ?> value="e">
															<label for="acc_bank_account_icon_a_e"><?php echo _l('empty'); ?></label>
														</div>

														<div class="col-md-6 text-right">
															<input type="radio" class="ui-radio" id="acc_bank_account_icon_b_e" name="acc_bank_account_icon_b" <?php if($bank_account_icon_b == 'e'){ echo 'checked'; } ?> value="e">
															<label for="acc_bank_account_icon_b_e"><?php echo _l('empty'); ?></label>
														</div>
													</div>
												</div>

												<div class="col-md-7">
													<div class="col-md-12 text-center text-uppercase"><?php echo _l('acc_review').' '._l('account').' #'; ?></div>
													<div class="col-md-12">
															<fieldset class="fieldset">
														
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

												
											</div>

										</div>
									</div>

									<div class="col-md-12">
										<hr>
										<fieldset class="fieldset">
									
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