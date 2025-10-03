<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'stock_take_code',
    'warehouse_id',
    'date_stock_take',
    'staff_id', 
    'addedfrom', 
    'approval',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'stock_take';
$join         = [ ];
$where = [];


$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['id']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

   for ($i = 0; $i < count($aColumns); $i++) {

        $_data = $aRow[$aColumns[$i]];
        if($aColumns[$i] == 'stock_take_code2'){
            $_data = $aRow['stock_take_code'];
        }elseif($aColumns[$i] == 'buyer_id'){
            $_data = '<a href="' . admin_url('staff/profile/' . $aRow['buyer_id']) . '">' . staff_profile_image($aRow['buyer_id'], [
                'staff-profile-image-small',
                ]) . '</a>';
            $_data .= ' <a href="' . admin_url('staff/profile/' . $aRow['buyer_id']) . '">' . get_staff_full_name($aRow['buyer_id']) . '</a>';
        }elseif($aColumns[$i] == 'warehouse_id'){
            $_data = get_warehouse_name($aRow['warehouse_id']) != null ? get_warehouse_name($aRow['warehouse_id'])->warehouse_name : '';
        }elseif ($aColumns[$i] == 'date_stock_take') {
            $_data = _d($aRow['date_stock_take']);
        }elseif($aColumns[$i] == 'stock_take_code'){
            $name = '<a href="' . admin_url('warehouse/view_purchase/' . $aRow['id'] ).'" onclick="init_goods_receipt('.$aRow['id'].'); return false;">' . $aRow['stock_take_code'] . '</a>';

            $name .= '<div class="row-options">';

            $name .= '<a href="' . admin_url('warehouse/view_purchase/' . $aRow['id'] ).'" onclick="init_goods_receipt('.$aRow['id'].'); return false;">' . _l('view') . '</a>';

            if (has_permission('warehouse', '', 'edit') || is_admin()) {
                $name .= ' | <a href="' . admin_url('warehouse/edit_purchase/' . $aRow['id'] ).'" >' . _l('edit') . '</a>';
            }


            $name .= '</div>';

            $_data = $name;
        }elseif($aColumns[$i] == 'staff_id'){
            $membersOutput = '';

            $members       = json_decode($aRow['staff_id']);
            $exportMembers = '';
            foreach ($members as $key => $member) {
                if ($member != '') {
                    $members_ids = new_explode(',', $aRow['members_ids']);
                    $member_id   = $members_ids[$key];
                    $membersOutput .= '<a href="' . admin_url('profile/' . $member_id) . '">' .
                    staff_profile_image($member_id, [
                        'staff-profile-image-small mright5',
                        ], 'small', [
                        'data-toggle' => 'tooltip',
                        'data-title'  => $member,
                        ]) . '</a>';
                    // For exporting
                    $exportMembers .= $member . ', ';
                }
            }

            $membersOutput .= '<span class="hide">' . trim($exportMembers, ', ') . '</span>';
            $_data = $membersOutput;

        }elseif($aColumns[$i] == 'addedfrom'){
            $_data = 1;
        }elseif($aColumns[$i] == 'approval') {
             
             if($aRow['approval'] == 1){
                $_data = '<span class="label label-tag tag-id-1 label-tab1"><span class="tag">'._l('approved').'</span><span class="hide">, </span></span>&nbsp';
             }elseif($aRow['approval'] == 0){
                $_data = '<span class="label label-tag tag-id-1 label-tab2"><span class="tag">'._l('not_yet_approve').'</span><span class="hide">, </span></span>&nbsp';
             }elseif($aRow['approval'] == -1){
                $_data = '<span class="label label-tag tag-id-1 label-tab3"><span class="tag">'._l('reject').'</span><span class="hide">, </span></span>&nbsp';
             }
        }
   


        $row[] = $_data;
    }
    $output['aaData'][] = $row;

}
