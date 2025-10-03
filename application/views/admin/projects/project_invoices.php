<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div id="vueApp">
    <a href="#"
        class="invoices-total tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 tw-mb-1 tw-block"
        onclick="slideToggle('#stats-top'); init_invoices_total(true); return false;">
        <?= _l('view_financial_stats'); ?>
    </a>
    <?php include_once APPPATH . 'views/admin/invoices/invoices_top_stats.php'; ?>
    <?php $this->load->view('admin/invoices/quick_stats'); ?>
    <div class="panel_s">
        <div class="panel-body">
            <div class="project_invoices">
                <?php include_once APPPATH . 'views/admin/invoices/filter_params.php'; ?>

                <?php $this->load->view('admin/invoices/list_template', [
                    'table'    => $invoices_table,
                    'table_id' => $invoices_table->id(),
                ]); ?>
            </div>
        </div>
    </div>
</div>