<?php

defined('BASEPATH') or exit('No direct script access allowed');

$dimensions = $pdf->getPageDimensions();

$info_right_column = '';
$info_left_column  = '';

$info_right_column .= '<span style="font-weight:bold;font-size:27px;">' . _l('wh_warranty_period_report') . '</span><br />';
$info_right_column .= '<b style="color:#4e4e4e;"># ' . date('YmdHi') . '</b>';


// Add logo
$info_left_column .= pdf_logo_url();

// Write top left logo and right column info/text
pdf_multi_row($info_left_column, $info_right_column, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

$pdf->ln(10);

$organization_info = '<div style="color:#424242;">';

$organization_info .= format_organization_info();

$organization_info .= '</div>';

// Bill to
$invoice_info = '';

// ship to to
$invoice_info .= '';


$left_info  = $swap == '1' ? $invoice_info : $organization_info;
$right_info = $swap == '1' ? $organization_info : $invoice_info;

pdf_multi_row($left_info, $right_info, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

// The Table
$pdf->Ln(hooks()->apply_filters('pdf_info_and_table_separator', 6));

// The items table
// $items = get_items_table_data($invoice, 'invoice', 'pdf');

$table_font_size = 'font-size:12px;';
$table_text_color = 'style="color:red";';
$items = '';
$items .='<table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
<thead>';
$items .= '<tr height="30" bgcolor="' . get_option('pdf_table_heading_color') . '" style="color:' . get_option('pdf_table_heading_text_color') . '; ">';
$items.='<th width="5%"  >#</th>
<th width="10%" align="left" style="font-size:12px;" >'. _l('goods_delivery').'</th>
<th width="15%" align="left" style="font-size:12px;" >'. _l('customer_name').'</th>
<th width="12%" align="left" style="font-size:12px;">'. _l('commodity_name').'</th>
<th width="10%" align="left" style="font-size:12px;">'. _l('quantity').'</th>
<th width="10%" align="left" style="font-size:12px;">'. _l('rate').'</th>
<th width="10%" align="left" style="font-size:12px;">'. _l('expiry_date').'</th>
<th width="10%" align="left" style="font-size:12px;">'. _l('lot_number').'</th>
<th width="10%" align="left" style="font-size:12px;">'. _l('wh_serial_number').'</th>
<th width="10%" align="right" style="font-size:12px;">'. _l('guarantee_period').'</th>
</tr>
</thead>
<tbody class="tbody-main" style="'.$table_font_size.'">';

// render item table start
foreach ($warranty_period as $key => $packing_list_detail) {
	$itemHTML = '';

			// Open table row
	$itemHTML .= '<tr style="'.$table_font_size.'">';

			// Table data number
	$itemHTML .= '<td align="center" width="5%">' . ($key+1) . '</td>';

	$itemHTML .= '<td class="description" align="left;" width="10%">';

	$text_color = '';
	if(strtotime($packing_list_detail['guarantee_period']) <= strtotime(date('Y-m-d'))){
		$text_color = $table_text_color;
	}

	$value = get_goods_delivery_code($packing_list_detail['goods_delivery_id']) != null ? get_goods_delivery_code($packing_list_detail['goods_delivery_id'])->goods_delivery_code : '';
	if($value != ''){
		$itemHTML .=  $value;
	}else{
		$itemHTML .= '';
	}


	$itemHTML .= '</td>';

			/**
			 * Item quantity
			 */
			$itemHTML .= '<td align="left" width="15%">' .  get_company_name($packing_list_detail['customer_code']);
			$itemHTML .= '</td>';

			/**
			 * Item rate
			 * @var string
			 */
			if(new_strlen($packing_list_detail['commodity_name'] ?? '') == 0){
				$commodity_name = '<span class="" '.$text_color.'>'. wh_get_item_variatiom($packing_list_detail['commodity_code']).'</span>';
			}else{
				$commodity_name = '<span class="" '.$text_color.'>'.$packing_list_detail['commodity_name'].'</span>';
			}

			$itemHTML .= '<td align="left" width="12%" '.$text_color.'>' . $commodity_name . '</td>';

			// sub total
			$itemHTML .= '<td class="amount " align="left" width="10%" '.$text_color.'>' . $packing_list_detail['quantities'].' '.wh_get_unit_name($packing_list_detail['unit_id']) . '</td>';
			
			// sub total
			$itemHTML .= '<td class="amount " align="left" width="10%" '.$text_color.'>' . app_format_money((float)$packing_list_detail['unit_price'], '') . '</td>';
			
			// sub total
			$itemHTML .= '<td class="amount " align="left" width="10%" '.$text_color.'>' . $packing_list_detail['expiry_date'] . '</td>';
			$itemHTML .= '<td class="amount " align="left" width="10%" '.$text_color.'>' . $packing_list_detail['lot_number'] . '</td>';
			$itemHTML .= '<td class="amount " align="left" width="10%" '.$text_color.'>' . $packing_list_detail['serial_number'] . '</td>';


			/**
			 * Possible action hook user to include tax in item total amount calculated with the quantiy
			 * eq Rate * QTY + TAXES APPLIED
			 */

			$itemHTML .= '<td class="amount '.$text_color.'" align="right" width="10%" '.$text_color.'>' . _d($packing_list_detail['guarantee_period']) . '</td>';

			// Close table row
			$itemHTML .= '</tr>';

			$items .= $itemHTML;

		}
// render item table end

		$items.= '</tbody>
		</table>';

		$tblhtml = $items;
		$pdf->writeHTML($tblhtml, true, false, false, false, '');

		$pdf->Ln(8);
