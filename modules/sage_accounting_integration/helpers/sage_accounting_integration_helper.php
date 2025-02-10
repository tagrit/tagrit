<?php
defined('BASEPATH') or exit('No direct script access allowed');

function get_acc_sage_accounting_config(){
    $CI = &get_instance();

    $client_id = get_option('acc_integration_sage_accounting_client_id');
    $client_secret = get_option('acc_integration_sage_accounting_client_secret');

    return array(
      'auth_url' => 'https://www.sageone.com/oauth2/auth/central?filter=apiv3.1', 
      'access_token_url' => 'https://oauth.accounting.sage.com/token', 
      'client_id' => html_entity_decode($CI->encryption->decrypt($client_id)), 
      'client_secret' => html_entity_decode($CI->encryption->decrypt($client_secret)), 
      'scope' => 'full_access', 
      'redirect_uri' => admin_url('sage_accounting_integration/connect'), 
      'access_token' => get_option('acc_integration_sage_accounting_access_token'), 
      'access_token_expires' => get_option('acc_integration_sage_accounting_access_token_expires'), 
      'refresh_token' => get_option('acc_integration_sage_accounting_refresh_token'), 
      'refresh_token_expires' => get_option('acc_integration_sage_accounting_refresh_token_expires'), 
      'requested_by_id' => get_option('acc_integration_sage_accounting_requested_by_id'), 
    );
}

function acc_get_sage_accounting_header(){
    $header = array(
        'Content-Type: application/json',
        'Authorization: '. get_option('acc_integration_sage_accounting_access_token'));

    return $header;
}

function acc_get_sage_accounting_api_domain(){
    $api_domain = "https://api.accounting.sage.com/v3.1";

    return $api_domain;
}


/**
 * Gets the url by type identifier.
 */
if(!function_exists('sync_get_url_by_type_id')){
  function sync_get_url_by_type_id($rel_id, $rel_type){
    $url = '#';
    $name = '';
    switch ($rel_type) {
          case 'invoice':
              $name = format_invoice_number($rel_id);
              $url = admin_url('invoices/list_invoices/'.$rel_id);
          break;

          case 'expense':
              $name = _l('sync_expense').' #'.$rel_id;
              $url = admin_url('expenses/list_expenses/'.$rel_id);
          break;

          case 'payment':
              $name = _l('sync_payment').' #'.$rel_id;
              $url = admin_url('payments/payment/'.$rel_id);
          break;

          case 'customer':
              $name = get_company_name($rel_id);
              $url = admin_url('clients/client/'.$rel_id);
          break;
      }

      if($name == ''){
        $name = _l('sync_'.$rel_type).' #'.$rel_id;
      }

      return '<a href="'.$url.'">'.$name.'</a>';
  }
}