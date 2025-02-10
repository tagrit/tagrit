<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * This class describes a sage accounting integration model.
 */
class Sage_accounting_integration_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * update general setting
     *
     * @param      array   $data   The data
     *
     * @return     boolean
     */
    public function update_setting($data)
    {
        $affectedRows = 0;
        if (!isset($data['settings']['acc_integration_sage_accounting_active'])) {
            $data['settings']['acc_integration_sage_accounting_active'] = 0;
        }

        if (!isset($data['settings']['acc_integration_sage_accounting_sync_from_system'])) {
            $data['settings']['acc_integration_sage_accounting_sync_from_system'] = 0;
        }

        if (!isset($data['settings']['acc_integration_sage_accounting_sync_to_system'])) {
            $data['settings']['acc_integration_sage_accounting_sync_to_system'] = 0;
        }


        if (isset($data['settings']['acc_integration_sage_accounting_client_id'])) {
            $data['settings']['acc_integration_sage_accounting_client_id'] = $this->encryption->encrypt($data['settings']['acc_integration_sage_accounting_client_id']);
        }

        if (isset($data['settings']['acc_integration_sage_accounting_client_secret'])) {
            $data['settings']['acc_integration_sage_accounting_client_secret'] = $this->encryption->encrypt($data['settings']['acc_integration_sage_accounting_client_secret']);
        }

        foreach ($data['settings'] as $key => $value) {
            if (update_option($key, $value)) {
                $affectedRows++;
            }
        }

        if ($affectedRows > 0) {
            return true;
        }
        return false;
    }

    public function init_sage_accounting_config(){
        $configs = get_acc_sage_accounting_config();

        // Prep Data Services
        $params = [
            'refresh_token' => $configs['refresh_token'],
            'client_id' => $configs['client_id'], 
            'client_secret' => $configs['client_secret'], 
            'grant_type' => "refresh_token"
        ];

        $http_header = array(
                 'Accept' => 'application/json',
                 'Content-Type' => 'application/x-www-form-urlencoded'
            );

        if($configs['access_token_expires'] <= (time() + 30)){
            $result = $this->executeRequest($configs['access_token_url'], $params, $http_header,'POST');

            if(isset($result['access_token'])){
                update_option('acc_integration_sage_accounting_access_token', $result['access_token']);
                update_option('acc_integration_sage_accounting_access_token_expires', time() + $result['expires_in']);
                update_option('acc_integration_sage_accounting_refresh_token', $result['refresh_token']);
                update_option('acc_integration_sage_accounting_refresh_token_expires', time() + $result['refresh_token_expires_in']);
                update_option('acc_integration_sage_accounting_requested_by_id', $result['requested_by_id']);
            }
        }
    }


    public function create_sage_accounting_customer($customer_id = ''){
        $this->db->select('*, '.db_prefix() . 'clients.userid as userid');
        if($customer_id != ''){
            $this->db->where(db_prefix().'clients.userid', $customer_id);
        }
        $this->db->join(db_prefix() . 'contacts', db_prefix() . 'contacts.userid=' . db_prefix() . 'clients.userid AND '.db_prefix() . 'contacts.is_primary = "1"', 'left');
        $this->db->join(db_prefix() . 'acc_integration_logs', db_prefix() . 'acc_integration_logs.rel_id=' . db_prefix() . 'clients.userid AND '.db_prefix() . 'acc_integration_logs.rel_type = "customer" AND software = "sage_accounting"', 'left');

        $clients = $this->db->get(db_prefix().'clients')->result_array();

        $header = acc_get_sage_accounting_header();
        $api_domain = acc_get_sage_accounting_api_domain();

        $customer_data = [];
        $continue = true;
        $page = 1;

        $list_results = [];
        while ($continue) {
            $continue = false;
            $url = $api_domain.'/contacts?attributes=all&contact_type_id=CUSTOMER&items_per_page=200&page='.$page;
            $entities = $this->callAPI($url, [], $header, 'GET');
            $page++;
            if (isset($entities['$items'])) {
                foreach ($entities['$items'] as $customer) {
                    $customer_data[$customer['id']] = $customer;
                }
            }

            if(isset($entities['$next']) && $entities['next'] != NULL){
                $continue = true;
            }
        }

        foreach ($clients as $client) {
            $customerObj = [
                "contact" => [
                 "name"=>  $client['company'],
                 "contact_type_ids" =>  ["CUSTOMER"],
                 "company_name"=>  $client['company'],
                 "website"=>  $client['website'],

                "main_address" => [
                     "name" => $client['company'].': Main Address',
                     "address_line_1"=>  $client['billing_street'],
                     "address_line_2"=>  '',
                     "city"=>  $client['billing_city'],
                     "region"=> $client['billing_country'],
                     "postal_code"=>  $client['billing_zip']
                 ],

                 "delivery_address" => [
                     "name" => $client['company'].': Delivery Address',
                     "address_line_1" =>  $client['shipping_street'],
                     "address_line_2" =>  '',
                     "city"=>  $client['shipping_city'],
                     "region"=> $client['shipping_country'],
                     "postal_code"=>  $client['shipping_zip']
                 ],

                 "main_contact_person" => [
                    "name" => $client['firstname'].' '.$client['lastname'],
                    "email" => $client['email'],
                    "telephone" => $client['phonenumber'],
                    "is_main_contact" => true
                 ]
             ]
         ];

            if($client['connect_id'] != '' && isset($customer_data[$client['connect_id']])){
                $url = $api_domain.'/contacts/'.$client['connect_id'];
                $result = $this->callAPI($url, $customerObj, $header, 'PUT');
            }else{
                if($client['connect_id'] != ''){
                    $this->delete_integration_log($client['userid'], 'customer', 'sage_accounting');
                }

                $url = $api_domain.'/contacts';
                $result = $this->callAPI($url, $customerObj, $header, 'POST');
            }

            $this->delete_integration_error_log($client['userid'], 'customer', 'sage_accounting');
            if (!isset($result['id'])) {

                $message = '';

                foreach ($result as $key => $value) {
                    $message .= '<br>'.$value['$message'];
                }

                $this->db->insert(db_prefix().'acc_integration_error_logs', [
                    'rel_id' => $client['userid'],
                    'rel_type' => 'customer',
                    'software' => 'sage_accounting',
                    'error_detail' => $message,
                    'date_updated' => date('Y-m-d H:i:s'),
                ]);

                $this->db->insert(db_prefix().'acc_integration_sync_logs', [
                    'rel_id' => $client['userid'],
                    'rel_type' => 'customer',
                    'software' => 'sage_accounting',
                    'type' => 'sync_up',
                    'status' => 0,
                    'connect_id' => '',
                    'datecreated' => date('Y-m-d H:i:s'),
                ]);

                if($customer_id != ''){
                    return $message;
                }
            }else{
                if(!isset($customer_data[$client['connect_id']])){
                    $this->db->insert(db_prefix().'acc_integration_logs', [
                        'rel_id' => $client['userid'],
                        'rel_type' => 'customer',
                        'software' => 'sage_accounting',
                        'connect_id' => $result['id'],
                        'date_updated' => date('Y-m-d H:i:s'),
                    ]);
                }

                $this->db->insert(db_prefix().'acc_integration_sync_logs', [
                    'rel_id' => $client['userid'],
                    'rel_type' => 'customer',
                    'software' => 'sage_accounting',
                    'type' => 'sync_up',
                    'status' => 1,
                    'connect_id' => $result['id'],
                    'datecreated' => date('Y-m-d H:i:s'),
                ]);

                if($customer_id != ''){
                    return true;
                }
            }
        }
    }

    public function create_sage_accounting_invoice($invoice_id = ''){
        $this->db->select('*, ' . db_prefix() . 'currencies.id as currencyid, ' . db_prefix() . 'invoices.id as id, ' . db_prefix() . 'currencies.name as currency_name');
        if($invoice_id != ''){
            $this->db->where(db_prefix().'invoices.id', $invoice_id);
        }
        $this->db->join(db_prefix() . 'currencies', '' . db_prefix() . 'currencies.id = ' . db_prefix() . 'invoices.currency', 'left');
        $this->db->join(db_prefix() . 'acc_integration_logs', db_prefix() . 'acc_integration_logs.rel_id=' . db_prefix() . 'invoices.id AND '.db_prefix() . 'acc_integration_logs.rel_type = "invoice" AND software = "sage_accounting"', 'left');

        $invoices = $this->db->get(db_prefix().'invoices')->result_array();

        $header = acc_get_sage_accounting_header();
        $api_domain = acc_get_sage_accounting_api_domain();

        $income_account_id = $this->get_sage_accounting_income_account();

        $invoice_data = [];
        $continue = true;
        $page = 1;

        $list_results = [];
        while ($continue) {
            $continue = false;
            $url = $api_domain.'/sales_invoices?attributes=all&items_per_page=200&page='.$page;
            $entities = $this->callAPI($url, [], $header, 'GET');
            $page++;
            if (isset($entities['$items'])) {
                foreach ($entities['$items'] as $invoice) {
                    $invoice_data[$invoice['id']] = $invoice;
                }
            }

            if(isset($entities['$next']) && $entities['next'] != NULL){
                $continue = true;
            }
        }

        foreach ($invoices as $invoice) {
            $customer_connect_id = $this->get_connect_id($invoice['clientid'], 'customer', 'sage_accounting');

            $items  = get_items_by_type('invoice', $invoice['id']);
            $item_array = [];

            $tax_amount = 0;
            foreach ($items as $item) {
                $data_tax = $this->get_invoice_item_tax($item, $invoice);

                foreach ($data_tax['tax_amount'] as $k => $amount) {
                    $tax_amount += $amount;
                }

                $tax_connect_id = '';

                $item_array[] = [
                    "item_id" => '',
                    "quantity" => $item['qty'],
                    "unit_price" => $item['rate'],
                    'description' => $item['description'],
                    'total_amount' => $item['rate'] * $item['qty'],
                    'tax_rate_id' => $tax_connect_id,
                    'ledger_account_id' => $income_account_id
                ];
            }

            if($tax_amount > 0){
                $item_array[] = [
                    "item_id" => '',
                    "quantity" => 1,
                    "unit_price" => $tax_amount,
                    'description' => 'Total Tax',
                    'total_amount' => $tax_amount,
                    'tax_rate_id' => $tax_connect_id,
                    'ledger_account_id' => $income_account_id
                ];
            }
            
            if($invoice['discount_total'] > 0){
                $item_array[] = [
                    "item_id" => '',
                    "quantity" => 1,
                    "unit_price" => 0,
                    "discount_amount" => $invoice['discount_total'],
                    'description' => 'Discount',
                    'ledger_account_id' => $income_account_id
                ];
            }

            if($invoice['adjustment'] != 0){
                $item_array[] = [
                    "item_id" => '',
                    "quantity" => 1,
                    "unit_price" => $invoice['adjustment'],
                    'description' => 'Adjustment',
                    'total_amount' => $invoice['adjustment'],
                    'ledger_account_id' => $income_account_id
                ];
            }

            $invoiceObj = [
                "sales_invoice" => [
                    "contact_id"=> $customer_connect_id,
                    "date" => $invoice['date'],
                    "due_date" => $invoice['duedate'],
                    "notes" => $invoice['clientnote'],
                    "reference" => format_invoice_number($invoice['id']),
                    "invoice_lines" => $item_array,
                    "total_amount" => $invoice['total'],
                    "net_amount" => $invoice['subtotal'],
                    "tax_amount" => $invoice['total_tax'],
                ]
            ];

            if($invoice['connect_id'] != '' && isset($invoice_data[$invoice['connect_id']])){
                $url = $api_domain.'/sales_invoices/'.$invoice['connect_id'];
                $result = $this->callAPI($url, $invoiceObj, $header, 'PUT');
            }else{
                if($invoice['connect_id'] != ''){
                    $this->delete_integration_log($invoice['id'], 'invoice', 'sage_accounting');
                }

                $url = $api_domain.'/sales_invoices';
                $result = $this->callAPI($url, $invoiceObj, $header, 'POST');
            }

            $this->delete_integration_error_log($invoice['id'], 'invoice', 'sage_accounting');
            if (!isset($result['id'])) {

                $message = '';

                foreach ($result as $key => $value) {
                    $message .= '<br>'.$value['$message'];
                }

                $this->db->insert(db_prefix().'acc_integration_error_logs', [
                    'rel_id' => $invoice['id'],
                    'rel_type' => 'invoice',
                    'software' => 'sage_accounting',
                    'error_detail' => $message,
                    'date_updated' => date('Y-m-d H:i:s'),
                ]);

                $this->db->insert(db_prefix().'acc_integration_sync_logs', [
                    'rel_id' => $invoice['id'],
                    'rel_type' => 'invoice',
                    'software' => 'sage_accounting',
                    'type' => 'sync_up',
                    'status' => 0,
                    'connect_id' => '',
                    'datecreated' => date('Y-m-d H:i:s'),
                ]);

                if($invoice_id != ''){
                    return $message;
                }
            }else{
                if(!isset($invoice_data[$invoice['connect_id']])){
                    $this->db->insert(db_prefix().'acc_integration_logs', [
                        'rel_id' => $invoice['id'],
                        'rel_type' => 'invoice',
                        'software' => 'sage_accounting',
                        'connect_id' => $result['id'],
                        'date_updated' => date('Y-m-d H:i:s'),
                    ]);
                }

                $this->db->insert(db_prefix().'acc_integration_sync_logs', [
                    'rel_id' => $invoice['id'],
                    'rel_type' => 'invoice',
                    'software' => 'sage_accounting',
                    'type' => 'sync_up',
                    'status' => 1,
                    'connect_id' => $result['id'],
                    'datecreated' => date('Y-m-d H:i:s'),
                ]);

                if($invoice_id != ''){
                    return true;
                }
            }
        }
    }

    public function create_sage_accounting_payment($payment_id = ''){
        $this->db->select('*, ' . db_prefix() . 'invoicepaymentrecords.id as id');
        if($payment_id != ''){
            $this->db->where(db_prefix().'invoicepaymentrecords.id', $payment_id);
        }
        $this->db->join(db_prefix() . 'acc_integration_logs', db_prefix() . 'acc_integration_logs.rel_id=' . db_prefix() . 'invoicepaymentrecords.id AND '.db_prefix() . 'acc_integration_logs.rel_type = "payment" AND software = "sage_accounting"', 'left');

        $payments = $this->db->get(db_prefix().'invoicepaymentrecords')->result_array();

        $header = acc_get_sage_accounting_header();
        $api_domain = acc_get_sage_accounting_api_domain();
        $bank_account_id = $this->get_sage_accounting_bank_account();

        $payment_data = [];
        $continue = true;
        $page = 1;

        $list_results = [];
        while ($continue) {
            $continue = false;
            $url = $api_domain.'/contact_payments?attributes=all&items_per_page=200&page='.$page;
            $entities = $this->callAPI($url, [], $header, 'GET');
            $page++;
            if (isset($entities['$items'])) {
                foreach ($entities['$items'] as $payment) {
                    $payment_data[$payment['id']] = $payment;
                }
            }

            if(isset($entities['$next']) && $entities['next'] != NULL){
                $continue = true;
            }
        }

        foreach ($payments as $payment) {
            $this->db->where('id', $payment['invoiceid']);
            $invoice = $this->db->get(db_prefix().'invoices')->row();

            $invoice_connect_id = $this->get_connect_id($payment['invoiceid'], 'invoice', 'sage_accounting');
            $customer_connect_id = $this->get_connect_id($invoice->clientid, 'customer', 'sage_accounting');

            $paymentObj = [
                "contact_payment" => [
                    "contact_id" => $customer_connect_id,
                    "date" => $payment['date'],
                    "total_amount" => $payment['amount'],
                    "payment_method_id" => "CASH",
                    "bank_account_id" => $bank_account_id,
                    "transaction_type_id" => 'CUSTOMER_RECEIPT',
                    "allocated_artefacts" => [
                        [
                            "artefact_id" => $invoice_connect_id,
                            "amount" => $payment['amount']
                        ]
                    ]
                ]
            ];

            if($payment['connect_id'] != '' && isset($payment_data[$payment['connect_id']])){
                $url = $api_domain.'/contact_payments/'.$payment['connect_id'];
                $result = $this->callAPI($url, $paymentObj, $header, 'PUT');
            }else{
                if($payment['connect_id'] != ''){
                    $this->delete_integration_log($payment['id'], 'payment', 'sage_accounting');
                }

                $url = $api_domain.'/contact_payments';
                $result = $this->callAPI($url, $paymentObj, $header, 'POST');
            }

            $this->delete_integration_error_log($payment['id'], 'payment', 'sage_accounting');
            if (!isset($result['id'])) {

                $message = '';

                foreach ($result as $key => $value) {
                    $message .= '<br>'.$value['$message'];
                }

                $this->db->insert(db_prefix().'acc_integration_error_logs', [
                    'rel_id' => $payment['id'],
                    'rel_type' => 'payment',
                    'software' => 'sage_accounting',
                    'error_detail' => $message,
                    'date_updated' => date('Y-m-d H:i:s'),
                ]);  

                $this->db->insert(db_prefix().'acc_integration_sync_logs', [
                    'rel_id' => $payment['id'],
                    'rel_type' => 'payment',
                    'software' => 'sage_accounting',
                    'type' => 'sync_up',
                    'status' => 0,
                    'connect_id' => '',
                    'datecreated' => date('Y-m-d H:i:s'),
                ]);

                if($payment_id != ''){
                    return $message;
                }
            }else{
                if(!isset($payment_data[$payment['connect_id']])){
                    $this->db->insert(db_prefix().'acc_integration_logs', [
                        'rel_id' => $payment['id'],
                        'rel_type' => 'payment',
                        'software' => 'sage_accounting',
                        'connect_id' => $result['id'],
                        'date_updated' => date('Y-m-d H:i:s'),
                    ]);
                }

                $this->db->insert(db_prefix().'acc_integration_sync_logs', [
                    'rel_id' => $payment['id'],
                    'rel_type' => 'payment',
                    'software' => 'sage_accounting',
                    'type' => 'sync_up',
                    'status' => 1,
                    'connect_id' => $result['id'],
                    'datecreated' => date('Y-m-d H:i:s'),
                ]);

                if($payment_id != ''){
                    return true;
                }
            }
        }
    }

    public function create_sage_accounting_expense($expense_id = ''){
        $this->db->select('*, ' . db_prefix() . 'expenses.id as id');
        if($expense_id != ''){
            $this->db->where(db_prefix().'expenses.id', $expense_id);
        }
        $this->db->join(db_prefix() . 'acc_integration_logs', db_prefix() . 'acc_integration_logs.rel_id=' . db_prefix() . 'expenses.id AND '.db_prefix() . 'acc_integration_logs.rel_type = "expense" AND software = "sage_accounting"', 'left');

        $expenses = $this->db->get(db_prefix().'expenses')->result_array();

        $header = acc_get_sage_accounting_header();
        $api_domain = acc_get_sage_accounting_api_domain();

        $expense_account_id = $this->get_sage_accounting_expense_account();
        $vendor_connect_id = $this->get_sage_accounting_vendor_default();

        $expense_data = [];
        $continue = true;
        $page = 1;

        while ($continue) {
            $continue = false;
            $url = $api_domain.'/purchase_quick_entries?attributes=all&items_per_page=200&page='.$page;
            $entities = $this->callAPI($url, [], $header, 'GET');
            $page++;
            if (isset($entities['$items'])) {
                foreach ($entities['$items'] as $expense) {
                    $expense_data[$expense['id']] = $expense;
                }
            }

            if(isset($entities['$next']) && $entities['next'] != NULL){
                $continue = true;
            }
        }
        
        foreach ($expenses as $expense) {
            $total_tax = 0;

            if($expense['tax'] > 0){
                $this->db->where('id', $expense['tax']);
                $tax = $this->db->get(db_prefix().'taxes')->row();
                if($tax){
                    $total_tax += ($tax->taxrate/100) * $expense['amount'];
                }
            }

            if($expense['tax2'] > 0){
                $this->db->where('id', $expense['tax2']);
                $tax = $this->db->get(db_prefix().'taxes')->row();
                if($tax){
                    $total_tax += ($tax->taxrate/100) * $expense['amount'];
                }
            }

            $customer_connect_id = '';

            if($expense['clientid'] != 0){
                $customer_connect_id = $this->get_connect_id($expense['clientid'], 'customer', 'sage_accounting');
            }

            $tax_connect_id = '';

            $expenseObj = [
                "purchase_quick_entry" => [
                    "quick_entry_type_id" => 'INVOICE',
                    "contact_id" => $vendor_connect_id,
                    "ledger_account_id" => $expense_account_id,
                    "date" => $expense['date'],
                    "net_amount" => $expense['amount'] + $total_tax,
                    "total_amount" => $expense['amount'] + $total_tax,
                    "reference" => "Expenses#".$expense['id']
                ]
            ];

            if($expense['connect_id'] != '' && isset($expense_data[$expense['connect_id']])){
                $url = $api_domain.'/purchase_quick_entries/'.$expense['connect_id'];
                $result = $this->callAPI($url, $expenseObj, $header, 'PUT');
            }else{
                if($expense['connect_id'] != ''){
                    $this->delete_integration_log($expense['id'], 'expense', 'sage_accounting');
                }

                $url = $api_domain.'/purchase_quick_entries';
                $result = $this->callAPI($url, $expenseObj, $header, 'POST');
            }

            $this->delete_integration_error_log($expense['id'], 'expense', 'sage_accounting');
            if (!isset($result['id'])) {

                $message = '';

                foreach ($result as $key => $value) {
                    $message .= '<br>'.$value['$message'];
                }

                $this->db->insert(db_prefix().'acc_integration_error_logs', [
                    'rel_id' => $expense['id'],
                    'rel_type' => 'expense',
                    'software' => 'sage_accounting',
                    'error_detail' => $message,
                    'date_updated' => date('Y-m-d H:i:s'),
                ]);

                $this->db->insert(db_prefix().'acc_integration_sync_logs', [
                    'rel_id' => $expense['id'],
                    'rel_type' => 'expense',
                    'software' => 'sage_accounting',
                    'type' => 'sync_up',
                    'status' => 0,
                    'connect_id' => '',
                    'datecreated' => date('Y-m-d H:i:s'),
                ]);

                if($expense_id != ''){
                    return $message;
                }
            }else{
                if(!isset($expense_data[$expense['connect_id']])){
                    $this->db->insert(db_prefix().'acc_integration_logs', [
                        'rel_id' => $expense['id'],
                        'rel_type' => 'expense',
                        'software' => 'sage_accounting',
                        'connect_id' => $result['id'],
                        'date_updated' => date('Y-m-d H:i:s'),
                    ]);
                }

                $this->db->insert(db_prefix().'acc_integration_sync_logs', [
                    'rel_id' => $expense['id'],
                    'rel_type' => 'expense',
                    'software' => 'sage_accounting',
                    'type' => 'sync_up',
                    'status' => 1,
                    'connect_id' => $result['id'],
                    'datecreated' => date('Y-m-d H:i:s'),
                ]);

                if($expense_id != ''){
                    return true;
                } 
            }
        }
    }

    public function get_sage_accounting_bank_account(){
        $header = acc_get_sage_accounting_header();
        $api_domain = acc_get_sage_accounting_api_domain();

        $url = $api_domain.'/bank_accounts?items_per_page=200';

        $result = $this->callAPI($url, [], $header, 'GET');

        if (!isset($result['$items'])) {
            return 0;   
        }else{
            foreach ($result['$items'] as $key => $value) {
                if($value['displayed_as'] == 'Cash (1210)'){
                    return $value['id'];
                }
            }

            return 0;
        }
    }

    public function get_sage_accounting_income_account(){
        $header = acc_get_sage_accounting_header();
        $api_domain = acc_get_sage_accounting_api_domain();

        $url = $api_domain.'/ledger_accounts?visible_in=sales&items_per_page=200';

        $result = $this->callAPI($url, [], $header, 'GET');

        if (!isset($result['$items'])) {
            return 0;   
        }else{
            foreach ($result['$items'] as $key => $value) {
                if($value['displayed_as'] == 'Other income (4900)'){
                    return $value['id'];
                }
            }

            return 0;
        }
    }

    public function get_sage_accounting_expense_account(){
        $header = acc_get_sage_accounting_header();
        $api_domain = acc_get_sage_accounting_api_domain();

        $url = $api_domain.'/ledger_accounts?visible_in=expenses&items_per_page=200';
        $result = $this->callAPI($url, [], $header, 'GET');

        if (!isset($result['$items'])) {
            return 0;   
        }else{
            foreach ($result['$items'] as $key => $value) {
                if($value['displayed_as'] == 'Cost of Sales - Goods (5000)'){
                    return $value['id'];
                }
            }

            return 0;
        }
    }

    public function get_sage_accounting_vendor_default(){
        $header = acc_get_sage_accounting_header();
        $api_domain = acc_get_sage_accounting_api_domain();

        $url = $api_domain.'/contacts?search=FromSync';
        $result = $this->callAPI($url, [], $header, 'GET');
        if (!isset($result['$items']) || (isset($result['$items']) && count($result['$items']) == 0)) {
            $customerObj = [
                "contact" => [
                     "name"=>  "Expenses Vendor",
                     "contact_type_ids" =>  ["VENDOR"],
                     "company_name"=>  "Expenses Vendor",
                     "reference" => "FromSync"
                ]
            ];

            $url = $api_domain.'/contacts';

            $result = $this->callAPI($url, $customerObj, $header, 'POST');

            if (!isset($result['id'])) {
                return '';
            }else{
                return $result['id'];
            }
        }else{
            foreach ($result['$items'] as $key => $value) {
                return $value['id'];
            }

            return '';
        }
    }

    public function get_sage_accounting_customer(){
        $customer_list = $this->clients_model->get();
     
        $customer_arr = [];
        foreach ($customer_list as $customer) {
            $customer_arr[$customer['userid']] = $customer;
        }

        $header = acc_get_sage_accounting_header();
        $api_domain = acc_get_sage_accounting_api_domain();

        $continue = true;
        $page = 1;

        $list_results = [];
        while ($continue) {
            $continue = false;
            $url = $api_domain.'/contacts?attributes=all&contact_type_id=CUSTOMER&items_per_page=200&page='.$page;
            $entities = $this->callAPI($url, [], $header, 'GET');
            $page++;
            if (isset($entities['$items'])) {
                $list_results = array_merge($list_results, $entities['$items']);
            }

            if(isset($entities['$next']) && $entities['next'] != NULL){
                $continue = true;
            }
        }

        foreach ($list_results as $customer) {

            $check_connect_id = $this->check_connect_id($customer['id'], 'customer', 'sage_accounting');
            

            $url = $api_domain.'/addresses/c10effa3125c4d69ab169044884d917c';
            $customer_detail = $this->callAPI($url, [], $header, 'GET');

            $customer_data = [];
            $customer_data['company'] = $customer['name'];
            $customer_data['phonenumber'] = '';

            $customer_data['balance'] = '';
            $customer_data['balance_as_of'] = '';

            $url = $api_domain.$customer['main_address']['$path'];
            $address_data = $this->callAPI($url, [], $header, 'GET');

            $customer_data['billing_street'] = $address_data['address_line_1'];
            $customer_data['billing_city'] = $address_data['city'];
            $customer_data['billing_state'] = '';
            $customer_data['billing_zip'] = $address_data['postal_code'];
            $customer_data['billing_country'] = $address_data['region'];

            $shipping_street = '';
            $shipping_city = '';
            $shipping_zip = '';

            $customer_data['shipping_street'] = $shipping_street;
            $customer_data['shipping_city'] = $shipping_city;
            $customer_data['shipping_state'] = '';
            $customer_data['shipping_zip'] = $shipping_zip;
            $customer_data['shipping_country'] = '';

            $customer_data['default_currency'] = '';
            if($check_connect_id != 0 && isset($customer_arr[$check_connect_id])){
                $this->clients_model->update($customer_data, $check_connect_id);
                $client_id = $check_connect_id;
            }else{
                if($check_connect_id != 0){
                    $this->delete_integration_log($check_connect_id, 'customer', 'sage_accounting');
                }

                $client_id = $this->clients_model->add($customer_data);
            }

            $sync_status = 0;
            if($client_id){
            $sync_status = 1;
                if(!isset($customer_arr[$check_connect_id])){
                    if(count($customer['main_contact_person']) > 0){

                        $url = $api_domain.$customer['main_contact_person']['$path'];
                        $contract_detail = $this->callAPI($url, [], $header, 'GET');
                        if(isset($contract_detail['id'])){
                            $contact_data = [];
                            $contact_data['firstname'] = $contract_detail['name'];
                            $contact_data['lastname'] = $contract_detail['name'];
                            $contact_data['phonenumber'] = $contract_detail['telephone'];
                            $contact_data['email'] = $contract_detail['email'];
                            $contact_data['title'] = '';
                            $contact_data['direction'] = '';
                            $contact_data['fakeusernameremembered'] = '';
                            $contact_data['fakepasswordremembered'] = '';
                            $contact_data['password'] = '123456@';
                            $contact_data['is_primary'] = 'on';
                            $contact_data['donotsendwelcomeemail'] = 'on';
                            $contact_data['permissions'] = ['1','2','3','4','5','6'];
                            $contact_data['invoice_emails'] = 'invoice_emails';
                            $contact_data['estimate_emails'] = 'estimate_emails';
                            $contact_data['credit_note_emails'] = 'credit_note_emails';
                            $contact_data['project_emails'] = 'project_emails';
                            $contact_data['ticket_emails'] = 'ticket_emails';
                            $contact_data['task_emails'] = 'task_emails';
                            $contact_data['contract_emails'] = 'contract_emails';
                            $this->clients_model->add_contact($contact_data, $client_id);
                        }
                    }

                    $this->db->insert(db_prefix().'acc_integration_logs', [
                        'rel_id' => $client_id,
                        'rel_type' => 'customer',
                        'software' => 'sage_accounting',
                        'connect_id' => $customer['id'],
                        'date_updated' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            $this->db->insert(db_prefix().'acc_integration_sync_logs', [
                        'rel_id' => $client_id,
                        'rel_type' => 'customer',
                        'software' => 'sage_accounting',
                        'type' => 'sync_down',
                        'status' => $sync_status,
                        'connect_id' => $customer['id'],
                        'datecreated' => date('Y-m-d H:i:s'),
                    ]);
        }
    }


    public function get_sage_accounting_invoice(){
        $this->load->model('invoices_model');
        $invoice_list = $this->invoices_model->get();
     
        $invoice_arr = [];
        foreach ($invoice_list as $invoice) {
            $invoice_arr[$invoice['id']] = $invoice;
        }

        $header = acc_get_sage_accounting_header();
        $api_domain = acc_get_sage_accounting_api_domain();

        $continue = true;
        $page = 1;

        $list_results = [];
        while ($continue) {
            $continue = false;
            $url = $api_domain.'/sales_invoices?attributes=all&items_per_page=200&page='.$page;
            $entities = $this->callAPI($url, [], $header, 'GET');
            $page++;
            if (isset($entities['$items'])) {
                $list_results = array_merge($list_results, $entities['$items']);
            }

            if(isset($entities['$next']) && $entities['next'] != NULL){
                $continue = true;
            }
        }

        $this->load->model('payment_modes_model');
        $payment_modes = $this->payment_modes_model->get();

        $payment_model_list = [];
        if ($payment_modes) {
            foreach($payment_modes as $payment_mode){
                $payment_model_list[] = $payment_mode['id'];
            }
        }

        foreach ($list_results as $invoice) {
            $check_connect_id = $this->check_connect_id($invoice['id'], 'invoice', 'sage_accounting');
            

            $customer_connect_id = $this->check_connect_id($invoice['contact']['id'], 'customer', 'sage_accounting');
            if($customer_connect_id == 0){
                $this->get_quickbook_customer();
                $customer_connect_id = $this->check_connect_id($invoice['contact']['id'], 'customer', 'sage_accounting');
            }

            $invoice_data = [];

            $currency = get_currency($invoice['currency']['id']);
            $invoice_data['currency'] = '1';

            $invoice_data['date'] = $invoice['date'];
            $invoice_data['duedate'] = $invoice['due_date'];

            $invoice_data['clientid']         = $customer_connect_id;

            $invoice_data['include_shipping']         = 1;
            $invoice_data['show_shipping_on_invoice'] = 1;

            $invoice_data["allowed_payment_modes"] = $payment_model_list;

            $billing_street = '';
            $billing_city = '';
            $billing_state = '';
            $billing_zip = '';
            $billing_country = '';
            if($invoice['main_address']){
                $billing_street = $invoice['main_address']['address_line_1'];
                $billing_city = $invoice['main_address']['city'];
                $billing_country = $invoice['main_address']['region'];
                $billing_zip = $invoice['main_address']['postal_code'];
            }

            $invoice_data['billing_street'] = $billing_street;
            $invoice_data['billing_city'] = $billing_city;
            $invoice_data['billing_state'] = '';
            $invoice_data['billing_zip'] = $billing_zip;
            $invoice_data['billing_country'] = $billing_country;

            $shipping_street = '';
            $shipping_city = '';
            $shipping_state = '';
            $shipping_zip = '';
            $shipping_country = '';
            if($invoice['delivery_address']){
                $shipping_street = $invoice['delivery_address']['address_line_1'];
                $shipping_city = $invoice['delivery_address']['city'];
                $shipping_country = $invoice['delivery_address']['region'];
                $shipping_zip = $invoice['delivery_address']['postal_code'];
            }

            $invoice_data['shipping_street'] = $shipping_street;
            $invoice_data['shipping_city'] = $shipping_city;
            $invoice_data['shipping_state'] = $shipping_state;
            $invoice_data['shipping_zip'] = $shipping_zip;
            $invoice_data['shipping_country'] = $shipping_country;
            $invoice_data['total']               = $invoice['total_amount'];

            $newitems = [];

            $discount_total = 0;
            $subtotal = 0;
            foreach ($invoice['invoice_lines'] as $key => $value) {
                $tax_arr = '';

                array_push($newitems, array(
                    'order' => $key, 
                    'description' => $value['displayed_as'] ?? $value['description'], 
                    'long_description' => '', 
                    'qty' => $value['quantity'] ?? 1, 
                    'unit' => '', 
                    'rate' => $value['unit_price'], 
                    'taxname' => $tax_arr));
                $subtotal += ($value['quantity'] * $value['unit_price']);

                if($value['discount_amount'] > 0){
                    $discount_total += $value['discount_amount'];
                }
            }

            
            if($invoice['shipping_total_amount'] > 0){
                $invoice_data['adjustment']  = $invoice['shipping_total_amount'];
            }

            if($discount_total > 0){
                $invoice_data['discount_type']               = 'before_tax';
                $invoice_data['discount_total']               = $discount_total;
            }

            $invoice_data['subtotal']            = $subtotal;

            $invoice_data['newitems'] = $newitems;
            
            if($check_connect_id != 0 && isset($invoice_arr[$check_connect_id])){
                $this->delete_invoice_item($check_connect_id);
                $this->invoices_model->update($invoice_data, $check_connect_id);
                $invoice_id = $check_connect_id;
            }else{
                if($check_connect_id != 0){
                    $this->delete_integration_log($check_connect_id, 'invoice', 'sage_accounting');
                }

                $__number        = get_option('next_invoice_number');
                $_invoice_number = str_pad($__number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
                $invoice_data['number']              = $_invoice_number;

                $invoice_id = $this->invoices_model->add($invoice_data);
            }

            $sync_status = 0;
            if($invoice_id){
            $sync_status = 1;
                if(!isset($invoice_arr[$check_connect_id])){
                    $this->db->insert(db_prefix().'acc_integration_logs', [
                        'rel_id' => $invoice_id,
                        'rel_type' => 'invoice',
                        'software' => 'sage_accounting',
                        'connect_id' => $invoice['id'],
                        'date_updated' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            $this->db->insert(db_prefix().'acc_integration_sync_logs', [
                        'rel_id' => $invoice_id,
                        'rel_type' => 'invoice',
                        'software' => 'sage_accounting',
                        'type' => 'sync_down',
                        'status' => $sync_status,
                        'connect_id' => $invoice['id'],
                        'datecreated' => date('Y-m-d H:i:s'),
                    ]);
        }
    }

    public function get_sage_accounting_payment(){
        $this->load->model('payments_model');
        $payment_list = $this->db->get(db_prefix() . 'invoicepaymentrecords')->result_array();
     
        $payment_arr = [];
        foreach ($payment_list as $payment) {
            $payment_arr[$payment['id']] = $payment;
        }

        $header = acc_get_sage_accounting_header();
        $api_domain = acc_get_sage_accounting_api_domain();

        $continue = true;
        $page = 1;

        $list_results = [];
        while ($continue) {
            $continue = false;
            $url = $api_domain.'/contact_payments?attributes=all&items_per_page=200&page='.$page;
            $entities = $this->callAPI($url, [], $header, 'GET');
            $page++;
            if (isset($entities['$items'])) {
                $list_results = array_merge($list_results, $entities['$items']);
            }

            if(isset($entities['$next']) && $entities['next'] != NULL){
                $continue = true;
            }
        }

        $this->load->model('payment_modes_model');

        $payment_modes = $this->payment_modes_model->get();

        $fisrt_payment_mode = 0;
        if (isset($payment_modes[0])) {
            $fisrt_payment_mode = $payment_modes[0]['id'];
        }

        foreach ($list_results as $payment) {
            $check_connect_id = $this->check_connect_id($payment['id'], 'payment', 'sage_accounting');

            if($payment['transaction_type']['id'] != 'CUSTOMER_RECEIPT'){
                continue;
            }
           
            $invoice_id = 0;
            $amount = round($payment['total_amount'], 2);
            foreach ($payment['allocated_artefacts'] as $key => $value) {
                $invoice_id = $this->check_connect_id($value['artefact']['id'], 'invoice', 'sage_accounting');
                $amount = round($value['amount'], 2);
            }

            if($invoice_id == 0){
                continue;
            }

            $payment_data = [];

            $payment_data['invoiceid'] = $invoice_id;
            $payment_data['amount'] = $amount;
            $payment_data['date'] = $payment['date'];
            $payment_data['transactionid'] = '';
            $payment_data['note'] = '';
            
            if($check_connect_id != 0 && isset($payment_arr[$check_connect_id])){
                $this->payments_model->update($payment_data, $check_connect_id);
                $payment_id = $check_connect_id;
            }else{
                if($check_connect_id != 0){
                    $this->delete_integration_log($check_connect_id, 'payment', 'sage_accounting');
                }

                $payment_data['do_not_send_email_template'] = 'on';
                $payment_data['do_not_redirect'] = 'on';
                $payment_data["paymentmode"] = $fisrt_payment_mode;
                $payment_id = $this->payments_model->add($payment_data);
            }

            $sync_status = 0;
            if($payment_id){
            $sync_status = 1;
                if(!isset($payment_arr[$check_connect_id])){
                    $this->db->insert(db_prefix().'acc_integration_logs', [
                        'rel_id' => $payment_id,
                        'rel_type' => 'payment',
                        'software' => 'sage_accounting',
                        'connect_id' => $payment['id'],
                        'date_updated' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            $this->db->insert(db_prefix().'acc_integration_sync_logs', [
                        'rel_id' => $payment_id,
                        'rel_type' => 'payment',
                        'software' => 'sage_accounting',
                        'type' => 'sync_down',
                        'status' => $sync_status,
                        'connect_id' => $payment['id'],
                        'datecreated' => date('Y-m-d H:i:s'),
                    ]);
        }
    }

    public function get_sage_accounting_expense(){
        $this->load->model('expenses_model');
        $expense_list = $this->expenses_model->get();
     
        $expense_arr = [];
        foreach ($expense_list as $expense) {
            $expense_arr[$expense['id']] = $expense;
        }

        $header = acc_get_sage_accounting_header();
        $api_domain = acc_get_sage_accounting_api_domain();

        $continue = true;
        $page = 1;

        $list_results = [];
        while ($continue) {
            $continue = false;
            $url = $api_domain.'/purchase_quick_entries?attributes=all&items_per_page=200&page='.$page;
            $entities = $this->callAPI($url, [], $header, 'GET');
            $page++;
            if (isset($entities['$items'])) {
                $list_results = array_merge($list_results, $entities['$items']);
            }

            if(isset($entities['$next']) && $entities['next'] != NULL){
                $continue = true;
            }
        }

        $this->load->model('payment_modes_model');
        $payment_modes = $this->payment_modes_model->get();

        $fisrt_payment_mode = 0;
        if (isset($payment_modes[0])) {
            $fisrt_payment_mode = $payment_modes[0]['id'];
        }

        foreach ($list_results as $expense) {
            $check_connect_id = $this->check_connect_id($expense['id'], 'expense', 'sage_accounting');
            

            $url = $api_domain.'/books/v3/expenses/'.$expense['id'];

            $expense_data = [];

            $expense_data['vendor'] = '';
            $expense_data['expense_name'] = '';
            $expense_data['note'] = '';
            $expense_data['category'] = $this->init_expense_category('Sage Accounting Expenses');
            $expense_data['date'] = $expense['date'];
            $expense_data['amount'] = $expense['total_amount'];
            $expense_data['clientid'] = '';
            $expense_data['project_id'] = '';

            $currency = get_currency($expense['currency']['id']);
            $expense_data['currency'] = '1';

            $expense_data['tax'] = '';
            $expense_data['tax2'] = '';
            $expense_data['paymentmode'] = '';
            $expense_data['reference_no'] = '';
            $expense_data['repeat_every'] = '';
            $expense_data['repeat_every_custom'] = '1';
            $expense_data['repeat_type_custom'] = 'day';

            if($check_connect_id != 0 && isset($expense_arr[$check_connect_id])){
                $this->expenses_model->update($expense_data, $check_connect_id);
                $expense_id = $check_connect_id;
            }else{
                if($check_connect_id != 0){
                    $this->delete_integration_log($check_connect_id, 'expense', 'sage_accounting');
                }

                $expense_id = $this->expenses_model->add($expense_data);
            }

            $sync_status = 0;
            if($expense_id){
            $sync_status = 1;
                if(!isset($expense_arr[$check_connect_id])){
                    $this->db->insert(db_prefix().'acc_integration_logs', [
                        'rel_id' => $expense_id,
                        'rel_type' => 'expense',
                        'software' => 'sage_accounting',
                        'connect_id' => $expense['id'],
                        'date_updated' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            $this->db->insert(db_prefix().'acc_integration_sync_logs', [
                        'rel_id' => $expense_id,
                        'rel_type' => 'expense',
                        'software' => 'sage_accounting',
                        'type' => 'sync_down',
                        'status' => $sync_status,
                        'connect_id' => $expense['id'],
                        'datecreated' => date('Y-m-d H:i:s'),
                    ]);
        }
    }

    public function init_expense_category($name)
    {
        $this->db->where('name', $name);

        $expenses_categorie = $this->db->get(db_prefix() . 'expenses_categories')->row();

        if($expenses_categorie){
            return $expenses_categorie->id;
        }
        
        $this->load->model('expenses_model');
        $id = $this->expenses_model->add_category(['name' => $name, 'description' => '']);

        return $id;
    }

    public function executeRequest($url, $parameters = array(), $http_header = '', $http_method = '')
    {

      $curl_options = array();

      switch($http_method){
            case 'GET':
              $curl_options[CURLOPT_HTTPGET] = 'true';
              if (is_array($parameters) && count($parameters) > 0) {
                $url .= '?' . http_build_query($parameters);
              } elseif ($parameters) {
                $url .= '?' . $parameters;
              }
              break;
            case 'POST':
              $curl_options[CURLOPT_POST] = '1';
              if(is_array($parameters) && count($parameters) > 0){
                $body = http_build_query($parameters);
                $curl_options[CURLOPT_POSTFIELDS] = $body;
              }
              break;
            default:
              break;
      }
      /**
      * An array of HTTP header fields to set, in the format array('Content-type: text/plain', 'Content-length: 100')
      */
      if(is_array($http_header)){
            $header = array();
            foreach($http_header as $key => $value) {
                $header[] = "$key: $value";
            }
            $curl_options[CURLOPT_HTTPHEADER] = $header;
      }

      $curl_options[CURLOPT_URL] = $url;
      $ch = curl_init();

      //debug_backtrace
      //curl_setopt($ch, CURLOPT_VERBOSE, true);
      //$verbose = fopen('php://temp', 'w+');
      //curl_setopt($ch, CURLOPT_STDERR, $verbose);


      curl_setopt_array($ch, $curl_options);

      //curl_setopt($ch, CURLINFO_HEADER_OUT, true);
      //Don't display, save it on result
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      //Execute the Curl Request
      $result = curl_exec($ch);

      $headerSent = curl_getinfo($ch, CURLINFO_HEADER_OUT );

      $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);


      $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
       if ($curl_error = curl_error($ch)) {
           throw new Exception($curl_error);
       } else {
           $json_decode = json_decode($result, true);
       }
       curl_close($ch);

       return $json_decode;
    }

    public function delete_integration_error_log($rel_id, $rel_type, $software){
        $this->db->where('rel_id', $rel_id);
        $this->db->where('rel_type', $rel_type);
        $this->db->delete(db_prefix().'acc_integration_error_logs');

        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    public function check_connect_id($connect_id, $rel_type, $software = 'quickbook'){
        $this->db->where('connect_id', $connect_id);
        $this->db->where('rel_type', $rel_type);
        $this->db->where('software', $software);
        $log = $this->db->get(db_prefix().'acc_integration_logs')->row();

        if($log){
            return $log->rel_id;
        }

        return 0;
    }


    public function callAPI($url, $params, $header, $method = 'POST'){
            $data_string = json_encode($params);

            $curl = curl_init($url);
            
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

            if($method == 'POST' || $method == 'PUT'){
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
            }

            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curl, CURLOPT_TIMEOUT, 120);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 120);
            curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
            
            $result = curl_exec($curl);

            $result = json_decode($result, true);

            return $result;
    }

    /**
     * [get_invoice_item_tax description]
     * @param  [type] $item
     * @return [type]      
     */
    public function get_invoice_item_tax($item, $invoice){
        $data_return = [];
        $data_return['tax_id'] = [];
        $data_return['tax_amount'] = [];
        $data_return['tax_rate'] = [];
        $item_total = $item['rate'] * $item['qty'];
        $this->db->where('itemid', $item['id']);
        $item_tax = $this->db->get(db_prefix().'item_tax')->result_array();

        foreach($item_tax as $tax){
            $this->db->where('taxrate', $tax['taxrate']);
            $this->db->where('name', $tax['taxname']);
            $_tax = $this->db->get(db_prefix().'taxes')->row();
            if($_tax){
                $data_return['tax_rate'][] = $tax['taxrate'];

                $data_return['tax_id'][] = $_tax->id;
                if($invoice['discount_type'] == 'before_tax'){
                    $total_tax = $item_total * $tax['taxrate'] / 100;
                    $t = ($invoice['discount_total'] / $invoice['subtotal']) * 100;

                    $data_return['tax_amount'][] = ($total_tax - $total_tax*$t/100);
                }else{
                    $data_return['tax_amount'][] = $item_total * $tax['taxrate'] / 100;
                }

            }
        }

        return $data_return;
    }

    public function delete_integration_log($rel_id, $rel_type, $software){
        $this->db->where('rel_id', $rel_id);
        $this->db->where('rel_type', $rel_type);
        $this->db->where('software', $software);
        $this->db->delete(db_prefix().'acc_integration_logs');

        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    public function get_connect_id($rel_id, $rel_type, $software = 'quickbook'){
        $this->db->where('rel_id', $rel_id);
        $this->db->where('rel_type', $rel_type);
        $this->db->where('software', $software);
        $log = $this->db->get(db_prefix().'acc_integration_logs')->row();

        if($log){
            return $log->connect_id;
        }

        return '';
    }

    public function delete_invoice_item($id){
        $items = get_items_by_type('invoice', $id);

        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'invoice');
        $this->db->delete(db_prefix() . 'itemable');

        foreach ($items as $item) {
            $this->db->where('item_id', $item['id']);
            $this->db->delete(db_prefix() . 'related_items');
        }
        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'invoice');
        $this->db->delete(db_prefix() . 'item_tax');

        return true;
    }
}