<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<a href="#" class="dropdown-toggle notifications-icon !tw-px-0 tw-group" data-toggle="dropdown" aria-expanded="false">
    <span class="sm:tw-inline-flex sm:tw-items-center sm:tw-justify-center sm:tw-h-8 sm:tw-w-9 sm:-tw-mt-1.5">
        <i class="fa-regular fa-bell fa-lg tw-shrink-0 tw-text-neutral-400 group-hover:tw-text-neutral-800"></i>
        <?php if ($current_user->total_unread_notifications > 0) { ?>
        <span
            class="tw-leading-none tw-px-1 tw-py-0.5 tw-text-xs bg-info tw-z-10 tw-absolute tw-rounded-full -tw-right-0.5 -tw-top-2 sm:tw-top-2 tw-min-w-[18px] tw-min-h-[18px] tw-inline-flex tw-items-center tw-justify-center icon-notifications tw-mt-px"><?= e($current_user->total_unread_notifications); ?></span>
        <?php } ?>
    </span>
</a>
<?php $_notifications = $this->misc_model->get_user_notifications(); ?>
<ul class="dropdown-menu notifications animated fadeIn width400<?= count($_notifications) > 0 ? ' tw-pb-0' : ''; ?>"
    data-total-unread="<?= e($current_user->total_unread_notifications); ?>">
    <div class="tw-py-1 tw-px-3 tw-mb-1.5 tw-text-right">
        <a href="#" class="tw-text-right tw-inline text-muted"
            onclick="event.stopPropagation(); mark_all_notifications_as_read_inline(this); return false;">
            <?= _l('mark_all_as_read'); ?>
        </a>
    </div>
    <li class="divider"></li>
    <?php foreach ($_notifications as $notification) { ?>
    <li class="relative notification-wrapper"
        data-notification-id="<?= e($notification['id']); ?>">
        <a href="<?= empty($notification['link']) ? '#' : admin_url($notification['link']); ?>"
            onclick="<?= empty($notification['link']) ? 'event.preventDefault();' : ''; ?>"
            class="notification-handler !tw-p-0 <?= $notification['isread_inline'] == 0 ? ' unread-notification' : '';
        echo empty($notification['link']) ? ' tw-cursor-text' : ' tw-cursor-pointer notification-top notification-link'; ?>">
            <div class="tw-p-3 notification-box">
                <?php
        if (($notification['fromcompany'] == null && $notification['fromuserid'] != 0) || ($notification['fromcompany'] == null && $notification['fromclientid'] != 0)) {
            if ($notification['fromuserid'] != 0) {
                echo staff_profile_image($notification['fromuserid'], ['staff-profile-image-small', 'img-circle notification-image', 'pull-left']);
            } else {
                echo '<img src="' . e(contact_profile_image_url($notification['fromclientid'])) . '" class="client-profile-image-small img-circle pull-left notification-image">';
            }
        }
        ?>
                <div class="media-body">
                    <?php
        $additional_data = '';
        if (! empty($notification['additional_data'])) {
            $additional_data = unserialize($notification['additional_data']);

            $i = 0;

            foreach ($additional_data as $data) {
                if (strpos($data, '<lang>') !== false) {
                    $lang = get_string_between($data, '<lang>', '</lang>');
                    $temp = _l($lang);
                    if (strpos($temp, 'project_status_') !== false) {
                        $status = get_project_status_by_id(strafter($temp, 'project_status_'));
                        $temp   = $status['name'];
                    }
                    $additional_data[$i] = $temp;
                }
                $i++;
            }
        }
        $description = _l($notification['description'], $additional_data);
        if (($notification['fromcompany'] == null && $notification['fromuserid'] != 0)
        || ($notification['fromcompany'] == null && $notification['fromclientid'] != 0)) {
            if ($notification['fromuserid'] != 0) {
                $description = e($notification['from_fullname']) . ' - ' . $description;
            } else {
                $description = e($notification['from_fullname']) . ' - ' . $description . '<br /><span class="label inline-block mtop5 label-info">' . _l('is_customer_indicator') . '</span>';
            }
        }
        echo '<span class="notification-title">' . $description . '</span>'; ?><br />
                    <span class=" tw-text-smtext-has-action notification-date" data-placement="right"
                        data-toggle="tooltip"
                        data-title="<?= e(_dt($notification['date'])); ?>">
                        <?= e(time_ago($notification['date'])); ?>
                    </span>
                </div>
            </div>
        </a>

        <?php if ($notification['isread_inline'] == 0) { ?>
        <a href="#" class="text-muted pull-right not-mark-as-read-inline"
            onclick="set_notification_read_inline(<?= e($notification['id']); ?>);"
            data-placement="left" data-toggle="tooltip"
            data-title="<?= _l('mark_as_read'); ?>">
            <small>
                <i class="fa-regular fa-circle"></i>
            </small>
        </a>
        <?php } ?>
    </li>
    <li class="divider !tw-my-0"></li>
    <?php } ?>
    <div class="tw-text-center tw-p-4 tw-bg-neutral-50">
        <?php if (count($_notifications) > 0) { ?>
        <a class="btn btn-default"
            href="<?= admin_url('profile?notifications=true'); ?>">
            <?= _l('nav_view_all_notifications'); ?>
        </a>
        <?php } else { ?>
        <p class="tw-text-neutral-500 tw-font-medium tw-mb-0 tw-inline-flex tw-items-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="tw-w-6 tw-h-6 tw-mr-1">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
            </svg>
            <?= _l('nav_no_notifications'); ?>
        </p>
        <?php } ?>
    </div>

</ul>