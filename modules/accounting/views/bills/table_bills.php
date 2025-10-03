<?php

defined('BASEPATH') or exit('No direct script access allowed');

$this->ci->load->model('currencies_model');
$this->ci->load->model('accounting/accounting_model');
$currency = $this->ci->currencies_model->get_base_currency();


$aColumns = [
    db_prefix() . 'expenses.id as id',
    db_prefix() . 'expenses.id as id',
    db_prefix() . 'pur_vendor.company as vendor_name',
    db_prefix() . 'expenses.date as date',
    db_prefix() . 'expenses.due_date as due_date',
    db_prefix() . 'expenses.date_paid as date_paid',
    db_prefix() . 'expenses.amount as amount',
    '(SELECT GROUP_CONCAT(check_id SEPARATOR ",") FROM '.db_prefix().'acc_check_details WHERE ' . db_prefix() . 'acc_check_details.bill = '.db_prefix().'expenses.id) as check_ids',
    db_prefix() . 'expenses.status as status'
];
$join = [
    'LEFT JOIN ' . db_prefix() . 'pur_vendor ON ' . db_prefix() . 'pur_vendor.userid = ' . db_prefix() . 'expenses.vendor',
];

$where  = [];
$filter = [];
//include_once(APPPATH . 'views/admin/tables/includes/expenses_filter.php');

if (!has_permission('accounting_bills', '', 'view')) {
    array_push($where, 'AND ' . db_prefix() . 'expenses.addedfrom=' . get_staff_user_id());
}

$type = '';
if ($this->ci->input->post('type')) {
    $type = $this->ci->input->post('type');
    switch ($type) {
        case 'unpaid':
        array_push($where, 'AND ' . db_prefix() . 'expenses.approved = 0');
        break;
        case 'paid':
        array_push($where, 'AND (' . db_prefix() . 'expenses.status = 2 or ' . db_prefix() . 'expenses.voided = 1 or ' . db_prefix() . 'expenses.status = 3)');
        break;
        case 'approved':
        array_push($where, 'AND ' . db_prefix() . 'expenses.approved = 1');
        array_push($where, 'AND ' . db_prefix() . 'expenses.voided = 0');
        array_push($where, 'AND ' . db_prefix() . 'expenses.status != 2');
        break;
        case 'voided':
        array_push($where, 'AND ' . db_prefix() . 'expenses.voided = 1');
        break;
        default:
        array_push($where, 'AND ' . db_prefix() . 'expenses.approved = 0');
        break;
    }
}else{
    array_push($where, 'AND ' . db_prefix() . 'expenses.approved = 0');

}

if ($this->ci->input->post('vendor_id') && $this->ci->input->post('vendor_id') != '') {
    $vendor_id = $this->ci->input->post('vendor_id');
   
    array_push($where, 'AND '.db_prefix().'pur_vendor.userid IN (' . implode(', ', $vendor_id) . ')');
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
    array_push($where, 'AND (' . db_prefix() . 'expenses.date >= "' . $from_date . '" and ' . db_prefix() . 'expenses.date <= "' . $to_date . '")');
} elseif ($from_date != '') {
    array_push($where, 'AND (' . db_prefix() . 'expenses.date >= "' . $from_date . '")');
} elseif ($to_date != '') {
    array_push($where, 'AND (' . db_prefix() . 'expenses.date <= "' . $to_date . '")');
}

array_push($where, 'AND ' . db_prefix() . 'expenses.is_bill = 1');


$sIndexColumn = 'id';
$sTable       = db_prefix() . 'expenses';

// Fix for big queries. Some hosting have max_join_limit

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
     'vendor', db_prefix().'expenses.approved'
]);
$output  = $result['output'];
$rResult = $result['rResult'];

$this->ci->load->model('payment_modes_model');

