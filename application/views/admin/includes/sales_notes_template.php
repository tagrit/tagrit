<?php defined('BASEPATH') or exit('No direct script access allowed');
$len = count($notes);
$i   = 0;
?>
<div id="sales-notes-wrapper" data-total="<?= e($len); ?>">
    <?php foreach ($notes as $note) { ?>
    <div class="media sales-note-wrapper">
        <div class="media-left">
            <a
                href="<?= admin_url('profile/' . $note['addedfrom']); ?>">
                <?= staff_profile_image($note['addedfrom'], ['staff-profile-image-small', 'media-object']); ?>
            </a>
        </div>
        <div class="media-body">
            <?php if ($note['addedfrom'] == get_staff_user_id() || is_admin()) { ?>
            <a href="#" class="pull-right text-muted"
                onclick="delete_sales_note(this,<?= e($note['id']); ?>);return false;">
                <i class="fa-regular fa-trash-can"></i>
            </a>
            <a href="#" class="text-muted pull-right tw-mr-2"
                onclick="toggle_edit_note(<?= e($note['id']); ?>);return false;">
                <i class="fa-regular fa-pen-to-square"></i>
            </a>
            <?php } ?>
            <p class="media-heading tw-font-semibold tw-mb-0"><a
                    href="<?= admin_url('profile/' . $note['addedfrom']); ?>">
                    <?= e(get_staff_full_name($note['addedfrom'])); ?>
                </a>
            </p>
            <span class="tw-text-sm tw-text-neutral-500">
                <?= e(_dt($note['dateadded'])); ?>
            </span>
            <div data-note-description="<?= e($note['id']); ?>"
                class="text-muted mtop10">
                <?= process_text_content_for_display($note['description']); ?>
            </div>
            <div data-note-edit-textarea="<?= e($note['id']); ?>"
                class="hide mtop15">
                <?= render_textarea('note', '', $note['description']); ?>
                <?php if ($note['addedfrom'] == get_staff_user_id() || is_admin()) { ?>
                <div class="text-right">
                    <button type="button" class="btn btn-default"
                        onclick="toggle_edit_note(<?= e($note['id']); ?>);return false;">
                        <?= _l('cancel'); ?>
                    </button>
                    <button type="button" class="btn btn-primary"
                        onclick="edit_note(<?= e($note['id']); ?>);">
                        <?= _l('update_note'); ?>
                    </button>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php if ($i >= 0 && $i != $len - 1) {
            echo '<hr />';
        }
        ?>
    </div>
    <?php
$i++;
    } ?>
</div>