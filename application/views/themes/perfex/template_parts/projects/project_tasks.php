<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if ($project->settings->view_tasks == 1) { ?>
<!-- Project Tasks -->
<?php if (! isset($view_task)) { ?>
<div
    class="pull-right tasks-table<?= $project->settings->view_milestones == 1 ? ' hide' : ''; ?>">
    <select class="selectpicker" name="tasksFilterByStatus[]"
        onchange="dt_custom_view('.table-tasks', 3, $(this).selectpicker('val'));" multiple="true"
        data-none-selected-text="<?= _l('filter_by'); ?>">
        <?php foreach ($tasks_statuses as $status) { ?>
        <option
            value="<?= e($status['name']); ?>">
            <?= e($status['name']); ?>
        </option>
        <?php } ?>
    </select>
</div>
<?php } ?>
<?php if ($project->settings->view_milestones == 1 && ! isset($view_task)) { ?>
<a href="#" class="btn btn-default pull-left task-table-toggle" onclick="taskTable(); return false;">
    <i class="fa fa-th-list"></i>
</a>
<div class="clearfix"></div>
<hr />
<div class="tasks-phases">
    <?php
      $milestones = [
          [
              'name'                 => _l('milestones_uncategorized'),
              'id'                   => 0,
              'total_logged_time'    => $this->projects_model->calc_milestone_logged_time($project->id, 0),
              'total_tasks'          => total_project_tasks_by_milestone(0, $project->id),
              'total_finished_tasks' => total_project_finished_tasks_by_milestone(0, $project->id),
              'color'                => null,
          ],
          ...$this->projects_model->get_milestones($project->id, ['hide_from_customer' => 0]),
      ];
    ?>
    <div class="kan-ban-row">
        <?php foreach ($milestones as $milestone) {
            $tasks   = $this->projects_model->get_tasks($project->id, ['milestone' => $milestone['id']]);
            $percent = 0;
            if ($milestone['total_finished_tasks'] >= floatval($milestone['total_tasks'])) {
                $percent = 100;
            } else {
                if ($milestone['total_tasks'] !== 0) {
                    $percent = number_format(($milestone['total_finished_tasks'] * 100) / $milestone['total_tasks'], 2);
                }
            }
            $milestone_color = '';
            if (! empty($milestone['color']) && ! is_null($milestone['color'])) {
                $milestone_color = ' style="background:' . $milestone['color'] . ';border:1px solid ' . $milestone['color'] . '"';
            } ?>
        <div
            class="kan-ban-col<?= $milestone['id'] == 0 && count($tasks) == 0 ? ' hide' : ''; ?>">
            <div class="panel-heading <?= $milestone_color != '' ? 'color-not-auto-adjusted color-white ' : ''; ?><?= $milestone['id'] != 0 ? 'task-phase' : 'info-bg'; ?>"
                <?= $milestone_color; ?>>
                <?php if ($milestone['id'] != 0 && $milestone['description_visible_to_customer'] == 1) { ?>
                <i class="fa fa-file-text pointer" aria-hidden="true" data-toggle="popover"
                    data-title="<?= _l('milestone_description'); ?>"
                    data-html="true"
                    data-content="<?= htmlspecialchars($milestone['description']); ?>"></i>&nbsp;
                <?php } ?>
                <span
                    class="bold tw-text-sm"><?= e($milestone['name']); ?></span>
                <span class="tw-text-xs">
                    <?= $milestone['id'] != 0 ? (' | ' . e(_d($milestone['start_date']) . ' - ' . _d($milestone['due_date']))) : ''; ?>
                </span>
                <?php if ($project->settings->view_task_total_logged_time == 1) { ?>
                <?= '<br /><small>' . _l('milestone_total_logged_time') . ': ' . e(seconds_to_time_format($milestone['total_logged_time'])) . '</small>';
                } ?>
            </div>
            <div class="panel-body">
                <?php if (count($tasks) == 0) {
                    echo _l('milestone_no_tasks_found');
                } ?>
                <?php foreach ($tasks as $task) { ?>
                <div
                    class="media _task_wrapper<?= (! empty($task['duedate']) && $task['duedate'] < date('Y-m-d') && $task['status'] != Tasks_model::STATUS_COMPLETE) ? ' overdue-task' : ''; ?>">
                    <div class="media-body">
                        <a href="<?= site_url('clients/project/' . $project->id . '?group=project_tasks&taskid=' . $task['id']); ?>"
                            class="task_milestone tw-truncate tw-max-w-64 tw-min-w-0 tw-block tw-mb-1 pull-left<?= $task['status'] == Tasks_model::STATUS_COMPLETE ? ' line-throught text-muted' : ''; ?>">
                            <?= e($task['name']); ?>
                        </a>
                        <?php if (
                            $project->settings->edit_tasks == 1
                            && $task['is_added_from_contact'] == 1
                            && $task['addedfrom'] == get_contact_user_id()
                            && $task['billed'] == 0
                        ) { ?>
                        <a href="<?= site_url('clients/project/' . $project->id . '?group=edit_task&taskid=' . $task['id']); ?>"
                            class="pull-right">
                            <small><i class="fa-regular fa-pen-to-square"></i></small>
                        </a>
                        <?php } ?>
                        <div class="clearfix"></div>
                        <p class="text-xs tw-mb-0">
                            <?= format_task_status($task['status'], true); ?>
                        </p>
                        <p class="tw-mb-0 tw-text-xs tw-text-neutral-500">
                            <?= _l('tasks_dt_datestart'); ?>:
                            <b><?= e(_d($task['startdate'])); ?></b>
                        </p>
                        <?php if (is_date($task['duedate'])) { ?>
                        <p class="tw-mb-0 tw-text-xs tw-text-neutral-500">
                            <?= _l('task_duedate'); ?>:
                            <b><?= e(_d($task['duedate'])); ?></b>
                        </p>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>
            </div>
            <div class="panel-footer">
                <div class="progress tw-my-0">
                    <div class="progress-bar progress-bar-default" role="progressbar" aria-valuenow="40"
                        aria-valuemin="0" aria-valuemax="100" style="width: 0%"
                        data-percent="<?= e($percent); ?>">
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
<?php } ?>
<?php if (! isset($view_task)) { ?>
<div class="clearfix"></div>
<hr class="tasks-table" />
<div
    class="tasks-table<?= $project->settings->view_milestones == 1 ? ' hide' : ''; ?>">
    <table class="table dt-table table-tasks" data-order-col="3" data-s-type='[{"column":3,"type":"task-status"}]'
        data-order-type="asc">
        <thead>
            <tr>
                <th>
                    <?= _l('tasks_dt_name'); ?>
                </th>
                <th>
                    <?= _l('tasks_dt_datestart'); ?>
                </th>
                <th>
                    <?= _l('task_duedate'); ?>
                </th>
                <th>
                    <?= _l('task_status'); ?>
                </th>
                <?php if ($project->settings->view_milestones == 1) { ?>
                <th>
                    <?= _l('task_milestone'); ?>
                </th>
                <?php } ?>
                <th>
                    <?= _l('task_billable'); ?>
                </th>
                <th>
                    <?= _l('task_billed'); ?>
                </th>
                <?php $custom_fields = get_custom_fields('tasks', ['show_on_client_portal' => 1]); ?>
                <?php foreach ($custom_fields as $field) { ?>
                <th>
                    <?= e($field['name']); ?>
                </th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($project_tasks as $task) { ?>
            <tr>
                <td>
                    <?php if (
                        $project->settings->edit_tasks == 1
                        && $task['is_added_from_contact'] == 1
                        && $task['addedfrom'] == get_contact_user_id()
                        && $task['billed'] == 0
                    ) { ?>
                    <a
                        href="<?= site_url('clients/project/' . $project->id . '?group=edit_task&taskid=' . $task['id']); ?>">
                        <i class="fa-regular fa-pen-to-square"></i>
                    </a>
                    <?php } ?>
                    <a
                        href="<?= site_url('clients/project/' . $project->id . '?group=project_tasks&taskid=' . $task['id']); ?>">
                        <?= e($task['name']); ?>
                    </a>
                </td>
                <td
                    data-order="<?= e($task['startdate']); ?>">
                    <?= e(_d($task['startdate'])); ?>
                </td>
                <td
                    data-order="<?= e($task['duedate']); ?>">
                    <?= e(_d($task['duedate'])); ?>
                </td>
                <td
                    data-order="<?= e($task['status']); ?>">
                    <?= format_task_status($task['status']); ?>
                </td>
                <?php if ($project->settings->view_milestones == 1) { ?>
                <td
                    data-order="<?= e($task['milestone_name']); ?>">
                    <?php if ($task['milestone'] != 0) {
                        echo e($task['milestone_name']);
                    } ?>
                </td>
                <?php } ?>
                <td
                    data-order="<?= e($task['billable']); ?>">
                    <?= $task['billable'] == 1 ? _l('task_billable_yes') : _l('task_billable_no'); ?>
                </td>
                <td
                    data-order="<?= e($task['billed']); ?>">
                    <?php if ($task['billed'] == 1) { ?>
                    <span class="label label-success pull-left">
                        <?= _l('task_billed_yes'); ?>
                    </span>
                    <?php } else { ?>
                    <span
                        class="label label-<?= ($task['billable'] == 1 ? 'danger' : 'default'); ?> pull-left">
                        <?= _l('task_billed_no'); ?>
                    </span>
                    <?php } ?>
                </td>
                <?php foreach ($custom_fields as $field) { ?>
                <td>
                    <?= get_custom_field_value($task['id'], $field['id'], 'tasks'); ?>
                </td>
                <?php } ?>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php } else {
    get_template_part('projects/project_task');
}
}
?>
<script>
    $(function() {
        var milesonesColumns = $('.tasks-phases .kan-ban-col:visible');
        var totalMilestones = milesonesColumns.length;
        if (totalMilestones > 0) {
            var phaseWidth = milesonesColumns.eq(0).width();
            $('.kan-ban-row').css('min-width', totalMilestones * (phaseWidth + 20) + 'px');
        } else if ($('.task-table-toggle').length > 0) {
            // When there are no milestones and the client is allowed to view milestones, show the tasks table as default
            taskTable();
        }
    });
</script>