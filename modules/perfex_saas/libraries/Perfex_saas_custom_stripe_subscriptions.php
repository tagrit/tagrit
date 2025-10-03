<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(APPPATH . 'libraries/Stripe_subscriptions.php');

class Perfex_saas_custom_stripe_subscriptions extends Stripe_subscriptions
{
    public function update_subscription($subscription_id, $update_values, $db_subscription, $prorate = false)
    {
        if (empty($subscription_id)) {
            return false;
        }

        // Prevent updating saas subscription from admin interface.
        $metadata = perfex_saas_search_client_metadata('subscription_id', (int)$subscription_id);
        if (!empty($metadata)) {
            throw new \Exception(_l('perfex_saas_stripe_admin_update_denied'), 1);
        }

        return parent::update_subscription($subscription_id, $update_values, $db_subscription, $prorate);
    }

    function get_subscription($data)
    {
        $stripeSubscription = parent::get_subscription($data);
        if (!isset($stripeSubscription->plan)) {
            $stripeSubscription->plan = $stripeSubscription->items->data[0]->plan ?? $stripeSubscription->items->data[0]->price ?? new stdClass;
            if (!isset($stripeSubscription->plan->currency))
                $stripeSubscription->plan->currency = $stripeSubscription->currency;
        }

        return $stripeSubscription;
    }
}