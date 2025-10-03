<?php

defined('BASEPATH') or exit('No direct script access allowed');

$dimensions = $pdf->getPageDimensions();

$info_right_column = '';
$info_left_column  = '';

$info_right_column .= '<span style="font-weight:bold;font-size:27px;">' . _l('wh_order_return') . '</span><br />';
$info_right_column .= '<b style="color:#4e4e4e;"># ' . $order_return_number . '</b>';


// Add logo
$info_left_column .= pdf_logo_url();

// Write top left logo and right column info/text
pdf_multi_row($info_left_column, $info_right_column, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

$pdf->ln(10);

$organization_info = '<div style="color:#424242;">';

$organization_info .= format_organization_info();

$organization_info .= '</div>';

$invoice_info .= '<br /><b>' . _l('customer_name') . ': </b>';									
$invoice_info .= get_company_name($order_return->company_id).'<br />';
$invoice_info .= '<br /><b>' . _l('email') . ': </b>';									
$invoice_info .= $order_return->email.'<br />';
$invoice_info .= '<b>' . _l('phonenumber') . ': </b>';									
$invoice_info .= $order_return->phonenumber.'<br />';									
$invoice_info .= '<b>' . _l('order_return_date') . ': </b>' . _d($order_return->datecreated) . '<br />';



$left_info  = $swap == '1' ? $invoice_info : $organization_info;
$right_info = $swap == '1' ? $organization_info : $invoice_info;

pdf_multi_row($left_info, $right_info, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

// The Table
$pdf->Ln(hooks()->apply_filters('pdf_info_and_table_separator', 6));

// The items table
// $items = get_items_table_data($invoice, 'invoice', 'pdf');

$table_font_size = 'font-size:12px;';
$items = '';
$items .='<table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
<thead>';
$items .= '<tr height="30" bgcolor="' . get_option('pdf_table_heading_color') . '" style="color:' . get_option('pdf_table_heading_text_color') . '; ">';
$items.='<th width="5%"  >#</th>
<th width="15%" align="left" style="font-size:12px;" >'. _l('commodity_code').'</th>
<th width="10%" align="right" style="font-size:12px;" class="qty">'. _l('quantity').'</th>
<th width="10%" align="right" style="font-size:12px;">'. _l('rate').'</th>
<th width="12%" align="right" style="font-size:12px;">'. _l('invoice_table_tax_heading').'</th>
<th width="15%" align="right" style="font-size:12px;">'. _l('subtotal').'</th>
<th width="10%" align="right" style="font-size:12px;">'. _l('discount').'(%)'.'</th>
<th width="10%" align="right" style="font-size:12px;">'. _l('discount(money)').'</th>
<th width="15%" align="right" style="font-size:12px;">'. _l('total_money').'</th>
</tr>
</thead>
<tbody class="tbody-main" style="'.$table_font_size.'">';

// render item table start
foreach ($order_return->order_return_detail as $key => $order_return_detail) {
	$itemHTML = '';

			// Open table row
	$itemHTML .= '<tr style="'.$table_font_size.'">';

			// Table data number
	$itemHTML .= '<td align="center" width="5%">' . ($key+1) . '</td>';

	$itemHTML .= '<td class="description" align="left;" width="20%">';

			/**
			 * Item description
			 */
			if (!empty($order_return_detail['commodity_name'])) {
				$itemHTML .= '<span style="font-size:10px;"><strong>'
				. $order_return_detail['commodity_name']
				. '</strong></span>';


			}

			$itemHTML .= '</td>';

			/**
			 * Item quantity
			 */
			$itemHTML .= '<td align="right" width="10%">' . floatVal($order_return_detail['quantity']);

			$unit_name = '';
			if(is_numeric($order_return_detail['unit_id'])){
				$unit_name = get_unit_type($order_return_detail['unit_id']) != null ? ' '.get_unit_type($order_return_detail['unit_id'])->unit_name : '';
			}
			/**
			 * Maybe item has added unit?
			 */
			if ($order_return_detail['unit_id']) {
				$itemHTML .= ' ' . $unit_name;
			}

			$itemHTML .= '</td>';

			/**
			 * Item rate
			 * @var string
			 */


			$itemHTML .= '<td align="right" width="10%">' . app_format_money($order_return_detail['unit_price'], '') . '</td>';

			/**
			 * Items table taxes HTML custom function because it's too general for all features/options
			 * @var string
			 */
			
			$item_tax = wh_convert_item_taxes($order_return_detail['tax_id'], $order_return_detail['tax_rate'], $order_return_detail['tax_name']);
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
			
			// sub total
			$itemHTML .= '<td class="amount" align="right" width="12%">' . app_format_money($order_return_detail['sub_total'], '') . '</td>';
			
			// sub total
			$itemHTML .= '<td class="amount" align="right" width="10%">' . $order_return_detail['discount'] . '</td>';
			
			// sub total
			$itemHTML .= '<td class="amount" align="right" width="10%">' . app_format_money($order_return_detail['discount_total'], '') . '</td>';


			/**
			 * Possible action hook user to include tax in item total amount calculated with the quantiy
			 * eq Rate * QTY + TAXES APPLIED
			 */

			$itemHTML .= '<td class="amount" align="right" width="15%">' . app_format_money($order_return_detail['total_after_discount'], '') . '</td>';

			// Close table row
			$itemHTML .= '</tr>';

			$items .= $itemHTML;

		}
// render item table end

$items.= '</tbody>
</table>';

		$tblhtml = $items;
// var_dump($tblhtml);die;
		// $pdf->writeHTML($tblhtml, true, false, false, false, '');
		$pdf->writeHTML($tblhtml, true, false, false, false, '');

		$pdf->Ln(8);

		$tbltotal = '';
		$tbltotal .= '<table cellpadding="6" style="font-size:' . ($font_size + 4) . 'px">';
		$tbltotal .= '
		<tr>
		<td align="right" width="85%"><strong>' . _l('subtotal') . '</strong></td>
		<td align="right" width="15%">' . app_format_money($order_return->subtotal, $order_return->base_currency) . '</td>
		</tr>';

		$tbltotal .= $order_return->tax_data['pdf_html_currency'];

		$discount_total = 0 ;
		if(isset($order_return)){
			$discount_total += (float)$order_return->discount_total  + (float)$order_return->additional_discount;
		}

		$tbltotal .= '<tr>
		<td align="right" width="85%"><strong>' . _l('total_discount') . '</strong></td>
		<td align="right" width="15%">' . app_format_money($discount_total, $order_return->base_currency) . '</td>
		</tr>';

		$tbltotal .= '
		<tr style="background-color:#f0f0f0;">
		<td align="right" width="85%"><strong>' . _l('total_money') . '</strong></td>
		<td align="right" width="15%">' . app_format_money($order_return->total_after_discount, $order_return->base_currency) . '</td>
		</tr>';


		$tbltotal .= '</table>';
		$pdf->writeHTML($tbltotal, true, false, false, false, '');

		if (!empty($order_return->return_reason)) {
			$pdf->Ln(4);
			$pdf->SetFont($font_name, 'B', $font_size);
			$pdf->Cell(0, 0, _l('reason_return'), 0, 1, 'L', 0, '', 0);
			$pdf->SetFont($font_name, '', $font_size);
			$pdf->Ln(2);
			$pdf->writeHTMLCell('', '', '', '', $order_return->return_reason, 0, 1, false, true, 'L', true);
		}

		if (!empty($order_return->admin_note)) {
			$pdf->Ln(4);
			$pdf->SetFont($font_name, 'B', $font_size);
			$pdf->Cell(0, 0, _l('admin_note'), 0, 1, 'L', 0, '', 0);
			$pdf->SetFont($font_name, '', $font_size);
			$pdf->Ln(2);
			$pdf->writeHTMLCell('', '', '', '', $order_return->admin_note, 0, 1, false, true, 'L', true);
		}

		if (!empty($order_return->return_policies_information)) {
			$pdf->Ln(4);
			$pdf->SetFont($font_name, 'B', $font_size);
			$pdf->Cell(0, 0, _l('return_policies_information'), 0, 1, 'L', 0, '', 0);
			$pdf->SetFont($font_name, '', $font_size);
			$pdf->Ln(2);
			$pdf->writeHTMLCell('', '', '', '', $order_return->return_policies_information, 0, 1, false, true, 'L', true);
		}

