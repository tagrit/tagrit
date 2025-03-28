<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
	'unit_type_id',
	'unit_name',
	'category_id',
	'unit_measure_type',
	'1',
];
$sIndexColumn = 'unit_type_id';
$sTable = db_prefix() . 'ware_unit_type';

$where = [];
$join= [];


$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['unit_type_id']);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];

	for ($i = 0; $i < count($aColumns); $i++) {

		if($aColumns[$i] == 'unit_type_id') {
			$_data = $aRow['unit_type_id'];

		}elseif ($aColumns[$i] == 'unit_name') {
			$code = $aRow['unit_name'];
			$_data = $code;

		}elseif($aColumns[$i] == 'category_id'){
			$_data =  get_category_name($aRow['category_id']);

		}elseif($aColumns[$i] == 'unit_measure_type'){
			$type ='';
			switch ($aRow['unit_measure_type']) {
				case 'bigger':
					$type .= _l('bigger_than_the_reference_Unit_of_Measure');
					break;

				case 'reference':
					$type .= _l('reference_Unit_of_Measure_for_this_category');
					break;

				case 'smaller':
					$type .= _l('smaller_than_the_reference_Unit_of_Measure');
					break;
			}

			$_data = $type;

		}elseif($aColumns[$i] == '1') {
			$_data ='';

			if(has_permission('manufacturing','','edit')) {
				$_data .=	'<a href="#" onclick="add_edit_unit_measure('. $aRow['unit_type_id'] .',\'updated\'); return false;" class="btn btn-default btn-icon" data-toggle="sidebar-right" ><i class="fa-regular fa-pen-to-square"></i></a>';
			} 

			if(has_permission('manufacturing','','delete')) {
				$_data .=	'<a href="'.admin_url('manufacturing/delete_unit_of_measure/'.$aRow['unit_type_id']).'" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>';

			} 

		}

		$row[] = $_data;
	}

	$output['aaData'][] = $row;
}

