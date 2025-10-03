<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="col-md-12">
    <?php $this->load->view('admin/estimates/estimates_top_stats'); ?>

    <div class="clearfix"></div>

    <div class="_filters _hidden_inputs hidden">
        <?php
            if (isset($estimates_sale_agents)) {
                foreach ($estimates_sale_agents as $agent) {
                    echo form_hidden('sale_agent_' . $agent['sale_agent']);
                }
            }
if (isset($estimate_statuses)) {
    foreach ($estimate_statuses as $_status) {
        $val = '';
        if ($_status == $this->input->get('status')) {
            $val = $_status;
        }
        echo form_hidden('estimates_' . $_status, $val);
    }
}
if (isset($estimates_years)) {
    foreach ($estimates_years as $year) {
        echo form_hidden('year_' . $year['year'], $year['year']);
    }
}
echo form_hidden('not_sent', $this->input->get('filter'));
echo form_hidden('project_id');
echo form_hidden('invoiced');
echo form_hidden('not_invoiced');
?>
    </div>

    <?php if (staff_can('create', 'estimates')) { ?>
    <a href="<?= admin_url('estimates/estimate'); ?>"
        class="btn btn-primary pull-left new new-estimate-btn">
        <i class="fa-regular fa-plus tw-mr-1"></i>
        <?= _l('create_new_estimate'); ?>
    </a>
    <?php } ?>
    <a href="<?= admin_url('estimates/pipeline/' . $switch_pipeline); ?>"
        class="btn btn-default mleft5 pull-left switch-pipeline hidden-xs" data-toggle="tooltip" data-placement="top"
        data-title="<?= _l('switch_to_pipeline'); ?>">
        <i class="fa-solid fa-grip-vertical"></i>
    </a>
    <?php if (! isset($project) && staff_can('view', 'bulk_pdf_exporter')) { ?>
    <a href="<?= admin_url('utilities/bulk_pdf_exporter?feature=estimates'); ?>"
        data-toggle="tooltip"
        title="<?= _l('bulk_pdf_exporter'); ?>"
        class="pull-left text-muted tw-p-2 tw-ml-2"><i class="fa-regular fa-file-pdf"></i></a>
    <?php } ?>
    <div class="display-block pull-right tw-space-x-0 sm:tw-space-x-1.5 rtl:tw-space-x-reverse">
        <a href="#" class="btn btn-default btn-with-tooltip toggle-small-view hidden-xs"
            onclick="toggle_small_view('.table-estimates','#estimate'); return false;" data-toggle="tooltip"
            title="<?= _l('estimates_toggle_table_tooltip'); ?>"><i
                class="fa fa-angle-double-left"></i></a>
        <app-filters id="<?= $estimates_table->id(); ?>"
            view="<?= $estimates_table->viewName(); ?>"
            :rules="extra.estimatesRules || <?= app\services\utilities\Js::from($this->input->get('status') ? $estimates_table->findRule('status')->setValue([$this->input->get('status')]) : ($this->input->get('not_sent') ? $estimates_table->findRule('sent')->setValue('0') : [])); ?>"
            :saved-filters="<?= $estimates_table->filtersJs(); ?>"
            :available-rules="<?= $estimates_table->rulesJs(); ?>">
        </app-filters>
    </div>
    <div class="clearfix"></div>
    <div class="row tw-mt-2">
        <div class="col-md-12" id="small-table">
            <div class="panel_s">
                <div class="panel-body">
                    <!-- if estimateid found in url -->
                    <?= form_hidden('estimateid', $estimateid); ?>
                    <?php $this->load->view('admin/estimates/table_html'); ?>
                </div>
            </div>
        </div>
        <div class="col-md-7 small-table-right-col">
            <div id="estimate" class="hide">
            </div>
        </div>
    </div>
</div>