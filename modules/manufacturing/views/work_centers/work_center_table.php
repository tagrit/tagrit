<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
	'id',
	'work_center_code',
	'work_center_name',
	'working_hours',
];
$sIndexColumn = 'id';
$sTable = db_prefix() . 'mrp_work_centers';

$where = [];
$join= [];


$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['id']);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];

	for ($i = 0; $i < count($aColumns); $i++) {

		if($aColumns[$i] == 'id') {
			$_data = $aRow['id'];

		}elseif ($aColumns[$i] == 'work_center_code') {
			$code = '<a href="' . admin_url('manufacturing/view_work_center/' . $aRow['id']) . '">' . $aRow['work_center_code'] . '</a>';
			$code .= '<div class="row-options">';

			$code .= '<a href="' . admin_url('manufacturing/view_work_center/' . $aRow['id']) . '" >' . _l('view') . '</a>';

			if (has_permission('manufacturing', '', 'edit') || is_admin()) {

				$code .= ' | <a href="' . admin_url('manufacturing/add_edit_work_center/' . $aRow['id']) . '" >' . _l('edit') . '</a>';
			}
			if (has_permission('manufacturing', '', 'delete') || is_admin()) {
				$code .= ' | <a href="' . admin_url('manufacturing/delete_work_center/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
			}
			$code .= '</div>';

			$_data = $code;


		}elseif($aColumns[$i] == 'work_center_name'){
			$_data =  $aRow['work_center_name'];

		}elseif($aColumns[$i] == 'working_hours'){
			$working_hours_name = '';

			if($aRow['working_hours'] != '' && $aRow['working_hours'] != null && $aRow['working_hours'] != 0){
				$working_hour = $this->ci->manufacturing_model->get_working_hour($aRow['working_hours']);
				if($working_hour['working_hour']){
					$working_hours_name .= $working_hour['working_hour']->working_hour_name;
				}
			}

			$_data =  $working_hours_name;

		}


		$row[] = $_data;
	}

	$output['aaData'][] = $row;
}

