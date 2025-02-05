<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700">
            <?= _l('filter_by'); ?>
        </h4>
        <div class="">
            <?= form_open(admin_url('projects/gantt'), ['method' => 'get', 'id' => 'ganttFiltersForm']); ?>
            <div class="tw-flex sm:tw-flex-row tw-flex-col tw-gap-2 sm:gap-3 sm:tw-items-center">
                <div class="tw-flex-1">
                    <select class="selectpicker"
                        data-none-selected-text="<?= _l('all'); ?>"
                        name="status[]" data-width="100%" multiple="true">
                        <?php foreach ($statuses as $status) { ?>
                        <option
                            value="<?= e($status['id']); ?>"
                            <?= in_array($status['id'], $selected_statuses) ? ' selected' : ''; ?>>
                            <?= e($status['name']); ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
                <?php
        /**
         * Only show this filter if user has permission for projects view otherwise
         * wont need this becuase by default this filter will be applied
         */
        if (staff_can('view', 'projects')) { ?>
                <div class="tw-flex-1">
                    <select class="selectpicker" data-live-search="true"
                        data-title="<?= _l('project_member'); ?>"
                        name="member" data-width="100%">
                        <option value=""></option>
                        <?php foreach ($project_members as $member) { ?>
                        <option
                            value="<?= e($member['staff_id']); ?>"
                            <?= $selectedMember == $member['staff_id'] ? ' selected' : ''; ?>>
                            <?= e($member['firstname'] . ' ' . $member['lastname']); ?>
                        </option>
                        <?php } ?>
                        </option>
                    </select>
                </div>
                <?php } ?>
                <div class="tw-flex-1">
                    <button type="submit" class="btn btn-primary">
                        <?= _l('apply'); ?>
                    </button>
                </div>
            </div>
            <?= form_close(); ?>
        </div>
        <div class="clearfix mbot10"></div>

        <div class="row">
            <div class="col-md-12">
                <?php if (count($gantt_data) > 0) { ?>
                <div class="form-group">
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
                <div id="gantt"></div>
                <?php } else { ?>
                <p><?= _l('no_tasks_found'); ?>
                </p>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
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
                if (typeof(data.project_id) != 'undefined') {
                    var projectViewUrl =
                        '<?= admin_url('projects/view'); ?>';
                    window.location.href = projectViewUrl + '/' + data.project_id;
                } else if (typeof(data.task_id) != 'undefined') {
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
</script>
</body>

</html>