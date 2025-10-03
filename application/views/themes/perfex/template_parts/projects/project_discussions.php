<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if (! isset($discussion)) {
    if ($project->settings->open_discussions == 1) { ?>
<a href="#" onclick="new_discussion();return false;"
    class="btn btn-primary mtop5"><?= _l('new_project_discussion'); ?></a>
<hr />
<!-- Miles Stones -->
<div class="modal fade" id="discussion" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?= form_open(site_url('clients/project/' . $project->id), ['id' => 'discussion_form']); ?>
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
                        <?= form_hidden('action', 'new_discussion'); ?>
                        <div id="additional_discussion"></div>
                        <?= render_input('subject', 'project_discussion_subject'); ?>
                        <?= render_textarea('description', 'project_discussion_description'); ?>
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
        </div>
        <!-- /.modal-content -->
        <?= form_close(); ?>
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<!-- Mile stones end -->
<?php } ?>
<table class="table dt-table" data-order-col="1" data-order-type="desc">
    <thead>
        <tr>
            <th>
                <?= _l('project_discussion_subject'); ?>
            </th>
            <th>
                <?= _l('project_discussion_last_activity'); ?>
            </th>
            <th>
                <?= _l('project_discussion_total_comments'); ?>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($discussions as $discussion) { ?>
        <tr>
            <td>
                <a
                    href="<?= site_url('clients/project/' . $project->id . '?group=' . $group . '&discussion_id=' . $discussion['id']); ?>">
                    <?= e($discussion['subject']); ?>
                </a>
            </td>
            <td
                data-order="<?= e($discussion['last_activity']); ?>">
                <?= e(! is_null($discussion['last_activity']) ? time_ago($discussion['last_activity']) : _l('project_discussion_no_activity')); ?>
            </td>
            <td>
                <?= e($discussion['total_comments']); ?>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php
} else { ?>
<?= form_hidden('discussion_user_profile_image_url', $discussion_user_profile_image_url); ?>
<?= form_hidden('discussion_id', $discussion->id); ?>
<h3 class="tw-font-medium tw-mt-0 tw-text-lg">
    <?= e($discussion->subject); ?>
</h3>
<p class="tw-mb-0 tw-text-neutral-700">
    <?= e(_l('project_discussion_posted_on', _d($discussion->datecreated))); ?>
</p>
<p class="tw-mb-0 tw-text-neutral-700">
    <?= e(_l('project_discussion_posted_by', $discussion->staff_id == 0 ? get_contact_full_name($discussion->contact_id) : get_staff_full_name($discussion->staff_id))); ?>
</p>
<p class="tw-text-neutral-700">
    <?= _l('project_discussion_total_comments'); ?>:
    <?= total_rows(db_prefix() . 'projectdiscussioncomments', ['discussion_id' => $discussion->id, 'discussion_type' => 'regular']); ?>
</p>
<div class="tw-text-neutral-500">
    <?= process_text_content_for_display($discussion->description); ?>
</div>
<hr />
<div id="discussion-comments" class="tc-content"></div>
<?php } ?>