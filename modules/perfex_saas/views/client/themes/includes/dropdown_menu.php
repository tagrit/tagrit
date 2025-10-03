<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!--- Menu -->
<div class="dropdown tw-pr-2">
    <a href="#" class="dropdown-toggle tw-pr-2" data-toggle="dropdown" role="button" aria-haspopup="true"
        aria-expanded="false">
        <i class="fa fa-ellipsis-v fa-2x" data-toggle="tooltip" data-title="<?= _l('perfex_saas_more_option'); ?>"
            data-placement="bottom" data-original-title="" title=""></i>
    </a>
    <ul class="dropdown-menu animated fadeIn">
        <?php if ($can_use_custom_domain) : ?>
        <li class="edit-custom-domain-nav">
            <a href="#custom-domain" onclick="return false;"><?= _l('perfex_saas_client_add_custom_domain'); ?></a>
        </li>
        <?php endif; ?>
        <li class="edit-company-nav">
            <a href="#company_edit_form" onclick="return false;"><?= _l('perfex_saas_client_edit_company'); ?></a>
        </li>
        <li class="customers-nav-item-logout">
            <?php if ($company->status != PERFEX_SAAS_STATUS_PENDING && $company->status !== PERFEX_SAAS_STATUS_PENDING_DELETE) : ?>
            <?= form_open(base_url('clients/companies/delete/' . $company->slug)); ?>
            <?= form_hidden('id', $company->id); ?>
            <input type="submit"
                class="text-danger text-left tw-ml-2 tw-pt-2 _delete tw-w-full tw-bg-transparent tw-border-0"
                value="<?= _l('perfex_saas_delete'); ?>">
            <?= form_close(); ?>
            <?php endif; ?>
        </li>
    </ul>
</div>
<!--- End drop down menu -->