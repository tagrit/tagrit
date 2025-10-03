<?php
$_where = '';
if (staff_cant('view', 'projects')) {
    $_where = 'id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')';
}
?>
<div
    class="_filters _hidden_inputs tw-flex sm:tw-flex-row tw-flex-col tw-gap-y-2 sm:tw-gap-x-2 tw-order-1 sm:-tw-order-none tw-mb-1 md:tw-mb-0">
    <?php foreach ($statuses as $status) { ?>
    <?php $where = ($_where == '' ? '' : $_where . ' AND ') . 'status = ' . $status['id']; ?>
    <a href="#"
        class="tw-bg-transparent tw-border tw-border-solid tw-border-neutral-300 tw-shadow-sm tw-py-1 tw-px-2 tw-rounded-lg tw-text-sm hover:tw-bg-neutral-200/60 tw-text-neutral-600 hover:tw-text-neutral-600 focus:tw-text-neutral-600"
        @click.prevent="extra.projectsRules = <?= app\services\utilities\Js::from($table->findRule('status')->setValue([(int) $status['id']])); ?>">
        <span class="tw-font-semibold tw-mr-1 rtl:tw-ml-1">
            <?= total_rows(db_prefix() . 'projects', $where); ?>
        </span>
        <span
            style="color: <?= e($status['color']); ?>"
            class="<?= 'project-status-' . $status['color']; ?>">
            <?= e($status['name']); ?>
        </span>
    </a>
    <?php } ?>
</div>