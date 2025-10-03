<?php

defined('BASEPATH') or exit('No direct script access allowed');
trait Perfex_saas_subscription_trait
{
    /**
     * Generate a client invoice.
     *
     * @param mixed $clientid The client ID.
     * @param mixed $packageid The package ID.
     * @param bool $extra_custom_data Extra invoice custom data
     * @return mixed The generated company invoice or array containing action url i.e action_url
     * @throws \Exception When certain conditions are not met.
     */
    public function generate_company_invoice($clientid, $packageid, $extra_custom_data = [])
    {
        $package = $this->packages($packageid);

        $metadata = $package->metadata;
        $old_invoice = $this->get_company_invoice($clientid);

        $date = date('Y-m-d');
        $duedate =  $date;
        $trial_period = (int)$package->trial_period;

        $client_metadata = (object)perfex_saas_get_or_save_client_metadata($clientid);
        $on_trial = isset($old_invoice->on_trial) && $old_invoice->on_trial;
        $can_trial = empty($old_invoice->id) && $trial_period > 0 && empty($client_metadata->trial_package_id) && empty($client_metadata->last_cancelled_invoice);

        $_old_invoice = $old_invoice ? $old_invoice : $this->get_company_cancelled_invoice($clientid, false);
        $old_customization = $this->prepare_invoice_customization_items($package, (array)$client_metadata, $_old_invoice ? $_old_invoice : null);

        // Set trial if can trial and return invoice
        if ($can_trial) {
            perfex_saas_get_or_save_client_metadata(
                $clientid,
                [
                    'trial_period_start' => $date,
                    'trial_period_ends' => date('Y-m-d', strtotime("+$trial_period days")),
                    'trial_package_id' => $packageid,
                    'trial_cancelled' => '',
                    'last_cancelled_invoice' => '',
                    'subscription_id' => '',
                ]
            );
            return $this->get_company_invoice($clientid);
        }


        // Check compatibility with new invoice
        if ($old_invoice) {

            // Validation: Ensure number of instances fit.
            $max_instance_limit = perfex_saas_get_tenant_instance_limit($package);

            // Count the user instances.
            $this->db->where('clientid', $clientid);
            $companies = $this->companies();
            $total_instances = $companies ? count($companies) : 0;

            if ($total_instances == 0)
                $old_customization = $old_customization->clear();

            if ($max_instance_limit > 0 && $total_instances > 0) {

                if ($total_instances > $max_instance_limit)
                    throw new \Exception(_l('perfex_saas_plan_upgrade_unfit_number_of_instances'), 1);

                // Confirm each company quota matches with new one.
                $package_quota = $package->metadata->limitations ?? [];
                $limited_resources = [];
                // Let filter out the resources that are not unlimited to save time complexity in next loop
                foreach ($package_quota as $res => $quota) {
                    if ((int)$quota >= 0) $limited_resources[] = $res;
                }

                foreach ($companies as $company) {
                    // Current usage limit
                    $usage_limits = perfex_saas_get_tenant_quota_usage($company, $limited_resources, $old_invoice);

                    // Simulate with new invoice to be and ensure tenant can upgrade to the package.
                    $company->package_invoice = $package; // Assign the new package to make resources_quota estimation

                    // Add custom limits purchase if any to have the real new quota
                    $company->package_invoice->custom_limits = $old_invoice->custom_limits ?? new stdClass();

                    foreach ($usage_limits as $resources => $usage) {
                        $quota = perfex_saas_tenant_resources_quota($company, $resources);

                        if ($quota !== -1 && $usage > $quota) {
                            throw new \Exception(_l('perfex_saas_plan_upgrade_unfit_quota', [$company->name, $resources]), 1);
                        }
                    }
                }
            }

            // Ensure package changing
            if ($old_invoice->{perfex_saas_column('packageid')} == $packageid) {

                if ($on_trial) {
                    // User convinced and subscribing to package early before end of trial
                    // Cancel trial
                    perfex_saas_get_or_save_client_metadata(
                        $clientid,
                        ['trial_cancelled' => 'true']
                    );
                    $on_trial = false;
                } else {

                    throw new \Exception(_l("perfex_saas_no_change_detected"), 1);
                }
            }


            // Now we confirm package upgrade is compatible, let handle trial prorate
            if ($on_trial) {
                // Get numbers of days the client has tried for
                $used_trial_days = (time() - strtotime($old_invoice->date)) / (60 * 60 * 24);
                $used_trial_days = floor($used_trial_days); // Rounds down to the nearest whole number of days

                $left_trial_period = $trial_period - $used_trial_days;
                if ($left_trial_period > 0) {
                    // Update trial package id and prorate end period
                    perfex_saas_get_or_save_client_metadata(
                        $clientid,
                        [
                            'trial_period_ends' => date('Y-m-d', strtotime("+$left_trial_period days")),
                            'trial_package_id' => $packageid,
                            'last_cancelled_invoice' => '',
                            'subscription_id' => '',
                        ]
                    );

                    return $this->get_company_invoice($clientid);
                } else {
                    // Cancel the trial
                    perfex_saas_get_or_save_client_metadata(
                        $clientid,
                        [
                            'trial_package_id' => $packageid,
                            'trial_cancelled' => 'true',
                            'last_cancelled_invoice' => '',
                            'subscription_id' => '',
                        ]
                    );
                }
            }
        }

        // Change of plan. Cancel existing invoices and its children
        if ($old_invoice) {
            $this->cancel_company_invoice($clientid, 'immediately', $old_invoice);
        }

        // Trial management over, if stripe enabled for the package return subscription model
        if (($metadata->stripe->enabled ?? '') == '1') {
            return $this->perfex_saas_stripe_model->generate_company_invoice($clientid, $packageid, $old_customization);
        }

        $next_invoice_number = get_option('next_invoice_number');
        $invoice_number      = str_pad($next_invoice_number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);

        // Payments options
        $payment_modes = $metadata->invoice->allowed_payment_modes ?? [];
        if (empty($payment_modes)) {
            $all_payment_modes = $this->payment_modes_model->get();
            foreach ($all_payment_modes as $pmode) {
                $payment_modes[] = $pmode['id'];
            }
        }

        $taxes = $metadata->invoice->taxname ?? [];
        $client = $this->clients_model->get($clientid);

        $new_items = [
            [
                "order" => "1",
                "description" => _l('perfex_saas_invoice_desc_subscription', $package->name),
                "long_description" => "",
                "qty" => "1",
                "unit" => "",
                "rate" => $package->price,
                "taxname" => $taxes
            ]
        ];

        $subtotal = $package->price;

        // Add old customization items
        if ($old_customization && !empty($old_customization->invoice_items)) {
            $new_items = array_merge($new_items, $old_customization->invoice_items);
            $subtotal = $subtotal + (float)$old_customization->total;
        }

        $data = [
            "clientid" => $clientid,
            "number" => $invoice_number,
            "date" => $date,
            "duedate" => $duedate,
            "tags" => PERFEX_SAAS_FILTER_TAG,
            "allowed_payment_modes" => $payment_modes,
            "currency" => get_base_currency()->id,
            "sale_agent" => $metadata->invoice->sale_agent ?? "",
            "recurring" => $metadata->invoice->recurring ?? "1",
            "repeat_every_custom" => $metadata->invoice->repeat_every_custom ?? "",
            "repeat_type_custom" => $metadata->invoice->repeat_type_custom ?? "",
            "show_quantity_as" => "1",
            "newitems" => $new_items,
            "subtotal" => $subtotal,
            "discount_percent" => "0",
            "discount_total" => "0.00",
            "adjustment" => "0",
            "total" => $subtotal,
            "billing_street"   => $client->billing_street ?? '',
            "billing_city"     => $client->billing_city ?? '',
            "billing_state"    => $client->billing_state ?? '',
            "billing_zip"      => $client->billing_zip ?? '',
            "billing_country"  => $client->billing_country ?? '',
            "shipping_street"  => $client->shipping_street ?? '',
            "shipping_city"    => $client->shipping_city ?? '',
            "shipping_state"   => $client->shipping_state ?? '',
            "shipping_zip"     => $client->shipping_zip ?? '',
            "shipping_country" => $client->shipping_country ?? '',
        ];

        $data = array_merge($data, $extra_custom_data);

        // Set taxes
        if (!empty($taxes)) {
            $total_tax = 0;
            foreach ($taxes as $key => $tax) {
                $tax = explode('|', $tax);
                $tax_amount = (float)end($tax);
                $total_tax += (($tax_amount / 100) * $data["subtotal"]);
            }
            $data["total"] = (float)$data["subtotal"] + $total_tax;
        }

        // mark as paid if zero invoice
        if ($data["total"] == 0) {
            $data["status"] = Invoices_model::STATUS_PAID;
        }

        // Important
        $data[perfex_saas_column('packageid')] = $packageid;

        if (!$this->invoices_model->add($data)) {
            throw new \Exception(((object)$this->db->error())->message, 1);
        }

        // clear last cancelled_invoice
        perfex_saas_get_or_save_client_metadata($clientid, ['last_cancelled_invoice' => '', 'subscription_id' => '']);

        $invoice = $this->get_company_invoice($clientid);
        if (!$invoice) throw new \Exception(_l('perfex_saas_client_invoice_lookup_error'), 1);

        $new_status = update_invoice_status($invoice->id, false);
        if ($new_status != false)
            $invoice->status = $new_status;

        // Add one time offers
        $one_time_offers = $old_customization->one_time_offers;
        foreach ($one_time_offers as $group => $items) {
            $this->update_company_onetime_invoices($clientid, $package, $data, $group, (array)$items);
        }


        return $invoice;
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
        $package = $this->packages($invoice->{perfex_saas_column('packageid')});
        $metadata = $package->metadata;
        $client = $this->clients_model->get($clientid);

        // Update invoice with new items
        $taxes = $metadata->invoice->taxname ?? [];
        $subtotal = (float)$package->price;
        $new_items = [
            [
                "order" => "1",
                "description" => _l('perfex_saas_invoice_desc_subscription', $package->name),
                "long_description" => "",
                "qty" => "1",
                "unit" => "",
                "rate" => $package->price,
                "taxname" => $taxes
            ]
        ];

        $validate_limits = $custom_limitations->validatable_limits;
        $new_items = array_merge($new_items, $custom_limitations->invoice_items);
        $subtotal = $subtotal + $custom_limitations->total;
        $one_time_offers = $custom_limitations->one_time_offers;

        // Validation: Ensure number of instances fit.
        $_package = clone $package;
        $_package->custom_limits = (object)$validate_limits;
        $max_instance_limit = perfex_saas_get_tenant_instance_limit($_package);
        // Get the user instances and check if matches with new limit
        $this->db->where('clientid', $clientid);
        $companies = $this->companies();
        if ($companies && count($companies) > $max_instance_limit)
            throw new \Exception(_l('perfex_saas_plan_upgrade_unfit_number_of_instances'), 1);


        // Confirm each company quota matches with new one.
        $_storage = $validate_limits['storage'] ?? null;
        if (isset($validate_limits['storage'])) unset($validate_limits['storage']);
        if (isset($validate_limits['tenant_instance'])) unset($validate_limits['tenant_instance']);

        $limited_resources = array_keys($validate_limits);
        $old_invoice = clone $invoice;
        foreach ($companies as $company) {

            $company->package_invoice = $old_invoice;

            $_storage = perfex_saas_tenant_storage_is_unlimited($company) ? null : $_storage;

            // Check for storage
            if (!is_null($_storage)) {
                $company->package_invoice->custom_limits->storage = $_storage;
                $usage = perfex_saas_tenant_used_storage($company, false);
                $quota = perfex_saas_convert_formatted_size_to_bytes(perfex_saas_tenant_storage_limit($company));
                if ($quota !== -1 && $usage > $quota)
                    throw new \Exception(_l('perfex_saas_plan_upgrade_unfit_quota', [$company->name, 'storage']), 1);
            }

            // Check for other limits
            if (!empty($limited_resources)) {
                $company->package_invoice->custom_limits = (object)$validate_limits;
                $usage_limits = perfex_saas_get_tenant_quota_usage($company, $limited_resources, $old_invoice);
                foreach ($usage_limits as $resources => $usage) {
                    $quota = perfex_saas_tenant_resources_quota($company, $resources);
                    if ($quota !== -1 && $usage > $quota) {
                        throw new \Exception(_l('perfex_saas_plan_upgrade_unfit_quota', [$company->name, $resources]), 1);
                    }
                }
            }
        }
        // End Validation

        // Validation went well, let us update invoice or subscription

        // Payments options
        $payment_modes = $metadata->invoice->allowed_payment_modes ?? [];
        if (empty($payment_modes)) {
            $all_payment_modes = $this->payment_modes_model->get();
            foreach ($all_payment_modes as $pmode) {
                $payment_modes[] = $pmode['id'];
            }
        }

        $items       = get_items_by_type('invoice', $invoice->id);

        $data = [
            "clientid" => $clientid,
            "date" => $invoice->date,
            "duedate" => $invoice->duedate,
            "tags" => PERFEX_SAAS_FILTER_TAG,
            "allowed_payment_modes" => $payment_modes,
            "currency" => get_base_currency()->id,
            "sale_agent" => $metadata->invoice->sale_agent ?? "",
            "recurring" => $metadata->invoice->recurring ?? "1",
            "repeat_every_custom" => $metadata->invoice->repeat_every_custom ?? "",
            "repeat_type_custom" => $metadata->invoice->repeat_type_custom ?? "",

            "show_quantity_as" => "1",
            "newitems" => $new_items,
            "subtotal" => $subtotal,
            "discount_percent" => "0",
            "discount_total" => "0.00",
            "total" => $subtotal,
            "removed_items" => array_column($items, 'id'),

            "billing_street"   => $client->billing_street ?? '',
            "billing_city"     => $client->billing_city ?? '',
            "billing_state"    => $client->billing_state ?? '',
            "billing_zip"      => $client->billing_zip ?? '',
            "billing_country"  => $client->billing_country ?? '',
            "shipping_street"  => $client->shipping_street ?? '',
            "shipping_city"    => $client->shipping_city ?? '',
            "shipping_state"   => $client->shipping_state ?? '',
            "shipping_zip"     => $client->shipping_zip ?? '',
            "shipping_country" => $client->shipping_country ?? '',

            "status" => $invoice->status
        ];

        // Apply tax
        if (!empty($taxes)) {
            $total_tax = 0;
            foreach ($taxes as $key => $tax) {
                $tax = explode('|', $tax);
                $tax_amount = (float)end($tax);
                $total_tax += (($tax_amount / 100) * $data['subtotal']);
            }
            $data["total"] = (float)$data["subtotal"] + $total_tax;
        }

        // Important
        $data[perfex_saas_column('packageid')] = $package->id;

        $client_metadata = (object)perfex_saas_get_or_save_client_metadata($clientid);
        $has_subscription = !empty($client_metadata->subscription_id) && !empty($client_metadata->subscription_package_id);

        if ($has_subscription) {

            $update = $this->perfex_saas_stripe_model->update_company_invoice($invoice, $clientid, $custom_limitations);
            if (isset($update->action_url)) {
                return $update;
            }
        }

        if (!$has_subscription) {

            if (!$this->invoices_model->update($data, $invoice->id)) {

                update_invoice_status($invoice->id);
                throw new \Exception(((object)$this->db->error())->message, 1);
            }
        }

        foreach ($one_time_offers as $group => $_items) {
            $this->update_company_onetime_invoices($clientid, $package, $data, $group, (array)$_items);
        }

        return true;
    }

