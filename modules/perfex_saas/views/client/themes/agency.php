<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- company management -->
<div class="ps-view tw-mt-8" id="companies" style="display:none;">
    <div
        class="<?= empty($companies) ? 'tw-w-full' : 'tw-grid tw-gap-3 tw-grid-cols-1 sm:tw-grid-cols-2 md:tw-grid-cols-3'; ?>">

        <?php

        $autolaunch_option = get_option('perfex_saas_autolaunch_instance');
        foreach ($companies as $company) :
            $company_options[] = ['key' => $company->slug, 'name' => $company->name . " - $company->slug"];
            $company->package_invoice = $invoice;
            $deploying = perfex_saas_company_status_can_deploy($company);

            $autolaunch = (time() - strtotime($companies[0]->created_at)) < 60 * 2 ? 'autolaunch' : ''; // auto launch if less than 60 secs*2
            $autolaunch =  $autolaunch_option === 'no' ? '' : ($autolaunch_option === 'yes' ? 'autolaunch' : $autolaunch);

        ?>
        <div
            class="company <?= $company->status; ?> panel_s tw-p-4 tw-py-2  tw-bg-neutral-150 hover:tw-bg-neutral-200 tw-flex tw-flex-col tw-justify-between tw-relative <?= $autolaunch; ?>">
            <!--- Menu -->
            <?php include('includes/dropdown_menu.php'); ?>

            <!-- Details -->
            <div class="panel_body tw-flex tw-flex-col tw-items-center tw-justify-center text-center">
                <h3 class="tw-mb-0">
                    <?= $company->name; ?>
                </h3>
                <div class="info tw-flex tw-flex-col tw-w-full">
                    <small class="d-block tw-text-muted">(<?= $company->slug ?>)</small>

                    <div class="tw-flex tw-items-center tw-gap-2 links">
                        <?= !empty($company->metadata->pending_custom_domain) ? $pending_custom_domain_notice : ''; ?>
                        <div class="tw-flex tw-flex-col  tw-w-full">
                            <a class="tw-mt-8 tw-mb-2 tw-text-ellipsis tw-truncate tw-w-full text-left !tw-max-w-xs"
                                target="_blank" data-toggle="tooltip"
                                data-title="<?= _l('perfex_saas_customer_link'); ?>"
                                href="<?= perfex_saas_tenant_base_url($company); ?>">
                                <?= perfex_saas_tenant_base_url($company); ?> <i class="fa fa-external-link"></i>
                            </a>
                            <a class="tw-mb-8 tw-text-ellipsis tw-truncate tw-w-full text-left !tw-max-w-xs"
                                target="_blank" data-toggle="tooltip" data-title="<?= _l('perfex_saas_admin_link'); ?>"
                                href="<?= perfex_saas_tenant_admin_url($company); ?>">
                                <?= perfex_saas_tenant_admin_url($company); ?>
                                <i class="fa fa-external-link"></i>
                            </a>
                        </div>
                    </div>

                    <div class="company-status">
                        <span
                            class="badge badge-success <?= $company->status == 'active' ? 'bg-success' : 'bg-danger'; ?>">
                            <?= perfex_saas_company_status_label($company); ?>
                            <?= $deploying ? '<i class="fa fa-spin fa-spinner"></i>' : ''; ?>
                        </span>
                    </div>
                </div>


                <!-- edit form -->
                <?php include('includes/edit_form.php'); ?>
                <!-- end edit form -->

                <!-- custom domain edit form -->
                <?php include('includes/custom_domain.php'); ?>
                <!-- custom domain edit form -->

            </div>
            <div class="panel_footer tw-flex tw-justify-between tw-items-center tw-mt-4 tw-mb-3">
                <div data-toggle="tooltip" data-title="<?= _l('perfex_saas_date_created'); ?>">
                    <i class="fa fa-calendar"></i>
                    <?= explode(' ', $company->created_at)[0]; ?>
                </div>
                <div class="tw-flex tw-space-x-2">

                    <?php if ($company->status == 'active' && $can_preview) : ?>
                    <button data-toggle="tooltip" data-title="<?= _l('perfex_saas_view'); ?>"
                        class="btn btn-primary tw-rounded-full view-company" data-slug="<?= $company->slug; ?>">
                        <i class="fa fa-eye"></i>
                    </button>
                    <?php endif; ?>

                </div>
            </div>
        </div>
        <?php endforeach ?>



        <!-- create company form -->
        <?php include('includes/create_form.php'); ?>
        <!-- end create company form -->
    </div>
</div>



<!-- Companies viewer modal -->
<div class="modal view-company-modal animated fadeIn" id="view-company-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog tw-w-full tw-h-screen tw-mt-0" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"
                        class="tw-text-danger-600 bold"><i class="fa fa-close"></i></span></button>
                <div class="tw-flex tw-justify-end">
                    <div class="tw-flex col-md-2 col-xs-6">
                        <?= render_select('view-company', $company_options, ['key', ['name']], '', '0', [], [], 'tw-w-full', '', true); ?>
                    </div>
                    <h4 class="modal-title"></h4>
                </div>
            </div>
            <div class="modal-body tw-m-0">
                <div class="tw-h-full tw-w-full tw-flex tw-items-center tw-justify-center first-loader">
                    <i class="fa fa-spin fa-spinner fa-4x"></i>
                </div>
                <iframe class="tw-w-full tw-h-full" id="company-viewer">
                </iframe>
            </div>
        </div>
    </div>
</div>