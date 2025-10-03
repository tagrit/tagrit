<?php

defined('BASEPATH') or exit('No direct script access allowed');
$company = get_default_company();

$this->ci->load->model('currencies_model');
$this->ci->load->model('accounting/accounting_model');
$currency = $this->ci->currencies_model->get_base_currency();
$accounts = $this->ci->accounting_model->get_accounts();
$account_name = [];
foreach ($accounts as $key => $value) {
    $account_name[$value['id']] = $value['name'];
}

$aColumns = [
    db_prefix() . 'acc_checks.date as date',
    db_prefix() . 'acc_checks.number as number',
    'rel_id',
    db_prefix() . 'acc_checks.amount as amount',
    db_prefix() . 'acc_checks.reason_for_void as reason_for_void',
    db_prefix() . 'acc_checks.issue as issue',
];

$join = [
];
$where  = [];
$filter = [];


$bank_account_check = '';
if ($this->ci->input->post('bank_account_check')) {
    $bank_account_check = $this->ci->input->post('bank_account_check');
    array_push($where, 'AND (' . db_prefix() . 'acc_checks.bank_account = "'.$bank_account_check.'" or ' . db_prefix() . 'acc_checks.bank_account is null)');
}else{
    array_push($where, 'AND ' . db_prefix() . 'acc_checks.bank_account = "-1"');
}

$from_date = '';
$to_date = '';
if ($this->ci->input->post('from_date')) {
    $from_date = $this->ci->input->post('from_date');
    if (!$this->ci->accounting_model->check_format_date($from_date)) {
        $from_date = to_sql_date($from_date);
    }
}

if ($this->ci->input->post('to_date')) {
    $to_date = $this->ci->input->post('to_date');
    if (!$this->ci->accounting_model->check_format_date($to_date)) {
        $to_date = to_sql_date($to_date);
    }
}
if ($from_date != '' && $to_date != '') {
    array_push($where, 'AND (' . db_prefix() . 'acc_checks.date >= "' . $from_date . '" and ' . db_prefix() . 'acc_checks.date <= "' . $to_date . '")');
} elseif ($from_date != '') {
    array_push($where, 'AND (' . db_prefix() . 'acc_checks.date >= "' . $from_date . '")');
} elseif ($to_date != '') {
    array_push($where, 'AND (' . db_prefix() . 'acc_checks.date <= "' . $to_date . '")');
}

array_push($where, 'AND issue = 3');

$type = '';

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'acc_checks';

array_push($where, 'AND '.db_prefix().'acc_checks.company = '.$company);

// Fix for big queries. Some hosting have max_join_limit

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['bank_account', 'id'
   
]);
$output  = $result['output'];
$rResult = $result['rResult'];

$this->ci->load->model('payment_modes_model');

foreach ($rResult as $aRow) {
    $row = [];

    $row[] = _d($aRow['date']);

    $row[]    = '#'.str_pad($aRow['number'], 4, '0', STR_PAD_LEFT);

    $categoryOutput = '';

    $categoryOutput = '<a href="'.admin_url('accounting/checks#'.$aRow['id']).'">' . acc_get_vendor_name($aRow['rel_id']) . '</a>';
                
    $row[] = $categoryOutput;


    $total    = $aRow['amount'];

    $row[] = app_format_money($total, $currency->name);
    $row[]  = $aRow['reason_for_void'];

    if($aRow['issue'] == 1){
        $label_class = 'success';
        $status_name = _l('issued');
    }elseif($aRow['issue'] == 2){
        $label_class = 'danger';
        $status_name = _l('printing_error');
    }elseif($aRow['issue'] == 3){
        $status_name = _l('void');
        $label_class = 'danger';
    }else{
        $status_name = _l('not_issued_yet');
        $label_class = 'default';
    }

    $row[] = '<span class="label label-' . $label_class . ' s-status">' . $status_name . '</span>';

    $row['DT_RowClass'] = 'has-row-options';

    $output['aaData'][] = $row;
}
