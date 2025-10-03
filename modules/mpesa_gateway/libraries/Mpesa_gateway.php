<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once(__DIR__ . '/Mpesa.php');

class Mpesa_gateway extends App_gateway
{
    protected $support_currency = ["KES", "USD"];

    /** @var Mpesa $client */
    protected $client;

    public function __construct()
    {
        /**
         * Call App_gateway __construct function
         */
        parent::__construct();


        $this->ci = &get_instance();
        $this->ci->load->database();

        /**
         * Gateway unique id - REQUIRED
         */
        $this->setId('mpesa');

        /**
         * REQUIRED
         * Gateway name
         */
        $this->setName('Mpesa');

        /**
         * Add gateway settings. This is used for generating UI setting by perfex core
         */
        $this->setSettings([
            [
                'name'              => 'consumer_key',
                'label'             => 'Consumer key',
                'type'              => 'input'
            ],
            [
                'name'              => 'consumer_secret',
                'label'             => 'Consumer secret',
                'type'              => 'input',
                'encrypted'         => true,
            ],
            [
                'name'              => 'stk_pass_key',
                'label'             => 'STK pass key',
                'type'              => 'input',
                'encrypted'         => true,
            ],
            [
                'name'              => 'short_code',
                'label'             => 'Short code',
                'type'              => 'input'
            ],
            [
                'name'              => 'phone_number',
                'label'             => 'Phone number',
                'type'              => 'input'
            ],
            [
                'name'              => 'party_b',
                'label'             => 'Organization ID ( Till/Paybill number or Leave empty if unsure)',
                'type'              => 'input'
            ],
            [
                'name'              => 'transaction_type',
                'label'             => 'Transaction type',
                'type'              => 'select',
                'after'             => render_select('settings[paymentmethod_mpesa_transaction_type]', [['id' => 'CustomerPayBillOnline'], ['id' => 'CustomerBuyGoodsOnline']], ['id', 'id'], 'Transaction type', $this->getSetting('transaction_type'), [], [], '', '', false)
            ],
            [
                'name'              => 'sandbox_mode_enabled',
                'type'              => 'yes_no',
                'default_value'     => 0,
                'label'             => 'settings_paymentmethod_testing_mode',
            ],
            [
                'name'              => 'currencies',
                'label'             => 'settings_paymentmethod_currencies',
                'default_value'     => implode(",", $this->support_currency)
            ],
            [
                'name'              => 'usd_to_kes',
                'type'              => 'input',
                'default_value'     => 127,
                'label'             => 'usd_to_kes',
            ],
        ]);

        hooks()->add_filter('app_payment_gateways', [$this, 'initMode']);
    }

    /**
     * Method to create instance of custom Mpesa sdk api wrapper for daraja api v2
     *
     * @return Mpesa
     */
    public function getClient()
    {
        if (!$this->client)
            //create new instance of mpesa library
            $this->client = new Mpesa([
                'mode' => $this->getSetting('sandbox_mode_enabled') ? 'sandbox' : 'live',
                'consumer_key' => $this->getSetting('consumer_key'),
                'consumer_secret' => $this->decryptSetting('consumer_secret'),
                'phone_number' => $this->getSetting('phone_number'), //admin mpesa phone number
                'party_b' => $this->getSetting('party_b'),
                'short_code' => $this->getSetting('short_code'),
                'stk_pass_key' => $this->decryptSetting('stk_pass_key'), //LIPA stk push password
                'logger' => function ($messages = []) {
                    log_message('debug', $messages[0]);
                },
                'transaction_type' => $this->getSetting('transaction_type')
            ]);

        return $this->client;
    }

    /**
     * Each time a customer click PAY NOW button on the invoice HTML area, the script will process the payment via this function.
     * You can show forms here, redirect to gateway website, redirect to Codeigniter controller etc..
     * @param  array $data - Contains the total amount to pay and the invoice information
     * @return mixed
     */
    public function process_payment($data)
    {
        $this->ci->session->set_userdata(['payment_data' => $data]);
        redirect(site_url('mpesa_gateway/process/make_payment'));
    }

    /**
     * Method to get KES equivalent amount of a currency
     *
     * @param float $amount
     * @param string $currency
     * @throws Exception Unsupported currency
     * @return float
     */
    public function get_amount_in_kes($amount, $currency)
    {
        $currency = strtoupper($currency);
        if ($currency == 'KES') return $amount;
        if ($currency == 'USD') {
            $oneUsdEquivInKes = (float)($this->getSetting('usd_to_kes') ?? 0);
            return $amount * $oneUsdEquivInKes;
        }
        throw new \Exception("Unsupported currency: $currency", 1);
    }


    /**
     * Method to convert a currency to another.
     * Currently support just USD and KES
     *
     * @param float $amount
     * @param string $to_currency
     * @param string $from_currency
     * @throws Exception Unsupported currency
     * @return void
     */
    public function get_amount_in_currency($amount, $to_currency, $from_currency = 'KES')
    {
        $currency = strtoupper($to_currency);
        $from_currency = strtoupper($from_currency);
        if ($currency == $from_currency) return $amount;
        if ($currency == 'USD') {
            if ($from_currency == 'KES') {
                $oneUsdEquivInKes = (float)($this->getSetting('usd_to_kes') ?? 0);
                return $amount / $oneUsdEquivInKes;
            }
        }

        throw new \Exception("Unsupported currency: $currency", 1);
    }
}