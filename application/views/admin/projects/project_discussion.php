<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- Miles Stones -->
<div class="modal fade" id="discussion" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?= form_open(admin_url('projects/discussion'), ['id' => 'discussion_form']); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span
                        class="edit-title"><?= _l('edit_discussion'); ?></span>
                    <span
                        class="add-title"><?= _l('new_project_discussion'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?= form_hidden('project_id', $project->id); ?>
                        <div id="additional_discussion"></div>
                        <?= render_input('subject', 'project_discussion_subject'); ?>
                        <?= render_textarea('description', 'project_discussion_description'); ?>
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="show_to_customer" checked id="show_to_customer">
                            <label
                                for="show_to_customer"><?= _l('project_discussion_show_to_customer'); ?></label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                    data-dismiss="modal"><?= _l('close'); ?></button>
                <button type="submit" class="btn btn-primary"
                    data-loading-text="<?= _l('wait_text'); ?>"
                    data-autocomplete="off"
                    data-form="#discussion_form"><?= _l('submit'); ?></button>
            </div>
        </div><!-- /.modal-content -->
        <?= form_close(); ?>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Mile stones end -->