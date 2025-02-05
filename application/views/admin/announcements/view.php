<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-7">
                <h4 class="tw-my-0 tw-font-bold tw-text-lg tw-text-neutral-700">
                    <?= e($announcement->name); ?>
                    <?php if (is_admin()) { ?>
                    <a href="<?= admin_url('announcements/announcement/' . $announcement->announcementid); ?>"
                        class="pull-right tw-font-normal">
                        <small><?= _l('edit'); ?></small>
                    </a>
                    <?php } ?>
                </h4>
                <p class="tw-text-neutral-500 tw-mb-0">
                    <?= e(_l('announcement_date', _dt($announcement->dateadded))); ?>
                </p>
                <?php if ($announcement->showname == 1) { ?>
                <p class="tw-text-neutral-500">
                    <?= e(_l('announcement_from') . ' ' . $announcement->userid); ?>
                </p>
                <?php } ?>
                <div class="panel_s tw-mt-3">
                    <div class="panel-body tc-content [&>p:last-child]:tw-mb-0">
                        <?= $announcement->message; ?>
                    </div>
                </div>
            </div>
            <?php if (count($recent_announcements) > 0) { ?>
            <div class="col-md-5">
                <h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700">
                    <?= _l('announcements_recent'); ?>
                </h4>
                <div class="panel_s">
                    <div class="panel-body">
                        <?php foreach ($recent_announcements as $announcement) { ?>
                        <a class="bold"
                            href="<?= admin_url('announcements/view/' . $announcement['announcementid']); ?>">
                            <?= e($announcement['name']); ?></a>
                        <p class="text-muted no-margin">
                            <?= e(_l('announcement_date', _dt($announcement['dateadded']))); ?>
                        </p>
                        <?php if ($announcement['showname'] == 1) { ?>
                        <p class="text-muted no-margin">
                            <?= _l('announcement_from') . ' ' . $announcement['userid']; ?>
                        </p>
                        <?php } ?>
                        <div class="mtop15">
                            <?= strip_tags(mb_substr($announcement['message'], 0, 250)) . '...'; ?>
                            <hr class="hr-panel-separator" />
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php init_tail(); ?>
    </body>

    </html>