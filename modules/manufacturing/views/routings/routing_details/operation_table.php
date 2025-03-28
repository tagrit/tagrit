<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
	'id',
	'display_order',
	'operation',
	'work_center_id',
	'duration_computation',
];
$sIndexColumn = 'id';
$sTable = db_prefix() . 'mrp_routing_details';

$where = [];
$join= [];

$routing_id = $this->ci->input->post('routing_id');
if($this->ci->input->post('routing_id')){
	$where_routing_id = '';
	$routing_id = $this->ci->input->post('routing_id');
	if($routing_id != '')
	{
		if($where_routing_id == ''){
			$where_routing_id .= 'AND routing_id = "'.$routing_id. '"';
		}else{
			$where_routing_id .= ' or routing_id = "' .$routing_id.'"';
		}
	}
	if($where_routing_id != '')
	{
		array_push($where, $where_routing_id);
	}
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['id', 'based_on', 'default_duration']);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];

	for ($i = 0; $i < count($aColumns); $i++) {

		if($aColumns[$i] == 'id') {
			$_data = $aRow['id'];

		}elseif($aColumns[$i] == 'display_order'){
			$_data = round($aRow['display_order'],0);

		}elseif ($aColumns[$i] == 'operation') {

			$code = $aRow['operation'] ;
			$code .= '<div class="row-options">';

			if (has_permission('manufacturing', '', 'edit') || is_admin()) {

				$code .= ' <a href="#" onclick="add_operation('. $routing_id .','. $aRow['id'] .',\'updated\'); return false;" >' . _l('edit') . '</a>';
			}
			if (has_permission('manufacturing', '', 'delete') || is_admin()) {
				$code .= ' | <a href="' . admin_url('manufacturing/delete_operation/' . $aRow['id']) . '/'.$routing_id.'" class="text-danger _delete">' . _l('delete') . '</a>';
			}
			$code .= '</div>';

			$_data = $code;


		}elseif($aColumns[$i] == 'work_center_id'){
			$_data =  get_work_center_name($aRow['work_center_id']);

		}elseif($aColumns[$i] == 'duration_computation'){
			if($aRow['duration_computation'] == 'set_duration_manually'){
				$_data =  round($aRow['default_duration'],0);
			}else{
				$_data =  round($aRow['based_on'],0);
			}

		}


		$row[] = $_data;
	}

	$output['aaData'][] = $row;
}

