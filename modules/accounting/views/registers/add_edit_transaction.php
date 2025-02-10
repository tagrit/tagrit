<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			

			<?php echo form_open_multipart(admin_url('accounting/register_add_edit_transaction'), array('id' => 'add_update_transaction','autocomplete'=>'off')); ?>

			<div class="col-md-12" >
				<div class="panel_s">
					
					<div class="panel-body">
						<h4 class="no-margin font-bold"><?php echo _l('acc_transactions'); ?></h4>
          				<hr />
						<div class="row">
							<div class="col-md-3 ">
								<?php echo render_date_input('from_date_filter',_l('from_date'), _d($from_date)); ?>
							</div>
							<div class="col-md-3 ">
								<?php echo render_date_input('to_date_filter',_l('to_date'), _d($to_date)); ?>
							</div>
							<div class="col-md-3 ">
								<?php echo render_input('number_filter',_l('number'),''); ?>
							</div>
							<div class="col-md-3 ">
								<?php echo render_select('payee_filter[]', $payee,array('id','label'),'payee', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
							</div>

							<div class="col-md-3 hide">
								<?php echo render_input('from_credit_filter',_l('from_payment_label'),'', 'number'); ?>
							</div>
							<div class="col-md-3 hide">
								<?php echo render_input('to_credit_filter',_l('to_payment_label'),'', 'number'); ?>
							</div>
							
							<div class="col-md-3 hide">
								<?php echo render_input('from_debit_filter',_l('from_deposit_label'),'', 'number'); ?>
							</div>
							<div class="col-md-3 hide">
								<?php echo render_input('to_debit_filter',_l('to_deposit_label'),'', 'number'); ?>
							</div>


							<div class="col-md-3 ">
								<?php echo render_select('account_filter[]',$accounts,array('id','name'),'Account ', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
							</div>

							<div class="col-md-3 ">
								<div class="form-group mtop25">
									<label> </label>
								<button type="button" class="btn btn-info reset_filter "><?php echo _l('reset_filter'); ?></button>
								</div>
							</div>


						</div>

						<!-- start tab -->
						<div class="modal-body">
							<div class="tab-content">
								
								<div class="row">
									<div class="col-md-12">
										<table class="table table-striped">

											<tr>
												<td width="30%"><?php echo '<strong>'._l('company').'</strong>' ?></td>
												<td width="70%"><?php echo new_html_entity_decode($company_name); ?></td>
											</tr>
											<tr>
												<td width="30%"><?php echo '<strong>'._l('acc_account').'</strong>' ?></td>
												<td width="70%"><?php echo new_html_entity_decode($account_name) ; ?></td>
											</tr>
										</table>
									</div>
								</div>
								<?php echo form_hidden('account', $account); ?>


								<div class="row">
        							<p class="font-italic text-danger">* <?php echo _l('handsontable_right_click_note'); ?></p>
									<div class="form"> 
										<div id="product_tab_hs">
										</div>
										<?php echo form_hidden('product_tab_hs'); ?>
									</div>

								</div>

								<div class="row">
									<div class="col-md-7"></div>
									<div class="col-md-5">
										<table class="table text-right">
											<tbody>
												<tr>
													<td><span class="bold"><?php echo _l('ending_balance'); ?></span>
													</td>
													<td class="ending_balance">
														<?php echo app_format_money($ending_balance, ''); ?>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="modal-footer">
								<a href="<?php echo admin_url('accounting/registers'); ?>"  class="btn btn-default mr-2 "><?php echo _l('close'); ?></a>
								<?php if(has_permission('accounting_registers', '', 'create') || has_permission('accounting_registers', '', 'edit')){ ?>
									<button type="button" class="btn btn-info pull-right add_user_register"><?php echo _l('submit'); ?></button>

								<?php } ?>
							</div>
						</div>

					</div>
				</div>
			</div>

			<?php echo form_close(); ?>
		</div>
	</div>
	<?php init_tail(); ?>
	
	<?php require 'modules/accounting/assets/js/registers/add_edit_transaction_js.php'; ?>

</body>
</html>
