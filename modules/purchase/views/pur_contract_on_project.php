<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
  <div class="col-md-12">
    <a href="<?php echo admin_url('purchase/contract?project='.$project->id); ?>" class="btn btn-info"><?php echo _l('new'); ?></a>
    <hr>
  </div>
	<div class="col-md-12">
		<?php echo form_hidden('_project_id', $project->id); ?>
		<?php render_datatable(array(
                      _l('department'),
                      _l('project'),
                      _l('service_category'),
                        _l('vendor'),
                        _l('contract_description'),
                        _l('contract_value'),
                        _l('payment_amount'),
                        _l('payment_cycle'),
                        _l('payment_terms'),
                        _l('start_date'),
                        _l('end_date'),
                        _l('status'),
                        ),'table_contracts'); ?>    
	</div>
</div>

