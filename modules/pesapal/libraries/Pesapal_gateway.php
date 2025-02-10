<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pesapal_gateway extends App_gateway
{
    protected $sandbox_url = 'https://demo.pesapal.com/api/';

    protected $production_url = 'https://www.pesapal.com/api/';

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
        $this->setId('pesapal');

        /**
         * REQUIRED
         * Gateway name
         */
        $this->setName('Pesapal');

        /**
         * Add gateway settings
        */
        $this->setSettings([
            [
                'name'  => 'consumer_key',
                'label' => 'payment_gateway_pesapal_consumer_key'
            ],
            [
                'name'      => 'consumer_secret',
                'label'     => 'payment_gateway_pesapal_consumer_secret'
            ], 
            [
                'name'  => 'consumer_key_demo',
                'label' => 'payment_gateway_pesapal_consumer_key_demo'
            ],
            [
                'name'      => 'consumer_secret_demo',
                'label'     => 'payment_gateway_pesapal_consumer_secret_demo'
            ],            
            [
                'name'      => 'type',
                'label'     => 'payment_gateway_pesapal_type',
                'default_value' => 'MERCHANT'
            ],
            [
                'name'          => 'description_dashboard',
                'label'         => 'settings_paymentmethod_description',
                'type'          => 'textarea',
                'default_value' => 'Payment for Invoice {invoice_number}',
            ],
            [
                'name'          => 'currencies',
                'label'         => 'payment_gateway_pesapal_currency',
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
        $this->ci->session->set_userdata(['pesapal_total' => number_format($data['amount'], 2, '.', '')]);
        redirect(site_url('pesapal/make_payment/' . $data['invoiceid'] . '/' . $data['invoice']->hash));
    }

    public function get_action_url()
    {
        return $this->getSetting('test_mode_enabled') == '1' ? $this->sandbox_url : $this->production_url;
    }

    public function gen_transaction_id()
    {
        return substr(hash('sha256', mt_rand() . microtime()), 0, 20);
    }

    public function get_field_value($table, $where=array(), $check="")
    {
        $CI =& get_instance();
        $CI->db->where($where);
        $data = $CI->db->get($table)->row();
        if(!empty($data) && !empty($check)){
          return $data->$check;
        }else if(!empty($data) && empty($check)){
          return $data;
        }else{
                return null;
        }
    }

}
