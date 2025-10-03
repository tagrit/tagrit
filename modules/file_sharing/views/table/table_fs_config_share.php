<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [ 
    'id',
    '(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ", ") FROM '.db_prefix().'fs_setting_configuration_relationship JOIN '.db_prefix().'staff ON '.db_prefix().'staff.staffid = '.db_prefix().'fs_setting_configuration_relationship.rel_id WHERE configuration_id='.db_prefix().'fs_setting_configuration.id and '.db_prefix().'fs_setting_configuration_relationship.rel_type = "staff" ORDER BY '.db_prefix().'fs_setting_configuration_relationship.rel_id) as staff',
    '(SELECT GROUP_CONCAT('.db_prefix().'roles.name SEPARATOR ", ") FROM '.db_prefix().'fs_setting_configuration_relationship JOIN '.db_prefix().'roles ON '.db_prefix().'roles.roleid = '.db_prefix().'fs_setting_configuration_relationship.rel_id WHERE configuration_id='.db_prefix().'fs_setting_configuration.id and '.db_prefix().'fs_setting_configuration_relationship.rel_type = "role" ORDER BY '.db_prefix().'fs_setting_configuration_relationship.rel_id) as role',
    'type',
    'is_read',
    'is_upload',
    'is_download',
    'is_delete',
    'is_write',
    'min_size',
    'max_size',
    'created_at',
    'inserted_at',
];
$sIndexColumn = 'id';
$sTable       = db_prefix().'fs_setting_configuration';
$join         = [];
$where = [];
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['(SELECT GROUP_CONCAT('.db_prefix().'clients.company SEPARATOR ", ") FROM '.db_prefix().'fs_setting_configuration_relationship JOIN '.db_prefix().'clients ON '.db_prefix().'clients.userid = '.db_prefix().'fs_setting_configuration_relationship.rel_id WHERE configuration_id='.db_prefix().'fs_setting_configuration.id and '.db_prefix().'fs_setting_configuration_relationship.rel_type = "customer" ORDER BY '.db_prefix().'fs_setting_configuration_relationship.rel_id) as customer', 
    '(SELECT GROUP_CONCAT('.db_prefix().'customers_groups.name SEPARATOR ", ") FROM '.db_prefix().'fs_setting_configuration_relationship JOIN '.db_prefix().'customers_groups ON '.db_prefix().'customers_groups.id = '.db_prefix().'fs_setting_configuration_relationship.rel_id WHERE configuration_id='.db_prefix().'fs_setting_configuration.id and '.db_prefix().'fs_setting_configuration_relationship.rel_type = "customer_group" ORDER BY '.db_prefix().'fs_setting_configuration_relationship.rel_id) as customer_group',
    '(SELECT GROUP_CONCAT(rel_id SEPARATOR ",") FROM '.db_prefix().'fs_setting_configuration_relationship WHERE configuration_id= '.db_prefix().'fs_setting_configuration.id and '.db_prefix().'fs_setting_configuration_relationship.rel_type = "customer") as customer_id',
    '(SELECT GROUP_CONCAT(rel_id SEPARATOR ",") FROM '.db_prefix().'fs_setting_configuration_relationship WHERE configuration_id= '.db_prefix().'fs_setting_configuration.id and '.db_prefix().'fs_setting_configuration_relationship.rel_type = "customer_group") as customers_group_id',
    '(SELECT GROUP_CONCAT(rel_id SEPARATOR ",") FROM '.db_prefix().'fs_setting_configuration_relationship WHERE configuration_id= '.db_prefix().'fs_setting_configuration.id and '.db_prefix().'fs_setting_configuration_relationship.rel_type = "staff") as staff_id',
    '(SELECT GROUP_CONCAT(rel_id SEPARATOR ",") FROM '.db_prefix().'fs_setting_configuration_relationship WHERE configuration_id='.db_prefix().'fs_setting_configuration.id and '.db_prefix().'fs_setting_configuration_relationship.rel_type = "role") as role_id',
    ]);
$output  = $result['output'];
$rResult = $result['rResult'];

$switch = new switchclass;

foreach ($rResult as $aRow) {
    $row = [];
    if($aRow['type'] == "fs_staff"){
        $row[] = $aRow['staff'];
        $row[] = $aRow['role'];
    }else {
        $row[] = $aRow['customer'];
        $row[] = $aRow['customer_group'];
    }
    $row[] = _l($aRow['type']);
    $row[] = $switch->switch_html($aRow['is_read'], 'is_read', $aRow['id']);
    $row[] = $switch->switch_html($aRow['is_write'], 'is_write', $aRow['id']);
    $row[] = $switch->switch_html($aRow['is_delete'], 'is_delete', $aRow['id']);
    $row[] = $switch->switch_html($aRow['is_upload'], 'is_upload', $aRow['id']);
    $row[] = $switch->switch_html($aRow['is_download'], 'is_download', $aRow['id']);
    // $row[] = _dt($aRow['expiration_date']);
    $row[] = $aRow['min_size'] . ' ' . _l('fs_mb') ;
    $row[] = $aRow['max_size'] . ' ' . _l('fs_mb');
    // $row[] = '<input type="password" class="password" value="'.$aRow['password'].'" disabled />';
    $row[] = get_staff_full_name($aRow['created_at']);
    $row[] = _d(date('Y-m-d', strtotime($aRow['inserted_at'])));
    $option = '';
    $option .= '<a 
    href="#" 
    class="btn btn-default btn-icon edit-config" 
    data-id="'.$aRow['id'].'" 
    data-type="'.$aRow['type'].'" 
    data-role-id="'.$aRow['role_id'].'" 
    data-staff-id="'.$aRow['staff_id'].'" 
    data-customer-id="'.$aRow['customer_id'].'" 
    data-customer-group-id="'.$aRow['customers_group_id'].'" 
    data-read="'.$aRow['is_read'].'" 
    data-write="'.$aRow['is_write'].'" 
    data-delete="'.$aRow['is_delete'].'" 
    data-upload="'.$aRow['is_upload'].'" 
    data-download="'.$aRow['is_download'].'" 
    data-min-size="'.$aRow['min_size'].'" 
    data-max-size="'.$aRow['max_size'].'" 
    >';


    $option .= '<i class="fa fa-edit"></i>';
    $option .= '</a>';
    $option .= '<a href="' . admin_url('file_sharing/delete_config/'.$aRow['id']) . '" class="btn btn-danger btn-icon _delete">';
    $option .= '<i class="fa fa-remove"></i>';
    $option .= '</a>';
    $row[] = $option; 
    
    $output['aaData'][] = $row;

}
