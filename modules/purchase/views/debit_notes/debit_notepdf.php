<?php defined('BASEPATH') or exit('No direct script access allowed');

$dimensions = $pdf->getPageDimensions();

$info_right_column = '';
$info_left_column  = '';

$info_right_column .= '<span style="font-weight:bold;font-size:27px;">' . _l('debit_note') . '</span><br />';
$info_right_column .= '<b style="color:#4e4e4e;"># ' . $debit_note_number . '</b>';


$info_right_column .= '<br /><span style="color:rgb(' . debit_note_status_color_pdf($debit_note->status) . ');text-transform:uppercase;">' . format_credit_note_status($debit_note->status, '', false) . '</span>';

// Add logo
$info_left_column .= get_po_logo(150);
// Write top left logo and right column info/text
pdf_multi_row($info_left_column, $info_right_column, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

$pdf->ln(10);

$organization_info = '<div style="color:#424242;">';

$organization_info .= format_organization_info();

$organization_info .= '</div>';

// Bill to
$debit_note_info = '<b>' . _l('debit_note_bill_to') . '</b>';
$debit_note_info .= '<div style="color:#424242;">';
    $debit_note_info .= format_vendor_info($debit_note, 'debit_note', 'billing');
$debit_note_info .= '</div>';

// ship to to
if ($debit_note->include_shipping == 1 && $debit_note->show_shipping_on_debit_note == 1) {
    $debit_note_info .= '<br /><b>' . _l('ship_to') . '</b>';
    $debit_note_info .= '<div style="color:#424242;">';
    $debit_note_info .= format_customer_info($debit_note, 'debit_note', 'shipping');
    $debit_note_info .= '</div>';
}

$debit_note_info .= '<br />' . _l('debit_note_date') . ': ' . _d($debit_note->date) . '<br />';

if (!empty($debit_note->reference_no)) {
    $debit_note_info .= _l('reference_no') . ': ' . $debit_note->reference_no . '<br />';
}



$left_info  = $swap == '1' ? $debit_note_info : $organization_info;
$right_info = $swap == '1' ? $organization_info : $debit_note_info;

pdf_multi_row($left_info, $right_info, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

// The Table
$pdf->Ln(hooks()->apply_filters('pdf_info_and_table_separator', 6));

// The items table
$items = get_items_table_data($debit_note, 'debit_note', 'pdf');

$tblhtml = $items->table();

$pdf->writeHTML($tblhtml, true, false, false, false, '');

$pdf->Ln(8);
$tbltotal = '';

$tbltotal .= '<table cellpadding="6" style="font-size:' . ($font_size + 4) . 'px">';
$tbltotal .= '
<tr>
    <td align="right" width="85%"><strong>' . _l('debit_note_subtotal') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($debit_note->subtotal, $debit_note->currency_name) . '</td>
</tr>';

if (is_sale_discount_applied($debit_note)) {
    $tbltotal .= '
    <tr>
        <td align="right" width="85%"><strong>' . _l('debit_note_discount');
    if (is_sale_discount($debit_note, 'percent')) {
        $tbltotal .= ' (' . app_format_number($debit_note->discount_percent, true) . '%)';
    }
    $tbltotal .= '</strong>';
    $tbltotal .= '</td>';
    $tbltotal .= '<td align="right" width="15%">-' . app_format_money($debit_note->discount_total, $debit_note->currency_name) . '</td>
    </tr>';
}

foreach ($items->taxes() as $tax) {
    $tbltotal .= '<tr>
    <td align="right" width="85%"><strong>' . $tax['taxname'] . ' (' . app_format_number($tax['taxrate']) . '%)' . '</strong></td>
    <td align="right" width="15%">' . app_format_money($tax['total_tax'], $debit_note->currency_name) . '</td>
</tr>';
}

if ((int) $debit_note->adjustment != 0) {
    $tbltotal .= '<tr>
    <td align="right" width="85%"><strong>' . _l('debit_note_adjustment') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($debit_note->adjustment, $debit_note->currency_name) . '</td>
</tr>';
}

$tbltotal .= '
<tr style="background-color:#f0f0f0;">
    <td align="right" width="85%"><strong>' . _l('debit_note_total') . '</strong></td>
    <td align="right" width="15%">' . app_format_money($debit_note->total, $debit_note->currency_name) . '</td>
</tr>';

if ($debit_note->debit_used) {
    $tbltotal .= '
    <tr>
        <td align="right" width="85%"><strong>' . _l('debit_used') . '</strong></td>
        <td align="right" width="15%">' . '-' . app_format_money($debit_note->debit_used, $debit_note->currency_name) . '</td>
    </tr>';
}

if ($debit_note->total_refunds) {
    $tbltotal .= '
    <tr>
        <td align="right" width="85%"><strong>' . _l('refund') . '</strong></td>
        <td align="right" width="15%">' . '-' . app_format_money($debit_note->total_refunds, $debit_note->currency_name) . '</td>
    </tr>';
}

$tbltotal .= '
    <tr>
        <td align="right" width="85%"><strong>' . _l('debits_remaining') . '</strong></td>
        <td align="right" width="15%">' . app_format_money($debit_note->remaining_debits, $debit_note->currency_name) . '</td>
   </tr>';

$tbltotal .= '</table>';

$pdf->writeHTML($tbltotal, true, false, false, false, '');

if (get_option('total_to_words_enabled') == 1) {
    // Set the font bold
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->writeHTMLCell('', '', '', '', _l('num_word') . ': ' . $CI->numberword->convert($debit_note->total, $debit_note->currency_name), 0, 1, false, true, 'C', true);
    // Set the font again to normal like the rest of the pdf
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Ln(4);
}

if (!empty($debit_note->vendornote)) {
    $pdf->Ln(4);
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->Cell(0, 0, _l('debit_note_vendor_note'), 0, 1, 'L', 0, '', 0);
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Ln(2);
    $pdf->writeHTMLCell('', '', '', '', $debit_note->vendornote, 0, 1, false, true, 'L', true);
}

if (!empty($debit_note->terms)) {
    $pdf->Ln(4);
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->Cell(0, 0, _l('terms_and_conditions'), 0, 1, 'L', 0, '', 0);
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Ln(2);
    $pdf->writeHTMLCell('', '', '', '', $debit_note->terms, 0, 1, false, true, 'L', true);
}
