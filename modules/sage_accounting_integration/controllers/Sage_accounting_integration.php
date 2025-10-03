<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * This class describes a sage accounting integration.
 */
class Sage_accounting_integration extends AdminController {
	public function __construct() {
		parent::__construct();
        hooks()->do_action('sage_accounting_integration_init');
	}

	/**
     * manage setting
     */
    public function setting()
    {
        if(get_option('acc_integration_sage_accounting_region') == 'south_african'){
            $this->load->model('sage_accounting_integration_model');
            $data['organizations'] = $this->sage_accounting_integration_model->get_sage_accounting_organizations();
        }

    	$data['title'] = _l('setting');
    	$this->load->view('setting', $data);
    }

    /**
     * update general setting
     */
    public function update_setting(){
    	$data = $this->input->post();
        $this->load->model('sage_accounting_integration_model');
    	$success = $this->sage_accounting_integration_model->update_setting($data);
        if($success == true){
            $message = _l('updated_successfully', _l('setting'));
            set_alert('success', $message);
        }
        redirect(admin_url('sage_accounting_integration/setting'));
    }

    public function manage(){
        
        $data['title']         = _l('integrations');

        $data['group'] = $this->input->get('group');
        if ($data['group'] == '') {
            $data['group'] = 'customers';
        }

        $data['tab'] = [];
        $data['tab'][] = 'customers';
        $data['tab'][] = 'invoices';
        $data['tab'][] = 'payments';
        $data['tab'][] = 'expenses';

        $data['tabs']['view'] = 'integrations/includes/' . $data['group'];

        $data['region']         = get_option('acc_integration_sage_accounting_region');
        if(get_option('acc_integration_sage_accounting_region') == 'south_african'){
            $this->load->model('sage_accounting_integration_model');
            $data['organizations'] = $this->sage_accounting_integration_model->get_sage_accounting_organizations();
        }

        $this->load->view('integrations/manage', $data);
    }

    public function expenses_table()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');
            $currency = $this->currencies_model->get_base_currency();
            $region = get_option('acc_integration_sage_accounting_region');
           
            $select = [
                db_prefix() . 'expenses_categories.name as category_name',
                'amount',
                'expense_name',
                db_prefix() . 'expenses.date as date',
                db_prefix() . 'payment_modes.name as payment_mode_name',
                'invoiceid',
                'connect_id',
                'error_detail',
                '1'
            ];
            $where = [];

            $software = $this->input->post('software');

            if ($this->input->post('status')) {
                $status = $this->input->post('status');
                $where_status = '';
                foreach ($status as $key => $value) {
                    if($value == 'synchronized'){
                        if($where_status != ''){
                            $where_status .= ' OR connect_id != ""';
                        }else{
                            $where_status .= 'connect_id != ""';
                        }
                    }

                    if($value == 'not_synchronized_yet'){
                        if($where_status != ''){
                            $where_status .= ' OR connect_id IS NULL';
                        }else{
                            $where_status .= 'connect_id IS NULL';
                        }
                    }
                }

                if($where_status != ''){
                    array_push($where, 'AND ('. $where_status . ')');
                }
            }
            
            $from_date = '';
            $to_date   = '';
            if ($this->input->post('from_date')) {
                $from_date = $this->input->post('from_date');
                $from_date = to_sql_date($from_date);
            }

            if ($this->input->post('to_date')) {
                $to_date = $this->input->post('to_date');
                $to_date = to_sql_date($to_date);
            }
            if ($from_date != '' && $to_date != '') {
                array_push($where, 'AND (' . db_prefix() . 'expenses.date >= "' . $from_date . '" and ' . db_prefix() . 'expenses.date <= "' . $to_date . '")');
            } elseif ($from_date != '') {
                array_push($where, 'AND (' . db_prefix() . 'expenses.date >= "' . $from_date . '")');
            } elseif ($to_date != '') {
                array_push($where, 'AND (' . db_prefix() . 'expenses.date <= "' . $to_date . '")');
            }

