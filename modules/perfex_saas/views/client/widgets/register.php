<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * This widget add subdomain and custom domain input field into Perfex CRM registeration form
 * (i.e /authentiaction/register form) when default package allows it. 
 */
if ($CI->router->fetch_class() !== 'authentication' || $CI->router->fetch_method() !== 'register' || is_client_logged_in()) return;

$package = [];

// Check if we have selected plan in session
$package_slug = $CI->session->{perfex_saas_route_id_prefix('plan')} ?? '';
if (!empty($package_slug)) {
    $CI->db->where('slug', $package_slug);
    $package = $CI->perfex_saas_model->packages()[0] ?? [];
} else {
    // Use the default package
    $CI->db->where('is_default', 1);
    $package = $CI->perfex_saas_model->packages()[0] ?? [];
}

if (empty($package)) return;

$package = (object)$package;
$can_use_subdomain = (int)$package->metadata->enable_subdomain && (int)perfex_saas_get_options('perfex_saas_enable_subdomain_input_on_signup_form');
$can_use_customdomain = (int)$package->metadata->enable_custom_domain && (int)perfex_saas_get_options('perfex_saas_enable_customdomain_input_on_signup_form');
?>

<?php if ($can_use_subdomain || $can_use_customdomain) : ?>

<!-- containter to hold the input fields -->
<div class="form-group mtop15 register-saas-info-group" style="display: none;">
    <!-- slug -->
    <?= $can_use_subdomain ? render_input('slug', _l('perfex_saas_register_form_subdomain_id') . perfex_saas_form_label_hint('perfex_saas_create_company_slug_hint', perfex_saas_get_saas_default_host()), '', 'text', ['maxlength' => PERFEX_SAAS_MAX_SLUG_LENGTH], [], "text-left tw-mb-4 ", '') : ''; ?>

    <!-- custom domain -->
    <?= $can_use_customdomain ? render_input('custom_domain', _l('perfex_saas_register_form_custom_domain') . perfex_saas_form_label_hint('perfex_saas_custom_domain_hint'), '', 'text', [], [], "text-left tw-mb-4", '') : ''; ?>

    <!-- package slug -->
    <input type="hidden" value="<?= $package->slug; ?>" name="<?= perfex_saas_route_id_prefix('plan');?>" />
</div>


<!-- Widget javascript -->
<script>
/**
 * This function modify the register form to include subdomain and custom domain input fields
 *
 * @return void
 */
function bindDomainInputToRegisterationForm() {
    $(".register-country-group, .register-zip-group").addClass('col-md-6 tw-pl-0');
    $(".register-city-group, .register-state-group").addClass('col-md-6 tw-pl-0 tw-pr-0');
    $(".register-saas-info-group").insertAfter($(".register-company-group"));
    $(".register-saas-info-group").show();
    bindAndListenToSlugInput(".register-saas-info-group", "input[name=company]");
}

// Bind
setTimeout(bindDomainInputToRegisterationForm, 200);

// Backup call incase content not in DOM during the time out call
window.addEventListener("DOMContentLoaded", () => {
    if (!$("form .register-saas-info-group").length) bindDomainInputToRegisterationForm()
});
</script>

<?php endif; ?>