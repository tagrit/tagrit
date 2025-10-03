<div class="modal fade" id="currencyRateModal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

				<h4 class="modal-title"><?php echo pur_html_entity_decode(_l('loy_edit_currency_rate')); ?></h4>
			</div>


			<?php echo form_open(admin_url('purchase/update_currency_rate/'.$currency_rate->id), array('id' => 'update_currency_rate')); ?>
			<div class="modal-body">
				<div class="tab-content">
					<div class="row">
						<div class="col-md-12 mbot15">
      						<a href="#" onclick="get_currency_rate(<?php echo pur_html_entity_decode($currency_rate->id); ?>); return false;" class="btn btn-info pull-right" ><?php echo _l('get_online_currency_rates'); ?></a>
						</div>
						<div class="col-md-5">
							<div class="input-group" id="discount-total">
								<input type="text" readonly="true" class="form-control text-right" name="from_currency_rate" value="<?php if(isset($currency_rate)){ echo app_format_money($currency_rate->from_currency_rate,''); } ?>">
								<div class="input-group-addon">
									<div class="dropdown">
										<span class="discount-type-selected">
											<?php echo pur_get_currency_name_symbol($currency_rate->from_currency_id, 'symbol') ;?>
										</span>

									</div>
								</div>
							</div>
						</div>
						<div class="col-md-2">
							<h5 class="text-center">
								<?php echo _l('pur_to'); ?>
							</h5>
						</div>
						<div class="col-md-5">
							<div class="input-group" id="discount-total">
								<input type="text"  class="form-control text-right" name="to_currency_rate" value="<?php if(isset($currency_rate)){ echo $currency_rate->to_currency_rate; } ?>">
								<div class="input-group-addon">
									<div class="dropdown">
										<span class="discount-type-selected">
											<?php echo pur_get_currency_name_symbol($currency_rate->to_currency_id, 'symbol') ;?>
										</span>

									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default close_btn" data-dismiss="modal"><?php echo _l('close'); ?></button>
				<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
			</div>

		</div>

		<?php echo form_close(); ?>
	</div>
</div>
</div>
