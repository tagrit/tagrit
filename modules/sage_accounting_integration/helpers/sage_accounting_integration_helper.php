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

    $authorization = get_option('acc_integration_sage_accounting_access_token');

    $header = array(
        'Content-Type: application/json',
        'Authorization: '. $authorization);

    if(get_option('acc_integration_sage_accounting_region') == 'south_african'){
      $username = get_option('acc_integration_sage_accounting_username');
      $value = get_option('acc_integration_sage_accounting_password');
      $CI = &get_instance();
      $password = $CI->encryption->decrypt($value);

      $authorization = base64_encode($username.':'.$password);

      $header = array(
        'Content-Type: application/json',
        'Authorization: Basic '. $authorization);
    }

    return $header;
}

function acc_get_sage_accounting_api_domain($region = ''){
    $api_domain = "https://api.accounting.sage.com/v3.1";

    if(get_option('acc_integration_sage_accounting_region') == 'south_african' || $region == 'south_african'){
      $api_domain = "https://accounting.sageone.co.za/api/2.0.0";
    }

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


function acc_get_sage_accounting_api_url($type, $page = ''){
  if(get_option('acc_integration_sage_accounting_region') == 'south_african'){
    $url = acc_get_sage_accounting_api_url_south_african($type, $page);
  }else{
    $url = acc_get_sage_accounting_api_url_central_european($type, $page);
  }

  return $url;
}

function acc_get_sage_accounting_api_url_south_african($type, $page = ''){
  $api_domain = acc_get_sage_accounting_api_domain();
  $api_key = get_option('acc_integration_sage_accounting_api_key');

  switch ($type) {
      case 'customer_list':
          $url = $api_domain . '/customer/get?apikey='.$api_key;
      break;

      case 'customer':
          $url = $api_domain . '/customer/save?apikey='.$api_key;
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

  return $api_domain;
}

function acc_get_sage_accounting_api_url_central_european($type, $param = '', $page = ''){
    $api_domain = acc_get_sage_accounting_api_domain();

    switch ($type) {
        case 'customer_list':
            $url = $api_domain.'/contacts?attributes=all&contact_type_id=CUSTOMER&items_per_page=200&page='.$page;
        break;

        case 'customer':
            $url = $api_domain.'/contacts'.$param;
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

    return $api_domain;
}

/**
 * get status modules wh
 * @param  string $module_name 
 * @return boolean             
 */
if(!function_exists('acc_get_status_modules')){
  function acc_get_status_modules($module_name){
    $CI             = &get_instance();

    $sql = 'select * from '.db_prefix().'modules where module_name = "'.$module_name.'" AND active =1 ';
    $module = $CI->db->query($sql)->row();
    if($module){
      return true;
    }else{
      return false;
    }
  }
}

/**
 * [get_item_code_by_name description]
 * @param  string $name [description]
 * @return [type]       [description]
 */

if(!function_exists('get_item_code_by_name')){
  function get_item_code_by_name($name = '')
  {
      $CI           = & get_instance();
      $item_code = '';
      $CI->db->where('description', $name);

      $item =  $CI->db->get(db_prefix() . 'items')->row();
      if($item){
          $item_code = $item->commodity_code;
      }
       return $item_code;
  }
}
/**
 * [get_item_by_name description]
 * @param  string $name [description]
 * @return [type]       [description]
 */

if(!function_exists('get_item_by_name')){
  function get_item_by_name($name = '')
  {
      $CI           = & get_instance();
      $CI->db->where('description', $name);

      $item =  $CI->db->get(db_prefix() . 'items')->row();
      if($item && $item->commodity_code == ''){
        $CI->load->model('sage_accounting_integration/sage_accounting_integration_model');
        $item_code = $CI->sage_accounting_integration_model->generate_commodity_code();
        
        $item->commodity_code = $item_code;

        $CI->db->where('id', $item->id);
        $CI->db->update(db_prefix() . 'items', ['commodity_code' => $item_code]);
      }
      return $item;
  }
}