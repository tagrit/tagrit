<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="col-md-12">
    <div class="row">
        <div class="col-md-12 tw-mb-2">
            <div class="_buttons">
                <?php $this->load->view('admin/invoices/invoices_top_stats'); ?>
                <?php if (staff_can('create', 'invoices')) { ?>
                <a href="<?= admin_url('invoices/invoice'); ?>"
                    class="btn btn-primary pull-left new new-invoice-list mright5">
                    <i class="fa-regular fa-plus tw-mr-1"></i>
                    <?= _l('create_new_invoice'); ?>
                </a>
                <?php } ?>
                <?php if (! isset($project) && ! isset($customer)) { ?>
                <?php if (staff_can('create', 'payments')) { ?>
                <button id="add-batch-payment" onclick="add_batch_payment()" class="btn btn-default pull-left">
                    <i class="fa-solid fa-plus tw-mr-1"></i>
                    <?= _l('batch_payments'); ?>
                </button>
                <?php } ?>
                <?php if (staff_can('view', 'bulk_pdf_exporter')) { ?>
                <a href="<?= admin_url('utilities/bulk_pdf_exporter?feature=invoices'); ?>"
                    data-toggle="tooltip"
                    title="<?= _l('bulk_pdf_exporter'); ?>"
                    class="pull-left text-muted tw-p-2 tw-ml-2"><i class="fa-regular fa-file-pdf"></i></a>
                <?php } ?>
                <?php } ?>
                <div class="display-block pull-right tw-space-x-0 sm:tw-space-x-1.5 rtl:tw-space-x-reverse">
                    <a href="#" class="btn btn-default btn-with-tooltip toggle-small-view hidden-xs"
                        onclick="toggle_small_view('.table-invoices','#invoice'); return false;" data-toggle="tooltip"
                        title="<?= _l('invoices_toggle_table_tooltip'); ?>"><i
                            class="fa fa-angle-double-left"></i>
                    </a>

                    <app-filters id="<?= $invoices_table->id(); ?>"
                        view="<?= $invoices_table->viewName(); ?>"
                        :rules="extra.invoicesRules || <?= app\services\utilities\Js::from($this->input->get('status') ? $invoices_table->findRule('status')->setValue([$this->input->get('status')]) : ($this->input->get('not_sent') ? $invoices_table->findRule('sent')->setValue('0') : [])); ?>"
                        :saved-filters="<?= $invoices_table->filtersJs(); ?>"
                        :available-rules="<?= $invoices_table->rulesJs(); ?>">
                    </app-filters>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-12" id="small-table">
            <div class="panel_s">
                <div class="panel-body panel-table-full">
                    <!-- if invoiceid found in url -->
                    <?= form_hidden('invoiceid', $invoiceid); ?>
                    <?php $this->load->view('admin/invoices/table_html'); ?>
                </div>
            </div>
        </div>
        <div class="col-md-7 small-table-right-col">
            <div id="invoice" class="hide"></div>
        </div>
    </div>
</div>