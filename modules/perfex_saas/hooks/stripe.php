<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('create_subscription_invoice_data')) {
    /**
     * Extend the core create_subscription_invoice_data function and add price description to each invoice item
     *
     * @param mixed $subscription
     * @param mixed $invoice
     * @return mixed
     */
    function create_subscription_invoice_data($subscription, $invoice)
    {
        if (perfex_saas_is_tenant()) return _create_subscription_invoice_data($subscription, $invoice);

        $CI = &get_instance();
        if ($invoice instanceof stdClass && defined('CLIENTS_AREA') && !empty($subscription->stripe_subscription_id))
            $invoice = $CI->stripe_subscriptions->get_upcoming_invoice($subscription->stripe_subscription_id);

        $subscription_local_package_id = null;
        $cache_id = $subscription->id;

        $items = $invoice->lines->data;
        foreach ($items as $key => $item) {
            if (($item->price->metadata->group ?? '') == 'pcrm_saas') { // We only want to add to SaaS subscription invoice only
                $desc = is_array($item) ? $item['price']['metadata']['description'] : $item->price->metadata->description;
                $invoice->lines->data[$key]['description'] = $desc . "<br/>" . $invoice->lines->data[$key]['description'];

                if (!$subscription_local_package_id)
                    $subscription_local_package_id = $item->price->metadata->package_id ?? '';
            }
        }

        if ($subscription_local_package_id) {
            hooks()->add_filter('subscription_invoice_data', function ($new_invoice_data) use ($subscription_local_package_id, $cache_id) {
                if ($cache_id == $new_invoice_data['subscription_id']) {
                    $new_invoice_data[perfex_saas_column('packageid')] = $subscription_local_package_id;
                }
                return $new_invoice_data;
            });
        }

        return _create_subscription_invoice_data($subscription, $invoice);
    }
}

// Only return here after declaring the function to ensure the overriden function is also available for the tenants usign stripe
if ($is_tenant) return;

$CI = &get_instance();

hooks()->add_action('admin_init', function () {
    if (staff_can('view', 'perfex_saas_packages')) {
        $CI = &get_instance();
        $CI->app_menu->add_sidebar_children_item(PERFEX_SAAS_MODULE_WHITELABEL_NAME, [
            'slug' => PERFEX_SAAS_MODULE_WHITELABEL_NAME . '_stripe_pricing',
            'name' => _l('perfex_saas_stripe_pricing'),
            'icon' => 'fa fa-list',
            'href' => admin_url(PERFEX_SAAS_ROUTE_NAME . '/stripe_pricing'),
            'position' => 6,
        ]);
    }
});


/**
 * Shadowed libraries
 */
$CI->load->library(PERFEX_SAAS_MODULE_NAME . '/' . PERFEX_SAAS_MODULE_NAME . '_custom_stripe_subscriptions');
$CI->stripe_subscriptions = $CI->perfex_saas_custom_stripe_subscriptions;


/**
 * Make neccessary patch to Perfex for the stripe integration
 *
 * @param bool $forward
 * @return void
 */
function perfex_saas_stripe_setup_patch($forward)
{
    $find = 'function create_subscription_invoice_data';
    $replace = 'function _create_subscription_invoice_data';
    $file = APPPATH . 'helpers/subscriptions_helper.php';
    if ($forward)
        replace_in_file($file, $find, $replace);
    else replace_in_file($file, $replace, $find);
}

function perfex_saas_stripe_package_recurring_is_over_three_years($package)
{
    $interval = 'month';
    $interval_count = $package->metadata->invoice->recurring;

    if ($interval_count == 'custom') {
        $interval = $package->metadata->invoice->repeat_type_custom;
        $interval_count = $package->metadata->invoice->repeat_every_custom;
    }

    // Define how many months each interval corresponds to
    $months_per_interval = [
        'day' => 1 / 30, // Approximate month value for a day
        'week' => 1 / 4.33, // Approximate month value for a week
        'month' => 1,
        'year' => 12,
    ];

    // Calculate total months based on interval
    $total_months = isset($months_per_interval[$interval]) ? $interval_count * $months_per_interval[$interval] : 0;

    // Return true if interval count exceeds 3 years (36 months), otherwise false
    return $total_months > 36;
}

hooks()->add_action('perfex_saas_after_installer_run', function () {
    perfex_saas_stripe_setup_patch(true);
});

hooks()->add_action('perfex_saas_after_uninstaller_run', function ($clean) {
    perfex_saas_stripe_setup_patch(false);
});

if (strpos(uri_string(), 'admin/subscriptions/edit') !== false && !empty($_POST)) {
    $subscription_id = explode('/', uri_string());
    $subscription_id = end($subscription_id);
    if (!empty($metadata = perfex_saas_search_client_metadata('subscription_id', (int)$subscription_id))) {
        // alway enforce 1 as quantity for SaaS subscription items to prevent it from being updated in stripe model
        $_POST['quantity'] = 1;
    }
}

// Attempt to sync to stripe when a package is updated.
hooks()->add_action('perfex_saas_after_package_update', function ($package) {
    $CI = &get_instance();
    if (($package->metadata->stripe->enabled ?? '') == '1')
        $CI->perfex_saas_stripe_model->setup_package_on_stripe($package);
});

// Attempt to remove stripe settings from cloned package
hooks()->add_filter('perfex_saas_package_clone_filter', function ($data) {
    $metadata = json_decode($data['entity_data']['metadata'], true);
    if ($data['entity'] == 'packages' && isset($metadata['stripe'])) {
        unset($metadata['stripe']);
        $data['entity_data']['metadata'] = json_encode($metadata);
    }
    return $data;
});