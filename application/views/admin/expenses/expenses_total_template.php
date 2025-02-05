<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if (count($expenses_years) > 1 || isset($currencies)) { ?>
<div class="tw-flex tw-space-x-3 rtl:tw-space-x-reverse tw-mb-2">
    <?php if (isset($currencies)) { ?>
    <div class="!tw-w-28 simple-bootstrap-select -tw-mb-2 rtl:-tw-mr-4">
        <select class="selectpicker !tw-w-28" data-width="auto" name="expenses_total_currency"
            onchange="init_expenses_total();">
            <?php foreach ($currencies as $currency) {
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
                data-subtext="<?= e($currency['name']); ?>"><?= e($currency['symbol']); ?>
            </option>
            <?php
            } ?>
        </select>
    </div>
    <?php } ?>
    <?php if (count($expenses_years) > 1) { ?>
    <div class="simple-bootstrap-select !tw-max-w-xs">
        <select
            data-none-selected-text="<?= date('Y'); ?>"
            data-width="auto" class="selectpicker tw-w-full" multiple name="expenses_total_years"
            onchange="init_expenses_total();">
            <?php foreach ($expenses_years as $year) { ?>
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

<dl class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 lg:tw-grid-cols-5 tw-gap-3 sm:tw-gap-5 tw-mb-0">
    <?php foreach ([
        ['key' => 'all', 'class' => 'text-warning', 'label' => _l('expenses_total')],
        ['key' => 'billable', 'class' => 'text-success', 'label' => _l('expenses_list_billable')],
        ['key' => 'non_billable', 'class' => 'text-warning', 'label' => _l('expenses_list_non_billable')],
        ['key' => 'unbilled', 'class' => 'text-danger', 'label' => _l('expenses_list_unbilled')],
        ['key' => 'billed', 'class' => 'text-success', 'label' => _l('expense_billed')],
    ] as $totalSection) { ?>
    <div
        class="tw-bg-white tw-border tw-border-solid tw-border-neutral-300/80 tw-shadow-sm tw-py-2 tw-px-3.5 tw-rounded-lg tw-text-sm">
        <dt
            class="tw-font-medium tw-text-base <?= e($totalSection['class']); ?>">
            <?= e($totalSection['label']); ?>
        </dt>
        <dd class="tw-mt-1 tw-flex tw-items-baseline tw-justify-between md:tw-block lg:tw-flex">
            <div class="tw-font-semibold tw-text-neutral-600">
                <?= e($totals[$totalSection['key']]['total']); ?>
            </div>
        </dd>
    </div>
    <?php } ?>
</dl>
<script>
    init_selectpicker();
</script>