            $organization_id = $this->input->post('organization');

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'expenses';
            $join         = [
                'JOIN ' . db_prefix() . 'expenses_categories ON ' . db_prefix() . 'expenses_categories.id = ' . db_prefix() . 'expenses.category',
                'LEFT JOIN ' . db_prefix() . 'payment_modes ON ' . db_prefix() . 'payment_modes.id = ' . db_prefix() . 'expenses.paymentmode',
                'LEFT JOIN ' . db_prefix() . 'currencies ON ' . db_prefix() . 'currencies.id = ' . db_prefix() . 'expenses.currency',
                'LEFT JOIN ' . db_prefix() . 'acc_integration_logs ON ' . db_prefix() . 'acc_integration_logs.rel_id = ' . db_prefix() . 'expenses.id AND ' . db_prefix() . 'acc_integration_logs.rel_type = "expense" AND software = "'.$software.'" AND ' . db_prefix() . 'acc_integration_logs.organization_id = "'.$organization_id.'"',
                'LEFT JOIN ' . db_prefix() . 'acc_integration_error_logs ON ' . db_prefix() . 'acc_integration_error_logs.rel_id = ' . db_prefix() . 'expenses.id AND ' . db_prefix() . 'acc_integration_error_logs.rel_type = "expense" AND ' . db_prefix() . 'acc_integration_error_logs.software = "'.$software.'" AND ' . db_prefix() . 'acc_integration_error_logs.organization_id = "'.$organization_id.'"'
            ];

            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix(). 'currencies.name as currency_name', 
                db_prefix() . 'expenses.id as id']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];
                $row[] = '<a href="' . admin_url('expenses/list_expenses/' . $aRow['id']) . '" target="_blank">' . $aRow['category_name'] . '</a>';
                $row[] = app_format_money($aRow['amount'], $aRow['currency_name']);
                $row[] = '<a href="' . admin_url('expenses/list_expenses/' . $aRow['id']) . '" target="_blank">' . $aRow['expense_name'] . '</a>';
                $row[] = _d($aRow['date']);

                $row[] = $aRow['payment_mode_name'];
                $row[] = '<a href="' . admin_url('invoices/list_invoices/' . $aRow['invoiceid']) . '" target="_blank">' . format_invoice_number($aRow['invoiceid']) . '</a>';

                $status_name = _l('not_synchronized_yet');
                $label_class = 'default';

                if ($aRow['connect_id'] > 0) {
                    $label_class = 'success';
                    $status_name = _l('synchronized');
                }
                
                $row[] = '<span class="label label-' . $label_class . ' s-status expense-status-' . $aRow['id'] . '">' . $status_name . '</span>';
                $row[] = html_entity_decode($aRow['error_detail'] ?? '');

                if($organization_id != '' || $region == 'central_european'){
                    $row[] = icon_btn('#', 'fa fa-share', 'btn-success', [
                        'title' => _l('manual_sync'),
                        'data-id' =>$aRow['id'],
                        'data-organization-id' =>$organization_id,
                        'data-type' => 'expense',
                        'data-software' => $software,
                        'onclick' => 'manual_sync(this); return false;'
                    ]);
                }else{
                    $row[] = '';
                }
                
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    public function invoices_table()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $currency = $this->currencies_model->get_base_currency();
            $region = get_option('acc_integration_sage_accounting_region');
           
            $select = [
                'number',
                db_prefix() .'invoices.date as date',
                'total',
                'clientid',
                db_prefix() . 'invoices.status',
                'connect_id',
                'error_detail',
                '1',
            ];
            $where = [];
            $software = $this->input->post('software');

            if ($this->input->post('status')) {
                $status = $this->input->post('status');
                $where_status = '';
                foreach ($status as $key => $value) {
                    if($value == 'synchronized'){
                        if($where_status != ''){
                            $where_status .= ' OR connect_id != ""';
                        }else{
                            $where_status .= 'connect_id != ""';
                        }
                    }

                    if($value == 'not_synchronized_yet'){
                        if($where_status != ''){
                            $where_status .= ' OR connect_id IS NULL';
                        }else{
                            $where_status .= 'connect_id IS NULL';
                        }
                    }
                }

                if($where_status != ''){
                    array_push($where, 'AND ('. $where_status . ')');
                }
            }
            
            $from_date = '';
            $to_date   = '';
            if ($this->input->post('from_date')) {
                $from_date = $this->input->post('from_date');
                $from_date = to_sql_date($from_date);
            }

            if ($this->input->post('to_date')) {
                $to_date = $this->input->post('to_date');
                $to_date = to_sql_date($to_date);
            }
            if ($from_date != '' && $to_date != '') {
                array_push($where, 'AND (' . db_prefix() . 'invoices.date >= "' . $from_date . '" and ' . db_prefix() . 'invoices.date <= "' . $to_date . '")');
            } elseif ($from_date != '') {
                array_push($where, 'AND (' . db_prefix() . 'invoices.date >= "' . $from_date . '")');
            } elseif ($to_date != '') {
                array_push($where, 'AND (' . db_prefix() . 'invoices.date <= "' . $to_date . '")');
            }

            $organization_id = $this->input->post('organization');

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'invoices';
            $join         = [
                'LEFT JOIN ' . db_prefix() . 'currencies ON ' . db_prefix() . 'currencies.id = ' . db_prefix() . 'invoices.currency',
                'LEFT JOIN ' . db_prefix() . 'acc_integration_logs ON ' . db_prefix() . 'acc_integration_logs.rel_id = ' . db_prefix() . 'invoices.id AND ' . db_prefix() . 'acc_integration_logs.rel_type = "invoice" AND software = "'.$software.'"  AND ' . db_prefix() . 'acc_integration_logs.organization_id = "'.$organization_id.'"',
                'LEFT JOIN ' . db_prefix() . 'acc_integration_error_logs ON ' . db_prefix() . 'acc_integration_error_logs.rel_id = ' . db_prefix() . 'invoices.id AND ' . db_prefix() . 'acc_integration_error_logs.rel_type = "invoice" AND ' . db_prefix() . 'acc_integration_error_logs.software = "'.$software.'" AND ' . db_prefix() . 'acc_integration_error_logs.organization_id = "'.$organization_id.'"'

                        ];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                db_prefix() . 'invoices.id as id',db_prefix(). 'currencies.name as currency_name',
            ]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];

                $categoryOutput = '<a href="' . admin_url('invoices/list_invoices/' . $aRow['id']) . '" target="_blank">' . format_invoice_number($aRow['id']) . '</a>';
                $row[] = $categoryOutput;

                $row[] = _d($aRow['date']);
                $row[] = app_format_money($aRow['total'], $aRow['currency_name']);

                $row[] = get_company_name($aRow['clientid']);

                $row[] = format_invoice_status($aRow[db_prefix() . 'invoices.status']);
                
                $status_name = _l('not_synchronized_yet');
                $label_class = 'default';

                if ($aRow['connect_id'] > 0) {
                    $label_class = 'success';
                    $status_name = _l('synchronized');
                }

                $row[] = '<span class="label label-' . $label_class . ' s-status invoice-status-' . $aRow['id'] . '">' . $status_name . '</span>';
                $row[] = html_entity_decode($aRow['error_detail'] ?? '');

                if($organization_id != '' || $region == 'central_european'){
                    $row[] = icon_btn('#', 'fa fa-share', 'btn-success', [
                        'title' => _l('manual_sync'),
                        'data-id' =>$aRow['id'],
                        'data-organization-id' =>$organization_id,
                        'data-type' => 'invoice',
                        'data-software' => $software,
                        'onclick' => 'manual_sync(this); return false;'
                    ]);
                }else{
                    $row[] = '';
                }

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    public function payments_table()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $currency = $this->currencies_model->get_base_currency();
            $region = get_option('acc_integration_sage_accounting_region');
            
            $select = [
                db_prefix() .'invoicepaymentrecords.date as date',
                'amount',
                db_prefix() . 'payment_modes.name as name',
                'invoiceid',
                'connect_id',
                'error_detail',
                '1',
            ];
            $where = [];
            $software = $this->input->post('software');

            if ($this->input->post('status')) {
                $status = $this->input->post('status');
                $where_status = '';
                foreach ($status as $key => $value) {
                    if($value == 'synchronized'){
                        if($where_status != ''){
                            $where_status .= ' OR connect_id != ""';
                        }else{
                            $where_status .= 'connect_id != ""';
                        }
                    }

                    if($value == 'not_synchronized_yet'){
                        if($where_status != ''){
                            $where_status .= ' OR connect_id IS NULL';
                        }else{
                            $where_status .= 'connect_id IS NULL';
                        }
                    }
                }

                if($where_status != ''){
                    array_push($where, 'AND ('. $where_status . ')');
                }
            }
            
            $from_date = '';
            $to_date   = '';
            if ($this->input->post('from_date')) {
                $from_date = $this->input->post('from_date');
                $from_date = to_sql_date($from_date);
            }

            if ($this->input->post('to_date')) {
                $to_date = $this->input->post('to_date');
                $to_date = to_sql_date($to_date);
            }
            if ($from_date != '' && $to_date != '') {
                array_push($where, 'AND (' . db_prefix() . 'invoicepaymentrecords.date >= "' . $from_date . '" and ' . db_prefix() . 'invoicepaymentrecords.date <= "' . $to_date . '")');
            } elseif ($from_date != '') {
                array_push($where, 'AND (' . db_prefix() . 'invoicepaymentrecords.date >= "' . $from_date . '")');
            } elseif ($to_date != '') {
                array_push($where, 'AND (' . db_prefix() . 'invoicepaymentrecords.date <= "' . $to_date . '")');
            }

            $organization_id = $this->input->post('organization');

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'invoicepaymentrecords';
            $join         = [
                'LEFT JOIN ' . db_prefix() . 'payment_modes ON ' . db_prefix() . 'payment_modes.id = ' . db_prefix() . 'invoicepaymentrecords.paymentmode',
                'LEFT JOIN ' . db_prefix() . 'invoices ON ' . db_prefix() . 'invoices.id = ' . db_prefix() . 'invoicepaymentrecords.invoiceid',
                'LEFT JOIN ' . db_prefix() . 'currencies ON ' . db_prefix() . 'currencies.id = ' . db_prefix() . 'invoices.currency',
                'LEFT JOIN ' . db_prefix() . 'acc_integration_logs ON ' . db_prefix() . 'acc_integration_logs.rel_id = ' . db_prefix() . 'invoicepaymentrecords.id AND ' . db_prefix() . 'acc_integration_logs.rel_type = "payment" AND software = "'.$software.'" AND ' . db_prefix() . 'acc_integration_logs.organization_id = "'.$organization_id.'"',
                'LEFT JOIN ' . db_prefix() . 'acc_integration_error_logs ON ' . db_prefix() . 'acc_integration_error_logs.rel_id = ' . db_prefix() . 'invoicepaymentrecords.id AND ' . db_prefix() . 'acc_integration_error_logs.rel_type = "payment" AND ' . db_prefix() . 'acc_integration_error_logs.software = "'.$software.'" AND ' . db_prefix() . 'acc_integration_error_logs.organization_id = "'.$organization_id.'"'

                        ];
                
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'invoicepaymentrecords.id as id','paymentmode', db_prefix(). 'currencies.name as currency_name']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];

                $row[] = '<a href="' . admin_url('payments/payment/' . $aRow['id']) . '" target="_blank">' . _d($aRow['date']) . '</a>';

                $row[] = app_format_money($aRow['amount'], $aRow['currency_name'] ?? '');

                $row[] = $aRow['name'];
                $row[] = '<a href="' . admin_url('invoices/list_invoices/' . $aRow['invoiceid']) . '" target="_blank">' . format_invoice_number($aRow['invoiceid']) . '</a>';

                $status_name = _l('not_synchronized_yet');
                $label_class = 'default';

                if ($aRow['connect_id'] > 0) {
                    $label_class = 'success';
                    $status_name = _l('synchronized');
                }

                $row[] = '<span class="label label-' . $label_class . ' s-status payment-status-' . $aRow['id'] . '">' . $status_name . '</span>';
                $row[] = html_entity_decode($aRow['error_detail'] ?? '');

                if($organization_id != '' || $region == 'central_european'){
                    $row[] = icon_btn('#', 'fa fa-share', 'btn-success', [
                        'title' => _l('manual_sync'),
                        'data-id' =>$aRow['id'],
                        'data-organization-id' =>$organization_id,
                        'data-type' => 'payment',
                        'data-software' => $software,
                        'onclick' => 'manual_sync(this); return false;'
                    ]);
                }else{
                    $row[] = '';
                }

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    public function customers_table()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $currency = $this->currencies_model->get_base_currency();
            $region = get_option('acc_integration_sage_accounting_region');
            
            $select = [
                
                'company',
                'CONCAT(firstname, " ", lastname) as fullname',
                'email',
                db_prefix() . 'clients.phonenumber as phonenumber',
                db_prefix() . 'clients.active',
                '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM ' . db_prefix() . 'customer_groups JOIN ' . db_prefix() . 'customers_groups ON ' . db_prefix() . 'customer_groups.groupid = ' . db_prefix() . 'customers_groups.id WHERE customer_id = ' . db_prefix() . 'clients.userid ORDER by name ASC) as customerGroups',
                'connect_id',
                'error_detail',
                '1',
            ];
            $where = [];
            $software = $this->input->post('software');

            if ($this->input->post('status')) {
                $status = $this->input->post('status');
                $where_status = '';
                foreach ($status as $key => $value) {
                    if($value == 'synchronized'){
                        if($where_status != ''){
                            $where_status .= ' OR connect_id != ""';
                        }else{
                            $where_status .= 'connect_id != ""';
                        }
                    }

                    if($value == 'not_synchronized_yet'){
                        if($where_status != ''){
                            $where_status .= ' OR connect_id IS NULL';
                        }else{
                            $where_status .= 'connect_id IS NULL';
                        }
                    }
                }

                if($where_status != ''){
                    array_push($where, 'AND ('. $where_status . ')');
                }
            }
            
            $from_date = '';
            $to_date   = '';
            if ($this->input->post('from_date')) {
                $from_date = $this->input->post('from_date');
                $from_date = to_sql_date($from_date);
            }

            if ($this->input->post('to_date')) {
                $to_date = $this->input->post('to_date');
                $to_date = to_sql_date($to_date);
            }
            if ($from_date != '' && $to_date != '') {
                array_push($where, 'AND (' . db_prefix() . 'invoicepaymentrecords.date >= "' . $from_date . '" and ' . db_prefix() . 'invoicepaymentrecords.date <= "' . $to_date . '")');
            } elseif ($from_date != '') {
                array_push($where, 'AND (' . db_prefix() . 'invoicepaymentrecords.date >= "' . $from_date . '")');
            } elseif ($to_date != '') {
                array_push($where, 'AND (' . db_prefix() . 'invoicepaymentrecords.date <= "' . $to_date . '")');
            }

            $organization_id = $this->input->post('organization');

            $aColumns     = $select;
            $sIndexColumn = 'userid';
            $sTable       = db_prefix() . 'clients';
            $join         = [
                'LEFT JOIN ' . db_prefix() . 'contacts ON ' . db_prefix() . 'contacts.userid=' . db_prefix() . 'clients.userid AND ' . db_prefix() . 'contacts.is_primary=1',
                'LEFT JOIN ' . db_prefix() . 'acc_integration_logs ON ' . db_prefix() . 'acc_integration_logs.rel_id = ' . db_prefix() . 'clients.userid AND ' . db_prefix() . 'acc_integration_logs.rel_type = "customer" AND software = "'.$software.'" AND ' . db_prefix() . 'acc_integration_logs.organization_id = "'.$organization_id.'"',
                'LEFT JOIN ' . db_prefix() . 'acc_integration_error_logs ON ' . db_prefix() . 'acc_integration_error_logs.rel_id = ' . db_prefix() . 'clients.userid AND ' . db_prefix() . 'acc_integration_error_logs.rel_type = "customer" AND ' . db_prefix() . 'acc_integration_error_logs.software = "'.$software.'" AND ' . db_prefix() . 'acc_integration_error_logs.organization_id = "'.$organization_id.'"'

                        ];
                
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                db_prefix() . 'clients.userid as userid',
                db_prefix() . 'contacts.id as contact_id',
                'lastname',
                db_prefix() . 'clients.zip as zip',
                'registration_confirmed',
               ]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];

                // Company
                $company  = $aRow['company'];
                $isPerson = false;

                if ($company == '') {
                    $company  = _l('no_company_view_profile');
                    $isPerson = true;
                }

                $url = admin_url('clients/client/' . $aRow['userid']);

                if ($isPerson && $aRow['contact_id']) {
                    $url .= '?contactid=' . $aRow['contact_id'];
                }

                $company = '<a href="' . $url . '">' . $company . '</a>';


                $row[] = $company;

                // Primary contact
                $row[] = ($aRow['contact_id'] ? '<a href="' . admin_url('clients/client/' . $aRow['userid'] . '?contactid=' . $aRow['contact_id']) . '" target="_blank">' . trim($aRow['fullname']) . '</a>' : '');

                // Primary contact email
                $row[] = ($aRow['email'] ? '<a href="mailto:' . $aRow['email'] . '">' . $aRow['email'] . '</a>' : '');

                // Primary contact phone
                $row[] = ($aRow['phonenumber'] ? '<a href="tel:' . $aRow['phonenumber'] . '">' . $aRow['phonenumber'] . '</a>' : '');

                // Toggle active/inactive customer
                $toggleActive = '<div class="onoffswitch" data-toggle="tooltip" data-title="' . _l('customer_active_inactive_help') . '">
                <input type="checkbox"' . ($aRow['registration_confirmed'] == 0 ? ' disabled' : '') . ' data-switch-url="' . admin_url() . 'clients/change_client_status" name="onoffswitch" class="onoffswitch-checkbox" id="' . $aRow['userid'] . '" data-id="' . $aRow['userid'] . '" ' . ($aRow[db_prefix() . 'clients.active'] == 1 ? 'checked' : '') . '>
                <label class="onoffswitch-label" for="' . $aRow['userid'] . '"></label>
                </div>';

                // For exporting
                $toggleActive .= '<span class="hide">' . ($aRow[db_prefix() . 'clients.active'] == 1 ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';

                $row[] = $toggleActive;

                // Customer groups parsing
                $groupsRow = '';
                if ($aRow['customerGroups']) {
                    $groups = explode(',', $aRow['customerGroups']);
                    foreach ($groups as $group) {
                        $groupsRow .= '<span class="label label-default mleft5 customer-group-list pointer">' . $group . '</span>';
                    }
                }

                $row[] = $groupsRow;
                $status_name = _l('not_synchronized_yet');
                $label_class = 'default';

                if ($aRow['connect_id'] > 0) {
                    $label_class = 'success';
                    $status_name = _l('synchronized');
                }

                $row[] = '<span class="label label-' . $label_class . ' s-status">' . $status_name . '</span>';
                $row[] = html_entity_decode($aRow['error_detail'] ?? '');
                

                if($organization_id != '' || $region == 'central_european'){
                    $row[] = icon_btn('#', 'fa fa-share', 'btn-success', [
                        'title' => _l('manual_sync'),
                        'data-id' =>$aRow['userid'],
                        'data-organization-id' =>$organization_id,
                        'data-type' => 'customer',
                        'data-software' => $software,
                        'onclick' => 'manual_sync(this); return false;'
                    ]);
                }else{
                    $row[] = '';
                }

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    public function manual_sync()
    {
        $data = $this->input->post();

        $this->load->model('sage_accounting_integration_model');
        $init_function = 'init_'.$data['software'].'_config';
        $this->sage_accounting_integration_model->$init_function();


        $function = 'create_'.$data['software'].'_'.$data['type'];
        if(get_option('acc_integration_sage_accounting_region') == 'south_african'){
            $organization_id = $this->input->post('organization_id');
            $function .= '_sa';
            $result = $this->sage_accounting_integration_model->$function($organization_id, $data['id']);
        }else{
            $result = $this->sage_accounting_integration_model->$function($data['id']);
        }


        if($result === true){
            $success = true;
            $message = _l('sync_successfully');
        }else{
            $success = false;
            $message = $result;
        }

        echo json_encode(['success' => $success, 'message' => $message]);
        die();

    }

    public function sync_transaction_from_accounting(){
        $data = $this->input->post();

        $this->load->model('sage_accounting_integration_model');
        $init_function = 'init_'.$data['software'].'_config';
        $this->sage_accounting_integration_model->$init_function();

        $function = 'get_'.$data['software'].'_'.$data['type'];
        if(get_option('acc_integration_sage_accounting_region') == 'south_african'){
            $organization_id = $this->input->post('organization_id');
            $function .= '_sa';
            $result = $this->sage_accounting_integration_model->$function($organization_id);
        }else{
            $result = $this->sage_accounting_integration_model->$function();
        }


        $success = true;
        $message = _l('sync_successfully');

        echo json_encode(['success' => $success, 'message' => $message]);
        die();
    }

    public function connect(){
        $data = [];

        $configs = get_acc_sage_accounting_config();

        $client_id = $configs['client_id'];
        $client_secret = $configs['client_secret'];
        $authorizationRequestUrl = $configs['auth_url'];
        $scope = $configs['scope'];
        $redirect_uri = $configs['redirect_uri'];
        $response_type = 'code';

        $code = $this->input->get("code") ?? '';

        if ($code == '')
        {
            update_option('acc_integration_sage_accounting_connected', 0);
            update_option('acc_integration_sage_accounting_access_token', '');
            update_option('acc_integration_sage_accounting_access_token_expires', '');
            update_option('acc_integration_sage_accounting_refresh_token', '');
            update_option('acc_integration_sage_accounting_refresh_token_expires', '');

            $parameters = array(
              'scope' => $scope,
              'client_id' => $client_id,
              'response_type' => $response_type,
              'redirect_uri' => $redirect_uri,
            );

            $authorizationRequestUrl .= '?' . http_build_query($parameters, '', '&', PHP_QUERY_RFC1738);

            redirect($authorizationRequestUrl);
        }
        else
        {
            $parameters = array(
              'client_id' => $client_id,
              'client_secret' => $client_secret,
              'code' => $code,
              'grant_type' => 'authorization_code',
              'redirect_uri' => $redirect_uri,
            );

            $http_header = array(
                 'Accept' => 'application/json',
                 'Content-Type' => 'application/x-www-form-urlencoded'
            );

            $this->load->model('sage_accounting_integration_model');
            $result = $this->sage_accounting_integration_model->executeRequest($configs['access_token_url'],  $parameters, $http_header, 'POST');
            update_option('acc_integration_sage_accounting_connected', 1);
            update_option('acc_integration_sage_accounting_access_token', $result['access_token']);
            update_option('acc_integration_sage_accounting_access_token_expires', time() + $result['expires_in']);
            update_option('acc_integration_sage_accounting_refresh_token', $result['refresh_token']);
            update_option('acc_integration_sage_accounting_refresh_token_expires', time() + $result['refresh_token_expires_in']);
            update_option('acc_integration_sage_accounting_requested_by_id', $result['requested_by_id']);

            redirect('admin/sage_accounting_integration/setting');
        }
    }

    public function sync_logs(){
        $data= [];
        $data['title'] = _l('sync_logs');
        if(get_option('acc_integration_sage_accounting_region') == 'south_african'){
            $this->load->model('sage_accounting_integration_model');
            $data['organizations'] = $this->sage_accounting_integration_model->get_sage_accounting_organizations();
        }

        $this->load->view('sync_logs', $data);
    }

    public function sync_logs_table()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');
            $currency = $this->currencies_model->get_base_currency();
           
            $select = [
                'rel_type',
                'rel_id',
                'type',
                'status',
                'datecreated',
            ];
            $where = [];

            $software = $this->input->post('software');
            array_push($where, 'AND ( software = "'. $software . '")');

            if ($this->input->post('organization')) {
                $organization_id = $this->input->post('organization');
                array_push($where, 'AND ( organization_id = "'. $organization_id . '")');
            }
            
            if ($this->input->post('status')) {
                $status = $this->input->post('status');
                $where_status = '';
                foreach ($status as $key => $value) {
                    if($where_status != ''){
                        $where_status .= ' OR status = "'.$value.'"';
                    }else{
                        $where_status .= 'status = "'.$value.'"';
                    }
                }

                if($where_status != ''){
                    array_push($where, 'AND ('. $where_status . ')');
                }
            }

            if ($this->input->post('transaction_type')) {
                $transaction_type = $this->input->post('transaction_type');
                $where_transaction_type = '';
                foreach ($transaction_type as $key => $value) {
                    if($where_transaction_type != ''){
                        $where_transaction_type .= ' OR rel_type = "'.$value.'"';
                    }else{
                        $where_transaction_type .= 'rel_type = "'.$value.'"';
                    }
                }

                if($where_transaction_type != ''){
                    array_push($where, 'AND ('. $where_transaction_type . ')');
                }
            }


            if ($this->input->post('type')) {
                $type = $this->input->post('type');
                $where_type = '';
                foreach ($type as $key => $value) {
                    if($where_type != ''){
                        $where_type .= ' OR type = "'.$value.'"';
                    }else{
                        $where_type .= 'type = "'.$value.'"';
                    }
                }

                if($where_type != ''){
                    array_push($where, 'AND ('. $where_type . ')');
                }
            }
            
            $from_date = '';
            $to_date   = '';
            if ($this->input->post('from_date')) {
                $from_date = $this->input->post('from_date');
                $from_date = to_sql_date($from_date);
            }

            if ($this->input->post('to_date')) {
                $to_date = $this->input->post('to_date');
                $to_date = to_sql_date($to_date);
            }
            if ($from_date != '' && $to_date != '') {
                array_push($where, 'AND (datecreated >= "' . $from_date . '" and datecreated <= "' . $to_date . '")');
            } elseif ($from_date != '') {
                array_push($where, 'AND (datecreated >= "' . $from_date . '")');
            } elseif ($to_date != '') {
                array_push($where, 'AND (datecreated <= "' . $to_date . '")');
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'acc_integration_sync_logs';
            $join         = [
            ];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];

                $row[] = _l('sync_'.$aRow['rel_type']);
                $row[] = sync_get_url_by_type_id($aRow['rel_id'], $aRow['rel_type']);
                $row[] = _l($aRow['type']);
                $status_name = _l('fail');
                $label_class = 'danger';

                if ($aRow['status'] > 0) {
                    $label_class = 'success';
                    $status_name = _l('success');
                }
                
                $row[] = '<span class="label label-' . $label_class . ' s-status">' . $status_name . '</span>';
                $row[] = _d($aRow['datecreated']);
                
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    public function test_connect(){
        $data = $this->input->post();

        $this->load->model('sage_accounting_integration_model');
        $success = $this->sage_accounting_integration_model->test_connect($data);
        $message = _l('connection_failed');

        if ($success) {
            $message = _l('connection_successful');
        }

        echo json_encode(['success' => $success, 'message' => $message]);
        die();
    }
}