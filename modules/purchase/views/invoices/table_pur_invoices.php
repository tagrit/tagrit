<?php

defined('BASEPATH') or exit('No direct script access allowed');

$custom_fields = get_custom_fields('pur_invoice', [
    'show_on_table' => 1,
    ]);


$aColumns = [
    'invoice_number',
    'vendor_invoice_number',
    db_prefix().'pur_invoices.vendor',
    'contract',
    db_prefix().'pur_invoices.pur_order',
    'invoice_date',
    'is_recurring_from',
    'subtotal',
    'tax', 
    'total',
    'payment_request_status',
    'payment_status',
    'transactionid',
    'vendor_note'
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'pur_invoices';
$join         = [ 'LEFT JOIN '.db_prefix().'pur_contracts ON '.db_prefix().'pur_contracts.id = '.db_prefix().'pur_invoices.contract' ];

$i = 0;
foreach ($custom_fields as $field) {
    $select_as = 'cvalue_' . $i;
    if ($field['type'] == 'date_picker' || $field['type'] == 'date_picker_time') {
        $select_as = 'date_picker_cvalue_' . $i;
    }
    array_push($aColumns, 'ctable_' . $i . '.value as ' . $select_as);
    array_push($join, 'LEFT JOIN '.db_prefix().'customfieldsvalues as ctable_' . $i . ' ON '.db_prefix().'pur_invoices.id = ctable_' . $i . '.relid AND ctable_' . $i . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $i . '.fieldid=' . $field['id']);
    $i++;
}


$where = [];


if ($this->ci->input->post('from_date')
    && $this->ci->input->post('from_date') != '') {
    array_push($where, 'AND invoice_date >= "'.to_sql_date($this->ci->input->post('from_date')).'"');
}

if(isset($vendor)){
    array_push($where, ' AND '.db_prefix().'pur_invoices.vendor = '.$vendor);
}


if ($this->ci->input->post('to_date')
    && $this->ci->input->post('to_date') != '') {
    array_push($where, 'AND invoice_date <= "'.to_sql_date($this->ci->input->post('to_date')).'"');
}

if(!has_permission('purchase_invoices', '', 'view')){
    array_push($where, 'AND (' . db_prefix() . 'pur_invoices.add_from = '.get_staff_user_id().' OR ' . db_prefix() . 'pur_invoices.vendor IN (SELECT vendor_id FROM ' . db_prefix() . 'pur_vendor_admin WHERE staff_id=' . get_staff_user_id() . '))');
}

$contract = $this->ci->input->post('contract');
if (isset($contract)) {
    $where_contract = '';
    foreach ($contract as $t) {
        if ($t != '') {
            if ($where_contract == '') {
                $where_contract .= ' AND ('.db_prefix().'pur_invoices.contract = "' . $t . '"';
            } else {
                $where_contract .= ' or '.db_prefix().'pur_invoices.contract = "' . $t . '"';
            }
        }
    }
    if ($where_contract != '') {
        $where_contract .= ')';
        array_push($where, $where_contract);
    }
}

$pur_orders = $this->ci->input->post('pur_orders');
if (isset($pur_orders)) {
    $where_pur_orders = '';
    foreach ($pur_orders as $t) {
        if ($t != '') {
            if ($where_pur_orders == '') {
                $where_pur_orders .= ' AND ('.db_prefix().'pur_invoices.pur_order = "' . $t . '"';
            } else {
                $where_pur_orders .= ' or '.db_prefix().'pur_invoices.pur_order = "' . $t . '"';
            }
        }
    }
    if ($where_pur_orders != '') {
        $where_pur_orders .= ')';
        array_push($where, $where_pur_orders);
    }
}

$vendors = $this->ci->input->post('vendors');
if (isset($vendors)) {
    $where_vendors = '';
    foreach ($vendors as $t) {
        if ($t != '') {
            if ($where_vendors == '') {
                $where_vendors .= ' AND ('.db_prefix().'pur_invoices.vendor = "' . $t . '"';
            } else {
                $where_vendors .= ' or '.db_prefix().'pur_invoices.vendor = "' . $t . '"';
            }
        }
    }
    if ($where_vendors != '') {
        $where_vendors .= ')';
        array_push($where, $where_vendors);
    }
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix().'pur_invoices.id as id','(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM ' . db_prefix() . 'taggables JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id WHERE rel_id = ' . db_prefix() . 'pur_invoices.id and rel_type="pur_invoice" ORDER by tag_order ASC) as tags', 'contract_number', 'invoice_number', 'currency'
]);

$output  = $result['output'];
$rResult = $result['rResult'];

