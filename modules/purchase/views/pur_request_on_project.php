<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
      <div class="col-md-12">
            <a href="<?php echo admin_url('purchase/pur_request?project='.$project->id); ?>" class="btn btn-info"><?php echo _l('new'); ?></a>
            <hr>
      </div>
	<div class="col-md-12">
        <?php echo form_hidden('_project_id', $project->id); ?>
		<?php render_datatable(array(
            _l('pur_rq_code'),
            _l('pur_rq_name'),
            _l('requester'),
            _l('department'),
            _l('request_date'),
            _l('status'),
            _l('po_no'),
            _l('options'),
            ),'table_pur_request'); ?>
	</div>
</div>

