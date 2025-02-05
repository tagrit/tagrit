<div class="horizontal-scrollable-tabs preview-tabs-top">
	<div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
	<div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
	<div class="horizontal-tabs">
		<ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
			<li role="presentation" class="<?php echo ((isset($unit_tab)) && ($unit_tab == "general") ? 'active' : '') ?>">
				<a href="<?php echo admin_url('purchase/setting?group=currency_rates&tab=general'); ?>" aria-controls="prefix_code" role="tab">
					<i class="fa fa-wrench"></i>&nbsp;<?php echo _l('pur_general'); ?>
				</a>
			</li>
			<li role="presentation" class="<?php echo ((isset($unit_tab)) && ($unit_tab == "logs") ? 'active' : '') ?>">
				<a href="<?php echo admin_url('purchase/setting?group=currency_rates&tab=logs'); ?>" aria-controls="unit" role="tab">
					<i class="fa fa-list"></i>&nbsp;<?php echo _l('pur_currency_rate_logs'); ?>
				</a>
			</li>
		</ul>
	</div>
</div>


<!-- tab  content -->
<div class="tab-content">
	<div role="tabpanel" class="tab-pane active" id="prefix_code">
		<?php $this->load->view('includes/currencies/'.$unit_tab); ?>
	</div>
</div>
