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

        if (isset($data['settings']['acc_integration_sage_accounting_password'])) {
            $data['settings']['acc_integration_sage_accounting_password'] = $this->encryption->encrypt($data['settings']['acc_integration_sage_accounting_password']);
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

    /**
     * [init_sage_accounting_config description]
     * @return [type] [description]
     */
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

    /**
     * [create_sage_accounting_customer description]
     * @param  string $customer_id [description]
     * @return [type]              [description]
     */
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

                if($customer_id == ''){
                    continue;
                }

                $url = $api_domain.'/contacts/'.$client['connect_id'];
                $result = $this->callAPI($url, $customerObj, $header, 'PUT');
            }else{

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

                if($client['connect_id'] == ''){
                    $this->db->insert(db_prefix().'acc_integration_logs', [
                        'rel_id' => $client['userid'],
                        'rel_type' => 'customer',
                        'software' => 'sage_accounting',
                        'connect_id' => $result['id'],
                        'date_updated' => date('Y-m-d H:i:s'),
                    ]);
                }else{
                    $this->db->where('rel_id', $client['userid']);
                    $this->db->where('rel_type', 'customer');
                    $this->db->where('software', 'sage_accounting');
                    $this->db->update(db_prefix().'acc_integration_logs', [
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

    /**
     * [create_sage_accounting_invoice description]
     * @param  string $invoice_id [description]
     * @return [type]             [description]
     */
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

                if($invoice_id == ''){
                    continue;
                }

                $url = $api_domain.'/sales_invoices/'.$invoice['connect_id'];
                $result = $this->callAPI($url, $invoiceObj, $header, 'PUT');
            }else{
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

                if($invoice['connect_id'] == ''){
                    $this->db->insert(db_prefix().'acc_integration_logs', [
                        'rel_id' => $invoice['id'],
                        'rel_type' => 'invoice',
                        'software' => 'sage_accounting',
                        'connect_id' => $result['id'],
                        'date_updated' => date('Y-m-d H:i:s'),
                    ]);
                }else{
                    $this->db->where('rel_id', $invoice['id']);
                    $this->db->where('rel_type', 'invoice');
                    $this->db->where('software', 'sage_accounting');
                    $this->db->update(db_prefix().'acc_integration_logs', [
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

    /**
     * [create_sage_accounting_payment description]
     * @param  string $payment_id [description]
     * @return [type]             [description]
     */
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

                if($payment['connect_id'] == ''){
                    $this->db->insert(db_prefix().'acc_integration_logs', [
                        'rel_id' => $payment['id'],
                        'rel_type' => 'payment',
                        'software' => 'sage_accounting',
                        'connect_id' => $result['id'],
                        'date_updated' => date('Y-m-d H:i:s'),
                    ]);
                }else{
                    $this->db->where('rel_id', $payment['id']);
                    $this->db->where('rel_type', 'payment');
                    $this->db->where('software', 'sage_accounting');
                    $this->db->update(db_prefix().'acc_integration_logs', [
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

    /**
     * [create_sage_accounting_expense description]
     * @param  string $expense_id [description]
     * @return [type]             [description]
     */
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

                if($expense['connect_id'] == ''){
                    $this->db->insert(db_prefix().'acc_integration_logs', [
                        'rel_id' => $expense['id'],
                        'rel_type' => 'expense',
                        'software' => 'sage_accounting',
                        'connect_id' => $result['id'],
                        'date_updated' => date('Y-m-d H:i:s'),
                    ]);
                }else{
                    $this->db->where('rel_id', $expense['id']);
                    $this->db->where('rel_type', 'expense');
                    $this->db->where('software', 'sage_accounting');
                    $this->db->update(db_prefix().'acc_integration_logs', [
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

    /**
     * [get_sage_accounting_bank_account description]
     * @return [type] [description]
     */
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

    /**
     * [get_sage_accounting_income_account description]
     * @return [type] [description]
     */
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

    /**
     * [get_sage_accounting_expense_account description]
     * @return [type] [description]
     */
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

    /**
     * [get_sage_accounting_vendor_default description]
     * @return [type] [description]
     */
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

    /**
     * [get_sage_accounting_customer description]
     * @return [type] [description]
     */
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

    /**
     * [get_sage_accounting_invoice description]
     * @return [type] [description]
     */
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
                $this->get_sage_accounting_customer();
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

    /**
     * [get_sage_accounting_payment description]
     * @return [type] [description]
     */
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

    /**
     * [get_sage_accounting_expense description]
     * @return [type] [description]
     */
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

    /**
     * [init_expense_category description]
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
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

    /**
     * [executeRequest description]
     * @param  [type] $url         [description]
     * @param  array  $parameters  [description]
     * @param  string $http_header [description]
     * @param  string $http_method [description]
     * @return [type]              [description]
     */
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

      curl_setopt_array($ch, $curl_options);

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

    /**
     * [delete_integration_error_log description]
     * @param  [type] $rel_id          [description]
     * @param  [type] $rel_type        [description]
     * @param  [type] $software        [description]
     * @param  string $organization_id [description]
     * @return [type]                  [description]
     */
    public function delete_integration_error_log($rel_id, $rel_type, $software, $organization_id = ''){
        if($organization_id != ''){
            $this->db->where('organization_id', $organization_id);
        }
        $this->db->where('rel_id', $rel_id);
        $this->db->where('rel_type', $rel_type);
        $this->db->delete(db_prefix().'acc_integration_error_logs');

        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * [check_connect_id description]
     * @param  [type] $connect_id      [description]
     * @param  [type] $rel_type        [description]
     * @param  string $software        [description]
     * @param  string $organization_id [description]
     * @return [type]                  [description]
     */
    public function check_connect_id($connect_id, $rel_type, $software = 'quickbook', $organization_id = ''){
        if($organization_id != ''){
            $this->db->where('organization_id', $organization_id);
        }
        $this->db->where('connect_id', $connect_id);
        $this->db->where('rel_type', $rel_type);
        $this->db->where('software', $software);
        $log = $this->db->get(db_prefix().'acc_integration_logs')->row();

        if($log){
            return $log->rel_id;
        }

        return 0;
    }

    /**
     * [callAPI description]
     * @param  [type] $url    [description]
     * @param  [type] $params [description]
     * @param  [type] $header [description]
     * @param  string $method [description]
     * @return [type]         [description]
     */
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
            $result_arr = json_decode($result, true);
            if($result_arr == ''){
                return $result;
            }

            return $result_arr;
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

    /**
     * [delete_integration_log description]
     * @param  [type] $rel_id          [description]
     * @param  [type] $rel_type        [description]
     * @param  [type] $software        [description]
     * @param  string $organization_id [description]
     * @return [type]                  [description]
     */
    public function delete_integration_log($rel_id, $rel_type, $software, $organization_id = ''){
        if($organization_id != ''){
            $this->db->where('organization_id', $organization_id);
        }
        $this->db->where('rel_id', $rel_id);
        $this->db->where('rel_type', $rel_type);
        $this->db->where('software', $software);
        $this->db->delete(db_prefix().'acc_integration_logs');

        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * [get_connect_id description]
     * @param  [type] $rel_id          [description]
     * @param  [type] $rel_type        [description]
     * @param  string $software        [description]
     * @param  string $organization_id [description]
     * @return [type]                  [description]
     */
    public function get_connect_id($rel_id, $rel_type, $software = 'quickbook', $organization_id = ''){
        if($organization_id != ''){
            $this->db->where('organization_id', $organization_id);
        }
        $this->db->where('rel_id', $rel_id);
        $this->db->where('rel_type', $rel_type);
        $this->db->where('software', $software);
        $log = $this->db->get(db_prefix().'acc_integration_logs')->row();

        if($log){
            return $log->connect_id;
        }

        return '';
    }

    /**
     * [delete_invoice_item description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
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

    /**
     * [create_sage_accounting_customer_sa description]
     * @param  string $organization_id [description]
     * @param  string $customer_id     [description]
     * @return [type]                  [description]
     */
    public function create_sage_accounting_customer_sa($organization_id = '', $customer_id = ''){
        $this->db->select('*, '.db_prefix() . 'clients.userid as userid');
        if($customer_id != ''){
            $this->db->where(db_prefix().'clients.userid', $customer_id);
        }

        $this->db->join(db_prefix() . 'contacts', db_prefix() . 'contacts.userid=' . db_prefix() . 'clients.userid AND '.db_prefix() . 'contacts.is_primary = "1"', 'left');
        $this->db->join(db_prefix() . 'acc_integration_logs', db_prefix() . 'acc_integration_logs.rel_id=' . db_prefix() . 'clients.userid AND '.db_prefix() . 'acc_integration_logs.rel_type = "customer" AND software = "sage_accounting" AND ' . db_prefix() . 'acc_integration_logs.organization_id = "'.$organization_id.'"', 'left');

        $clients = $this->db->get(db_prefix().'clients')->result_array();

        $header = acc_get_sage_accounting_header();
        $api_domain = acc_get_sage_accounting_api_domain();
        $api_key = get_option('acc_integration_sage_accounting_api_key');

        $customer_data = [];
        $continue = true;
        $page = 1;
        $top = 100;

        $list_results = [];
        while ($continue) {
            $continue = false;
            $skip = ($page - 1) * $top;
            $url = $api_domain.'/customer/get?apikey='.$api_key.'&CompanyId='.$organization_id.'&$skip='.$skip.'&$top='.$top;
            $entities = $this->callAPI($url, [], $header, 'GET');

            $page++;
            if (isset($entities['Results'])) {
                foreach ($entities['Results'] as $customer) {
                    $customer_data[$customer['ID']] = $customer;
                }
            }

            if(isset($entities['ReturnedResults']) && $entities['ReturnedResults'] > 0){
                $continue = true;
            }
        }

        foreach ($clients as $client) {
            if($client['connect_id'] != ''){
                continue;
            }
            $PostalAddress01 = $client['billing_street'].' '.$client['billing_city'].' '.$client['billing_country'].' '.$client['billing_zip'];
            $DeliveryAddress01 = $client['shipping_street'].' '.$client['shipping_city'].' '.$client['shipping_country'].' '.$client['shipping_zip'];

            $customerObj = [
                "Name"=>  $client['company'],
                "WebAddress"=>  $client['website'],
                "Telephone" => $client['phonenumber'],
                "ContactName" => $client['firstname'].' '.$client['lastname'],
                "Email" => $client['email'],
                "Active" => true,
                "PostalAddress01"=>  $PostalAddress01,
                "DeliveryAddress01"=>  $DeliveryAddress01,
            ];

            $client_groups = $this->client_groups_model->get_customer_groups($client['userid']);
            if($client_groups){
                foreach ($client_groups as $key => $client_group) {
                    $group_connect_id = $this->get_connect_id($client_group['groupid'], 'customer_group', 'sage_accounting', $organization_id);

                    if($group_connect_id == ''){
                        $this->create_sage_accounting_customer_group_sa($organization_id, $client_group['groupid']);
                        $group_connect_id = $this->get_connect_id($client_group['groupid'], 'customer_group', 'sage_accounting', $organization_id);
                    }
                }

                if($group_connect_id != ''){
                    $customerObj['Category'] = ['ID' => $group_connect_id];
                }
            }

            if($client['connect_id'] != '' && isset($customer_data[$client['connect_id']])){
                if($customer_id == ''){
                    continue;
                }

                $customerObj['ID'] = $client['connect_id'];
            }

            $url = $api_domain.'/Customer/Save?apikey='.$api_key.'&CompanyId='.$organization_id;
            $result = $this->callAPI($url, $customerObj, $header, 'POST');

            $this->delete_integration_error_log($client['userid'], 'customer', 'sage_accounting', $organization_id);
            if (!isset($result['ID'])) {

                $message = '';

                if (isset($result['Message'])) {
                    $message = $result['Message'];
                }elseif (is_string($result)) {
                    $message = $result;
                }

                $this->db->insert(db_prefix().'acc_integration_error_logs', [
                    'organization_id' => $organization_id,
                    'rel_id' => $client['userid'],
                    'rel_type' => 'customer',
                    'software' => 'sage_accounting',
                    'error_detail' => $message,
                    'date_updated' => date('Y-m-d H:i:s'),
                ]);

                $this->db->insert(db_prefix().'acc_integration_sync_logs', [
                    'organization_id' => $organization_id,
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

                if($client['connect_id'] == ''){
                    $this->db->insert(db_prefix().'acc_integration_logs', [
                        'organization_id' => $organization_id,
                        'rel_id' => $client['userid'],
                        'rel_type' => 'customer',
                        'software' => 'sage_accounting',
                        'connect_id' => $result['ID'],
                        'date_updated' => date('Y-m-d H:i:s'),
                    ]);
                }else{
                    $this->db->where('organization_id', $organization_id);
                    $this->db->where('rel_id', $client['userid']);
                    $this->db->where('rel_type', 'customer');
                    $this->db->where('software', 'sage_accounting');
                    $this->db->update(db_prefix().'acc_integration_logs', [
                        'connect_id' => $result['ID'],
                        'date_updated' => date('Y-m-d H:i:s'),
                    ]);
                }

                $this->db->insert(db_prefix().'acc_integration_sync_logs', [
                    'organization_id' => $organization_id,
                    'rel_id' => $client['userid'],
                    'rel_type' => 'customer',
                    'software' => 'sage_accounting',
                    'type' => 'sync_up',
                    'status' => 1,
                    'connect_id' => $result['ID'],
                    'datecreated' => date('Y-m-d H:i:s'),
                ]);

                if($customer_id != ''){
                    return true;
                }
            }
        }
    }

    /**
     * [test_connect description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function test_connect($data){
        $api_domain = acc_get_sage_accounting_api_domain('south_african');
        $authorization = base64_encode($data['username'].':'.$data['password']);

        $header = array(
            'Content-Type: application/json',
            'Authorization: Basic '. $authorization);

        $url = $api_domain.'/Company/get?apikey='.$data['api_key'];

        $result = $this->callAPI($url, [], $header, 'GET');

        if(isset($result['Results'])){
            return true;
        }

        return false;
    }

    /**
     * [get_sage_accounting_organizations description]
     * @return [type] [description]
     */
    public function get_sage_accounting_organizations(){
        $header = acc_get_sage_accounting_header();
        $api_domain = acc_get_sage_accounting_api_domain();
        $api_key = get_option('acc_integration_sage_accounting_api_key');

        $url = $api_domain.'/company/get?apikey='.$api_key;
        $entities = $this->callAPI($url, [], $header, 'GET');

        if (isset($entities['Results'])) {
            return $entities['Results'];
        } else {
            return [];
        }
    }

    /**
     * [create_sage_accounting_customer_group_sa description]
     * @param  string $organization_id [description]
     * @param  string $group_id        [description]
     * @return [type]                  [description]
     */
    public function create_sage_accounting_customer_group_sa($organization_id = '', $group_id = ''){
        $this->db->select('*, ' . db_prefix() . 'customers_groups.id as id');
        if($group_id != ''){
            $this->db->where(db_prefix().'customers_groups.id', $group_id);
        }
        $this->db->join(db_prefix() . 'acc_integration_logs', db_prefix() . 'acc_integration_logs.rel_id=' . db_prefix() . 'customers_groups.id AND '.db_prefix() . 'acc_integration_logs.rel_type = "customer_group" AND software = "sage_accounting" AND ' . db_prefix() . 'acc_integration_logs.organization_id = "'.$organization_id.'"', 'left');

        $groups = $this->db->get(db_prefix().'customers_groups')->result_array();

        $header = acc_get_sage_accounting_header();
        $api_domain = acc_get_sage_accounting_api_domain();
        $api_key = get_option('acc_integration_sage_accounting_api_key');

        $group_data = [];
        $continue = true;
        $page = 1;
        $top = 100;

        $list_results = [];
        while ($continue) {
            $continue = false;
            $skip = ($page - 1) * $top;

            $url = $api_domain.'/CustomerCategory/Get?apikey='.$api_key.'&CompanyId='.$organization_id.'&$skip='.$skip.'&$top='.$top;
            $entities = $this->callAPI($url, [], $header, 'GET');
            $page++;
            if (isset($entities['Results'])) {
                foreach ($entities['Results'] as $group) {
                    $group_data[$group['ID']] = $group;
                }
            }

            if(isset($entities['ReturnedResults']) && $entities['ReturnedResults'] > 0){
                $continue = true;
            }
        }

        foreach ($groups as $group) {
            if($group['connect_id'] != ''){
                continue;
            }

            $groupObj = [
                "Description" => $group['name']
            ];

            if($group['connect_id'] != '' && isset($group_data[$group['connect_id']])){
                $groupObj['ID'] = $group['connect_id'];
            }

            $url = $api_domain.'/CustomerCategory/save?apikey='.$api_key.'&CompanyId='.$organization_id;
            $result = $this->callAPI($url, $groupObj, $header, 'POST');

            $this->delete_integration_error_log($group['id'], 'customer_group', 'sage_accounting', $organization_id);
            if (!isset($result['ID'])) {
                $message = '';

                if (isset($result['Message'])) {
                    $message = $result['Message'];
                }elseif (is_string($result)) {
                    $message = $result;
                }

                $this->db->insert(db_prefix().'acc_integration_error_logs', [
                    'organization_id' => $organization_id,
                    'rel_id' => $group['id'],
                    'rel_type' => 'customer_group',
                    'software' => 'sage_accounting',
                    'error_detail' => $message,
                    'date_updated' => date('Y-m-d H:i:s'),
                ]);  

                $this->db->insert(db_prefix().'acc_integration_sync_logs', [
                    'organization_id' => $organization_id,
                    'rel_id' => $group['id'],
                    'rel_type' => 'customer_group',
                    'software' => 'sage_accounting',
                    'type' => 'sync_up',
                    'status' => 0,
                    'connect_id' => '',
                    'datecreated' => date('Y-m-d H:i:s'),
                ]);

                if($group_id != ''){
                    return $message;
                }
            }else{

                if($group['connect_id'] == ''){
                    $this->db->insert(db_prefix().'acc_integration_logs', [
                        'organization_id' => $organization_id,
                        'rel_id' => $group['id'],
                        'rel_type' => 'customer_group',
                        'software' => 'sage_accounting',
                        'connect_id' => $result['ID'],
                        'date_updated' => date('Y-m-d H:i:s'),
                    ]);
                }else{
                    $this->db->where('organization_id', $organization_id);
                    $this->db->where('rel_id', $group['id']);
                    $this->db->where('rel_type', 'customer_group');
                    $this->db->where('software', 'sage_accounting');
                    $this->db->update(db_prefix().'acc_integration_logs', [
                        'connect_id' => $result['ID'],
                        'date_updated' => date('Y-m-d H:i:s'),
                    ]);
                }

                $this->db->insert(db_prefix().'acc_integration_sync_logs', [
                    'organization_id' => $organization_id,
                    'rel_id' => $group['id'],
                    'rel_type' => 'customer_group',
                    'software' => 'sage_accounting',
                    'type' => 'sync_up',
                    'status' => 1,
                    'connect_id' => $result['ID'],
                    'datecreated' => date('Y-m-d H:i:s'),
                ]);

                if($group_id != ''){
                    return true;
                }
            }
        }
    }

    /**
     * [create_sage_accounting_invoice_sa description]
     * @param  string $organization_id [description]
     * @param  string $invoice_id      [description]
     * @return [type]                  [description]
     */
    public function create_sage_accounting_invoice_sa($organization_id = '', $invoice_id = ''){
        $this->db->select('*, ' . db_prefix() . 'currencies.id as currencyid, ' . db_prefix() . 'invoices.id as id, ' . db_prefix() . 'currencies.name as currency_name');
        if($invoice_id != ''){
            $this->db->where(db_prefix().'invoices.id', $invoice_id);
        }
        $this->db->join(db_prefix() . 'currencies', '' . db_prefix() . 'currencies.id = ' . db_prefix() . 'invoices.currency', 'left');
        $this->db->join(db_prefix() . 'acc_integration_logs', db_prefix() . 'acc_integration_logs.rel_id=' . db_prefix() . 'invoices.id AND '.db_prefix() . 'acc_integration_logs.rel_type = "invoice" AND software = "sage_accounting" AND ' . db_prefix() . 'acc_integration_logs.organization_id = "'.$organization_id.'"', 'left');

        $invoices = $this->db->get(db_prefix().'invoices')->result_array();

        $header = acc_get_sage_accounting_header();
        $api_domain = acc_get_sage_accounting_api_domain();
        $api_key = get_option('acc_integration_sage_accounting_api_key');

        $invoice_data = [];
        $continue = true;
        $page = 1;
        $top = 100;

        $list_results = [];
        while ($continue) {
            $continue = false;
            $skip = ($page - 1) * $top;

            $url = $api_domain.'/TaxInvoice/Get?apikey='.$api_key.'&CompanyId='.$organization_id.'&$skip='.$skip.'&$top='.$top;
            $entities = $this->callAPI($url, [], $header, 'GET');

            $page++;
            if (isset($entities['Results'])) {
                foreach ($entities['Results'] as $invoice) {
                    $invoice_data[$invoice['ID']] = $invoice;
                }
            }

            if(isset($entities['ReturnedResults']) && $entities['ReturnedResults'] > 0){
                $continue = true;
            }
        }

        $insert_item = [];
        foreach ($invoices as $invoice) {
            if($invoice['connect_id'] != ''){
                continue;
            }
            $items  = get_items_by_type('invoice', $invoice['id']);
            $item_array = [];
            $total_tax = 0;

            foreach ($items as $item) {
                

                $_item = get_item_by_name($item['description']);
                $SelectionId = '';
                if($_item){
                    $item_id = $_item->id;
                    $item_connect_id = $this->get_connect_id($item_id, 'item', 'sage_accounting', $organization_id);
                    if($item_connect_id == ''){
                        $this->create_sage_accounting_item_sa($organization_id, $item_id);
                        $item_connect_id = $this->get_connect_id($item_id, 'item', 'sage_accounting', $organization_id);
                    }

                }else{
                    $item_code = $this->generate_commodity_code();

                    $this->db->insert(db_prefix() . 'items', ['commodity_code' => $item_code, 'description' => $item['description'], 'rate' => $item['rate']]);
                    $item_id = $this->db->insert_id();

                    $this->create_sage_accounting_item_sa($organization_id, $item_id);
                    $item_connect_id = $this->get_connect_id($item_id, 'item', 'sage_accounting', $organization_id);
                }

                $data_tax = $this->get_invoice_item_tax($item, $invoice);

                $tax_amount = 0;
                foreach ($data_tax['tax_amount'] as $k => $amount) {
                    $tax_amount += $amount;
                }

                $tax_rate = 0;
                foreach ($data_tax['tax_rate'] as $k => $rate) {
                    $tax_rate += $rate;
                }
                
                $item_amount = ($item['rate'] * $item['qty']);
                $tax_amount = ($tax_rate/100) * $item_amount;

                $item_array[] = [
                    "SelectionId" => $item_connect_id,
                    "TaxTypeId" => -1,
                    "UnitPriceExclusive" => (float)$item['rate'],
                    "Quantity" => $item['qty'],
                    'Description' => $item['description'],
                    'Exclusive' => $item_amount,
                    'Total' => $item_amount + $tax_amount,
                    'Tax' => $tax_amount,
                    'TaxPercentage' => $tax_rate,
                ];

                $total_tax += $tax_amount;
            }

            $customer_connect_id = $this->get_connect_id($invoice['clientid'], 'customer', 'sage_accounting', $organization_id);
            if($customer_connect_id == ''){
                $this->create_sage_accounting_customer_sa($organization_id, $invoice['clientid']);
                $customer_connect_id = $this->get_connect_id($invoice['clientid'], 'customer', 'sage_accounting', $organization_id);
            }

            $invoiceObj = [
                "CustomerId"=> $customer_connect_id,
                "Date" => $invoice['date'],
                "DueDate" => $invoice['duedate'] ?? $invoice['date'],
                "Message" => $invoice['clientnote'],
                "Reference" => format_invoice_number($invoice['id']),
                "Lines" => $item_array,
                "Exclusive" => (float)$invoice['subtotal'],
                "Total" => (float)$invoice['total'],
                "Tax" => (float)$total_tax,
            ];

            if($invoice['discount_total'] > 0){
                $invoiceObj['Discount'] = (float)$invoice['discount_total'];
            }

            if($invoice['discount_percent'] > 0){
                $invoiceObj['DiscountPercentage'] = (float)($invoice['discount_percent']/100);
            }

            if($invoice['adjustment'] != 0){
                
            }

            if($invoice['connect_id'] != '' && isset($invoice_data[$invoice['connect_id']])){
                if($invoice_id == ''){
                    continue;
                }

                $invoiceObj['ID'] = $invoice['connect_id'];
            }

            $url = $api_domain.'/TaxInvoice/save?apikey='.$api_key.'&CompanyId='.$organization_id;
            $result = $this->callAPI($url, $invoiceObj, $header, 'POST');

            $this->delete_integration_error_log($invoice['id'], 'invoice', 'sage_accounting', $organization_id);
            if (!isset($result['ID'])) {

                $message = '';

                if (isset($result['Message'])) {
                    $message = $result['Message'];
                }elseif (is_string($result)) {
                    $message = $result;
                }

                $this->db->insert(db_prefix().'acc_integration_error_logs', [
                    'organization_id' => $organization_id,
                    'rel_id' => $invoice['id'],
                    'rel_type' => 'invoice',
                    'software' => 'sage_accounting',
                    'error_detail' => $message,
                    'date_updated' => date('Y-m-d H:i:s'),
                ]);

                $this->db->insert(db_prefix().'acc_integration_sync_logs', [
                    'organization_id' => $organization_id,
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

                if($invoice['connect_id'] == ''){
                    $this->db->insert(db_prefix().'acc_integration_logs', [
                        'organization_id' => $organization_id,
                        'rel_id' => $invoice['id'],
                        'rel_type' => 'invoice',
                        'software' => 'sage_accounting',
                        'connect_id' => $result['ID'],
                        'date_updated' => date('Y-m-d H:i:s'),
                    ]);
                }else{
                    $this->db->where('organization_id', $organization_id);
                    $this->db->where('rel_id', $invoice['id']);
                    $this->db->where('rel_type', 'invoice');
                    $this->db->where('software', 'sage_accounting');
                    $this->db->update(db_prefix().'acc_integration_logs', [
                        'connect_id' => $result['ID'],
                        'date_updated' => date('Y-m-d H:i:s'),
                    ]);
                }

                $this->db->insert(db_prefix().'acc_integration_sync_logs', [
                    'organization_id' => $organization_id,
                    'rel_id' => $invoice['id'],
                    'rel_type' => 'invoice',
                    'software' => 'sage_accounting',
                    'type' => 'sync_up',
                    'status' => 1,
                    'connect_id' => $result['ID'],
                    'datecreated' => date('Y-m-d H:i:s'),
                ]);

                if($invoice_id != ''){
                    return true;
                }
            }
        }
    }

    /**
     * [create_sage_accounting_payment_sa description]
     * @param  string $organization_id [description]
     * @param  string $payment_id      [description]
     * @return [type]                  [description]
     */
    public function create_sage_accounting_payment_sa($organization_id = '', $payment_id = ''){
        $this->db->select('*, ' . db_prefix() . 'invoicepaymentrecords.id as id');
        if($payment_id != ''){
            $this->db->where(db_prefix().'invoicepaymentrecords.id', $payment_id);
        }
        $this->db->join(db_prefix() . 'acc_integration_logs', db_prefix() . 'acc_integration_logs.rel_id=' . db_prefix() . 'invoicepaymentrecords.id AND '.db_prefix() . 'acc_integration_logs.rel_type = "payment" AND software = "sage_accounting" AND ' . db_prefix() . 'acc_integration_logs.organization_id = "'.$organization_id.'"', 'left');

        $payments = $this->db->get(db_prefix().'invoicepaymentrecords')->result_array();

        $header = acc_get_sage_accounting_header();
        $api_domain = acc_get_sage_accounting_api_domain();
        $api_key = get_option('acc_integration_sage_accounting_api_key');
        $bank_account = $this->get_sage_accounting_bank_account_sa($organization_id);

        $payment_data = [];
        $continue = true;
        $page = 1;
        $top = 100;

        $list_results = [];
        while ($continue) {
            $continue = false;
            $skip = ($page - 1) * $top;

            $url = $api_domain.'/CustomerReceipt/Get?apikey='.$api_key.'&CompanyId='.$organization_id.'&includeBankAccountDetails=true&includeCustomerDetails=true&$skip='.$skip.'&$top='.$top;
            $entities = $this->callAPI($url, [], $header, 'GET');

            $page++;
            if (isset($entities['Results'])) {
                foreach ($entities['Results'] as $payment) {
                    $payment_data[$payment['ID']] = $payment;
                }
            }

            if(isset($entities['ReturnedResults']) && $entities['ReturnedResults'] > 0){
                $continue = true;
            }
        }

        foreach ($payments as $payment) {
            if($payment['connect_id'] != ''){
                continue;
            }
            $this->db->where('id', $payment['invoiceid']);
            $invoice = $this->db->get(db_prefix().'invoices')->row();

            $invoice_connect_id = $this->get_connect_id($payment['invoiceid'], 'invoice', 'sage_accounting', $organization_id);
            if($invoice_connect_id == ''){
                $this->create_sage_accounting_invoice_sa($organization_id, $payment['invoiceid']);
                $invoice_connect_id = $this->get_connect_id($payment['invoiceid'], 'invoice', 'sage_accounting', $organization_id);
            }

            $customer_connect_id = $this->get_connect_id($invoice->clientid, 'customer', 'sage_accounting', $organization_id);
            if($customer_connect_id == ''){
                $this->create_sage_accounting_customer_sa($organization_id, $invoice->clientid);
                $customer_connect_id = $this->get_connect_id($invoice->clientid, 'customer', 'sage_accounting', $organization_id);
            }

            $paymentObj = [
                "CustomerId" => $customer_connect_id,
                "Date" => $payment['date'],
                "PaymentMethod" => "1",
                "Reference" => format_invoice_number($invoice->id),
                "BankAccountId" => $bank_account['ID'],
                "Total" => $payment['amount'],
            ];

            if($payment['connect_id'] != '' && isset($payment_data[$payment['connect_id']])){
                if($payment_id == ''){
                    continue;
                }

                $paymentObj['ID'] = $payment['connect_id'];
            }

            $url = $api_domain.'/CustomerReceipt/Save?apikey='.$api_key.'&CompanyId='.$organization_id;
            $result = $this->callAPI($url, $paymentObj, $header, 'POST');

            $this->delete_integration_error_log($payment['id'], 'payment', 'sage_accounting', $organization_id);
            if (!isset($result['ID'])) {

                $message = '';

                if (isset($result['Message'])) {
                    $message = $result['Message'];
                }elseif (is_string($result)) {
                    $message = $result;
                }

                $this->db->insert(db_prefix().'acc_integration_error_logs', [
                    'organization_id' => $organization_id,
                    'rel_id' => $payment['id'],
                    'rel_type' => 'payment',
                    'software' => 'sage_accounting',
                    'error_detail' => $message,
                    'date_updated' => date('Y-m-d H:i:s'),
                ]);  

                $this->db->insert(db_prefix().'acc_integration_sync_logs', [
                    'organization_id' => $organization_id,
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

                if($payment['connect_id'] == ''){
                    $allocationObj = [
                        "SourceDocumentId" => $result['ID'],
                        "AllocatedToDocumentId" => $invoice_connect_id,
                        "DocumentHeaderId_Source" => $result['ID'],
                        "DocumentHeaderId_Allocation" => $invoice_connect_id,
                        "Total" => $payment['amount'],
                    ];

                    $url = $api_domain.'/Allocation/Save?apikey='.$api_key.'&CompanyId='.$organization_id;
                    
                    $allocationResult = $this->callAPI($url, $allocationObj, $header, 'POST');
                    if (!isset($allocationResult['ID'])) {
                        $message = '';

                        if (isset($allocationResult['Message'])) {
                            $message = $allocationResult['Message'];
                        }elseif (is_string($allocationResult)) {
                            $message = $allocationResult;
                        }

                        $this->db->insert(db_prefix().'acc_integration_error_logs', [
                            'organization_id' => $organization_id,
                            'rel_id' => $payment['id'],
                            'rel_type' => 'payment',
                            'software' => 'sage_accounting',
                            'error_detail' => $message,
                            'date_updated' => date('Y-m-d H:i:s'),
                        ]);  

                        $this->db->insert(db_prefix().'acc_integration_sync_logs', [
                            'organization_id' => $organization_id,
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
                        if($payment['connect_id'] == ''){
                            $this->db->insert(db_prefix().'acc_integration_logs', [
                                'organization_id' => $organization_id,
                                'rel_id' => $payment['id'],
                                'rel_type' => 'payment',
                                'software' => 'sage_accounting',
                                'connect_id' => $allocationResult['ID'],
                                'date_updated' => date('Y-m-d H:i:s'),
                            ]);
                        }else{
                            $this->db->where('organization_id', $organization_id);
                            $this->db->where('rel_id', $payment['id']);
                            $this->db->where('rel_type', 'payment');
                            $this->db->where('software', 'sage_accounting');
                            $this->db->update(db_prefix().'acc_integration_logs', [
                                'connect_id' => $allocationResult['ID'],
                                'date_updated' => date('Y-m-d H:i:s'),
                            ]);
                        }
                    }
                }

                $this->db->insert(db_prefix().'acc_integration_sync_logs', [
                    'organization_id' => $organization_id,
                    'rel_id' => $payment['id'],
                    'rel_type' => 'payment',
                    'software' => 'sage_accounting',
                    'type' => 'sync_up',
                    'status' => 1,
                    'connect_id' => $payment['connect_id'],
                    'datecreated' => date('Y-m-d H:i:s'),
                ]);

                if($payment_id != ''){
                    return true;
                }
            }
        }
    }

    /**
     * [create_sage_accounting_expense_sa description]
     * @param  string $organization_id [description]
     * @param  string $expense_id      [description]
     * @return [type]                  [description]
     */
    public function create_sage_accounting_expense_sa($organization_id = '', $expense_id = ''){
        $this->db->select('*, ' . db_prefix() . 'expenses.id as id');
        if($expense_id != ''){
            $this->db->where(db_prefix().'expenses.id', $expense_id);
        }
        $this->db->join(db_prefix() . 'acc_integration_logs', db_prefix() . 'acc_integration_logs.rel_id=' . db_prefix() . 'expenses.id AND '.db_prefix() . 'acc_integration_logs.rel_type = "expense" AND software = "sage_accounting" AND ' . db_prefix() . 'acc_integration_logs.organization_id = "'.$organization_id.'"', 'left');

        $expenses = $this->db->get(db_prefix().'expenses')->result_array();

        $header = acc_get_sage_accounting_header();
        $api_domain = acc_get_sage_accounting_api_domain();
        $api_key = get_option('acc_integration_sage_accounting_api_key');

        $expense_data = [];
        $continue = true;
        $page = 1;
        $top = 100;

        $list_results = [];
        while ($continue) {
            $continue = false;
            $skip = ($page - 1) * $top;

            $url = $api_domain.'/SupplierInvoice/Get?apikey='.$api_key.'&CompanyId='.$organization_id.'&$skip='.$skip.'&$top='.$top;
            $entities = $this->callAPI($url, [], $header, 'GET');

            $page++;
            if (isset($entities['Results'])) {
                foreach ($entities['Results'] as $expense) {
                    $expense_data[$expense['ID']] = $expense;
                }
            }

            if(isset($entities['ReturnedResults']) && $entities['ReturnedResults'] > 0){
                $continue = true;
            }
        }

        $vendor_connect_id = $this->get_sage_accounting_vendor_default_sa($organization_id);
        $expense_item_connect_id = $this->get_sage_accounting_expense_item_default_sa($organization_id);
        $bank_account = $this->get_sage_accounting_bank_account_sa($organization_id);

        foreach ($expenses as $expense) {
            if($expense['connect_id'] != ''){
                continue;
            }
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

            $item_array = [];

            $item_array[] = [
                    "SelectionId" => $expense_item_connect_id,
                    "UnitPriceExclusive" => $expense['amount'] + $total_tax,
                    "Quantity" => 1,
                    'Description' => 'Expense Item',
                    'Total' => $expense['amount'] + $total_tax,
                ];

            $expenseObj = [
                "SupplierId"=> $vendor_connect_id,
                "Date" => $expense['date'],
                "DueDate" => $expense['date'],
                "Message" => $expense['note'],
                "Reference" => "Expenses#".$expense['id'],
                "Lines" => $item_array,
                "Total" => $expense['amount'] + $total_tax,
            ];

            if($expense['connect_id'] != '' && isset($expense_data[$expense['connect_id']])){
                if($expense_id == ''){
                    continue;
                }

                $expenseObj['ID'] = $expense['connect_id'];
            }

            $url = $api_domain.'/SupplierInvoice/Save?apikey='.$api_key.'&CompanyId='.$organization_id;
            $result = $this->callAPI($url, $expenseObj, $header, 'POST');

            $this->delete_integration_error_log($expense['id'], 'expense', 'sage_accounting', $organization_id);
            if (!isset($result['ID'])) {

                $message = '';

                if (isset($result['Message'])) {
                    $message = $result['Message'];
                }elseif (is_string($result)) {
                    $message = $result;
                }

                $this->db->insert(db_prefix().'acc_integration_error_logs', [
                    'organization_id' => $organization_id,
                    'rel_id' => $expense['id'],
                    'rel_type' => 'expense',
                    'software' => 'sage_accounting',
                    'error_detail' => $message,
                    'date_updated' => date('Y-m-d H:i:s'),
                ]);

                $this->db->insert(db_prefix().'acc_integration_sync_logs', [
                    'organization_id' => $organization_id,
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
                $expense_connect_id = $result['ID'];
                if($expense['connect_id'] == ''){
                    $paymentObj = [
                        "SupplierId" => $vendor_connect_id,
                        "Date" => $expense['date'],
                        "PaymentMethod" => "1",
                        "Reference" => "Expenses#".$expense['id'],
                        "BankAccountId" => $bank_account['ID'],
                        "Total" => $expense['amount'] + $total_tax,
                    ];

                    $url = $api_domain.'/SupplierPayment/Save?apikey='.$api_key.'&CompanyId='.$organization_id;
                    $paymentResult = $this->callAPI($url, $paymentObj, $header, 'POST');

                    if (isset($paymentResult['ID'])) {
                        $allocationObj = [
                            "SourceDocumentId" => $paymentResult['ID'],
                            "AllocatedToDocumentId" => $expense_connect_id,
                            "DocumentHeaderId_Source" => $paymentResult['ID'],
                            "DocumentHeaderId_Allocation" => $expense_connect_id,
                            "Total" => $expense['amount'] + $total_tax,
                        ];

                        $url = $api_domain.'/Allocation/Save?apikey='.$api_key.'&CompanyId='.$organization_id;
                        $this->callAPI($url, $allocationObj, $header, 'POST');
                    }

                    $this->db->insert(db_prefix().'acc_integration_logs', [
                        'organization_id' => $organization_id,
                        'rel_id' => $expense['id'],
                        'rel_type' => 'expense',
                        'software' => 'sage_accounting',
                        'connect_id' => $expense_connect_id,
                        'date_updated' => date('Y-m-d H:i:s'),
                    ]);
                }else{
                    $this->db->where('organization_id', $organization_id);
                    $this->db->where('rel_id', $expense['id']);
                    $this->db->where('rel_type', 'expense');
                    $this->db->where('software', 'sage_accounting');
                    $this->db->update(db_prefix().'acc_integration_logs', [
                        'connect_id' => $expense_connect_id,
                        'date_updated' => date('Y-m-d H:i:s'),
                    ]);
                }

                $this->db->insert(db_prefix().'acc_integration_sync_logs', [
                    'organization_id' => $organization_id,
                    'rel_id' => $expense['id'],
                    'rel_type' => 'expense',
                    'software' => 'sage_accounting',
                    'type' => 'sync_up',
                    'status' => 1,
                    'connect_id' => $expense_connect_id,
                    'datecreated' => date('Y-m-d H:i:s'),
                ]);

                if($expense_id != ''){
                    return true;
                } 
            }
        }
    }

    /**
     * [create_sage_accounting_item_sa description]
     * @param  string $organization_id [description]
     * @param  string $item_id         [description]
     * @return [type]                  [description]
     */
    public function create_sage_accounting_item_sa($organization_id = '', $item_id = ''){
        $this->db->select('*, ' . db_prefix() . 'items.id as id');
        if($item_id != ''){
            $this->db->where(db_prefix().'items.id', $item_id);
        }
        $this->db->join(db_prefix() . 'acc_integration_logs', db_prefix() . 'acc_integration_logs.rel_id=' . db_prefix() . 'items.id AND '.db_prefix() . 'acc_integration_logs.rel_type = "item" AND software = "sage_accounting" AND ' . db_prefix() . 'acc_integration_logs.organization_id = "'.$organization_id.'"', 'left');

        $items = $this->db->get(db_prefix().'items')->result_array();

        $header = acc_get_sage_accounting_header();
        $api_domain = acc_get_sage_accounting_api_domain();
        $api_key = get_option('acc_integration_sage_accounting_api_key');

        $item_data = [];
        $continue = true;
        $page = 1;
        $top = 1;
        while ($continue) {
            $continue = false;
            $skip = ($page - 1) * $top;

            $url = $api_domain.'/Item/Get?apikey='.$api_key.'&CompanyId='.$organization_id.'&includeDetail=true&$skip='.$skip.'&$top='.$top;
            $entities = $this->callAPI($url, [], $header, 'GET');

            $page++;
            if (isset($entities['Results'])) {
                foreach ($entities['Results'] as $item) {
                    $item_data[$item['Code']] = $item;
                }
            }

            if(isset($entities['ReturnedResults']) && $entities['ReturnedResults'] > 0){
                $continue = true;
            }
        }

        foreach ($items as $item) {
            if($item['connect_id'] != ''){
                continue;
            }
           

            if($item['commodity_code'] == ''){
                $item_code = $this->generate_commodity_code();
                $item['commodity_code'] = $item_code;

                $this->db->where('id', $item['id']);    
                $this->db->update(db_prefix() . 'items', ['commodity_code' => $item_code]);
            }

            $itemObj = [
                "Code" => $item['commodity_code'],
                "Description" => preg_replace('/\s+/', ' ', $item['description']),
                "PriceExclusive" => $item['rate'],
                "PriceInclusive" => $item['rate'],
                "TaxTypeIdSales" => 0,
                "TaxTypeIdPurchases" => 0,
                "Active" => true,
                "Physical" => false
            ];

            $group_connect_id = $this->get_connect_id($item['group_id'], 'item_group', 'sage_accounting', $organization_id);

            if($group_connect_id == ''){
                $this->create_sage_accounting_item_group_sa($organization_id, $item['group_id']);
                $group_connect_id = $this->get_connect_id($item['group_id'], 'item_group', 'sage_accounting', $organization_id);
            }

            if($group_connect_id != ''){
                $itemObj['Category'] = ['ID' => $group_connect_id];
            }

            $url = $api_domain.'/Item/save?apikey='.$api_key.'&CompanyId='.$organization_id;
            $result = $this->callAPI($url, $itemObj, $header, 'POST');

            $this->delete_integration_error_log($item['id'], 'item', 'sage_accounting', $organization_id);
            if (!isset($result['ID'])) {

                $message = '';

                if (isset($result['Message'])) {
                    $message = $result['Message'];
                }elseif (is_string($result)) {
                    $message = $result;
                }

                $this->db->insert(db_prefix().'acc_integration_error_logs', [
                    'organization_id' => $organization_id,
                    'rel_id' => $item['id'],
                    'rel_type' => 'item',
                    'software' => 'sage_accounting',
                    'error_detail' => $message,
                    'date_updated' => date('Y-m-d H:i:s'),
                ]);

                $this->db->insert(db_prefix().'acc_integration_sync_logs', [
                    'organization_id' => $organization_id,
                    'rel_id' => $item['id'],
                    'rel_type' => 'item',
                    'software' => 'sage_accounting',
                    'type' => 'sync_up',
                    'status' => 0,
                    'connect_id' => '',
                    'datecreated' => date('Y-m-d H:i:s'),
                ]);

                if($item_id != ''){
                    return $message;
                }
            }else{

                if($item['connect_id'] == ''){
                    $this->db->insert(db_prefix().'acc_integration_logs', [
                        'organization_id' => $organization_id,
                        'rel_id' => $item['id'],
                        'rel_type' => 'item',
                        'software' => 'sage_accounting',
                        'connect_id' => $result['ID'],
                        'date_updated' => date('Y-m-d H:i:s'),
                    ]);
                }else{
                    $this->db->where('organization_id', $organization_id);
                    $this->db->where('rel_id', $item['id']);
                    $this->db->where('rel_type', 'item');
                    $this->db->where('software', 'sage_accounting');
                    $this->db->update(db_prefix().'acc_integration_logs', [
                        'connect_id' => $result['ID'],
                        'date_updated' => date('Y-m-d H:i:s'),
                    ]);
                }

                $this->db->insert(db_prefix().'acc_integration_sync_logs', [
                    'organization_id' => $organization_id,
                    'rel_id' => $item['id'],
                    'rel_type' => 'item',
                    'software' => 'sage_accounting',
                    'type' => 'sync_up',
                    'status' => 1,
                    'connect_id' => $result['ID'],
                    'datecreated' => date('Y-m-d H:i:s'),
                ]);

                if($item_id != ''){
                    return true;
                }
            }
        }
    }

    /**
     * [get_sage_accounting_tax_no_vat description]
     * @param  [type] $organization_id [description]
     * @return [type]                  [description]
     */
    public function get_sage_accounting_tax_no_vat($organization_id){
        $header = acc_get_sage_accounting_header();
        $api_domain = acc_get_sage_accounting_api_domain();
        $api_key = get_option('acc_integration_sage_accounting_api_key');

        $url = $api_domain.'/TaxType/get?apikey='.$api_key.'&CompanyId='.$organization_id;
        $entities = $this->callAPI($url, [], $header, 'GET');
        if (isset($entities['Results'])) {

            return $entities['Results'];
        } else {
            return [];
        }
    }

    /**
     * generate commodity code
     *
     * @return     string
     */
    public function generate_commodity_code() {
        $item = false;
        do {
            $length = 11;
            $chars = '0123456789';
            $count = new_strlen($chars);
            $password = '';
            for ($i = 0; $i < $length; $i++) {
                $index = rand(0, $count - 1);
                $password .= mb_substr($chars, $index, 1);
            }
            $this->db->where('commodity_code', $password);
            $item = $this->db->get(db_prefix() . 'items')->row();
        } while ($item);

        return $password;
    }

    /**
     * [create_sage_accounting_item_group_sa description]
     * @param  string $organization_id [description]
     * @param  string $group_id        [description]
     * @return [type]                  [description]
     */
    public function create_sage_accounting_item_group_sa($organization_id = '', $group_id = ''){
        $this->db->select('*, ' . db_prefix() . 'items_groups.id as id');
        if($group_id != ''){
            $this->db->where(db_prefix().'items_groups.id', $group_id);
        }
        $this->db->join(db_prefix() . 'acc_integration_logs', db_prefix() . 'acc_integration_logs.rel_id=' . db_prefix() . 'items_groups.id AND '.db_prefix() . 'acc_integration_logs.rel_type = "item_group" AND software = "sage_accounting" AND ' . db_prefix() . 'acc_integration_logs.organization_id = "'.$organization_id.'"', 'left');

        $groups = $this->db->get(db_prefix().'items_groups')->result_array();

        $header = acc_get_sage_accounting_header();
        $api_domain = acc_get_sage_accounting_api_domain();
        $api_key = get_option('acc_integration_sage_accounting_api_key');

        $group_data = [];
        $continue = true;
        $page = 1;
        $top = 100;

        $list_results = [];
        while ($continue) {
            $continue = false;
            $skip = ($page - 1) * $top;

            $url = $api_domain.'/ItemCategory/Get?apikey='.$api_key.'&CompanyId='.$organization_id.'&$skip='.$skip.'&$top='.$top;
            $entities = $this->callAPI($url, [], $header, 'GET');
            $page++;
            if (isset($entities['Results'])) {
                foreach ($entities['Results'] as $group) {
                    $group_data[$group['ID']] = $group;
                }
            }

            if(isset($entities['ReturnedResults']) && $entities['ReturnedResults'] > 0){
                $continue = true;
            }
        }

        foreach ($groups as $group) {
            if($group['connect_id'] != ''){
                continue;
            }

            $groupObj = [
                "Description" => $group['name']
            ];

            if($group['connect_id'] != '' && isset($group_data[$group['connect_id']])){
                $groupObj['ID'] = $group['connect_id'];
            }

            $url = $api_domain.'/ItemCategory/save?apikey='.$api_key.'&CompanyId='.$organization_id;
            $result = $this->callAPI($url, $groupObj, $header, 'POST');

            $this->delete_integration_error_log($group['id'], 'item_group', 'sage_accounting', $organization_id);
            if (!isset($result['ID'])) {
                $message = '';

                if (isset($result['Message'])) {
                    $message = $result['Message'];
                }elseif (is_string($result)) {
                    $message = $result;
                }

                $this->db->insert(db_prefix().'acc_integration_error_logs', [
                    'organization_id' => $organization_id,
                    'rel_id' => $group['id'],
                    'rel_type' => 'item_group',
                    'software' => 'sage_accounting',
                    'error_detail' => $message,
                    'date_updated' => date('Y-m-d H:i:s'),
                ]);  

                $this->db->insert(db_prefix().'acc_integration_sync_logs', [
                    'organization_id' => $organization_id,
                    'rel_id' => $group['id'],
                    'rel_type' => 'item_group',
                    'software' => 'sage_accounting',
                    'type' => 'sync_up',
                    'status' => 0,
                    'connect_id' => '',
                    'datecreated' => date('Y-m-d H:i:s'),
                ]);

                if($group_id != ''){
                    return $message;
                }
            }else{

                if($group['connect_id'] == ''){
                    $this->db->insert(db_prefix().'acc_integration_logs', [
                        'organization_id' => $organization_id,
                        'rel_id' => $group['id'],
                        'rel_type' => 'item_group',
                        'software' => 'sage_accounting',
                        'connect_id' => $result['ID'],
                        'date_updated' => date('Y-m-d H:i:s'),
                    ]);
                }else{
                    $this->db->where('organization_id', $organization_id);
                    $this->db->where('rel_id', $group['id']);
                    $this->db->where('rel_type', 'item_group');
                    $this->db->where('software', 'sage_accounting');
                    $this->db->update(db_prefix().'acc_integration_logs', [
                        'connect_id' => $result['ID'],
                        'date_updated' => date('Y-m-d H:i:s'),
                    ]);
                }

                $this->db->insert(db_prefix().'acc_integration_sync_logs', [
                    'organization_id' => $organization_id,
                    'rel_id' => $group['id'],
                    'rel_type' => 'item_group',
                    'software' => 'sage_accounting',
                    'type' => 'sync_up',
                    'status' => 1,
                    'connect_id' => $result['ID'],
                    'datecreated' => date('Y-m-d H:i:s'),
                ]);

                if($group_id != ''){
                    return true;
                }
            }
        }
    }

    /**
     * [get_sage_accounting_bank_account_sa description]
     * @param  [type] $organization_id [description]
     * @return [type]                  [description]
     */
    public function get_sage_accounting_bank_account_sa($organization_id){
        $header = acc_get_sage_accounting_header();
        $api_domain = acc_get_sage_accounting_api_domain();
        $api_key = get_option('acc_integration_sage_accounting_api_key');

        $url = $api_domain.'/BankAccount/get?apikey='.$api_key.'&CompanyId='.$organization_id;
        $entities = $this->callAPI($url, [], $header, 'GET');
        if (isset($entities['Results'])) {
            foreach ($entities['Results'] as $key => $value) {
                if($value['Default'] == true && $value['Active'] == true){
                    return $value;
                }
            }
        }

        return [];
    }

    /**
     * [get_sage_accounting_vendor_default_sa description]
     * @param  [type] $organization_id [description]
     * @return [type]                  [description]
     */
    public function get_sage_accounting_vendor_default_sa($organization_id){
        $header = acc_get_sage_accounting_header();
        $api_domain = acc_get_sage_accounting_api_domain();
        $api_key = get_option('acc_integration_sage_accounting_api_key');

        $url = $api_domain.'/Supplier/Get?apikey='.$api_key.'&CompanyId='.$organization_id;
        $result = $this->callAPI($url, [], $header, 'GET');

        if (isset($result['Results'])) {
            foreach ($result['Results'] as $key => $value) {
                if($value['Name'] == 'Expense Vendor'){
                    return $value['ID'];
                }
            }
        }

        $supplierObj = [
             "Name"=>  "Expense Vendor",
             "Active" => true
        ];

        $url = $api_domain.'/Supplier/Save?apikey='.$api_key.'&CompanyId='.$organization_id;
        $result = $this->callAPI($url, $supplierObj, $header, 'POST');

        if (isset($result['ID'])) {
            return $result['ID'];
        }

        return '';
    }

    /**
     * [get_sage_accounting_expense_item_default_sa description]
     * @param  [type] $organization_id [description]
     * @return [type]                  [description]
     */
    public function get_sage_accounting_expense_item_default_sa($organization_id){
        $header = acc_get_sage_accounting_header();
        $api_domain = acc_get_sage_accounting_api_domain();
        $api_key = get_option('acc_integration_sage_accounting_api_key');

        $url = $api_domain.'/Item/Get?apikey='.$api_key.'&CompanyId='.$organization_id;
        $result = $this->callAPI($url, [], $header, 'GET');

        if (isset($result['Results'])) {
            foreach ($result['Results'] as $key => $value) {
                if($value['Description'] == 'Expense Item'){
                    return $value['ID'];
                }
            }
        }

        $item_code = $this->generate_commodity_code();
            $itemObj = [
            "Code" => $item_code,
            "Description" => 'Expense Item',
            "PriceExclusive" => 0,
            "PriceInclusive" => 0,
            "TaxTypeIdSales" => 0,
            "TaxTypeIdPurchases" => 0,
            "Active" => true,
            "Physical" => false
        ];

        $url = $api_domain.'/Item/Save?apikey='.$api_key.'&CompanyId='.$organization_id;
        $result = $this->callAPI($url, $itemObj, $header, 'POST');

        if (isset($result['ID'])) {
            return $result['ID'];
        }

        return '';
    }

    /**
     * [get_sage_accounting_customer_sa description]
     * @param  string $organization_id [description]
     * @return [type]                  [description]
     */
    public function get_sage_accounting_customer_sa($organization_id = ''){
        $this->get_sage_accounting_customer_group_sa($organization_id);
        $customer_list = $this->clients_model->get();
     
        $customer_arr = [];
        foreach ($customer_list as $customer) {
            $customer_arr[$customer['userid']] = $customer;
        }

        $header = acc_get_sage_accounting_header();
        $api_domain = acc_get_sage_accounting_api_domain();
        $api_key = get_option('acc_integration_sage_accounting_api_key');

        $continue = true;
        $page = 1;
        $top = 100;

        $list_results = [];
        while ($continue) {
            $continue = false;
            $skip = ($page - 1) * $top;
            $url = $api_domain.'/Customer/get?apikey='.$api_key.'&CompanyId='.$organization_id.'&$skip='.$skip.'&$top='.$top;
            $entities = $this->callAPI($url, [], $header, 'GET');

            $page++;
            if (isset($entities['Results'])) {
                $list_results = array_merge($list_results, $entities['Results']);
            }

            if(isset($entities['ReturnedResults']) && $entities['ReturnedResults'] > 0){
                $continue = true;
            }
        }

        foreach ($list_results as $customer) {
            $check_connect_id = $this->check_connect_id($customer['ID'], 'customer', 'sage_accounting', $organization_id);
            if($check_connect_id != 0){
                continue;
            }
            
            $customer_data = [];
            $customer_data['company'] = $customer['Name'];
            $customer_data['phonenumber'] = $customer['Telephone'] ?? '';
            $customer_data['website'] = $customer['WebAddress'] ?? '';

            if(isset($customer['Category'])){
                $group_connect_id = $this->check_connect_id($customer['Category']['ID'], 'customer_group', 'sage_accounting', $organization_id);
                $customer_data['groups_in'] = [$group_connect_id];
            }

            $customer_data['billing_street'] = $customer['PostalAddress01'] ?? '';
            $customer_data['billing_city'] = '';
            $customer_data['billing_state'] = '';
            $customer_data['billing_zip'] = '';
            $customer_data['billing_country'] = '';

            $shipping_street = '';
            $shipping_city = '';
            $shipping_zip = '';

            $customer_data['shipping_street'] = $customer['DeliveryAddress01'] ?? '';
            $customer_data['shipping_city'] = '';
            $customer_data['shipping_state'] = '';
            $customer_data['shipping_zip'] = '';
            $customer_data['shipping_country'] = '';

            $customer_data['default_currency'] = '';
            if($check_connect_id != 0 && isset($customer_arr[$check_connect_id])){
                $this->clients_model->update($customer_data, $check_connect_id);
                $client_id = $check_connect_id;
            }else{
                if($check_connect_id != 0){
                    $this->delete_integration_log($check_connect_id, 'customer', 'sage_accounting', $organization_id);
                }

                $client_id = $this->clients_model->add($customer_data);
            }

            $sync_status = 0;
            if($client_id){
            $sync_status = 1;
                if(!isset($customer_arr[$check_connect_id])){
                    if(isset($customer['Email'])){
                        $contact_data = [];
                        $contact_data['firstname'] = $customer['ContactName'] ?? $customer['Name'];
                        $contact_data['lastname'] = '';
                        $contact_data['phonenumber'] = $customer['Telephone'] ?? '';
                        $contact_data['email'] = $customer['Email'];
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

                    $this->db->insert(db_prefix().'acc_integration_logs', [
                        'organization_id' => $organization_id,
                        'rel_id' => $client_id,
                        'rel_type' => 'customer',
                        'software' => 'sage_accounting',
                        'connect_id' => $customer['ID'],
                        'date_updated' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            $this->db->insert(db_prefix().'acc_integration_sync_logs', [
                        'organization_id' => $organization_id,
                        'rel_id' => $client_id,
                        'rel_type' => 'customer',
                        'software' => 'sage_accounting',
                        'type' => 'sync_down',
                        'status' => $sync_status,
                        'connect_id' => $customer['ID'],
                        'datecreated' => date('Y-m-d H:i:s'),
                    ]);
        }
    }

    /**
     * [get_sage_accounting_invoice_sa description]
     * @param  string $organization_id [description]
     * @return [type]                  [description]
     */
    public function get_sage_accounting_invoice_sa($organization_id = ''){
        $this->get_sage_accounting_item_sa($organization_id);

        $this->load->model('currencies_model');
        $currency = $this->currencies_model->get_base_currency();

        $this->load->model('invoices_model');
        $invoice_list = $this->invoices_model->get();
     
        $invoice_arr = [];
        foreach ($invoice_list as $invoice) {
            $invoice_arr[$invoice['id']] = $invoice;
        }

        $header = acc_get_sage_accounting_header();
        $api_domain = acc_get_sage_accounting_api_domain();
        $api_key = get_option('acc_integration_sage_accounting_api_key');

        $continue = true;
        $page = 1;
        $top = 100;

        $list_results = [];
        while ($continue) {
            $continue = false;
            $skip = ($page - 1) * $top;

            $url = $api_domain.'/TaxInvoice/Get?apikey='.$api_key.'&CompanyId='.$organization_id.'&includeDetail=true&$skip='.$skip.'&$top='.$top;
            $entities = $this->callAPI($url, [], $header, 'GET');

            $page++;
            if (isset($entities['Results'])) {
                $list_results = array_merge($list_results, $entities['Results']);
            }

            if(isset($entities['ReturnedResults']) && $entities['ReturnedResults'] > 0){
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
            $check_connect_id = $this->check_connect_id($invoice['ID'], 'invoice', 'sage_accounting', $organization_id);
            if($check_connect_id != 0){
                continue;
            }
            $customer_connect_id = $this->check_connect_id($invoice['CustomerId'], 'customer', 'sage_accounting', $organization_id);
            if($customer_connect_id == 0){
                $this->get_sage_accounting_customer_sa($organization_id);
                $customer_connect_id = $this->check_connect_id($invoice['CustomerId'], 'customer', 'sage_accounting', $organization_id);
            }

            if($customer_connect_id == ''){
                continue;
            }

            $invoice_data = [];


            $invoice_data['currency'] = $currency->id;

            $date = new DateTime($invoice['Date']);
            $invoice['Date'] = $date->format("Y-m-d");

            $invoice_data['date'] = $invoice['Date'];

            $date = new DateTime($invoice['DueDate']);
            $invoice['DueDate'] = $date->format("Y-m-d");
            $invoice_data['duedate'] = $invoice['DueDate'];

            $invoice_data['clientid']         = $customer_connect_id;

            $invoice_data['include_shipping']         = 1;
            $invoice_data['show_shipping_on_invoice'] = 1;

            $invoice_data["allowed_payment_modes"] = $payment_model_list;

            $billing_street = '';
            $billing_city = '';
            $billing_state = '';
            $billing_zip = '';
            $billing_country = '';
           
            $invoice_data['billing_street'] = $invoice['PostalAddress01'] ?? '';
            $invoice_data['billing_city'] = $billing_city;
            $invoice_data['billing_state'] = '';
            $invoice_data['billing_zip'] = $billing_zip;
            $invoice_data['billing_country'] = $billing_country;

            $shipping_street = '';
            $shipping_city = '';
            $shipping_state = '';
            $shipping_zip = '';
            $shipping_country = '';
           
            $invoice_data['shipping_street'] = $invoice['DeliveryAddress01'];
            $invoice_data['shipping_city'] = $shipping_city;
            $invoice_data['shipping_state'] = $shipping_state;
            $invoice_data['shipping_zip'] = $shipping_zip;
            $invoice_data['shipping_country'] = $shipping_country;
            $invoice_data['total']               = $invoice['Total'];
            $invoice_data['adminnote'] = "The invoice was synced from the Sage Accounting system and is linked to invoice number: ".$invoice['DocumentNumber'];

            $newitems = [];

            $discount_total = 0;
            $subtotal = 0;
            foreach ($invoice['Lines'] as $key => $value) {
                $tax_arr = [];
                if($value['TaxPercentage'] > 0){
                    $tax_arr = ['Tax|'.$value['TaxPercentage']];
                }

                array_push($newitems, array(
                    'order' => $key, 
                    'description' => $value['Description'] ?? 'Item', 
                    'long_description' => '', 
                    'qty' => $value['Quantity'] ?? 1, 
                    'unit' => $value['Unit'] ?? '',  
                    'rate' => $value['UnitPriceExclusive'], 
                    'taxname' => $tax_arr));
                $subtotal += ($value['Quantity'] * $value['UnitPriceExclusive']);
                
                if($value['Discount'] > 0){
                    $discount_total += $value['Discount'];
                }
            }

            if($invoice['Discount'] > 0){
                $discount_total += $invoice['Discount'];
            }

            if($discount_total > 0){
                $invoice_data['discount_type']               = 'before_tax';
                $invoice_data['discount_total']               = $discount_total;
            }

            if($invoice['DiscountPercentage'] > 0){
                $invoice_data['discount_percent']               = $invoice['DiscountPercentage']*100;
            }

            $invoice_data['subtotal']            = $subtotal;

            $invoice_data['newitems'] = $newitems;
            
            if($check_connect_id != 0 && isset($invoice_arr[$check_connect_id])){
                $this->delete_invoice_item($check_connect_id);
                $this->invoices_model->update($invoice_data, $check_connect_id);
                $invoice_id = $check_connect_id;
            }else{
                if($check_connect_id != 0){
                    $this->delete_integration_log($check_connect_id, 'invoice', 'sage_accounting', $organization_id);
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
                        'organization_id' => $organization_id,
                        'rel_id' => $invoice_id,
                        'rel_type' => 'invoice',
                        'software' => 'sage_accounting',
                        'connect_id' => $invoice['ID'],
                        'date_updated' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            $this->db->insert(db_prefix().'acc_integration_sync_logs', [
                        'organization_id' => $organization_id,
                        'rel_id' => $invoice_id,
                        'rel_type' => 'invoice',
                        'software' => 'sage_accounting',
                        'type' => 'sync_down',
                        'status' => $sync_status,
                        'connect_id' => $invoice['ID'],
                        'datecreated' => date('Y-m-d H:i:s'),
                    ]);
        }
    }

    /**
     * [get_sage_accounting_payment_sa description]
     * @param  string $organization_id [description]
     * @return [type]                  [description]
     */
    public function get_sage_accounting_payment_sa($organization_id = ''){
        $this->load->model('payments_model');
        $payment_list = $this->db->get(db_prefix() . 'invoicepaymentrecords')->result_array();
     
        $payment_arr = [];
        foreach ($payment_list as $payment) {
            $payment_arr[$payment['id']] = $payment;
        }

        $header = acc_get_sage_accounting_header();
        $api_domain = acc_get_sage_accounting_api_domain();
        $api_key = get_option('acc_integration_sage_accounting_api_key');

        $continue = true;
        $page = 1;
        $top = 100;

        $list_results = [];
        while ($continue) {
            $continue = false;
            $skip = ($page - 1) * $top;

            $url = $api_domain.'/Allocation/Get?apikey='.$api_key.'&CompanyId='.$organization_id.'&$skip='.$skip.'&$top='.$top;
            $entities = $this->callAPI($url, [], $header, 'GET');

            $page++;
            if (isset($entities['Results'])) {
                $list_results = array_merge($list_results, $entities['Results']);
            }

            if(isset($entities['ReturnedResults']) && $entities['ReturnedResults'] > 0){
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
            
            $check_connect_id = $this->check_connect_id($payment['ID'], 'payment', 'sage_accounting', $organization_id);
            if($check_connect_id != 0){
                continue;
            }

            $invoice_id = 0;
            $amount = round($payment['Total'], 2);

            $invoice_id = $this->check_connect_id($payment['AllocatedToDocumentId'], 'invoice', 'sage_accounting', $organization_id);

            if($invoice_id == 0){
                continue;
            }

            $payment_data = [];

            $payment_data['invoiceid'] = $invoice_id;
            $payment_data['amount'] = $amount;
            
            $date = new DateTime($payment['Created']);
            $payment_data['date'] = $date->format("Y-m-d");
            $payment_data['transactionid'] = '';
            $payment_data['note'] = '';
            
            if($check_connect_id != 0 && isset($payment_arr[$check_connect_id])){
                $this->payments_model->update($payment_data, $check_connect_id);
                $payment_id = $check_connect_id;
            }else{
                if($check_connect_id != 0){
                    $this->delete_integration_log($check_connect_id, 'payment', 'sage_accounting', $organization_id);
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
                        'organization_id' => $organization_id,
                        'rel_id' => $payment_id,
                        'rel_type' => 'payment',
                        'software' => 'sage_accounting',
                        'connect_id' => $payment['ID'],
                        'date_updated' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            $this->db->insert(db_prefix().'acc_integration_sync_logs', [
                        'organization_id' => $organization_id,
                        'rel_id' => $payment_id,
                        'rel_type' => 'payment',
                        'software' => 'sage_accounting',
                        'type' => 'sync_down',
                        'status' => $sync_status,
                        'connect_id' => $payment['ID'],
                        'datecreated' => date('Y-m-d H:i:s'),
                    ]);
        }
    }

    /**
     * [get_sage_accounting_expense_sa description]
     * @param  string $organization_id [description]
     * @return [type]                  [description]
     */
    public function get_sage_accounting_expense_sa($organization_id = ''){
        $this->load->model('currencies_model');
        $currency = $this->currencies_model->get_base_currency();

        $this->load->model('expenses_model');
        $expense_list = $this->expenses_model->get();
     
        $expense_arr = [];
        foreach ($expense_list as $expense) {
            $expense_arr[$expense['id']] = $expense;
        }

        $header = acc_get_sage_accounting_header();
        $api_domain = acc_get_sage_accounting_api_domain();
        $api_key = get_option('acc_integration_sage_accounting_api_key');

        $continue = true;
        $page = 1;
        $top = 100;

        $list_results = [];
        while ($continue) {
            $continue = false;
            $skip = ($page - 1) * $top;

            $url = $api_domain.'/SupplierInvoice/Get?apikey='.$api_key.'&CompanyId='.$organization_id.'&$skip='.$skip.'&$top='.$top;
            $entities = $this->callAPI($url, [], $header, 'GET');

            $page++;
            if (isset($entities['Results'])) {
                $list_results = array_merge($list_results, $entities['Results']);
            }

            if(isset($entities['ReturnedResults']) && $entities['ReturnedResults'] > 0){
                $continue = true;
            }
        }

        foreach ($list_results as $expense) {
            $check_connect_id = $this->check_connect_id($expense['ID'], 'expense', 'sage_accounting', $organization_id);
            if($check_connect_id != 0){
                continue;
            }

            $expense_data = [];

            $expense_data['vendor'] = '';
            $expense_data['expense_name'] = '';
            $expense_data['note'] = '';
            $expense_data['category'] = $this->init_expense_category('Sage Accounting Expenses');

            $date = new DateTime($expense['Date']);
            $expense_data['date'] = $date->format("Y-m-d");
            $expense_data['amount'] = $expense['Total'];
            $expense_data['clientid'] = '';
            $expense_data['project_id'] = '';

            $expense_data['currency'] = $currency->id;

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
                    $this->delete_integration_log($check_connect_id, 'expense', 'sage_accounting', $organization_id);
                }

                $expense_id = $this->expenses_model->add($expense_data);
            }

            $sync_status = 0;
            if($expense_id){
            $sync_status = 1;
                if(!isset($expense_arr[$check_connect_id])){
                    $this->db->insert(db_prefix().'acc_integration_logs', [
                        'organization_id' => $organization_id,
                        'rel_id' => $expense_id,
                        'rel_type' => 'expense',
                        'software' => 'sage_accounting',
                        'connect_id' => $expense['ID'],
                        'date_updated' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            $this->db->insert(db_prefix().'acc_integration_sync_logs', [
                        'organization_id' => $organization_id,
                        'rel_id' => $expense_id,
                        'rel_type' => 'expense',
                        'software' => 'sage_accounting',
                        'type' => 'sync_down',
                        'status' => $sync_status,
                        'connect_id' => $expense['ID'],
                        'datecreated' => date('Y-m-d H:i:s'),
                    ]);
        }
    }

    /**
     * [get_sage_accounting_customer_group_sa description]
     * @param  string $organization_id [description]
     * @return [type]                  [description]
     */
    public function get_sage_accounting_customer_group_sa($organization_id = ''){
        $groups = $this->db->get(db_prefix().'customers_groups')->result_array();
     
        $group_arr = [];
        foreach ($groups as $group) {
            $group_arr[$group['id']] = $group;
        }

        $header = acc_get_sage_accounting_header();
        $api_domain = acc_get_sage_accounting_api_domain();
        $api_key = get_option('acc_integration_sage_accounting_api_key');

        $continue = true;
        $page = 1;
        $top = 100;

        $list_results = [];
        while ($continue) {
            $continue = false;
            $skip = ($page - 1) * $top;
            $url = $api_domain.'/CustomerCategory/get?apikey='.$api_key.'&CompanyId='.$organization_id.'&$skip='.$skip.'&$top='.$top;
            $entities = $this->callAPI($url, [], $header, 'GET');

            $page++;
            if (isset($entities['Results'])) {
                $list_results = array_merge($list_results, $entities['Results']);
            }

            if(isset($entities['ReturnedResults']) && $entities['ReturnedResults'] > 0){
                $continue = true;
            }
        }

        foreach ($list_results as $group) {

            $check_connect_id = $this->check_connect_id($group['ID'], 'customer_group', 'sage_accounting', $organization_id);
            if($check_connect_id != 0){
                continue;
            }

            if($check_connect_id != 0 && isset($group_arr[$check_connect_id])){
                $this->client_groups_model->edit(['name' => $group['Description'], 'id' => $check_connect_id]);
                $group_id = $check_connect_id;
            }else{
                if($check_connect_id != 0){
                    $this->delete_integration_log($check_connect_id, 'customer_group', 'sage_accounting', $organization_id);
                }

                $group_id = $this->client_groups_model->add(['name' => $group['Description']]);
            }

            $sync_status = 0;
            if($group_id){
                $sync_status = 1;
                if(!isset($group_arr[$check_connect_id])){
                    $this->db->insert(db_prefix().'acc_integration_logs', [
                        'organization_id' => $organization_id,
                        'rel_id' => $group_id,
                        'rel_type' => 'customer_group',
                        'software' => 'sage_accounting',
                        'connect_id' => $group['ID'],
                        'date_updated' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            $this->db->insert(db_prefix().'acc_integration_sync_logs', [
                        'organization_id' => $organization_id,
                        'rel_id' => $group_id,
                        'rel_type' => 'customer_group',
                        'software' => 'sage_accounting',
                        'type' => 'sync_down',
                        'status' => $sync_status,
                        'connect_id' => $group['ID'],
                        'datecreated' => date('Y-m-d H:i:s'),
                    ]);
        }
    }

    /**
     * [get_sage_accounting_item_sa description]
     * @param  string $organization_id [description]
     * @return [type]                  [description]
     */
    public function get_sage_accounting_item_sa($organization_id = ''){
        $items = $this->db->get(db_prefix().'items')->result_array();
        $item_arr = [];
        foreach ($items as $item) {
            $item_arr[$item['id']] = 1;
        }

        $header = acc_get_sage_accounting_header();
        $api_domain = acc_get_sage_accounting_api_domain();
        $api_key = get_option('acc_integration_sage_accounting_api_key');

        $continue = true;
        $page = 1;
        $top = 100;

        $list_results = [];
        while ($continue) {
            $continue = false;
            $skip = ($page - 1) * $top;
            $url = $api_domain.'/Item/get?apikey='.$api_key.'&CompanyId='.$organization_id.'&$skip='.$skip.'&$top='.$top;
            $entities = $this->callAPI($url, [], $header, 'GET');

            $page++;
            if (isset($entities['Results'])) {
                $list_results = array_merge($list_results, $entities['Results']);
            }

            if(isset($entities['ReturnedResults']) && $entities['ReturnedResults'] > 0){
                $continue = true;
            }
        }

        foreach ($list_results as $item) {

            $check_connect_id = $this->check_connect_id($item['ID'], 'item', 'sage_accounting', $organization_id);
            if($check_connect_id != 0){
                continue;
            }

            $insert_item = [
                'commodity_code' => $item['Code'],
                'description' => $item['Description'],
                'rate' => $item['PriceExclusive'],
            ];

            if(isset($item['Category'])){
                $group_connect_id = $this->check_connect_id($item['Category']['ID'], 'item_group', 'sage_accounting', $organization_id);
                if($group_connect_id == 0){
                    $this->get_sage_accounting_item_group_sa($organization_id);
                    $group_connect_id = $this->check_connect_id($item['Category']['ID'], 'item_group', 'sage_accounting', $organization_id);
                }
                $insert_item['group_id'] = $group_connect_id;
            }

            $this->db->insert(db_prefix().'items', $insert_item);
            $item_id = $this->db->insert_id();

            $sync_status= 0;
            if($item_id){
                $sync_status= 1;
                $this->db->insert(db_prefix().'acc_integration_logs', [
                    'organization_id' => $organization_id,
                    'rel_id' => $item_id,
                    'rel_type' => 'item',
                    'software' => 'sage_accounting',
                    'connect_id' => $item['ID'],
                    'date_updated' => date('Y-m-d H:i:s'),
                ]);
            }

            $this->db->insert(db_prefix().'acc_integration_sync_logs', [
                        'organization_id' => $organization_id,
                        'rel_id' => $item_id,
                        'rel_type' => 'item',
                        'software' => 'sage_accounting',
                        'type' => 'sync_down',
                        'status' => $sync_status,
                        'connect_id' => $item['ID'],
                        'datecreated' => date('Y-m-d H:i:s'),
                    ]);

        }
    }

    /**
     * [get_sage_accounting_item_group_sa description]
     * @param  string $organization_id [description]
     * @return [type]                  [description]
     */
    public function get_sage_accounting_item_group_sa($organization_id = ''){
        $groups = $this->db->get(db_prefix().'items_groups')->result_array();
     
        $group_arr = [];
        foreach ($groups as $group) {
            $group_arr[$group['id']] = $group;
        }

        $header = acc_get_sage_accounting_header();
        $api_domain = acc_get_sage_accounting_api_domain();
        $api_key = get_option('acc_integration_sage_accounting_api_key');

        $continue = true;
        $page = 1;
        $top = 100;

        $list_results = [];
        while ($continue) {
            $continue = false;
            $skip = ($page - 1) * $top;
            $url = $api_domain.'/ItemCategory/get?apikey='.$api_key.'&CompanyId='.$organization_id.'&$skip='.$skip.'&$top='.$top;
            $entities = $this->callAPI($url, [], $header, 'GET');

            $page++;
            if (isset($entities['Results'])) {
                $list_results = array_merge($list_results, $entities['Results']);
            }

            if(isset($entities['ReturnedResults']) && $entities['ReturnedResults'] > 0){
                $continue = true;
            }
        }

        foreach ($list_results as $group) {

            $check_connect_id = $this->check_connect_id($group['ID'], 'item_group', 'sage_accounting', $organization_id);
            if($check_connect_id != 0){
                continue;
            }

            $this->db->insert(db_prefix().'items_groups', ['name' => $group['Description']]);
            $group_id = $this->db->insert_id();

            $sync_status= 0;
            if($group_id){
                $sync_status= 1;
                $this->db->insert(db_prefix().'acc_integration_logs', [
                    'organization_id' => $organization_id,
                    'rel_id' => $group_id,
                    'rel_type' => 'item_group',
                    'software' => 'sage_accounting',
                    'connect_id' => $group['ID'],
                    'date_updated' => date('Y-m-d H:i:s'),
                ]);
            }

            $this->db->insert(db_prefix().'acc_integration_sync_logs', [
                        'organization_id' => $organization_id,
                        'rel_id' => $group_id,
                        'rel_type' => 'item_group',
                        'software' => 'sage_accounting',
                        'type' => 'sync_down',
                        'status' => $sync_status,
                        'connect_id' => $group['ID'],
                        'datecreated' => date('Y-m-d H:i:s'),
                    ]);
        }
    }
}