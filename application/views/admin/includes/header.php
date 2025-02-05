<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div id="header">
    <button type="button"
        class="hide-menu tw-inline-flex tw-bg-transparent tw-border-0 tw-p-1 tw-mt-4 hover:tw-bg-neutral-600/10 tw-text-neutral-600 hover:tw-text-neutral-800 focus:tw-text-neutral-800 focus:tw-outline-none tw-rounded-md tw-mx-4 ltr:md:tw-ml-6 rtl:md:tw-mr-6 ltr:tw-float-left  rtl:tw-float-right">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="tw-h-4 tw-w-4 tw-text-current">
            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M2.25 18.003h19.5m-19.5-6h19.5m-19.5-6h19.5"></path>
        </svg>
    </button>
    <nav>
        <div class="tw-flex tw-justify-between">
            <div class="tw-overflow-hidden tw-shrink-0">
                <div id="logo"
                    class="tw-h-[57px] tw-hidden md:tw-flex tw-items-center [&_img]:tw-h-9 [&_img]:tw-w-auto">
                    <?= get_company_logo(get_admin_uri() . '/', '!tw-mt-0', 'dark')?>
                </div>
            </div>
            <div class="tw-flex tw-flex-1 sm:tw-flex-initial">
                <div id="top_search"
                    class="tw-inline-flex tw-relative dropdown sm:tw-ml-1.5 sm:tw-mr-3 tw-max-w-xl tw-flex-auto tw-group/top-search"
                    data-toggle="tooltip" data-placement="bottom"
                    data-title="<?= _l('search_by_tags'); ?>">
                    <input type="search" id="search_input"
                        class="ltr:tw-pr-4 ltr:tw-pl-9 rtl:tw-pr-9 rtl:tw-pl-4 tw-ml-1 tw-mt-2 focus:!tw-ring-0 tw-w-full !tw-placeholder-neutral-500 !tw-shadow-none tw-text-neutral-800 focus:!tw-placeholder-neutral-600 hover:!tw-placeholder-neutral-600 sm:tw-w-[350px] tw-h-[38px] tw-border-0 tw-border-solid !tw-border-white !tw-bg-neutral-100 !tw-rounded-xl"
                        placeholder="<?= _l('top_search_placeholder'); ?>"
                        autocomplete="off">
                    <div id="top_search_button" class="tw-absolute rtl:tw-right-2 ltr:tw-left-2 tw-top-2.5">
                        <button
                            class="tw-outline-none tw-border-0 tw-p-2 tw-text-neutral-400 group-focus-within/top-search:tw-text-neutral-600 tw-bg-transparent">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                    <div id="search_results">
                    </div>
                    <ul class="dropdown-menu search-results animated fadeIn search-history" id="search-history">
                    </ul>

                </div>
                <ul class="nav navbar-nav visible-md visible-lg">
                    <?php $quickActions = collect($this->app->get_quick_actions_links())->reject(function ($action) {
                        return isset($action['permission']) && staff_cant('create', $action['permission']);
                    }); ?>
                    <?php if ($quickActions->isNotEmpty()) { ?>
                    <li class="icon tw-relative ltr:tw-mr-1.5 rtl:tw-ml-1.5 -tw-mt-1"
                        title="<?= _l('quick_create'); ?>"
                        data-toggle="tooltip" data-placement="bottom">
                        <a href="#" class="!tw-px-0 tw-group !tw-text-white" data-toggle="dropdown">
                            <span
                                class="tw-rounded-full tw-bg-primary-600 tw-text-white tw-inline-flex tw-items-center tw-justify-center tw-h-7 tw-w-7 -tw-mt-1 group-hover:!tw-bg-primary-700">
                                <i class="fa-regular fa-plus fa-lg"></i>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right animated fadeIn tw-text-base">
                            <li class="dropdown-header tw-mb-1">
                                <?= _l('quick_create'); ?>
                            </li>
                            <?php foreach ($quickActions as $key => $item) {
                                $url = '';
                                if (isset($item['permission'])) {
                                    if (staff_cant('create', $item['permission'])) {
                                        continue;
                                    }
                                }
                                if (isset($item['custom_url'])) {
                                    $url = $item['url'];
                                } else {
                                    $url = admin_url('' . $item['url']);
                                }
                                $href_attributes = '';
                                if (isset($item['href_attributes'])) {
                                    foreach ($item['href_attributes'] as $key => $val) {
                                        $href_attributes .= $key . '="' . $val . '"';
                                    }
                                } ?>
                            <li>
                                <a href="<?= e($url); ?>"
                                    <?= $href_attributes; ?>
                                    class="tw-group tw-inline-flex tw-space-x-0.5 tw-text-neutral-700">
                                    <?php if (isset($item['icon'])) { ?>
                                    <i
                                        class="<?= e($item['icon']); ?> tw-text-neutral-400 group-hover:tw-text-neutral-600 tw-h-5 tw-w-5"></i>
                                    <?php } ?>
                                    <span>
                                        <?= e($item['name']); ?>
                                    </span>
                                </a>
                            </li>
                            <?php
                            } ?>
                        </ul>
                    </li>
                    <?php } ?>
                </ul>
            </div>

            <div class="mobile-menu tw-shrink-0 ltr:tw-ml-4 rtl:tw-mr-4">
                <button type="button"
                    class="navbar-toggle visible-md visible-sm visible-xs mobile-menu-toggle collapsed tw-ml-1.5"
                    data-toggle="collapse" data-target="#mobile-collapse" aria-expanded="false">
                    <i class="fa fa-chevron-down fa-lg"></i>
                </button>
                <ul class="mobile-icon-menu tw-inline-flex tw-mt-5">
                    <?php
               // To prevent not loading the timers twice
            if (is_mobile()) { ?>
                    <li class="dropdown notifications-wrapper header-notifications tw-block ltr:tw-mr-3 rtl:tw-ml-3">
                        <?php $this->load->view('admin/includes/notifications'); ?>
                    </li>
                    <li class="header-timers ltr:tw-mr-1.5 rtl:tw-ml-1.5">
                        <a href="#" id="top-timers" class="dropdown-toggle top-timers tw-block tw-h-5 tw-w-5"
                            data-toggle="dropdown">
                            <i
                                class="fa-regular fa-clock fa-lg tw-text-neutral-400 group-hover:tw-text-neutral-800 tw-shrink-0<?= count($startedTimers) > 0 ? ' tw-animate-spin-slow' : ''; ?>"></i>
                            <span
                                class="tw-leading-none tw-px-1 tw-py-0.5 tw-text-xs bg-success tw-z-10 tw-absolute tw-rounded-full -tw-right-3 -tw-top-2 tw-min-w-[18px] tw-min-h-[18px] tw-inline-flex tw-items-center tw-justify-center icon-started-timers<?= $totalTimers = count($startedTimers) == 0 ? ' hide' : ''; ?>"><?= count($startedTimers); ?></span>
                        </a>
                        <ul class="dropdown-menu animated fadeIn started-timers-top width300" id="started-timers-top">
                            <?php $this->load->view('admin/tasks/started_timers', ['startedTimers' => $startedTimers]); ?>
                        </ul>
                    </li>
                    <?php } ?>
                </ul>
                <div class="mobile-navbar collapse" id="mobile-collapse" aria-expanded="false" style="height: 0px;"
                    role="navigation">
                    <ul class="nav navbar-nav">
                        <li class="header-my-profile"><a
                                href="<?= admin_url('profile'); ?>">
                                <?= _l('nav_my_profile'); ?>
                            </a>
                        </li>
                        <li class="header-my-timesheets"><a
                                href="<?= admin_url('staff/timesheets'); ?>">
                                <?= _l('my_timesheets'); ?>
                            </a>
                        </li>
                        <li class="header-edit-profile"><a
                                href="<?= admin_url('staff/edit_profile'); ?>">
                                <?= _l('nav_edit_profile'); ?>
                            </a>
                        </li>
                        <?php if (is_staff_member()) { ?>
                        <li class="header-newsfeed">
                            <a href="#" class="open_newsfeed mobile">
                                <?= _l('whats_on_your_mind'); ?>
                            </a>
                        </li>
                        <?php } ?>
                        <li class="header-logout">
                            <a href="#" onclick="logout(); return false;">
                                <?= _l('nav_logout'); ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <ul class="nav navbar-nav navbar-right -tw-mt-px">
                <?php do_action_deprecated('after_render_top_search', [], '3.0.0', 'admin_navbar_start'); ?>
                <?php hooks()->do_action('admin_navbar_start'); ?>
                <?php if (staff_can('view', 'settings')) { ?>
                <li>
                    <a
                        href="<?= admin_url('settings'); ?>">
                        <span class="tw-flex tw-items-center tw-gap-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="tw-size-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <span><?= _l('settings'); ?></span>
                        </span>
                    </a>
                </li>
                <?php } ?>
                <?php if (is_staff_member()) { ?>
                <li class="icon header-newsfeed -tw-mr-1.5">
                    <a href="#" class="open_newsfeed desktop" data-toggle="tooltip"
                        title="<?= _l('whats_on_your_mind'); ?>"
                        data-placement="bottom">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor"
                            class="tw-w-[calc(theme(spacing.5)-1px)] tw-h-[calc(theme(spacing.5)-1px)]">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" />
                        </svg>
                    </a>
                </li>
                <?php } ?>

                <li class="icon header-todo">
                    <a href="<?= admin_url('todo'); ?>"
                        data-toggle="tooltip"
                        title="<?= _l('nav_todo_items'); ?>"
                        data-placement="bottom" class="">
                        <i class="fa-regular fa-square-check fa-lg tw-shrink-0"></i>
                        <span
                            class="tw-leading-none tw-px-1 tw-py-0.5 tw-text-xs bg-warning tw-z-10 tw-absolute tw-rounded-full -tw-right-0.5 tw-top-2 tw-min-w-[18px] tw-min-h-[18px] tw-inline-flex tw-items-center tw-justify-center nav-total-todos<?= $current_user->total_unfinished_todos == 0 ? ' hide' : ''; ?>">
                            <?= e($current_user->total_unfinished_todos); ?>
                        </span>
                    </a>
                </li>

                <li class="icon header-timers timer-button tw-relative ltr:tw-mr-1.5 rtl:tw-ml-1.5"
                    data-placement="bottom" data-toggle="tooltip"
                    data-title="<?= _l('my_timesheets'); ?>">
                    <a href="#" id="top-timers" class="top-timers !tw-px-0 tw-group" data-toggle="dropdown">
                        <span class="tw-inline-flex tw-items-center tw-justify-center tw-h-8 tw-w-9 -tw-mt-1.5">
                            <i
                                class="fa-regular fa-clock fa-lg tw-text-neutral-400 group-hover:tw-text-neutral-800 tw-shrink-0<?= count($startedTimers) > 0 ? ' tw-animate-spin-slow' : ''; ?>"></i>
                        </span>
                        <span
                            class="tw-leading-none tw-px-1 tw-py-0.5 tw-text-xs bg-success tw-z-10 tw-absolute tw-rounded-full -tw-right-1.5 tw-top-2 tw-min-w-[18px] tw-min-h-[18px] tw-inline-flex tw-items-center tw-justify-center icon-started-timers<?= $totalTimers = count($startedTimers) == 0 ? ' hide' : ''; ?>">
                            <?= count($startedTimers); ?>
                        </span>
                    </a>
                    <ul class="dropdown-menu animated fadeIn started-timers-top width300" id="started-timers-top">
                        <?php $this->load->view('admin/tasks/started_timers', ['startedTimers' => $startedTimers]); ?>
                    </ul>
                </li>

                <li class="icon dropdown tw-relative tw-block notifications-wrapper header-notifications rtl:tw-ml-3"
                    data-toggle="tooltip"
                    title="<?= _l('nav_notifications'); ?>"
                    data-placement="bottom">
                    <?php $this->load->view('admin/includes/notifications'); ?>
                </li>


                <?php hooks()->do_action('admin_navbar_end'); ?>
            </ul>
        </div>
    </nav>
</div>