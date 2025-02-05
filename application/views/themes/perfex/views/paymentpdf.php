<?php

defined('BASEPATH') or exit('No direct script access allowed');

$dimensions = $pdf->getPageDimensions();

// Get Y position for the separation
$y = $pdf->getY();

$company_info = '<div style="color:#424242;">';
$company_info .= format_organization_info();
$company_info .= '</div>';

// Bill to
$client_details = format_customer_info($payment->invoice_data, 'payment', 'billing');

$left_info  = $swap == '1' ? $client_details : $company_info;
$right_info = $swap == '1' ? $company_info : $client_details;

pdf_multi_row($left_info, $right_info, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

$pdf->setFillColor([209, 213, 219]);
$pdf->SetFontSize(15);

$receipt_heading = '<div style="font-weight: bold;">' . mb_strtoupper(_l('payment_receipt'), 'UTF-8') . '</div>';

$pdf->Ln(20);
$pdf->writeHTMLCell(0, '', '', '', $receipt_heading, 0, 1, false, true, 'L', true);
$pdf->SetFontSize($font_size);
$pdf->Ln(10);
$pdf->Cell(0, 0, _l('payment_date') . ' ' . _d($payment->date), 0, 1, 'L', 0, '', 0);
$pdf->Ln(3);
$pdf->Line($pdf->getX(), $pdf->getY(), 90, $pdf->getY(), [
    'color' => [209, 213, 219],
]);
$pdf->Ln(3);
$payment_name = $payment->name;

if (! empty($payment->paymentmethod)) {
    $payment_name .= ' - ' . $payment->paymentmethod;
}

$pdf->Cell(0, 0, _l('payment_view_mode') . ' ' . $payment_name, 0, 1, 'L', 0, '', 0);

if (! empty($payment->transactionid)) {
    $pdf->Ln(3);
    $pdf->Line($pdf->getX(), $pdf->getY(), 90, $pdf->getY());
    $pdf->Ln(3);
    $pdf->Cell(0, 0, _l('payment_transaction_id') . ': ' . $payment->transactionid, 0, 1, 'L', 0, '', 0);
}

$pdf->Ln(3);
$pdf->Line($pdf->getX(), $pdf->getY(), 90, $pdf->getY());
$pdf->Ln(5);
$pdf->RoundedRect($pdf->getX(), $pdf->getY(), 80.5, 20, 2, '1111', 'FD', ['color' => [229, 231, 235]], [243, 244, 246]);
$pdf->Ln(0.5);
$pdf->SetFont($font_name, '', 11);
$pdf->Cell(80, 8, '   ' . _l('payment_total_amount'), 0, 1, 'L', '0', '', 0, true, 'T', 'B');
$pdf->SetFont($font_name, 'B', 11);
$pdf->Cell(80, 11, '   ' . app_format_money($payment->amount, $payment->invoice_data->currency_name), 0, 1, 'L', '0');

$pdf->Ln(10);
$pdf->SetTextColor(0);
$pdf->SetFont($font_name, 'B', 12);
$pdf->Cell(0, 0, _l('payment_for_string'), 0, 1, 'L', 0, '', 0);
$pdf->SetFont($font_name, '', $font_size);
$pdf->Ln(3);

// Header
$tblhtml = '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="0">
<tr height="30" style="color:#030712;" bgcolor="#f3f4f6">
    <th width="' . ($amountDue ? 20 : 25) . '%;">' . _l('payment_table_invoice_number') . '</th>
    <th width="' . ($amountDue ? 20 : 25) . '%;">' . _l('payment_table_invoice_date') . '</th>
    <th width="' . ($amountDue ? 20 : 25) . '%;">' . _l('payment_table_invoice_amount_total') . '</th>
    <th width="' . ($amountDue ? 20 : 25) . '%;">' . _l('payment_table_payment_amount_total') . '</th>';

if ($amountDue) {
    $tblhtml .= '<th width="20%">' . _l('invoice_amount_due') . '</th>';
}

$tblhtml .= '</tr>';

$tblhtml .= '<tbody>';
$tblhtml .= '<tr>';
$tblhtml .= '<td>' . format_invoice_number($payment->invoice_data->id) . '</td>';
$tblhtml .= '<td>' . _d($payment->invoice_data->date) . '</td>';
$tblhtml .= '<td>' . app_format_money($payment->invoice_data->total, $payment->invoice_data->currency_name) . '</td>';
$tblhtml .= '<td>' . app_format_money($payment->amount, $payment->invoice_data->currency_name) . '</td>';
if ($amountDue) {
    $tblhtml .= '<td style="color:#fc2d42">' . app_format_money($payment->invoice_data->total_left_to_pay, $payment->invoice_data->currency_name) . '</td>';
}
$tblhtml .= '</tr>';
$tblhtml .= '</tbody>';
$tblhtml .= '</table>';
$pdf->writeHTML($tblhtml, true, false, false, false, '');
