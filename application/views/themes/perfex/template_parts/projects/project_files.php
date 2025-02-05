<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if ($project->settings->upload_files == 1) { ?>
<?= form_open_multipart(site_url('clients/project/' . $project->id), ['class' => 'dropzone mbot15', 'id' => 'project-files-upload']); ?>
<input type="file" name="file" multiple class="hide" />
<?= form_close(); ?>
<div class="pull-left mbot20">
  <a href="<?= site_url('clients/download_all_project_files/' . $project->id); ?>"
    class="btn btn-primary">
    <?= _l('download_all'); ?>
  </a>
</div>
<div class="pull-right mbot20">
  <button class="gpicker" data-on-pick="projectFileGoogleDriveSave">
    <i class="fa-brands fa-google" aria-hidden="true"></i>
    <?= _l('choose_from_google_drive'); ?>
  </button>
  <div id="dropbox-chooser-project-files"></div>
</div>
<?php } ?>
<table class="table dt-table" data-order-col="4" data-order-type="desc">
  <thead>
    <tr>
      <th>
        <?= _l('project_file_filename'); ?>
      </th>
      <th>
        <?= _l('project_file__filetype'); ?>
      </th>
      <th>
        <?= _l('project_discussion_last_activity'); ?>
      </th>
      <th>
        <?= _l('project_discussion_total_comments'); ?>
      </th>
      <th>
        <?= _l('project_file_dateadded'); ?>
      </th>
      <?php if (get_option('allow_contact_to_delete_files') == 1) { ?>
      <th><?= _l('options'); ?></th>
      <?php } ?>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($files as $file) {
        $path = get_upload_path_by_type('project') . $project->id . '/' . $file['file_name']; ?>
    <tr>
      <td
        data-order="<?= e($file['file_name']); ?>">
        <a href="#"
          onclick="view_project_file(<?= e($file['id']); ?>,<?= e($file['project_id']); ?>); return false;">
          <?php if (is_image(PROJECT_ATTACHMENTS_FOLDER . $project->id . '/' . $file['file_name']) || (! empty($file['external']) && ! empty($file['thumbnail_link']))) {
              echo '<div class="text-left"><i class="fa fa-spinner fa-spin mtop30"></i></div>';
              echo '<img class="project-file-image img-table-loading" src="#" data-orig="' . project_file_url($file, true) . '" width="100">';
              echo '</div>';
          }
        echo $file['subject']; ?></a>
      </td>
      <td
        data-order="<?= e($file['filetype']); ?>">
        <?= e($file['filetype']); ?>
      </td>
      <td
        data-order="<?= e($file['last_activity']); ?>">
        <?= e(! is_null($file['last_activity']) ? time_ago($file['last_activity']) : _l('project_discussion_no_activity')); ?>
      </td>
      <?php $total_file_comments = total_rows(db_prefix() . 'projectdiscussioncomments', ['discussion_id' => $file['id'], 'discussion_type' => 'file']); ?>
      <td data-order="<?= e($total_file_comments); ?>">
        <?= e($total_file_comments); ?>
      </td>
      <td
        data-order="<?= e($file['dateadded']); ?>">
        <?= e(_dt($file['dateadded'])); ?>
      </td>
      <?php if (get_option('allow_contact_to_delete_files') == 1) { ?>
      <td>
        <?php if ($file['contact_id'] == get_contact_user_id()) { ?>
        <a href="<?= site_url('clients/delete_file/' . $file['id'] . '/project'); ?>"
          class="btn btn-danger btn-icon _delete">
          <i class="fa fa-remove"></i>
        </a>
        <?php } ?>
      </td>
      <?php } ?>
    </tr>
    <?php } ?>
  </tbody>
</table>
<div id="project_file_data"></div>