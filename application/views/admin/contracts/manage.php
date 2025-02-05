<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div id="contract_summary" class="tw-mb-6">
                    <h4 class="tw-mt-0 tw-mb-2 tw-font-bold tw-text-xl">
                        <?= _l('contracts'); ?>
                    </h4>
                    <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-3 lg:tw-grid-cols-5 tw-gap-2">
                        <div
                            class="tw-bg-white tw-border-neutral-300/80 tw-shadow-sm tw-text-sm tw-border tw-border-solid tw-rounded-lg tw-px-4 tw-py-3 text-sm tw-flex-1 tw-flex tw-items-center tw-font-medium">
                            <span class="tw-font-semibold tw-mr-1 rtl:tw-ml-1">
                                <?= e($count_active); ?></span>
                            <span
                                class="text-info"><?= _l('contract_summary_active'); ?></span>
                        </div>
                        <div
                            class="tw-bg-white tw-border-neutral-300/80 tw-shadow-sm tw-text-sm tw-border tw-border-solid tw-rounded-lg tw-px-4 tw-py-3 text-sm tw-flex-1 tw-flex tw-items-center tw-font-medium">
                            <span class="tw-font-semibold tw-mr-1 rtl:tw-ml-1">
                                <?= e($count_expired); ?></span>
                            <span
                                class="text-danger"><?= _l('contract_summary_expired'); ?></span>
                        </div>
                        <div
                            class="tw-bg-white tw-border-neutral-300/80 tw-shadow-sm tw-text-sm tw-border tw-border-solid tw-rounded-lg tw-px-4 tw-py-3 text-sm tw-flex-1 tw-flex tw-items-center tw-font-medium">
                            <span class="tw-font-semibold tw-mr-1 rtl:tw-ml-1">
                                <?= count($expiring); ?>
                            </span>
                            <span
                                class="text-warning"><?= _l('contract_summary_about_to_expire'); ?></span>
                        </div>
                        <div
                            class="tw-bg-white tw-border-neutral-300/80 tw-shadow-sm tw-text-sm tw-border tw-border-solid tw-rounded-lg tw-px-4 tw-py-3 text-sm tw-flex-1 tw-flex tw-items-center tw-font-medium">
                            <span class="tw-font-semibold tw-mr-1 rtl:tw-ml-1">
                                <?= e($count_recently_created); ?></span>
                            <span
                                class="text-success"><?= _l('contract_summary_recently_added'); ?></span>
                        </div>
                        <div
                            class="tw-bg-white tw-border-neutral-300/80 tw-shadow-sm tw-text-sm tw-border tw-border-solid tw-rounded-lg tw-px-4 tw-py-3 text-sm tw-flex-1 tw-flex tw-items-center tw-font-medium">
                            <span class="tw-font-semibold tw-mr-1 rtl:tw-ml-1">
                                <?= e($count_trash); ?></span>
                            <span
                                class="text-muted"><?= _l('contract_summary_trash'); ?></span>
                        </div>
                    </div>
                </div>
                <div class="_buttons">
                    <?php if (staff_can('create', 'contracts')) { ?>
                    <a href="<?= admin_url('contracts/contract'); ?>"
                        class="btn btn-primary pull-left display-block">
                        <i class="fa-regular fa-plus tw-mr-1"></i>
                        <?= _l('new_contract'); ?>
                    </a>
                    <?php } ?>
                    <div id="vueApp" class="tw-inline pull-right tw-ml-0 sm:tw-ml-1.5 rtl:tw-mr-1.5 rtl:tw-ml-0">
                        <app-filters id="<?= $table->id(); ?>"
                            view="<?= $table->viewName(); ?>"
                            :saved-filters="<?= $table->filtersJs(); ?>"
                            :available-rules="<?= $table->rulesJs(); ?>">
                        </app-filters>
                    </div>
                    <div class="clearfix"></div>

                </div>

                <div class="row tw-my-2">
                    <div class="col-md-6">
                        <div class="panel_s">
                            <div class="panel-body">
                                <h4 class="tw-font-semibold tw-mb-8">
                                    <?= _l('contract_summary_by_type'); ?>
                                </h4>
                                <div class="relative" style="max-height:400px">
                                    <canvas class="chart" height="400" id="contracts-by-type-chart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="panel_s">
                            <div class="panel-body">
                                <h4 class="tw-font-semibold tw-mb-8">
                                    <?= _l('contract_summary_by_type_value'); ?>
                                    (<span data-toggle="tooltip"
                                        data-title="<?= _l('base_currency_string'); ?>"
                                        class="text-has-action">
                                        <?= e($base_currency->name); ?></span>)
                                </h4>
                                <div class="relative" style="max-height:400px">
                                    <canvas class="chart" height="400" id="contracts-value-by-type-chart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel_s tw-mt-2 sm:tw-mt-4">
                    <?= form_hidden('custom_view'); ?>
                    <div class="panel-body">

                        <div class="panel-table-full">
                            <?php $this->load->view('admin/contracts/table_html'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function() {
        initDataTable('.table-contracts', admin_url + 'contracts/table', undefined, undefined, {},
            <?= hooks()->apply_filters('contracts_table_default_order', json_encode([6, 'asc'])); ?>
        );

        new Chart($('#contracts-by-type-chart'), {
            type: 'bar',
            data: <?= $chart_types; ?> ,
            options: {
                legend: {
                    display: false,
                },
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        display: true,
                        ticks: {
                            suggestedMin: 0,
                        }
                    }]
                }
            }
        });
        new Chart($('#contracts-value-by-type-chart'), {
            type: 'line',
            data: <?= $chart_types_values; ?> ,
            options: {
                responsive: true,
                legend: {
                    display: false,
                },
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        display: true,
                        ticks: {
                            suggestedMin: 0,
                        }
                    }]
                }
            }
        });
    });
</script>
</body>

</html>