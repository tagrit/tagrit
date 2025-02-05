<?php

defined('BASEPATH') or exit('No direct script access allowed');
/*
Module Name: Stripe Ideal V2
Description: Stripe Ideal Payment module
Version: 1.0.0
Requires at least: 3.1.6
*/

const IDEAL_MODULE_NAME       = 'ideal';
const IDEAL_MODULE_GATEWAY_ID = 'Ideal_gateway';

register_language_files(IDEAL_MODULE_NAME, [IDEAL_MODULE_NAME]);
register_payment_gateway(IDEAL_MODULE_GATEWAY_ID, IDEAL_MODULE_NAME);
register_activation_hook(IDEAL_MODULE_NAME, 'idealModuleActivation');

function idealModuleActivation()
{
    add_option('ideal_module_stripe_webhook_id');
    add_option('ideal_module_stripe_webhook_signing_secret');
}

/**
 * Get the necessary Stripe integration webhook events
 */
function idealModuleWebhookEvents(): array
{
    return [
        Stripe\Event::TYPE_PAYMENT_INTENT_SUCCEEDED,
    ];
}

/**
 * @param array{id: string, active: string, instance: Ideal_gateway} $gateway
 */
function idealModuleWebhookCheck(array $gateway): void
{
    if ($gateway['id'] === IDEAL_MODULE_GATEWAY_ID) {
        $idealGateway = $gateway['instance'];
        if ($idealGateway->hasSecretKey() && $gateway['active'] == '1') {
            try {
                $webhook = $idealGateway->getCurrentWebhookObject();
            } catch (Exception $e) {
                echo '<div class="alert alert-warning">';
                // useful when user add wrong keys
                // e.q. This API call cannot be made with a publishable API key. Please use a secret API key. You can find a list of your API keys at https://dashboard.stripe.com/account/apikeys.
                echo $e->getMessage();
                echo '</div>';

                return;
            }

            $environment = $idealGateway->environment();
            $endpoint    = $idealGateway->getWebhookEndPoint();
            $CI          = &get_instance();
            if ($CI->session->has_userdata('stripe-webhook-failure')) {
                echo '<div class="alert alert-warning" style="margin-bottom:15px;">';
                echo '<h4>Error: ' . $CI->session->userdata('stripe-webhook-failure') . '</h4>';
                echo 'The system was unable to create the <b>required</b> webhook endpoint for Stripe.';
                echo '<br />You should consider creating webhook manually directly via Stripe dashboard for your environment (' . $environment . ')';
                echo '<br /><br /><b>Webhook URL:</b><br />' . $endpoint;
                echo '<br /><br /><b>Webhook events:</b><br />' . implode(',<br />', idealModuleWebhookEvents());
                echo '</div>';
            }

            if (! $webhook || ! startsWith($webhook->url, site_url())) {
                echo '<div class="alert alert-warning">';
                echo 'Webhook endpoint (' . $endpoint . ') not found for ' . $environment . ' environment.';
                echo '<br />Click <a href="' . site_url('ideal/create_webhook') . '">here</a> to create the webhook directly in Stripe.';
                echo '</div>';
            } elseif ($webhook->id != get_option('ideal_module_stripe_webhook_id')) {
                echo '<div class="alert alert-warning">';
                echo 'The application stored Stripe webhook id does not match the configured webhook.';
                echo '<br />Click <a href="' . site_url('ideal/create_webhook?recreate=true') . '">here</a> to re-create the webhook directly in Stripe and delete the old webhook.';
                echo '</div>';
            } elseif ($webhook->status != 'enabled') {
                echo '<div class="alert alert-warning">';
                echo 'Your Stripe configured webhook is disabled, you should consider enabling your webhook via Stripe dashboard or by clicking <a href="' . site_url('ideal/enable_webhook') . '">here</a>.';
                echo '</div>';
            }
        }
    }
}
