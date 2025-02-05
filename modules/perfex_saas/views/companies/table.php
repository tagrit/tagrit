<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'name',
    get_sql_select_client_company(),
    'status',
    'clientid',
    'dsn',
    'metadata',
    'created_at',
];

$sTable       = perfex_saas_table('companies');
$sIndexColumn = 'id';

$clientTable = db_prefix() . 'clients';
$join = ['LEFT JOIN ' . $clientTable . ' ON ' . $clientTable . '.userid = ' . $sTable . '.clientid'];

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, [], [$sTable . '.id', 'userid', 'slug', 'status_note']);

$output  = $result['output'];
$rResult = $result['rResult'];
$CI = &get_instance();

$customFields = $aColumns;
$customFields[1] = "company";

$is_single_package = perfex_saas_is_single_package_mode();
$package_id_col = perfex_saas_column('packageid');

$need_action = [];

$clientInvoices = [];

foreach ($rResult as $aRow) {

    $row = [];
    $aRow = (array) $CI->perfex_saas_model->parse_company((object)$aRow);

    if (isset($clientInvoices[$aRow['clientid']])) {
        $invoice = $clientInvoices[$aRow['clientid']];
    } else {
        $invoice = $CI->perfex_saas_model->get_company_invoice($aRow['clientid']);
        $clientInvoices[$aRow['clientid']] = $invoice;
    }

    $invoiceLink = !empty($invoice->id)  && !isset($invoice->is_mock) ? admin_url('invoices/list_invoices/' . $invoice->id) : '';
    $packageLink = '';
    if (!empty($invoice->{$package_id_col}))
        $packageLink = $is_single_package ? admin_url(PERFEX_SAAS_ROUTE_NAME . '/pricing') : admin_url(PERFEX_SAAS_ROUTE_NAME . '/packages/edit/' . $invoice->{$package_id_col});
    $viewLink = perfex_saas_tenant_admin_url((object)$aRow);
    $editLink = admin_url(PERFEX_SAAS_ROUTE_NAME . '/companies/edit/' . $aRow['id']);
    $notice = '';

    for ($i = 0; $i < count($customFields); $i++) {
        $_data = $aRow[$customFields[$i]];

        if ($customFields[$i] == 'name') {
            $_data = '<a href="' . $viewLink . '" target="_blank">' . e($_data) . ' <i class="fa fa-external-link"></i></a>';
        } elseif ($customFields[$i] == 'company') {
            $_data = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '">' . e($_data) . '</a>';
            $_data .= '<div class="row-options tw-ml-9">';
            $_data .= '<a href="' . admin_url('clients/login_as_client/' . $aRow['userid']) . '" target="_blank"><i class="fa-regular fa-share-from-square"></i> ' . _l('login_as_client') . '</a>';
            $_data .= '</div>';
        } elseif ($customFields[$i] == 'created_at' || $customFields[$i] == 'updated_at') {
            $_data = _d($_data);
        } elseif ($customFields[$i] == 'status') {
            $className = $_data == PERFEX_SAAS_STATUS_ACTIVE ? 'success' : ($_data == PERFEX_SAAS_STATUS_PENDING ? 'primary' : 'danger');
            $statusNote = '';
            if ($_data != PERFEX_SAAS_STATUS_ACTIVE && !empty($aRow['status_note']))
                $statusNote = "<span data-toggle='tooltip' data-title='" . $aRow['status_note'] . "'><i class='fa fa-warning text-danger'></i></span>";
            $_data = '<span class="badge tw-bg-' . $className . '-200">' . _l($_data, '', false) . '</span>' . $statusNote;
        } elseif ($customFields[$i] == 'clientid') {
            $_data = '-';
            if (!empty($invoice->name)) {
                $_data =  '<a href="' . $packageLink . '" target="_blank">' . _l($invoice->name, '', false) . '</a>';
                $_data .= '<div class="row-options tw-ml-9">';
                if (!empty($invoiceLink))
                    $_data .= '<a href="' . $invoiceLink . '" target="_blank">' . _l('invoice') . ' <i class="fa fa-external-link"></i></a>';
                $_data .= '</div>';
            }
        } elseif ($customFields[$i] == 'dsn') {
            if (!empty($_data)) {
                $_data = perfex_saas_parse_dsn($_data);
                $_data = $_data['host'] . ':<b>' . $_data['dbname'] . '</b>';
            } else {
                $_data = '-';
            }
        } elseif ($customFields[$i] == 'metadata') {

            $disabled_modules = trim(implode(', ', array_merge($_data->disabled_modules ?? [], $_data->admin_disabled_modules ?? [])), ', ');
            $admin_approved_modules = trim(implode(', ', $_data->admin_approved_modules ?? []), ', ');
            $_data = [];

            if (!empty($disabled_modules))
                $_data[] = '<strong>' . _l('perfex_saas_disabled_modules') . '</strong>: ' . e($disabled_modules);
            if (!empty($admin_approved_modules))
                $_data[] = '<strong>' . _l('perfex_saas_admin_approved_modules') . '</strong>: ' . e($admin_approved_modules);

            $_data = implode('<br/><br/>', $_data);
        }

        $row[] = $_data;
    }

    $options = '<div class="tw-flex tw-items-center tw-space-x-3">';

    $notice = empty($aRow['metadata']->pending_custom_domain) ? "" : "<span data-toggle='tooltip' data-title='" . strip_tags(_l("perfex_saas_pending_domain_request", [$aRow['name'], $aRow['metadata']->pending_custom_domain])) . "'><i class='fa fa-warning text-danger'></i></span>";
    $options .= '<a href="' . $editLink . '" target="_blank" class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700">' . $notice . '
        <i class="fa fa-eye fa-lg"></i>
    </a>';

    if (staff_can('edit', 'perfex_saas_companies')) {
        $options .= '<a href="' . $editLink . '" class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700">
            <i class="fa-regular fa-pen-to-square fa-lg"></i>
        </a>';
    }

    if (staff_can('delete', 'perfex_saas_companies') && $aRow['status'] !== PERFEX_SAAS_STATUS_PENDING_DELETE) {
        $options .= form_open(admin_url(PERFEX_SAAS_ROUTE_NAME . '/companies/delete/' . $aRow['id'])) .
            form_hidden('id', $aRow['id']) .
            '<button class="tw-bg-transparent tw-border-0 tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 _delete">
            <i class="fa-regular fa-trash-can fa-lg"></i>
        </button>' . form_close();
    }

    $options .= '</div>';

    $row[] = $options;

    $row['DT_RowClass'] = 'has-row-options';
    if (empty($notice))
        $output['aaData'][] = $row;
    else
        array_unshift($output['aaData'], $row);
}