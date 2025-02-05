<div id="<?= ($filters_wrapper_id ?? 'tasksFilters') . ($detached ?? false ? '-detached' : ''); ?>"
    class="tw-inline pull-right tw-ml-0 sm:tw-ml-1.5 rtl:sm:tw-mr-1.5 rtl:sm:tw-ml-0">
    <app-filters id="<?= $tasks_table->id(); ?>"
        view="<?= $tasks_table->viewName(); ?>"
        :rules="extra.tasksRules || undefined"
        :saved-filters="<?= $tasks_table->filtersJs(); ?>"
        :available-rules="<?= $tasks_table->rulesJs(); ?>">
    </app-filters>
</div>
<script>
    if (typeof(vNewApp) == 'function') {
        vNewApp(
            '#<?= $filters_wrapper_id ?? 'tasksFilters'; ?>')
    }
</script>