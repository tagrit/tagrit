<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-12">
        <a href="<?php echo admin_url('purchase/pur_order?project='.$project->id); ?>" class="btn btn-info"><?php echo _l('new'); ?></a>
        <hr>
    </div>
	<div class="col-md-12">
		<?php echo form_hidden('_project_id', $project->id); ?>
		<?php $table_data = array(
        _l('purchase_order'),
        _l('total'),
        _l('estimates_total_tax'),
        _l('vendor'),
        _l('order_date'),
        _l('payment_status'),
        _l('status'),
        );
        $custom_fields = get_custom_fields('pur_order',array('show_on_table'=>1));
        foreach($custom_fields as $field){
         array_push($table_data,$field['name']);
        }
        render_datatable($table_data,'table_pur_order'); ?>
	</div>
</div>