    public function update_company_onetime_invoices($clientid, $package, $parent_invoice_data, $group, $items = [])
    {
        // Create separate invoices for each life time offer not yet purchased
        $metadata = $package->metadata;
        $taxes = $metadata->invoice->taxname ?? [];

        $scenario_key = 'onetime_purchased_' . $group . '_invoice';

        // Create separate invoices for each life time offer not yet purchased
        $item_purchased_invoices = [];
        try {

            $client_metadata = perfex_saas_get_or_save_client_metadata($clientid);

            foreach ($items as $item_id => $item) {

                $new_invoice_id = null;

                // Check if customer have item invoice generated already
                $metadata = $client_metadata;
                $onetime_purchased_item_invoice = (array)($metadata[$scenario_key] ?? []);
                if (isset($onetime_purchased_item_invoice[$item_id])) {
                    $invoice = $this->invoices_model->get($onetime_purchased_item_invoice[$item_id]);
                    if (!empty($invoice->id))
                        $new_invoice_id = $invoice->id;
                }

                if (!$new_invoice_id) {
                    // Create invoice and add to meta.
                    $price = $item['unit_price'];

                    $next_invoice_number = get_option('next_invoice_number');
                    $invoice_number      = str_pad($next_invoice_number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);

                    $new_invoice_data = $parent_invoice_data;
                    unset($new_invoice_data[perfex_saas_column('packageid')]);
                    unset($new_invoice_data['removed_items']);
                    $new_invoice_data['number'] = $invoice_number;
                    $new_invoice_data['recurring_type'] = null;
                    $new_invoice_data['custom_recurring'] = 0;
                    $new_invoice_data['subtotal']         = $price;
                    $new_invoice_data['total']            = $price;
                    $new_invoice_data['adminnote']        = '';
                    $new_invoice_data['recurring']        = 0;
                    $new_invoice_data['cycles']              = 0;
                    $new_invoice_data['total_cycles']        = 0;
                    $new_invoice_data['last_recurring_date'] = null;
                    //$new_invoice_data['is_recurring_from'] = $invoice->id; // comment this has displaying child invoice show error whe getting recurring info
                    $new_invoice_data['date'] = date('Y-m-d');
                    $new_invoice_data['duedate'] = date('Y-m-d');
                    // Set to unpaid status automatically
                    $new_invoice_data['status']                = Invoices_model::STATUS_UNPAID;
                    $new_invoice_data['newitems']              = [
                        [
                            "order" => 1,
                            "description" => $item['description'],
                            "long_description" => $item['long_description'] ?? '',
                            "qty" => 1,
                            "unit" => "",
                            "rate" => $price,
                            "taxname" => $taxes
                        ]
                    ];
                    // Apply tax
                    if (!empty($taxes)) {
                        $total_tax = 0;
                        foreach ($taxes as $tax) {
                            $tax = explode('|', $tax);
                            $tax_amount = (float)end($tax);
                            $total_tax += (($tax_amount / 100) * $new_invoice_data['subtotal']);
                        }
                        $new_invoice_data["total"] = (float)$new_invoice_data["subtotal"] + $total_tax;
                    }

                    $new_invoice_id = $this->invoices_model->add($new_invoice_data);
                }

                if ($new_invoice_id) {

                    $item_purchased_invoices[] = $new_invoice_id;
                    $onetime_purchased_item_invoice[$item_id] = $new_invoice_id;

                    // Save new custom item to DB
                    $metadata = array_merge($metadata, [$scenario_key => $onetime_purchased_item_invoice]);
                    $client_metadata = perfex_saas_get_or_save_client_metadata($clientid, $metadata);

                    if (empty($client_metadata)) {
                        $this->invoices_model->delete($new_invoice_id);
                    }
                }
            }
        } catch (\Throwable $th) {
            foreach ($item_purchased_invoices as $_new_invoice_id) {
                $this->invoices_model->delete($_new_invoice_id);
            }
            throw new \Exception($th->getMessage(), 1);
        }
        return true;
    }

