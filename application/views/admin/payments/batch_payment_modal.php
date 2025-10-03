<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="batch-payment-modal">
    <div class="modal-dialog modal-xxl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <?= _l('add_batch_payments') ?>
                </h4>
            </div>
            <?= form_open('admin/payments/add_batch_payment', ['id' => 'batch-payment-form']); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group select-placeholder">
                            <select id="batch-payment-filter" class="selectpicker" name="client_filter"
                                data-width="100%"
                                data-none-selected-text="<?= _l('batch_payment_filter_by_customer') ?>">
                                <option value=""></option>
                                <?php foreach ($customers as $customer) { ?>
                                <option
                                    value="<?= e($customer->userid); ?>">
                                    <?= e($customer->company); ?>
                                </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th><strong><?= _l('batch_payments_table_invoice_number_heading'); ?>
                                                #</strong></th>
                                        <th><strong><?= _l('batch_payments_table_payment_date_heading'); ?></strong>
                                        </th>
                                        <th><strong><?= _l('batch_payments_table_payment_mode_heading'); ?></strong>
                                        </th>
                                        <th><strong><?= _l('batch_payments_table_transaction_id_heading'); ?></strong>
                                        </th>
                                        <th><strong><?= _l('batch_payments_table_amount_received_heading'); ?></strong>
                                        </th>
                                        <th><strong><?= _l('batch_payments_table_invoice_balance_due'); ?></strong>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($invoices as $index => $invoice) { ?>
                                    <tr class="batch_payment_item"
                                        data-clientid="<?= e($invoice->clientid); ?>"
                                        data-invoiceId="<?= $invoice->id ?>">
                                        <td>
                                            <a href="<?= admin_url('invoices/list_invoices/' . $invoice->id); ?>"
                                                target="_blank">
                                                <?= format_invoice_number($invoice->id) ?>
                                            </a><br>
                                            <a class="text-dark"
                                                href="<?= admin_url('clients/client/' . $invoice->clientid); ?>"
                                                target="_blank">
                                                <?= $invoice->company ?>
                                            </a>

                                            <input type="hidden"
                                                name="invoice[<?= $index ?>][invoiceid]"
                                                value="<?= $invoice->id?>">
                                        </td>
                                        <td class="tw-w-48">
                                            <?= render_date_input('invoice[' . $index . '][date]', '', date(get_current_date_format(true))) ?>
                                        </td>
                                        <td class="tw-w-56">
                                            <div class="form-group">
                                                <select class="selectpicker"
                                                    name="invoice[<?= $index ?>][paymentmode]"
                                                    data-width="100%" data-none-selected-text="-">
                                                    <option></option>
                                                    <?php foreach ($invoice->allowed_payment_modes as $mode) { ?>
                                                    <option
                                                        value="<?= e($mode->id); ?>">
                                                        <?= e($mode->name); ?>
                                                    </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td><?= render_input('invoice[' . $index . '][transactionid]') ?>
                                        </td>
                                        <td><?= render_input('invoice[' . $index . '][amount]', '', '', 'number', ['max' => $invoice->total_left_to_pay]) ?>
                                        </td>
                                        <td><?= app_format_money($invoice->total_left_to_pay, $invoice->currency) ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-sm-12 row">
                            <div class="checkbox">
                                <input type="checkbox" name="do_not_send_invoice_payment_recorded" value="1"
                                    id="do_not_send_invoice_payment_recorded">
                                <label for="do_not_send_invoice_payment_recorded">
                                    <?= _l('batch_payments_send_invoice_payment_recorded'); ?>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default close_btn"
                        data-dismiss="modal"><?= _l('close'); ?></button>
                    <button type="submit"
                        class="btn btn-primary"><?= _l('apply'); ?></button>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>