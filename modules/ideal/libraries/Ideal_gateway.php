<?php

use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\SignatureVerificationException;
use Stripe\PaymentIntent;
use Stripe\StripeClient;
use Stripe\Webhook;
use Stripe\WebhookEndpoint;

defined('BASEPATH') or exit('No direct script access allowed');

class Ideal_gateway extends App_gateway
{
    public bool $processingFees = true;
    private StripeClient $stripe;
    private string $webhookEndPoint;

    public function __construct()
    {
        parent::__construct();
        $this->setId(IDEAL_MODULE_GATEWAY_ID);
        $this->setName('Stripe iDEAL V2');
        $this->setSettings([
            [
                'name' => 'api_publishable_key',
                'label' => 'ideal_api_publishable_key',
                'type' => 'input'
            ],
            [
                'name' => 'api_secret_key',
                'encrypted' => true,
                'label' => 'ideal_api_secret_key',
                'type' => 'input',
            ],
            [
                'name' => 'currencies',
                'label' => 'settings_paymentmethod_currencies',
                'default_value' => 'EUR',
                'field_attributes' => ['disabled' => true],
            ],
            [
                'name' => 'description_dashboard',
                'label' => 'settings_paymentmethod_description',
                'type' => 'textarea',
                'default_value' => 'Payment for Invoice {invoice_number}',
            ],
        ]);

        $this->stripe = new StripeClient([
            "api_key" => $this->decryptSetting('api_secret_key')
        ]);

        $this->webhookEndPoint = site_url('ideal/webhook');
        hooks()->add_action('before_render_payment_gateway_settings', 'idealModuleWebhookCheck');
    }

    /**
     * @throws ApiErrorException
     */
    public function process_payment(array $data): void
    {
        if ($this->processingFees) {
            $this->ci->session->set_userdata([
                'attempt_fee' => $data['payment_attempt']->fee,
                'attempt_amount' => $data['payment_attempt']->amount,
            ]);
        }

        $sessionData = [
            'payment_method_types' => ['ideal'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $this->getSetting('currencies'),
                    'product_data' => [
                        'name' => $this->getDescription($data['invoiceid']),
                    ],
                    'unit_amount' => $data['amount'] * 100,
                ],
                'quantity' => 1,
            ]],
            'payment_intent_data' => [
                'capture_method' => 'automatic',
                'metadata' => [
                    'invoice_id' => $data['invoice']->id,
                    'attempt_reference' => $data['payment_attempt']->reference,
                    'attempt_fee' => $data['payment_attempt']->fee,
                ],
            ],
            'mode' => 'payment',
            'ui_mode' => 'embedded',
            'return_url' => site_url("/ideal/callback/{$data['invoiceid']}/{$data['hash']}?session_id={CHECKOUT_SESSION_ID}"),
        ];
        if ($data['invoice']->client->stripe_id) {
            $sessionData['customer'] = $data['invoice']->client->stripe_id;
            $sessionData['saved_payment_method_options'] = ['payment_method_save' => 'enabled'];
        }

        $session = $this->stripe->checkout->sessions->create($sessionData);

        $this->ci->session->set_userdata([
            'total_amount' => $data['amount'],
            'ideal_client_secret' => $session->client_secret
        ]);

        redirect(site_url('/ideal/make_payment/' . $data['invoice']->id . '/' . $data['invoice']->hash));
    }

    public function getPublishableKey(): string
    {
        return $this->getSetting('api_publishable_key');
    }

    public function getDescription($invoiceId): string
    {
        $invoiceNumber = format_invoice_number($invoiceId);
        return str_replace('{invoice_number}', $invoiceNumber, $this->getSetting('description_dashboard'));
    }

    /**
     * @throws ApiErrorException
     */
    public function retrieveSession($sessionId): Session
    {
        return $this->stripe->checkout->sessions->retrieve($sessionId);
    }

    /**
     * @throws ApiErrorException
     */
    public function retrievePaymentIntent($intentId): PaymentIntent
    {
        return $this->stripe->paymentIntents->retrieve($intentId);
    }

    public function hasSecretKey(): bool
    {
        return !empty($this->decryptSetting('api_secret_key'));
    }

    public function getWebhookEndPoint(): string
    {
        return $this->webhookEndPoint;
    }

    /**
     * Determine the Stripe environment based on the keys
     *
     * @return string
     */
    public function environment(): string
    {
        $environment = 'production';
        $apiKey = $this->decryptSetting('api_secret_key');

        if (str_contains($apiKey, 'sk_test')) {
            $environment = 'test';
        }

        return $environment;
    }

    /**
     * @return null|WebhookEndpoint
     * @throws ApiErrorException
     */
    public function getCurrentWebhookObject(): ?WebhookEndpoint
    {
        $webhook = null;
        foreach ($this->getAllWebhookObjects() as $endpoint) {
            if ($endpoint->url == $this->getWebhookEndPoint()) {
                $webhook = $endpoint;
                break;
            }
        }
        return $webhook;
    }

    /**
     * @return WebhookEndpoint[]
     * @throws ApiErrorException
     */
    public function getAllWebhookObjects(): array
    {
        return $this->stripe->webhookEndpoints->all()->data;
    }

    /**
     * @throws ApiErrorException
     */
    public function deleteWebhook(WebhookEndpoint $webhookEndpoint): void
    {
        $this->stripe->webhookEndpoints->delete($webhookEndpoint->id);
    }

    public function getIdentificationKey(): string
    {
        return "ideal-gateway-v2-" . get_option('identification_key');
    }

    public function createWebhook(): WebhookEndpoint
    {
        $webhook = $this->stripe->webhookEndpoints->create([
            'url' => $this->webhookEndPoint,
            'enabled_events' => idealModuleWebhookEvents(),
            'metadata' => ['identification_key' => $this->getIdentificationKey()],
        ]);
        update_option('ideal_module_stripe_webhook_id', $webhook->id);
        update_option('ideal_module_stripe_webhook_signing_secret', $webhook->secret);

        return $webhook;
    }

    /**
     * @throws ApiErrorException
     */
    public function enableCurrentWebhookEndpoint(): void
    {
        $this->stripe->webhookEndpoints->update(
            $this->getCurrentWebhookObject()?->id,
            ['disabled' => false,]
        );
    }

    /**
     * @throws SignatureVerificationException
     */
    public function validateAndReturnWebhookEvent(bool|string $payload): \Stripe\Event
    {
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $secret = get_option('ideal_module_stripe_webhook_signing_secret');
        return Webhook::constructEvent($payload, $sig_header, $secret);
    }
}