    /**
     * Get a company invoice.
     *
     * @param mixed $clientid The client ID.
     * @param array $options The optional option params
     * @return mixed The company invoice.
     */
    public function get_company_invoice($clientid, $options = [])
    {
        $options['parser_callback'] = [$this, 'parse_package'];
        return perfex_saas_get_client_package_invoice($clientid, $options);
    }

    /**
     * Cancel the company subscription (invoice)
     *
     * @param integer $clientid
     * @param string $type
     * @param object|null $invoice
     * @return boolean|object False or the invoice object
     * @throws Exception
     */
    public function cancel_company_invoice(int $clientid, $type = 'immediately', $invoice = null)
    {
        // Check if the client has a subscription i.e invoice
        $invoice = $invoice ?? $this->get_company_invoice($clientid);
        $packageid = $invoice->{perfex_saas_column('packageid')};

        if (empty($packageid)) {
            throw new \Exception(_l('perfex_saas_no_invoice_client_for_client'), 1);
        }


        if (!empty($invoice->subscription_id)) {
            $cancelled_invoice = $this->perfex_saas_stripe_model->cancel_subscription($invoice->subscription_id, $type);
            if ($cancelled_invoice && $type == 'at_period_end')
                perfex_saas_get_or_save_client_metadata($clientid, ['last_cancelled_invoice' => $cancelled_invoice->subscription_id]);
            return $cancelled_invoice;
        }

        // Mark as cancelled if not paid or partially paid
        $stale_invoices = perfex_saas_get_company_invoice_child_invoices($invoice, false);
        if (!isset($invoice->is_mock))
            $stale_invoices[] = $invoice;

        foreach ($stale_invoices as $_invoice) {

            // Mark as cancelled is not paid or partially paid
            if (
                $_invoice->status == Invoices_model::STATUS_DRAFT ||
                (
                    $_invoice->id == $invoice->id && !in_array($_invoice->status, [Invoices_model::STATUS_PAID, Invoices_model::STATUS_PARTIALLY])
                )
            ) {
                if (!$this->invoices_model->mark_as_cancelled($_invoice->id))
                    throw new \Exception(_l('perfex_saas_invoice_cancel_error'), 1);
            }

            // Mark as non recurring (so the child invoice will not be recreated by cron)
            if ($_invoice->recurring != "0")
                $this->invoices_model->db->update(perfex_saas_master_db_prefix() . 'invoices', ["recurring" => "0"], ['id' => $_invoice->id]);
        }

        // Remove recurring
        perfex_saas_get_or_save_client_metadata($clientid, ['last_cancelled_invoice' => $invoice->id, 'last_cancelled_invoice_recurring' => $invoice->recurring, 'subscription_id' => '']);
        return $this->get_company_cancelled_invoice($clientid, false);
    }

