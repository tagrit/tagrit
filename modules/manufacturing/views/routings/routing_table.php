<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
	'id',
	'routing_code',
	'routing_name',
	'description',
];
$sIndexColumn = 'id';
$sTable = db_prefix() . 'mrp_routings';

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

		}elseif ($aColumns[$i] == 'routing_code') {
			$code = '<a href="' . admin_url('manufacturing/operation_manage/' . $aRow['id']) . '">' . $aRow['routing_code'] . '</a>';
			$code .= '<div class="row-options">';

			$code .= '<a href="' . admin_url('manufacturing/operation_manage/' . $aRow['id']) . '" >' . _l('view') . '</a>';

			if (has_permission('manufacturing', '', 'edit') || is_admin()) {

				$code .= ' | <a href="' . admin_url('manufacturing/operation_manage/' . $aRow['id']) . '" >' . _l('edit') . '</a>';
			}
			if (has_permission('manufacturing', '', 'delete') || is_admin()) {
				$code .= ' | <a href="' . admin_url('manufacturing/delete_routing/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
			}
			$code .= '</div>';

			$_data = $code;


		}elseif($aColumns[$i] == 'routing_name'){
			$_data =  $aRow['routing_name'];

		}elseif($aColumns[$i] == 'description'){
			/*get frist 400 character */

			if(new_strlen($aRow['description']) > 400){
				$pos=strpos($aRow['description'], ' ', 400);
				$description_sub = substr($aRow['description'],0,$pos ); 
			}else{
				$description_sub = $aRow['description'];
			}

			$_data =   $description_sub;

		}

		$row[] = $_data;
	}

	$output['aaData'][] = $row;
}

