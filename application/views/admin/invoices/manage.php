<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div id="vueApp">
			<div class="row">
				<div class="col-md-12 tw-mb-3">
					<h4 class="tw-my-0 tw-font-bold tw-text-xl">
						<?= _l('invoices'); ?>
					</h4>
					<?php if (! isset($project)) { ?>
					<a href="<?= admin_url('invoices/recurring'); ?>"
						class="tw-mr-4">
						<?= _l('invoices_list_recurring'); ?>
						&rarr;
					</a>
					<?php } ?>
					<a href="#"
						class="invoices-total tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700"
						onclick="slideToggle('#stats-top'); init_invoices_total(true); return false;">
						<?= _l('view_financial_stats'); ?>
					</a>
				</div>
				<div class="col-md-12">
					<?php $this->load->view('admin/invoices/quick_stats'); ?>
				</div>
				<?php include_once APPPATH . 'views/admin/invoices/filter_params.php'; ?>
				<?php $this->load->view('admin/invoices/list_template'); ?>
			</div>
		</div>
	</div>
</div>
<?php $this->load->view('admin/includes/modals/sales_attach_file'); ?>
<div id="modal-wrapper"></div>
<script>
	var hidden_columns = [2, 6, 7, 8];
</script>
<?php init_tail(); ?>
<script>
	$(function() {
		init_invoice();
	});
</script>
</body>

</html>