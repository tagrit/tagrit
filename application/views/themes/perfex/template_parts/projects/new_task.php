<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-12 mtop10">
        <?= form_open_multipart('', ['id' => 'task-form']); ?>
        <?= form_hidden('action', 'new_task'); ?>
        <h2 class="tw-text-xl tw-font-semibold tw-mt-0">
            <?= _l('new_task'); ?>
        </h2>
        <div class="form-group">
            <label for="name">
                <?= _l('task_add_edit_subject'); ?>
            </label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="row">
            <div
                class="col-md-<?= $project->settings->view_milestones == 1 ? '6' : '12'; ?>">
                <div class="form-group">
                    <label for="priority" class="control-label">
                        <?= _l('task_add_edit_priority'); ?>
                    </label>
                    <select name="priority" class="selectpicker" id="priority" data-width="100%"
                        data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>">
                        <option value="1" <?= get_option('default_task_priority') == 1 ? 'selected' : ''; ?>>
                            <?= _l('task_priority_low'); ?>
                        </option>
                        <option value="2" <?= get_option('default_task_priority') == 2 ? 'selected' : ''; ?>>
                            <?= _l('task_priority_medium'); ?>
                        </option>
                        <option value="3" <?= get_option('default_task_priority') == 3 ? 'selected' : ''; ?>>
                            <?= _l('task_priority_high'); ?>
                        </option>
                        <option value="4" <?= get_option('default_task_priority') == 4 ? 'selected' : ''; ?>>
                            <?= _l('task_priority_urgent'); ?>
                        </option>
                        <?php hooks()->apply_filters('task_priorities_select', 0); ?>
                    </select>
                </div>
            </div>
            <?php if ($project->settings->view_milestones == 1) { ?>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="milestone">
                        <?= _l('task_milestone'); ?>
                    </label>
                    <select name="milestone" id="milestone" class="selectpicker" data-width="100%"
                        data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>">
                        <option value=""></option>
                        <?php foreach ($milestones as $milestone) { ?>
                        <option
                            value="<?= e($milestone['id']); ?>">
                            <?= e($milestone['name']); ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <?php } ?>
        </div>
        <div class="row">
            <div class="col-md-6">
                <?= render_date_input('startdate', 'task_add_edit_start_date', _d(date('Y-m-d')), ['required' => true]); ?>
            </div>
            <div class="col-md-6">
                <?= render_date_input('duedate', 'task_add_edit_due_date', '', $project->deadline ? ['data-date-end-date' => $project->deadline] : []); ?>
            </div>
        </div>
        <?php if ($project->settings->view_team_members == 1) { ?>
        <div class="form-group">
            <label for="assignees">
                <?= _l('task_single_assignees_select_title'); ?>
            </label>
            <select class="selectpicker" multiple="true" name="assignees[]" id="assignees" data-width="100%"
                data-live-search="true">
                <?php foreach ($members as $member) { ?>
                <option
                    value="<?= e($member['staff_id']); ?>"
                    <?= count($members) == 1 ? 'selected' : ''; ?>>
                    <?= e(get_staff_full_name($member['staff_id'])); ?>
                </option>
                <?php } ?>
            </select>
        </div>
        <?php } ?>
        <div class="form-group">
            <label for="description">
                <?= _l('task_add_edit_description'); ?>
            </label>
            <textarea name="description" id="description" rows="10" class="form-control"></textarea>
        </div>
        <?= render_custom_fields('tasks', '', ['show_on_client_portal' => 1]); ?>
        <?php if ($project->settings->upload_on_tasks == 1) { ?>
        <hr />
        <div class="row attachments">
            <div class="attachment">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="attachment"
                            class="control-label"><?= _l('add_task_attachments'); ?></label>
                        <div class="input-group">
                            <input type="file"
                                extension="<?= str_replace('.', '', get_option('allowed_files')); ?>"
                                filesize="<?= file_upload_max_size(); ?>"
                                class="form-control" name="attachments[0]">
                            <span class="input-group-btn">
                                <button class="btn btn-default add_more_attachments" type="button">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>

        <button type="submit" class="btn btn-primary pull-right">
            <?= _l('submit'); ?>
        </button>
        <?= form_close(); ?>
    </div>
</div>