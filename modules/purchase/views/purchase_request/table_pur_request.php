<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
     
    'pur_rq_code',
    'pur_rq_name',
    'requester',
    'department', 
    'request_date',
    'status',
    'project',
    'id',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'pur_request';
$join         = [ 'LEFT JOIN '.db_prefix().'departments ON '.db_prefix().'departments.departmentid = '.db_prefix().'pur_request.department' ];
$where = [];

if ($this->ci->input->post('from_date')
    && $this->ci->input->post('from_date') != '') {
    array_push($where, 'AND request_date >= "'.$this->ci->input->post('from_date').'"');
}

if ($this->ci->input->post('to_date')
    && $this->ci->input->post('to_date') != '') {
    array_push($where, 'AND request_date <= "'.$this->ci->input->post('to_date').'"');
}

if ($this->ci->input->post('department')
    && count($this->ci->input->post('department')) > 0) {
    array_push($where, 'AND department IN (' . implode(',', $this->ci->input->post('department')) . ')');
}

if(isset($project)){
    array_push($where, ' AND '.db_prefix().'pur_request.project = '.$project);
}

if(!has_permission('purchase_request', '', 'view')){
  $or_where = '';
  $list_vendor = get_vendor_admin_list(get_staff_user_id());
  foreach($list_vendor as $vendor_id){
    $or_where .= ' OR find_in_set('.$vendor_id.', ' . db_prefix() . 'pur_request.send_to_vendors)';
  }

  array_push($where, 'AND (' . db_prefix() . 'pur_request.requester = '.get_staff_user_id().  $or_where. ' OR '.get_staff_user_id().' IN (SELECT staffid FROM ' . db_prefix() . 'pur_approval_details WHERE ' . db_prefix() . 'pur_approval_details.rel_type = "pur_request" AND ' . db_prefix() . 'pur_approval_details.rel_id = '.db_prefix().'pur_request.id))');
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['id','name','pur_rq_code']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

   for ($i = 0; $i < count($aColumns); $i++) {

        $_data = $aRow[$aColumns[$i]];
        if($aColumns[$i] == 'request_date'){
            $_data = _dt($aRow['request_date']);
        }elseif($aColumns[$i] == 'requester'){
            $_data = '<a href="' . admin_url('staff/profile/' . $aRow['requester']) . '">' . staff_profile_image($aRow['requester'], [
                'staff-profile-image-small',
                ]) . '</a>';
            $_data .= ' <a href="' . admin_url('staff/profile/' . $aRow['requester']) . '">' . get_staff_full_name($aRow['requester']) . '</a>';
        }elseif($aColumns[$i] == 'department'){
            $_data = $aRow['name'];
        }elseif ($aColumns[$i] == 'status') {
            
            $approve_status = get_status_approve($aRow['status']);

            if($aRow['status'] == 1){
                $approve_status = '<span class="label label-primary" id="status_span_'.$aRow['id'].'"> '._l('purchase_draft');
            }elseif($aRow['status'] == 2){
                $approve_status = '<span class="label label-success" id="status_span_'.$aRow['id'].'"> '._l('purchase_approved');
            }elseif($aRow['status'] == 3){
                $approve_status = '<span class="label label-warning" id="status_span_'.$aRow['id'].'"> '._l('pur_rejected');
            }elseif($aRow['status'] == 4){
                $approve_status = '<span class="label label-danger" id="status_span_'.$aRow['id'].'"> '._l('pur_canceled');
            }

            if(has_permission('purchase_request_change_approve_status', '', 'edit')){
                $approve_status .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
                $approve_status .= '<a href="#" class="dropdown-toggle text-dark" id="tablePurOderStatus-' . $aRow['id'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                $approve_status .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
                $approve_status .= '</a>';

                $approve_status .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tablePurOderStatus-' . $aRow['id'] . '">';

                if($aRow['status'] == 1){
                   
                    $approve_status .= '<li>
                              <a href="#" onclick="change_pr_approve_status( 2 ,' . $aRow['id'] . '); return false;">
                                 ' ._l('purchase_approved') . '
                              </a>
                           </li>';
                    $approve_status .= '<li>
                              <a href="#" onclick="change_pr_approve_status( 3 ,' . $aRow['id'] . '); return false;">
                                 ' ._l('pur_rejected') . '
                              </a>
                           </li>';
                }else if($aRow['status'] == 2){
                    $approve_status .= '<li>
                              <a href="#" onclick="change_pr_approve_status( 1 ,' . $aRow['id'] . '); return false;">
                                 ' ._l('purchase_draft') . '
                              </a>
                           </li>';
                   
                    $approve_status .= '<li>
                              <a href="#" onclick="change_pr_approve_status( 3 ,' . $aRow['id'] . '); return false;">
                                 ' ._l('pur_rejected') . '
                              </a>
                           </li>';

                }else if($aRow['status'] == 3) {
                   
                    $approve_status .= '<li>
                              <a href="#" onclick="change_pr_approve_status( 1 ,' . $aRow['id'] . '); return false;">
                                 ' ._l('purchase_draft') . '
                              </a>
                           </li>';
                    $approve_status .= '<li>
                              <a href="#" onclick="change_pr_approve_status( 2 ,' . $aRow['id'] . '); return false;">
                                 ' ._l('purchase_approved') . '
                              </a>
                           </li>';
                }

                $approve_status .= '</ul>';
                $approve_status .= '</div>';
            }

            $approve_status .= '</span>';

            $_data = $approve_status;

        }elseif($aColumns[$i] == 'pur_rq_name'){
            $name = '<a href="' . admin_url('purchase/view_pur_request/' . $aRow['id'] ).'">'.$aRow['pur_rq_name'] . '</a>';

            $name .= '<div class="row-options">';

            $name .= '<a href="' . admin_url('purchase/view_pur_request/' . $aRow['id'] ).'" >' . _l('view') . '</a>';

            if ( (has_permission('purchase_request', '', 'edit') || is_admin()) &&  $aRow['status'] != 2) {
                $name .= ' | <a href="' . admin_url('purchase/pur_request/' . $aRow['id'] ).'" >' . _l('edit') . '</a>';
            }

            if (has_permission('purchase_request', '', 'delete') || is_admin()) {
                $name .= ' | <a href="' . admin_url('purchase/delete_pur_request/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
            }

            $name .= '</div>';

            $_data = $name;
        }elseif($aColumns[$i] == 'id'){
            if($aRow['status'] == 2){
                $_data = '<div class="btn-group mright5" data-toggle="tooltip" title="'._l('request_quotation_tooltip').'">
                           <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><i class="fa fa-file-pdf"></i><span class="caret"></span></a>
                           <ul class="dropdown-menu dropdown-menu-right">
                              <li class="hidden-xs"><a href="'. admin_url('purchase/request_quotation_pdf/'.$aRow['id'].'?output_type=I').'">'. _l('view_pdf').'</a></li>
                              <li class="hidden-xs"><a href="'. admin_url('purchase/request_quotation_pdf/'.$aRow['id'].'?output_type=I').'" target="_blank">'. _l('view_pdf_in_new_window').'</a></li>
                              <li><a href="'.admin_url('purchase/request_quotation_pdf/'.$aRow['id']).'">'. _l('download').'</a></li>
                           </ul>
                           </div>';

                $_data .= '<a href="#" onclick="send_request_quotation('.$aRow['id'].'); return false;" class="btn btn-success" ><i class="fa fa-envelope" data-toggle="tooltip" title="'. _l('request_quotation') .'"></i></a>';

                $_data .= '<a href="#" onclick="share_request('.$aRow['id'].'); return false;" class="btn btn-primary mleft5" ><i class="fa fa-share-alt" data-toggle="tooltip" title="'. _l('share_request') .'"></i></a>';
            }else{
                $_data = '';
            }
        }elseif($aColumns[$i] == 'pur_rq_code'){
            $_data = '<a href="' . admin_url('purchase/view_pur_request/' . $aRow['id'] ).'">'.$aRow['pur_rq_code'] . '</a>';
        }elseif($aColumns[$i] == 'project'){

            $_data = get_po_html_by_pur_request($aRow['id']);
        }


        $row[] = $_data;
    }
    $output['aaData'][] = $row;

}
