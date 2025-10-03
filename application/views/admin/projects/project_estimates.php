<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div id="vueApp">
    <a href="#"
        class="estimates-total tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 tw-block tw-mb-1"
        onclick="slideToggle('#stats-top'); init_estimates_total(true); return false;">
        <?= _l('view_financial_stats'); ?>
    </a>
    <?php include_once APPPATH . 'views/admin/estimates/estimates_top_stats.php'; ?>
    <?php $this->load->view('admin/estimates/quick_stats'); ?>
    <div class="panel_s panel-table-full ">
        <div class="panel-body">
            <div class="project_estimates">
                <?php $this->load->view('admin/estimates/list_template', [
                    'table'    => $estimates_table,
                    'table_id' => $estimates_table->id(),
                ]); ?>
            </div>
        </div>
    </div>
</div>