<?php
defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();
$packages = isset($packages) ? $packages : $CI->perfex_saas_model->packages();
$invoice_package_id = $invoice->{perfex_saas_column('packageid')} ?? '';

if (is_client_logged_in()) {
    // Filter by assigned customers
    $packages = $CI->perfex_saas_model->packages_filter_by_assigned_client($packages, get_client_user_id(), $invoice_package_id);
}

$currency = get_base_currency();
$is_client = is_client_logged_in();
$showing_subscribed_card = isset($list_active_only) && $list_active_only;
$single_pricing_mode = perfex_saas_is_single_package_mode();
$modules = $CI->perfex_saas_model->modules();

// Prevent user from trial when have a cancelled subscription
$_can_trial = $is_client ? perfex_saas_client_can_trial_package(get_client_user_id()) : true;

$enable_grouping = $showing_subscribed_card || $single_pricing_mode ? false : get_option('perfex_saas_enable_package_grouping') != '0';

// Group and sort the data by interval alias
$grouped_packages = $enable_grouping ? perfex_saas_group_pricing_by_interval_alias($packages) : ['0' => $packages];

$package_groups = array_keys($grouped_packages);

$filter_groups = array_values(array_filter($package_groups, function ($group) {
    return !str_starts_with($group, 'private_');
}));

$total_groups = count($filter_groups);

if ($enable_grouping && $total_groups < 2)
    $enable_grouping = false;

// Ensure unique instalce of this component
$filter_wrapper_class = 'list_' . rand(10000, 999999);
?>

<div class="<?= $filter_wrapper_class; ?>" style="<?= $enable_grouping ? 'display:none;' : ''; ?>">

    <?php if ($enable_grouping) require(__DIR__ . '/pricing-filter.php'); ?>

    <div
        class="<?= $showing_subscribed_card || $single_pricing_mode ? '' : 'tw-grid tw-gap-3 tw-grid-cols-1 sm:tw-grid-cols-2 md:tw-grid-cols-3'; ?>">
        <?php foreach ($grouped_packages as $package_group => $_packages) : ?>

        <?php
            foreach ($_packages as $package) :

                $subscribed = !empty($invoice_package_id) && $invoice_package_id == $package->id;
                if ($subscribed)
                    echo "<template data-package-default-group='$package_group'></template>";

                if ($showing_subscribed_card && !$subscribed) continue;
                if ($is_client && $package->is_private && !$subscribed) continue;

                if ($is_client && $single_pricing_mode) {
                    if ($package->is_default != '1') continue;
                    else $package->name = '';
                }

                require(__DIR__ . '/pricing-card-item.php');

            endforeach;
            ?>
        <?php endforeach ?>
    </div>

</div>