<?php defined('BASEPATH') or exit('No direct script access allowed');
if (count($invoices_to_merge) > 0) { ?>
<div class="mergeable-invoices">
    <h4
        class="tw-font-bold tw-text-base tw-mt-0 tw-bg-neutral-100 tw-rounded-md tw-px-3 tw-py-2 tw-mb-3 tw-inline-block">
        <?= _l('invoices_available_for_merging'); ?>
    </h4>
    <?php foreach ($invoices_to_merge as $_inv) { ?>
    <div class="checkbox">
        <input type="checkbox" name="invoices_to_merge[]"
            value="<?= e($_inv->id); ?>">
        <label for="">
            <a href="<?= admin_url('invoices/list_invoices/' . $_inv->id); ?>"
                data-toggle="tooltip"
                data-title="<?= format_invoice_status($_inv->status, '', false); ?>"
                target="_blank">
                <?= e(format_invoice_number($_inv->id)); ?>
            </a> -
            <?= e(app_format_money($_inv->total, $_inv->currency_name)); ?>
        </label>
    </div>
    <?php
                if ($_inv->discount_total > 0) {
                    echo '<b>' . e(_l('invoices_merge_discount', app_format_money($_inv->discount_total, $_inv->currency_name))) . '</b><br />';
                }
        ?>
    <?php } ?>
    <p>
    <div class="checkbox checkbox-info">
        <input type="checkbox" checked name="cancel_merged_invoices" id="cancel_merged_invoices">
        <label for="cancel_merged_invoices"><i class="fa-regular fa-circle-question" data-toggle="tooltip"
                data-title="<?= _l('invoice_merge_number_warning'); ?>"
                data-placement="bottom"></i>
            <?= _l('invoices_merge_cancel_merged_invoices'); ?></label>
    </div>
    </p>
</div>
<?php } ?>