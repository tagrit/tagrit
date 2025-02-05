<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<table class="table dt-table table-timesheets" data-order-col="3" data-order-type="desc">
    <thead>
        <tr>
            <th>
                <?= _l('project_timesheet_user'); ?>
            </th>
            <th>
                <?= _l('project_timesheet_task'); ?>
            </th>
            <th>
                <?= _l('project_timesheet_start_time'); ?>
            </th>
            <th>
                <?= _l('project_timesheet_end_time'); ?>
            </th>
            <th>
                <?= _l('project_timesheet_time_spend'); ?>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($timesheets as $timesheet) { ?>
        <tr>
            <td>
                <?= staff_profile_image($timesheet['staff_id'], ['staff-profile-image-small']) . ' ' . e($timesheet['staff_name']); ?>
            </td>
            <td>
                <a
                    href="<?= site_url('clients/project/' . $project->id . '?group=project_tasks&taskid=' . $timesheet['task_data']->id); ?>">
                    <?= e($timesheet['task_data']->name); ?>
                </a>
            </td>
            <td
                data-order="<?= date('Y-m-d H:i:s', $timesheet['start_time']); ?>">
                <?= e(_dt($timesheet['start_time'], true)); ?>
            </td>
            <td
                data-order="<?= ! is_null($timesheet['end_time']) ? date('Y-m-d H:i:s', $timesheet['end_time']) : ''; ?>">
                <?= ! is_null($timesheet['end_time']) ? e(_dt($timesheet['end_time'], true)) : '-'; ?>
            </td>
            <td><?= e(seconds_to_time_format($timesheet['total_spent'])); ?>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>