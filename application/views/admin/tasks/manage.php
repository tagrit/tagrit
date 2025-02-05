<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content" id="vueApp">
        <div class="row">
            <?php if (! ($this->session->has_userdata('tasks_kanban_view')
                            && $this->session->userdata('tasks_kanban_view') == 'true')
            ) { ?>
            <div class="col-md-12 tw-mb-3">
                <h4 class="tw-my-0 tw-font-bold tw-text-xl">
                    <?= _l('tasks'); ?>
                </h4>
                <a
                    href="<?= admin_url('tasks/detailed_overview'); ?>">
                    <?= _l('detailed_overview'); ?>
                    &rarr;
                </a>
            </div>
            <div class="col-md-12 tw-mb-6">
                <?php $this->load->view('admin/tasks/_summary', ['table' => '.table-tasks']); ?>
            </div>
            <?php } ?>
        </div>
        <div class="row _buttons tw-mb-2">
            <div class="col-md-8">
                <?php if (staff_can('create', 'tasks')) { ?>
                <a href="#" onclick="new_task(<?php if ($this->input->get('project_id')) {
                    echo "'" . admin_url('tasks/task?rel_id=' . $this->input->get('project_id') . '&rel_type=project') . "'";
                } ?>); return false;" class="btn btn-primary pull-left new">
                    <i class="fa-regular fa-plus tw-mr-1"></i>
                    <?= _l('new_task'); ?>
                </a>
                <?php } ?>
                <a href="<?= admin_url(! $this->input->get('project_id') ? ('tasks/switch_kanban/' . $switch_kanban) : ('projects/view/' . $this->input->get('project_id') . '?group=project_tasks')); ?>"
                    class="btn btn-default mleft10 pull-left hidden-xs" data-toggle="tooltip" data-placement="top"
                    data-title="<?= $switch_kanban == 1 ? _l('switch_to_list_view') : _l('leads_switch_to_kanban'); ?>">
                    <?php if ($switch_kanban == 1) { ?>
                    <i class="fa-solid fa-table-list"></i>
                    <?php } else { ?>
                    <i class="fa-solid fa-grip-vertical"></i>
                    <?php } ?>
                </a>
            </div>
            <div class="col-md-4">
                <?php if ($this->session->has_userdata('tasks_kanban_view') && $this->session->userdata('tasks_kanban_view') == 'true') { ?>
                <div data-toggle="tooltip" data-placement="top"
                    data-title="<?= _l('search_by_tags'); ?>">
                    <?= render_input('search', '', '', 'search', ['data-name' => 'search', 'onkeyup' => 'tasks_kanban();', 'placeholder' => _l('search_tasks')], [], 'no-margin') ?>
                </div>
                <?php } else { ?>
                <?php $this->load->view('admin/tasks/filters', ['filters_wrapper_id' => 'vueApp', 'detached' => true]); ?>
                <?php } ?>
            </div>
        </div>
        <div class="row">

            <div class="col-md-12">
                <?php if ($this->session->has_userdata('tasks_kanban_view') && $this->session->userdata('tasks_kanban_view') == 'true') { ?>
                <div class="kan-ban-tab" id="kan-ban-tab" style="overflow:auto;">
                    <div class="row">
                        <div id="kanban-params">
                            <?= form_hidden('project_id', $this->input->get('project_id')); ?>
                        </div>
                        <div class="container-fluid">
                            <div id="kan-ban"></div>
                        </div>
                    </div>
                </div>
                <?php } else { ?>
                <div class="panel_s">
                    <div class="panel-body">

                        <a href="#" data-toggle="modal" data-target="#tasks_bulk_actions"
                            class="hide bulk-actions-btn table-btn"
                            data-table=".table-tasks"><?= _l('bulk_actions'); ?></a>
                        <div class="panel-table-full">
                            <?php $this->load->view('admin/tasks/_table', ['bulk_actions' => true]); ?>
                        </div>
                        <?php $this->load->view('admin/tasks/_bulk_actions'); ?>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    taskid = '<?= e($taskid); ?>';
    $(function() {
        tasks_kanban();
    });
</script>
</body>

</html>