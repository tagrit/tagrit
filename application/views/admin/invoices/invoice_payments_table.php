<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>
                    <span
                        class="bold"><?= _l('payments_table_number_heading'); ?></span>
                </th>
                <th>
                    <span
                        class="bold"><?= _l('payments_table_mode_heading'); ?></span>
                </th>
                <th>
                    <span
                        class="bold"><?= _l('payments_table_date_heading'); ?></span>
                </th>
                <th>
                    <span
                        class="bold"><?= _l('payments_table_amount_heading'); ?></span>
                </th>
                <th>
                    <span
                        class="bold"><?= _l('options'); ?></span>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($invoice->payments as $payment) { ?>
            <tr class="payment">
                <td><?= e($payment['paymentid']); ?>
                    <?= icon_btn('payments/pdf/' . $payment['paymentid'], 'fa-regular fa-file-pdf', 'btn-default pull-right'); ?>
                </td>
                <td><?= e($payment['name']); ?>
                    <?php if (! empty($payment['paymentmethod'])) {
                        echo ' - ' . $payment['paymentmethod'];
                    }
                if ($payment['transactionid']) {
                    echo '<br />' . _l('payments_table_transaction_id', $payment['transactionid']);
                }
                ?>
                </td>
                <td><?= e(_d($payment['date'])); ?>
                </td>
                <td><?= e(app_format_money($payment['amount'], $invoice->currency_name)); ?>
                </td>
                <td>
                    <div class="tw-flex tw-items-center tw-space-x-2">
                        <a href="<?= admin_url('payments/payment/' . $payment['paymentid']); ?>"
                            class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700">
                            <i class="fa-regular fa-pen-to-square fa-lg"></i>
                        </a>
                        <?php if (staff_can('delete', 'payments')) { ?>
                        <a href="<?= admin_url('invoices/delete_payment/' . $payment['paymentid'] . '/' . $payment['invoiceid']); ?>"
                            class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 _delete">
                            <i class="fa-regular fa-trash-can"></i>
                        </a>
                        <?php } ?>
                    </div>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>