<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
$tenant = $companies[0];
$tenant->package_invoice = $invoice;
$company = $tenant;
$deploying = perfex_saas_company_status_can_deploy($company);
?>

<div class="single-theme">
    <!-- company management -->
    <div class="ps-view tw-mt-8" id="companies">
        <?php if (!$deploying && !empty($invoice)) : ?>
        <!-- quota system --->
        <div class="tw-flex tw-flex-col tw-mb-4 tw-w-full">
            <?php require($view_path . '/includes/quota_stats.php'); ?>
        </div>
        <?php endif ?>

        <!-- tenant and subscription details -->
        <div class="row tw-flex tw-justify-center tw-flex-wrap  company <?= $company->status; ?> <?= $autolaunch; ?>">
            <div class="col-xs-12 col-md-7 company-info">
                <div class="tw-relative">
                    <!-- edit form -->
                    <?php include('includes/edit_form.php'); ?>
                    <!-- end edit form -->

                    <!-- custom domain edit form -->
                    <?php include('includes/custom_domain.php'); ?>
                    <!-- custom domain edit form -->

                    <div class="info">

                        <div class="menu">
                            <?php if ($company->status == 'active') : ?>

                            <?= !empty($company->metadata->pending_custom_domain) ? $pending_custom_domain_notice : ''; ?>
                            <?php if ($can_preview && !$deploying) : ?>
                            <button data-toggle="tooltip" data-title="<?= _l('perfex_saas_quick_view'); ?>"
                                class="btn btn-primary tw-rounded-full view-company" data-slug="<?= $company->slug; ?>">
                                <i class="fa fa-eye"></i>
                            </button>
                            <?php endif; ?>
                            <?php endif; ?>

                            <!--- Menu -->
                            <?php include('includes/dropdown_menu.php'); ?>

                            <!--- End drop down menu -->
                        </div>

                        <?php require($view_path . '/companies/view.php'); ?>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-5 text-center">
                <?php
                if ($invoice) {
                    $list_active_only = true;
                    require($view_path . '/packages/list.php');
                }
                ?>
                <?php if ($invoice && $invoice->status != '5' && !perfex_saas_is_single_package_mode()) : ?>
                <a class="btn btn-primary"
                    href="<?= site_url('clients/?subscription'); ?>"><?= _l('perfex_saas_change_plan'); ?></a>
                <?php elseif (!$invoice) : ?>
                <div class="tw-h-full text-center tw-flex tw-items-center">
                    <div class="">
                        <span class="text-danger">
                            <?= _l('perfex_saas_no_invoice_client_for_client_mid'); ?>
                        </span>
                        <a class="btn btn-primary tw-mt-8"
                            href="<?= perfex_saas_default_base_url('clients/?subscription'); ?>">
                            <?= _l('perfex_saas_manage_subscription'); ?>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>


    <!-- Companies viewer modal -->
    <?php if (!$deploying && $can_preview) : ?>
    <div class="iframe-wrapper tw-m-0 tw-bg-white" id="view-company-modal" style="display: none;">
        <div class="iframe-toolbar">
            <div class="tw-flex" id="company-switch">
                <button type="button" class="btn btn-danger close-btn" data-toggle="tooltip"
                    data-title="<?= _l('perfex_saas_back_to_client_portal'); ?>" aria-label="Close"
                    data-placement="right">
                    <span aria-hidden="true" class="text-white bold" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-chevron-down"></i>
                    </span>
                </button>
            </div>
        </div>
        <iframe class="tw-w-full tw-h-full" id="company-viewer"
            src="<?= base_url('clients/ps_magic/' . $tenant->slug); ?>">
        </iframe>
    </div>
    <?php endif ?>
</div>

<link rel="stylesheet" type="text/css"
    href="<?= module_dir_url(PERFEX_SAAS_MODULE_NAME, 'assets/css/single-theme.css'); ?>" />

<!-- style control for client menu visibility -->
<?php if ((int)get_option('perfex_saas_control_client_menu')) : ?>
<style>
.section-client-dashboard>*:not(.single-theme, #greeting) {
    display: none !important;
}
</style>
<?php endif; ?>