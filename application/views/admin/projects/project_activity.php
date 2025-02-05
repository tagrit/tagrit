<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="panel_s">
    <div class="panel-body">
        <div class="activity-feed">
            <?php foreach ($activity as $activity) { ?>
            <div class="feed-item">
                <div class="row">
                    <div class="col-md-8">
                        <div class="date"><span class="text-has-action" data-toggle="tooltip"
                                data-title="<?= e(_dt($activity['dateadded'])); ?>">
                                <?= e(time_ago($activity['dateadded'])); ?>
                            </span>
                        </div>
                        <div class="text">
                            <?php $fullname = e($activity['fullname']); ?>
                            <?php if ($activity['staff_id'] != 0) { ?>
                            <a
                                href="<?= admin_url('profile/' . $activity['staff_id']); ?>">
                                <?= staff_profile_image($activity['staff_id'], ['staff-profile-xs-image', 'pull-left mright10']); ?>
                            </a>
                            <?php } elseif ($activity['contact_id'] != 0) {
                                $fullname = '<span class="label label-info inline-block tw-mb-1">' . _l('is_customer_indicator') . '</span> ' . $fullname = e($activity['fullname']); ?>
                            <a
                                href="<?= admin_url('clients/client/' . get_user_id_by_contact_id($activity['contact_id']) . '?contactid=' . $activity['contact_id']); ?>">
                                <img src="<?= e(contact_profile_image_url($activity['contact_id'])); ?>"
                                    class="staff-profile-xs-image pull-left tw-mr-2.5">
                            </a>
                            <?php } ?>
                            <p class="tw-mb-0 tw-mt-2.5">
                                <?= $fullname . ' - <b>' . e($activity['description']) . '</b>'; ?>
                            </p>
                            <p class="tw-mb-0 text-muted mleft30 tw-mt-1">
                                <?= $activity['additional_data']; ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4 text-right">
                        <p class="text-muted tw-text-sm">
                            <?= _l('project_activity_visible_to_customer'); ?>
                        </p>
                        <div class="pull-right">
                            <div class="onoffswitch">
                                <input type="checkbox"
                                    <?= staff_cant('create', 'projects') ? 'disabled' : ''; ?>
                                id="<?= e($activity['id']); ?>"
                                data-id="<?= e($activity['id']); ?>"
                                class="onoffswitch-checkbox"
                                data-switch-url="<?= admin_url(); ?>projects/change_activity_visibility"
                                <?= $activity['visible_to_customer'] == 1 ? 'checked' : ''; ?>>
                                <label class="onoffswitch-label"
                                    for="<?= e($activity['id']); ?>"></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>

    </div>
</div>