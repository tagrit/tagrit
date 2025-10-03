<?php

defined('BASEPATH') or exit('No direct script access allowed');

$dimensions = $pdf->getPageDimensions();

$info_right_column = '';
$info_left_column  = '';

$info_right_column .= '<span style="font-weight:bold;font-size:27px;">' . _l('wh_packing_list') . '</span><br />';
$info_right_column .= '<b style="color:#4e4e4e;"># ' . $packing_list_number . '</b>';


// Add logo
$info_left_column .= pdf_logo_url();

// Write top left logo and right column info/text
pdf_multi_row($info_left_column, $info_right_column, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

$pdf->ln(10);

$organization_info = '<div style="color:#424242;">';

$organization_info .= format_organization_info();

$organization_info .= '</div>';

// Bill to
$invoice_info = '<b>' . _l('invoice_bill_to') . ':</b>';
$invoice_info .= '<div style="color:#424242;">';
$invoice_info .= format_customer_info($packing_list, 'invoice', 'billing');
$invoice_info .= '</div>';

// ship to to
$invoice_info .= '<br /><b>' . _l('ship_to') . ':</b>';
$invoice_info .= '<div style="color:#424242;">';
$invoice_info .= format_customer_info($packing_list, 'invoice', 'shipping');
$invoice_info .= '</div>';

$invoice_info .= '<br />' . _l('packing_date') . ' ' . _d($packing_list->datecreated) . '<br />';



$left_info  = $swap == '1' ? $invoice_info : $organization_info;
$right_info = $swap == '1' ? $organization_info : $invoice_info;

pdf_multi_row($left_info, $right_info, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

// The Table
$pdf->Ln(hooks()->apply_filters('pdf_info_and_table_separator', 6));

// The items table
// $items = get_items_table_data($invoice, 'invoice', 'pdf');
$item_description_width = 15;
$pdf_display_rate = get_option('packing_list_pdf_display_rate');
$pdf_display_tax = get_option('packing_list_pdf_display_tax');
$pdf_display_subtotal = get_option('packing_list_pdf_display_subtotal');
$pdf_display_discount_percent = get_option('packing_list_pdf_display_discount_percent');
$pdf_display_discount_amount = get_option('packing_list_pdf_display_discount_amount');
$pdf_display_totalpayment = get_option('packing_list_pdf_display_totalpayment');
$pdf_display_summary = get_option('packing_list_pdf_display_summary');
if($pdf_display_rate == 0){
	$item_description_width += 10;
}
if($pdf_display_tax == 0){
	$item_description_width += 10;
}
if($pdf_display_subtotal == 0){
	$item_description_width += 15;
}
if($pdf_display_discount_percent == 0){
	$item_description_width += 10;
}
if($pdf_display_discount_amount == 0){
	$item_description_width += 10;
}
if($pdf_display_totalpayment == 0){
	$item_description_width += 10;
}

$table_font_size = 'font-size:12px;';
$items = '';
$items .='<table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
<thead>';
$items .= '<tr height="30" bgcolor="' . get_option('pdf_table_heading_color') . '" style="color:' . get_option('pdf_table_heading_text_color') . '; ">';
$items.='<th width="5%"  >#</th>
<th width="'.$item_description_width.'%" align="left" style="font-size:12px;" >'. _l('commodity_code').'</th>
<th width="10%" align="right" style="font-size:12px;" class="qty">'. _l('quantity').'</th>';
if($pdf_display_rate == 1){
	$items.='<th width="10%" align="right" style="font-size:12px;">'. _l('rate').'</th>';
}
if($pdf_display_tax == 1){
	$items .='<th width="10%" align="right" style="font-size:12px;">'. _l('invoice_table_tax_heading').'</th>';
}
if($pdf_display_subtotal == 1){
	$items .='<th width="15%" align="right" style="font-size:12px;">'. _l('subtotal').'</th>';
}
if($pdf_display_discount_percent == 1){
	$items .='<th width="10%" align="right" style="font-size:12px;">'. _l('discount').'(%)'.'</th>';
}
if($pdf_display_discount_amount == 1){
	$items .='<th width="10%" align="right" style="font-size:12px;">'. _l('discount(money)').'</th>';
}
if($pdf_display_totalpayment == 1){
	$items .='<th width="15%" align="right" style="font-size:12px;">'. _l('total_money').'</th>';
}

$items .='</tr>
</thead>
<tbody class="tbody-main" style="'.$table_font_size.'">';

// render item table start
foreach ($packing_list->packing_list_detail as $key => $packing_list_detail) {
	$itemHTML = '';

			// Open table row
	$itemHTML .= '<tr style="'.$table_font_size.'">';

			// Table data number
	$itemHTML .= '<td align="center" width="5%">' . ($key+1) . '</td>';

	$itemHTML .= '<td class="description" align="left;" width="'.$item_description_width.'%">';

			/**
			 * Item description
			 */
			if (!empty($packing_list_detail['commodity_name'])) {
				$itemHTML .= '<span style="font-size:10px;"><strong>'
				. $packing_list_detail['commodity_name']
				. '</strong></span>';


			}

			$itemHTML .= '</td>';

			/**
			 * Item quantity
			 */
			$itemHTML .= '<td align="right" width="10%">' . floatVal($packing_list_detail['quantity']);

			$unit_name = '';
			if(is_numeric($packing_list_detail['unit_id'])){
				$unit_name = get_unit_type($packing_list_detail['unit_id']) != null ? ' '.get_unit_type($packing_list_detail['unit_id'])->unit_name : '';
			}
			/**
			 * Maybe item has added unit?
			 */
			if ($packing_list_detail['unit_id']) {
				$itemHTML .= ' ' . $unit_name;
			}

			$itemHTML .= '</td>';

			/**
			 * Item rate
			 * @var string
			 */

			if($pdf_display_rate == 1){
				$itemHTML .= '<td align="right" width="10%">' . app_format_money($packing_list_detail['unit_price'], '') . '</td>';
			}

			/**
			 * Items table taxes HTML custom function because it's too general for all features/options
			 * @var string
			 */
			if($pdf_display_tax == 1){

				$item_tax = wh_convert_item_taxes($packing_list_detail['tax_id'], $packing_list_detail['tax_rate'], $packing_list_detail['tax_name']);
				$itemHTML .= '<td align="right" width="10%">';

				if(is_array($item_tax) && isset($item_tax)){
					if (count($item_tax) > 0) {
						foreach ($item_tax as $tax) {

							$item_tax = '';
							if ( get_option('remove_tax_name_from_item_table') == false || multiple_taxes_found_for_item($item_tax)) {
								$tmp      = new_explode('|', $tax['taxname']);
								$item_tax = $tmp[0] . ' ' . app_format_number($tmp[1]) . '%<br />';
							} else {
								$item_tax .= app_format_number($tax['taxrate']) . '%';
							}
							$itemHTML .= $item_tax;
						}
					} else {
						$itemHTML .=  app_format_number(0) . '%';
					}
				}
				$itemHTML .= '</td>';
			}
			
			// sub total
			if($pdf_display_subtotal == 1){

				$itemHTML .= '<td class="amount" align="right" width="15%">' . app_format_money($packing_list_detail['sub_total'], '') . '</td>';
			}
			
			// sub total
			if($pdf_display_discount_percent == 1){

				$itemHTML .= '<td class="amount" align="right" width="10%">' . $packing_list_detail['discount'] . '</td>';
			}
			
			// sub total
			if($pdf_display_discount_amount == 1){

				$itemHTML .= '<td class="amount" align="right" width="10%">' . app_format_money($packing_list_detail['discount_total'], '') . '</td>';
			}


			/**
			 * Possible action hook user to include tax in item total amount calculated with the quantiy
			 * eq Rate * QTY + TAXES APPLIED
			 */
			if($pdf_display_totalpayment == 1){

				$itemHTML .= '<td class="amount" align="right" width="15%">' . app_format_money($packing_list_detail['total_after_discount'], '') . '</td>';
			}

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

		$tbltotal = '';
		$tbltotal .= '<table cellpadding="6" style="font-size:' . ($font_size + 4) . 'px">';
		$tbltotal .= '
		<tr>
		<td align="right" width="85%"><strong>' . _l('subtotal') . '</strong></td>
		<td align="right" width="15%">' . app_format_money($packing_list->subtotal, $packing_list->base_currency) . '</td>
		</tr>';

		$tbltotal .= $packing_list->tax_data['pdf_html_currency'];

		$discount_total = 0 ;
		if(isset($packing_list)){
			$discount_total += (float)$packing_list->discount_total  + (float)$packing_list->additional_discount;
		}

		$tbltotal .= '<tr>
		<td align="right" width="85%"><strong>' . _l('total_discount') . '</strong></td>
		<td align="right" width="15%">' . app_format_money($discount_total, $packing_list->base_currency) . '</td>
		</tr>';

		$tbltotal .= '<tr>
		<td align="right" width="85%"><strong>' . _l('wh_shipping_fee') . '</strong></td>
		<td align="right" width="15%">' . app_format_money($packing_list->shipping_fee, $packing_list->base_currency) . '</td>
		</tr>';

		$tbltotal .= '
		<tr style="background-color:#f0f0f0;">
		<td align="right" width="85%"><strong>' . _l('total_money') . '</strong></td>
		<td align="right" width="15%">' . app_format_money($packing_list->total_after_discount, $packing_list->base_currency) . '</td>
		</tr>';


		$tbltotal .= '</table>';
		if($pdf_display_summary == 1){
			$pdf->writeHTML($tbltotal, true, false, false, false, '');
		}

		if (!empty($packing_list->client_note)) {
			$pdf->Ln(4);
			$pdf->SetFont($font_name, 'B', $font_size);
			$pdf->Cell(0, 0, _l('client_note'), 0, 1, 'L', 0, '', 0);
			$pdf->SetFont($font_name, '', $font_size);
			$pdf->Ln(2);
			$pdf->writeHTMLCell('', '', '', '', $packing_list->client_note, 0, 1, false, true, 'L', true);
		}

