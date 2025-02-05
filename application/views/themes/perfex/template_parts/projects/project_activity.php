<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="mtop15"></div>
<div class="activity-feed">
    <?php if ($project->settings->view_activity_log == 1) { ?>
    <?php foreach ($activity as $activity) { ?>
    <div class="feed-item">
        <div class="date">
            <?= e(time_ago($activity['dateadded'])); ?>
        </div>
        <?php $fullname = e($activity['fullname']); ?>

        <?php if ($activity['staff_id'] != 0) { ?>
        <?= staff_profile_image($activity['staff_id'], ['staff-profile-image-small', 'pull-left mright10']); ?>
        <?php } elseif ($activity['contact_id'] != 0) { ?>
        <img src="<?= e(contact_profile_image_url($activity['contact_id'])); ?>"
            class="client-profile-image-small pull-left mright10">
        <?php } ?>
        <div class="media-body">
            <div class="display-block">
                <p class="mtop5 no-mbot">
                    <?= $fullname . ' - <b>' . e($activity['description']) . '</b>'; ?>
                </p>
                <p class="text-muted mtop5">
                    <?= $activity['additional_data']; ?>
                </p>
            </div>
        </div>
        <hr class="hr-10" />
    </div>
    <?php } ?>
    <?php } ?>
</div>