<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- create company form -->
<div class="panel_s tw-p-4 tw-py-2 tw-bg-neutral-100 tw-w-full tw-flex tw-flex-col tw-justify-center">
    <div class="panel_body">

        <!-- Trigger -->
        <div class="tw-flex tw-flex-col tw-items-center tw-justify-center text-center" id="add-company-trigger">
            <div class="tw-mt-8 tw-flex">
                <button type="button"
                    class="tw-bg-white tw-py-4 tw-px-4 tw-rounded-full tw-border-primary-600 add-company-btn"><i
                        class="fas fa-plus fa-2x tw-px-4 tw-py-4"></i></button>
            </div>
            <h3 class="tw-mt-8">
                <?= empty($companies) ?  _l('perfex_saas_let_us_create_your_first_company') : _l('perfex_saas_spin_up_another_awesome_crm'); ?>
            </h3>
            <button type="button" class="btn btn-primary tw-mb-8 add-company-btn">
                <?= _l('perfex_saas_new_company'); ?>
            </button>
        </div>

        <!-- actual form -->
        <?php echo form_open_multipart('clients/companies/create', ['id' => "add-company-form", 'style' => 'display:none;']); ?>
        <div class="tw-flex tw-flex-col tw-items-center tw-justify-center tw-mt-4 tw-mb-4">
            <!-- company name -->
            <?= render_input('name', 'perfex_saas_company_name', empty($companies) ? get_client(get_client_user_id())->company : '', 'text', [], [], "text-left tw-mb-4 $centered_size", $input_class); ?>
            <!-- slug -->
            <?= $can_use_subdomain ? render_input('slug', _l('perfex_saas_create_company_slug') . perfex_saas_form_label_hint('perfex_saas_create_company_slug_hint', perfex_saas_get_saas_default_host()), $next_slug, 'text', ['maxlength' => PERFEX_SAAS_MAX_SLUG_LENGTH], [], "text-left tw-mb-4 $centered_size", $input_class) : ''; ?>
            <!-- custom domain -->
            <?= $can_use_custom_domain ? render_input('custom_domain', _l('perfex_saas_custom_domain') . perfex_saas_form_label_hint('perfex_saas_custom_domain_hint'), '', 'text', [], [], "text-left tw-mb-4 $centered_size", $input_class) : ''; ?>

            <div class="tw-flex tw-justify-end tw-mt-2">
                <button type="button" class="btn btn-secondary tw-mr-4"
                    id="cancel-add-company"><?= _l('perfex_saas_cancel'); ?></button>
                <button class="btn btn-primary" id="submit-company"><?= _l('perfex_saas_create'); ?></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<!-- end create company form -->