<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if (count($expenses_to_bill) > 0) { ?>
<h4 class="tw-font-bold tw-text-base tw-mt-0 tw-bg-neutral-100 tw-rounded-md tw-px-3 tw-py-2 tw-mb-3 tw-inline-block">
    <?= _l('expenses_available_to_bill'); ?>
</h4>
<?php
foreach ($expenses_to_bill as $expense) {
    $additional_action = ''; ?>
<?php if (! empty($expense['expense_name']) || ! empty($expense['note'])) {
        ob_start(); ?>
<p>
    <?= _l('expense_include_additional_data_on_convert'); ?>
</p>
<p>
    <b><?= _l('expense_add_edit_description'); ?>+</b>
</p>
<?php if (! empty($expense['note'])) { ?>
<div class="checkbox checkbox-primary invoice_inc_expense_additional_info">
    <input type="checkbox" id="inc_note"
        data-id="<?= e($expense['id']); ?>"
        data-content="<?= e(clear_textarea_breaks($expense['note'])); ?>">
    <label for="inc_note" data-toggle="tooltip"
        data-title="<?= e($expense['note']); ?>"><?= _l('expense'); ?>
        <?= _l('expense_add_edit_note'); ?></label>
</div>
<?php } ?>
<?php if (! empty($expense['expense_name'])) { ?>
<div class="checkbox checkbox-primary invoice_inc_expense_additional_info">
    <input type="checkbox" id="inc_name"
        data-id="<?= e($expense['id']); ?>"
        data-content="<?= e($expense['expense_name']); ?>">
    <label for="inc_name" data-toggle="tooltip"
        data-title="<?= e($expense['expense_name']); ?>">
        <?= _l('expense'); ?>
        <?= _l('expense_name'); ?>
    </label>
</div>
<?php }
$additional_action         = ob_get_contents();
        $additional_action = htmlspecialchars($additional_action);
        ob_end_clean(); ?>
<?php
    }
    $expense['currency_data'] = $this->currencies_model->get($expense['currency']); ?>
<div class="checkbox">
    <input type="checkbox" name="bill_expenses[]"
        value="<?= e($expense['id']); ?>"
        data-toggle="popover" data-html="true"
        data-content="<?= $additional_action; ?>"
        data-placement="bottom">
    <label for=""><a
            href="<?= admin_url('expenses/list_expenses/' . $expense['id']); ?>"
            target="_blank"><?= e($expense['category_name']); ?>
            <?php if (! empty($expense['expense_name'])) {
                echo '(' . e($expense['expense_name']) . ')';
            } ?>
        </a>
        <?= ' - ' . e(app_format_money($expense['amount'], $expense['currency_data']));
    if ($expense['tax'] != 0) {
        echo '<br /><span class="bold">' . _l('tax_1') . ':</span> ' . e($expense['taxrate']) . '% (' . e($expense['tax_name']) . ')';
        $total = $expense['amount'];
        $total += ($total / 100 * $expense['taxrate']);
    }
    if ($expense['tax2'] != 0) {
        echo '<br /><span class="bold">' . _l('tax_2') . ':</span> ' . e($expense['taxrate2']) . '% (' . e($expense['tax_name2']) . ')';
        $total += ($expense['amount'] / 100 * $expense['taxrate2']);
    }
    if ($expense['tax'] != 0 || $expense['tax2'] != 0) {
        echo '<p class="font-medium bold text-danger">' . _l('total_with_tax') . ': ' . e(app_format_money($total, $expense['currency_data'])) . '</p>';
    } ?>
    </label>
</div>
<?php } ?>
<?php } ?>