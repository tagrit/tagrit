<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<table class="table dt-table table-estimates" data-order-col="1" data-order-type="desc">
    <thead>
        <tr>
            <th class="th-estimate-number">
                <?= _l('clients_estimate_dt_number'); ?>
            </th>
            <th class="th-estimate-date">
                <?= _l('clients_estimate_dt_date'); ?>
            </th>
            <th class="th-estimate-duedate">
                <?= _l('clients_estimate_dt_duedate'); ?>
            </th>
            <th class="th-estimate-amount">
                <?= _l('clients_estimate_dt_amount'); ?>
            </th>
            <th class="th-estimate-reference-number">
                <?= _l('reference_no'); ?>
            </th>
            <th class="th-estimate-status">
                <?= _l('clients_estimate_dt_status'); ?>
            </th>
            <?php
            $custom_fields = get_custom_fields('estimate', ['show_on_client_portal' => 1]);

foreach ($custom_fields as $field) { ?>
            <th><?= e($field['name']); ?>
            </th>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($estimates as $estimate) { ?>
        <tr>
            <td
                data-order="<?= e($estimate['number']); ?>">
                <a href="<?= site_url('estimate/' . $estimate['id'] . '/' . $estimate['hash']); ?>"
                    class="estimate-number"><?= e(format_estimate_number($estimate['id'])); ?></a>
                <?php
            if ($estimate['invoiceid']) {
                echo '<br /><span class="text-success">' . _l('estimate_invoiced') . '</span>';
            }
            ?>
            </td>
            <td
                data-order="<?= e($estimate['date']); ?>">
                <?= e(_d($estimate['date'])); ?>
            </td>
            <td
                data-order="<?= e($estimate['expirydate']); ?>">
                <?= e(_d($estimate['expirydate'])); ?>
            </td>
            <td
                data-order="<?= e($estimate['total']); ?>">
                <?= e(app_format_money($estimate['total'], $estimate['currency_name'])); ?>
            </td>
            <td><?= e($estimate['reference_no']); ?>
            </td>
            <td><?= format_estimate_status($estimate['status'], 'inline-block', true); ?>
            </td>
            <?php foreach ($custom_fields as $field) { ?>
            <td><?= get_custom_field_value($estimate['id'], $field['id'], 'estimate'); ?>
            </td>
            <?php } ?>
        </tr>
        <?php } ?>
    </tbody>
</table>