    /**
     * Get the company resumable invoice.
     *
     * @param int $clientid
     * @param bool $resumable
     * @return false|object The invoice with package_invoice or false
     */
    public function get_company_cancelled_invoice($clientid, $resumable = true)
    {
        $client_metadata = (object)perfex_saas_get_or_save_client_metadata($clientid);

        if (!empty($client_metadata->subscription_id)) {
            $canceled_invoice = $this->get_company_invoice($clientid, ['include_cancelled' => true]);


            if (!$canceled_invoice) return false;

            if ($canceled_invoice->subscription_status != 'canceled' && empty($canceled_invoice->subscription_ends_at))
                return false;

            if ($resumable && $canceled_invoice->subscription_status == 'canceled')
                return false;

            return $canceled_invoice;
        }

        $last_cancelled_invoice_id = $client_metadata->last_cancelled_invoice ?? '';
        if (empty($last_cancelled_invoice_id))
            return false;

        // Check if the client has a subscription i.e invoice
        $invoice = $this->invoices_model->get($last_cancelled_invoice_id);
        if (!$invoice || empty($package_id = $invoice->{perfex_saas_column('packageid')} ?? ''))
            return false;

        return $invoice;
    }

    /**
     * Resume the company subscription (invoice)
     *
     * @param integer $clientid
     * @return bool|object False or the invoice object
     */
    public function resume_company_invoice(int $clientid)
    {
        $invoice = $this->get_company_cancelled_invoice($clientid);

        $client_metadata = (object)perfex_saas_get_or_save_client_metadata($clientid);

        if ($invoice && !empty($package_id = $invoice->{perfex_saas_column('packageid')})) {

            // Ensure the package still exist
            // @todo Ensure the package is active also
            $package = $this->packages($package_id);
            if (!$package) return false;

            if (!empty($invoice->subscription_id)) {
                $resumed_invoice = $this->perfex_saas_stripe_model->resume_subscription($invoice->subscription_id);
            } else {

                // Resume recurring
                $metadata = $package->metadata;
                $update_data = [
                    "recurring" => $client_metadata->last_cancelled_invoice_recurring ?? $metadata->invoice->recurring ?? "1",
                    'status' => $invoice->status == Invoices_model::STATUS_CANCELLED ? Invoices_model::STATUS_UNPAID : $invoice->status,
                ];
                $this->invoices_model->db->update(perfex_saas_master_db_prefix() . 'invoices', $update_data, ['id' => $invoice->id]);
                $resumed_invoice = $this->get_company_invoice($clientid);
            }
            if ($resumed_invoice) {
                perfex_saas_get_or_save_client_metadata($clientid, ['last_cancelled_invoice' => '', 'last_cancelled_invoice_recurring' => '']);
                return $resumed_invoice;
            }
        }
        return false;
    }


