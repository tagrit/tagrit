<?php

use Stripe\Checkout\Session;
use Stripe\Collection;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\SignatureVerificationException;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\TaxRate;
use Stripe\Webhook;
use Stripe\WebhookEndpoint;

defined('BASEPATH') or exit('No direct script access allowed');
// For Stripe Checkout
class Stripe_core
{
    protected $ci;

    protected $secretKey;

    protected $publishableKey;

    protected $apiVersion = '2020-03-02';

    /**
     * Initialize Stripe_core class
     */
    public function __construct()
    {
        $this->ci             = &get_instance();
        $this->secretKey      = $this->ci->stripe_gateway->decryptSetting('api_secret_key');
        $this->publishableKey = $this->ci->stripe_gateway->getSetting('api_publishable_key');

        \Stripe\Stripe::setApiVersion($this->apiVersion);
        \Stripe\Stripe::setApiKey($this->secretKey);
    }

    /**
     * Create new customer in strip
     *
     * @param array $data
     *
     * @return Customer
     * @throws ApiErrorException
     */
    public function create_customer($data): Customer
    {
        return Customer::create($data);
    }

    /**
     * Retrieve customer
     *
     * @param array|string $id
     *
     * @return Customer
     * @throws ApiErrorException
     */
    public function get_customer($id): Customer
    {
        return Customer::retrieve($id);
    }

    /**
     * Update customer
     *
     * @param string $id
     * @param array $payload
     *
     * @return Customer
     * @throws ApiErrorException
     */
    public function update_customer($id, $payload): Customer
    {
        return Customer::update($id, $payload);
    }

    /**
     * Get Stripe publishable key
     *
     * @return string|null
     */
    public function get_publishable_key(): ?string
    {
        return $this->publishableKey;
    }

    /**
     * List the created webhook endpoint for the current environment
     *
     * @return Collection
     * @throws ApiErrorException
     */
    public function list_webhook_endpoints(): Collection
    {
        return WebhookEndpoint::all();
    }

    /**
     * Get the necessary Stripe integration webhook events
     *
     * @return array
     */
    public function get_webhook_events(): array
    {
        $events = [
            'checkout.session.completed',
            'invoice.payment_succeeded',
            'invoice.payment_action_required',
            'invoice.payment_failed',
            'customer.subscription.created',
            'customer.subscription.deleted',
            'customer.subscription.updated',
            'customer.deleted',
        ];

        return hooks()->apply_filters('stripe_webhook_events', $events);
    }

    /**
     * Get available Stripe tax rates
     *
     * @return Collection
     * @throws ApiErrorException
     */
    public function get_tax_rates(): Collection
    {
        return TaxRate::all(['limit' => 100]);
    }

    /**
     * Retrieve tax rate by given id
     *
     * @param array|string $id
     *
     * @return TaxRate
     * @throws ApiErrorException
     */
    public function retrieve_tax_rate($id): TaxRate
    {
        return TaxRate::retrieve($id);
    }

    /**
     * Create webhook in Stripe for the integration
     *
     * @return WebhookEndpoint
     * @throws ApiErrorException
     */
    public function create_webhook(): WebhookEndpoint
    {
        $webhook = WebhookEndpoint::create([
            'url'            => $this->ci->stripe_gateway->webhookEndPoint,
            'enabled_events' => $this->get_webhook_events(),
            'api_version'    => $this->apiVersion,
            'metadata'       => ['identification_key' => get_option('identification_key')],
        ]);

        update_option('stripe_webhook_id', $webhook->id);
        update_option('stripe_webhook_signing_secret', $webhook->secret);

        return $webhook;
    }

    /**
     * Enable webhook by given id
     *
     * @param string $id
     *
     * @return void
     * @throws ApiErrorException
     */
    public function enable_webhook($id): void
    {
        WebhookEndpoint::update($id, [
            'disabled' => false,
          ]);
    }

    /**
     * Delete the given webhook
     *
     * @param string $id
     *
     * @return void
     * @throws ApiErrorException
     */
    public function delete_webhook($id): void
    {
        $endpoint = WebhookEndpoint::retrieve($id);
        $endpoint->delete();
    }

    /**
     * Create new checkout session
     *
     * @param array $data
     *
     * @return Session
     * @throws ApiErrorException
     */
    public function create_session($data): Session
    {
        return Session::create($data);
    }

    /**
     * Retrieve checkout session
     *
     * @param array|string $data
     *
     * @return Session
     * @throws ApiErrorException
     */
    public function retrieve_session($data): Session
    {
        return Session::retrieve($data);
    }

    /**
     * Retrieve payment intent
     *
     * @param array|string $data
     *
     * @return PaymentIntent
     * @throws ApiErrorException
     */
    public function retrieve_payment_intent($data): PaymentIntent
    {
        return PaymentIntent::retrieve($data);
    }

    /**
     * Retrieve payment method
     *
     * @param array|string $data
     *
     * @return PaymentMethod
     * @throws ApiErrorException
     */
    public function retrieve_payment_method($data): PaymentMethod
    {
        return PaymentMethod::retrieve($data);
    }

    /**
     * Create constturct event
     *
     * @param array $payload
     * @param string $secret
     *
     * @return \Stripe\Event
     * @throws SignatureVerificationException
     */
    public function construct_event($payload, $secret): \Stripe\Event
    {
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];

        return Webhook::constructEvent(
                $payload,
                $sig_header,
                $secret
          );
    }

    /**
     * Check whether there is api key added for the integration
     *
     * @return boolean
     */
    public function has_api_key(): bool
    {
        return $this->secretKey != '';
    }
}
