<div class="col-md-6">
	<?php 
		$vat_value = $vat != '' ? $vat : '';
	 ?>
    <?php echo render_input('vat','client_vat_number',$vat,'text'); ?>

</div>
