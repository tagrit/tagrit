<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $_announcements = get_announcements_for_user();
if (sizeof($_announcements) > 0 && isset($dashboard) && is_staff_member()) { ?>
<div class="col-lg-12 tw-mt-1.5">
    <div>
        <?php foreach ($_announcements as $__announcement) { ?>
        <div class="alert alert-info alert-dismissible announcement tc-content tw-mb-3" role="alert">
            <a href="<?= admin_url('misc/dismiss_announcement/' . $__announcement['announcementid']); ?>"
                class="alert-link pull-right" aria-label="Close">
                <i class="fa-solid fa-xmark"></i>
            </a>
            <?php if (is_admin()) { ?>
            <a href="<?= admin_url('announcements/announcement/' . $__announcement['announcementid']); ?>"
                class="alert-link pull-right tw-mr-3 -tw-mt-px">
                <i class="fa-regular fa-pen-to-square"></i>
            </a>
            <?php } ?>

            <h4 class="alert-title tw-mb-0 tw-flex tw-items-center tw-space-x-2">
                <i class="fa-solid fa-bullhorn"></i>
                <span><?= _l('announcement'); ?>
                    -</span>
                <span class="tw-text-xs tw-font-medium">
                    <?= _dt($__announcement['dateadded']); ?>
                </span>
            </h4>
            <?php if ($__announcement['showname'] == 1) { ?>
            <p class="tw-text-sm !tw-my-0 !-tw-mb-1.5">
                <?= e(_l('announcement_from')) . ' <span class="tw-font-medium">' . e($__announcement['userid']) . '</span>'; ?>
            </p>
            <?php } ?>
            <hr />
            <h4 class="alert-title">
                <?= e($__announcement['name']); ?>
            </h4>
            <div class="[&>p:last-child]:tw-mb-0">
                <?= check_for_links($__announcement['message']); ?>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
<?php } ?>
<?php hooks()->do_action('before_start_render_content'); ?>