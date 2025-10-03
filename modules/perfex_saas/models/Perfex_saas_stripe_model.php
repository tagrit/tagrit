<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Perfex_saas_stripe_model extends App_Model
{
    public function get_success_cancel_callback_url($subscription)
    {
        $cancel_url = site_url('subscription/' . $subscription->hash);
        $success_url = current_url() . '?session_sub_hash=' . $subscription->hash . "&session_id={CHECKOUT_SESSION_ID}";
        return [$success_url, $cancel_url];
    }

    /**
     * Generate a client invoice.
     *
     * @param mixed $clientid The client ID.
     * @param mixed $packageid The package ID.
     * @param Perfex_saas_custom_limit $custom_limitations Extra invoice custom data
     * @return mixed The generated company invoice.
     * @throws \Exception When certain conditions are not met.
     */
    public function generate_company_invoice($clientid, $packageid, $custom_limitations)
    {
        $this->load->model('subscriptions_model');
        $this->load->library('stripe_subscriptions');

        $package = $this->perfex_saas_model->packages($packageid);
        $package_stripe_settings = $this->get_package_stripe_settings($package);

        $default_stripe_price_id = $this->get_resources_price_id_from_settings($package_stripe_settings, 'default');
        if (empty($default_stripe_price_id)) {
            $package_stripe_settings = $this->setup_package_on_stripe($package);
        }

        // Check for accessibility
        if (($package_stripe_settings->enabled ?? '') != '1' || empty($default_stripe_price_id)) {
            throw new \Exception(_l('perfex_saas_package_can_not_use_strip_model'), 1);
        }

        $old_invoice = $this->perfex_saas_model->get_company_invoice($clientid);

        $package_column = perfex_saas_column('packageid');
        $subscription = null;

        if ($old_invoice) {
            $subscription = $this->subscriptions_model->get_by_id($old_invoice->subscription_id);
            $has_active_sub = $subscription && $subscription->status == 'active';

            if ($has_active_sub) {

                if ($old_invoice->{$package_column} == $packageid) {

                    return $old_invoice;
                } else {

                    // Cancel current subscription immediately
                    $this->cancel_subscription($subscription->id);
                    $subscription = null;
                }
            }

            if ($subscription && in_array($subscription->status, ['canceled', 'incomplete_expired'])) {
                $subscription = null;
            }
        }

        $session_sub_hash = $this->input->get('session_sub_hash');
        if (!empty($session_sub_hash)) {
            $subscription = $this->subscriptions_model->get_by_hash($session_sub_hash);
        }

        // Create new subscription and save the client subscription meta
        $data = array_merge($this->package_stripe_settings_to_subscription_info($package), ['clientid' => $clientid]);
        if (!$subscription) {

            // Find handing one
            $this->subscriptions_model->db->where('stripe_plan_id', $data['stripe_plan_id']);
            $this->subscriptions_model->db->where('currency', $data['currency']);
            $subscription = $this->subscriptions_model->db->get(db_prefix() . 'subscriptions')->row();

            if ($subscription && (empty($subscription->status) || $subscription->status == 'incomplete')) {
                $insert_id = $subscription->id;
            } else {
                $insert_id = $this->subscriptions_model->create($data);
                if (!$insert_id)
                    throw new \Exception(((object)$this->db->error())->message, 1);
            }

            $subscription = $this->subscriptions_model->get_by_id($insert_id);
        }

        if (!$subscription)
            throw new \Exception("Error creating subscription blueprint", 1);

        // Get the stripe customer
        $stripe_customer = $this->get_stripe_customer($clientid);

        // Prepare item for subscription
        $items = [
            [
                'price' => $default_stripe_price_id,
                'quantity' => 1,
                'metadata' => ['is_default' => true]
            ]
        ];

        // Get existing resources customization
        $custom_limit_items = $this->get_custom_limit_items($package, $custom_limitations);
        $items = array_merge($items, array_values($custom_limit_items));
        $total = $package->price + $custom_limitations->total;
        if ($total > 0) {

            // Get the customer payment method if not yet provided through stripe session
            if (empty($session_sub_hash)) {
                list($success_url, $cancel_url) = $this->get_success_cancel_callback_url($subscription);
                $session = $this->get_stripe_checkout_session(
                    $stripe_customer,
                    $subscription->name,
                    $success_url,
                    $cancel_url
                );

                if (isset($session->url))
                    return (object)['action_url' => $session->url, 'stripe_session_id' => $session->id];
            }

            // Check if there is active finalizing session from stripe
            if (!empty($stripe_session_id = $this->input->get('session_id'))) {

                // Link payment to the customer
                $this->finalize_stripe_checkout_session($stripe_customer, $stripe_session_id);
            }
        }


        // Create subscription on stripe
        try {

            $proration_behavior = $package_stripe_settings->proration_behavior ?? 'none';
            $taxes  = $package_stripe_settings->taxes ?? [];

            $stripeSubscription = $this->create_stripe_subscription($subscription, $items, $stripe_customer, $proration_behavior, $taxes);

            $this->subscriptions_model->update($subscription->id, [
                'status'                 => $stripeSubscription->status,
                'stripe_subscription_id' => $stripeSubscription->id,
            ]);

            perfex_saas_get_or_save_client_metadata($clientid, [
                'subscription_id' => $subscription->id,
                'subscription_package_id' => $packageid,
            ]);

            if ($stripeSubscription->status === 'incomplete') {
                if ($stripeSubscription->latest_invoice->payment_intent->status === 'requires_action') {
                    return (object)['action_url' => $stripeSubscription->latest_invoice->hosted_invoice_url];
                }
            }

            return $this->perfex_saas_model->get_company_invoice($clientid);
        } catch (\Throwable $th) {
            $this->subscriptions_model->delete($subscription->id);
            throw $th;
        }
    }

    /**
     * Update a client invoice or subscription
     *
     * @param object $invoice
     * @param int $clientid
     * @param Perfex_saas_custom_limit $custom_limitations
     * @return true
     * @throws Exception
     */
    public function update_company_invoice($invoice, $clientid, $custom_limitations)
    {
        $this->load->model('subscriptions_model');
        $this->load->library('stripe_subscriptions');

        $session_sub_hash = $this->input->get('session_sub_hash');
        if (!empty($session_sub_hash)) {
            $subscription = $this->subscriptions_model->get_by_hash($session_sub_hash);
        }
        if (!isset($subscription) || empty($subscription))
            $subscription = $this->subscriptions_model->get_by_id($invoice->subscription_id);

        $stripeSubscriptionId = $subscription->stripe_subscription_id;

        $package = $this->perfex_saas_model->packages($invoice->{perfex_saas_column('packageid')});
        $package_stripe_settings = $this->get_package_stripe_settings($package);

        // Prepare the extra limit customization items to update
        $new_items = $this->get_custom_limit_items($package, $custom_limitations);

        $update = array_merge($this->package_stripe_settings_to_subscription_info($package), ['clientid' => $clientid]);
        if (!empty($stripeSubscriptionId)) {
            unset($update['clientid']);
            unset($update['date']);
        }

        /** Handle payment method */
        // Get the stripe customer
        $stripe_customer = $this->get_stripe_customer($clientid);

        $total = $package->price + $custom_limitations->total;
        if ($total > 0) {

            // Get the customer payment method if not yet provided through stripe session
            if (empty($session_sub_hash)) {
                list($success_url, $cancel_url) = $this->get_success_cancel_callback_url($subscription);
                $session = $this->get_stripe_checkout_session(
                    $stripe_customer,
                    $subscription->name,
                    $success_url,
                    $cancel_url
                );

                if (isset($session->url))
                    return (object)['action_url' => $session->url, 'stripe_session_id' => $session->id];
            }

            // Check if there is active finalizing session from stripe
            if (!empty($stripe_session_id = $this->input->get('session_id'))) {

                // Link payment to the customer
                $this->finalize_stripe_checkout_session($stripe_customer, $stripe_session_id);
            }
        }
        /** End payment method */

        // Update remote subscription items
        $update['custom_limit_items'] = $new_items;
        $proration_behavior = $package_stripe_settings->proration_behavior ?? 'none';
        $taxes = $package_stripe_settings->taxes ?? [];
        $stripeSubscription = $this->update_stripe_subscription($stripeSubscriptionId, $update, $proration_behavior, $taxes);
        if (!$stripeSubscription)
            throw new \Exception("Error updating stripe subscription", 1);

        unset($update['custom_limit_items']);
        $this->subscriptions_model->update($subscription->id, $update);

        return true;
    }

    public function cancel_subscription($subscription_id, $type = 'immediately')
    {
        $this->load->model('subscriptions_model');
        $this->load->library('stripe_subscriptions');

        $ends_at = time();

        $subscription = $this->subscriptions_model->get_by_id($subscription_id);
        if (!empty($subscription->stripe_subscription_id)) {

            if ($type == 'immediately') {
                $this->stripe_subscriptions->cancel($subscription->stripe_subscription_id);
            } elseif ($type == 'at_period_end') {
                $ends_at = $this->stripe_subscriptions->cancel_at_end_of_billing_period($subscription->stripe_subscription_id);
            } else {
                throw new Exception('Invalid Cancelation Type', 1);
            }
        }

        $update = ['ends_at' => $ends_at];
        if ($type == 'immediately') {
            $update['status'] = 'canceled';
        }

        $this->subscriptions_model->update($subscription_id, $update);
        return $this->perfex_saas_model->get_company_invoice($subscription->clientid, ['include_cancelled' => true]);
    }

    public function resume_subscription($subscription_id)
    {
        $this->load->model('subscriptions_model');
        $this->load->library('stripe_subscriptions');

        $subscription = $this->subscriptions_model->get_by_id($subscription_id);
        if (empty($subscription->stripe_subscription_id))
            throw new \Exception("Cancel: subscription not found", 1);

        if (!empty($subscription->ends_at) && $subscription->status == 'canceled') return false;

        \Stripe\Subscription::update(
            $subscription->stripe_subscription_id,
            [
                'cancel_at_period_end' => false,
            ]
        );

        $this->subscriptions_model->update($subscription_id, ['ends_at' => null]);
        return $this->perfex_saas_model->get_company_invoice($subscription->clientid);
    }

    /**
     * Get array of subscription customization item to be added to a subscription object
     *
     * @param object $package
     * @param Perfex_saas_custom_limit $custom_limitations
     * @return array
     * @throws Exception
     */
    protected function get_custom_limit_items($package, $custom_limitations, $force_sync_package = false)
    {
        $discounts = $package->metadata->formatted_discounts ?? [];
        $package_stripe_settings = $this->get_package_stripe_settings($package);

        if (empty($custom_limitations->customization_items))  return [];

        $default_stripe_price_id = $this->get_resources_price_id_from_settings($package_stripe_settings, 'default');
        if ($force_sync_package || empty($default_stripe_price_id))
            $package_stripe_settings = $this->setup_package_on_stripe($package);

        // Prepare the items to update
        $new_items = [];

        $has_update_package_on_stripe = false;

        foreach ($custom_limitations->customization_items as $key => $limit) {

            $quantity = (int)$limit['quantity'];
            $resources = $limit['resources'];
            if ($quantity <= 0) {
                continue;
            }

            $resources_stripe_price_id = $this->get_resources_price_id_from_settings($package_stripe_settings, $resources);
            if (empty($resources_stripe_price_id) && !$has_update_package_on_stripe) {
                $package_stripe_settings = $this->setup_package_on_stripe($package);
                $has_update_package_on_stripe = true;
                $resources_stripe_price_id = $this->get_resources_price_id_from_settings($package_stripe_settings, $resources);
            }


            $discount = $discounts->{$resources} ?? [];
            if (!empty($discount)) {
                arsort($discount);
                foreach ($discount as $level => $value) {
                    $level = (int)$level;
                    if ($quantity >= $level) {
                        $resources_stripe_price_id_alt = $this->get_resources_price_id_from_settings($package_stripe_settings, $resources . '_' . $level);
                        $resources_stripe_price_id = !empty($resources_stripe_price_id_alt) ? $resources_stripe_price_id_alt : $resources_stripe_price_id;
                        break;
                    }
                }
            }

            if (!$force_sync_package && empty($resources_stripe_price_id)) {
                return $this->get_custom_limit_items($package, $custom_limitations, true);
            }

            if (empty($resources_stripe_price_id)) {
                throw new \Exception(_l('perfex_saas_stripe_missing_price_id', _l('perfex_saas_limit_' . $resources)), 1);
            }

            $new_items[$resources] = [
                "quantity" => $quantity,
                "price" => $resources_stripe_price_id
            ];
        }

        return $new_items;
    }

    protected function get_package_stripe_settings($package)
    {
        $package = is_object($package) ? $package : $this->perfex_saas_model->packages((int)$package);
        $settings = $package->metadata->stripe ?? new stdClass;

        if (empty($settings->currency))
            $settings->currency = get_base_currency()->name;

        if (is_numeric($settings->currency))
            $settings->currency = get_currency($settings->currency)->name;

        $settings->currency = strtolower($settings->currency);
        return $settings;
    }

    protected function save_package_stripe_settings($package_id, array $data)
    {
        $package = $this->perfex_saas_model->packages($package_id);
        $metadata = (array)$package->metadata;
        $metadata['stripe'] = array_merge((array)($metadata['stripe'] ?? []), $data);
        $data = ['id' => $package->id, 'metadata' => json_encode($metadata)];
        $this->perfex_saas_model->add_or_update('packages', $data);
        return (object)$metadata['stripe'];
    }

    /**
     * Will sync a local package to stripe
     *
     * @param object $package
     * @return object $package_stripe_settings Modified package stripe settings. 
     */
    public function setup_package_on_stripe($package)
    {
        $this->load->model('subscriptions_model');
        $this->load->library('stripe_subscriptions');

        $package_stripe_settings = $this->get_package_stripe_settings($package);
        if (!isset($package_stripe_settings->pricing))
            $package_stripe_settings->pricing = new stdClass;

        if (($package_stripe_settings->sync ?? '1') == '0' || ($package_stripe_settings->enabled ?? '') != '1') return $package_stripe_settings;

        // Create product items
        $product_data = [
            'name' => $package->name,
            'metadata' => ['group' => 'pcrm_saas', 'local_id' => $package->id]
        ];
        $product = $this->create_or_get_stripe_product($product_data, $package_stripe_settings->product_id ?? null);

        $package_stripe_settings = $this->save_package_stripe_settings($package->id, ['product_id' => $product->id]);
        $package->metadata->stripe = $package_stripe_settings;

        // Prepare the items to update or create
        $prices = $this->prepare_package_stripe_prices($package, $product);
        foreach ($prices as $price_data) {

            $stripe_price = $this->create_or_get_stripe_price($price_data);
            $package_stripe_settings->pricing->{$price_data['metadata']['resources']} = $stripe_price->id;
        }

        $package_stripe_settings = $this->save_package_stripe_settings($package->id, ['pricing' => $package_stripe_settings->pricing]);

        // Setup the taxes on stripe
        $taxes = array_unique($package->metadata->invoice->taxname ?? []);
        if (!empty($taxes)) {

            // Fetch all stripe taxes and map
            $stripe_taxes = $this->stripe_subscriptions->get_tax_rates();
            $stripe_taxes_formatted = [];
            foreach ($stripe_taxes->data as $tax) {
                if ($tax->active)
                    $stripe_taxes_formatted[$tax->metadata->local_id] = $tax;
            }

            $new_taxes = [];
            foreach ($taxes as $_tax) {
                $stripe_tax = $stripe_taxes_formatted[$_tax] ?? null;
                if (empty($stripe_tax)) {
                    $tax = explode('|', $_tax);
                    $tax_amount = end($tax);
                    $tax_name = rtrim(str_replace($tax_amount, '', $_tax), '|');
                    $tax_amount = (float)$tax_amount;
                    $stripe_tax =  $this->create_stripe_tax(['name' => $tax_name, 'amount' => $tax_amount, 'metadata' => ['group' => 'pcrm_saas', 'local_id' => $_tax]]);
                }

                if (!empty($stripe_tax->id)) {
                    $new_taxes[] = $stripe_tax->id;
                }
            }

            $package_stripe_settings = $this->save_package_stripe_settings($package->id, ['taxes' => $new_taxes]);
        }

        return $package_stripe_settings;
    }

    /**
     * Generate structure of stripe prices for a package given package
     * Will generate price data for default price and extra limitations (modules, services and discounts).
     *
     * @param object $package
     * @param object $stripe_product
     * @return array
     */
    protected function prepare_package_stripe_prices($package, $stripe_product)
    {
        $package_stripe_settings = $this->get_package_stripe_settings($package);
        if (!isset($package_stripe_settings->pricing))
            $package_stripe_settings->pricing = new stdClass;

        $currency = $package_stripe_settings->currency;

        $interval = 'month';
        $interval_count = $package->metadata->invoice->recurring;
        if ($interval_count == 'custom') {
            $interval = $package->metadata->invoice->repeat_type_custom;
            $interval_count = $package->metadata->invoice->repeat_every_custom;
        }

        $prices = [];
        // Add default price
        $unit_amount = $this->convert_amount_to_lowest_unit($package->price, $currency);
        $prices[] =
            [
                'id' => $this->get_resources_price_id_from_settings($package_stripe_settings, 'default'),
                'nickname' => $package->name,
                'unit_amount' => $unit_amount,
                'currency' => $currency,
                'product' => $stripe_product->id,
                'recurring' => [
                    'interval' => $interval, //Either day, week, month or year.
                    'interval_count' => $interval_count,
                ],
                'lookup_key' => 'pcrm_saas_package_' . $package->id . '_' . $unit_amount . '_' . $interval . '_' . $interval_count,
                'metadata' => [
                    "description" => _l('perfex_saas_invoice_desc_subscription', $package->name),
                    "package_id" => $package->id,
                    "resources" => 'default',
                    "group" => "pcrm_saas"
                ]
            ];


        $limitations = $package->metadata->limitations;
        $limitations_price = $package->metadata->limitations_unit_price;
        $limitations_description = new stdClass;
        $limitations_name = new stdClass;

        // Add storage
        $limitations->storage = $package->metadata->storage_limit->size ?? -1;
        $limitations_price->storage = $package->metadata->storage_limit->unit_price ?? 0;

        // Add instance limit
        $limitations->tenant_instance = $package->metadata->max_instance_limit ?? 1;

        // Add modules
        $modules = $this->perfex_saas_model->modules();
        foreach ($modules as $module) {
            $billing_mode = $module['billing_mode'] ?? '';
            if ($billing_mode == 'lifetime') continue;
            $key = $module['system_name'];
            $name = $module['custom_name'] ?? $key;
            $name = _l($name, '', false);

            $price = $package->metadata->limitations_unit_price->{$key} ?? '';
            if ($price === '')
                $price = $module['price'] ?? 0;

            if (empty($price)) continue;

            $limitations->{$key} = 0;
            $limitations_price->{$key} = $price;
            $limitations_name->{$key} = $name;
            $limitations_description->{$key} = _l('perfex_saas_invoice_addon_module_item_desc', $name);
        }

        // Add services
        $services = $this->perfex_saas_model->services();
        foreach ($services as $service_id => $service) {
            if (empty($service_id)) continue;
            $name = $service['name'];
            $billing_mode = $service['billing_mode'] ?? '';
            if ($billing_mode == 'lifetime') continue;
            $price = $package->metadata->limitations_unit_price->{$service_id} ?? $service['price'] ?? 0;
            if (empty($price)) continue;
            $limitations->{$service_id} = 0;
            $limitations_price->{$service_id} = $price;
            $limitations_name->{$service_id} = $name;
            $limitations_description->{$service_id} = _l('perfex_saas_invoice_addon_service_item_desc', $name);
        }

        // Add discounts as standlone price
        $discounts = (object)($package->metadata->formatted_discounts ?? []);
        foreach ($discounts as $resources => $levels) {
            $unit_price = (float)$limitations_price->{$resources};
            foreach ($levels as $level) {
                $_percent = $level['percent'];
                $percent = ((float)$_percent) / 100;
                $discount_amount =  ($unit_price * $percent);
                $price = $unit_price - $discount_amount;
                $key = $resources . '_' . $level['unit'];
                $limitations->{$key} = 1;
                $limitations_price->{$key} = $price;
                $limitations_name->{$key} = $limitations_name->{$resources} ?? $resources . ' ' . $level['unit'] . '+';
                $limitations_description->{$key} = _l('perfex_saas_invoice_addon_item_desc', _l('perfex_saas_limit_' . $resources)) . " ($_percent% off)";
            }
        }

        // Construct the price object for stripe
        foreach ($limitations as $resources => $limit) {
            if ($limit == -1) continue;
            $value_unit_price = $limitations_price->{$resources} ?? 0;
            if ($limit == 0 && $value_unit_price == 0) continue;

            // Skip original limiations with price as zero
            if (isset($package->metadata->limitations->{$resources}) && $value_unit_price == 0)
                continue;

            $res_name = _l('perfex_saas_limit_' . $resources, '', false);
            $res_name = str_starts_with($res_name, 'perfex_saas') ? ($limitations_name->{$resources} ?? $resources) : $res_name;

            $unit_amount = $this->convert_amount_to_lowest_unit($value_unit_price, $currency);
            $lookup_key = 'pcrm_saas_package_' . $package->id . '_' . $resources . '_' . $unit_amount;
            $prices[] = [
                'id' => $this->get_resources_price_id_from_settings($package_stripe_settings, $resources),
                'nickname' => $package->name . ' ' . $res_name,
                'unit_amount' => $unit_amount,
                'currency' => $currency,
                'product' => $stripe_product->id,
                'lookup_key' =>  implode('_', [$lookup_key, $interval, $interval_count]),
                'recurring' => [
                    'interval' => $interval, //Either day, week, month or year.
                    'interval_count' => $interval_count,
                ],
                'metadata' => [
                    "description" => $limitations_description->{$resources} ?? _l('perfex_saas_invoice_addon_item_desc', $res_name),
                    "package_id" => $package->id,
                    "resources" => $resources,
                    "group" => "pcrm_saas"
                ]
            ];
        }

        return $prices;
    }

    protected function get_resources_price_id_from_settings($package_stripe_settings, $resources)
    {
        if ($package_stripe_settings->sync == '1') return $package_stripe_settings->pricing->{$resources} ?? '';

        return $package_stripe_settings->manual_pricing->{$resources} ??
            ($package_stripe_settings->pricing->{$resources} ?? '');
    }

    protected function package_stripe_settings_to_subscription_info($package)
    {

        $package_stripe_settings = $this->get_package_stripe_settings($package);
        $currency = $package_stripe_settings->currency;
        $currency_id = get_currency($currency)->id;

        return [
            'description_in_item' => 0,
            'date' => null,
            'project_id' => 0,
            'quantity' => 1,
            'name' => _l($package->name, '', false),
            'description' => nl2br(_l('perfex_saas_invoice_desc_subscription', $package->name)),
            'stripe_plan_id' => $this->get_resources_price_id_from_settings($package_stripe_settings, 'default'),
            'terms' => nl2br($package_stripe_settings->terms ?? ''),
            'stripe_tax_id' => $package_stripe_settings->taxes[0] ?? false,
            'stripe_tax_id_2' => $package_stripe_settings->taxes[1] ?? false,
            'currency' => $currency_id
        ];
    }

    protected function get_stripe_customer($clientid)
    {
        $client = $this->clients_model->get($clientid);
        $contact = perfex_saas_get_primary_contact($clientid);

        if (empty($contact->email)) throw new Exception("Customer requires a contact with an email address", 1);

        $customerPayload = [
            'email' => $contact->email,
            'name' => implode(' ', [$contact->firstname ?? '', $contact->lastname ?? '']),
            'description' => $client->company,
            'address' => [
                'line1' => $client->billing_street,
                'postal_code' => $client->billing_zip,
                'city' => $client->billing_zip,
                'state' => $client->billing_state,
                'country' => get_country($client->billing_country)->iso2 ?? null,
            ],
        ];

        if ($client->stripe_id) {
            $customer = $this->stripe_subscriptions->get_customer($client->stripe_id);
        } else if (!empty($customerPayload['email'])) {

            $customers = \Stripe\Customer::all(['email' => $customerPayload['email']]);
            if (!empty($customers->data)) $customer = $customers->data[0];
        }

        if (empty($customer->id)) {
            $customer = $this->stripe_subscriptions->create_customer($customerPayload);
        }

        if ($customer->id) {
            $this->clients_model->update(['stripe_id' => $customer->id], $clientid);
        }

        return $customer;
    }

    /**
     * Generate checkout session to get the card details for the customer
     *
     * @param \Stripe\Customer $stripe_customer
     * @param string $description
     * @param string $success_url
     * @param string $cancel_url
     * @return \Stripe\Checkout\Session
     */
    public function get_stripe_checkout_session($stripe_customer, $description, $success_url = '', $cancel_url = '')
    {
        // Check if the stripe customer actually have default payment method
        // Perhaps the stripe_id is saved via regular invoice payments where
        // the payment method is not stored
        if (!empty($stripe_customer->invoice_settings->default_payment_method)) {
            return true;
        }

        $session_data = [
            'payment_method_types' => ['card'],
            'mode'                 => 'setup',
            'success_url'          => $success_url,
            'cancel_url'           => $cancel_url,
            'setup_intent_data'    => [
                'description' => $description,
            ],
        ];

        $session_data['client_reference_id'] = $stripe_customer->id;
        $session_data['customer_email'] = $stripe_customer->email;

        $session = $this->stripe_subscriptions->create_session($session_data);
        return $session;
    }

    /**
     * Will link the payment intent from session to the customer
     *
     * @param \Stripe\Customer $stripe_customer
     * @param string $stripe_session_id
     * @return \Stripe\Customer
     */
    public function finalize_stripe_checkout_session($stripe_customer, $stripe_session_id)
    {
        $session = $this->stripe_subscriptions->retrieve_session([
            'id'     => $stripe_session_id,
            'expand' => ['setup_intent.payment_method'],
        ]);

        $payment_method = $session->setup_intent->payment_method;

        $payment_method->attach(['customer' => $stripe_customer->id]);

        return $this->stripe_subscriptions->update_customer($stripe_customer->id, [
            'invoice_settings' => [
                'default_payment_method' => $payment_method->id,
            ],
        ]);
    }

    protected function create_stripe_subscription($subscription, $items, $stripe_customer, $proration_behavior, $taxes = [])
    {
        $params = [];
        $params['default_tax_rates'] = $taxes;

        $params['metadata'] = [
            'pcrm-subscription-hash' => $subscription->hash,
            // Indicated the the customer was on session,
            // see requires action event
            'customer-on-session' => true,
        ];
        $params['items'] = $items;
        $params['expand'] = ['latest_invoice.payment_intent'];
        $params['proration_behavior'] = $proration_behavior;
        $params['off_session'] = true;

        $stripeSubscription = $this->stripe_subscriptions->subscribe($stripe_customer->id, $params);
        return $stripeSubscription;
    }

    protected function update_stripe_subscription($subscription_id, $update_values, $proration_behavior, $taxes = [])
    {
        if (empty($subscription_id)) {
            return false;
        }

        $stripeSubscription = $this->stripe_subscriptions->get_subscription($subscription_id);

        $has_default = false;
        $default_price_placeholder = [
            'price' => $update_values['stripe_plan_id'],
            'quantity' => 1,
            'metadata' => ['is_default' => true]
        ];

        $items = [];
        $deletedItems = [];
        $custom_limit_items = $update_values['custom_limit_items'] ?? [];

        // Update existing items
        $old_item_update_list = [];

        foreach ($stripeSubscription->items->data as $item) {
            $resources = $item->price->metadata->resources;

            if (!$resources) {
                if (
                    isset($item->metadata->is_default) &&
                    ($item->metadata->is_default == true || $item->metadata->is_default == "true")
                ) {
                    $resources = 'default';
                }
            }

            if (!$resources) {
                continue;
            }

            if ($resources == 'default') {
                $default_price_placeholder['id'] = $item->id;
                unset($default_price_placeholder['price']);
                $items[] = $default_price_placeholder;
                $has_default = true;
                continue;
            }

            if (isset($custom_limit_items[$resources])) {
                $old_item_update_list[] = $resources;
                $items[] = [
                    'id' => $item->id,
                    'quantity' => $custom_limit_items[$resources]['quantity'],
                ];
            } else {
                $deletedItems[] = [
                    'id' => $item->id,
                    'deleted' => true
                ];
            }
        }

        // Add new extra limit items
        foreach ($custom_limit_items as $resources => $_new_item) {

            if (in_array($resources, $old_item_update_list))
                continue;

            $items[] = [
                'price' => $_new_item['price'],
                'quantity' => $_new_item['quantity'],
            ];
        }

        // Remove the item to be deleted from the subscription
        foreach ($deletedItems as $item) {
            try {
                $subscriptionItem = \Stripe\SubscriptionItem::retrieve($item['id']);
                $subscriptionItem->delete();
            } catch (\Exception $e) {
                // Handle the error (e.g., log it, notify someone, etc.)
                log_message('error', 'Error deleting stripe subscription item: ' . $e->getMessage());
            }
        }

        // Ensure the default plan is always included.
        if (!$has_default) {
            $items = array_merge([$default_price_placeholder], $items);
        }

        $update_data = [
            'items' => $items,
            'proration_behavior' => $proration_behavior,
            'default_tax_rates' => $taxes
        ];

        return \Stripe\Subscription::update($stripeSubscription->id, $update_data);
    }

    protected function create_or_get_stripe_price($price_data)
    {
        // Search for the price using lookup key
        $prices = \Stripe\Price::all([
            'active' => true,
            'lookup_keys' => [$price_data['lookup_key']],
            'product' => $price_data['product'],
        ]);

        $stripe_price = null;
        foreach ($prices->data as $key => $value) {
            if ($value->lookup_key == $price_data['lookup_key'] && $value->metadata->resources == $price_data['metadata']['resources']) {
                $stripe_price = $value;
                break;
            }
        }

        $price_id = $price_data['id'] ?? '';
        unset($price_data['id']);

        $price_data['active'] = true;

        try {

            if (
                $stripe_price && isset($stripe_price->metadata->resources)
            ) {
                $matched_amount_and_currency = $price_data['unit_amount'] == $stripe_price->unit_amount &&
                    $stripe_price->currency == $price_data['currency'];

                $matched_interval = $price_data['recurring']['interval'] == $stripe_price->recurring->interval &&
                    $price_data['recurring']['interval_count'] == $stripe_price->recurring->interval_count;

                if (
                    $matched_amount_and_currency && $matched_interval
                ) {

                    $_price_data = ['active' => true];

                    if (isset($price_data['metadata']))
                        $_price_data['metadata'] = $price_data['metadata'];

                    if (isset($price_data['nickname']))
                        $_price_data['nickname'] = $price_data['nickname'];

                    return \Stripe\Price::update($stripe_price->id, $_price_data);
                } else {
                    // Change in price, recreate a new and archive old one
                    \Stripe\Price::update($stripe_price->id, ['active' => false]);
                }

                // Disable the previous plan from the local cache also
                if (!empty($price_id) && $price_id != $stripe_price->id) {
                    \Stripe\Price::update($price_id, ['active' => false]);
                }
            }
            $stripe_price = null;
        } catch (\Throwable $th) {
            $stripe_price = null;
            $price_id = '';
            throw $th;
        }

        if ($stripe_price && !empty($stripe_price->id)) return $stripe_price;

        return \Stripe\Price::create($price_data);
    }

    protected function get_all_stripe_active_products()
    {
        $hasMore       = true;
        $data          = null;
        $startingAfter = null;
        do {
            $products = \Stripe\Product::all(
                array_merge(['limit' => 100, 'active' => true], $startingAfter ? ['starting_after' => $startingAfter] : [])
            );

            if (is_null($data)) {
                $data = $products;
            } else {
                $data->data = array_merge($data->data, $products->data);
            }

            $startingAfter    = $data->data[count($data->data) - 1]->id ?? null;
            $hasMore          = $products['has_more'];
            $data['has_more'] = $hasMore;
        } while ($hasMore);

        return $data;
    }

    protected function create_or_get_stripe_product($product_data, $id = null)
    {
        // Search for the product
        $products = $this->get_all_stripe_active_products();
        foreach ($products->data as $key => $value) {
            if (
                ($id && $value->id == $id) ||
                ($value->metadata->group == $product_data['metadata']['group']
                    && $value->metadata->local_id == $product_data['metadata']['local_id'])
            ) {
                $product = $value;
                break;
            }
        }

        if (isset($product) && !empty($product->id) && $product->active) {
            $product_data['active'] = true;
            return \Stripe\Product::update(
                $product->id,
                $product_data
            );
        }

        return \Stripe\Product::create($product_data);
    }

    protected function create_stripe_tax($data)
    {
        return \Stripe\TaxRate::create([
            'display_name' => $data['name'],
            'percentage' => (float)$data['amount'],
            'inclusive' => false,
            'active' => true,
            'metadata' => $data['metadata'] ?? []
        ]);
    }


    protected function convert_amount_to_lowest_unit($amount, $currency)
    {

        $amount = (float)$amount;
        $zeroDecimalCurrencies = [
            'BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA', 'PYG',
            'RWF', 'UGX', 'VND', 'VUV', 'XAF', 'XOF', 'XPF'
        ];

        // If the currency does not have a minor unit, return the amount as is
        if (in_array(strtoupper($currency), $zeroDecimalCurrencies)) {
            return intval($amount);
        }

        // Otherwise, convert to the smallest currency unit
        return intval($amount * 100);
    }
}