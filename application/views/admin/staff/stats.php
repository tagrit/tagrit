<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="staff_logged_time">
    <div class="tw-mb-3 tw-flex tw-flex-col">
        <h4 class="tw-font-bold tw-mt-0 tw-mb-1">
            <?= _l('timesheet_summary'); ?>
        </h4>
        <?php if (strpos(current_url(), admin_url('staff/timesheets')) === false) { ?>
        <a href="<?= admin_url('staff/timesheets'); ?>"
            class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700">
            <?= _l('timesheet_detailed_overview'); ?>
            &rarr;
        </a>
        <?php } else { ?>
        <p class="tw-text-neutral-500 tw-mb-0">
            <?= _l('timesheet_detailed_overview'); ?>
        </p>
        <?php } ?>
    </div>
    <dl class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 lg:tw-grid-cols-5 tw-gap-2">
        <div
            class="tw-bg-white tw-border tw-border-solid tw-border-neutral-300/80 tw-shadow-sm tw-py-2.5 tw-px-3.5 tw-rounded-lg tw-text-sm tw-text-neutral-600">
            <div class="tw-font-semibold tw-text-base tw-truncate"
                title="<?= _l('staff_stats_total_logged_time'); ?>">
                <?= _l('staff_stats_total_logged_time'); ?>
            </div>
            <div class="tw-text-neutral-500 tw-font-medium">
                <?= e(seconds_to_time_format($logged_time['total'])); ?>
            </div>
        </div>

        <div
            class="tw-bg-white tw-border tw-border-solid tw-border-neutral-300/80 tw-shadow-sm tw-py-2.5 tw-px-3.5 tw-rounded-lg tw-text-sm tw-text-neutral-600">
            <div class="tw-font-semibold tw-text-base tw-truncate"
                title="<?= _l('staff_stats_last_month_total_logged_time'); ?>">
                <?= _l('staff_stats_last_month_total_logged_time'); ?>
            </div>
            <div class="tw-text-neutral-500 tw-font-medium">
                <?= e(seconds_to_time_format($logged_time['last_month'])); ?>
            </div>
        </div>

        <div
            class="tw-bg-white tw-border tw-border-solid tw-border-neutral-300/80 tw-shadow-sm tw-py-2.5 tw-px-3.5 tw-rounded-lg tw-text-sm tw-text-neutral-600">
            <div class="tw-font-semibold tw-text-base tw-text-primary-600 tw-truncate"
                title="<?= _l('staff_stats_this_month_total_logged_time'); ?>">
                <?= _l('staff_stats_this_month_total_logged_time'); ?>
            </div>
            <div class="tw-text-neutral-500 tw-font-medium">
                <?= e(seconds_to_time_format($logged_time['this_month'])); ?>
            </div>
        </div>

        <div
            class="tw-bg-white tw-border tw-border-solid tw-border-neutral-300/80 tw-shadow-sm tw-py-2.5 tw-px-3.5 tw-rounded-lg tw-text-sm tw-text-neutral-600">
            <div class="tw-font-semibold tw-text-base tw-truncate"
                title="<?= _l('staff_stats_last_week_total_logged_time'); ?>">
                <?= _l('staff_stats_last_week_total_logged_time'); ?>
            </div>
            <div class="tw-text-neutral-500 tw-font-medium">
                <?= e(seconds_to_time_format($logged_time['last_week'])); ?>
            </div>
        </div>

        <div
            class="tw-bg-white tw-border tw-border-solid tw-border-neutral-300/80 tw-shadow-sm tw-py-2.5 tw-px-3.5 tw-rounded-lg tw-text-sm tw-text-neutral-600">
            <div class="tw-font-semibold tw-text-base tw-truncate"
                title="<?= _l('staff_stats_this_week_total_logged_time'); ?>">
                <?= _l('staff_stats_this_week_total_logged_time'); ?>
            </div>
            <div class="tw-text-neutral-500 tw-font-medium">
                <?= e(seconds_to_time_format($logged_time['this_week'])); ?>
            </div>
        </div>
    </dl>
</div>