    /**
     * Prepare the invoice customization data for a package from a given data source
     * Data source can be post or client metadata (customization  info) or both
     * 
     * @param object $package
     * @param array $data_source
     * @param array $old_package Optional. Provide to filter out neccessary limits only for the new $package
     * @return Perfex_saas_custom_limit
     */
    public function prepare_invoice_customization_items($package, $data_source, $old_package = null)
    {
        $modules = $this->perfex_saas_model->modules();
        $services = $this->perfex_saas_model->services();
        return Perfex_saas_custom_limit::create($package, $data_source, $modules, $services, $old_package);
    }
}


/**
 * Class to manage custom limitations in friendly way
 */
class Perfex_saas_custom_limit
{

    public ?array $custom_limits;
    public ?array $purchased_modules;
    public ?array $purchased_services;
    public ?array $customization_items;
    public ?array $one_time_offers;
    public ?array $invoice_items;
    public ?float $total;
    public ?float $total_discounts;
    public ?array $validatable_limits;

    public function __construct(object $data = null)
    {
        $this->custom_limits = $data->custom_limits ?? [];
        $this->purchased_modules = $data->purchased_modules ?? [];
        $this->purchased_services = $data->purchased_services ?? [];
        $this->customization_items = $data->customization_items ?? [];
        $this->one_time_offers = $data->one_time_offers ?? [];
        $this->invoice_items = $data->invoice_items ?? [];
        $this->total = $data->total ?? 0;
        $this->total_discounts = $data->total_discounts ?? 0;
        $this->validatable_limits = $data->validatable_limits ?? [];
    }

