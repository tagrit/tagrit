<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-3 lg:tw-grid-cols-5 tw-gap-2">
    <?php foreach (tasks_summary_data(($rel_id ?? null), ($rel_type ?? null)) as $summary) { ?>
    <button type="button"
        @click="extra.tasksRules = <?= app\services\utilities\Js::from($tasks_table->findRule('status')->setValue([$summary['status_id']])); ?>"
        class="tw-bg-white tw-border tw-border-solid tw-border-neutral-300/80 tw-shadow-sm tw-py-2 tw-px-3.5 tw-rounded-lg tw-text-sm hover:tw-bg-neutral-100 tw-text-neutral-600 hover:tw-text-neutral-600 focus:tw-text-neutral-600 text-left">
        <span class="tw-font-semibold tw-mr-1 rtl:tw-ml-1">
            <?= e($summary['total_tasks']); ?>
        </span>
        <span class="tw-font-medium"
            style="color:<?= e($summary['color']); ?>">
            <?= e($summary['name']); ?>
        </span>
        <span class="tw-text-sm tw-text-neutral-800 tw-block">
            <span
                class="tw-text-neutral-500"><?= _l('home_my_tasks'); ?>:</span>
            <?= e($summary['total_my_tasks']); ?>
        </span>
    </button>
    <?php } ?>
</div>