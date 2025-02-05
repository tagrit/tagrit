<div class="quick-top-stats tw-mb-6">
    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 lg:tw-grid-cols-5 tw-mb-0 tw-gap-x-2">
        <?php foreach ($estimate_statuses as $status) {
            $percent_data = get_estimates_percent_by_status(
                $status,
                (isset($project) ? $project->id : null)
            ); ?>
        <button type="button"
            @click="extra.estimatesRules = <?= app\services\utilities\Js::from($estimates_table->findRule('status')->setValue([$status])); ?>"
            class="tw-bg-white tw-border tw-border-solid tw-border-neutral-300/80 tw-shadow-sm tw-py-2 tw-px-3.5 tw-rounded-lg tw-text-sm hover:tw-bg-neutral-100 tw-text-neutral-600 hover:tw-text-neutral-600 focus:tw-text-neutral-600 text-left">
            <div class="tw-flex tw-items-center">
                <span
                    class="tw-font-medium tw-text-base tw-inline-flex tw-items-center text-<?= estimate_status_color_class($status); ?>">
                    <?= format_estimate_status($status, '', false); ?>
                </span>
                <span class="tw-ml-2 rtl:tw-mr-2 tw-text-xs tw-text-neutral-500 tw-mt-px">
                    (<?= e($percent_data['percent']); ?>%)
                </span>
            </div>
            <div class="tw-mt-0.5">
                <div class="tw-text-neutral-600">
                    <span class="tw-font-semibold">
                        <?= e($percent_data['total_by_status']); ?>
                        /
                        <?= e($percent_data['total']); ?>
                    </span>
                </div>
            </div>
        </button>
        <?php } ?>
    </div>
</div>