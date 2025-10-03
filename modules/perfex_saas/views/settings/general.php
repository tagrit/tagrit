<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();
$CI->load->model('emails_model');
$CI->load->model('invoices_model');

$reserved_slugs = get_option('perfex_saas_reserved_slugs');

$label2 = perfex_saas_input_label_with_hint('perfex_saas_reserved_slugs');
$email_templates = $CI->emails_model->get(["`slug` LIKE" => "company-instance%", 'language' => 'english'], 'result');

$label3 = perfex_saas_input_label_with_hint('perfex_saas_route_id');
$label4 = perfex_saas_input_label_with_hint('perfex_saas_alternative_base_host');

?>

<div class="tw-flex tw-flex-col">

    <!-- dont add 'settings' array, will be added by the function 'render_yes_no_option' -->
    <?php render_yes_no_option('perfex_saas_enable_single_package_mode', _l('perfex_saas_enable_single_package_mode'), _l('perfex_saas_enable_single_package_mode_hint')); ?>
    <?php render_yes_no_option('perfex_saas_enable_package_grouping', _l('perfex_saas_enable_package_grouping'), _l('perfex_saas_enable_package_grouping_hint')); ?>
    <?php render_yes_no_option('perfex_saas_enable_auto_trial', _l('perfex_saas_enable_auto_trial'), _l('perfex_saas_enable_auto_trial_hint')); ?>
    <?php render_yes_no_option('perfex_saas_autocreate_first_company', _l('perfex_saas_autocreate_first_company'), _l('perfex_saas_autocreate_first_company_hint')); ?>
    <?php render_yes_no_option('perfex_saas_allow_customer_cancel_subscription', _l('perfex_saas_allow_customer_cancel_subscription'), _l('perfex_saas_allow_customer_cancel_subscription_hint')); ?>

    <!-- Tenant wont be able to access instances when invoice status is any of the choose below -->
    <?php
    $key = 'perfex_saas_require_invoice_payment_status';
    $value = get_option($key);
    $selected = empty($value) ? [Invoices_model::STATUS_OVERDUE] : (array)json_decode($value);

    echo perfex_saas_render_select(
        'settings[' . $key . '][]',
        [
            ['key' => Invoices_model::STATUS_OVERDUE, 'label' => _l('invoice_status_overdue')],
            ['key' => Invoices_model::STATUS_UNPAID, 'label' => _l('invoice_status_unpaid')],
            ['key' => Invoices_model::STATUS_PARTIALLY, 'label' => _l('invoice_status_not_paid_completely')],
        ],
        ['key', ['label']],
        perfex_saas_input_label_with_hint($key),
        $selected,
        ['multiple' => true, 'allow_blank' => true],
        []
    );
    ?>

    <!-- Deferred billing update to package (customaization) -->
    <?php
    $key = 'perfex_saas_deferred_billing_status';
    $value = get_option($key);
    echo render_select(
        'settings[' . $key . ']',
        [
            ['key' => Invoices_model::STATUS_DRAFT, 'label' => _l('invoice_status_draft')],
            ['key' => Invoices_model::STATUS_UNPAID, 'label' => _l('invoice_status_unpaid')],
        ],
        ['key', ['label']],
        perfex_saas_input_label_with_hint($key),
        $value
    );
    ?>

    <div class="tw-mt-4 tw-mb-4">
        <hr />
    </div>
    <?php render_yes_no_option('perfex_saas_control_client_menu', _l('perfex_saas_control_client_menu'), _l('perfex_saas_control_client_menu_hint')); ?>
    <?php
    $key = 'perfex_saas_autolaunch_instance';
    $value = get_option($key);
    echo render_select(
        'settings[' . $key . ']',
        [
            ['key' => 'yes', 'label' => _l('perfex_saas_yes')],
            ['key' => 'no', 'label' => _l('perfex_saas_no')],
            ['key' => 'new', 'label' => _l('perfex_saas_new_instance_only')],
        ],
        ['key', ['label']],
        $key,
        empty($value) ? 'new' : $value,
        [],
        [],
        '',
        '',
        false
    );
    ?>
    <div class="tw-mt-4 tw-mb-4">
        <hr />
    </div>

    <?php echo render_input('settings[perfex_saas_alternative_base_host]', $label4, get_option('perfex_saas_alternative_base_host')); ?>
    <?php echo render_input('settings[perfex_saas_route_id]', $label3, get_option('perfex_saas_route_id')); ?>
    <?php echo render_input('settings[perfex_saas_reserved_slugs]', $label2, empty($reserved_slugs) ? 'www,app,deal,controller,master,ww3,hack' : $reserved_slugs); ?>

    <div class="tw-mt-4 tw-mb-4">
        <hr />
    </div>
    <?php
    $masked_settings_pages_key = 'perfex_saas_masked_settings_pages';
    $masked_settings_pages = get_option($masked_settings_pages_key);
    echo render_input('settings[' . $masked_settings_pages_key . ']', perfex_saas_input_label_with_hint($masked_settings_pages_key), empty($masked_settings_pages) ? '' : $masked_settings_pages, 'text', ['placeholder' => '/admin/settings, /config']);
    ?>
    <div class="tw-mt-4 tw-mb-4">
        <hr />
    </div>

    <?php render_yes_no_option('perfex_saas_enable_subdomain_input_on_signup_form', _l('perfex_saas_enable_subdomain_input_on_signup_form')); ?>
    <?php render_yes_no_option('perfex_saas_enable_customdomain_input_on_signup_form', _l('perfex_saas_enable_customdomain_input_on_signup_form')); ?>

    <div class="tw-mt-4 tw-mb-4">
        <hr />
    </div>
    <div class="form-group">
        <h4><?= _l('perfex_saas_landing_page_settings'); ?>:</h4>
        <br />
        <div class="proxy row">
            <div class="col-sm-8">
                <?php $value = get_option('perfex_saas_landing_page_url'); ?>
                <?= render_input('settings[perfex_saas_landing_page_url]', _l('perfex_saas_landing_page_url') . perfex_saas_form_label_hint('perfex_saas_landing_page_url_hint'), $value, 'text', ['placeholder' => 'https://mycrm.com/home']); ?>
            </div>
            <div class="col-sm-4">
                <?php $value = get_option('perfex_saas_landing_page_url_mode'); ?>
                <?= render_select('settings[perfex_saas_landing_page_url_mode]', [['key' => 'proxy'], ['key' => 'redirection']], ['key', ['key']], _l('perfex_saas_landing_page_url_mode') . perfex_saas_form_label_hint('perfex_saas_landing_page_url_mode_hint'), empty($value) ? 'proxy' : $value); ?>
            </div>
        </div>
    </div>
    <div class="tw-mt-4 tw-mb-4">
        <hr />
    </div>
    <?php render_yes_no_option('perfex_saas_force_redirect_to_dashboard', _l('perfex_saas_force_redirect_to_dashboard'), _l('perfex_saas_force_redirect_to_dashboard_hint')); ?>
    <div class="tw-mt-4 tw-mb-4">
        <hr />
    </div>

    <!-- client nav and options -->
    <?php
    $yes_no_keys = [
        'perfex_saas_enable_client_bridge',
        'perfex_saas_enable_cross_domain_bridge',
        'perfex_saas_enable_instance_switch',
        'perfex_saas_enable_client_menu_in_bridge',
        'perfex_saas_enable_client_menu_in_interim_pages',
    ];
    foreach ($yes_no_keys as $key) {
        render_yes_no_option($key, _l($key), _l($key . '_hint'));
    }
    ?>
    <?php
    $key = 'perfex_saas_client_bridge_account_menu_position';
    echo render_select(
        'settings[' . $key . ']',
        [
            ['key' => 'setup', 'label' => _l('perfex_saas_setup_menu')],
            ['key' => 'sidebar', 'label' => _l('perfex_saas_sidebar_menu')],
        ],
        ['key', ['label']],
        $key,
        get_option($key),
        [],
        [],
        '',
        '',
        false
    );
    ?>

    <?php $key = 'perfex_saas_clients_default_theme_whitelabel_name'; ?>
    <?= render_input('settings[' . $key . ']', perfex_saas_input_label_with_hint($key), get_option($key)); ?>

    <div class="tw-mt-4 tw-mb-4">
        <hr />
    </div>
    <div class="">
        <label><?= _l('perfex_saas_email_templates'); ?></label>
        <ul class="tw-mt-4">
            <?php foreach ($email_templates as $t) : ?>
            <li>
                <a href="<?= admin_url('emails/email_template/' . $t->emailtemplateid); ?>" target="_blank">
                    <i class="fa fa-pen"></i><!-- <i class="fa fa-external-link"></i>--> <?= $t->name ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="tw-mt-4 tw-mb-4">
        <hr />
    </div>
    <?php $key = 'perfex_saas_instance_delete_pending_days'; ?>
    <?= render_input('settings[' . $key . ']', perfex_saas_input_label_with_hint($key), get_option($key), 'number', ['step' => 0.1]); ?>

</div>