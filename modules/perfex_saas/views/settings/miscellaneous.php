<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();
?>

<div class="tw-flex tw-flex-col">
    <!-- redirection after url -->
    <?php $key = 'perfex_saas_after_first_instance_redirect_url'; ?>
    <?= render_input('settings[' . $key . ']', perfex_saas_input_label_with_hint($key), get_option($key)); ?>

    <?php $key = 'perfex_saas_trial_expire_page_url'; ?>
    <?= render_input('settings[' . $key . ']', perfex_saas_input_label_with_hint($key), get_option($key)); ?>

    <?php
    $key = 'perfex_saas_enable_deploy_splash_screen';
    render_yes_no_option($key, _l($key));
    ?>

    <?php
    $key = 'perfex_saas_deploy_splash_screen_theme';
    $value = get_option($key);
    $splash_themes = [
        ['key' => 'verbose', 'label' => _l('perfex_saas_deploy_splash_screen_theme_verbose')],
        ['key' => 'simple', 'label' => _l('perfex_saas_deploy_splash_screen_theme_simple')],
    ]; ?>
    <?= render_select('settings[' . $key . ']', $splash_themes, ['key', ['label']], _l($key) . perfex_saas_form_label_hint($key . '_hint'), $value, [], [], '', '', false); ?>

    <div class="tw-mt-4 tw-mb-4">
        <hr />
    </div>

    <!-- contact selection -->

    <?php
    $key = 'perfex_saas_client_restriction_mode';
    $value = get_option($key);
    $restriction_modes = [
        ['key' => '', 'label' => _l('perfex_saas_client_restriction_mode_disabled')],
        ['key' => 'exclusive', 'label' => _l('perfex_saas_client_restriction_mode_exclusive')],
        ['key' => 'inclusive', 'label' => _l('perfex_saas_client_restriction_mode_inclusive')],
    ]; ?>
    <?= render_select('settings[' . $key . ']', $restriction_modes, ['key', ['label']], _l($key) . perfex_saas_form_label_hint($key . '_hint'), $value, [], [], '', '', false); ?>

    <?php
    $key = 'perfex_saas_restricted_clients_id';
    $value = get_option($key);
    ?>

    <!-- Set this input to ensure the $key value can be cleared i.e emptied -->
    <?php
    $selected = $value;
    if (is_string($selected)) $selected = json_decode($selected);
    $CI->load->view('perfex_saas/includes/client_select', ['name' => 'settings[' . $key . '][]', 'label' => _l($key), 'value' => $selected]);
    ?>

    <div class="tw-mt-4 tw-mb-4">
        <hr />
    </div>


    <?php
    $key = 'perfex_saas_trial_notification_day';
    $value = get_option($key);
    echo render_input('settings[' . $key . ']', perfex_saas_input_label_with_hint($key), empty($value) ? '0' : $value, 'number');
    ?>

    <div class="tw-mt-4 tw-mb-4">
        <hr />
    </div>

    <!-- Custom tenant seed source -->
    <div class="row">
        <div class="col-md-8 form-group">
            <label for="w-full">
                <?php
                $key = 'perfex_saas_demo_instance';
                $reset_key = $key . '_reset_hour';
                $reset = get_option($reset_key);
                ?>
                <?= perfex_saas_input_label_with_hint($key, $key . '_hint'); ?>
            </label>
            <?php
            $demo_tenant_slugs = get_option($key);
            $demo_tenant_slugs = empty($demo_tenant_slugs) ? [] : json_decode($demo_tenant_slugs);
            ?>
            <?php $CI->load->view(PERFEX_SAAS_MODULE_NAME . '/includes/tenant_select', ['name' => 'settings[' . $key . '][]', 'value' => $demo_tenant_slugs, 'multiple' => 'multiple', 'id' => $key]); ?>
        </div>
        <div class="col-md-4 form-group">
            <label class="invisible"><?= _l($reset_key); ?></label>
            <div class="input-group" data-toggle="tooltip" data-title="<?= _l($reset_key); ?>">
                <input min="0" step="0.5" type="number" name="settings[<?= $reset_key; ?>]"
                    value="<?= empty($reset) ? '24' : $reset; ?>" class="form-control"
                    placeholder="<?= _l($reset_key); ?>" />
                <span class="input-group-addon tw-px-2 tw-border-l-0">
                    hours
                </span>
            </div>
        </div>
    </div>

    <div class="tw-mt-4 tw-mb-4">
        <hr />
    </div>
    <?php
    $key = 'perfex_saas_disable_cron_job_setup_tab';
    render_yes_no_option($key, _l($key));
    ?>
    <div class="tw-mt-4 tw-mb-4">
        <hr />
    </div>
    <?php $key = 'perfex_saas_custom_domain_guide'; ?>
    <?= render_textarea('settings[' . $key . ']', $key, get_option($key), [], [], '', 'tinymce'); ?>
    <p><?= _l('perfex_saas_custom_domain_guide_tags', '{ip_address}, {subdomain}'); ?></p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preload some customers
    setTimeout(() => {
        $(".ajax-search input[type=search]").val($(".filter-option-inner-inner").text()).trigger(
                'keyup')
            .val('');
    }, 1000);

    $("[name='settings[perfex_saas_client_restriction_mode]']").on('change', function() {
        var input = $("select[name='settings[perfex_saas_restricted_clients_id][]']");
        if ($(this).val() != '') {
            input.closest('.form-group').show();
        } else {
            input.closest('.form-group').hide();
        }
    }).trigger('change');
})
</script>