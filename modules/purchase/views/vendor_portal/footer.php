<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $viewuri = $_SERVER['REQUEST_URI']; ?>
<?php if((strpos($viewuri, 'purchase/vendors_portal/add_update_invoice') === false) && (strpos($viewuri, 'purchase/vendors_portal/add_update_quotation') === false) ){ ?>
	<div class="pusher"></div>
	<footer class=" footer">
		<div class="container">
			<div class="row">
				<div class="col-md-12 text-center">
					<span class="copyright-footer"><?php echo date('Y'); ?> <?php echo _l('clients_copyright', get_option('companyname')).' - <a href="'.site_url('purchase/vendors_portal/terms_and_conditions').'">'._l('terms_and_conditions').'</a>'; ?></span>
				</div>
			</div>
		</div>
	</footer>
<?php } ?>
