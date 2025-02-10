<?php

defined('BASEPATH') or exit('No direct script access allowed');

$sTable       = db_prefix() . 'mpesa_gateway_transactions';
$aColumns = [
    'invoice_id',
    'ref_id',
    'phone',
    'amount',
    $sTable . '.status',
    'description',
    'timestamp',
];

$sIndexColumn = 'id';

$invoiceTable = db_prefix() . 'invoices';
$join = ['LEFT JOIN ' . $invoiceTable . ' ON ' . $invoiceTable . '.id = ' . $sTable . '.invoice_id'];

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, [], [$sTable . '.id', $invoiceTable . '.hash as invoice_hash, receipt_number']);

$output  = $result['output'];
$rResult = $result['rResult'];
$CI = &get_instance();

$customFields = $aColumns;

foreach ($rResult as $aRow) {
    $row = [];
    $invoiceViewLink = site_url('invoice/' . $aRow['invoice_id'] . '/' . $aRow['invoice_hash']);
    $queryLink = site_url(MPESA_GATEWAY_MODULE_NAME . '/process/verify/' . $aRow['id'] . '/redirect/admin');
    for ($i = 0; $i < count($customFields); $i++) {
        $_data = $aRow[$customFields[$i]];

        if ($customFields[$i] == 'invoice_id') {
            $_data = '<a href="' . $invoiceViewLink . '" target="_blank">' . format_invoice_number($_data) . ' <i class="fa fa-external-link"></i></a>';
        } elseif ($customFields[$i] == 'timestamp') {
            $_data = _d($_data);
        } elseif ($customFields[$i] == $sTable . '.status') {
            $className = $_data == Invoices_model::STATUS_PAID ? 'success' : ($_data == 'pending' ? 'info' : 'danger');
            $_data = $_data == Invoices_model::STATUS_PAID ? 'success' : $_data;
            $_data = '<span class="badge tw-bg-' . $className . '-200">' . $_data . '</span>';
        } elseif ($customFields[$i] == 'description') {

            $_data = '<small>' . $_data . '</small>';
        } elseif ($customFields[$i] == 'ref_id') {
            $_data = !empty($aRow['receipt_number'] ?? '') ? $aRow['receipt_number'] : $_data;
        }

        $row[] = $_data;
    }

    $options = '<div class="tw-flex tw-items-center tw-space-x-3">';
    $options .= '<a href="' . $queryLink . '" class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700">
        <i class="fa fa-refresh"></i> ' . _l('mpesa_requery') . '
    </a>';

    $options .= '</div>';

    $row[] = $options;

    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;
}