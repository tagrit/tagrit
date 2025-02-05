<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- Miles Stones -->
<div class="modal fade" id="milestone" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?= form_open(admin_url('projects/milestone'), ['id' => 'milestone_form']); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span
                        class="edit-title"><?= _l('edit_milestone'); ?></span>
                    <span
                        class="add-title"><?= _l('new_milestone'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?= form_hidden('project_id', $project->id); ?>
                        <div id="additional_milestone"></div>
                        <?= render_input('name', 'milestone_name'); ?>
                        <?= render_date_input('start_date', 'milestone_start_date', _d(date('Y-m-d')), ['data-date-min-date' => $project->start_date]); ?>
                        <?= render_date_input('due_date', 'milestone_due_date', '', array_merge(['data-date-min-date' => $project->start_date], ($project->deadline) ? ['data-date-end-date' => $project->deadline] : [])); ?>
                        <?= render_textarea('description', 'milestone_description'); ?>
                        <div class="checkbox">
                            <input type="checkbox" id="description_visible_to_customer"
                                name="description_visible_to_customer">
                            <label
                                for="description_visible_to_customer"><?= _l('description_visible_to_customer'); ?></label>
                        </div>
                        <?php if ($project->settings->view_milestones == 1) {?>
                        <div class="checkbox">
                            <input type="checkbox" id="hide_from_customer" name="hide_from_customer">
                            <label for="hide_from_customer">
                                <i class="fa-regular fa-circle-question" data-toggle="tooltip"
                                    title="<?= _l('hide_milestone_from_customer_help') ?>"></i>
                                <?= _l('hide_milestone_from_customer'); ?>
                            </label>
                        </div>
                        <?php } ?>
                        <?= render_input('milestone_order', 'project_milestone_order', total_rows(db_prefix() . 'milestones', ['project_id' => $project->id]) + 1, 'number'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                    data-dismiss="modal"><?= _l('close'); ?></button>
                <button type="submit"
                    class="btn btn-primary"><?= _l('submit'); ?></button>
            </div>
        </div><!-- /.modal-content -->
        <?= form_close(); ?>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Mile stones end -->