<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
$CI = &get_instance();

$total_packages = $CI->db->count_all_results(perfex_saas_table('packages'));
$total_companies = $CI->db->count_all_results(perfex_saas_table('companies'));
$total_subscriptions = $CI->db->where(perfex_saas_column('packageid') . ' >', 0)->count_all_results(db_prefix() . 'invoices');
$invoices_total = $CI->perfex_saas_model->get_invoices_total([]);

$money_icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="tw-w-6 tw-h-6 tw-text-neutral-500">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                </svg>'; ?>

<div class="widget relative tw-mb-8" id="widget-saas_top_stats" data-name="<?= _l('perfex_saas_dashboard_statistic'); ?>">
    <div class="panel_s">
        <div class="panel-body padding-10">
            <div class="widget-dragger"></div>

            <p class="tw-font-medium tw-flex tw-items-center tw-mb-0 tw-space-x-1.5 rtl:tw-space-x-reverse tw-p-1.5">
                <?= $money_icon; ?>

                <span class="tw-text-neutral-700">
                    <?= _l('perfex_saas_dashboard_statistic'); ?>
                </span>
            </p>

            <hr class="-tw-mx-3 tw-mt-3 tw-mb-6">
            <div class="row tw-flex tw-flex-wrap tw-justify-center">

                <!-- statistic cards -->
                <div class="col-md-5 tw-flex tw-flex-wrap">
                    <div class="col-xs-12 col-md-6 tw-mb-2">
                        <div class="top_stats_wrapper">
                            <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center"> <i class="fa fa-university tw-mr-2"></i> <?= _l('perfex_saas_companies'); ?>
                                </div>
                                <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"><?= $total_companies; ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12 col-md-6 tw-mb-2">
                        <div class="top_stats_wrapper">
                            <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center mr-2"> <i class="fa fa-list tw-mr-2"></i> <?= _l('perfex_saas_packages'); ?>
                                </div>
                                <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"><?= $total_packages; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6 tw-mb-2">
                        <div class="top_stats_wrapper">
                            <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center mr-2"> <i class="fa-regular fa-file-lines tw-mr-2"></i>
                                    <?= _l('perfex_saas_recurring_invoices'); ?>
                                </div>
                                <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"><?= $total_subscriptions; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6 tw-mb-2">
                        <div class="top_stats_wrapper">
                            <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center mr-2">
                                    <?= $money_icon; ?>
                                    <span class="tw-ml-1"><?= _l('perfex_saas_total_revenue_overdue'); ?></span>
                                </div>
                                <span class="tw-font-semibold tw-shrink-0 text-danger"><?= app_format_money($invoices_total['overdue'], $invoices_total['currency']); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6 tw-mb-2">
                        <div class="top_stats_wrapper">
                            <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center mr-2">
                                    <?= $money_icon; ?>
                                    <span class="tw-ml-1"><?= _l('perfex_saas_total_revenue_due'); ?></span>
                                </div>
                                <span class="tw-font-semibold text-warning tw-shrink-0"><?= app_format_money($invoices_total['due'], $invoices_total['currency']); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6 tw-mb-2">
                        <div class="top_stats_wrapper">
                            <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                                <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center mr-2">
                                    <?= $money_icon; ?>
                                    <span class="tw-ml-1"><?= _l('perfex_saas_total_revenue_paid'); ?></span>
                                </div>
                                <span class="tw-font-semibold tw-shrink-0 text-success"><?= app_format_money($invoices_total['paid'], $invoices_total['currency']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- package invoice chart -->
                <div class="col-md-7">
                    <div class="relative" style="height:250px">
                        <canvas class="chart" height="250" id="package_invoice_stats"></canvas>
                    </div>
                    <p class="text-center"><?= _l('perfex_saas_dashboard_package_invoice_stats'); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>