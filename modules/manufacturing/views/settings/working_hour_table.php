<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
	'id',
	'working_hour_name',
	'hours_per_day',
	'1',
];
$sIndexColumn = 'id';
$sTable = db_prefix() . 'mrp_working_hours';

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

		}elseif ($aColumns[$i] == 'working_hour_name') {
			$code = $aRow['working_hour_name'];
			$_data = $code;

		}elseif($aColumns[$i] == 'hours_per_day'){
			$_data =  $aRow['hours_per_day'];

		}elseif($aColumns[$i] == '1') {
			$_data ='';

			if(has_permission('manufacturing','','edit')) {
				$_data .=	'<a href="'.admin_url('manufacturing/add_edit_working_hour/'.$aRow['id']).'"  class="btn btn-default btn-icon" data-toggle="sidebar-right" ><i class="fa-regular fa-pen-to-square"></i></a>';
			} 

			if(has_permission('manufacturing','','delete')) {
				$_data .=	'<a href="'.admin_url('manufacturing/delete_working_hour/'.$aRow['id']).'" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>';

			} 

		}

		$row[] = $_data;
	}

	$output['aaData'][] = $row;
}