    public function clear()
    {
        self::__construct(new stdClass);
        return $this;
    }

    public static function create(object $package, $data_source, $modules, $services, object $old_package = null)
    {
        $taxes = $package->metadata->invoice->taxname ?? [];

        // Modules
        $purchased_modules = (array)($data_source['purchased_modules'] ?? []);

        // Services
        $purchased_services = (array)($data_source['purchased_services'] ?? []);

        // Custom limits
        $custom_limits = (array)($data_source['custom_limits'] ?? []);

        $one_time_offers = [];

        $invoice_items = [];
        $custom_limitations = [];
        $validatable_limits = [];

        $discounts = $package->metadata->formatted_discounts ?? [];

        // Build invoice items.
        if (!empty($custom_limits)) {
            foreach ($custom_limits as $resources => $quantity) {

                $quantity = (int)$quantity;
                if ($quantity <= 0) continue;

                $is_storage = $resources === 'storage';
                $unit_price = $is_storage ? ($package->metadata->storage_limit->unit_price ?? 0) : ($package->metadata->limitations_unit_price->{$resources} ?? 0);
                $unit_price = (float)$unit_price;

                // When old package is provided,make comparism and only include item that is not coverred in the new package
                if ($old_package) {
                    $old_quota = (int)($old_package->metadata->limitations->{$resources} ?? -1);
                    $old_quota = $old_quota + $quantity;
                    $new_limit = (int)($package->metadata->limitations->{$resources} ?? -1);
                    if ($old_quota <= -1 || $new_limit == -1 || $new_limit >= $old_quota) {
                        continue;
                    }
                }

                $custom_limitations[] = [
                    'resources' => $resources,
                    'quantity' => $quantity,
                    'description' => _l('perfex_saas_invoice_addon_item_desc', _l('perfex_saas_limit_' . $resources)),
                    'unit_price' => $unit_price,
                ];
            }
        }

        if (!empty($purchased_modules)) {
            foreach ($purchased_modules as $index => $module) {

                if (!isset($modules[$module])) continue;

                $price = $package->metadata->limitations_unit_price->{$module} ?? '';
                if ($price === '')
                    $price = $modules[$module]['price'] ?? 0;

                if (!$price) {
                    unset($purchased_modules[$index]);
                    continue;
                }

                // Only include purchased modules on in the new package package
                if ($old_package && in_array($module, (array)$package->modules ?? [])) {
                    continue;
                }

                $item = [
                    'resources' => $module,
                    'quantity' => 1,
                    'description' => _l('perfex_saas_invoice_addon_module_item_desc', $modules[$module]['custom_name']),
                    //"long_description" => $modules[$module]['description'] ?? "",
                    'unit_price' => $price,
                    'skip_limit_validation' => true,
                ];

                if ($modules[$module]['billing_mode'] === 'lifetime')
                    $one_time_offers['module'][$module] = $item;
                else
                    $custom_limitations[] = $item;
            }
        }

        if (!empty($purchased_services)) {
            foreach ($purchased_services as $index => $service_id) {

                if (!isset($services[$service_id])) continue;

                $service = $services[$service_id];
                $price = (float)($package->metadata->limitations_unit_price->{$service_id} ?? ($service['price'] ?? 0));
                if (!$price) {
                    unset($purchased_services[$index]);
                    continue;
                }

                $item = [
                    'resources' => $service_id,
                    'quantity' => 1,
                    'description' => _l('perfex_saas_invoice_addon_service_item_desc', $service['name']),
                    'unit_price' => $price,
                    'skip_limit_validation' => true,
                ];

                if ($service['billing_mode'] === 'lifetime')
                    $one_time_offers['service'][$service_id] = $item;
                else
                    $custom_limitations[] = $item;
            }
        }

        $order = 2; // Starting from 2 so main item have 1
        $total = 0;
        $total_discounts = 0;

        // Apply discount and make invoice items
        if (!empty($custom_limitations)) {
            foreach ($custom_limitations as $key => $limit) {
                $quantity = (int)$limit['quantity'];
                $resources = $limit['resources'];
                $unit_price = (float)$limit['unit_price'];

                if ($quantity <= 0) {
                    continue;
                }

                if (!isset($limit['skip_limit_validation']))
                    $validatable_limits[$resources] = $quantity;

                $discount = $discounts->{$resources} ?? [];
                if (!empty($discount)) {
                    arsort($discount);
                    foreach ($discount as $level => $value) {
                        $level = (int)$level;
                        if ($quantity >= $level) {
                            $percent = ((float)$value['percent']) / 100;
                            $discount_amount =  ($unit_price * $percent);
                            $unit_price = $unit_price - $discount_amount;
                            $custom_limitations[$key]['discounted_unit_price'] = $unit_price;
                            $total_discounts += $discount_amount;
                            break;
                        }
                    }
                }

                $total = $total + ($unit_price * $quantity);

                $invoice_items[] = [
                    "order" => $order,
                    "description" => $limit['description'],
                    "long_description" => $limit['long_description'] ?? '',
                    "qty" => $quantity,
                    "unit" => "",
                    "rate" => $unit_price,
                    "taxname" => $taxes
                ];
                $order++;
            }
        }

        $result = new Perfex_saas_custom_limit();
        $result->custom_limits = $custom_limits;
        $result->purchased_modules = $purchased_modules;
        $result->purchased_services = $purchased_services;
        $result->customization_items = $custom_limitations;
        $result->one_time_offers = $one_time_offers;
        $result->invoice_items = $invoice_items;
        $result->total = $total;
        $result->total_discounts = $total_discounts;
        $result->validatable_limits = $validatable_limits;

        return $result;
    }
}