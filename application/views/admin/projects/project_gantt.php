<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-12">
        <?php if (count($gantt_data) > 0) { ?>
        <div class="form-group pull-right">
            <select class="selectpicker" name="gantt_view">
                <option value="Day">
                    <?= _l('gantt_view_day'); ?>
                </option>
                <option value="Week">
                    <?= _l('gantt_view_week'); ?>
                </option>
                <option value="Month" selected>
                    <?= _l('gantt_view_month'); ?>
                </option>
                <option value="Year">
                    <?= _l('gantt_view_year'); ?>
                </option>
            </select>
        </div>
        <?php } else { ?>
        <p><?= _l('no_tasks_found'); ?>
        </p>
        <?php } ?>
        <div class="form-group pull-right mright10">
            <select class="selectpicker" name="gantt_type" onchange="gantt_filter();">
                <option value="milestones" <?= ! $this->input->get('gantt_type') || $this->input->get('gantt_type') == 'milestones' ? ' selected' : ''; ?>>
                    <?= _l('project_milestones'); ?>
                </option>
                <option value="members" <?= $this->input->get('gantt_type') == 'members' ? ' selected' : ''; ?>>
                    <?php
                    if (staff_can('view', 'tasks') || (staff_cant('view', 'tasks') && get_option('show_all_tasks_for_project_member') == 1)) {
                        echo _l('project_members');
                    } else {
                        echo _l('home_my_tasks');
                    } ?>
                </option>
                <option value="status" <?= $this->input->get('gantt_type') == 'status' ? ' selected' : ''; ?>>
                    <?= _l('task_status'); ?>
                </option>
            </select>
        </div>
        <div class="form-group pull-right mright10">
            <select class="selectpicker" name="gantt_task_status" onchange="gantt_filter(this);"
                data-none-selected-text="<?= _l('task_status'); ?>">
                <option value="">
                    <?= _l('task_list_all'); ?>
                </option>
                <?php foreach ($task_statuses as $status) { ?>
                <option
                    value="<?= e($status['id']); ?>"
                    <?= $this->input->get('gantt_task_status') == $status['id'] ? ' selected' : ''; ?>>
                    <?= e($status['name']); ?>
                </option>
                <?php } ?>
            </select>
        </div>
    </div>
</div>
<div class="clearfix"></div>
<svg id="gantt"></svg>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        var gantt_data = <?= json_encode($gantt_data); ?> ;

        if (gantt_data.length > 0) {
            var gantt = new Gantt("#gantt", gantt_data, {
                view_modes: ['Day', 'Week', 'Month', 'Year'],
                view_mode: 'Month',
                date_format: 'YYYY-MM-DD',
                popup_trigger: 'click mouseover',
                on_date_change: function(data, start, end) {
                    if (typeof(data.task_id) != 'undefined') {
                        $.post(admin_url + 'tasks/gantt_date_update/' + data.task_id, {
                            startdate: moment(start).format('YYYY-MM-DD'),
                            duedate: moment(end).format('YYYY-MM-DD'),
                        });
                    }
                },
                on_click: function(data) {
                    if (typeof(data.task_id) != 'undefined') {
                        init_task_modal(data.task_id);
                    }
                }
            });

            $('body').on('mouseleave', '.grid-row', function() {
                gantt.hide_popup();
            })

            $('select[name$="gantt_view"').change(function(el) {
                let view = $(el.target).val();
                gantt.change_view_mode(view);
            })
        }

    });
</script>