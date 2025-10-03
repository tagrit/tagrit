<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<h4 class="customer-profile-group-heading">
    <?= _l('projects'); ?>
</h4>
<?php if (isset($client)) { ?>
<?php if (staff_can('create', 'projects')) { ?>
<a href="<?= admin_url('projects/project?customer_id=' . $client->userid); ?>"
    class="btn btn-primary mbot15<?= $client->active == 0 ? ' disabled' : ''; ?>">
    <i class="fa-regular fa-plus tw-mr-1"></i>
    <?= _l('new_project'); ?>
</a>
<?php } ?>
<?php
    $_where = '';
    if (staff_cant('view', 'projects')) {
        $_where = 'id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')';
    }
    ?>
<dl class="tw-grid tw-gap-2 sm:tw-grid-flow-col sm:tw-auto-cols-max tw-mb-5 tw-overflow-x-auto">
    <?php foreach ($project_statuses as $status) { ?>
    <div
        class="tw-bg-neutral-50 tw-border tw-border-solid tw-border-neutral-300 tw-shadow-sm tw-py-1 tw-px-2 tw-rounded-lg tw-text-sm tw-text-neutral-600">
        <span class="tw-font-semibold tw-mr-1 rtl:tw-ml-1">
            <?php $where = ($_where == '' ? '' : $_where . ' AND ') . 'status = ' . $status['id'] . ' AND clientid=' . $client->userid; ?>
            <?= total_rows(db_prefix() . 'projects', $where); ?>
        </span>
        <span
            style="color: <?= e($status['color']); ?>"
            class="<?= 'project-status-' . $status['color']; ?>">
            <?= e($status['name']); ?>
        </span>
    </div>
    <?php } ?>
</dl>
<?php
       $this->load->view('admin/projects/table_html', ['class' => 'projects-single-client']);
}
?>