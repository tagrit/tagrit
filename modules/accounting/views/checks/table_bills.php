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
    db_prefix() . 'expenses.id as id',
    'due_date',
    db_prefix() . 'expenses.amount as amount',
    db_prefix() . 'expenses.date as date',
];

$join = [
    'LEFT JOIN ' . db_prefix() . 'acc_check_details ON ' . db_prefix() . 'acc_check_details.bill = ' . db_prefix() . 'expenses.id',
    'LEFT JOIN ' . db_prefix() . 'acc_checks ON ' . db_prefix() . 'acc_checks.id = ' . db_prefix() . 'acc_check_details.check_id',
];
$where  = [];
$filter = [];

if (!has_permission('accounting_bills', '', 'view')) {
    array_push($where, 'AND ' . db_prefix() . 'expenses.addedfrom=' . get_staff_user_id());
}

$bank_account_check = '';
if ($this->ci->input->post('bank_account_check')) {
    $bank_account_check = $this->ci->input->post('bank_account_check');
    array_push($where, 'AND (' . db_prefix() . 'acc_checks.bank_account = "'.$bank_account_check.'" or ' . db_prefix() . 'acc_checks.bank_account is null)');
}

$type = '';

array_push($where, 'AND ((' . db_prefix() . 'expenses.approved = 1 AND ' . db_prefix() . 'expenses.voided = 0 AND ' . db_prefix() . 'expenses.status != 2) OR check_id = "") ');

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'expenses';

// Fix for big queries. Some hosting have max_join_limit

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    '(SELECT GROUP_CONCAT(account SEPARATOR ",") FROM '.db_prefix().'acc_bill_mappings WHERE bill_id = '.db_prefix().'expenses.id and type = "debit") as account_ids', 'check_id', 'status', 'vendor'
]);
$output  = $result['output'];
$rResult = $result['rResult'];

$this->ci->load->model('payment_modes_model');

foreach ($rResult as $aRow) {
    $row = [];

    $categoryOutput = '';

    $categoryOutput = '<div class="pull-left"><a href="' . admin_url('accounting/bills/' . $aRow['id']) . '" onclick="init_bills(' . $aRow['id'] . ');return false;">' . acc_get_vendor_name($aRow['vendor']) . '</a></div>';
        
    $categoryOutput .= '<div class="row-options pull-right">';

    if($aRow['check_id'] != ''){
        $categoryOutput .= '<a href="#" class="pull-right" onclick="init_checks('.$aRow['check_id'].'); return false;">' . _l('open_check') . '</a>';
    }else{
        $categoryOutput .= '<a href="' . admin_url('accounting/check?bill=' . $aRow['id'].'&bank_account_check='.$bank_account_check) . '" class="pull-right">' . _l('create_new_check') . '</a>';
    }

    $categoryOutput .= '</div>';
                
    $row[] = $categoryOutput;

    $row[] = _d($aRow['due_date']);

    $accountsRow = '';
    if ($aRow['account_ids']) {
        $accounts = explode(',', $aRow['account_ids']);
        foreach ($accounts as $account) {
            $name = (isset($account_name[$account]) ? $account_name[$account] : '');
            if($accountsRow == ''){
                $accountsRow .= $name;
            }else{
                $accountsRow .= ', '.$name;
            }
        }
    }

    $row[] = $accountsRow;

    $total    = $aRow['amount'];

    $row[] = app_format_money($total, $currency->name);

    $row[] = bill_status_html($aRow['id']);

    $row['DT_RowClass'] = 'has-row-options';

    $output['aaData'][] = $row;
}
