	
<?php if($status_convert == 'true'){ ?>	

	<!-- convert lead to costomer, proposal status to processing -->
  <li><a href="#"  class="convert_lead_to_customer" onclick="convert_lead_to_customer(<?php echo new_html_entity_decode($lead_id); ?>); return false;"><?php echo _l('proposal_convert_processing'); ?></a></li>
<?php }else{ ?>

	<!-- convert proposal status to processing -->
  <li><a href="#" data-template="invoice" onclick="proposal_convert_processing(this, '<?php echo new_html_entity_decode($proposal->id); ?>'); return false;"><?php echo _l('proposal_convert_processing'); ?></a></li>
<?php } ?>
