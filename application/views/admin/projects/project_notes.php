<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700">
    <?= _l('project_note_private'); ?>
</h4>
<?= form_open(admin_url('projects/save_note/' . $project->id)); ?>
<?= render_textarea('content', '', $staff_notes, [], [], '', 'tinymce'); ?>
<div class="text-right">
    <button type="submit"
        class="btn btn-primary"><?= _l('project_save_note'); ?></button>
</div>
<?= form_close(); ?>