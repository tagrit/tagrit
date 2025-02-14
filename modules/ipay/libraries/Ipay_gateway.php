<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ipay_gateway extends App_gateway
{
   
    public function __construct()
    {
        /**
        * Call App_gateway __construct function
        */
        parent::__construct();
        /**
         * REQUIRED
         * Gateway unique id
         * The ID must be alpha/alphanumeric
         */
        $this->setId('ipay');

        /**
         * REQUIRED
         * Gateway name
         */
        $this->setName('iPay');

        /**
         * Add gateway settings
        */
        $this->setSettings([
            [
                'name'  => 'vendor_id',
                'label' => 'payment_gateway_ipay_vendor_id'
            ],
            [
                'name'      => 'security_key',
                'label'     => 'payment_gateway_ipay_security_key'
            ],
            [
                'name'          => 'description_dashboard',
                'label'         => 'settings_paymentmethod_description',
                'type'          => 'textarea',
                'default_value' => 'Payment for Invoice {invoice_number}',
            ],
            [
                'name'          => 'currencies',
                'label'         => 'payment_gateway_ipay_currency',
                'default_value' => 'KES'
            ],
            [
                'name'          => 'test_mode_enabled',
                'type'          => 'yes_no',
                'default_value' => 1,
                'label'         => 'settings_paymentmethod_testing_mode',
            ],
            ]);
    }

    public function process_payment($data)
    {
        $this->ci->session->set_userdata(['ipay_total' => number_format($data['amount'], 2, '.', '')]);
        redirect(site_url('ipay/make_payment/' . $data['invoiceid'] . '/' . $data['invoice']->hash));
    }

}
