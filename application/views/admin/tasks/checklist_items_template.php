<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="clearfix"></div>
<div class="progress mtop15 no-mbot hide">
    <div class="progress-bar not-dynamic progress-bar-default task-progress-bar" role="progressbar" aria-valuenow="40"
        aria-valuemin="0" aria-valuemax="100" style="width:0%">
    </div>
</div>
<div class="tw-flex tw-flex-col">
    <?php foreach ($checklists as $list) { ?>
    <div>
        <div class="checklist"
            data-checklist-id="<?= e($list['id']); ?>">
            <div class="tw-flex">
                <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="">
                    <input type="checkbox"
                        <?= $list['finished'] == 1 && $list['finished_from'] != get_staff_user_id() && ! is_admin() ? 'disabled' : ''; ?>
                    name="checklist-box"
                    <?= $list['finished'] == 1 ? 'checked' : ''; ?>>
                    <label for="">
                        <span class="hide">
                            <?= e($list['description']); ?>
                        </span>
                    </label>
                </div>
                <div class="tw-grow">
                    <textarea data-taskid="<?= e($task_id); ?>"
                        name="checklist-description" rows="1"
                        <?= $list['addedfrom'] != get_staff_user_id() && staff_cant('edit', 'tasks') ? 'disabled' : ''; ?>><?= clear_textarea_breaks($list['description']); ?></textarea>
                </div>
                <div class="mleft10 tw-inline-flex tw-items-center tw-space-x-1 sm:tw-space-x-2">
                    <?php if (($list['addedfrom'] == get_staff_user_id() || $current_user_is_creator || is_admin()) && count($task_staff_members) > 0) { ?>
                    <span class="dropdown"
                        data-title="<?= _l('task_checklist_assign'); ?>"
                        data-toggle="tooltip">
                        <a href="#" class="tw-text-neutral-500 dropdown-toggle tw-mt-px" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false"
                            id="checklist-item-<?= e($list['id']); ?>"
                            onclick="return false;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor"
                                class="tw-w-[calc(theme(spacing.5)-2px)] tw-h-[calc(theme(spacing.5)-2px)] tw-mt-px">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                            </svg>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-left"
                            aria-labelledby="checklist-item-<?= e($list['id']); ?>">
                            <?php foreach ($task_staff_members as $_staff) { ?>
                            <li>
                                <a href="#"
                                    onclick="save_checklist_assigned_staff('<?= $_staff['staffid']; ?>', '<?= e($list['id']); ?>'); return false;">
                                    <?= e($_staff['firstname'] . ' ' . $_staff['lastname']); ?>
                                </a>
                            </li>
                            <?php } ?>
                        </ul>
                    </span>
                    <?php } ?>

                    <?php if (staff_can('create', 'checklist_templates')) { ?>
                    <a href="#"
                        class="tw-text-neutral-500 save-checklist-template<?= $list['description'] == '' || total_rows(db_prefix() . 'tasks_checklist_templates', ['description' => $list['description']]) > 0 ? ' hide' : ''; ?>"
                        data-toggle="tooltip"
                        data-title="<?= _l('save_as_template'); ?>"
                        onclick="save_checklist_item_template(<?= e($list['id']); ?>,this); return false;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor"
                            class="tw-w-[calc(theme(spacing.5)-2px)] tw-h-[calc(theme(spacing.5)-2px)]">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8.25 7.5V6.108c0-1.135.845-2.098 1.976-2.192.373-.03.748-.057 1.123-.08M15.75 18H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08M15.75 18.75v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5A3.375 3.375 0 006.375 7.5H5.25m11.9-3.664A2.251 2.251 0 0015 2.25h-1.5a2.251 2.251 0 00-2.15 1.586m5.8 0c.065.21.1.433.1.664v.75h-6V4.5c0-.231.035-.454.1-.664M6.75 7.5H4.875c-.621 0-1.125.504-1.125 1.125v12c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V16.5a9 9 0 00-9-9z" />
                        </svg>
                    </a>
                    <?php } ?>
                    <?php if (staff_can('delete', 'tasks') || $list['addedfrom'] == get_staff_user_id()) { ?>
                    <a href="#" class="tw-text-neutral-500 remove-checklist -tw-mt-1 tw-ml-1"
                        onclick="delete_checklist_item(<?= e($list['id']); ?>,this); return false;">
                        <i class="fa-regular fa-trash-can"></i>
                    </a>
                    <?php } ?>
                </div>
            </div>
            <?php if ($list['finished'] == 1 || $list['addedfrom'] != get_staff_user_id() || ! empty($list['assigned'])) { ?>
            <p class="font-medium-xs mtop15 tw-text-neutral-500 checklist-item-info">
                <?php
                if ($list['addedfrom'] != get_staff_user_id()) {
                    echo e(_l('task_created_by', get_staff_full_name($list['addedfrom'])));
                }

                if ($list['addedfrom'] != get_staff_user_id() && $list['finished'] == 1) {
                    echo ' - ';
                }

                if ($list['finished'] == 1) {
                    echo e(_l('task_checklist_item_completed_by', get_staff_full_name($list['finished_from'])));
                }

                if (($list['addedfrom'] != get_staff_user_id() || $list['finished'] == 1) && ! empty($list['assigned'])) {
                    echo ' - ';
                }

                if (! empty($list['assigned'])) {
                    echo e(_l('task_checklist_assigned', get_staff_full_name($list['assigned'])));
                }
                ?>
            </p>
            <?php } ?>
        </div>
    </div>
    <?php } ?>
</div>
<script>
    $(function() {
        $("#checklist-items").sortable({
            helper: 'clone',
            items: 'div.checklist',
            update: function(event, ui) {
                update_checklist_order();
            }
        });

        setTimeout(function() {
            do_task_checklist_items_height();
        }, 200);

        init_selectpicker();
        var _hideCompletedItems = '<?= $hide_completed_items ?>'
        if (_hideCompletedItems == 1) {
            toggle_completed_checklist_items_visibility();
        }
    });

    function toggle_completed_checklist_items_visibility(el, forceShow) {
        var _task_checklist_items = $("body").find("input[name='checklist-box']");
        $.each(_task_checklist_items, function() {
            var $this = $(this);
            if ($this.prop('checked') == true) {
                $this.closest('.checklist ').toggleClass('hide');
            }
        });
        if (typeof el != 'undefined') {
            var _hideCompleted = $(el).data('hide');
            $(el).addClass('hide');
            $(el).siblings().removeClass('hide');
            $.post(admin_url + 'staff/save_completed_checklist_visibility', {
                task_id: "<?= $task_id ?>",
                hideCompleted: _hideCompleted
            }, "json");
        }
    }

    function save_checklist_assigned_staff(staffId, list_id) {
        $.post(
            admin_url + 'tasks/save_checklist_assigned_staff', {
                assigned: staffId,
                checklistId: list_id,
                taskId: "<?= $task_id ?>",
            }
        ).done(function(response) {
            init_tasks_checklist_items(false, "<?= $task_id ?>");
        });
    }
</script>