<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'userid as userid',
    'company',
    'phonenumber as phonenumber',
    'address',
    'addedfrom',
    'datecreated',
];

$sIndexColumn = 'userid';
$sTable       = db_prefix().'pur_vendor';
$where        = [];

$join = [
];


$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['userid'] . '"><label></label></div>';

    // Company
    $company  = $aRow['company'];
    $isPerson = false;


    $url = admin_url('accounting/vendor/' . $aRow['userid']);
    
    $company = '<a href="' . $url . '">' . $company . '</a>';

    $company .= '<div class="row-options ">';
    
    $company .= '<a href="' . $url . '" class="">' . _l('view') . '</a>';
    $company .= ' | <a href="' . admin_url('accounting/delete_vendor/' . $aRow['userid']) . '" class="text-danger _delete ">' . _l('delete') . '</a>';
    $company .= '</div>';

    $row[] = $company;

    $row[] = ($aRow['phonenumber'] ? '<a href="tel:' . $aRow['phonenumber'] . '">' . $aRow['phonenumber'] . '</a>' : '');

    $row[] = $aRow['address'];

    $row[] = get_staff_full_name($aRow['addedfrom']);
    $row[] = _dt($aRow['datecreated']);


    $row['DT_RowClass'] = 'has-row-options';

    $output['aaData'][] = $row;
}