foreach ($rResult as $aRow) {
    $row = [];
    $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '" data-vendor="'.$aRow['vendor'].'" name="bill_'.$aRow['id'].'" id="bill_'.$aRow['id'].'"><label for="bill_'.$aRow['id'].'"></label></div>';
    $row[] = $aRow['id'];

    $categoryOutput = '';

    $categoryOutput = '<a href="' . admin_url('accounting/bills/' . $aRow['id']) . '" onclick="init_bills(' . $aRow['id'] . ');return false;">' . $aRow['vendor_name'] . '</a>';

        switch ($type) {
            case 'unpaid':
                $categoryOutput .= '<div class="row-options ">';
                $categoryOutput .= '<a href="#" onclick="init_bills(' . $aRow['id'] . ');return false;" class="">' . _l('acc_open') . '</a>';

                $categoryOutput .= ' | <a href="#" class="" onclick="approve_payable('.$aRow['id'].'); return false;">' . _l('approve_payable') . '</a>';
                if (has_permission('accounting_bills', '', 'edit')) {
                    $categoryOutput .= ' | <a href="' . admin_url('accounting/bill/' . $aRow['id']) . '" class="">' . _l('edit') . '</a>';
                }

                if (has_permission('accounting_bills', '', 'delete')) {
                    $categoryOutput .= ' | <a href="#" class="text-danger " onclick="delete_bill('.$aRow['id'].'); return false;">' . _l('delete') . '</a>';
                }


                $categoryOutput .= '</div>';
                break;
            case 'approved':
                $categoryOutput .= '<div class="row-options ">';

                $categoryOutput .= '<a href="#" onclick="init_bills(' . $aRow['id'] . ');return false;" class="">' . _l('acc_open') . '</a>';


                if (has_permission('accounting_bills', '', 'edit')) {
                    $categoryOutput .= ' | <a href="' . admin_url('accounting/pay_bill?bill=' . $aRow['id']) . '" class="">' . _l('pay_bill') . '</a>';
                }

                if (has_permission('accounting_bills', '', 'delete')) {
                    $categoryOutput .= ' | <a href="#" class="text-danger " onclick="delete_bill('.$aRow['id'].'); return false;">' . _l('delete') . '</a>';
                }
                $categoryOutput .= '</div>';
                break;
            case 'paid':
                $categoryOutput .= '<div class="row-options ">';

                $categoryOutput .= ' <a href="#" onclick="init_bills(' . $aRow['id'] . ');return false;" class="">' . _l('acc_open') . '</a>';

                $categoryOutput .= '</div>';
                break;
            case '':
                $categoryOutput .= '<div class="row-options ">';

                $categoryOutput .= '<a href="#" onclick="init_bills(' . $aRow['id'] . ');return false;" class="">' . _l('acc_open') . '</a>';

                $categoryOutput .= ' | <a href="#" onclick="approve_payable('.$aRow['id'].'); return false;" class="">' . _l('approve_payable') . '</a>';
                
                if (has_permission('accounting_bills', '', 'edit')) {
                    $categoryOutput .= ' | <a href="' . admin_url('accounting/bill/' . $aRow['id']) . '" class="">' . _l('edit') . '</a>';
                }

                if (has_permission('accounting_bills', '', 'delete')) {
                    $categoryOutput .= ' | <a href="#" class="text-danger " onclick="delete_bill('.$aRow['id'].'); return false;">' . _l('delete') . '</a>';
                }
                $categoryOutput .= '</div>';
                break;
            default:
                break;
        }
   
    $row[] = $categoryOutput;
    
    $row[] = _d($aRow['date']);
    $row[] = _d($aRow['due_date']);
    $row[] = _d($aRow['date_paid']);

    if($type == 'paid'){
        $bill_amount_left = bill_amount_left($aRow['id']);
        $total = round($aRow['amount'] - $bill_amount_left, 2);
        $row[] = app_format_money($total, $currency->name);
    }else{
        $row[] = bill_amount_left($aRow['id']);
    }

    $check_number = '';
    if ($aRow['check_ids']) {
        $checks = explode(',', $aRow['check_ids']);
        foreach ($checks as $check) {
            $number = acc_format_check_number($check);
            if($check_number == ''){
                $check_number .= $number;
            }else{
                $check_number .= ', '.$number;
            }
        }
    }

    $row[] = $check_number;

    $row[] = bill_status_html($aRow['id']);

    $row['DT_RowClass'] = 'has-row-options';

    $output['aaData'][] = $row;
}
