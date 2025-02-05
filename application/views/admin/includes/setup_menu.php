<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div id="setup-menu-wrapper"
    class="sidebar animated<?= $this->session->has_userdata('setup-menu-open')
    && $this->session->userdata('setup-menu-open') == true ? ' display-block' : ''; ?>">
    <ul class="nav metis-menu tw-mt-[57px]" id="setup-menu">
        <div
            class="tw-flex tw-items-center tw-justify-between tw-space-x-2 rtl:tw-space-x-reverse tw-pl-4 tw-pr-2.5 tw-py-3">

            <span class="text-left tw-font-semibold customizer-heading">
                <?= _l('setting_bar_heading'); ?>
            </span>
            <a
                class="close-customizer tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 hover:tw-bg-neutral-200 tw-p-0.5 hover:tw-rounded-md">
                <i class="fa fa-close fa-fw"></i>
            </a>
        </div>
        <?php
        $totalSetupMenuItems = 0;

foreach ($setup_menu as $key => $item) {
    if (isset($item['collapse']) && count($item['children']) === 0) {
        continue;
    }
    $totalSetupMenuItems++; ?>
        <li
            class="menu-item-<?= e($item['slug']); ?>">
            <a href="<?= count($item['children']) > 0 ? '#' : $item['href']; ?>"
                aria-expanded="false">
                <i
                    class="<?= e($item['icon']); ?> menu-icon"></i>
                <span class="menu-text">
                    <?= html_purify(_l($item['name'], '', false)); ?>
                </span>
                <?php if (count($item['children']) > 0) { ?>
                <span class="fa arrow"></span>
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
                <li
                    class="sub-menu-item-<?= e($submenu['slug']); ?>">
                    <a
                        href="<?= e($submenu['href']); ?>">
                        <?php if (! empty($submenu['icon'])) { ?>
                        <i
                            class="<?= e($submenu['icon']); ?> menu-icon"></i>
                        <?php } ?>
                        <span class="sub-menu-text">
                            <?= e(_l($submenu['name'], '', false)); ?>
                        </span>
                    </a>
                    <?php if (isset($submenu['badge'], $submenu['badge']['value']) && ! empty($submenu['badge'])) {?>
                    <span
                        class="badge pull-right mright5
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
        <?php hooks()->do_action('after_render_single_setup_menu', $item); ?>
        <?php } ?>
        <?php if (get_option('show_help_on_setup_menu') == 1 && is_admin()) {
            $totalSetupMenuItems++; ?>
        <li>
            <a href="<?= hooks()->apply_filters('help_menu_item_link', 'https://help.perfexcrm.com'); ?>"
                target="_blank">
                <?= hooks()->apply_filters('help_menu_item_text', _l('setup_help')); ?>
            </a>
        </li>
        <?php } ?>
    </ul>
</div>
<?php $this->app->set_setup_menu_visibility($totalSetupMenuItems); ?>