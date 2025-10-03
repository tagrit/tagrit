<?php defined('BASEPATH') or exit('No direct script access allowed');
ob_start();
$len = count($comments);
$i   = 0;

foreach ($comments as $comment) { ?>
<div class="col-md-12 comment-item"
    data-commentid="<?= e($comment['id']); ?>">
    <?php if ($comment['staffid'] != 0) { ?>
    <a
        href="<?= admin_url('profile/' . $comment['staffid']); ?>">
        <?= staff_profile_image($comment['staffid'], [
            'staff-profile-image-small',
            'media-object img-circle pull-left mright10',
        ]);
        ?>
    </a>
    <?php } ?>
    <?php if ($comment['staffid'] == get_staff_user_id() || is_admin()) { ?>
    <a href="#" class="pull-right"
        onclick="remove_proposal_comment(<?= e($comment['id']); ?>); return false;">
        <i class="fa fa-times text-danger"></i>
        <a href="#" class="pull-right mright5"
            onclick="toggle_proposal_comment_edit(<?= e($comment['id']); ?>);return false;">
            <i class="fa-regular fa-pen-to-square"></i>
        </a>
    </a>
    <?php } ?>
    <div class="media-body">
        <div class="mtop5">
            <?php if ($comment['staffid'] != 0) { ?>
            <a
                href="<?= admin_url('profile/' . $comment['staffid']); ?>">
                <?= e(get_staff_full_name($comment['staffid'])); ?>
            </a>
            <?php } else { ?>
            <?= '<b>' . _l('is_customer_indicator') . '</b>'; ?>
            <?php } ?>
            <small class="text-muted text-has-action" data-toggle="tooltip"
                data-title="<?= e(_dt($comment['dateadded'])); ?>">
                -
                <?= e(time_ago($comment['dateadded'])); ?></small>
        </div>
        <div data-proposal-comment="<?= e($comment['id']); ?>"
            class="tw-mt-3">
            <?= process_text_content_for_display($comment['content']); ?>
        </div>
        <div data-proposal-comment-edit-textarea="<?= e($comment['id']); ?>"
            class="hide tw-mt-3">
            <?= render_textarea('comment-content', '', $comment['content']); ?>
            <?php if ($comment['staffid'] == get_staff_user_id() || is_admin()) { ?>
            <div class="text-right">
                <button type="button" class="btn btn-default"
                    onclick="toggle_proposal_comment_edit(<?= e($comment['id']); ?>);return false;">
                    <?= _l('cancel'); ?>
                </button>
                <button type="button" class="btn btn-primary"
                    onclick="edit_proposal_comment(<?= e($comment['id']); ?>);">
                    <?= _l('update_comment'); ?>
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
<?php $i++;
} ?>