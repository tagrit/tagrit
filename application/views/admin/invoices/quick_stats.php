<?php
$where_all           = '';
$has_permission_view = staff_can('view', 'invoices');

if (isset($project)) {
    $where_all .= 'project_id=' . $project->id . ' AND ';
}

if (! $has_permission_view) {
    $where_all .= get_invoices_where_sql_for_staff(get_staff_user_id());
}

$where_all = trim($where_all);

if (endsWith($where_all, ' AND')) {
    $where_all = substr_replace($where_all, '', -4);
}

$total_invoices = total_rows(db_prefix() . 'invoices', $where_all);
?>
<div class="quick-top-stats tw-mb-6">
    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 lg:tw-grid-cols-5 tw-mb-0 tw-gap-x-2">
        <?php
   foreach ($invoices_statuses as $status) {
       if ($status == Invoices_model::STATUS_CANCELLED) {
           continue;
       }

       $where = ['status' => $status];

       if (isset($project)) {
           $where['project_id'] = $project->id;
       }

       if (! $has_permission_view) {
           $where['addedfrom'] = get_staff_user_id();
       }
       $total_by_status = total_rows(db_prefix() . 'invoices', $where);
       $percent         = ($total_invoices > 0 ? number_format(($total_by_status * 100) / $total_invoices, 2) : 0); ?>

        <button type="button"
            @click="extra.invoicesRules = <?= app\services\utilities\Js::from($invoices_table->findRule('status')->setValue([$status])); ?>"
            class="tw-bg-white tw-border tw-border-solid tw-border-neutral-300/80 tw-shadow-sm tw-py-2 tw-px-3.5 tw-rounded-lg tw-text-sm hover:tw-bg-neutral-100 tw-text-neutral-600 hover:tw-text-neutral-600 focus:tw-text-neutral-600 text-left">
            <div class="tw-flex tw-items-center">
                <span
                    class="tw-font-medium tw-text-base tw-inline-flex tw-items-center text-<?= get_invoice_status_label($status); ?>">
                    <?= format_invoice_status($status, '', false); ?>
                </span>
                <span class="tw-ml-2 rtl:tw-mr-2 tw-text-xs tw-text-neutral-500 tw-mt-px">
                    (<?= e($percent); ?>%)
                </span>
            </div>
            <div class="tw-mt-0.5">
                <div class="tw-text-neutral-600">
                    <span class="tw-font-semibold">
                        <?= e($total_by_status); ?> /
                        <?= e($total_invoices); ?>
                    </span>
                </div>
            </div>
        </button>
        <?php } ?>
    </div>
</div>