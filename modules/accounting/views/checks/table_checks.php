<?php

defined('BASEPATH') or exit('No direct script access allowed');

$this->ci->load->model('currencies_model');
$this->ci->load->model('accounting/accounting_model');
$currency = $this->ci->currencies_model->get_base_currency();
$accounts = $this->ci->accounting_model->get_accounts();
$account_name = [];
foreach ($accounts as $key => $value) {
    $account_name[$value['id']] = $value['name'];
}

$aColumns = [
    db_prefix() . 'pur_vendor.company as vendor_name',
    db_prefix() . 'acc_checks.date as date',
    db_prefix() . 'acc_checks.bank_account as bank_account',
    db_prefix() . 'acc_checks.amount as amount',
    db_prefix() . 'acc_checks.number as number',
    db_prefix() . 'acc_checks.issue as issue',
];

$join = ['LEFT JOIN ' . db_prefix() . 'pur_vendor ON ' . db_prefix() . 'pur_vendor.userid = ' . db_prefix() . 'acc_checks.rel_id'
];
$where  = [];
$filter = [];


$bank_account_check = '';
if ($this->ci->input->post('bank_account_check')) {
    $bank_account_check = $this->ci->input->post('bank_account_check');
    array_push($where, 'AND (' . db_prefix() . 'acc_checks.bank_account = "'.$bank_account_check.'" or ' . db_prefix() . 'acc_checks.bank_account is null)');
}

$type = '';

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'acc_checks';

array_push($where, 'AND (issue is null or issue = 0)');

// Fix for big queries. Some hosting have max_join_limit

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['id'
   
]);
$output  = $result['output'];
$rResult = $result['rResult'];

$this->ci->load->model('payment_modes_model');

foreach ($rResult as $aRow) {
    $row = [];

    $categoryOutput = '';

    $categoryOutput = '<a href="#" onclick="init_checks(' . $aRow['id'] . ');return false;">' . $aRow['vendor_name'] . '</a>';
        
    $categoryOutput .= '<div class="row-options">';

    $categoryOutput .= '<a href="#" onclick="init_checks('.$aRow['id'].'); return false;">' . _l('open_check') . '</a>';

    $categoryOutput .= '</div>';
                
    $row[] = $categoryOutput;

    $row[] = _d($aRow['date']);

    $accountsRow = '';

    $name = (isset($account_name[$aRow['bank_account']]) ? $account_name[$aRow['bank_account']] : '');
    $accountsRow .= $name;

    $row[] = $accountsRow;

    $total    = $aRow['amount'];

    $row[] = app_format_money($total, $currency->name);

    if($aRow['issue'] == 1){
        $label_class = 'success';
        $status_name = _l('issued');
    }elseif($aRow['issue'] == 2){
        $label_class = 'danger';
        $status_name = _l('printing_error');
    }else{
        $status_name = _l('not_issued_yet');
        $label_class = 'default';
    }

    $row[]    = '#'.str_pad($aRow['number'], 4, '0', STR_PAD_LEFT);

    $row[] = '<span class="label label-' . $label_class . ' s-status">' . $status_name . '</span>';

    $row['DT_RowClass'] = 'has-row-options';

    $output['aaData'][] = $row;
}