$this->ci->load->model('purchase/purchase_model');

foreach ($rResult as $aRow) {
    $row = [];

   for ($i = 0; $i < count($aColumns); $i++) {

        $base_currency = get_base_currency_pur();
        if($aRow['currency'] != 0){
            $base_currency = pur_get_currency_by_id($aRow['currency']);
        }

        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if($aColumns[$i] == 'invoice_number'){
            $numberOutput = '';
    
            $numberOutput = '<a href="' . admin_url('purchase/purchase_invoice/' . $aRow['id']) . '"  >'.$aRow['invoice_number']. '</a>';
            
            $numberOutput .= '<div class="row-options">';

            if (has_permission('purchase_invoices', '', 'view') || has_permission('purchase_invoices', '', 'view_own')) {
                $numberOutput .= ' <a href="' . admin_url('purchase/purchase_invoice/' . $aRow['id']) . '" >' . _l('view') . '</a>';
            }
            if ((has_permission('purchase_invoices', '', 'edit') || is_admin()) ) {
                $numberOutput .= ' | <a href="' . admin_url('purchase/pur_invoice/' . $aRow['id']) . '">' . _l('edit') . '</a>';
            }
            if (has_permission('purchase_invoices', '', 'delete') || is_admin()) {
                $numberOutput .= ' | <a href="' . admin_url('purchase/delete_pur_invoice/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
            }
            $numberOutput .= '</div>';

            $_data = $numberOutput;
        }else if($aColumns[$i] == 'vendor_invoice_number'){
            if($aRow['vendor_invoice_number'] != ''){
                $_data = $aRow['vendor_invoice_number'];
            }else{
                $_data = $aRow['invoice_number'];
            }

        }elseif($aColumns[$i] == 'vendor_note'){
            $_data = render_tags($aRow['tags']);
        }elseif($aColumns[$i] == 'invoice_date'){
            $_data = _d($aRow['invoice_date']);
        }elseif($aColumns[$i] == 'subtotal'){
            $_data = app_format_money($aRow['subtotal'],$base_currency->symbol);
        }elseif($aColumns[$i] == 'tax'){
            $tax = $this->ci->purchase_model->get_html_tax_pur_invoice($aRow['id']);
            $total_tax = 0;
            foreach($tax['taxes_val'] as $tax_val){
                $total_tax += $tax_val;
            }

            $_data = app_format_money($total_tax,$base_currency->symbol);
        }elseif($aColumns[$i] == 'total'){
            $_data = app_format_money($aRow['total'],$base_currency->symbol);
        }elseif($aColumns[$i] == 'payment_status'){
            $class = '';
            if($aRow['payment_status'] == 'unpaid'){
                $class = 'danger';
            }elseif($aRow['payment_status'] == 'paid'){
                $class = 'success';
            }elseif ($aRow['payment_status'] == 'partially_paid') {
                $class = 'warning';
            }

            $_data = '<span class="label label-'.$class.' s-status invoice-status-3">'._l($aRow['payment_status']).'</span>';
        }elseif($aColumns[$i] == 'contract'){
            $_data = '<a href="'.admin_url('purchase/contract/'.$aRow['contract']).'">'.$aRow['contract_number'].'</a>';
        }elseif($aColumns[$i] == 'payment_request_status'){
            $_data = get_payment_request_status_by_inv($aRow['id']);
        }elseif($aColumns[$i] == db_prefix().'pur_invoices.pur_order'){
            $_data = '<a href="'.admin_url('purchase/purchase_order/'.$aRow[db_prefix().'pur_invoices.pur_order']).'">'.get_pur_order_subject($aRow[ db_prefix().'pur_invoices.pur_order']).'</a>';
        }elseif($aColumns[$i] == 'is_recurring_from'){
            $_data = ' <a href="' . admin_url('purchase/purchase_invoice/' . $aRow['is_recurring_from']) . '" >' . get_pur_invoice_number($aRow['is_recurring_from']) . '</a>'; 
        }elseif($aColumns[$i] == db_prefix().'pur_invoices.vendor'){
            $_data = '<a href="' . admin_url('purchase/vendor/' . $aRow[db_prefix().'pur_invoices.vendor']) . '" >' .  get_vendor_company_name($aRow[db_prefix().'pur_invoices.vendor']) . '</a>'; 
        }else {
            if (strpos($aColumns[$i], 'date_picker_') !== false) {
                $_data = (strpos($_data, ' ') !== false ? _dt($_data) : _d($_data));
            }
        }

        $row[] = $_data;
    }
    $output['aaData'][] = $row;

}
