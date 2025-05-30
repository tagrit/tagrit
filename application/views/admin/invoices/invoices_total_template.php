<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if (count($invoices_years) > 1 || isset($invoices_total_currencies)) { ?>
<div class="tw-flex tw-space-x-3 tw-mb-2">
    <?php if (isset($invoices_total_currencies)) { ?>
    <div class="!tw-w-28 simple-bootstrap-select -tw-mb-2">
        <select data-show-subtext="true" data-width="auto" class="selectpicker !tw-w-28" name="total_currency"
            onchange="init_invoices_total();">
            <?php foreach ($invoices_total_currencies as $currency) {
                $selected = '';
                if (! $this->input->post('currency')) {
                    if ($currency['isdefault'] == 1 || isset($_currency) && $_currency == $currency['id']) {
                        $selected = 'selected';
                    }
                } else {
                    if ($this->input->post('currency') == $currency['id']) {
                        $selected = 'selected';
                    }
                } ?>
            <option
                value="<?= e($currency['id']); ?>"
                <?= e($selected); ?>
                data-subtext="<?= e($currency['name']); ?>">
                <?= e($currency['symbol']); ?>
            </option>
            <?php
            } ?>
        </select>
    </div>
    <?php } ?>
    <?php if (count($invoices_years) > 1) { ?>
    <div class="simple-bootstrap-select !tw-max-w-xs">
        <select
            data-none-selected-text="<?= date('Y'); ?>"
            data-width="auto" class="selectpicker tw-w-full" name="invoices_total_years"
            onchange="init_invoices_total();" multiple="true" id="invoices_total_years">
            <?php foreach ($invoices_years as $year) { ?>
            <option
                value="<?= e($year['year']); ?>"
                <?php if ($this->input->post('years') && in_array($year['year'], $this->input->post('years')) || ! $this->input->post('years') && date('Y') == $year['year']) {
                    echo ' selected';
                } ?>>
                <?= e($year['year']); ?>
            </option>
            <?php } ?>
        </select>
    </div>
    <?php } ?>
</div>
<?php } ?>
<dl class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-3 sm:tw-gap-5 tw-mb-0">
    <div class="tw-border tw-border-solid tw-border-neutral-300/80 tw-rounded-md tw-bg-white">
        <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
            <dt class="tw-font-medium text-warning">
                <?= _l('outstanding_invoices'); ?>
            </dt>
            <dd class="tw-mt-1 tw-flex tw-items-baseline tw-justify-between md:tw-block lg:tw-flex">
                <div class="tw-flex tw-items-baseline tw-font-semibold tw-text-neutral-600">
                    <?= e(app_format_money($total_result['due'], $total_result['currency'])); ?>
                </div>
            </dd>
        </div>
    </div>
    <div class="tw-border tw-border-solid tw-border-neutral-300/80 tw-rounded-md tw-bg-white">
        <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
            <dt class="tw-font-medium text-muted">
                <?= _l('past_due_invoices'); ?>
            </dt>
            <dd class="tw-mt-1 tw-flex tw-items-baseline tw-justify-between md:tw-block lg:tw-flex">
                <div class="tw-flex tw-items-baseline tw-font-semibold tw-text-neutral-600">
                    <?= e(app_format_money($total_result['overdue'], $total_result['currency'])); ?>
                </div>
            </dd>
        </div>
    </div>

    <div class="tw-border tw-border-solid tw-border-neutral-300/80 tw-rounded-md tw-bg-white">
        <div class="tw-px-4 tw-py-5 sm:tw-px-4 sm:tw-py-2">
            <dt class="tw-font-medium text-success">
                <?= _l('paid_invoices'); ?>
            </dt>
            <dd class="tw-mt-1 tw-flex tw-items-baseline tw-justify-between md:tw-block lg:tw-flex">
                <div class="tw-flex tw-items-baseline tw-font-semibold tw-text-neutral-600">
                    <?= e(app_format_money($total_result['paid'], $total_result['currency'])); ?>
                </div>
            </dd>
        </div>
    </div>
</dl>
<script>
    (function() {
        if (typeof(init_selectpicker) == 'function') {
            init_selectpicker();
        }
    })();
</script>