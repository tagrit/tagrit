<?php

use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\SignatureVerificationException;
use Stripe\PaymentIntent;

/**
 * @property-read Ideal_gateway $ideal_gateway
 * @property-read Invoices_model $invoices_model
 * @property-read CI_Session $session
 */
class Ideal extends App_Controller
{
    public function make_payment($id, $hash)
    {
        check_invoice_restrictions($id, $hash);
        $this->load->model('invoices_model');
        $invoice = $this->invoices_model->get($id);
        load_client_language($invoice->clientid);


        $data['invoice'] = $invoice;
        $data['description'] = $this->ideal_gateway->getDescription($id);
        $data['total'] = $this->session->userdata('total_amount');
        $data['client_secret'] = $this->session->userdata('ideal_client_secret');
        if ($this->ideal_gateway->processingFees) {
            $data['attempt_amount'] = $this->session->userdata('attempt_amount');
            $data['attempt_fee'] = $this->session->userdata('attempt_fee');
        }

        if (!$data['client_secret']) {
            redirect(site_url("invoice/{$id}/{$hash}"));
        }
        echo $this->getHtml($data);
    }

    private function getHtml($data): string
    {
        ob_start();
        echo payment_gateway_head(); ?>
        <script src="https://js.stripe.com/v3/"></script>

        <body class="gateway-stripe-ideal">
        <div class="container">
            <div class="col-md-8 col-md-offset-2 mtop30">
                <div class="mbot30 text-center">
                    <?php echo payment_gateway_logo(); ?>
                </div>
                <div class="row">
                    <div class="panel_s">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <?php echo _l('payment_for_invoice'); ?>
                                <a
                                        href="<?php echo site_url('invoice/' . $data['invoice']->id . '/' . $data['invoice']->hash); ?>">
                                    <?php echo e(format_invoice_number($data['invoice']->id)); ?>
                                </a>
                            </h4>
                        </div>
                        <div class="panel-body">
                            <?php if (isset($data['attempt_amount']) && isset($data['attempt_fee'])) { ?>
                                <div>
                                    <h5><?php echo _l('payment_attempt_amount') . ": " . e(app_format_money($data['attempt_amount'], $data['invoice']->currency_name)); ?></h5>
                                    <h5><?php echo _l('payment_attempt_fee') . ": " . e(app_format_money($data['attempt_fee'], $data['invoice']->currency_name)); ?></h5>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="panel-footer">

                            <div id="checkout">
                                <!-- Checkout will insert the payment form here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Initialize Stripe.js
            const stripe = Stripe('<?= $this->ideal_gateway->getPublishableKey() ?>');

            const initialize = async () => {
                const fetchClientSecret = () => '<?= $data['client_secret'] ?>';

                // Initialize Checkout
                const checkout = await stripe.initEmbeddedCheckout({
                    fetchClientSecret,
                });
                // Mount Checkout
                checkout.mount('#checkout');
            }
            try {
                initialize();
            } catch (e) {
                alert_float('warning', 'please try again');
                window.location.replace('<?= site_url('invoice/' . $data['invoice']->id . '/' . $data['invoice']->hash); ?>')
            }
        </script>
        </body>

        <?php
        echo payment_gateway_footer();
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }

    public function callback($id, $hash)
    {
        $this->load->model('invoices_model');
        check_invoice_restrictions($id, $hash);

        $invoice = $this->invoices_model->get($id);
        load_client_language($invoice->clientid);
        $redirectURL = site_url('invoice/' . $id . '/' . $hash);

        $sessionId = $this->input->get('session_id');
        $this->session->unset_userdata(['total_amount', 'ideal_client_secret']);
        if ($sessionId === null || $sessionId === '') {
            redirect($redirectURL);
        }

        try {
            $session = $this->ideal_gateway->retrieveSession($sessionId);
            if ($session->status !== Session::STATUS_COMPLETE) {
                set_alert('danger', _l('ideal_payment_failure_message'));
                redirect($redirectURL);
            }

            if (total_rows('invoicepaymentrecords', ['transactionid' => $session->payment_intent]) !== 0) {
                redirect($redirectURL);
            }

            $metaData = $this->ideal_gateway->retrievePaymentIntent($session->payment_intent)->metadata->toArray();
            $success = $this->ideal_gateway->addPayment([
                'amount' => $session->amount_total / 100,
                'invoiceid' => $metaData['invoice_id'],
                'transactionid' => $session->payment_intent,
                'paymentmethod' => 'IDEAL',
                'payment_attempt_reference' => $metaData['attempt_reference'],
            ]);

            if ($success) {
                set_alert('success', _l('online_payment_recorded_success'));
            } else {
                set_alert('danger', _l('online_payment_recorded_success_fail_database'));
            }
            redirect($redirectURL);
        } catch (ApiErrorException $e) {
            set_alert('danger', _l('ideal_payment_failure_message'));
            redirect($redirectURL);
        }
    }

    public function create_webhook()
    {
        try {
            foreach ($this->ideal_gateway->getAllWebhookObjects() as $webhook) {
                if ($webhook->metadata?->identification_key === $this->ideal_gateway->getIdentificationKey()
                    || $webhook->url == $this->ideal_gateway->getWebhookEndPoint()
                ) {
                    $this->ideal_gateway->deleteWebhook($webhook);
                }
            }

            if ($this->input->get('recreate')) {
                update_option('ideal_module_stripe_webhook_id', '');
                update_option('ideal_module_stripe_webhook_signing_secret', '');
            }
        } catch (Exception $e) {
        }

        try {
            $this->ideal_gateway->createWebhook();
            set_alert('success', _l('webhook_created'));
        } catch (Exception $e) {
            $this->session->set_flashdata('stripe-webhook-failure', $e->getMessage());
        }

        redirect(admin_url('settings/?group=payment_gateways&tab=online_payments_Ideal_gateway_tab'));
    }

    /**
     * @throws ApiErrorException
     */
    public function enable_webhook(): void
    {
        if (staff_can('edit', 'settings')) {
            $this->ideal_gateway->enableCurrentWebhookEndpoint();
        }
        redirect(admin_url('settings/?group=payment_gateways&tab=online_payments_Ideal_gateway_tab'));
    }

    public function webhook(): void
    {
        $payload = @file_get_contents('php://input');
        try {
            $event = $this->ideal_gateway->validateAndReturnWebhookEvent($payload);
        } catch (UnexpectedValueException|SignatureVerificationException $e) {
            echo '⚠️  Webhook error while validating signature.';
            http_response_code(400);
            exit();
        }

        switch ($event->type) {
            case \Stripe\Event::TYPE_PAYMENT_INTENT_SUCCEEDED:
                /** @var PaymentIntent $paymentIntent */
                $paymentIntent = $event->data->object;
                if (total_rows('invoicepaymentrecords', ['transactionid' => $paymentIntent->id]) !== 0) {
                    http_response_code(200);
                    exit();
                }
                $this->ideal_gateway->addPayment([
                    'amount' => $paymentIntent->amount / 100,
                    'invoiceid' => $paymentIntent->metadata->invoice_id,
                    'transactionid' => $paymentIntent->id,
                    'paymentmethod' => 'IDEAL - ' . $paymentIntent->source,
                    'payment_attempt_reference' => $paymentIntent->metadata->attempt_reference,
                ]);
                http_response_code(200);
                exit();
            default:
                exit();
        }
    }
}
