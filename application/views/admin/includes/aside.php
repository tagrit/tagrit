<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<aside id="menu" class="sidebar">
    <div class="dropdown sidebar-user-profile tw-mt-[80px] tw-mx-1.5 ">
        <a href="#"
            class="dropdown-toggle profile -tw-mt-1 tw-text-neutral-700 tw-font-medium hover:tw-text-neutral-800 tw-border-solid tw-border-neutral-400/40 tw-border tw-rounded-lg tw-bg-white tw-py-2 tw-px-2.5 hover:tw-bg-neutral-100 focus:tw-bg-neutral-100 tw-block tw-shadow-sm focus:tw-text-neutral-800"
            data-toggle="dropdown" aria-expanded="false">
            <span class="tw-inline-flex tw-items-center tw-gap-x-3 tw-pt-0.5">
                <?= staff_profile_image($current_user->staffid, ['img', 'img-responsive', 'staff-profile-image-small']); ?>
                <span>
                    <span
                        class="tw-truncate tw-block tw-w-[140px] tw-font-semibold"><?= get_staff_full_name(); ?></span>
                    <span class="tw-font-normal tw-text-neutral-500 tw-truncate tw-block tw-w-[140px] tw-text-sm">
                        <?= get_staff()->email; ?>
                    </span>
                </span>
            </span>
        </a>
        <ul class="dropdown-menu tw-w-full">
            <li class="header-my-profile"><a
                    href="<?= admin_url('profile'); ?>"><?= _l('nav_my_profile'); ?></a>
            </li>
            <li class="header-my-timesheets"><a
                    href="<?= admin_url('staff/timesheets'); ?>"><?= _l('my_timesheets'); ?></a>
            </li>
            <li class="header-edit-profile"><a
                    href="<?= admin_url('staff/edit_profile'); ?>"><?= _l('nav_edit_profile'); ?>
                </a>
            </li>
            <?php if (! is_language_disabled()) { ?>
            <li class="dropdown-submenu pull-left header-languages">
                <a href="#"
                    tabindex="-1"><?= _l('language'); ?></a>
                <ul class="dropdown-menu dropdown-menu">
                    <li
                        class="<?= $current_user->default_language == '' ? 'active' : ''; ?>">
                        <a
                            href="<?= admin_url('staff/change_language'); ?>">
                            <?= _l('system_default_string'); ?>
                        </a>
                    </li>
                    <?php foreach ($this->app->get_available_languages() as $user_lang) { ?>
                    <li
                        class="<?= $current_user->default_language == $user_lang ? 'active' : ''; ?>">
                        <a
                            href="<?= admin_url('staff/change_language/' . $user_lang); ?>">
                            <?= e(ucfirst($user_lang)); ?>
                        </a>
                        <?php } ?>
                </ul>
            </li>
            <?php } ?>
            <li class="header-logout">
                <a href="#"
                    onclick="logout(); return false;"><?= _l('nav_logout'); ?></a>
            </li>
        </ul>
    </div>
    <ul class="nav metis-menu tw-mt-[15px]" id="side-menu">

        <?php
         hooks()->do_action('before_render_aside_menu');
?>
        <?php foreach ($sidebar_menu as $key => $item) {
            if ((isset($item['collapse']) && $item['collapse']) && count($item['children']) === 0) {
                continue;
            } ?>
        <li class="menu-item-<?= e($item['slug']); ?>"
            <?= _attributes_to_string($item['li_attributes'] ?? []); ?>>
            <a href="<?= count($item['children']) > 0 ? '#' : $item['href']; ?>"
                aria-expanded="false"
                <?= _attributes_to_string($item['href_attributes'] ?? []); ?>>
                <i
                    class="<?= e($item['icon']); ?> menu-icon"></i>
                <span class="menu-text">
                    <?= e(_l($item['name'], '', false)); ?>
                </span>
                <?php if (count($item['children']) > 0) { ?>
                <span class="fa arrow pleft5"></span>
                <?php } ?>
                <?php if (isset($item['badge'], $item['badge']['value']) && ! empty($item['badge'])) {?>
                <span
                    class="badge pull-right
               <?= isset($item['badge']['type']) && $item['badge']['type'] != '' ? "bg-{$item['badge']['type']}" : 'bg-info' ?>"
                    <?= (isset($item['badge']['type']) && $item['badge']['type'] == '')
                       || isset($item['badge']['color']) ? "style='background-color: {$item['badge']['color']}'" : '' ?>>
                    <?= e($item['badge']['value']) ?>
                </span>
                <?php } ?>
            </a>
            <?php if (count($item['children']) > 0) { ?>
            <ul class="nav nav-second-level collapse" aria-expanded="false">
                <?php foreach ($item['children'] as $submenu) { ?>
                <li class="sub-menu-item-<?= e($submenu['slug']); ?>"
                    <?= _attributes_to_string($submenu['li_attributes'] ?? []); ?>>
                    <a href="<?= e($submenu['href']); ?>"
                        <?= _attributes_to_string($submenu['href_attributes'] ?? []); ?>>
                        <?php if (! empty($submenu['icon'])) { ?>
                        <i
                            class="<?= e($submenu['icon']); ?> menu-icon"></i>
                        <?php } ?>
                        <span class="sub-menu-text">
                            <?= _l($submenu['name'], '', false); ?>
                        </span>
                    </a>
                    <?php if (isset($submenu['badge'], $submenu['badge']['value']) && ! empty($submenu['badge'])) {?>
                    <span
                        class="badge pull-right
               <?= isset($submenu['badge']['type']) && $submenu['badge']['type'] != '' ? "bg-{$submenu['badge']['type']}" : 'bg-info' ?>"
                        <?= (isset($submenu['badge']['type']) && $submenu['badge']['type'] == '')
               || isset($submenu['badge']['color']) ? "style='background-color: {$submenu['badge']['color']}'" : '' ?>>
                        <?= e($submenu['badge']['value']) ?>
                    </span>
                    <?php } ?>
                </li>
                <?php } ?>
            </ul>
            <?php } ?>
        </li>
        <?php hooks()->do_action('after_render_single_aside_menu', $item); ?>
        <?php
        } ?>
        <?php if ($this->app->show_setup_menu() == true && (is_staff_member() || is_admin())) { ?>
        <li<?php if (get_option('show_setup_menu_item_only_on_hover') == 1) {
            echo ' style="display:none;"';
        } ?> id="setup-menu-item">
            <a href="#" class="open-customizer"><i class="fa fa-cog menu-icon"></i>
                <span class="menu-text">
                    <?= _l('setting_bar_heading'); ?>
                    <?php
               if ($modulesNeedsUpgrade = $this->app_modules->number_of_modules_that_require_database_upgrade()) {
                   echo '<span class="badge menu-badge !tw-bg-warning-600">' . $modulesNeedsUpgrade . '</span>';
               }
            ?>
                </span>
            </a>
            <?php } ?>
            </li>
            <?php hooks()->do_action('after_render_aside_menu'); ?>
            <?php $this->load->view('admin/projects/pinned'); ?>
    </ul>
</aside>