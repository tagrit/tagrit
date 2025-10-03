<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<table class="table dt-table table-invoices" data-order-col="1" data-order-type="desc">
    <thead>
        <tr>
            <th>
                <?= _l('clients_invoice_dt_number'); ?>
            </th>
            <th>
                <?= _l('clients_invoice_dt_date'); ?>
            </th>
            <th>
                <?= _l('clients_invoice_dt_duedate'); ?>
            </th>
            <th>
                <?= _l('clients_invoice_dt_amount'); ?>
            </th>
            <th>
                <?= _l('clients_invoice_dt_status'); ?>
            </th>
            <?php $custom_fields = get_custom_fields('invoice', ['show_on_client_portal' => 1]); ?>

            <?php foreach ($custom_fields as $field) { ?>
            <th>
                <?= e($field['name']); ?>
            </th>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($invoices as $invoice) { ?>
        <tr>
            <td
                data-order="<?= e($invoice['number']); ?>">
                <a href="<?= site_url('invoice/' . $invoice['id'] . '/' . $invoice['hash']); ?>"
                    class="invoice-number">
                    <?= e(format_invoice_number($invoice['id'])); ?>
                </a>
            </td>
            <td
                data-order="<?= e($invoice['date']); ?>">
                <?= e(_d($invoice['date'])); ?>
            </td>
            <td
                data-order="<?= e($invoice['duedate']); ?>">
                <?= e(_d($invoice['duedate'])); ?>
            </td>
            <td
                data-order="<?= e($invoice['total']); ?>">
                <?= e(app_format_money($invoice['total'], $invoice['currency_name'])); ?>
            </td>
            <td>
                <?= format_invoice_status($invoice['status'], 'pull-left', true); ?>
            </td>
            <?php foreach ($custom_fields as $field) { ?>
            <td>
                <?= get_custom_field_value($invoice['id'], $field['id'], 'invoice'); ?>
            </td>
            <?php } ?>
        </tr>
        <?php } ?>
    </tbody>
</table>