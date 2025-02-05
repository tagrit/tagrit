<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * This class describes a purchase.
 */
class purchase extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('purchase_model');
        hooks()->do_action('purchase_init');        
    }

    public function index(){
        if(is_staff_logged_in()){
            redirect(admin_url('purchase/reports'));
        }

        if(is_vendor_logged_in()){

            redirect(site_url('purchase/authentication_vendor'));
        }
    }

    /**
     * { vendors }
     */
    public function vendors(){
    	
        $data['title']          = _l('vendor');
        $data['vendor_categorys'] = $this->purchase_model->get_vendor_category();
        $this->load->view('vendors/manage', $data);
    }

    /**
     * { table vendor }
     */
    public function table_vendor()
    {

        $this->app->get_table_data(module_views_path('purchase', 'vendors/table_vendor'));
    }

    /**
     * { vendor }
     *
     * @param      string  $id     The vendor
     * @return      view
     */
    public function vendor($id = '')
    {
        
        if ($this->input->post() && !$this->input->is_ajax_request()) {
            if ($id == '') {
                

                $data = $this->input->post();

                $save_and_add_contact = false;
                if (isset($data['save_and_add_contact'])) {
                    unset($data['save_and_add_contact']);
                    $save_and_add_contact = true;
                }
                $id = $this->purchase_model->add_vendor($data);
                if (!has_permission('purchase_vendors', '', 'view')) {
                    $assign['customer_admins']   = [];
                    $assign['customer_admins'][] = get_staff_user_id();
                    $this->purchase_model->assign_vendor_admins($assign, $id);
                }
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('vendor')));
                    if ($save_and_add_contact == false) {
                        redirect(admin_url('purchase/vendor/' . $id));
                    } else {
                        redirect(admin_url('purchase/vendor/' . $id . '?group=contacts&new_contact=true'));
                    }
                }
            } else {

                if(has_permission('purchase_vendors', '', 'view_own') && !is_admin()){
                    if(!is_vendor_admin($id, get_staff_user_id())){
                        access_denied('purchase');
                    }
                }
                
                $success = $this->purchase_model->update_vendor($this->input->post(), $id);
                if ($success == true) {
                    set_alert('success', _l('updated_successfully', _l('vendor')));
                }
                redirect(admin_url('purchase/vendor/' . $id));
            }
        }

        $group         = !$this->input->get('group') ? 'profile' : $this->input->get('group');
        $data['group'] = $group;

        if ($group != 'contacts' && $contact_id = $this->input->get('contactid')) {
            redirect(admin_url('purchase/vendor/' . $id . '?group=contacts&contactid=' . $contact_id));
        }

        
        

        if ($id == '') {
            $title = _l('add_new', _l('vendor_lowercase'));
        } else {
            if(has_permission('purchase_vendors', '', 'view_own') && !is_admin()){
                if(!is_vendor_admin($id, get_staff_user_id())){
                    access_denied('purchase');
                }
            }

            $client                = $this->purchase_model->get_vendor($id);
            $data['customer_tabs'] = get_customer_profile_tabs();

            if (!$client) {
                show_404();
            }

            $data['contacts'] = $this->purchase_model->get_contacts($id);
            
            $data['payments'] = $this->purchase_model->get_payment_invoices_by_vendor($id);

            $data['group'] = $this->input->get('group');

            $data['vendor_contacts'] = $this->purchase_model->get_contacts($id);

	        $data['title']                 = _l('setting');
	        $data['tab'][] = ['name' => 'profile', 'icon' => '<i class="fa fa-user-circle menu-icon"></i>'];
	        $data['tab'][] = ['name' => 'contacts','icon' => '<i class="fa fa-users menu-icon"></i>'];
            $data['tab'][] = ['name' => 'quotations','icon' => '<i class="fa fa-file-powerpoint menu-icon"></i>'];
            $data['tab'][] = ['name' => 'contracts', 'icon' => '<i class="fa fa-file-text menu-icon"></i>'];
            $data['tab'][] = ['name' => 'purchase_order', 'icon' => '<i class="fa fa-cart-plus menu-icon"></i>'];
            $data['tab'][] = ['name' => 'purchase_invoice', 'icon' => '<i class="fa fa-clipboard menu-icon"></i>'];
            $data['tab'][] = ['name' => 'debit_notes', 'icon' => '<i class="fa fa-credit-card menu-icon"></i>'];
            $data['tab'][] = ['name' => 'purchase_statement', 'icon' => '<i class="fa fa-building menu-icon"></i>'];
            $data['tab'][] = ['name' => 'payments', 'icon' => '<i class="fa fa-usd menu-icon"></i>']; 
            $data['tab'][] = ['name' => 'expenses', 'icon' => '<i class="fa fa-tasks menu-icon"></i>']; 
            $data['tab'][] = ['name' => 'notes', 'icon' => '<i class="fa fa-sticky-note menu-icon"></i>'];
            $data['tab'][] = ['name' => 'attachments', 'icon' => '<i class="fa fa-paperclip menu-icon"></i>'];
	        
	        if($data['group'] == ''){
	            $data['group'] = 'profile';
	        }
	        $data['tabs']['view'] = 'vendors/groups/'.$data['group'];
            // Fetch data based on groups
            if ($data['group'] == 'profile') {
               $data['customer_admins'] = $this->purchase_model->get_vendor_admins($id);
            }  elseif ($group == 'estimates') {
                $this->load->model('estimates_model');
                $data['estimate_statuses'] = $this->estimates_model->get_statuses();
            } elseif ($group == 'notes') {
               
                $data['user_notes'] = $this->misc_model->get_notes($id, 'pur_vendor');
            } elseif ($group == 'payments') {
                $this->load->model('payment_modes_model');
                $data['payment_modes'] = $this->payment_modes_model->get();
            } elseif ($group == 'attachments') {
                $data['attachments'] = get_all_pur_vendor_attachments($id);
            } elseif ($group == 'expenses') {
                $this->load->model('expenses_model');
                $data['expenses'] = $this->expenses_model->get('', [ 'vendor' =>  $id ]);
            }

            $data['staff'] = $this->staff_model->get('', ['active' => 1]);

            $data['client'] = $client;
            $title          = $client->company;

            // Get all active staff members (used to add reminder)
            $data['members'] = $data['staff'];

            if (!empty($data['client']->company)) {
                // Check if is realy empty client company so we can set this field to empty
                // The query where fetch the client auto populate firstname and lastname if company is empty
                if (is_empty_vendor_company($data['client']->userid)) {
                    $data['client']->company = '';
                }
            }
        }

        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();

        if ($id != '') {
            $customer_currency = $data['client']->default_currency;

            foreach ($data['currencies'] as $currency) {
                if ($customer_currency != 0) {
                    if ($currency['id'] == $customer_currency) {
                        $customer_currency = $currency;

                        break;
                    }
                } else {
                    if ($currency['isdefault'] == 1) {
                        $customer_currency = $currency;

                        break;
                    }
                }
            }

            if (is_array($customer_currency)) {
                $customer_currency = (object) $customer_currency;
            }

            $data['customer_currency'] = $customer_currency;

            
        }

        $data['bodyclass'] = 'customer-profile dynamic-create-groups';
        $data['vendor_categories'] = $this->purchase_model->get_vendor_category();
        $data['title']     = $title;

        $this->load->view('vendors/vendor', $data);
    }

    /**
     * { setting }
     */
    public function setting(){
    	if (!is_admin() && !has_permission('purchase_settings', '', 'edit')) {
            access_denied('purchase');
        }
        $data['group'] = $this->input->get('group');
        $data['unit_tab'] = $this->input->get('tab');

        $data['title']                 = _l('setting');
       
		$this->db->where('module_name','warehouse');
        $module = $this->db->get(db_prefix().'modules')->row();
        $data['tab'][] = 'purchase_order_setting';
        $data['tab'][] = 'purchase_options';
        $data['tab'][] = 'units';
        $data['tab'][] = 'approval';
        $data['tab'][] = 'commodity_group';
        $data['tab'][] = 'sub_group';
        $data['tab'][] = 'vendor_category';
        $data['tab'][] = 'permissions';
        $data['tab'][] = 'order_return';
        $data['tab'][] = 'currency_rates';

        if($data['group'] == ''){
            $data['group'] = 'purchase_order_setting';
        }else if($data['group'] == 'units'){
            $data['unit_types'] = $this->purchase_model->get_unit_type();
        }

        if($data['group'] == 'currency_rates'){
            $this->load->model('currencies_model');
            $this->purchase_model->check_auto_create_currency_rate();

            $data['currencies'] = $this->currencies_model->get();
            if($data['unit_tab'] == ''){
                $data['unit_tab'] = 'general';
            }
        }


        $data['tabs']['view'] = 'includes/'.$data['group'];
        $data['commodity_group_types'] = $this->purchase_model->get_commodity_group_type();
        $data['sub_groups'] = $this->purchase_model->get_sub_group();
        $data['item_group'] = $this->purchase_model->get_item_group();
        $data['approval_setting'] = $this->purchase_model->get_approval_setting();
        $data['vendor_categories'] = $this->purchase_model->get_vendor_category();
        $data['staffs'] = $this->staff_model->get(); 
        
        $this->load->view('manage_setting', $data);
    }
    
    /**
     * { assign vendor admins }
     *
     * @param      string  $id     The identifier
     * @return      redirect
     */
    public function assign_vendor_admins($id)
    {
        if (!has_permission('purchase_vendors', '', 'create') && !has_permission('purchase_vendors', '', 'edit')) {
            access_denied('vendors');
        }
        $success = $this->purchase_model->assign_vendor_admins($this->input->post(), $id);
        if ($success == true) {
            set_alert('success', _l('updated_successfully', _l('vendor')));
        }

        redirect(admin_url('purchase/vendor/' . $id . '?tab=vendor_admins'));
    }

    /**
     * { delete vendor }
     *
     * @param      <type>  $id     The identifier
     * @return      redirect
     */
   	public function delete_vendor($id){
   		if (!has_permission('purchase_vendors', '', 'delete')) {
            access_denied('vendors');
        }
        if (!$id) {
            redirect(admin_url('purchase/vendors'));
        }
        $response = $this->purchase_model->delete_vendor($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('customer_delete_transactions_warning', _l('invoices') . ', ' . _l('estimates') . ', ' . _l('credit_notes')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('client')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('client_lowercase')));
        }
        redirect(admin_url('purchase/vendors'));
   	}

    /**
     * { form contact }
     *
     * @param      <type>  $customer_id  The customer identifier
     * @param      string  $contact_id   The contact identifier
     */
   	public function form_contact($customer_id, $contact_id = '')
    {
        if (!has_permission('purchase_vendors', '', 'view') && !has_permission('purchase_vendors', '', 'view_own')) {
            if (!is_vendor_admin($customer_id)) {
                echo _l('access_denied');
                die;
            }
        }
        $data['customer_id'] = $customer_id;
        $data['contactid']   = $contact_id;
        if ($this->input->post()) {
            $data             = $this->input->post();
            $data['password'] = $this->input->post('password', false);

            unset($data['contactid']);
            if ($contact_id == '') {
                if (!has_permission('purchase_vendors', '', 'create')) {
                    if (!is_vendor_admin($customer_id)) {
                        header('HTTP/1.0 400 Bad error');
                        echo json_encode([
                            'success' => false,
                            'message' => _l('access_denied'),
                        ]);
                        die;
                    }
                }
                $id      = $this->purchase_model->add_contact($data, $customer_id);
                $message = '';
                $success = false;
                if ($id) {
                   
                    $success = true;
                    $message = _l('added_successfully', _l('contact'));
                }
                echo json_encode([
                    'success'             => $success,
                    'message'             => $message,
                    'has_primary_contact' => (total_rows(db_prefix().'pur_contacts', ['userid' => $customer_id, 'is_primary' => 1]) > 0 ? true : false),
                    'is_individual'       => is_empty_vendor_company($customer_id) && total_rows(db_prefix().'pur_contacts', ['userid' => $customer_id]) == 1,
                ]);
                die;
            }
            if (!has_permission('purchase_vendors', '', 'edit')) {
                if (!is_vendor_admin($customer_id)) {
                    header('HTTP/1.0 400 Bad error');
                    echo json_encode([
                            'success' => false,
                            'message' => _l('access_denied'),
                        ]);
                    die;
                }
            }
            $original_contact = $this->purchase_model->get_contact($contact_id);
            $success          = $this->purchase_model->update_contact($data, $contact_id);
            $message          = '';
            $proposal_warning = false;
            $original_email   = '';
            $updated          = false;
            if (is_array($success)) {
                if (isset($success['set_password_email_sent'])) {
                    $message = _l('set_password_email_sent_to_client');
                } elseif (isset($success['set_password_email_sent_and_profile_updated'])) {
                    $updated = true;
                    $message = _l('set_password_email_sent_to_client_and_profile_updated');
                }
            } else {
                if ($success == true) {
                    $updated = true;
                    $message = _l('updated_successfully', _l('contact'));
                }
            }
       
            echo json_encode([
                    'success'             => $success,
                    'proposal_warning'    => $proposal_warning,
                    'message'             => $message,
                    'original_email'      => $original_email,
                    'has_primary_contact' => (total_rows(db_prefix().'pur_contacts', ['userid' => $customer_id, 'is_primary' => 1]) > 0 ? true : false),
                ]);
            die;
        }
        if ($contact_id == '') {
            $title = _l('add_new', _l('contact_lowercase'));
        } else {
            $data['contact'] = $this->purchase_model->get_contact($contact_id);

            if (!$data['contact']) {
                header('HTTP/1.0 400 Bad error');
                echo json_encode([
                    'success' => false,
                    'message' => 'Contact Not Found',
                ]);
                die;
            }
            $title = $data['contact']->firstname . ' ' . $data['contact']->lastname;
        }

        
        $data['title']                = $title;
        $this->load->view('vendors/modals/contact', $data);
    }

    /**
     * { vendor contacts }
     *
     * @param      <type>  $client_id  The client identifier
     */
    public function vendor_contacts($client_id)
    {
        $this->app->get_table_data(module_views_path('purchase', 'vendors/table_contacts'), [
            'client_id' => $client_id,
        ]);
    }

    /**
     * Determines if contact email exists.
     */
    public function contact_email_exists()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                // First we need to check if the email is the same
                $userid = $this->input->post('userid');
                if ($userid != '') {
                    $this->db->where('id', $userid);
                    $_current_email = $this->db->get(db_prefix() . 'pur_contacts')->row();
                    if ($_current_email->email == $this->input->post('email')) {
                        echo json_encode(true);
                        die();
                    }
                }
                $this->db->where('email', $this->input->post('email'));
                $total_rows = $this->db->count_all_results(db_prefix() . 'pur_contacts');
                if ($total_rows > 0) {
                    echo json_encode(false);
                } else {
                    echo json_encode(true);
                }
                die();
            }
        }
    }

    /**
     * { delete vendor contact }
     *
     * @param      string  $customer_id  The customer identifier
     * @param      <type>  $id           The identifier
     * @return     redirect
     */
    public function delete_vendor_contact($customer_id, $id)
    {
        if (!has_permission('purchase_vendors', '', 'delete')) {
            if (!is_customer_admin($customer_id)) {
                access_denied('vendors');
            }
        }

        $this->purchase_model->delete_contact($id);
        
        redirect(admin_url('purchase/vendor/' . $customer_id . '?group=contacts'));
    }


    /**
     * { all contacts }
     * @return     view
     */
    public function all_contacts()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('purchase', 'vendors/table_all_contacts'));
        }

        if (is_gdpr() && get_option('gdpr_enable_consent_for_contacts') == '1') {
            $this->load->model('gdpr_model');
            $data['consent_purposes'] = $this->gdpr_model->get_consent_purposes();
        }

        $data['title'] = _l('customer_contacts');
        $this->load->view('vendors/all_contacts', $data);
    }

    /**
     * { purchase request }
     * @return     view
     */
    public function purchase_request(){
        $this->load->model('departments_model');

        $data['title'] = _l('purchase_request');
        $data['vendors'] = $this->purchase_model->get_vendor();
        $data['departments'] = $this->departments_model->get();
        $data['vendor_contacts'] = $this->purchase_model->get_contacts();
        $this->load->view('purchase_request/manage', $data);
    }

    /**
     * { add update purchase request }
     *
     * @param      string  $id     The identifier
     * @return    redirect, view
     */
    public function pur_request($id = ''){
    	$this->load->model('departments_model');
        $this->load->model('staff_model');
        $this->load->model('projects_model');
        $this->load->model('currencies_model');
    	if($id == ''){
    		
    		if($this->input->post()){
    			$add_data = $this->input->post();
    			$id = $this->purchase_model->add_pur_request($add_data);
    			if($id){
    				set_alert('success',_l('added_pur_request'));
    			}
    			redirect(admin_url('purchase/purchase_request'));
    		}

    		$data['title'] = _l('add_new');
    	}else{
    		if($this->input->post()){
    			$edit_data = $this->input->post();
    			$success = $this->purchase_model->update_pur_request($edit_data,$id);
    			if($success == true){
    				set_alert('success',_l('updated_pur_request'));
    			}
    			redirect(admin_url('purchase/purchase_request'));
    		}

    		$data['pur_request_detail'] = json_encode($this->purchase_model->get_pur_request_detail($id));
    		$data['pur_request'] = $this->purchase_model->get_purchase_request($id);
            $data['taxes_data'] = $this->purchase_model->get_html_tax_pur_request($id);
    		$data['title'] = _l('edit');
    	}

        $data['base_currency'] = $this->currencies_model->get_base_currency();

        $purchase_request_row_template = $this->purchase_model->create_purchase_request_row_template();

        if($id != ''){
            $data['pur_request_detail'] = $this->purchase_model->get_pur_request_detail($id);
            $currency_rate = 1;
            if($data['pur_request']->currency != 0 && $data['pur_request']->currency_rate != null){
                $currency_rate = $data['pur_request']->currency_rate;
            }

            $to_currency = $data['base_currency']->name;
            if($data['pur_request']->currency != 0 && $data['pur_request']->to_currency != null) {
                $to_currency = $data['pur_request']->to_currency;
            }

            if (count($data['pur_request_detail']) > 0) { 
                $index_request = 0;
                foreach ($data['pur_request_detail'] as $request_detail) {
                    $index_request++;
                    $unit_name = pur_get_unit_name($request_detail['unit_id']);
                    $taxname = '';
                    $item_text = $request_detail['item_text'];

                    if(strlen($item_text) == 0){
                        $item_text = pur_get_item_variatiom($request_detail['item_code']);
                    }

                    $purchase_request_row_template .= $this->purchase_model->create_purchase_request_row_template('items[' . $index_request . ']', $request_detail['item_code'], $item_text, $request_detail['unit_price'], $request_detail['quantity'], $unit_name, $request_detail['unit_id'], $request_detail['into_money'], $request_detail['prd_id'], $request_detail['tax_value'], $request_detail['total'], $request_detail['tax_name'], $request_detail['tax_rate'], $request_detail['tax'], true, $currency_rate, $to_currency);
                }
            }
        }

        $data['currencies'] = $this->currencies_model->get();

        $data['vendors'] = $this->purchase_model->get_vendor();
        $data['purchase_request_row_template'] = $purchase_request_row_template;
        $data['invoices'] = $this->purchase_model->get_invoice_for_pr();
        $data['salse_estimates'] = $this->purchase_model->get_sale_estimate_for_pr();
        
        $data['taxes'] = $this->purchase_model->get_taxes();
        $data['projects'] = $this->projects_model->get();
        $data['staffs'] = $this->staff_model->get();
    	$data['departments'] = $this->departments_model->get();
    	$data['units'] = $this->purchase_model->get_units();

        // Old script  $data['items'] = $this->purchase_model->get_items();
    	$data['ajaxItems'] = false;

        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->purchase_model->pur_get_grouped('can_be_purchased');
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }
    	
        $this->load->view('purchase_request/pur_request', $data);
    }

    /**
     * { view pur request }
     *
     * @param      <type>  $id     The identifier
     * @return view
     */
    public function view_pur_request($id){
        if(!has_permission('purchase_request', '', 'view') && !has_permission('purchase_request', '', 'view_own')){
            access_denied('purchase');
        }

    	$this->load->model('departments_model');
        $this->load->model('currencies_model');

        $send_mail_approve = $this->session->userdata("send_mail_approve");
        if((isset($send_mail_approve)) && $send_mail_approve != ''){
            $data['send_mail_approve'] = $send_mail_approve;
            $this->session->unset_userdata("send_mail_approve");
        }
        $data['pur_request'] = $this->purchase_model->get_purchase_request($id);

        if(has_permission('purchase_request', '', 'view_own') && !is_admin()){
            $staffid = get_staff_user_id();
            $in_vendor = false;

            if( $data['pur_request']->send_to_vendors != null &&  $data['pur_request']->send_to_vendors != ''){
                $send_to_vendors_ids = explode(',', $data['pur_request']->send_to_vendors);

                $list_vendor = get_vendor_admin_list($staffid);
                foreach($list_vendor as $vendor_id){
                    if(in_array($vendor_id, $send_to_vendors_ids)){
                        $in_vendor = true;
                    }
                }

            }

            $approve_access = total_rows(db_prefix().'pur_approval_details', ['staffid' => $staffid, 'rel_type' => 'pur_request', 'rel_id' => $id]);

            if( $data['pur_request']->requester != $staffid && $in_vendor == false && $approve_access == 0){
                access_denied('purchase');
            }
        }

        if(!$data['pur_request']){
            show_404();
        }

    	$data['pur_request_detail'] = $this->purchase_model->get_pur_request_detail($id);
		$data['title'] = $data['pur_request']->pur_rq_name;
		$data['departments'] = $this->departments_model->get();
    	$data['units'] = $this->purchase_model->get_units();
    	$data['items'] = $this->purchase_model->get_items();
    	$data['taxes_data'] = $this->purchase_model->get_html_tax_pur_request($id);
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['check_appr'] = $this->purchase_model->get_approve_setting('pur_request');
        $data['get_staff_sign'] = $this->purchase_model->get_staff_sign($id,'pur_request');
        $data['check_approve_status'] = $this->purchase_model->check_approval_details($id,'pur_request');
        $data['list_approve_status'] = $this->purchase_model->get_list_approval_details($id,'pur_request');
        $data['taxes'] = $this->purchase_model->get_taxes();
        $data['pur_request_attachments'] = $this->purchase_model->get_purchase_request_attachments($id);

        $this->load->view('purchase_request/view_pur_request', $data);

    }

    /**
     * { approval setting }
     * @return redirect
     */
    public function approval_setting()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            if ($data['approval_setting_id'] == '') {
                $message = '';
                $success = $this->purchase_model->add_approval_setting($data);
                if ($success) {
                    $message = _l('added_successfully', _l('approval_setting'));
                }
                set_alert('success', $message);
                redirect(admin_url('purchase/setting?group=approval'));
            } else {
                $message = '';
                $id = $data['approval_setting_id'];
                $success = $this->purchase_model->edit_approval_setting($id, $data);
                if ($success) {
                    $message = _l('updated_successfully', _l('approval_setting'));
                }
                set_alert('success', $message);
                redirect(admin_url('purchase/setting?group=approval'));
            }
        }
    }

    /**
     * { delete approval setting }
     *
     * @param      <type>  $id     The identifier
     * @return redirect
     */
    public function delete_approval_setting($id)
    {
        if (!$id) {
            redirect(admin_url('purchase/setting?group=approval'));
        }
        $response = $this->purchase_model->delete_approval_setting($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('approval_setting')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('approval_setting')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('approval_setting')));
        }
        redirect(admin_url('purchase/setting?group=approval'));
    }

    /**
     * { items change event}
     *
     * @param      <type>  $val    The value
     * @return      json
     */
    public function items_change($val){

        $value = $this->purchase_model->items_change($val);
        
        echo json_encode([
            'value' => $value
        ]);
        die;
    }

    /**
     * { table pur request }
     */
    public function table_pur_request(){
    	 $this->app->get_table_data(module_views_path('purchase', 'purchase_request/table_pur_request'));
    }

    /**
     * { delete pur request }
     *
     * @param      <type>  $id     The identifier
     * @return     redirect
     */
    public function delete_pur_request($id){
    	if (!$id) {
            redirect(admin_url('purchase/purchase_request'));
        }
        $response = $this->purchase_model->delete_pur_request($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('purchase_request')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('purchase_request')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('purchase_request')));
        }
        redirect(admin_url('purchase/purchase_request'));
    }

    /**
     * { change status pur request }
     *
     * @param      <type>  $status  The status
     * @param      <type>  $id      The identifier
     * @return     json
     */
    public function change_status_pur_request($status,$id){
    	$change = $this->purchase_model->change_status_pur_request($status,$id);
        if($change == true){
            
            $message = _l('change_status_pur_request').' '._l('successfully');
            echo json_encode([
                'result' => $message,
            ]);
        }else{
            $message = _l('change_status_pur_request').' '._l('fail');
            echo json_encode([
                'result' => $message,
            ]);
        }
    }

    /**
     * { quotations }
     *
     * @param      string  $id     The identifier
     * @return     view
     */
    public function quotations($id = ''){
    	if (!has_permission('purchase_quotations', '', 'view') && !is_admin() && !has_permission('purchase_quotations', '', 'view_own')) {
            access_denied('quotations');
        }

            // Pipeline was initiated but user click from home page and need to show table only to filter
        if ($this->input->get('status') || $this->input->get('filter') && $isPipeline) {
            $this->pipeline(0, true);
        }

        $data['estimateid']            = $id;
        $data['pur_request'] = $this->purchase_model->get_purchase_request();
        $data['vendors'] = $this->purchase_model->get_vendor();
        $data['title']                 = _l('estimates');
        $data['bodyclass']             = 'estimates-total-manual';
        
        $this->load->view('quotations/manage', $data);
    
    }

    /**
     * { function_description }
     *
     * @param      string  $id     The identifier
     * @return     redirect
     */
    public function estimate($id = '')
    {
        $this->load->model('currencies_model');
        if ($this->input->post()) {
            $estimate_data = $this->input->post();
            $estimate_data['terms'] = $this->input->post('terms', false);
            if ($id == '') {
                if (!has_permission('purchase_quotations', '', 'create')) {
                    access_denied('quotations');
                }
                $id = $this->purchase_model->add_estimate($estimate_data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('estimate')));
                    
                    redirect(admin_url('purchase/quotations/' . $id));
                    
                }
            } else {
                if (!has_permission('purchase_quotations', '', 'edit')) {
                    access_denied('quotations');
                }
                $success = $this->purchase_model->update_estimate($estimate_data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('estimate')));
                }
                redirect(admin_url('purchase/quotations/' . $id));
                
            }
        }
        if ($id == '') {
            $title = _l('create_new_estimate');
        } else {
            $estimate = $this->purchase_model->get_estimate($id);

            $data['tax_data'] = $this->purchase_model->get_html_tax_pur_estimate($id);
            
            $data['estimate'] = $estimate;
            $data['edit']     = true;
            $title            = _l('edit', _l('estimate_lowercase'));
        }
        if ($this->input->get('customer_id')) {
            $data['customer_id'] = $this->input->get('customer_id');
        }

        $data['base_currency'] = $this->currencies_model->get_base_currency();

        $pur_quotation_row_template = $this->purchase_model->create_quotation_row_template();

        if($id != ''){
            $data['estimate_detail'] = $this->purchase_model->get_pur_estimate_detail($id);
            $currency_rate = 1;
            if($data['estimate']->currency != 0 && $data['estimate']->currency_rate != null){
                $currency_rate = $data['estimate']->currency_rate;
            }

            $to_currency = $data['base_currency']->name;
            if($data['estimate']->currency != 0 && $data['estimate']->to_currency != null) {
                $to_currency = $data['estimate']->to_currency;
            }


            if (count($data['estimate_detail']) > 0) { 
                $index_quote = 0;
                foreach ($data['estimate_detail'] as $quote_detail) { 
                    $index_quote++;
                    $unit_name = pur_get_unit_name($quote_detail['unit_id']);
                    $taxname = $quote_detail['tax_name'];
                    $item_name = $quote_detail['item_name'];

                    if(strlen($item_name) == 0){
                        $item_name = pur_get_item_variatiom($quote_detail['item_code']);
                    }

                    $pur_quotation_row_template .= $this->purchase_model->create_quotation_row_template('items[' . $index_quote . ']',  $item_name, $quote_detail['quantity'], $unit_name, $quote_detail['unit_price'], $taxname, $quote_detail['item_code'], $quote_detail['unit_id'], $quote_detail['tax_rate'],  $quote_detail['total_money'], $quote_detail['discount_%'], $quote_detail['discount_money'], $quote_detail['total'], $quote_detail['into_money'], $quote_detail['tax'], $quote_detail['tax_value'], $quote_detail['id'], true, $currency_rate, $to_currency);
                }
            }

        }

        $data['pur_quotation_row_template'] = $pur_quotation_row_template;

        $this->load->model('taxes_model');
        $data['taxes'] = $this->purchase_model->get_taxes();
        
        $data['currencies'] = $this->currencies_model->get();

        $this->load->model('invoice_items_model');

        $data['ajaxItems'] = false;
        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->purchase_model->pur_get_grouped('can_be_purchased');
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }
        
        $data['items_groups'] = $this->invoice_items_model->get_groups();

        $data['staff']             = $this->staff_model->get('', ['active' => 1]);
        $data['vendors'] = $this->purchase_model->get_vendor();
        $data['pur_request'] = $this->purchase_model->get_pur_request_by_status(2);
        $data['units'] = $this->purchase_model->get_units();
       
        $data['title']             = $title;
        $this->load->view('quotations/estimate', $data);
    }

    /**
     * { validate estimate number }
     */
    public function validate_estimate_number()
    {
        $isedit          = $this->input->post('isedit');
        $number          = $this->input->post('number');
        $date            = $this->input->post('date');
        $original_number = $this->input->post('original_number');
        $number          = trim($number);
        $number          = ltrim($number, '0');

        if ($isedit == 'true') {
            if ($number == $original_number) {
                echo json_encode(true);
                die;
            }
        }

        if (total_rows(db_prefix().'pur_estimates', [
            'YEAR(date)' => date('Y', strtotime(to_sql_date($date))),
            'number' => $number,
        ]) > 0) {
            echo 'false';
        } else {
            echo 'true';
        }
    }

    /**
     * { table estimates }
     */
    public function table_estimates(){
        $this->app->get_table_data(module_views_path('purchase', 'quotations/table_estimates'));
    }

    /**
     * Gets the estimate data ajax.
     *
     * @param      <type>   $id         The identifier
     * @param      boolean  $to_return  To return
     *
     * @return     <type>   view.
     */
    public function get_estimate_data_ajax($id, $to_return = false)
    {
        if (!has_permission('purchase_quotations', '', 'view') && !is_admin() && !has_permission('purchase_quotations', '', 'view_own')) {
            echo _l('access_denied');
            die;
        }

        if (!$id) {
            die('No estimate found');
        }

        $estimate = $this->purchase_model->get_estimate($id);

        if(has_permission('purchase_quotations', '', 'view_own') && !is_admin()){
            $staffid = get_staff_user_id();

            $approve_access = total_rows(db_prefix().'pur_approval_details', ['staffid' => $staffid, 'rel_type' => 'pur_quotation', 'rel_id' => $id]);

            if($estimate->buyer != $staffid && $estimate->addedfrom != $staffid && !is_vendor_admin($estimate->vendor->userid) && $approve_access == 0){
                echo _l('access_denied');
                die;
            }
        }

        $estimate->date       = _d($estimate->date);
        $estimate->expirydate = _d($estimate->expirydate);
    

        if ($estimate->sent == 0) {
            $template_name = 'estimate_send_to_customer';
        } else {
            $template_name = 'estimate_send_to_customer_already_sent';
        }

        $data['pur_estimate_attachments'] = $this->purchase_model->get_purchase_estimate_attachments($id);
        $data['estimate_detail'] = $this->purchase_model->get_pur_estimate_detail($id);
        $data['estimate']          = $estimate;
        $data['members']           = $this->staff_model->get('', ['active' => 1]);
        $data['vendor_contacts'] = $this->purchase_model->get_contacts($estimate->vendor->userid);
        $send_mail_approve = $this->session->userdata("send_mail_approve");
        if((isset($send_mail_approve)) && $send_mail_approve != ''){
            $data['send_mail_approve'] = $send_mail_approve;
            $this->session->unset_userdata("send_mail_approve");
        }
        $data['check_appr'] = $this->purchase_model->get_approve_setting('pur_quotation');
        $data['get_staff_sign'] = $this->purchase_model->get_staff_sign($id,'pur_quotation');
        $data['check_approve_status'] = $this->purchase_model->check_approval_details($id,'pur_quotation');
        $data['list_approve_status'] = $this->purchase_model->get_list_approval_details($id,'pur_quotation');
        $data['tax_data'] = $this->purchase_model->get_html_tax_pur_estimate($id);
        
        if ($to_return == false) {
            $this->load->view('quotations/estimate_preview_template', $data);
        } else {
            return $this->load->view('quotations/estimate_preview_template', $data, true);
        }
    }

    /**
     * { delete estimate }
     *
     * @param      <type>  $id     The identifier
     * @return     redirect
     */
    public function delete_estimate($id)
    {
        if (!has_permission('purchase_quotations', '', 'delete')) {
            access_denied('estimates');
        }
        if (!$id) {
            redirect(admin_url('purchase/quotations'));
        }
        $success = $this->purchase_model->delete_estimate($id);
        if (is_array($success)) {
            set_alert('warning', _l('is_invoiced_estimate_delete_error'));
        } elseif ($success == true) {
            set_alert('success', _l('deleted', _l('estimate')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('estimate_lowercase')));
        }
        redirect(admin_url('purchase/quotations'));
    }

    /**
     * { tax change event }
     *
     * @param      <type>  $tax    The tax
     * @return   json
     */
    public function tax_change($tax){
        $this->load->model('taxes_model');

        $taxes = explode('%7C', $tax);
        $total_tax = $this->purchase_model->get_total_tax($taxes);
        $tax_arr = [];
        foreach($taxes as $t){
            
            $tax_if = $this->taxes_model->get($t);
            if($tax_if){
                $tax_arr[$tax_if->id] = $tax_if->taxrate;
            }
            
        }
        
        echo json_encode([
            'total_tax' => $total_tax,
            'taxes' => $tax_arr
        ]);
    }

    /**
     * { coppy pur request }
     *
     * @param      <type>  $pur_request  The purchase request id
     * @return json
     */
    public function coppy_pur_request($pur_request){
        $this->load->model('currencies_model');

        $pur_request_detail = $this->purchase_model->get_pur_request_detail_in_estimate($pur_request);
        $purchase_request = $this->purchase_model->get_purchase_request($pur_request);

        $base_currency = $this->currencies_model->get_base_currency();
        $taxes = [];
        $tax_val = [];
        $tax_name = [];
        $subtotal = 0;
        $total = 0;
        $data_rs = [];
        $tax_html = '';
        
        if(count($pur_request_detail) > 0){
            foreach($pur_request_detail as $key => $item){
                $subtotal += $item['into_money'];
                $total += $item['total'];
            }
        }

        $list_item = $this->purchase_model->create_quotation_row_template();

        $currency_rate = 1;
        $to_currency = $base_currency->id;
        if($purchase_request->currency != 0 && $purchase_request->currency_rate != null){
            $currency_rate = $purchase_request->currency_rate;
            $to_currency = $purchase_request->currency;
        }

        if(count($pur_request_detail) > 0){
            $index_quote = 0;
            foreach($pur_request_detail as $key => $item){
                $index_quote++;
                $unit_name = pur_get_unit_name($item['unit_id']);
                $taxname = $item['tax_name'];
                $item_name = $item['item_text'];

                if(strlen($item_name) == 0){
                    $item_name = pur_get_item_variatiom($item['item_code']);
                }

                $list_item .= $this->purchase_model->create_quotation_row_template('newitems[' . $index_quote . ']',  $item_name, $item['quantity'], $unit_name, $item['unit_price'], $taxname, $item['item_code'], $item['unit_id'], $item['tax_rate'],  $item['total'], '', '', $item['total'], $item['into_money'], $item['tax'], $item['tax_value'], $index_quote, true, $currency_rate, $to_currency);
            }
        }
        

        $taxes_data = $this->purchase_model->get_html_tax_pur_request($pur_request);
        $tax_html = $taxes_data['html'];

        echo json_encode([
            'result' => $pur_request_detail,
            'subtotal' => app_format_money(round($subtotal,2),''),
            'total' => app_format_money(round($total, 2),''),
            'tax_html' => $tax_html,
            'taxes' => $taxes,
            'list_item' => $list_item,
            'currency' => $to_currency,
            'currency_rate' => $currency_rate,
        ]);
    }

    /**
     * { coppy pur request }
     *
     * @param      <type>  $pur_request  The purchase request id
     * @return json
     */
    public function coppy_pur_request_for_po($pur_request, $vendor = ''){

        $this->load->model('currencies_model');

        $pur_request_detail = $this->purchase_model->get_pur_request_detail_in_po($pur_request);
        $purchase_request = $this->purchase_model->get_purchase_request($pur_request);

        $base_currency = $this->currencies_model->get_base_currency();
        $taxes = [];
        $tax_val = [];
        $tax_name = [];
        $subtotal = 0;
        $total = 0;
        $data_rs = [];
        $tax_html = '';
        $estimate_html = '';

        $estimate_html .= $this->purchase_model->get_estimate_html_by_pr_vendor($pur_request, $vendor);
        
        if(count($pur_request_detail) > 0){
            foreach($pur_request_detail as $key => $item){
                $subtotal += $item['into_money'];
                $total += $item['total'];
            }
        }

        $list_item = $this->purchase_model->create_purchase_order_row_template();

        $currency_rate = 1;
        $to_currency = $base_currency->id;
        if($purchase_request->currency != 0 && $purchase_request->currency_rate != null){
            $currency_rate = $purchase_request->currency_rate;
            $to_currency = $purchase_request->currency;
        }


        if(count($pur_request_detail) > 0){
            $index_quote = 0;
            foreach($pur_request_detail as $key => $item){
                $index_quote++;
                $unit_name = pur_get_unit_name($item['unit_id']);
                $taxname = $item['tax_name'];
                $item_name = $item['item_text'];

                if(strlen($item_name) == 0){
                    $item_name = pur_get_item_variatiom($item['item_code']);
                }

                $list_item .= $this->purchase_model->create_purchase_order_row_template('newitems[' . $index_quote . ']',  $item_name,'', $item['quantity'], $unit_name, $item['unit_price'], $taxname, $item['item_code'], $item['unit_id'], $item['tax_rate'],  $item['total'], '', '', $item['total'], $item['into_money'], $item['tax'], $item['tax_value'], $index_quote, true, $currency_rate, $to_currency);
            }
        }

        $taxes_data = $this->purchase_model->get_html_tax_pur_request($pur_request);
        $tax_html = $taxes_data['html'];

        echo json_encode([
            'result' => $pur_request_detail,
            'subtotal' => app_format_money(round($subtotal,2),''),
            'total' => app_format_money(round($total, 2),''),
            'tax_html' => $tax_html,
            'taxes' => $taxes,
            'list_item' => $list_item,
            'currency' => $to_currency,
            'currency_rate' => $currency_rate,
            'estimate_html' => $estimate_html,
        ]);
    }

    /**
     * { coppy pur estimate }
     *
     * @param      <type>  $pur_estimate  The purchase estimate id
     * @return  json
     */
    public function coppy_pur_estimate($pur_estimate_id){
        $this->load->model('currencies_model');
        $pur_estimate_detail = $this->purchase_model->get_pur_estimate_detail_in_order($pur_estimate_id);
        $pur_estimate = $this->purchase_model->get_estimate($pur_estimate_id);

        $taxes = [];
        $tax_val = [];
        $tax_name = [];
        $subtotal = 0;
        $total = 0;
        $data_rs = [];
        $tax_html = '';
        
        if(count($pur_estimate_detail) > 0){
            foreach($pur_estimate_detail as $key => $item){
                $subtotal += $item['into_money'];
                $total += $item['total'];
            }
        }

        $base_currency = $this->currencies_model->get_base_currency();
        $list_item = $this->purchase_model->create_purchase_order_row_template();

        $currency_rate = 1;
        $to_currency = $base_currency->id;
        if($pur_estimate->currency != 0 && $pur_estimate->currency_rate != null){
            $currency_rate = $pur_estimate->currency_rate;
            $to_currency = $pur_estimate->currency;
        }

        if(count($pur_estimate_detail) > 0){
            $index = 0;
            foreach($pur_estimate_detail as $key => $item){
                $index++;
                $unit_name = pur_get_unit_name($item['unit_id']);
                $taxname = $item['tax_name'];
                $item_name = $item['item_name'];
                if(strlen($item_name) == 0){
                    $item_name = pur_get_item_variatiom($item['item_code']);
                }

                $list_item .= $this->purchase_model->create_purchase_order_row_template('newitems[' . $index . ']',  $item_name, '', $item['quantity'], $unit_name, $item['unit_price'], $taxname, $item['item_code'], $item['unit_id'], $item['tax_rate'],  $item['total_money'], $item['discount_%'], $item['discount_money'], $item['total'], $item['into_money'], $item['tax'], $item['tax_value'], $index, true, $currency_rate, $to_currency);
            }
        }

        $taxes_data = $this->purchase_model->get_html_tax_pur_estimate($pur_estimate_id);
        $tax_html = $taxes_data['html'];

        echo json_encode([
            'result' => $pur_estimate_detail,
            'dc_percent' => $pur_estimate->discount_percent,
            'dc_total' => $pur_estimate->discount_total,
            'discount_type' => $pur_estimate->discount_type,
            'subtotal' => app_format_money(round($subtotal,2),''),
            'total' => app_format_money(round($total, 2),''),
            'tax_html' => $tax_html,
            'taxes' => $taxes,
            'list_item' => $list_item,
            'currency' => $to_currency,
            'currency_rate' => $currency_rate,
            'shipping_fee' => $pur_estimate->shipping_fee
        ]);
    }

    /**
     * { view purchase order }
     *
     * @param      <type>  $pur_order  The purchase order id
     * @return json
     */
    public function view_pur_order($pur_order){
        $pur_order_detail = $this->purchase_model->get_pur_order_detail($pur_order);
        $pur_order = $this->purchase_model->get_pur_order($pur_order);
        $base_currency = get_base_currency_pur();

        $total = $pur_order->total;
        $rate = 1;
        if($base_currency->id != $pur_order->currency && $pur_order->currency != 0){
            $po_currency = pur_get_currency_by_id($pur_order->currency);

            $rate = pur_get_currency_rate($po_currency->name, $base_currency->name);
        }

        $total = $total*$rate;

        echo json_encode([
            'total' => app_format_money($total,''),
            'vendor' => $pur_order->vendor,
            'buyer' => $pur_order->buyer,
            'project' => $pur_order->project,
            'department' => $pur_order->department,
        ]);
    }

    /**
     * { change status pur estimate }
     *
     * @param      <type>  $status  The status
     * @param      <type>  $id      The identifier
     * @return json
     */
    public function change_status_pur_estimate($status,$id){
        $change = $this->purchase_model->change_status_pur_estimate($status,$id);
        if($change == true){
            
            $message = _l('change_status_pur_estimate').' '._l('successfully');
            echo json_encode([
                'result' => $message,
            ]);
        }else{
            $message = _l('change_status_pur_estimate').' '._l('fail');
            echo json_encode([
                'result' => $message,
            ]);
        }
    }

    /**
     * { change status pur order }
     *
     * @param      <type>  $status  The status
     * @param      <type>  $id      The identifier
     * @return json
     */
    public function change_status_pur_order($status,$id){
        $change = $this->purchase_model->change_status_pur_order($status,$id);
        if($change == true){
            
            $message = _l('change_status_pur_order').' '._l('successfully');
            echo json_encode([
                'result' => $message,
            ]);
        }else{
            $message = _l('change_status_pur_order').' '._l('fail');
            echo json_encode([
                'result' => $message,
            ]);
        }
    }

    /**
     * { purchase order }
     *
     * @param      string  $id     The identifier
     * @return view
     */
    public function purchase_order($id = ''){
        if(!has_permission('purchase_orders', '', 'view') && !is_admin() && !has_permission('purchase_orders', '', 'view_own')){
            access_denied('purchase');
        }
        $this->load->model('expenses_model');
        $this->load->model('payment_modes_model');
        $this->load->model('taxes_model');
        $this->load->model('currencies_model');
        $this->load->model('departments_model');
        $this->load->model('projects_model');
        $this->load->model('clients_model');

        $data['pur_orderid']            = $id;
        $data['title'] = _l('purchase_order');

        $data['departments'] = $this->departments_model->get();
        $data['projects'] = $this->projects_model->get();
        $data['currency'] = $this->currencies_model->get_base_currency();
        $data['payment_modes'] = $this->payment_modes_model->get('', [], true);
        $data['currencies']         = $this->currencies_model->get();
        $data['taxes']              = $this->taxes_model->get();
        $data['vendors'] = $this->purchase_model->get_vendor();
        $data['expense_categories'] = $this->expenses_model->get_category();
        $data['item_tags'] = $this->purchase_model->get_item_tag_filter();
        $data['customers'] = $this->clients_model->get();
        $data['pur_request'] = $this->purchase_model->get_pur_request_by_status(2);

        $data['projects'] = $this->projects_model->get();
        
        $this->load->view('purchase_order/manage', $data);
    }

    /**
     * Gets the pur order data ajax.
     *
     * @param      <type>   $id         The identifier
     * @param      boolean  $to_return  To return
     *
     * @return     view.
     */
    public function get_pur_order_data_ajax($id, $to_return = false)
    {
        if (!has_permission('purchase_orders', '', 'view') && !has_permission('purchase_orders', '', 'view_own')) {
            echo _l('access_denied');
            die;
        }

        if (!$id) {
            die('No purchase order found');
        }

        $estimate = $this->purchase_model->get_pur_order($id);

        if(has_permission('purchase_orders', '', 'view_own') && !is_admin()){
            $staffid = get_staff_user_id();

            $approve_access = total_rows(db_prefix().'pur_approval_details', ['staffid' => $staffid, 'rel_type' => 'pur_order', 'rel_id' => $id]);

            if($estimate->buyer != $staffid && $estimate->addedfrom != $staffid && !is_vendor_admin($estimate->vendor) && $approve_access == 0){
                echo _l('access_denied');
                die;
            }
        }

        $this->load->model('payment_modes_model');
        $data['payment_modes'] = $this->payment_modes_model->get('', [
            'expenses_only !=' => 1,
        ]);

        $data['payment'] = $this->purchase_model->get_inv_payment_purchase_order($id);
        $data['pur_order_attachments'] = $this->purchase_model->get_purchase_order_attachments($id);
        $data['estimate_detail'] = $this->purchase_model->get_pur_order_detail($id);
        $data['estimate']          = $estimate;
        $data['members']           = $this->staff_model->get('', ['active' => 1]);
        $data['vendor_contacts'] = $this->purchase_model->get_contacts($estimate->vendor);
        $send_mail_approve = $this->session->userdata("send_mail_approve");
        if((isset($send_mail_approve)) && $send_mail_approve != ''){
            $data['send_mail_approve'] = $send_mail_approve;
            $this->session->unset_userdata("send_mail_approve");
        }
        $data['invoices'] = $this->purchase_model->get_invoices_by_po($id);
        $data['check_appr'] = $this->purchase_model->get_approve_setting('pur_order');
        $data['get_staff_sign'] = $this->purchase_model->get_staff_sign($id,'pur_order');
        $data['check_approve_status'] = $this->purchase_model->check_approval_details($id,'pur_order');
        $data['list_approve_status'] = $this->purchase_model->get_list_approval_details($id,'pur_order');
        $data['tax_data'] = $this->purchase_model->get_html_tax_pur_order($id);
        
        if ($to_return == false) {
            $this->load->view('purchase_order/pur_order_preview', $data);
        } else {
            return $this->load->view('purchase_order/pur_order_preview', $data, true);
        }
    }

    /**
     * { purchase order form }
     *
     * @param      string  $id     The identifier
     * @return redirect, view
     */
    public function pur_order($id = ''){
        if ($this->input->post()) {
            $pur_order_data = $this->input->post();
            $pur_order_data['terms'] = $this->input->post('terms', false);
            $pur_order_data['vendornote'] = $this->input->post('vendornote', false);
            if ($id == '') {
                if (!has_permission('purchase_orders', '', 'create')) {
                    access_denied('purchase_order');
                }
                $id = $this->purchase_model->add_pur_order($pur_order_data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('pur_order')));
                    
                    redirect(admin_url('purchase/purchase_order/' . $id));
                    
                }
            } else {
                if (!has_permission('purchase_orders', '', 'edit')) {
                    access_denied('purchase_order');
                }
                $success = $this->purchase_model->update_pur_order($pur_order_data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('pur_order')));
                }
                redirect(admin_url('purchase/purchase_order/' . $id));
                
            }
        }

        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();

        $pur_order_row_template = $this->purchase_model->create_purchase_order_row_template();

        if ($id == '') {
            $title = _l('create_new_pur_order');
        } else {
            $data['pur_order_detail'] = $this->purchase_model->get_pur_order_detail($id);
            $data['pur_order'] = $this->purchase_model->get_pur_order($id);

            $currency_rate = 1;
            if($data['pur_order']->currency != 0 && $data['pur_order']->currency_rate != null){
                $currency_rate = $data['pur_order']->currency_rate;
            }

            $to_currency = $data['base_currency']->name;
            if($data['pur_order']->currency != 0 && $data['pur_order']->to_currency != null) {
                $to_currency = $data['pur_order']->to_currency;
            }


            $data['tax_data'] = $this->purchase_model->get_html_tax_pur_order($id);
            $title = _l('pur_order_detail');

            if (count($data['pur_order_detail']) > 0) { 
                $index_order = 0;
                foreach ($data['pur_order_detail'] as $order_detail) { 
                    $index_order++;
                    $unit_name = pur_get_unit_name($order_detail['unit_id']);
                    $taxname = $order_detail['tax_name'];
                    $item_name = $order_detail['item_name'];

                    if(strlen($item_name) == 0){
                        $item_name = pur_get_item_variatiom($order_detail['item_code']);
                    }

                    $pur_order_row_template .= $this->purchase_model->create_purchase_order_row_template('items[' . $index_order . ']',  $item_name, $order_detail['description'], $order_detail['quantity'], $unit_name, $order_detail['unit_price'], $taxname, $order_detail['item_code'], $order_detail['unit_id'], $order_detail['tax_rate'],  $order_detail['total_money'], $order_detail['discount_%'], $order_detail['discount_money'], $order_detail['total'], $order_detail['into_money'], $order_detail['tax'], $order_detail['tax_value'], $order_detail['id'], true, $currency_rate, $to_currency);
                }
            }
        }
        $data['pur_order_row_template'] = $pur_order_row_template;

        
        $data['currencies'] = $this->currencies_model->get();

        $this->load->model('clients_model');
        $data['clients'] = $this->clients_model->get();

        $this->load->model('departments_model');
        $data['departments'] = $this->departments_model->get();

        $data['invoices'] = $this->purchase_model->get_invoice_for_pr();
        $data['pur_request'] = $this->purchase_model->get_pur_request_by_status(2);
        $data['projects'] = $this->projects_model->get();
        $data['ven'] = $this->input->get('vendor');
        $data['taxes'] = $this->purchase_model->get_taxes();
        $data['staff']             = $this->staff_model->get('', ['active' => 1]);
        $data['vendors'] = $this->purchase_model->get_vendor();
        $data['estimates'] = $this->purchase_model->get_estimates_by_status(2);
        $data['units'] = $this->purchase_model->get_units();

        $data['ajaxItems'] = false;
        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->purchase_model->pur_get_grouped('can_be_purchased');
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }

        $data['title'] = $title;

        $this->load->view('purchase_order/pur_order', $data);
    }

    /**
     * { delete pur order }
     *
     * @param      <type>  $id     The identifier
     * @return redirect
     */
    public function delete_pur_order($id){
        if (!has_permission('purchase_orders', '', 'delete')) {
            access_denied('purchase_order');
        }
        if (!$id) {
            redirect(admin_url('purchase/purchase_order'));
        }
        $success = $this->purchase_model->delete_pur_order($id);
        if (is_array($success)) {
            set_alert('warning', _l('purchase_order'));
        } elseif ($success == true) {
            set_alert('success', _l('deleted', _l('purchase_order')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('purchase_order')));
        }
        redirect(admin_url('purchase/purchase_order'));
    }

    /**
     * { estimate by vendor }
     *
     * @param      <type>  $vendor  The vendor
     * @return json
     */
    public function estimate_by_vendor($vendor){
        $estimate = $this->purchase_model->estimate_by_vendor($vendor);
        $ven = $this->purchase_model->get_vendor($vendor);

        $currency = get_base_currency_pur();
        $currency_id = $currency->id;
        if($ven->default_currency != 0){
            $currency_id = $ven->default_currency;
        }
        
        $vendor_data = '';
        $html = '<option value=""></option>';
        $company = '';
        foreach($estimate as $es){
            $html .= '<option value="'.$es['id'].'">'.format_pur_estimate_number($es['id']).'</option>';
        }

        $option_html = '';

        if(total_rows(db_prefix().'pur_vendor_items', ['vendor' => $vendor]) <= ajax_on_total_items()){
            $items = $this->purchase_model->get_items_by_vendor_variation($vendor);
            $option_html .= '<option value=""></option>';
            foreach($items as $item){
                $option_html .= '<option value="'.$item['id'].'" >'.$item['label'].'</option>';
            }
        }


        if($ven){
            $vendor_data .= '<div class="col-md-6">';
            $vendor_data .= '<p class="bold p_style">'._l('vendor_detail').'</p>
                            <hr class="hr_style"/>';
            $vendor_data .= '<table class="table table-striped table-bordered"><tbody>';
            $vendor_data .= '<tr><td>'._l('company').'</td><td>'.$ven->company.'</td></tr>';
            $vendor_data .= '<tr><td>'._l('client_vat_number').'</td><td>'.$ven->vat.'</td></tr>';
            $vendor_data .= '<tr><td>'._l('client_phonenumber').'</td><td>'.$ven->phonenumber.'</td></tr>';
            $vendor_data .= '<tr><td>'._l('website').'</td><td>'.$ven->website.'</td></tr>';
            $vendor_data .= '<tr><td>'._l('vendor_category').'</td><td>'.get_vendor_category_html($ven->category).'</td></tr>';
            $vendor_data .= '<tr><td>'._l('client_address').'</td><td>'.$ven->address.'</td></tr>';
            $vendor_data .= '<tr><td>'._l('client_city').'</td><td>'.$ven->city.'</td></tr>';
            $vendor_data .= '<tr><td>'._l('client_state').'</td><td>'.$ven->state.'</td></tr>';
            $vendor_data .= '<tr><td>'._l('client_postal_code').'</td><td>'.$ven->zip.'</td></tr>';
            $vendor_data .= '<tr><td>'._l('clients_country').'</td><td>'.get_country_short_name($ven->country).'</td></tr>';
            $vendor_data .= '<tr><td>'._l('bank_detail').'</td><td>'.$ven->bank_detail.'</td></tr>';
            $vendor_data .= '<tr><td>'._l('payment_terms').'</td><td>'.$ven->payment_terms.'</td></tr>';
            $vendor_data .= '</tbody></table>';                    
            $vendor_data .= '</div>';

            $vendor_data .= '<div class="col-md-6">';
            $vendor_data .= '<p class="bold p_style">'._l('billing_address').'</p>
                            <hr class="hr_style"/>';
            $vendor_data .= '<table class="table table-striped table-bordered"><tbody>';
            $vendor_data .= '<tr><td>'._l('billing_street').'</td><td>'.$ven->billing_street.'</td></tr>';
            $vendor_data .= '<tr><td>'._l('billing_city').'</td><td>'.$ven->billing_city.'</td></tr>';
            $vendor_data .= '<tr><td>'._l('billing_state').'</td><td>'.$ven->billing_state.'</td></tr>';
            $vendor_data .= '<tr><td>'._l('billing_zip').'</td><td>'.$ven->billing_zip.'</td></tr>';
            $vendor_data .= '<tr><td>'._l('billing_country').'</td><td>'.get_country_short_name($ven->billing_country).'</td></tr>';
            $vendor_data .= '</tbody></table>'; 
            $vendor_data .= '<p class="bold p_style">'._l('shipping_address').'</p>
                            <hr class="hr_style"/>';
            $vendor_data .= '<table class="table table-striped table-bordered"><tbody>';
            $vendor_data .= '<tr><td>'._l('shipping_street').'</td><td>'.$ven->shipping_street.'</td></tr>';
            $vendor_data .= '<tr><td>'._l('shipping_city').'</td><td>'.$ven->shipping_city.'</td></tr>';
            $vendor_data .= '<tr><td>'._l('shipping_state').'</td><td>'.$ven->shipping_state.'</td></tr>';
            $vendor_data .= '<tr><td>'._l('shipping_zip').'</td><td>'.$ven->shipping_zip.'</td></tr>';
            $vendor_data .= '<tr><td>'._l('shipping_country').'</td><td>'.get_country_short_name($ven->shipping_country).'</td></tr>';
            $vendor_data .= '</tbody></table>';                  
            $vendor_data .= '</div>';

            if($ven->vendor_code != ''){
               $company = $ven->vendor_code; 
            }
            
        }

        echo json_encode([
            'result' => $html,
            'ven_html' => $vendor_data,
            'company' => $company,
            'option_html' => $option_html,
            'currency_id' => $currency_id
        ]);
    }

    /**
     * { table pur order }
     */
    public function table_pur_order(){
        $this->app->get_table_data(module_views_path('purchase', 'purchase_order/table_pur_order'));
    }

    /**
     * { contracts }
     * @return  view
     */
    public function contracts(){
        $this->load->model('departments_model');
        $data['departments'] = $this->departments_model->get();
        $this->load->model('projects_model');
        $data['projects'] = $this->projects_model->get();
        $data['vendors'] = $this->purchase_model->get_vendor();
        $data['title'] = _l('contracts');
        $this->load->view('contracts/manage',$data);
    }

    /**
     * { contract }
     *
     * @param      string  $id     The identifier
     * @return redirect , view
     */
    public function contract($id = ''){
        if ($this->input->post()) {
            $contract_data = $this->input->post();
            $contract_data['currency'] = get_base_currency()->id;
            if ($id == '') {
                
                $id = $this->purchase_model->add_contract($contract_data);
                if ($id) {
                    handle_pur_contract_file($id);
                    set_alert('success', _l('added_successfully', _l('contract')));
                    
                    redirect(admin_url('purchase/contracts'));
                    
                }
            } else {
                handle_pur_contract_file($id);
                $success = $this->purchase_model->update_contract($contract_data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('pur_order')));
                }
                redirect(admin_url('purchase/contract/' . $id));                
            }
        }

        if ($id == '') {
            $title = _l('create_new_contract');
        } else {
            $data['contract'] = $this->purchase_model->get_contract($id);

            if(!has_permission('purchase_contracts', '', 'view') && !has_permission('purchase_contracts', '', 'view_own')){
                access_denied('purchase');
            }

            if(has_permission('purchase_contracts', '', 'view_own') && !is_admin()){
                if($data['contract']->add_from != get_staff_user_id() && !in_array($data['contract']->vendor, get_vendor_admin_list(get_staff_user_id()))){
                    access_denied('purchase');
                }
            }

            
            $data['vendor_contacts'] = $this->purchase_model->get_contacts($data['contract']->vendor);
            $data['attachments'] = $this->purchase_model->get_pur_contract_attachment($id);
            $data['payment'] = $this->purchase_model->get_payment_by_contract($id);
            $title = _l('contract_detail');
        }
        $this->load->model('departments_model');
        $data['departments'] = $this->departments_model->get();
        $this->load->model('projects_model');
        $data['projects'] = $this->projects_model->get();
        $data['ven'] = $this->input->get('vendor');
        $data['pur_orders'] = $this->purchase_model->get_pur_order_approved();
        $data['taxes'] = $this->purchase_model->get_taxes();
        $data['staff']             = $this->staff_model->get('', ['active' => 1]);
        $data['members']             = $this->staff_model->get('', ['active' => 1]);
        $data['vendors'] = $this->purchase_model->get_vendor();
        $data['units'] = $this->purchase_model->get_units();
        $data['items'] = $this->purchase_model->get_items();
        $data['title'] = $title;

        $this->load->view('contracts/contract', $data);
    }

    /**
     * { delete contract }
     *
     * @param      <type>  $id     The identifier
     * @return redirect
     */
    public function delete_contract($id){
        if (!has_permission('purchase_contracts', '', 'delete')) {
            access_denied('contracts');
        }
        if (!$id) {
            redirect(admin_url('purchase/contracts'));
        }
        $success = $this->purchase_model->delete_contract($id);
        if (is_array($success)) {
            set_alert('warning', _l('contracts'));
        } elseif ($success == true) {
            set_alert('success', _l('deleted', _l('contracts')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('contracts')));
        }
        redirect(admin_url('purchase/contracts'));
    }

    /**
     * Determines if contract number exists.
     */
    public function contract_number_exists()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                // First we need to check if the email is the same
                $contract = $this->input->post('contract');
                if ($contract != '') {
                    $this->db->where('id', $contract);
                    $cd = $this->db->get('tblpur_contracts')->row();
                    if ($cd->contract_number == $this->input->post('contract_number')) {
                        echo json_encode(true);
                        die();
                    }
                }
                $this->db->where('contract_number', $this->input->post('contract_number'));
                $total_rows = $this->db->count_all_results('tblpur_contracts');
                if ($total_rows > 0) {
                    echo json_encode(false);
                } else {
                    echo json_encode(true);
                }
                die();
            }
        }
    }

    /**
     * { table contracts }
     */
    public function table_contracts(){
         $this->app->get_table_data(module_views_path('purchase', 'contracts/table_contracts'));
    }

    /**
     * Saves a contract data.
     * @return  json
     */
    public function save_contract_data()
    {
        if (!has_permission('purchase_contracts', '', 'edit') && !has_permission('purchase_contracts', '', 'create')) {
            header('HTTP/1.0 400 Bad error');
            echo json_encode([
                'success' => false,
                'message' => _l('access_denied'),
            ]);
            die;
        }

        $success = false;
        $message = '';

        $this->db->where('id', $this->input->post('contract_id'));
        $this->db->update(db_prefix().'pur_contracts', [
                'content' => $this->input->post('content', false),
        ]);

        $success = $this->db->affected_rows() > 0;
        $message = _l('updated_successfully', _l('contract'));

        echo json_encode([
            'success' => $success,
            'message' => $message,
        ]);
    }

    /**
     * { pdf contract }
     *
     * @param      <type>  $id     The identifier
     * @return pdf output
     */
    public function pdf_contract($id)
    {
        if (!has_permission('purchase_contracts', '', 'view') && !has_permission('purchase_contracts', '', 'view_own')) {
            access_denied('contracts');
        }

        if (!$id) {
            redirect(admin_url('purchase/contracts'));
        }

        $contract = $this->purchase_model->get_contract($id);
        
        try {
            $pdf = pur_contract_pdf($contract);
        } catch (Exception $e) {
            echo pur_html_entity_decode($e->getMessage());
            die;
        }

        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf->Output(slug_it($contract->contract_number) . '.pdf', $type);
    }

    /**
     * { sign contract }
     *
     * @param      <type>  $contract  The contract
     * @return json
     */
    public function sign_contract($contract){
        if($this->input->post()){
            $data = $this->input->post();
            $success = $this->purchase_model->sign_contract($contract, $data['status']);
            $message = '';
            if($success == true){
                process_digital_signature_image($data['signature'], PURCHASE_MODULE_UPLOAD_FOLDER .'/contract_sign/'. $contract);
                $message = _l('sign_successfully');
            }

            echo json_encode([
                'success' => $success,
                'message' => $message,
            ]);
            
        }

    }

    /**
     * Sends a request approve.
     * @return  json
     */
    public function send_request_approve(){
        $data = $this->input->post();
        $message = 'Send request approval fail';
        $success = $this->purchase_model->send_request_approve($data);
        if ($success === true) {                
                $message = 'Send request approval success';
                $data_new = [];
                $data_new['send_mail_approve'] = $data;
                $this->session->set_userdata($data_new);
        }elseif($success === false){
            $message = _l('no_matching_process_found');
            $success = false;
            
        }else{
            $message = _l('could_not_find_approver_with', _l($success));
            $success = false;
        }
        echo json_encode([
            'success' => $success,
            'message' => $message,
        ]); 
        die;
    }

    /**
     * Sends a mail.
     * @return json
     */
    public function send_mail()
    {
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            if((isset($data)) && $data != ''){
                $this->purchase_model->send_mail($data);

                $success = 'success';
                echo json_encode([
                'success' => $success,                
            ]); 
            }
        }
    }

    /**
     * { approve request }
     * @return json
     */
    public function approve_request(){
        $data = $this->input->post();
        $data['staff_approve'] = get_staff_user_id();
        $success = false; 
        $code = '';
        $signature = '';

        if(isset($data['signature'])){
            $signature = $data['signature'];
            unset($data['signature']);
        }
        $status_string = 'status_'.$data['approve'];
        $check_approve_status = $this->purchase_model->check_approval_details($data['rel_id'],$data['rel_type']);
        
        if(isset($data['approve']) && in_array(get_staff_user_id(), $check_approve_status['staffid'])){

            $success = $this->purchase_model->update_approval_details($check_approve_status['id'], $data);

            $message = _l('approved_successfully');

            if ($success) {
                if($data['approve'] == 2){
                    $message = _l('approved_successfully');
                    $data_log = [];

                    if($signature != ''){
                        $data_log['note'] = "signed_request";
                    }else{
                        $data_log['note'] = "approve_request";
                    }
                    if($signature != ''){
                        switch ($data['rel_type']) {
                            case 'payment_request':
                                $path = PURCHASE_MODULE_UPLOAD_FOLDER .'/payment_invoice/signature/' .$data['rel_id'];
                                break;
                            case 'pur_order':
                                $path = PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_order/signature/' .$data['rel_id'];
                                break;
                            case 'pur_request':
                                $path = PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_request/signature/' .$data['rel_id'];
                                break;
                            case 'pur_quotation':
                                $path = PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_estimate/signature/' .$data['rel_id'];
                                break;
                            case 'order_return':
                                $path = PURCHASE_MODULE_UPLOAD_FOLDER .'/order_return/signature/' .$data['rel_id'];
                                break;
                            default:
                                $path = PURCHASE_MODULE_UPLOAD_FOLDER;
                                break;
                        }
                        purchase_process_digital_signature_image($signature, $path, 'signature_'.$check_approve_status['id']);
                        $message = _l('sign_successfully');
                    }
                   


                    $check_approve_status = $this->purchase_model->check_approval_details($data['rel_id'],$data['rel_type']);
                    if ($check_approve_status === true){
                        $this->purchase_model->update_approve_request($data['rel_id'],$data['rel_type'], 2);
                    }
                }else{
                    $message = _l('rejected_successfully');
                    
                    $this->purchase_model->update_approve_request($data['rel_id'],$data['rel_type'], '3');
                }
            }
        }

        $data_new = [];
        $data_new['send_mail_approve'] = $data;
        $this->session->set_userdata($data_new);
        echo json_encode([
            'success' => $success,
            'message' => $message,
        ]);
        die();      
    }

    /**
     * approve request
     * @param  integer $id
     * @return json
     */
    public function approve_request_for_order_return() {
        $data = $this->input->post();

        $data['staff_approve'] = get_staff_user_id();
        $success = false;
        $code = '';
        $signature = '';

        if (isset($data['signature'])) {
            $signature = $data['signature'];
            unset($data['signature']);
        }
        $status_string = 'status_' . $data['approve'];
        $check_approve_status = $this->purchase_model->check_approval_details($data['rel_id'], $data['rel_type']);


        if (isset($data['approve']) && in_array(get_staff_user_id(), $check_approve_status['staffid'])) {

            $success = $this->purchase_model->update_approval_details($check_approve_status['id'], $data);

            $message = _l('approved_successfully');

            if ($success) {
                if ($data['approve'] == 1) {
                    $message = _l('approved_successfully');
                    $data_log = [];

                    if ($signature != '') {
                        $data_log['note'] = "signed_request";
                    } else {
                        $data_log['note'] = "approve_request";
                    }
                    if ($signature != '') {
                        switch ($data['rel_type']) {
                        

                            case 'order_return':
                            $path = PURCHASE_MODULE_UPLOAD_FOLDER .'/order_return/signature/' .$data['rel_id'];
                            break;
                            


                            default:
                            $path = PURCHASE_MODULE_UPLOAD_FOLDER;
                            break;
                        }
                        purchase_process_digital_signature_image($signature, $path, 'signature_' . $check_approve_status['id']);
                        $message = _l('sign_successfully');
                    }
                    $data_log['rel_id'] = $data['rel_id'];
                    $data_log['rel_type'] = $data['rel_type'];
                    $data_log['staffid'] = get_staff_user_id();
                    $data_log['date'] = date('Y-m-d H:i:s');

                    $this->purchase_model->add_activity_log($data_log);

                    $check_approve_status = $this->purchase_model->check_approval_details($data['rel_id'], $data['rel_type']);

                    if ($check_approve_status === true) {
                        $this->purchase_model->update_approve_request($data['rel_id'], $data['rel_type'], 1);
                    }
                } else {
                    $message = _l('rejected_successfully');
                    $data_log = [];
                    $data_log['rel_id'] = $data['rel_id'];
                    $data_log['rel_type'] = $data['rel_type'];
                    $data_log['staffid'] = get_staff_user_id();
                    $data_log['date'] = date('Y-m-d H:i:s');
                    $data_log['note'] = "rejected_request";
                    $this->purchase_model->add_activity_log($data_log);
                    $this->purchase_model->update_approve_request($data['rel_id'], $data['rel_type'], '-1');
                }
            }
        }

        $data_new = [];
        $data_new['send_mail_approve'] = $data;
        $this->session->set_userdata($data_new);
        echo json_encode([
            'success' => $success,
            'message' => $message,
        ]);
        die();
    }

    /**
     * Sends a request quotation.
     * @return redirect
     */
    public function send_request_quotation(){
        if($this->input->post()){
            $data = $this->input->post();
            $data['content'] = $this->input->post('content', false);
            $send = $this->purchase_model->send_pr($data);
            if($send){
                set_alert('success',_l('send_pr_successfully'));
                
            }else{
                set_alert('warning',_l('send_pr_fail'));
            }
            redirect(admin_url('purchase/purchase_request'));
            
        }
    }

    /**
     * { purchase request pdf }
     *
     * @param      <type>  $id     The identifier
     * @return pdf output
     */
    public function pur_request_pdf($id)
    {
        if (!$id) {
            redirect(admin_url('purchase/purchase_request'));
        }

        $pur_request = $this->purchase_model->get_pur_request_pdf_html($id);

        try {
            $pdf = $this->purchase_model->pur_request_pdf($pur_request);
        } catch (Exception $e) {
            echo pur_html_entity_decode($e->getMessage());
            die;
        }
        
        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf->Output('purchase_request.pdf', $type);
    }

    /**
     * { request quotation pdf }
     *
     * @param      <type>  $id     The identifier
     * @return pdf output
     */
    public function request_quotation_pdf($id)
    {
        if (!$id) {
            redirect(admin_url('purchase/purchase_request'));
        }

        $pur_request = $this->purchase_model->get_request_quotation_pdf_html($id);

        try {
            $pdf = $this->purchase_model->request_quotation_pdf($pur_request);
        } catch (Exception $e) {
            echo pur_html_entity_decode($e->getMessage());
            die;
        }
        
        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf->Output('request_quotation.pdf', $type);
    }

    /**
     * { purchase order setting }
     * @return  json
     */
    public function purchase_order_setting(){
        $data = $this->input->post();
        if($data != 'null'){
            $value = $this->purchase_model->update_purchase_setting($data);
            if($value){
                $success = true;
                $message = _l('updated_successfully');
            }else{
                $success = false;
                $message = _l('updated_false');
            }
            echo json_encode([
                'message' => $message,
                'success' => $success,
            ]);
            die;
        }
    }

    /**
     * { purchase order setting }
     * @return  json
     */
    public function item_by_vendor(){
        $data = $this->input->post();
        if($data != 'null'){
            $value = $this->purchase_model->update_purchase_setting($data);
            if($value){
                $success = true;
                $message = _l('updated_successfully');
            }else{
                $success = false;
                $message = _l('updated_false');
            }
            echo json_encode([
                'message' => $message,
                'success' => $success,
            ]);
            die;
        }
    }

    /**
     * { purchase order setting }
     * @return  json
     */
    public function show_item_cf_on_pdf(){
        $data = $this->input->post();
        if($data != 'null'){
            $value = $this->purchase_model->update_pc_options_setting($data);
            if($value){
                $success = true;
                $message = _l('updated_successfully');
            }else{
                $success = false;
                $message = _l('updated_false');
            }
            echo json_encode([
                'message' => $message,
                'success' => $success,
            ]);
            die;
        }
    }

    /**
     * { purchase order setting }
     * @return  json
     */
    public function show_tax_column(){
        $data = $this->input->post();
        if($data != 'null'){
            $value = $this->purchase_model->update_purchase_setting($data);
            if($value){
                $success = true;
                $message = _l('updated_successfully');
            }else{
                $success = false;
                $message = _l('updated_false');
            }
            echo json_encode([
                'message' => $message,
                'success' => $success,
            ]);
            die;
        }
    }

    /**
     * { purchase order setting }
     * @return  json
     */
    public function send_email_welcome_for_new_contact(){
        $data = $this->input->post();
        if($data != 'null'){
            $value = $this->purchase_model->update_purchase_setting($data);
            if($value){
                $success = true;
                $message = _l('updated_successfully');
            }else{
                $success = false;
                $message = _l('updated_false');
            }
            echo json_encode([
                'message' => $message,
                'success' => $success,
            ]);
            die;
        }
    }

     /**
     * { purchase order setting }
     * @return  json
     */
    public function reset_purchase_order_number_every_month(){
        $data = $this->input->post();
        if($data != 'null'){
            $value = $this->purchase_model->update_purchase_setting($data);
            if($value){
                $success = true;
                $message = _l('updated_successfully');
            }else{
                $success = false;
                $message = _l('updated_false');
            }
            echo json_encode([
                'message' => $message,
                'success' => $success,
            ]);
            die;
        }
    }

    /**
     * { purchase order setting }
     * @return  json
     */
    public function po_only_prefix_and_number(){
        $data = $this->input->post();
        if($data != 'null'){
            $value = $this->purchase_model->update_purchase_setting($data);
            if($value){
                $success = true;
                $message = _l('updated_successfully');
            }else{
                $success = false;
                $message = _l('updated_false');
            }
            echo json_encode([
                'message' => $message,
                'success' => $success,
            ]);
            die;
        }
    }

    /**
     * Gets the notes.
     *
     * @param      <type>  $id     The id of purchase order
     */
    public function get_notes($id)
    {       
        $data['notes'] = $this->misc_model->get_notes($id, 'purchase_order');
        $this->load->view('admin/includes/sales_notes_template', $data);
    }

    /**
     * Gets the purchase contract notes.
     *
     * @param      <type>  $id     The id of purchase order
     */
    public function get_notes_pur_contract($id)
    {
        $data['notes'] = $this->misc_model->get_notes($id, 'pur_contract');
        $this->load->view('admin/includes/sales_notes_template', $data);
    }

     /**
     * Gets the purchase invoice notes.
     *
     * @param      <type>  $id     The id of purchase order
     */
    public function get_notes_pur_invoice($id)
    {
        $data['notes'] = $this->misc_model->get_notes($id, 'pur_invoice');
        $this->load->view('admin/includes/sales_notes_template', $data);
    }

    /**
     * Adds a note.
     *
     * @param        $rel_id  The purchase contract id
     */
    public function add_pur_contract_note($rel_id)
    {
        if ($this->input->post() ) {
            $this->misc_model->add_note($this->input->post(), 'pur_contract', $rel_id);
            echo pur_html_entity_decode($rel_id);
        }
    }

    /**
     * Adds a note.
     *
     * @param        $rel_id  The purchase contract id
     */
    public function add_pur_invoice_note($rel_id)
    {
        if ($this->input->post() ) {
            $this->misc_model->add_note($this->input->post(), 'pur_invoice', $rel_id);
            echo pur_html_entity_decode($rel_id);
        }
    }

    /**
     * Adds a note.
     *
     * @param      <type>  $rel_id  The purchase order id
     */
    public function add_note($rel_id)
    {
        if ($this->input->post() ) {
            $this->misc_model->add_note($this->input->post(), 'purchase_order', $rel_id);
            echo pur_html_entity_decode($rel_id);
        }
    }

    /**
     * Uploads a purchase order attachment.
     *
     * @param      string  $id  The purchase order
     * @return redirect
     */
    public function purchase_order_attachment($id){

        handle_purchase_order_file($id);

        redirect(admin_url('purchase/purchase_order/'.$id));
    }

    /**
     * Uploads a purchase order attachment.
     *
     * @param      string  $id  The purchase order
     * @return redirect
     */
    public function purchase_request_attachment($id){

        handle_purchase_request_file($id);

        redirect(admin_url('purchase/view_pur_request/'.$id.'?tab=attachment'));
    }


    /**
     * { preview purchase order file }
     *
     * @param      <type>  $id      The identifier
     * @param      <type>  $rel_id  The relative identifier
     * @return  view
     */
    public function file_purorder($id, $rel_id)
    {
        $data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
        $data['current_user_is_admin']             = is_admin();
        $data['file'] = $this->purchase_model->get_file($id, $rel_id);
        if (!$data['file']) {
            header('HTTP/1.0 404 Not Found');
            die;
        }
        $this->load->view('purchase_order/_file', $data);
    }

    /**
     * { delete purchase order attachment }
     *
     * @param      <type>  $id     The identifier
     */
    public function delete_purorder_attachment($id)
    {
        $this->load->model('misc_model');
        $file = $this->misc_model->get_file($id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo pur_html_entity_decode($this->purchase_model->delete_purorder_attachment($id));
        } else {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }
    }

    /**
     * Adds a payment.
     *
     * @param      <type>  $pur_order  The purchase order id
     * @return  redirect
     */
    public function add_payment($pur_order){
         if ($this->input->post()) {
            $data = $this->input->post();
            $message = '';
            $success = $this->purchase_model->add_payment($data, $pur_order);
            if ($success) {
                $message = _l('added_successfully', _l('payment'));
            }
            set_alert('success', $message);
            redirect(admin_url('purchase/purchase_order/'.$pur_order));
            
        }
    }

    /**
     * Adds a payment on PO.
     *
     * @param      <type>  $pur_order  The purchase order id
     * @return  redirect
     */
    public function add_payment_on_po($pur_order){
         if ($this->input->post()) {
            $data = $this->input->post();
            $message = '';
            $success = $this->purchase_model->add_payment_on_po($data, $pur_order);
            if ($success) {
                $message = _l('added_successfully', _l('payment'));
            }
            set_alert('success', $message);
            redirect(admin_url('purchase/purchase_order/'.$pur_order));
            
        }
    }

    /**
     * { delete payment }
     *
     * @param      <type>  $id         The identifier
     * @param      <type>  $pur_order  The pur order
     * @return  redirect
     */
    public function delete_payment($id,$pur_order)
    {
        if (!$id) {
            redirect(admin_url('purchase/purchase_order/'.$pur_order));
        }
        $response = $this->purchase_model->delete_payment($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('payment')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('payment')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('payment')));
        }
        redirect(admin_url('purchase/purchase_order/'.$pur_order));
    }

    /**
     * { purchase order pdf }
     *
     * @param      <type>  $id     The identifier
     * @return pdf output
     */
    public function purorder_pdf($id)
    {
        if (!$id) {
            redirect(admin_url('purchase/purchase_request'));
        }

        $pur_request = $this->purchase_model->get_purorder_pdf_html($id);

        try {
            $pdf = $this->purchase_model->purorder_pdf($pur_request);
        } catch (Exception $e) {
            echo pur_html_entity_decode($e->getMessage());
            die;
        }
        
        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf->Output('purchase_order.pdf', $type);
    }

    /**
     * { clear signature }
     *
     * @param      <type>  $id     The identifier
     */
    public function clear_signature($id)
    {
        if (has_permission('purchase_contracts', '', 'delete')) {
            $this->purchase_model->clear_signature($id);
        }

        redirect(admin_url('purchase/contract/' . $id));
    }

    /**
     * { Purchase reports }
     * 
     * @return view
     */
    public function reports(){
        if (!is_admin() && !has_permission('purchase_reports','','view')) {
            access_denied('purchase');
        }

        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();
        $data['title'] = _l('purchase_reports');
        $data['items'] = $this->purchase_model->get_items();
        $this->load->view('reports/manage_report',$data);
    }

    /**
     *  import goods report
     *  
     *  @return json
     */
    public function import_goods_report()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $select = [
                'tblitems.commodity_code as item_code', 
                'tblitems.description as item_name',
                '(select pur_order_name from ' . db_prefix() . 'pur_orders where ' . db_prefix() . 'pur_orders.id = pur_order) as po_name', 
                'total_money',
            ];
            $where =[];
            $custom_date_select = $this->get_where_report_period(db_prefix() . 'pur_orders.order_date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }
            $currency = $this->currencies_model->get_base_currency();

            if($this->input->post('report_currency')){
                $report_currency = $this->input->post('report_currency');
                $base_currency = get_base_currency_pur();

                if($report_currency == $base_currency->id){
                    array_push($where, 'AND '.db_prefix().'pur_orders.currency IN (0, '.$report_currency.')');
                }else{
                    array_push($where, 'AND '.db_prefix().'pur_orders.currency = '.$report_currency);
                }

                $currency = pur_get_currency_by_id($report_currency);
            }

            if ($this->input->post('products_services')) {
                $products_services  = $this->input->post('products_services');
                $_products_services = [];
                if (is_array($products_services)) {
                    foreach ($products_services as $product) {
                        if ($product != '') {
                            array_push($_products_services, $product);
                        }
                    }
                }
                if (count($_products_services) > 0) {
                    array_push($where, 'AND tblitems.id IN (' . implode(', ', $_products_services) . ')');
                }
            }
            
            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'pur_order_detail';
            $join         = [
                'LEFT JOIN ' . db_prefix() . 'items ON ' . db_prefix() . 'items.id = ' . db_prefix() . 'pur_order_detail.item_code',
                'LEFT JOIN ' . db_prefix() . 'pur_orders ON ' . db_prefix() . 'pur_orders.id = ' . db_prefix() . 'pur_order_detail.pur_order',
            ];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                db_prefix() . 'items.id as item_id',
                db_prefix() . 'pur_order_detail.pur_order as po_id'
            ]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = [
                'total'           => 0,
            ];

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = '<a href="' . admin_url('purchase/items/' . $aRow['item_id']) . '" target="_blank">' . $aRow['item_code'] . '</a>';

                $row[] = $aRow['item_name'];

                $row[] = '<a href="' . admin_url('purchase/purchase_order/' . $aRow['po_id']) . '" target="_blank">' . $aRow['po_name'] . '</a>';


                

                $row[] = app_format_money($aRow['total_money'], $currency->name);
                $footer_data['total'] += $aRow['total_money'];

                $output['aaData'][] = $row;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = app_format_money($total, $currency->name);
            }

            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    /**
     * Gets the where report period.
     *
     * @param      string  $field  The field
     *
     * @return     string  The where report period.
     */
    private function get_where_report_period($field = 'date')
    {
        $months_report      = $this->input->post('report_months');
        $custom_date_select = '';
        if ($months_report != '') {
            if (is_numeric($months_report)) {
                // Last month
                if ($months_report == '1') {
                    $beginMonth = date('Y-m-01', strtotime('first day of last month'));
                    $endMonth   = date('Y-m-t', strtotime('last day of last month'));
                } else {
                    $months_report = (int) $months_report;
                    $months_report--;
                    $beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
                    $endMonth   = date('Y-m-t');
                }

                $custom_date_select = 'AND (' . $field . ' BETWEEN "' . $beginMonth . '" AND "' . $endMonth . '")';
            } elseif ($months_report == 'this_month') {
                $custom_date_select = 'AND (' . $field . ' BETWEEN "' . date('Y-m-01') . '" AND "' . date('Y-m-t') . '")';
            } elseif ($months_report == 'this_year') {
                $custom_date_select = 'AND (' . $field . ' BETWEEN "' .
                date('Y-m-d', strtotime(date('Y-01-01'))) .
                '" AND "' .
                date('Y-m-d', strtotime(date('Y-12-31'))) . '")';
            } elseif ($months_report == 'last_year') {
                $custom_date_select = 'AND (' . $field . ' BETWEEN "' .
                date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01'))) .
                '" AND "' .
                date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31'))) . '")';
            } elseif ($months_report == 'custom') {
                $from_date = to_sql_date($this->input->post('report_from'));
                $to_date   = to_sql_date($this->input->post('report_to'));
                if ($from_date == $to_date) {
                    $custom_date_select = 'AND ' . $field . ' = "' . $from_date . '"';
                } else {
                    $custom_date_select = 'AND (' . $field . ' BETWEEN "' . $from_date . '" AND "' . $to_date . '")';
                }
            }
        }

        return $custom_date_select;
    }

    /**
     * get data Purchase statistics by number of purchase orders
     * 
     * @return     json
     */
    public function number_of_purchase_orders_analysis(){
        $year_report      = $this->input->post('year');
        echo json_encode($this->purchase_model->number_of_purchase_orders_analysis($year_report));
        die();
    }

    /**
     * get data Purchase statistics by cost
     * 
     * @return     json
     */
    public function cost_of_purchase_orders_analysis(){
        $this->load->model('currencies_model');
        $year_report      = $this->input->post('year');
        $report_currency = $this->input->post('report_currency');
        $currency = pur_get_currency_by_id($report_currency);
        
        $currency_name = '';
        $currency_unit = '';
        if($currency){
            $currency_name = $currency->name;
            $currency_unit = $currency->symbol;
        }
        echo json_encode([
            'data' => $this->purchase_model->cost_of_purchase_orders_analysis($year_report, $report_currency),
            'unit' => $currency_unit,
            'name' => $currency_name,
        ]);
        die();
    }

    /**
     * { table vendor contracts }
     *
     * @param      <type>  $vendor  The vendor
     */
    public function table_vendor_contracts($vendor){
        $this->app->get_table_data(module_views_path('purchase', 'contracts/table_contracts'),['vendor' => $vendor]);
    }

    /**
     * { table vendor pur order }
     *
     * @param      <type>  $vendor  The vendor
     */
    public function table_vendor_pur_order($vendor){
        $this->app->get_table_data(module_views_path('purchase', 'purchase_order/table_pur_order'),['vendor' => $vendor]);
    }

    /**
     * { delete vendor admin }
     *
     * @param      <type>  $customer_id  The customer identifier
     * @param      <type>  $staff_id     The staff identifier
     */
    public function delete_vendor_admin($customer_id, $staff_id)
    {
        if (!has_permission('purchase_vendors', '', 'create') && !has_permission('purchase_vendors', '', 'edit')) {
            access_denied('vendors');
        }

        $this->db->where('vendor_id', $customer_id);
        $this->db->where('staff_id', $staff_id);
        $this->db->delete(db_prefix().'pur_vendor_admin');
        redirect(admin_url('purchase/vendor/' . $customer_id) . '?tab=vendor_admins');
    }

/**
     * table commodity list
     * 
     * @return array
     */
    public function table_item_list()
    {
        $this->app->get_table_data(module_views_path('purchase', 'items/table_item_list'));
    }

    /**
     * item list
     * @param  integer $id 
     * @return load view
     */
    public function items($id = ''){
        $this->load->model('departments_model');
        $this->load->model('staff_model');

        
        $data['units'] = $this->purchase_model->get_unit_add_item();
        $data['taxes'] = $this->purchase_model->get_taxes();
        $data['commodity_groups'] = $this->purchase_model->get_commodity_group_add_commodity();
        $data['sub_groups'] = $this->purchase_model->get_sub_group();
        $data['title'] = _l('item_list');

        $data['item_id'] = $id;

        $this->load->view('items/item_list', $data);
    }

    /**
     * get item data ajax
     * @param  integer $id 
     * @return view
     */
    public function get_item_data_ajax($id){
        
        $data['id'] = $id;
        $data['item'] = $this->purchase_model->get_item($id);
        $data['item_file'] = $this->purchase_model->get_item_attachments($id);
        if(is_numeric($data['item']->from_vendor_item)){
            $data['vendor_image'] = $this->purchase_model->get_vendor_item_file($data['item']->from_vendor_item);
        }

        $this->load->view('items/item_detail', $data);
    }

    /**
     * add item list
     * @param  integer $id 
     * @return redirect
     */
    public function add_item_list($id = '')
    {
        if ($this->input->post()) {
            $message          = '';
            $data             = $this->input->post();
            
            if (!$this->input->post('id')) {
           
                $mess = $this->purchase_model->add_item($data);
                if ($mess) {
                    set_alert('success',_l('added_successfully'). _l('item_list'));

                }else{
                    set_alert('warning',_l('Add_item_list_false'));
                }
                redirect(admin_url('purchase/item_list'));
               
            } else {
                $id = $data['id'];
                unset($data['id']);
                $success = $this->purchase_model->add_purchase($data, $id);
                if ($success) {
                    set_alert('success',_l('updated_successfully'). _l('item_list'));
                }else{
                    set_alert('warning',_l('updated_item_list_false'));
                }
                
                redirect(admin_url('purchase/item_list'));
            }
        }
    }

    /**
     * delete item
     * @param  integer $id 
     * @return redirect
     */
    public function delete_item($id){
        if (!$id) {
            redirect(admin_url('purchase/item_list'));
        }
        $response = $this->purchase_model->delete_item($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('item_list')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('item_list')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('item_list')));
        }
        redirect(admin_url('purchase/item_list'));
    }

    /**
     * Gets the commodity barcode.
     */
    public function get_commodity_barcode()
    {
        $commodity_barcode = $this->purchase_model->generate_commodity_barcode();

        echo json_encode([
            $commodity_barcode
        ]);
        die();
    }

    /**
     * commodity list add edit
     * @param  integer $id
     * @return json
     */
    public function commodity_list_add_edit($id=''){
        $data = $this->input->post();
        if($data){
            if(!isset($data['id'])){
                $ids = $this->purchase_model->add_commodity_one_item($data);
                if ($ids) {

                    // handle commodity list add edit file
                    $success = true;
                    $message = _l('added_successfully');
                    set_alert('success', $message);
                    /*upload multifile*/
                    echo json_encode([
                        'url'       => admin_url('purchase/items/' . $ids),
                        'commodityid' => $ids,
                    ]);
                    die;

                }
                echo json_encode([
                    'url' => admin_url('purchase/items'),
                ]);
                die;
               
            }else{
                $id = $data['id'];
                unset($data['id']);
                $success = $this->purchase_model->update_commodity_one_item($data,$id);

                /*update file*/

                if($success == true){
                    
                    $message = _l('updated_successfully');
                    set_alert('success', $message);
                }

                echo json_encode([
                    'url'       => admin_url('purchase/items/' . $id),
                    'commodityid' => $id,
                ]);
                die;

                
            }
        }


    }

    /**
     * add commodity attachment
     * @param  integer $id
     * @return json
     */
    public function add_commodity_attachment($id)
    {

        handle_item_attachments($id);
        echo json_encode([
            'url' => admin_url('purchase/items'),
        ]);
    }

    /**
     * get commodity file url 
     * @param  integer $commodity_id
     * @return json
     */
    public function get_commodity_file_url($commodity_id){
        $arr_commodity_file = $this->purchase_model->get_item_attachments($commodity_id);
        /*get images old*/
        $images_old_value='';

        $custom_fields_html = render_custom_fields('items', $commodity_id, [], ['items_pr' => true]);

        $list = $this->purchase_model->get_item_longdescriptions($commodity_id);

        if(isset($list)){
            $long_descriptions = strip_tags($list->long_description);
            $description = $list->long_description;
        }else{
            $long_descriptions = '';
            $description = '';

        }


        if(count($arr_commodity_file) > 0){
            foreach ($arr_commodity_file as $key => $value) {
                $images_old_value .='<div class="dz-preview dz-image-preview image_old'.$value["id"].'">';

                    $images_old_value .='<div class="dz-image">';
                    if(file_exists(PURCHASE_MODULE_ITEM_UPLOAD_FOLDER .$value["rel_id"].'/'.$value["file_name"])){
                        $images_old_value .='<img class="image-w-h" data-dz-thumbnail alt="'.$value["file_name"].'" src="'.site_url('modules/purchase/uploads/item_img/'.$value["rel_id"].'/'.$value["file_name"]).'">';
                    }else{
                        $images_old_value .='<img class="image-w-h" data-dz-thumbnail alt="'.$value["file_name"].'" src="'.site_url('modules/warehouse/uploads/item_img/'.$value["rel_id"].'/'.$value["file_name"]).'">';
                    }
                    $images_old_value .='</div>';

                    $images_old_value .='<div class="dz-error-mark">';
                        $images_old_value .='<a class="dz-remove" data-dz-remove>Remove file';
                        $images_old_value .='</a>';
                    $images_old_value .='</div>';

                    $images_old_value .='<div class="remove_file">';
                        $images_old_value .= '<a href="#" class="text-danger" onclick="delete_contract_attachment(this,'.$value["id"].'); return false;"><i class="fa fa fa-times"></i></a>';
                    $images_old_value .='</div>';

                $images_old_value .='</div>';
            }
        }
        
        if(isset($list->from_vendor_item) && is_numeric($list->from_vendor_item)){
            $vendor_image = $this->purchase_model->get_vendor_item_file($list->from_vendor_item);
            if(count($vendor_image) > 0){ 
                foreach ($vendor_image as $key => $value) {
                    $images_old_value .='<div class="dz-preview dz-image-preview image_old'.$value["id"].'">';

                    $images_old_value .='<div class="dz-image">';
                    if(file_exists(PURCHASE_PATH.'vendor_items/' .$list->from_vendor_item .'/'.$value['file_name'])){
                        $images_old_value .='<img class="image-w-h" data-dz-thumbnail alt="'.$value["file_name"].'" src="'.site_url('modules/purchase/uploads/vendor_items/' . $value['rel_id'] .'/'.$value['file_name']).'">';
                    }
                    $images_old_value .='</div>';

                    $images_old_value .='<div class="dz-error-mark">';
                        $images_old_value .='<a class="dz-remove" data-dz-remove>Remove file';
                        $images_old_value .='</a>';
                    $images_old_value .='</div>';

                

                $images_old_value .='</div>';
                }
            }
        }
        
              
              
            echo json_encode([
                'arr_images' => $images_old_value,
                'custom_fields_html' => $custom_fields_html,
                'description' => $description,
                'long_descriptions' => $long_descriptions,
            ]);
            die();

    }

    /**
     * delete commodity file
     * @param  integer $attachment_id
     * @return json
     */
    public function delete_commodity_file($attachment_id)
    {
        if (!has_permission('purchase_items', '', 'delete') && !is_admin()) {
            access_denied('purchase');
        }

        $file = $this->misc_model->get_file($attachment_id);
            echo json_encode([
                'success' => $this->purchase_model->delete_commodity_file($attachment_id),
            ]);
    }

    /**
     * unit type 
     * @param  integer $id 
     * @return redirect    
     */
    public function unit_type($id = '')
        {
            if ($this->input->post()) {
                $message          = '';
                $data             = $this->input->post();
                
                if (!$this->input->post('id')) {
                    $mess = $this->purchase_model->add_unit_type($data);
                    if ($mess) {
                        set_alert('success',_l('added_successfully').' '. _l('unit_type'));

                    }else{
                        set_alert('warning',_l('Add_unit_type_false'));
                    }
                    redirect(admin_url('purchase/setting?group=units'));
                   
                } else {
                    $id = $data['id'];
                    unset($data['id']);
                    $success = $this->purchase_model->add_unit_type($data, $id);
                    if ($success) {
                        set_alert('success',_l('updated_successfully').' '. _l('unit_type'));
                    }else{
                        set_alert('warning',_l('updated_unit_type_false'));
                    }
                    
                    redirect(admin_url('purchase/setting?group=units'));
                }
            }
        }


        /**
         * delete unit type 
         * @param  integer $id
         * @return redirect
         */
        public function delete_unit_type($id){
            if (!$id) {
                redirect(admin_url('purchase/setting?group=units'));
            }
            $response = $this->purchase_model->delete_unit_type($id);
            if (is_array($response) && isset($response['referenced'])) {
                set_alert('warning', _l('is_referenced', _l('unit_type')));
            } elseif ($response == true) {
                set_alert('success', _l('deleted', _l('unit_type')));
            } else {
                set_alert('warning', _l('problem_deleting', _l('unit_type')));
            }
            redirect(admin_url('purchase/setting?group=units'));
        }

    /**
     * delete commodity
     * @param  integer $id 
     * @return redirect
     */
    public function delete_commodity($id){
        if (!$id) {
            redirect(admin_url('purchase/items'));
        }
        $response = $this->purchase_model->delete_commodity($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('commodity_list')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('commodity_list')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('commodity_list')));
        }
        redirect(admin_url('purchase/items'));
    }

    /**
     * Adds an expense.
     */
    public function add_expense()
    {
        if ($this->input->post()) {
            $this->load->model('expenses_model');
            $data = $this->input->post();

            if(isset($data['pur_order'])){
                $pur_order = $data['pur_order'];
                unset($data['pur_order']);
            }

            $id = $this->expenses_model->add($data);

            if ($id) {

                $this->purchase_model->mark_converted_pur_order($pur_order,$id);

                set_alert('success', _l('converted', _l('expense')));
                echo json_encode([
                    'url'       => admin_url('expenses/list_expenses/' .$id),
                    'expenseid' => $id,
                ]);
                die;

            }
        }
    }

    /**
     * Uploads an attachment.
     *
     * @param      <type>  $id     The identifier
     */
    public function upload_attachment($id)
    {
        handle_pur_vendor_attachments_upload($id);
    }

    /**
     * { function_description }
     *
     * @param      <type>  $id      The identifier
     * @param      <type>  $rel_id  The relative identifier
     */
    public function file_pur_vendor($id, $rel_id)
    {
        $data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
        $data['current_user_is_admin']             = is_admin();
        $data['file'] = $this->purchase_model->get_file($id, $rel_id);
        if (!$data['file']) {
            header('HTTP/1.0 404 Not Found');
            die;
        }
        $this->load->view('vendors/_file', $data);
    }

    /**
     * { delete ic attachment }
     *
     * @param      <type>  $id     The identifier
     */
    public function delete_ic_attachment($id)
    {
        $this->load->model('misc_model');
        $file = $this->misc_model->get_file($id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo pur_html_entity_decode($this->purchase_model->delete_ic_attachment($id));
        } else {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }
    }

    /* Change client status / active / inactive */
    public function change_contact_status($id, $status)
    {
        if (has_permission('purchase_vendors', '', 'edit') || is_vendor_admin(get_user_id_by_contact_id_pur($id)) || is_admin()) {
            if ($this->input->is_ajax_request()) {
                $this->purchase_model->change_contact_status($id, $status);
            }
        }
    }

    /**
     * { vendor items }
     */
    public function vendor_items(){
        if (!has_permission('purchase_vendor_items', '', 'view') && !is_admin() && !has_permission('purchase_vendor_items', '', 'view_own')) {
            access_denied('vendor_items');
        }
            
        $data['title'] = _l('vendor_items');
        $data['vendors'] = $this->purchase_model->get_vendor();

        $data['ajaxItems'] = false;
        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->purchase_model->pur_get_grouped('can_be_purchased');
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }
        
        //$data['items_groups'] = $this->invoice_items_model->get_groups();
        $data['commodity_groups'] = $this->purchase_model->get_commodity_group_add_commodity();
        $this->load->view('vendor_items/manage', $data);
    }

    /**
     *  vendor item table
     *  
     *  @return json
     */
    public function vendor_items_table()
    {
        if ($this->input->is_ajax_request()) {
            
            $select = [
                db_prefix() . 'pur_vendor_items.id as vendor_items_id',
                db_prefix() . 'pur_vendor_items.items as items',
                db_prefix() . 'pur_vendor.company as company', 
                db_prefix() . 'pur_vendor_items.add_from as pur_vendor_items_addedfrom', 
               
            ];
            $where = [];
            

            if ($this->input->post('vendor_filter')) {
                $vendor_filter  = $this->input->post('vendor_filter');
                array_push($where, 'AND vendor IN ('. implode(',', $vendor_filter).')');
            }

            if ($this->input->post('group_items_filter')) {
                $group_items_filter  = $this->input->post('group_items_filter');
                array_push($where, 'AND group_items IN ('. implode(',', $group_items_filter).')');
            }

            if ($this->input->post('items_filter')) {
                $items_filter  = $this->input->post('items_filter');
                array_push($where, 'AND items = '.$items_filter);
            }

            if(!has_permission('purchase_vendor_items', '', 'view')){
                array_push($where, 'AND ' . db_prefix() . 'pur_vendor_items.vendor IN (SELECT vendor_id FROM ' . db_prefix() . 'pur_vendor_admin WHERE staff_id=' . get_staff_user_id() . ')');
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'pur_vendor_items';
            $join         = ['LEFT JOIN ' . db_prefix() . 'pur_vendor ON ' . db_prefix() . 'pur_vendor.userid = ' . db_prefix() . 'pur_vendor_items.vendor',
                            'LEFT JOIN ' . db_prefix() . 'items ON ' . db_prefix() . 'items.id = ' . db_prefix() . 'pur_vendor_items.items'
                        ];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where,[db_prefix() . 'pur_vendor.userid as userid','datecreate','description','commodity_code']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = [
                'total' => 0,
            ];

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['vendor_items_id'] . '"><label></label></div>';

                $row[] = '<a href="'.admin_url('purchase/vendor/'.$aRow['userid']).'">'.$aRow['company'].'</a>';

                $row[] = '<a href="'.admin_url('purchase/items/'.$aRow['items']).'" >'.$aRow['commodity_code'].' - '.$aRow['description'].'</a>';

                $row[] = _d($aRow['datecreate']);

                $options = icon_btn('purchase/delete_vendor_items/' . $aRow['vendor_items_id'], 'remove', 'btn-danger', ['title' => _l('delete')]);

                $row[] =  $options;

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * new vendor items
     */
    public function new_vendor_items(){
        if (!has_permission('purchase_vendor_items', '', 'create') && !is_admin() ) {
            access_denied('vendor_items');
        }
        $this->load->model('staff_model');

        if ($this->input->post()) {
            $data                = $this->input->post();
            if (!has_permission('purchase_vendor_items', '', 'create')) {
                access_denied('vendor_items');
            }
            $success = $this->purchase_model->add_vendor_items($data);
            if ($success) {
                set_alert('success', _l('added_successfully', _l('vendor_items')));
            }
            redirect(admin_url('purchase/vendor_items'));
        }
        $data['title'] = _l('vendor_items');

        $data['vendors'] = $this->purchase_model->get_vendor();
        $data['ajaxItems'] = false;
        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->purchase_model->pur_get_grouped('can_be_purchased');
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }
        $data['commodity_groups'] = $this->purchase_model->get_commodity_group_add_commodity();
    
        $this->load->view('vendor_items/vendor_items', $data);
    }

    /**
     * { group item change }
     */
    public function group_it_change($group = ''){
        if($group != ''){
            $html = '';
            if (total_rows(db_prefix() . 'items', [ 'group_id' => $group ]) <= ajax_on_total_items()) {
                $list_items = $this->purchase_model->get_item_by_group($group);
                if(count($list_items) > 0){
                    foreach($list_items as $item){
                        $html .= '<option value="'.$item['id'].'" selected>'.$item['commodity_code'].' - '.$item['description'].'</option>';
                    }
                }
            }

            echo json_encode([
                'html' => $html,
            ]);
        }else{

            $html = '';
            if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
                $items = $this->purchase_model->get_item();
                if(count($items) > 0){
                    foreach($items as $it){
                        $html .= '<option value="'.$it['id'].'">'.$it['commodity_code'].' - '.$it['description'].'</option>';
                    }
                }
            }

            echo json_encode([
                'html' => $html,
            ]);
        }   

    }

    /**
     * { delete vendor item  }
     *
     * @param      <type>  $id     The identifier
     */
    public function delete_vendor_items($id){
        if (!has_permission('purchase_vendor_items', '', 'delete') && !is_admin()) {
            access_denied('vendor_items');
        }
        if (!$id) {
            redirect(admin_url('purchase/vendor_items'));
        }
        
        $success = $this->purchase_model->delete_vendor_items($id);
        if ($success == true) {
            set_alert('success', _l('deleted', _l('vendor_items')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('vendor_items')));
        }
        redirect(admin_url('purchase/vendor_items'));
    }

    /**
     * purchase delete bulk action
     * @return
     */
    public function purchase_delete_bulk_action()
    {
        if (!is_staff_member()) {
            ajax_access_denied();
        }
        $total_deleted = 0;
        $total_updated = 0;
        $total_cloned = 0;
        if ($this->input->post()) {

            $ids                   = $this->input->post('ids');
            $rel_type                   = $this->input->post('rel_type');
            /*check permission*/
            switch ($rel_type) {
                case 'commodity_list':
                    if (!has_permission('purchase_items', '', 'delete') && !is_admin()) {
                        access_denied('commodity_list');
                    }
                    break;

                case 'vendors':
                    if (!has_permission('purchase_vendors', '', 'delete') && !is_admin()) {
                        access_denied('vendors');
                    }
                    break;

                case 'vendor_items':
                    if (!has_permission('purchase_vendor_items', '', 'delete') && !is_admin()) {
                        access_denied('vendor_items');
                    }
                    break;

                case 'change_item_selling_price':
                if (!has_permission('purchase_items', '', 'edit') && !is_admin()) {
                    access_denied('commodity_list');
                }
                break;

                case 'change_item_purchase_price':
                if (!has_permission('purchase_items', '', 'edit') && !is_admin()) {
                    access_denied('commodity_list');
                }
                break;
                
                default:
                    break;
            }

            /*delete data*/
            if ($this->input->post('mass_delete') && $this->input->post('mass_delete') == 'true') {
                if (is_array($ids)) {
                    foreach ($ids as $id) {

                            switch ($rel_type) {
                                case 'commodity_list':
                                    if ($this->purchase_model->delete_commodity($id)) {
                                        $total_deleted++;
                                        break;
                                    }else{
                                        break;
                                    }

                                case 'vendors':
                                    if ($this->purchase_model->delete_vendor($id)) {
                                        $total_deleted++;
                                        break;
                                    }else{
                                        break;
                                    }

                                case 'vendor_items':
                                    if ($this->purchase_model->delete_vendor_items($id)) {
                                        $total_deleted++;
                                        break;
                                    }else{
                                        break;
                                    }
                                
                                default:
                                   
                                    break;
                            }
                        }
                    }
                /*return result*/
                switch ($rel_type) {
                    case 'commodity_list':
                        set_alert('success', _l('total_commodity_list'). ": " .$total_deleted);
                        break;

                    case 'vendors':
                        set_alert('success', _l('total_vendors_list'). ": " .$total_deleted);
                        break;

                    case 'vendor_items':
                        set_alert('success', _l('total_vendor_items_list'). ": " .$total_deleted);
                        break;    
                    
                    default:
                        break;

                }
            }

            // Clone items
            if ($this->input->post('clone_items') && $this->input->post('clone_items') == 'true') {
                if (is_array($ids)) {
                    foreach ($ids as $id) {

                            switch ($rel_type) {
                                case 'commodity_list':
                                    if ($this->purchase_model->clone_item($id)) {
                                        $total_cloned++;
                                        break;
                                    }else{
                                        break;
                                    }
                                
                                default:
                                   
                                    break;
                            }
                        }
                    }
                /*return result*/
                switch ($rel_type) {
                    case 'commodity_list':
                        set_alert('success', _l('total_commodity_list'). ": " .$total_cloned);
                        break;

                    default:
                        break;

                }
            }

            // update selling price, purchase price
            if ( ($this->input->post('change_item_selling_price') ) || ($this->input->post('change_item_purchase_price') )  )  {

                if (is_array($ids)) {
                    foreach ($ids as $id) {

                        switch ($rel_type) {
                            case 'change_item_selling_price':
                            if ($this->purchase_model->commodity_udpate_profit_rate($id, $this->input->post('selling_price'), 'selling_percent' )) {
                                $total_updated++;
                                break;
                            }else{
                                break;
                            }

                            case 'change_item_purchase_price':
                            if ($this->purchase_model->commodity_udpate_profit_rate($id, $this->input->post('purchase_price'), 'purchase_percent' )) {
                                $total_updated++;
                                break;
                            }else{
                                break;
                            }
                            

                            default:

                            break;
                        }


                    }
                }

                /*return result*/
                switch ($rel_type) {
                    case 'change_item_selling_price':
                    set_alert('success', _l('total_commodity_list'). ": " .$total_updated);
                    break;

                    case 'change_item_purchase_price':
                    set_alert('success', _l('total_commodity_list'). ": " .$total_updated);
                    break;
                    

                    default:
                    break;

                }

            }


        }
    }

    /**
     * { pur order setting }
     * @return redirect
     */
    public function pur_order_setting(){
        if( !is_admin() && !has_permission('purchase_settings', '', 'edit')){
            access_denied('purchase');
        }

        if($this->input->post()){
            $data = $this->input->post();

            $data['terms_and_conditions'] = $this->input->post('terms_and_conditions', false);
            $update = $this->purchase_model->update_po_number_setting($data);

            if($update == true){
                set_alert('success', _l('updated_successfully'));
            }else{
                set_alert('warning', _l('updated_fail'));
            }

            redirect(admin_url('purchase/setting'));
        }

    }

    /**
     * { pur order setting }
     * @return redirect
     */
    public function update_order_return_setting(){
        if( !is_admin() && !has_permission('purchase_settings', '', 'edit')){
            access_denied('purchase');
        }

        if($this->input->post()){
            $data = $this->input->post();
            $update = $this->purchase_model->update_order_return_setting($data);

            if($update == true){
                set_alert('success', _l('updated_successfully'));
            }else{
                set_alert('warning', _l('updated_fail'));
            }

            redirect(admin_url('purchase/setting?group=order_return'));
        }

    }


    public function get_html_approval_setting($id = '')
    {
        $html = '';
        $staffs = $this->staff_model->get();
        $approver = [
                0 => ['id' => 'direct_manager', 'name' => _l('direct_manager')],
                1 => ['id' => 'head_of_department', 'name' => _l('department_manager')],
                2 => ['id' => 'staff', 'name' => _l('staff')]];
        $action = [ 
                    1 => ['id' => 'approve', 'name' => _l('approve')],
                    0 => ['id' => 'sign', 'name' => _l('sign')],
                ];

        $hr_record_status = 0; 
        if(get_status_modules_pur('hr_profile') == true){
            $hr_record_status = 1;
        }
        if(is_numeric($id)){
            $approval_setting = $this->purchase_model->get_approval_setting($id);

            $setting = json_decode($approval_setting->setting);

            $approver_md = '1';
            $hide_class = 'hide';
            $staff_md = '8';
            $approver_default = 'staff';
            $staff_hide = '';
            if($hr_record_status == 1){
                $approver_md = '4';
                $staff_md = '4';
                $hide_class = '';
                $approver_default = '';
                $staff_hide = 'hide';
            }
            
            foreach ($setting as $key => $value) {

                if($value->approver == 'staff'){
                    $staff_hide = '';
                }else{
                    $staff_hide = 'hide';
                }
                if($key == 0){

                    $html .= '<div id="item_approve">
                                    <div class="col-md-11">
                                    <div class="col-md-'.$approver_md.' '.$hide_class.'"> '.
                                    render_select('approver['.$key.']',$approver,array('id','name'),'approver', $value->approver, array('data-id' => '0', 'required' => 'true'), [],'', 'approver_class').'
                                    </div>
                                    <div class="col-md-'.$staff_md.' '.$staff_hide.'" id="is_staff_0">
                                    '. render_select('staff['.$key.']',$staffs,array('staffid','full_name'),'staff', $value->staff).'
                                    </div>
                                    <div class="col-md-4">
                                        '. render_select('action['.$key.']',$action,array('id','name'),'action', $value->action).' 
                                    </div>
                                    </div>
                                    <div class="col-md-1 btn_apr">
                                    <span class="pull-bot">
                                        <button name="add" class="btn new_vendor_requests btn-success" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
                                        </span>
                                  </div>
                                </div>';
                }else{
                     $html .= '<div id="item_approve">
                                    <div class="col-md-11">
                                    <div class="col-md-'.$approver_md.' '.$hide_class.'"">
                                        '.
                                    render_select('approver['.$key.']',$approver,array('id','name'),'approver', $value->approver, array('data-id' => '0', 'required' => 'true'), [],'', 'approver_class').' 
                                    </div>
                                    <div class="col-md-'.$staff_md.' '.$staff_hide.'" id="is_staff_'.$key.'">
                                        '. render_select('staff['.$key.']',$staffs,array('staffid','full_name'),'staff', $value->staff).' 
                                    </div>
                                    <div class="col-md-4">
                                        '. render_select('action['.$key.']',$action,array('id','name'),'action', $value->action).' 
                                    </div>
                                    </div>
                                    <div class="col-md-1 btn_apr">
                                    <span class="pull-bot">
                                        <button name="add" class="btn remove_vendor_requests btn-danger" data-ticket="true" type="button"><i class="fa fa-minus"></i></button>
                                        </span>
                                  </div>
                                </div>';
                }
            }
        }else{

            $approver_md = '1';
            $hide_class = 'hide';
            $staff_md = '8';
            $approver_default = 'staff';
            $staff_hide = '';
            if($hr_record_status == 1){
                $approver_md = '4';
                $staff_md = '4';
                $hide_class = '';
                $approver_default = '';
                $staff_hide = 'hide';
            }
            $html .= '<div id="item_approve">
                        <div class="col-md-11">
                        <div class="col-md-'.$approver_md.' '.$hide_class.' "> '.
                        render_select('approver[0]',$approver,array('id','name'),'approver', $approver_default, array('data-id' => '0', 'required' => 'true'), [],'', 'approver_class').'
                        </div>
                        <div class="col-md-'.$staff_md.' '.$staff_hide.'" id="is_staff_0">
                        '. render_select('staff[0]',$staffs,array('staffid','full_name'),'staff').'
                        </div>
                        <div class="col-md-4">
                            '. render_select('action[0]',$action,array('id','name'),'action','approve').' 
                        </div>
                        </div>
                        <div class="col-md-1 btn_apr">
                        <span class="pull-bot">
                            <button name="add" class="btn new_vendor_requests btn-success" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
                            </span>
                      </div>
                    </div>';
        }

        echo json_encode([
                    $html
                ]);
    }

    /**
     * commodty group type
     * @param  integer $id
     * @return redirect
     */
    public function commodity_group_type($id = '') {
        if ($this->input->post()) {
            $message = '';
            $data = $this->input->post();

            if (!$this->input->post('id')) {

                $mess = $this->purchase_model->add_commodity_group_type($data);
                if ($mess) {
                    set_alert('success', _l('added_successfully') . _l('commodity_group_type'));

                } else {
                    set_alert('warning', _l('Add_commodity_group_type_false'));
                }
                redirect(admin_url('purchase/setting?group=commodity_group'));

            } else {
                $id = $data['id'];
                unset($data['id']);
                $success = $this->purchase_model->add_commodity_group_type($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully') . _l('commodity_group_type'));
                } else {
                    set_alert('warning', _l('updated_commodity_group_type_false'));
                }

                redirect(admin_url('purchase/setting?group=commodity_group'));
            }
        }
    }

    /**
     * delete commodity group type
     * @param  integer $id
     * @return redirect
     */
    public function delete_commodity_group_type($id) {
        if (!$id) {
            redirect(admin_url('purchase/setting?group=commodity_group'));
        }
        $response = $this->purchase_model->delete_commodity_group_type($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('commodity_group_type')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('commodity_group_type')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('commodity_group_type')));
        }
        redirect(admin_url('purchase/setting?group=commodity_group'));
    }

    /**
     * sub group
     * @param  integer $id
     * @return redirect
     */
    public function sub_group($id = '') {
        if ($this->input->post()) {
            $message = '';
            $data = $this->input->post();

            if (!$this->input->post('id')) {

                $mess = $this->purchase_model->add_sub_group($data);
                if ($mess) {
                    set_alert('success', _l('added_successfully') . ' ' . _l('sub_group'));

                } else {
                    set_alert('warning', _l('Add_sub_group_false'));
                }
                redirect(admin_url('purchase/setting?group=sub_group'));

            } else {
                $id = $data['id'];
                unset($data['id']);
                $success = $this->purchase_model->add_sub_group($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully') . ' ' . _l('sub_group'));
                } else {
                    set_alert('warning', _l('updated_sub_group_false'));
                }

                redirect(admin_url('purchase/setting?group=sub_group'));
            }
        }
    }

    /**
     * delete sub group
     * @param  integer $id
     * @return redirect
     */
    public function delete_sub_group($id) {
        if (!$id) {
            redirect(admin_url('purchase/setting?group=sub_group'));
        }
        $response = $this->purchase_model->delete_sub_group($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('sub_group')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('sub_group')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('sub_group')));
        }
        redirect(admin_url('purchase/setting?group=sub_group'));
    }

     /**
     * get subgroup fill data
     * @return html 
     */
    public function get_subgroup_fill_data()
    {
        $data = $this->input->post();

        $subgroup = $this->purchase_model->list_subgroup_by_group($data['group_id']);

        echo json_encode([
        'subgroup' => $subgroup
        ]);

    }

    /**
     * { copy public link }
     *
     * @param      string  $id     The identifier
     */
    public function copy_public_link($id){
        $pur_order = $this->purchase_model->get_pur_order($id);
        $copylink = '';
        if($pur_order){
            if($pur_order->hash != '' && $pur_order->hash != null){
                $copylink = site_url('purchase/vendors_portal/pur_order/'.$id.'/'.$pur_order->hash);
            }else{
                $hash = app_generate_hash();
                $copylink = site_url('purchase/vendors_portal/pur_order/'.$id.'/'.$hash);
                $this->db->where('id',$id);
                $this->db->update(db_prefix().'pur_orders',['hash' => $hash,]);
            }
        }

        echo json_encode([
            'copylink' => $copylink,
        ]);
    }

    /**
     * { copy public link pur request }
     *
     * @param      string  $id     The identifier
     */
    public function copy_public_link_pur_request($id){
        $pur_request = $this->purchase_model->get_purchase_request($id);
        $copylink = '';
        if($pur_request){
            if($pur_request->hash != '' && $pur_request->hash != null){
                $copylink = site_url('purchase/vendors_portal/pur_request/'.$id.'/'.$pur_request->hash);
            }else{
                $hash = app_generate_hash();
                $copylink = site_url('purchase/vendors_portal/pur_request/'.$id.'/'.$hash);
                $this->db->where('id',$id);
                $this->db->update(db_prefix().'pur_request',['hash' => $hash,]);
            }
        }

        echo json_encode([
            'copylink' => $copylink,
        ]);
    }

    /**
     * { file pur vendor }
     *
     * @param       $id      The identifier
     * @param       $rel_id  The relative identifier
     */
    public function file_pur_contract($id, $rel_id)
    {
        $data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
        $data['current_user_is_admin']             = is_admin();
        $data['file'] = $this->purchase_model->get_file($id, $rel_id);
        if (!$data['file']) {
            header('HTTP/1.0 404 Not Found');
            die;
        }
        $this->load->view('contracts/_file', $data);
    }

    /**
     * { delete purchase contract attachment }
     *
     * @param        $id     The identifier
     */
    public function delete_pur_contract_attachment($id)
    {
        $this->load->model('misc_model');
        $file = $this->misc_model->get_file($id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo pur_html_entity_decode($this->purchase_model->delete_pur_contract_attachment($id));
        } else {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }
    }

    /**
     * { vendor category form }
     * @return redirect
     */
    public function vendor_cate(){
        if ($this->input->post()) {
            $message = '';
            $data = $this->input->post();
            if (!$this->input->post('id')) {
                $id = $this->purchase_model->add_vendor_category($data);
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('vendor_category'));
                    set_alert('success', $message);
                }
                redirect(admin_url('purchase/setting?group=vendor_category'));
            } else {
                $id = $data['id'];
                unset($data['id']);
                $success = $this->purchase_model->update_vendor_category($data, $id);
                if ($success) {
                    $message = _l('updated_successfully', _l('vendor_category'));
                    set_alert('success', $message);
                }
                redirect(admin_url('purchase/setting?group=vendor_category'));
            }
            die;
        }
    }

    /**
     * delete job_position
     * @param  integer $id
     * @return redirect
     */
    public function delete_vendor_category($id) {
        if (!$id) {
            redirect(admin_url('purchase/setting?group=vendor_category'));
        }
        $response = $this->purchase_model->delete_vendor_category($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('vendor_category')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('vendor_category')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('vendor_category')));
        }
        redirect(admin_url('purchase/setting?group=vendor_category'));
    }

    /**
     * Uploads a purchase estimate attachment.
     *
     * @param      string  $id  The purchase order
     * @return redirect
     */
    public function purchase_estimate_attachment($id){

        handle_purchase_estimate_file($id);

        redirect(admin_url('purchase/quotations/'.$id));
    }

    /**
     * { preview purchase estimate file }
     *
     * @param        $id      The identifier
     * @param        $rel_id  The relative identifier
     * @return  view
     */
    public function file_pur_estimate($id, $rel_id)
    {
        $data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
        $data['current_user_is_admin']             = is_admin();
        $data['file'] = $this->purchase_model->get_file($id, $rel_id);
        if (!$data['file']) {
            header('HTTP/1.0 404 Not Found');
            die;
        }
        $this->load->view('quotations/_file', $data);
    }

    /**
     * { delete purchase order attachment }
     *
     * @param      <type>  $id     The identifier
     */
    public function delete_estimate_attachment($id)
    {
        $this->load->model('misc_model');
        $file = $this->misc_model->get_file($id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo pur_html_entity_decode($this->purchase_model->delete_estimate_attachment($id));
        } else {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }
    }

    /**
     * Determines if vendor code exists.
     */
    public function vendor_code_exists()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                // First we need to check if the email is the same
                $id = $this->input->post('userid');
                if ($id != '') {
                    $this->db->where('userid', $id);
                    $pur_vendor = $this->db->get(db_prefix().'pur_vendor')->row();
                    if ($pur_vendor->vendor_code == $this->input->post('vendor_code')) {
                        echo json_encode(true);
                        die();
                    }
                }
                $this->db->where('vendor_code', $this->input->post('vendor_code'));
                $total_rows = $this->db->count_all_results(db_prefix().'pur_vendor    ');
                if ($total_rows > 0) {
                    echo json_encode(false);
                } else {
                    echo json_encode(true);
                }
                die();
            }
        }
    }

    /**
     * { dpm name in pur request number }
     *
     * @param        $dpm    The dpm
     */
    public function dpm_name_in_pur_request_number($dpm){
        $this->load->model('departments_model');
        $department = $this->departments_model->get($dpm);
        $name_rs = '';
        if($department){
            $name_repl = str_replace(' ', '', $department->name);
            $name_rs = strtoupper($name_repl);
        }

        echo json_encode([
            'rs' => $name_rs,
        ]);
    }

    /**
     * { update customfield po }
     *
     * @param        $id     The identifier
     */
    public function update_customfield_po($id){
        if($this->input->post()){
            $data = $this->input->post();
            $success = $this->purchase_model->update_customfield_po($id,$data);
            if($success){
                $message = _l('updated_successfully', _l('vendor_category'));
                set_alert('success', $message);
            }
            redirect(admin_url('purchase/purchase_order/'.$id));
        }
    }

    /**
     * { po voucher }
     */
    public function po_voucher(){

        $po_voucher = $this->purchase_model->get_po_voucher_html();

        try {
            $pdf = $this->purchase_model->povoucher_pdf($po_voucher);
        } catch (Exception $e) {
            echo pur_html_entity_decode($e->getMessage());
            die;
        }
        
        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf->Output('PO_voucher.pdf', $type);
    }


    /**
     *  po voucher report
     *  
     *  @return json
     */
    public function po_voucher_report()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $select = [
                'pur_order_number',
                'order_date',
                'type',
                'project',
                'department',
                'vendor',
                'approve_status',
                'delivery_status',
            ];
            $where =[];
            $custom_date_select = $this->get_where_report_period(db_prefix() . 'pur_orders.order_date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            

            $currency = $this->currencies_model->get_base_currency();
            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'pur_orders';
            $join         = [
                'LEFT JOIN ' . db_prefix() . 'departments ON ' . db_prefix() . 'departments.departmentid = ' . db_prefix() . 'pur_orders.department',
                'LEFT JOIN ' . db_prefix() . 'projects ON ' . db_prefix() . 'projects.id = ' . db_prefix() . 'pur_orders.project',
                'LEFT JOIN ' . db_prefix() . 'pur_vendor ON ' . db_prefix() . 'pur_vendor.userid = ' . db_prefix() . 'pur_orders.vendor',
            ];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                db_prefix().'pur_orders.id as id',
                db_prefix().'departments.name as department_name',
                db_prefix().'projects.name as project_name',
                db_prefix().'pur_vendor.company as vendor_name',
                'total',
            ]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = '<a href="' . admin_url('purchase/purchase_order/' . $aRow['id']) . '" target="_blank">' . $aRow['pur_order_number'] . '</a>';

                $row[] = _d($aRow['order_date']);

                $row[] = _l($aRow['type']);

                $row[] = '<a href="'. admin_url('projects/view/'.$aRow['project']) .'" target="_blank">'.$aRow['project_name'].'</a>';

                $row[] = $aRow['department_name'];

                $row[] = '<a href="'. admin_url('purchase/vendor/'.$aRow['vendor']) .'" target="_blank">'.$aRow['vendor_name'].'</a>';

                $row[] = get_status_approve($aRow['approve_status']);

                $delivery_status = '';
                if($aRow['delivery_status'] == 0){
                    $delivery_status = '<span class="label label-danger">'._l('undelivered').'</span>';
                }elseif($aRow['delivery_status'] == 1){
                    $delivery_status = '<span class="label label-success">'._l('delivered').'</span>';
                }
                $row[] = $delivery_status;

                 $paid = $aRow['total'] - purorder_inv_left_to_pay($aRow['id']);

                $percent = 0;

                if($aRow['total'] > 0){

                    $percent = ($paid / $aRow['total'] ) * 100;

                }

                

                $row[] = '<div class="progress">

                              <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40"

                              aria-valuemin="0" aria-valuemax="100" style="width:'.round($percent).'%">

                               ' .round($percent).' % 

                              </div>

                            </div>';
              
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     *  po voucher report
     *  
     *  @return json
     */
    public function po_report()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $select = [
                'pur_order_number',
                'order_date',
                'department',
                'vendor',
                'approve_status',
                'subtotal',
                'total_tax',
                'total',
            ];
            $where =[];
            $custom_date_select = $this->get_where_report_period(db_prefix() . 'pur_orders.order_date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            

            $currency = $this->currencies_model->get_base_currency();

            if($this->input->post('report_currency')){
                $report_currency = $this->input->post('report_currency');
                $base_currency = get_base_currency_pur();

                if($report_currency == $base_currency->id){
                    array_push($where, 'AND '.db_prefix().'pur_orders.currency IN (0, '.$report_currency.')');
                }else{
                    array_push($where, 'AND '.db_prefix().'pur_orders.currency = '.$report_currency);
                }

                $currency = pur_get_currency_by_id($report_currency);
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'pur_orders';
            $join         = [
                'LEFT JOIN ' . db_prefix() . 'departments ON ' . db_prefix() . 'departments.departmentid = ' . db_prefix() . 'pur_orders.department',
                'LEFT JOIN ' . db_prefix() . 'projects ON ' . db_prefix() . 'projects.id = ' . db_prefix() . 'pur_orders.project',
                'LEFT JOIN ' . db_prefix() . 'pur_vendor ON ' . db_prefix() . 'pur_vendor.userid = ' . db_prefix() . 'pur_orders.vendor',
            ];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                db_prefix().'pur_orders.id as id',
                db_prefix().'departments.name as department_name',
                db_prefix().'projects.name as project_name',
                db_prefix().'pur_vendor.company as vendor_name',
                'total',
            ]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = [
                'total'           => 0,
                'total_tax'       => 0,
                'total_value'     => 0,
            ];

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = '<a href="' . admin_url('purchase/purchase_order/' . $aRow['id']) . '" target="_blank">' . $aRow['pur_order_number'] . '</a>';

                $row[] = _d($aRow['order_date']);

                $row[] = $aRow['department_name'];

                $row[] = '<a href="'. admin_url('purchase/vendor/'.$aRow['vendor']) .'" target="_blank">'.$aRow['vendor_name'].'</a>';

                $row[] = get_status_approve($aRow['approve_status']);

                $row[] = app_format_money($aRow['subtotal'], $currency->name);

                $row[] = app_format_money($aRow['total_tax'], $currency->name);

                $row[] = app_format_money($aRow['total'], $currency->name);

                $footer_data['total'] += $aRow['total'];
                $footer_data['total_tax'] += $aRow['total_tax'];
                $footer_data['total_value'] += $aRow['subtotal'];
              
                $output['aaData'][] = $row;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = app_format_money($total, $currency->name);
            }

            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    /**
     *  purchase inv report
     *  
     *  @return json
     */
    public function purchase_inv_report()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $select = [
                'invoice_number',
                'contract',
                 db_prefix().'pur_invoices.pur_order',
                'invoice_date',
                'payment_status',
                'subtotal',
                'tax',
                'total',
            ];
            $where =[];
            $custom_date_select = $this->get_where_report_period(db_prefix() . 'pur_invoices.invoice_date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            

            $currency = $this->currencies_model->get_base_currency();

            if($this->input->post('report_currency')){
                $report_currency = $this->input->post('report_currency');
                $base_currency = get_base_currency_pur();

                if($report_currency == $base_currency->id){
                    array_push($where, 'AND '.db_prefix().'pur_invoices.currency IN (0, '.$report_currency.')');
                }else{
                    array_push($where, 'AND '.db_prefix().'pur_invoices.currency = '.$report_currency);
                }

                $currency = pur_get_currency_by_id($report_currency);
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'pur_invoices';
            $join         = [
                'LEFT JOIN '.db_prefix().'pur_contracts ON '.db_prefix().'pur_contracts.id = '.db_prefix().'pur_invoices.contract'
            ];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                db_prefix().'pur_invoices.id as id',
                db_prefix().'pur_contracts.contract_number as contract_number',

            ]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = [
                'total'           => 0,
                'total_tax'       => 0,
                'total_value'     => 0,
            ];

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = '<a href="' . admin_url('purchase/purchase_invoice/' . $aRow['id']) . '" target="_blank">' . $aRow['invoice_number'] . '</a>';

                $row[] = '<a href="'.admin_url('purchase/contract/'.$aRow['contract']).'" target="_blank">'.$aRow['contract_number'].'</a>';

                $row[] = '<a href="'.admin_url('purchase/purchase_order/'.$aRow[db_prefix().'pur_invoices.pur_order']).'" target="_blank">'.get_pur_order_subject($aRow[ db_prefix().'pur_invoices.pur_order']).'</a>';

                $row[] = _d($aRow['invoice_date']);

                $class = '';
                if($aRow['payment_status'] == 'unpaid'){
                    $class = 'danger';
                }elseif($aRow['payment_status'] == 'paid'){
                    $class = 'success';
                }elseif ($aRow['payment_status'] == 'partially_paid') {
                    $class = 'warning';
                }

                $row[] = '<span class="label label-'.$class.' s-status invoice-status-3">'._l($aRow['payment_status']).'</span>';

                $row[] = app_format_money($aRow['subtotal'], $currency->name);

                $row[] = app_format_money($aRow['tax'], $currency->name);

                $row[] = app_format_money($aRow['total'], $currency->name);

                $footer_data['total'] += $aRow['total'];
                $footer_data['total_tax'] += $aRow['tax'];
                $footer_data['total_value'] += $aRow['subtotal'];
              
                $output['aaData'][] = $row;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = app_format_money($total, $currency->name);
            }

            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    /**
     * { invoices }
     * @return view
     */
    public function invoices(){
        $data['title'] = _l('invoices');
        $data['contracts'] = $this->purchase_model->get_contract();
        $data['pur_orders'] = $this->purchase_model->get_list_pur_orders();
        $data['vendors'] = $this->purchase_model->get_vendor();
        $this->load->view('invoices/manage',$data);
    }

    /**
     * { table purchase invoices }
     */
    public function table_pur_invoices(){
        $this->app->get_table_data(module_views_path('purchase', 'invoices/table_pur_invoices'));
    }

    /**
     * { purchase invoice }
     *
     * @param      string  $id     The identifier
     */
    public function pur_invoice($id = ''){
        if($id == ''){
            $data['title'] = _l('add_invoice');

        }else{
            $data['title'] = _l('edit_invoice');
            
        }
        $data['contracts'] = $this->purchase_model->get_contract();
        $data['taxes'] = $this->purchase_model->get_taxes();
        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();

        $data['vendors'] = $this->purchase_model->get_vendor();
        $pur_invoice_row_template = $this->purchase_model->create_purchase_invoice_row_template();

        $data['base_currency'] = $this->currencies_model->get_base_currency();

        if($id != ''){
            $data['pur_orders'] = $this->purchase_model->get_pur_order_approved();
            $data['pur_invoice'] = $this->purchase_model->get_pur_invoice($id);
            $data['pur_invoice_detail'] = $this->purchase_model->get_pur_invoice_detail($id);

            $currency_rate = 1;
            if($data['pur_invoice']->currency != 0 && $data['pur_invoice']->currency_rate != null){
                $currency_rate = $data['pur_invoice']->currency_rate;
            }

            $to_currency = $data['base_currency']->name;
            if($data['pur_invoice']->currency != 0 && $data['pur_invoice']->to_currency != null) {
                $to_currency = $data['pur_invoice']->to_currency;
            }

            if (count($data['pur_invoice_detail']) > 0) { 
                $index_order = 0;
                foreach ($data['pur_invoice_detail'] as $inv_detail) { 
                    $index_order++;
                    $unit_name = pur_get_unit_name($inv_detail['unit_id']);
                    $taxname = $inv_detail['tax_name'];
                    $item_name = $inv_detail['item_name'];

                    if(strlen($item_name) == 0){
                        $item_name = pur_get_item_variatiom($inv_detail['item_code']);
                    }

                    $pur_invoice_row_template .= $this->purchase_model->create_purchase_invoice_row_template('items[' . $index_order . ']',  $item_name, $inv_detail['description'], $inv_detail['quantity'], $unit_name, $inv_detail['unit_price'], $taxname, $inv_detail['item_code'], $inv_detail['unit_id'], $inv_detail['tax_rate'],  $inv_detail['total_money'], $inv_detail['discount_percent'], $inv_detail['discount_money'], $inv_detail['total'], $inv_detail['into_money'], $inv_detail['tax'], $inv_detail['tax_value'], $inv_detail['id'], true, $currency_rate, $to_currency);
                }
            }else{
                $item_name = $data['pur_invoice']->invoice_number;
                $description = $data['pur_invoice']->adminnote;
                $quantity = 1;
                $taxname = '';
                $tax_rate = 0;
                $tax = get_tax_rate_item($id);
                if($tax && !is_array($tax)){
                    $taxname = $tax->name;
                    $tax_rate = $tax->taxrate;
                }

                $total = $data['pur_invoice']->subtotal + $data['pur_invoice']->tax;
                $index = 0;

                $pur_invoice_row_template .= $this->purchase_model->create_purchase_invoice_row_template('newitems[' . $index . ']',  $item_name, $description, $quantity, '', $data['pur_invoice']->subtotal, $taxname, null, null, $tax_rate,  $data['pur_invoice']->total, 0, 0, $total, $data['pur_invoice']->subtotal , $data['pur_invoice']->tax_rate, $data['pur_invoice']->tax, '', true);
            }

        }else{
            $data['pur_orders'] = $this->purchase_model->get_pur_order_approved_for_inv();
        }

        $data['pur_invoice_row_template'] = $pur_invoice_row_template;

        $data['ajaxItems'] = false;
        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->purchase_model->pur_get_grouped('can_be_purchased');
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }

        $this->load->view('invoices/pur_invoice',$data);
    }

    /**
     * { vendors change }
     */
    public function pur_vendors_change($vendor){
        $currency_id = get_vendor_currency($vendor);
        if($currency_id == 0 || $currency_id == ''){
            $currency_id = get_base_currency()->id;
        }

        $option_po = '<option value=""></option>';
        $option_ct = '<option value=""></option>';
       
        $pur_orders = $this->purchase_model->get_pur_order_approved_for_inv_by_vendor($vendor);
        foreach($pur_orders as $po){
            $option_po .= '<option value="'.$po['id'].'">'.$po['pur_order_number'].'</option>';
        }
    
        $contracts = $this->purchase_model->get_contracts_by_vendor($vendor);
        foreach($contracts as $ct){
            $option_ct .= '<option value="'.$ct['id'].'">'.$ct['contract_number'].'</option>';
        }
        

        $option_html = '';

        if(total_rows(db_prefix().'pur_vendor_items', ['vendor' => $vendor]) <= ajax_on_total_items()){
            $items = $this->purchase_model->get_items_by_vendor_variation($vendor);
            $option_html .= '<option value=""></option>';
            foreach($items as $item){
                $option_html .= '<option value="'.$item['id'].'" >'.$item['label'].'</option>';
            }
        }

        echo json_encode([
            'type' => get_purchase_option('create_invoice_by'),
            'html' => $option_ct,
            'po_html' => $option_po,
            'option_html' => $option_html,
            'currency_id' => $currency_id,
        ]);
    }

    /**
     * { pur invoice form }
     * @return redirect
     */
    public function pur_invoice_form(){
        if($this->input->post()){
            $data = $this->input->post();
            if($data['id'] == ''){
                unset($data['id']);
                $mess = $this->purchase_model->add_pur_invoice($data);
                if ($mess) {
                    handle_pur_invoice_file($mess);
                    set_alert('success', _l('added_successfully') . ' ' . _l('purchase_invoice'));

                } else {
                    set_alert('warning', _l('add_purchase_invoice_fail'));
                }
                redirect(admin_url('purchase/invoices'));
            }else{
                $id = $data['id'];
                unset($data['id']);
                handle_pur_invoice_file($id);
                $success = $this->purchase_model->update_pur_invoice($id, $data);
                if($success){
                    set_alert('success', _l('updated_successfully') . ' ' . _l('purchase_invoice'));
                }else{
                    set_alert('warning', _l('update_purchase_invoice_fail'));
                }
                redirect(admin_url('purchase/invoices'));
            }
        }
    }

    public function delete_pur_invoice($id){
        if (!$id) {
            redirect(admin_url('purchase/invoices'));
        }
        $response = $this->purchase_model->delete_pur_invoice($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('purchase_invoice')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('purchase_invoice')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('purchase_invoice')));
        }
        redirect(admin_url('purchase/invoices'));
    }

    /**
     * { contract change }
     *
     * @param      <type>  $ct    
     */
    public function contract_change($ct){
        $contract = $this->purchase_model->get_contract($ct);
        $value = 0;
        if($contract){
            $value = $contract->contract_value;
        }

        echo json_encode([
            'value' => $value,
            'purchase_order' => $contract->pur_order,
        ]);
    }

    /**
     * { purchase order change }
     *
     * @param      <type>  $ct    
     */
    public function pur_order_change($ct){
        $pur_order = $this->purchase_model->get_pur_order($ct);
        $pur_order_detail = $this->purchase_model->get_pur_order_detail($ct);
        
        $list_item = $this->purchase_model->create_purchase_order_row_template();
        $discount_percent = 0;

        $base_currency = get_base_currency();

        $currency_rate = 1;
        $to_currency = $base_currency->id;
        if($pur_order->currency != 0 && $pur_order->currency_rate != null){
            $currency_rate = $pur_order->currency_rate;
            $to_currency = $pur_order->currency;
        }

        if(count($pur_order_detail) > 0){
            $index = 0;
            foreach($pur_order_detail as $key => $item){
                $index++;
                $unit_name = pur_get_unit_name($item['unit_id']);
                $taxname = $item['tax_name'];
                $item_name = $item['item_name'];
                if(strlen($item_name) == 0){
                    $item_name = pur_get_item_variatiom($item['item_code']);
                }

                $list_item .= $this->purchase_model->create_purchase_invoice_row_template('newitems[' . $index . ']',  $item_name, '', $item['quantity'], $unit_name, $item['unit_price'], $taxname, $item['item_code'], $item['unit_id'], $item['tax_rate'],  $item['total_money'], $item['discount_%'], $item['discount_money'], $item['total'], $item['into_money'], $item['tax'], $item['tax_value'], $index, true, $currency_rate, $to_currency);
            }
        }

        $discount_type = 'after_tax';
        if($pur_order){
            $discount_percent = $pur_order->discount_percent;
            $discount_type = $pur_order->discount_type;
        }

        echo json_encode([
            'discount_type' => $discount_type,
            'list_item' => $list_item,
            'discount_percent' => $discount_percent,
            'currency' => $to_currency,
            'currency_rate' => $currency_rate,
            'shipping_fee' => $pur_order->shipping_fee,
            'order_discount' => $pur_order->discount_total,
        ]);
    }

    /**
     * { tax rate change }
     *
     * @param        $tax    The tax
     */
    public function tax_rate_change($tax){
        $this->load->model('taxes_model');
        $tax = $this->taxes_model->get($tax);
        $rate = 0;
        if($tax){
            $rate = $tax->taxrate;
        }

        echo  json_encode([
            'rate' => $rate,
        ]);
    }

    /**
     * { purchase invoice }
     *
     * @param       $id     The identifier
     */
    public function purchase_invoice($id){
        if (!$id) {
            redirect(admin_url('purchase/invoices'));
        }
        $data['pur_invoice'] = $this->purchase_model->get_pur_invoice($id);

        if(!has_permission('purchase_invoices', '', 'view') && !has_permission('purchase_invoices', '', 'view_own')){
            access_denied('purchase');
        }

        if(has_permission('purchase_invoices', '', 'view_own') && !is_admin()){
            if($data['pur_invoice']->add_from != get_staff_user_id() && !in_array($data['pur_invoice']->vendor, get_vendor_admin_list(get_staff_user_id()))){
                access_denied('purchase');
            }
        }


        $this->load->model('staff_model');
        $this->load->model('currencies_model');

        $this->load->model('payment_modes_model');
        $data['payment_modes'] = $this->payment_modes_model->get('', [
            'expenses_only !=' => 1,
        ]);

        $data['applied_debits'] = $this->purchase_model->get_applied_invoice_debits($id);
        $data['debits_available'] = $this->purchase_model->total_remaining_debits_by_vendor($data['pur_invoice']->vendor, $data['pur_invoice']->currency);

        if ($data['debits_available'] > 0) {
            $data['open_debits'] = $this->purchase_model->get_open_debits($data['pur_invoice']->vendor);
        }
        $vendor_currency_id = get_vendor_currency($data['pur_invoice']->vendor);
        $data['vendor_currency'] = $this->currencies_model->get_base_currency();
        if($vendor_currency_id != 0){
            $data['vendor_currency'] = pur_get_currency_by_id($vendor_currency_id);
        }

        $data['invoice_detail'] = $this->purchase_model->get_pur_invoice_detail($id);

        $data['tax_data'] = $this->purchase_model->get_html_tax_pur_invoice($id);
        
        $data['title'] = $data['pur_invoice']->invoice_number;
        $data['members']           = $this->staff_model->get('', ['active' => 1]);
        $data['payment'] = $this->purchase_model->get_payment_invoice($id);
        $data['pur_invoice_attachments'] = $this->purchase_model->get_purchase_invoice_attachments($id);
        $this->load->view('invoices/pur_invoice_preview',$data);
    }

    /**
     * Adds a payment for invoice.
     *
     * @param      <type>  $pur_order  The purchase order id
     * @return  redirect
     */
    public function add_invoice_payment($invoice){
         if ($this->input->post()) {
            $data = $this->input->post();
            $message = '';
            $success = $this->purchase_model->add_invoice_payment($data, $invoice);
            if ($success) {
                $message = _l('added_successfully', _l('payment'));
            }
            set_alert('success', $message);
            redirect(admin_url('purchase/purchase_invoice/'.$invoice));
            
        }
    }

     /**
     * { delete payment }
     *
     * @param      <type>  $id         The identifier
     * @param      <type>  $pur_order  The pur order
     * @return  redirect
     */
    public function delete_payment_pur_invoice($id,$inv)
    {
        if (!$id) {
            redirect(admin_url('purchase/purchase_invoice/'.$inv));
        }
        $response = $this->purchase_model->delete_payment_pur_invoice($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('payment')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('payment')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('payment')));
        }
        redirect(admin_url('purchase/purchase_invoice/'.$inv));
    }

    /**
     * { payment invoice }
     *
     * @param       $id     The identifier
     * @return view
     */
    public function payment_invoice($id){
        $this->load->model('currencies_model');

        $send_mail_approve = $this->session->userdata("send_mail_approve");
        if((isset($send_mail_approve)) && $send_mail_approve != ''){
            $data['send_mail_approve'] = $send_mail_approve;
            $this->session->unset_userdata("send_mail_approve");
        }
        
        $data['check_appr'] = $this->purchase_model->get_approve_setting('payment_request');
        $data['get_staff_sign'] = $this->purchase_model->get_staff_sign($id,'payment_request');
        $data['check_approve_status'] = $this->purchase_model->check_approval_details($id,'payment_request');
        $data['list_approve_status'] = $this->purchase_model->get_list_approval_details($id,'payment_request');


        $data['payment_invoice'] = $this->purchase_model->get_payment_pur_invoice($id);
        $data['title'] = _l('payment_for').' '.get_pur_invoice_number($data['payment_invoice']->pur_invoice);

        $data['invoice'] = $this->purchase_model->get_pur_invoice($data['payment_invoice']->pur_invoice);

        $data['base_currency'] = $this->currencies_model->get_base_currency();
        if($data['invoice']->currency != 0){
            $data['base_currency'] = pur_get_currency_by_id($data['invoice']->currency);
        }

        $this->load->view('invoices/payment_invoice',$data);
    }

    /**
     * { purchase invoice attachment }
     */
    public function purchase_invoice_attachment($id){
        handle_pur_invoice_file($id);
        redirect(admin_url('purchase/purchase_invoice/'.$id));
    }

    /**
     * { preview purchase invoice file }
     *
     * @param        $id      The identifier
     * @param        $rel_id  The relative identifier
     * @return  view
     */
    public function file_purinv($id, $rel_id)
    {
        $data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
        $data['current_user_is_admin']             = is_admin();
        $data['file'] = $this->purchase_model->get_file($id, $rel_id);
        if (!$data['file']) {
            header('HTTP/1.0 404 Not Found');
            die;
        }
        $this->load->view('invoices/_file', $data);
    }

    /**
     * { delete purchase order attachment }
     *
     * @param      <type>  $id     The identifier
     */
    public function delete_purinv_attachment($id)
    {
        $this->load->model('misc_model');
        $file = $this->misc_model->get_file($id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo pur_html_entity_decode($this->purchase_model->delete_purinv_attachment($id));
        } else {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }
    }

    /**
     * { purchase estimate pdf }
     *
     * @param      <type>  $id     The identifier
     * @return pdf output
     */
    public function purestimate_pdf($id)
    {
        if (!$id) {
            redirect(admin_url('purchase/quotations'));
        }

        $pur_estimate = $this->purchase_model->get_purestimate_pdf_html($id);

        try {
            $pdf = $this->purchase_model->purestimate_pdf($pur_estimate,$id);
        } catch (Exception $e) {
            echo pur_html_entity_decode($e->getMessage());
            die;
        }
        
        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf->Output(format_pur_estimate_number($id).'.pdf', $type);
    }

    /**
     * Sends a request quotation.
     * @return redirect
     */
    public function send_quotation(){
        if($this->input->post()){
            $data = $this->input->post();
            $data['content'] = $this->input->post('content', false);
            $send = $this->purchase_model->send_quotation($data);
            if($send){
                set_alert('success',_l('send_quotation_successfully'));
                
            }else{
                set_alert('warning',_l('send_quotation_fail'));
            }
            redirect(admin_url('purchase/quotations/'.$data['pur_estimate_id']));
            
        }
    }

    /**
     * Sends a purchase order.
     * @return redirect
     */
    public function send_po(){
        if($this->input->post()){
            $data = $this->input->post();
            $data['content'] = $this->input->post('content', false);
            $send = $this->purchase_model->send_po($data);
            if($send){
                set_alert('success',_l('send_po_successfully'));
                
            }else{
                set_alert('warning',_l('send_po_fail'));
            }
            redirect(admin_url('purchase/purchase_order/'.$data['po_id']));
            
        }
    }

    /**
     * import xlsx commodity
     * @param  integer $id
     * @return view
     */
    public function import_xlsx_commodity() {
        if (!is_admin() && !has_permission('purchase_items', '', 'create')) {
            access_denied('purchase');
        }
        $this->load->model('staff_model');
        $data_staff = $this->staff_model->get(get_staff_user_id());

        /*get language active*/
        if ($data_staff) {
            if ($data_staff->default_language != '') {
                $data['active_language'] = $data_staff->default_language;

            } else {

                $data['active_language'] = get_option('active_language');
            }

        } else {
            $data['active_language'] = get_option('active_language');
        }
        $data['title'] = _l('import_excel');

        $this->load->view('items/import_excel', $data);
    }

    /**
     * import file xlsx commodity
     * @return json
     */
    public function import_file_xlsx_commodity() {
        if (!is_admin() && !has_permission('purchase_items', '', 'create')) {
            access_denied(_l('purchase'));
        }

        if(!class_exists('XLSXReader_fin')){
            require_once(module_dir_path(PURCHASE_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
        }
        require_once(module_dir_path(PURCHASE_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');

        $total_row_false = 0;
        $total_rows_data = 0;
        $dataerror = 0;
        $total_row_success = 0;
        $total_rows_data_error = 0;
        $filename='';

        if ($this->input->post()) {

            /*delete file old before export file*/
            $path_before = COMMODITY_ERROR_PUR.'FILE_ERROR_COMMODITY'.get_staff_user_id().'.xlsx';
            if(file_exists($path_before)){
                unlink(COMMODITY_ERROR_PUR.'FILE_ERROR_COMMODITY'.get_staff_user_id().'.xlsx');
            }

            if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
                //do_action('before_import_leads');

                // Get the temp file path
                $tmpFilePath = $_FILES['file_csv']['tmp_name'];
                // Make sure we have a filepath
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    $tmpDir = TEMP_FOLDER . '/' . time() . uniqid() . '/';

                    if (!file_exists(TEMP_FOLDER)) {
                        mkdir(TEMP_FOLDER, 0755);
                    }

                    if (!file_exists($tmpDir)) {
                        mkdir($tmpDir, 0755);
                    }

                    // Setup our new file path
                    $newFilePath = $tmpDir . $_FILES['file_csv']['name'];

                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                        
                        //Writer file
                        $writer_header = array(
                            _l('commodity_code')          =>'string',
                            _l('commodity_name')          =>'string',
                            _l('commodity_barcode')          =>'string',
                            _l('sku_code')               =>'string',
                            _l('sku_name')       =>'string',
                            _l('description')             =>'string',
                            _l('unit_id')                      =>'string',
                            _l('commodity_group')                     =>'string',
                            _l('sub_group')                     =>'string',
                            _l('purchase_price')                     =>'string',
                            _l('rate')                     =>'string',
                            _l('tax_1')                     =>'string',
                            _l('tax_2')                     =>'string',
                            _l('error')                     =>'string',
                        );

                        $widths_arr = array();
                        for($i = 1; $i <= count($writer_header); $i++ ){
                            $widths_arr[] = 40;
                        }

                        $writer = new XLSXWriter();
                        $writer->writeSheetHeader('Sheet1', $writer_header,  $col_options = ['widths'=>$widths_arr ]);

                        //Reader file
                        $xlsx = new XLSXReader_fin($newFilePath);
                        $sheetNames = $xlsx->getSheetNames();
                        $data = $xlsx->getSheetData($sheetNames[1]);

                        $total_rows = 0;
                        $total_row_false    = 0;
                        
                        for ($row = 1; $row < count($data); $row++) {

                            $total_rows++;

                            $rd = array();
                            $flag = 0;
                            $flag2 = 0;

                            $string_error ='';

                            $flag_id_unit_id;
                            $flag_id_commodity_group;
                            $flag_id_sub_group;
                            $flag_id_tax;
                            $flag_id_tax2;

                            $value_commodity_code    = isset($data[$row][0]) ? $data[$row][0] : '' ;
                            $value_commodity_name    = isset($data[$row][1]) ? $data[$row][1] : '' ;
                            $value_commodity_barcode    = isset($data[$row][2]) ? $data[$row][2] : '';
                            $value_sku_code   = isset($data[$row][3]) ? $data[$row][3] : '' ;
                            $value_sku_name      = isset($data[$row][4]) ? $data[$row][4] : '';
                            $value_description       = isset($data[$row][5]) ? $data[$row][5] : '';
                            $value_unit_id            = isset($data[$row][6]) ? $data[$row][6] : '';
                            $value_commodity_group            = isset($data[$row][7]) ? $data[$row][7] : '';
                            $value_sub_group            = isset($data[$row][8]) ? $data[$row][8] : '';
                            $value_purchase_price            = isset($data[$row][9]) ? $data[$row][9] : '';
                            $value_rate            = isset($data[$row][10]) ? $data[$row][10] : '';
                            $value_tax            = isset($data[$row][11]) ? $data[$row][11] : '';
                            $value_tax2            = isset($data[$row][12]) ? $data[$row][12] : '';

                            if(is_null($value_commodity_code) == true || $value_commodity_code ==''){
                                $string_error .=_l('commodity_code'). _l('not_yet_entered');
                                $flag = 1;
                            }else{
                                $this->db->where('commodity_code', $value_commodity_code);
                                $total_rows_check = $this->db->count_all_results(db_prefix().'items');
                                if ($total_rows_check > 0) {
                                    $string_error .=_l('commodity_code'). _l('already_exist');
                                    $flag = 1;
                                }
                            }
                            
                            if(is_null($value_commodity_name) == true || $value_commodity_name ==''){
                                $string_error .=_l('commodity_name'). _l('not_yet_entered');
                                $flag = 1;
                            }

                            //check unit_code exist  (input: id or name contract)
                            if (is_null($value_unit_id) != true && ( $value_unit_id != '0') && $value_unit_id != '') {
                                /*case input id*/
                                if (is_numeric($value_unit_id)) {
                                    $this->db->where('unit_type_id', $value_unit_id);
                                    $unit_id_value = $this->db->count_all_results(db_prefix() . 'ware_unit_type');
                                    if ($unit_id_value == 0) {
                                        $string_error .= _l('unit_id') . _l('does_not_exist');
                                        $flag2 = 1;
                                    } else {
                                        /*get id unit_id*/
                                        $flag_id_unit_id = $value_unit_id;
                                    }
                                } else {
                                    /*case input name*/
                                    $this->db->like(db_prefix() . 'ware_unit_type.unit_code', $value_unit_id);
                                    $unit_id_value = $this->db->get(db_prefix() . 'ware_unit_type')->result_array();
                                    if (count($unit_id_value) == 0) {
                                        $string_error .= _l('unit_id') . _l('does_not_exist');
                                        $flag2 = 1;
                                    } else {
                                        /*get unit_id*/
                                        $flag_id_unit_id = $unit_id_value[0]['unit_type_id'];
                                    }
                                }
                            }

                            //check commodity_group exist  (input: id or name contract)
                            if (is_null($value_commodity_group) != true && ($value_commodity_group != '0') && $value_commodity_group != '') {
                                /*case input id*/
                                if (is_numeric($value_commodity_group)) {
                                    $this->db->where('id', $value_commodity_group);
                                    $commodity_group_value = $this->db->count_all_results(db_prefix() . 'items_groups');
                                    if ($commodity_group_value == 0) {
                                        $string_error .= _l('commodity_group') . _l('does_not_exist');
                                        $flag2 = 1;
                                    } else {
                                        /*get id commodity_group*/
                                        $flag_id_commodity_group = $value_commodity_group;
                                    }
                                } else {
                                    /*case input name*/
                                    $this->db->like(db_prefix() . 'items_groups.commodity_group_code', $value_commodity_group);
                                    $commodity_group_value = $this->db->get(db_prefix() . 'items_groups')->result_array();
                                    if (count($commodity_group_value) == 0) {
                                        $string_error .= _l('commodity_group') . _l('does_not_exist');
                                        $flag2 = 1;
                                    } else {
                                        /*get id commodity_group*/
                                        $flag_id_commodity_group = $commodity_group_value[0]['id'];
                                    }
                                }
                            }

                            //check taxes exist  (input: id or name contract)
                            if (is_null($value_tax) != true && ($value_tax!= '0') && $value_tax != '') {
                                /*case input id*/
                                if (is_numeric($value_tax)) {
                                    $this->db->where('id', $value_tax);
                                    $cell_tax_value = $this->db->count_all_results(db_prefix() . 'taxes');
                                    if ($cell_tax_value == 0) {
                                        $string_error .= _l('tax') . _l('does_not_exist');
                                        $flag2 = 1;
                                    } else {
                                        /*get id cell_tax*/
                                        $flag_id_tax = $value_tax;
                                    }
                                } else {
                                    /*case input name*/
                                    $this->db->like(db_prefix() . 'taxes.name', $value_tax);
                                    $cell_tax_value = $this->db->get(db_prefix() . 'taxes')->result_array();
                                    if (count($cell_tax_value) == 0) {
                                        $string_error .= _l('tax') . _l('does_not_exist');
                                        $flag2 = 1;
                                    } else {
                                        /*get id warehouse_id*/
                                        $flag_id_tax = $cell_tax_value[0]['id'];
                                    }
                                }
                            }

                            //check taxes exist  (input: id or name contract)
                            if (is_null($value_tax2) != true && ($value_tax2!= '0') && $value_tax2 != '') {
                                /*case input id*/
                                if (is_numeric($value_tax2)) {
                                    $this->db->where('id', $value_tax2);
                                    $cell_tax_value2 = $this->db->count_all_results(db_prefix() . 'taxes');
                                    if ($cell_tax_value2 == 0) {
                                        $string_error .= _l('tax') . _l('does_not_exist');
                                        $flag2 = 1;
                                    } else {
                                        /*get id cell_tax*/
                                        $flag_id_tax2 = $value_tax2;
                                    }
                                } else {
                                    /*case input name*/
                                    $this->db->like(db_prefix() . 'taxes.name', $value_tax2);
                                    $cell_tax_value2 = $this->db->get(db_prefix() . 'taxes')->result_array();
                                    if (count($cell_tax_value2) == 0) {
                                        $string_error .= _l('tax') . _l('does_not_exist');
                                        $flag2 = 1;
                                    } else {
                                        /*get id warehouse_id*/
                                        $flag_id_tax2 = $cell_tax_value2[0]['id'];
                                    }
                                }
                            }

                            //check commodity_group exist  (input: id or name contract)
                            if (is_null($value_sub_group) != true && $value_sub_group != '') {
                                /*case input id*/
                                if (is_numeric($value_sub_group)) {
                                    $this->db->where('id', $value_sub_group);
                                    $sub_group_value = $this->db->count_all_results(db_prefix() . 'wh_sub_group');
                                    if ($sub_group_value == 0) {
                                        $string_error .= _l('sub_group') . _l('does_not_exist');
                                        $flag2 = 1;
                                    } else {
                                        /*get id sub_group*/
                                        $flag_id_sub_group = $value_sub_group;
                                    }
                                } else {
                                    /*case input  name*/
                                    $this->db->like(db_prefix() . 'wh_sub_group.sub_group_code', $value_sub_group);
                                    $sub_group_value = $this->db->get(db_prefix() . 'wh_sub_group')->result_array();
                                    if (count($sub_group_value) == 0) {
                                        $string_error .= _l('sub_group') . _l('does_not_exist');
                                        $flag2 = 1;
                                    } else {
                                        /*get id sub_group*/
                                        $flag_id_sub_group = $sub_group_value[0]['id'];
                                    }
                                }
                            }

                            //check value_rate input
                            if (is_null($value_rate) != true && $value_rate != '') {
                                if (!check_valid_number_with_setting($value_rate)) {
                                    $string_error .= _l('cell_rate') . _l('_check_invalid');
                                    $flag = 1;
                                }
                            }
                            //check value_purchase_price input
                            if (is_null($value_purchase_price) != true && $value_purchase_price != '') {
                                if (!check_valid_number_with_setting($value_purchase_price)) {
                                    $string_error .= _l('purchase_price') . _l('_check_invalid');
                                    $flag = 1;
                                }
                            }

                            if(($flag == 1) || $flag2 == 1 ){
                                //write error file
                                $writer->writeSheetRow('Sheet1', [
                                    $value_commodity_code,
                                    $value_commodity_name,
                                    $value_commodity_barcode,
                                    $value_sku_code,
                                    $value_sku_name,
                                    $value_description,
                                    $value_unit_id,
                                    $value_commodity_group,
                                    $value_sub_group,
                                    $value_purchase_price,
                                    $value_rate,
                                    $value_tax,
                                    $value_tax2,
                                    $string_error,
                                ]);

                                // $numRow++;
                                $total_row_false++;
                            }

                            if($flag == 0 && $flag2 == 0){
                                $rd['commodity_code']                = $value_commodity_code;
                                $rd['description']                = $value_commodity_name;
                                $rd['commodity_barcode']                     = $value_commodity_barcode;
                                $rd['sku_code']     = $value_sku_code;
                                $rd['sku_name']                = $value_sku_name;
                                $rd['long_description']                         = $value_description;
                                $rd['unit_id']                         = isset($flag_id_unit_id) ? $flag_id_unit_id : '';
                                $rd['group_id']                         = isset($flag_id_commodity_group) ? $flag_id_commodity_group : '';
                                $rd['sub_group']                         = isset($flag_id_sub_group) ? $flag_id_sub_group : '';
                                $rd['tax']                         = isset($flag_id_tax) ? $flag_id_tax : '';
                                $rd['tax2']                         = isset($flag_id_tax2) ? $flag_id_tax2 : '';
                                $rd['rate']                         = reformat_currency_pur($value_rate);
                                $rd['purchase_price']                         = reformat_currency_pur($value_purchase_price);
                               
                                $rows[] = $rd;
                                $response = $this->purchase_model->import_xlsx_commodity($rd);
                            }
                        }

                        $total_rows = $total_rows;
                        $total_row_success = isset($rows) ? count($rows) : 0;
                        // $dataerror = $dataError;
                        $dataerror = '';
                        $message ='Not enought rows for importing';

                        if($total_row_false != 0){
                            $filename = 'Import_item_error_'.get_staff_user_id().'_'.strtotime(date('Y-m-d H:i:s')).'.xlsx';
                            $writer->writeToFile(str_replace($filename, PURCHASE_IMPORT_ITEM_ERROR.$filename, $filename));
                        }
                    }
                    
                } else {
                    set_alert('warning', _l('import_upload_failed'));
                }
            }

        }
        echo json_encode([
            'message'           => $message,
            'total_row_success' => $total_row_success,
            'total_row_false'   => $total_row_false,
            'total_rows'        => $total_rows,
            'site_url'          => site_url(),
            'staff_id'          => get_staff_user_id(),
            'filename'          => PURCHASE_IMPORT_ITEM_ERROR.$filename,

        ]);

    }

    /**
     * { import vendor }
     */
    public function vendor_import()
    {
        if (!has_permission('purchase_vendors', '', 'create')) {
            access_denied('purchase');
        }

        $this->load->model('staff_model');
        $data_staff = $this->staff_model->get(get_staff_user_id());

        /*get language active*/
        if ($data_staff) {
            if ($data_staff->default_language != '') {
                $data['active_language'] = $data_staff->default_language;

            } else {

                $data['active_language'] = get_option('active_language');
            }

        } else {
            $data['active_language'] = get_option('active_language');
        }
        $data['title'] = _l('import_excel');

        $this->load->view('vendors/import_excel', $data);
    }

    /**
     * { reset data }
     */
    public function reset_data()
    {

        if ( !is_admin()) {
            access_denied('purchase');
        }

            //delete purchase request
            $this->db->truncate(db_prefix().'pur_request');
            //delete purchase request detail
            $this->db->truncate(db_prefix().'pur_request_detail');
            //delete purchase order
            $this->db->truncate(db_prefix().'pur_orders');
            //delete purchase order detail
            $this->db->truncate(db_prefix().'pur_order_detail');
            //delete purchase order payment
            $this->db->truncate(db_prefix().'pur_order_payment');
            //delete purchase invoice
            $this->db->truncate(db_prefix().'pur_invoices');
            //delete purchase invoice payment
            $this->db->truncate(db_prefix().'pur_invoice_payment');
            //delete purchase estimate
            $this->db->truncate(db_prefix().'pur_estimates');
            //delete pur_estimate_detail
            $this->db->truncate(db_prefix().'pur_estimate_detail');
            //delete pur_contracts
            $this->db->truncate(db_prefix().'pur_contracts');
            //delete tblpur_approval_details
            $this->db->truncate(db_prefix().'pur_approval_details');

            //delete create task rel_type: "pur_contract", "pur_contract".
            $this->db->where('rel_type', 'pur_contract');
            $this->db->or_where('rel_type', 'pur_order');
            $this->db->or_where('rel_type', 'pur_quotation');
            $this->db->or_where('rel_type', 'pur_invoice');
            $this->db->delete(db_prefix() . 'tasks');


            $this->db->where('rel_type', 'pur_contract');
            $this->db->or_where('rel_type', 'pur_order');
            $this->db->or_where('rel_type', 'pur_estimate');
            $this->db->or_where('rel_type', 'pur_invoice');
            $this->db->delete(db_prefix() . 'files');

            delete_files_pur(PURCHASE_MODULE_UPLOAD_FOLDER.'/pur_order/');
            delete_files_pur(PURCHASE_MODULE_UPLOAD_FOLDER.'/pur_contract/');
            delete_files_pur(PURCHASE_MODULE_UPLOAD_FOLDER.'/pur_order/signature/');
            delete_files_pur(PURCHASE_MODULE_UPLOAD_FOLDER.'/pur_invoice/');
            delete_files_pur(PURCHASE_MODULE_UPLOAD_FOLDER.'/pur_estimate/');
            delete_files_pur(PURCHASE_MODULE_UPLOAD_FOLDER.'/pur_estimate/signature/');
            delete_files_pur(PURCHASE_MODULE_UPLOAD_FOLDER.'/payment_invoice/signature/');
            delete_files_pur(PURCHASE_MODULE_UPLOAD_FOLDER.'/payment_request/signature/');
            delete_files_pur(PURCHASE_MODULE_UPLOAD_FOLDER.'/request_quotation/');
            delete_files_pur(PURCHASE_MODULE_UPLOAD_FOLDER.'/send_po/');
            delete_files_pur(PURCHASE_MODULE_UPLOAD_FOLDER.'/send_quotation/');

            $this->db->where('rel_type', 'pur_contract');
            $this->db->or_where('rel_type', 'purchase_order');
            $this->db->or_where('rel_type', 'pur_invoice');
            $this->db->delete(db_prefix() . 'notes');

            $this->db->where('rel_type', 'pur_contract');
            $this->db->or_where('rel_type', 'purchase_order');
            $this->db->or_where('rel_type', 'pur_invoice');
            $this->db->delete(db_prefix() . 'reminders');

            $this->db->where('fieldto', 'pur_order');
            $this->db->delete(db_prefix() . 'customfieldsvalues');

            $this->db->where('rel_type', 'pur_invoice');
            $this->db->or_where('rel_type', 'pur_order');
            $this->db->delete(db_prefix() . 'taggables');

            set_alert('success',_l('reset_data_successful'));
            
            redirect(admin_url('purchase/setting'));

    }

    /**
     * Removes a po logo.
     */
    public function remove_po_logo(){
        if ( !is_admin()) {
            access_denied('purchase');
        }

        $success = $this->purchase_model->remove_po_logo();
        if($success){
            set_alert('success', _l('deleted', _l('po_logo')));
        }
        redirect(admin_url('purchase/setting'));
    }

    /**
     * delete_error file day before
     * @return [type] 
     */
    public function delete_error_file_day_before()
    {
        //Delete old file before 7 day
        $date = date_create(date('Y-m-d H:i:s'));
        date_sub($date,date_interval_create_from_date_string("7 days"));
        $before_7_day = strtotime(date_format($date,"Y-m-d H:i:s"));

        foreach(glob(PURCHASE_IMPORT_VENDOR_ERROR . '*') as $file) {

            $file_arr = explode("/",$file);
            $filename = array_pop($file_arr);

            if(file_exists($file)) {
                $file_name_arr = explode("_",$filename);
                $date_create_file = array_pop($file_name_arr);
                $date_create_file =  str_replace('.xlsx', '', $date_create_file);

                if((float)$date_create_file <= (float)$before_7_day){
                    unlink(PURCHASE_IMPORT_VENDOR_ERROR.$filename);
                }
            }
        }
        return true;
    }

    /**
     * { import job position excel }
     */
    public function import_file_xlsx_vendor()
    {
        if(!class_exists('XLSXReader_fin')){
            require_once(module_dir_path(PURCHASE_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
        }
        require_once(module_dir_path(PURCHASE_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');


        $filename ='';
        if($this->input->post()){
            if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {

                $this->delete_error_file_day_before();

                // Get the temp file path
                $tmpFilePath = $_FILES['file_csv']['tmp_name'];                
                // Make sure we have a filepath
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    $tmpDir = TEMP_FOLDER . '/' . time() . uniqid() . '/';

                    if (!file_exists(TEMP_FOLDER)) {
                        mkdir(TEMP_FOLDER, 0755);
                    }

                    if (!file_exists($tmpDir)) {
                        mkdir($tmpDir, 0755);
                    }

                    // Setup our new file path
                    $newFilePath = $tmpDir . $_FILES['file_csv']['name'];                    

                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                        //Writer file
                        $writer_header = array(
                            _l('vendor_code')          =>'string',
                            _l('first_name')          =>'string',
                            _l('last_name')          =>'string',
                            _l('email')               =>'string',
                            _l('contact_phonenumber')       =>'string',
                            _l('position')             =>'string',
                            _l('company')                      =>'string',
                            _l('vat')                     =>'string',
                            _l('phonenumber')                     =>'string',
                            _l('country')                     =>'string',
                            _l('city')                     =>'string',
                            _l('zip')                     =>'string',
                            _l('state')                     =>'string',
                            _l('address')                     =>'string',
                            _l('website')                     =>'string',
                            _l('bank_detail')                     =>'string',
                            _l('payment_terms')                     =>'string',
                            _l('pur_billing_street')                     =>'string',
                            _l('pur_billing_city')                     =>'string',
                            _l('pur_billing_state')                     =>'string',
                            _l('pur_billing_zip')                     =>'string',
                            _l('pur_billing_country')                     =>'string',
                            _l('pur_shipping_street')                     =>'string',
                            _l('pur_shipping_city')                     =>'string',
                            _l('pur_shipping_state')                     =>'string',
                            _l('pur_shipping_zip')                     =>'string',
                            _l('pur_shipping_country')                     =>'string',
                            _l('error')                     =>'string',
                        );

                        $widths_arr = array();
                        for($i = 1; $i <= count($writer_header); $i++ ){
                            $widths_arr[] = 40;
                        }

                        $writer = new XLSXWriter();
                        $writer->writeSheetHeader('Sheet1', $writer_header,  $col_options = ['widths'=>$widths_arr ]);

                        //Reader file
                        $xlsx = new XLSXReader_fin($newFilePath);
                        $sheetNames = $xlsx->getSheetNames();
                        $data = $xlsx->getSheetData($sheetNames[1]);

                        $total_rows = 0;
                        $total_row_false    = 0; 

                        for ($row = 1; $row < count($data); $row++) {

                            $total_rows++;

                            $rd = array();
                            $flag = 0;
                            $flag2 = 0;

                            $string_error ='';

                            $value_vendor_code    = isset($data[$row][0]) ? $data[$row][0] : '' ;
                            $value_fist_name    = isset($data[$row][1]) ? $data[$row][1] : '' ;
                            $value_last_name    = isset($data[$row][2]) ? $data[$row][2] : '';
                            $value_email   = isset($data[$row][3]) ? $data[$row][3] : '' ;
                            $value_contact_phonenumber      = isset($data[$row][4]) ? $data[$row][4] : '';
                            $value_position       = isset($data[$row][5]) ? $data[$row][5] : '';
                            $value_company            = isset($data[$row][6]) ? $data[$row][6] : '';
                            $value_vat            = isset($data[$row][7]) ? $data[$row][7] : '';
                            $value_phonenumber            = isset($data[$row][8]) ? $data[$row][8] : '';
                            $value_country            = isset($data[$row][9]) ? $data[$row][9] : '';
                            $value_city            = isset($data[$row][10]) ? $data[$row][10] : '';
                            $value_zip            = isset($data[$row][11]) ? $data[$row][11] : '';
                            $value_state            = isset($data[$row][12]) ? $data[$row][12] : '';
                            $value_address            = isset($data[$row][13]) ? $data[$row][13] : '';
                            $value_website            = isset($data[$row][14]) ? $data[$row][14] : '';
                            $value_bank_detail            = isset($data[$row][15]) ? $data[$row][15] : '';
                            $value_payment_terms            = isset($data[$row][16]) ? $data[$row][16] : '';
                            $value_pur_billing_street            = isset($data[$row][17]) ? $data[$row][17] : '';
                            $value_pur_billing_city            = isset($data[$row][18]) ? $data[$row][18] : '';
                            $value_pur_billing_state            = isset($data[$row][19]) ? $data[$row][19] : '';
                            $value_pur_billing_zip            = isset($data[$row][20]) ? $data[$row][20] : '';
                            $value_pur_billing_country            = isset($data[$row][21]) ? $data[$row][21] : '';
                            $value_pur_shipping_street            = isset($data[$row][22]) ? $data[$row][22] : '';
                            $value_pur_shipping_city            = isset($data[$row][23]) ? $data[$row][23] : '';
                            $value_pur_shipping_state            = isset($data[$row][24]) ? $data[$row][24] : '';
                            $value_pur_shipping_zip            = isset($data[$row][25]) ? $data[$row][25] : '';
                            $value_pur_shipping_country            = isset($data[$row][26]) ? $data[$row][26] : '';

                            if(is_null($value_vendor_code) == true || $value_vendor_code ==''){
                                $string_error .=_l('vendor_code'). _l('not_yet_entered');
                                $flag = 1;
                            }else{
                                $this->db->where('vendor_code', $value_vendor_code);
                                $total_rows_check = $this->db->count_all_results(db_prefix().'pur_vendor');
                                if ($total_rows_check > 0) {
                                    $string_error .=_l('vendor_code'). _l('already_exist');
                                    $flag = 1;
                                }
                            }
                            
                            if(is_null($value_fist_name) == true || $value_fist_name ==''){
                                $string_error .=_l('fist_name'). _l('not_yet_entered');
                                $flag = 1;
                            }

                            if(is_null($value_last_name) == true || $value_last_name ==''){
                                $string_error .=_l('last_name'). _l('not_yet_entered');
                                $flag = 1;
                            }

                            if(is_null($value_email) == true || $value_email ==''){
                                $string_error .=_l('email'). _l('not_yet_entered');
                                $flag = 1;
                            }else{
                                $this->db->where('email', $value_email);
                                $total_rows_check_email = $this->db->count_all_results(db_prefix().'pur_contacts');
                                if ($total_rows_check_email > 0) {
                                    $string_error .=_l('email'). _l('already_exist');
                                    $flag = 1;
                                }
                            }

                            if(is_null($value_company) == true || $value_company ==''){
                                $string_error .=_l('company'). _l('not_yet_entered');
                                $flag = 1;
                            }

                            if(($flag == 1) || $flag2 == 1 ){
                                //write error file
                                $writer->writeSheetRow('Sheet1', [
                                    $value_vendor_code,
                                    $value_fist_name,
                                    $value_last_name,
                                    $value_email,
                                    $value_contact_phonenumber,
                                    $value_position,
                                    $value_company,
                                    $value_vat,
                                    $value_phonenumber,
                                    $value_country,
                                    $value_city,
                                    $value_zip,
                                    $value_state,
                                    $value_address,
                                    $value_website,
                                    $value_bank_detail,
                                    $value_payment_terms,
                                    $value_pur_billing_street,
                                    $value_pur_billing_city,
                                    $value_pur_billing_state,
                                    $value_pur_billing_zip,
                                    $value_pur_billing_country,
                                    $value_pur_shipping_street,
                                    $value_pur_shipping_city,
                                    $value_pur_shipping_state,
                                    $value_pur_shipping_zip,
                                    $value_pur_shipping_country,
                                    $string_error,
                                ]);

                                // $numRow++;
                                $total_row_false++;
                            }

                            if($flag == 0 && $flag2 == 0){
                                $rd['vendor_code']                = $value_vendor_code;
                                $rd['firstname']                = $value_fist_name;
                                $rd['lastname']                     = $value_last_name;
                                $rd['email']     = $value_email;
                                $rd['contact_phonenumber']                = $value_contact_phonenumber;
                                $rd['title']                         = $value_position;
                                $rd['company']                         = $value_company;
                                $rd['vat']                         = $value_vat;
                                $rd['phonenumber']                         = $value_phonenumber;
                                $rd['country']                         = $value_country;
                                $rd['city']                         = $value_city;
                                $rd['zip']                         = $value_zip;
                                $rd['state']                         = $value_state;
                                $rd['address']                         = $value_address;
                                $rd['website']                         = $value_website;
                                $rd['bank_detail']                         = $value_bank_detail;
                                $rd['payment_terms']                         = $value_payment_terms;
                                $rd['billing_street']                         = $value_pur_billing_street;
                                $rd['billing_city']                         = $value_pur_billing_city;
                                $rd['billing_state']                         = $value_pur_billing_state;
                                $rd['billing_zip']                         = $value_pur_billing_zip;
                                $rd['billing_country']                         = $value_pur_billing_country;
                                $rd['shipping_street']                         = $value_pur_shipping_street;
                                $rd['shipping_city']                         = $value_pur_shipping_city;
                                $rd['shipping_state']                         = $value_pur_shipping_state;
                                $rd['shipping_zip']                         = $value_pur_shipping_zip;
                                $rd['shipping_country']                         = $value_pur_shipping_country;

                                $rows[] = $rd;
                                $response = $this->purchase_model->add_vendor($rd,null, true);

                            }


                        }

                        $total_rows = $total_rows;
                        $total_row_success = isset($rows) ? count($rows) : 0;
                        // $dataerror = $dataError;
                        $dataerror = '';
                        $message ='Not enought rows for importing';

                        if($total_row_false != 0){
                            $filename = 'Import_vendor_error_'.get_staff_user_id().'_'.strtotime(date('Y-m-d H:i:s')).'.xlsx';
                            $writer->writeToFile(str_replace($filename, PURCHASE_IMPORT_VENDOR_ERROR.$filename, $filename));
                        }

                    }
                }
            }
        }


        if (file_exists($newFilePath)) {
            @unlink($newFilePath);
        }

        echo json_encode([
            'message'           => $message,
            'total_row_success' => $total_row_success,
            'total_row_false'   => $total_row_false,
            'total_rows'        => $total_rows,
            'site_url'          => site_url(),
            'staff_id'          => get_staff_user_id(),
            'filename'          => PURCHASE_IMPORT_VENDOR_ERROR.$filename,
        ]);
    }

    /**
     * { change delivery status }
     *
     * @param      integer  $status     The status
     * @param         $pur_order  The pur order
     * @return     json
     */
    public function change_delivery_status($status, $pur_order){
        $success = $this->purchase_model->change_delivery_status($status, $pur_order);
        $message = '';
        $html = '';
        $status_str = '';
        $class = '';
        if($success == true){
            $message = _l('change_delivery_status_successfully');
        }else{
            $message = _l('change_delivery_status_fail');
        }

        if(has_permission('purchase_orders', '', 'edit') || is_admin()){
            $html .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
            $html .= '<a href="#" class="dropdown-toggle text-dark" id="tablePurOderStatus-' . $pur_order . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
            $html .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
            $html .= '</a>';

            $html .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tablePurOderStatus-' . $pur_order . '">';

            if($status == 0){
                $html .= '<li>
                          <a href="#" onclick="change_delivery_status( 1 ,' . $pur_order . '); return false;">
                             ' ._l('completely_delivered') . '
                          </a>
                       </li>';
                $html .= '<li>
                          <a href="#" onclick="change_delivery_status( 2 ,' . $pur_order . '); return false;">
                             ' ._l('pending_delivered') . '
                          </a>
                       </li>';
                $html .= '<li>
                          <a href="#" onclick="change_delivery_status( 3 ,' . $pur_order . '); return false;">
                             ' ._l('partially_delivered') . '
                          </a>
                       </li>';

                $status_str = _l('undelivered');
                $class = 'label-danger';
            }else if($status == 1){
                $html .= '<li>
                          <a href="#" onclick="change_delivery_status( 0 ,' . $pur_order . '); return false;">
                             ' ._l('undelivered') . '
                          </a>
                       </li>';
                $html .= '<li>
                          <a href="#" onclick="change_delivery_status( 2 ,' . $pur_order . '); return false;">
                             ' ._l('pending_delivered') . '
                          </a>
                       </li>';
                $html .= '<li>
                          <a href="#" onclick="change_delivery_status( 3 ,' . $pur_order . '); return false;">
                             ' ._l('partially_delivered') . '
                          </a>
                       </li>';
                $status_str = _l('completely_delivered');
                $class = 'label-success';
            }else if($status == 2){ 
                $html .= '<li>
                          <a href="#" onclick="change_delivery_status( 0 ,' . $pur_order . '); return false;">
                             ' ._l('undelivered') . '
                          </a>
                       </li>';
                $html .= '<li>
                          <a href="#" onclick="change_delivery_status( 1 ,' . $pur_order . '); return false;">
                             ' ._l('completely_delivered') . '
                          </a>
                       </li>';
                $html .= '<li>
                          <a href="#" onclick="change_delivery_status( 3 ,' . $pur_order . '); return false;">
                             ' ._l('partially_delivered') . '
                          </a>
                       </li>';
                $status_str = _l('pending_delivered');
                $class = 'label-info';
            }else if($status == 3){ 
                $html .= '<li>
                          <a href="#" onclick="change_delivery_status( 0 ,' . $pur_order . '); return false;">
                             ' ._l('undelivered') . '
                          </a>
                       </li>';
                $html .= '<li>
                          <a href="#" onclick="change_delivery_status( 1 ,' . $pur_order . '); return false;">
                             ' ._l('completely_delivered') . '
                          </a>
                       </li>';
                $html .= '<li>
                          <a href="#" onclick="change_delivery_status( 2 ,' . $pur_order . '); return false;">
                             ' ._l('pending_delivered') . '
                          </a>
                       </li>';
                $status_str = _l('partially_delivered');
                $class = 'label-warning';
            }
               

            $html .= '</ul>';
            $html .= '</div>';
        }

        echo json_encode([
            'success' => $success,
            'status_str' => $status_str,
            'class' => $class,
            'mess' => $message,
            'html' => $html,
        ]);
    }

    /**
     * { convert po payment }
     */
    public function convert_po_payment($pur_order){
        $success = $this->purchase_model->convert_po_payment($pur_order);
        $mess = '';
        if($success == true){
            $mess = _l('converted_succesfully');
        }else{
            $mess = _l('no_payments_are_converted');
        }

        echo json_encode([
            'mess' => $mess,
            'success' => $success,
        ]);
    }

    /**
     * Gets the comments.
     *
     * @param        $id     The identifier
     */
    public function get_comments($id, $type)
    {
        $data['comments'] = $this->purchase_model->get_comments($id, $type);
        $this->load->view('comments_template', $data);
    }

    /**
     * Adds a comment.
     */
    public function add_comment()
    {
        if ($this->input->post()) {
            echo json_encode([
                'success' => $this->purchase_model->add_comment($this->input->post()),
            ]);
        }
    }

    /**
     * { edit comment }
     *
     * @param        $id     The identifier
     */
    public function edit_comment($id)
    {
        if ($this->input->post()) {
            echo json_encode([
                'success' => $this->purchase_model->edit_comment($this->input->post(), $id),
                'message' => _l('comment_updated_successfully'),
            ]);
        }
    }

    /**
     * Removes a comment.
     *
     * @param        $id     The identifier
     */
    public function remove_comment($id)
    {
        $this->db->where('id', $id);
        $comment = $this->db->get(db_prefix().'pur_comments')->row();
        if ($comment) {
            if ($comment->staffid != get_staff_user_id() && !is_admin()) {
                echo json_encode([
                    'success' => false,
                ]);
                die;
            }
            echo json_encode([
                'success' => $this->purchase_model->remove_comment($id),
            ]);
        } else {
            echo json_encode([
                'success' => false,
            ]);
        }
    }

    /**
     * { coppy sale invoice }
     *
     * @param        $invoice  The invoice
     */
    public function coppy_sale_invoice($invoice){
        $this->load->model('currencies_model');
        $this->load->model('invoices_model');

        $inv = $this->invoices_model->get($invoice);
        $base_currency = $this->currencies_model->get_base_currency();

        $list_item = $this->purchase_model->create_purchase_request_row_template();
        $currency_rate = 1;
        $to_currency = $base_currency->id;
        if($inv->currency != 0 && $inv->currency != $base_currency->id){
            $inv_currency = pur_get_currency_by_id($inv->currency);
            $currency_rate = pur_get_currency_rate($base_currency->name, $inv_currency->name);;
            $to_currency = $inv->currency;
        }

        if($inv && isset($inv->items)){
            if(count($inv->items) > 0){

                $index_request = 0;
                foreach($inv->items as $key => $item){
                    $index_request++;

                    $item_taxes = get_invoice_item_taxes($item['id']); 

                    $tax = '';
                    $tax_value = 0;
                    $tax_name = [];
                    $tax_name[0] = '';
                    $tax_rate = '';

                    if(count($item_taxes) > 0){
                        foreach($item_taxes as $key => $_tax){
                            if(($key + 1) < count($item_taxes) ){
                                $tax .= $this->purchase_model->get_tax_by_tax_name($_tax['taxname']).'|';
                                
                                $tax_rate .= $_tax['taxrate'].'|';
                            }else{
                                $tax .= $this->purchase_model->get_tax_by_tax_name($_tax['taxname']);
                               
                                $tax_rate .= $_tax['taxrate'];
                            }
                            $tax_name[] = $_tax['taxname'];

                            $tax_value += $item['qty'] * $item['rate'] * $_tax['taxrate'] / 100;
                        }
                    }



                    $item_code = get_item_id_by_des($item['description']);
                    $item_text = $item['description'];
                    $unit_price = $item['rate'];
                    $unit_name = $item['unit'];
                    $into_money = (float) ($item['rate'] * $item['qty']);
                    $total = $tax_value + $into_money;
                    
                    $list_item .= $this->purchase_model->create_purchase_request_row_template('newitems[' . $index_request . ']', $item_code, $item_text, $unit_price, $item['qty'], $unit_name, '', $into_money, $index_request, $tax_value, $total, $tax_name, $tax_rate, $tax, false, $currency_rate, $to_currency);
                }
            }
        }

        echo json_encode([
            'list_item' => $list_item,
            'currency' => $to_currency,
            'currency_rate' => $currency_rate,
        ]);
        
    }

    /**
     * { coppy sale invoice }
     *
     * @param        $invoice  The invoice
     */
    public function coppy_sale_estimate($estimate_id){
        $this->load->model('currencies_model');
        $this->load->model('estimates_model');

        $estimate = $this->estimates_model->get($estimate_id);
        $base_currency = $this->currencies_model->get_base_currency();

        $list_item = $this->purchase_model->create_purchase_request_row_template();
        $currency_rate = 1;
        $to_currency = $base_currency->id;
        if($estimate->currency != 0 && $estimate->currency != $base_currency->id){
            $es_currency = pur_get_currency_by_id($estimate->currency);
            $currency_rate = pur_get_currency_rate($base_currency->name, $es_currency->name);;
            $to_currency = $estimate->currency;
        }

        if($estimate && isset($estimate->items)){
            if(count($estimate->items) > 0){

                $index_request = 0;
                foreach($estimate->items as $key => $item){
                    $index_request++;

                    $item_taxes = get_invoice_item_taxes($item['id']); 

                    $tax = '';
                    $tax_value = 0;
                    $tax_name = [];
                    $tax_name[0] = '';
                    $tax_rate = '';

                    if(count($item_taxes) > 0){
                        foreach($item_taxes as $key => $_tax){
                            if(($key + 1) < count($item_taxes) ){
                                $tax .= $this->purchase_model->get_tax_by_tax_name($_tax['taxname']).'|';
                                
                                $tax_rate .= $_tax['taxrate'].'|';
                            }else{
                                $tax .= $this->purchase_model->get_tax_by_tax_name($_tax['taxname']);
                               
                                $tax_rate .= $_tax['taxrate'];
                            }
                            $tax_name[] = $_tax['taxname'];

                            $tax_value += $item['qty'] * $item['rate'] * $_tax['taxrate'] / 100;
                        }
                    }



                    $item_code = get_item_id_by_des($item['description']);
                    $item_text = $item['description'];
                    $unit_price = $item['rate'];
                    $unit_name = $item['unit'];
                    $into_money = (float) ($item['rate'] * $item['qty']);
                    $total = $tax_value + $into_money;
                    
                    $list_item .= $this->purchase_model->create_purchase_request_row_template('newitems[' . $index_request . ']', $item_code, $item_text, $unit_price, $item['qty'], $unit_name, '', $into_money, $index_request, $tax_value, $total, $tax_name, $tax_rate, $tax, false, $currency_rate, $to_currency);
                }
            }
        }

        echo json_encode([
            'list_item' => $list_item,
            'currency' => $to_currency,
            'currency_rate' => $currency_rate,
        ]);
        
    }

    /**
     * { inv by client }
     */
    public function inv_by_client(){
        $data_rs = [];
        $html = '';
        if($this->input->post()){
            $clients = $this->input->post('client');
            foreach($clients as $cli){
                $list_inv = $this->purchase_model->get_inv_by_client_for_po($cli);
                if(count($list_inv) > 0){
                    foreach($list_inv as $inv){
                        if(total_rows(db_prefix().'pur_orders', ['sale_invoice' => $inv['id']]) <= 0){
                            $data_rs[] = $inv;
                        }
                    }
                }
            }
        }else{
            $data_rs = $this->purchase_model->get_invoice_for_pr();
        }

        $html .= '<option value=""></option>';
        foreach($data_rs as $rs){
            $html .= '<option value="'.$rs['id'].'">'.format_invoice_number($rs['id']).'</option>';
        }

        echo json_encode(['html' => $html]);
    }

    /**
     * { coppy sale invoice }
     *
     * @param        $invoice  The invoice
     */
    public function coppy_sale_invoice_po($invoice){
        $this->load->model('currencies_model');
        $this->load->model('invoices_model');

        $inv = $this->invoices_model->get($invoice);
        $base_currency = $this->currencies_model->get_base_currency();

        $list_item = $this->purchase_model->create_purchase_order_row_template();

        $currency_rate = 1;
        $to_currency = $base_currency->id;
        if($inv->currency != 0 && $inv->currency != $base_currency->id){
            $inv_currency = pur_get_currency_by_id($inv->currency);
            $currency_rate = pur_get_currency_rate($base_currency->name, $inv_currency->name);;
            $to_currency = $inv->currency;
        }

        $discount_type = 'after_tax';
        $discount_total = 0;
        if($inv){
            $discount_type = $inv->discount_type;
            $discount_total = $inv->discount_total;
        }

        if($inv && isset($inv->items)){
            if(count($inv->items) > 0){
                $index_request = 0;
                foreach($inv->items as $key => $item){
                    $index_request++;

                    $item_taxes = get_invoice_item_taxes($item['id']); 

                    $tax = '';
                    $tax_value = 0;
                    $tax_name = [];

                    $tax_rate = '';

                    if(count($item_taxes) > 0){
                        foreach($item_taxes as $key => $_tax){
                            if(($key + 1) < count($item_taxes) ){
                                $tax .= $this->purchase_model->get_tax_by_tax_name($_tax['taxname']).'|';
                                
                                $tax_rate .= $_tax['taxrate'].'|';
                            }else{
                                $tax .= $this->purchase_model->get_tax_by_tax_name($_tax['taxname']);
                               
                                $tax_rate .= $_tax['taxrate'];
                            }
                            $tax_name[] = $_tax['taxname'];

                            $tax_value += $item['qty'] * $item['rate'] * $_tax['taxrate'] / 100;
                        }
                    }

                    $item_code = get_item_id_by_des($item['description']);
                    $item_text = $item['description'];
                    $unit_price = $item['rate'];
                    $unit_name = $item['unit'];
                    $into_money = (float) ($item['rate'] * $item['qty']);
                    $total = $tax_value + $into_money;

                    $list_item .= $this->purchase_model->create_purchase_order_row_template('newitems[' . $index_request . ']', $item_text, $item['long_description'], $item['qty'], $unit_name, $unit_price, $tax_name, $item_code, '', $tax_rate, $total, '', '', $total, $into_money, $tax, $tax_value, $index_request, false, $currency_rate, $to_currency);
                }
            }
        }


        echo json_encode([
            'discount_type' => $discount_type,
            'discount_total' => $discount_total,
            'list_item' => $list_item,
            'currency' => $to_currency,
            'currency_rate' => $currency_rate,
        ]);
    }

        /**
     * { table vendor }
     */
    public function dashboard_po_table()
    {
        $this->app->get_table_data(module_views_path('purchase', 'dashboard_po_table'));
    }

    /**
     * Compares the quote pur request.
     *
     * @param        $pur_request  The pur request
     */
    public function compare_quote_pur_request($pur_request){
        if($this->input->post()){
            $data = $this->input->post();
            $success = $this->purchase_model->update_compare_quote($pur_request, $data);
            if($success){
                set_alert('success', _l('updated_successfully'));
            }
            redirect(admin_url('purchase/view_pur_request/'. $pur_request));
        }
    }

    /**
     * { debit notes }
     *
     * @param      string  $id     The identifier
     */
    public function debit_notes($id = ''){
        if (!has_permission('purchase_debit_notes', '', 'view') && !is_admin() && !has_permission('purchase_debit_notes', '', 'view_own')) {
            access_denied('debit_notes');
        }

        close_setup_menu();

        $data['years']          = $this->purchase_model->get_debits_years();
        $data['statuses']       = $this->purchase_model->get_debit_note_statuses();
        $data['debit_note_id'] = $id;
        $data['title']          = _l('pur_debit_note');
        $this->load->view('debit_notes/manage', $data);
    }

    /**
     * { debit notes table }
     */
    public function debit_notes_table(){
        $this->app->get_table_data(module_views_path('purchase', 'debit_notes/table_debit_notes'));
    }


    /**
     * { debit note }
     *
     * @param      string  $id     The identifier
     */
    public function debit_note($id = ''){
        if (!has_permission('purchase_debit_notes', '', 'view') && !is_admin() && !has_permission('purchase_debit_notes', '', 'view_own')) {
            access_denied('debit_notes');
        }
        if ($this->input->post()) {
            $debit_note_data = $this->input->post();
            $debit_note_data['terms'] = $this->input->post('terms', false);
            if ($id == '') {
                if (!has_permission('purchase_debit_notes', '', 'create')) {
                    access_denied('debit_notes');
                }
                $id = $this->purchase_model->add_debit_note($debit_note_data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('debit_note')));
                    redirect(admin_url('purchase/debit_notes/' . $id));
                }
            } else {
                if (!has_permission('purchase_debit_notes', '', 'edit')) {
                    access_denied('debit_notes');
                }
                $success = $this->purchase_model->update_debit_note($debit_note_data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('debit_note')));
                }
                redirect(admin_url('purchase/debit_notes/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('debit_note'));
        } else {
            $debit_note = $this->purchase_model->get_debit_note($id);

            if (!has_permission('purchase_debit_notes', '', 'edit')) {
                access_denied('debit_notes');
            }

            if (!$debit_note || (!has_permission('purchase_debit_notes', '', 'view') && !has_permission('purchase_debit_notes', '', 'view_own') && $debit_note->addedfrom != get_staff_user_id() && !in_array($debit_note->vendorid, get_vendor_admin_list()))) {
                blank_page(_l('credit_note_not_found'), 'danger');
            }

            $data['debit_note'] = $debit_note;
            $data['edit']        = true;
            $title               = _l('edit', _l('debit_note')) . ' - ' . format_debit_note_number($debit_note->id);
        }

        if ($this->input->get('customer_id')) {
            $data['customer_id'] = $this->input->get('customer_id');
        }

        $this->load->model('taxes_model');
        $data['taxes'] = $this->taxes_model->get();
        $this->load->model('invoice_items_model');

       
        $data['vendors'] = $this->purchase_model->get_vendor();


        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();

        $data['base_currency'] = $this->currencies_model->get_base_currency();

        $data['ajaxItems'] = false;
        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->purchase_model->pur_get_grouped('can_be_purchased');
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }

        $data['title']     = $title;
        $data['bodyclass'] = 'credit-note';
        $this->load->view('debit_notes/debit_note', $data);
    }

    /**
     * { validate number }
     */
    public function validate_debit_note_number()
    {
        $isedit          = $this->input->post('isedit');
        $number          = $this->input->post('number');
        $date            = $this->input->post('date');
        $original_number = $this->input->post('original_number');
        $number          = trim($number);
        $number          = ltrim($number, '0');
        if ($isedit == 'true') { 
            if ($number == $original_number) {
                echo json_encode(true);
                die;
            }
        }
        if (total_rows(db_prefix() . 'pur_debit_notes', [
            'YEAR(date)' => date('Y', strtotime(to_sql_date($date))),
            'number' => $number,
        ]) > 0) {
            echo 'false';
        } else {
            echo 'true';
        }
    }

    /**
     * { vendor change data }
     *
     * @param        $vendor  The vendor
     */
    public function vendor_change_data($vendor){
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');
            $data                     = [];
            $data['billing_shipping'] = $this->purchase_model->get_vendor_billing_and_shipping_details($vendor);
            $data['vendor_currency']  = get_vendor_currency($vendor);

            $option_html = '';

            if(total_rows(db_prefix().'pur_vendor_items', ['vendor' => $vendor]) <= ajax_on_total_items()){
                $items = $this->purchase_model->get_items_by_vendor_variation($vendor);
                $option_html .= '<option value=""></option>';
                foreach($items as $item){
                    $option_html .= '<option value="'.$item['id'].'" >'.$item['label'].'</option>';
                }
            }

            $data['option_html'] = $option_html;
            
            echo json_encode($data);
        }
    }

    /**
     * Gets the debit note data ajax.
     *
     * @param        $id     The identifier
     */
    public function get_debit_note_data_ajax($id)
    {
        if (!has_permission('purchase_debit_notes', '', 'view') && !is_admin() && !has_permission('purchase_debit_notes', '', 'view_own')) {
            echo _l('access_denied');
            die;
        }

        if (!$id) {
            die(_l('debit_note_not_found'));
        }

        $debit_note = $this->purchase_model->get_debit_note($id);

        if (!$debit_note || (!has_permission('purchase_debit_notes', '', 'view') && !has_permission('purchase_debit_notes', '', 'view_own') )) {
            echo _l('debit_note_not_found');
            die;
        }

        if(has_permission('purchase_debit_notes', '', 'view_own') && !is_admin()){
            if($debit_note->addedfrom != get_staff_user_id() && !in_array($debit_note->vendorid, get_vendor_admin_list(get_staff_user_id()))){
                echo _l('access_denied');
                die;
            }
        }

        $data['vendor_contacts'] = $this->purchase_model->get_contacts($debit_note->vendorid);
        $data['debit_note']                   = $debit_note;
        $data['members']                       = $this->staff_model->get('', ['active' => 1]);
        $data['available_debitable_invoices'] = $this->purchase_model->get_available_debitable_invoices($id);

        $this->load->view('debit_notes/debit_note_preview_template', $data);
    }

    /**
     * { delete debit note }
     */
    public function delete_debit_note($id){
        if (!has_permission('purchase_debit_notes', '', 'delete')) {
            access_denied('debit_notes');
        }

        if (!$id) {
            redirect(admin_url('debit_notes'));
        }

        $debit_note = $this->purchase_model->get_debit_note($id);

        if ($debit_note->debit_used || $debit_note->status == 2) {
            $success = false;
        } else {
            $success = $this->purchase_model->delete_debit_note($id);
        }

        if ($success) {
            set_alert('success', _l('deleted', _l('debit_note')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('debit_note_lowercase')));
        }

        redirect(admin_url('purchase/debit_notes'));
    }

    /**
     * { apply debits to invoices }
     *
     * @param        $debit_note_id  The debit note identifier
     */
    public function apply_debits_to_invoices($debit_note_id)
    {
        $debitApplied = false;
        if ($this->input->post()) {
            foreach ($this->input->post('amount') as $invoice_id => $amount) {
                if ($this->purchase_model->apply_debits($debit_note_id, ['amount' => $amount, 'invoice_id' => $invoice_id])) {
                    $this->purchase_model->update_pur_invoice_status($invoice_id);
                    $debitApplied = true;
                }
            }
        }
        if ($debitApplied) {
            set_alert('success', _l('debits_successfully_applied_to_invoices'));
        }
        redirect(admin_url('purchase/debit_notes/' . $credit_note_id));
    }

    /**
     * { delete debit note applied debit }
     *
     * @param        $id          The identifier
     * @param        $debit_id   The debit identifier
     * @param        $invoice_id  The invoice identifier
     */
    public function delete_debit_note_applied_debit($id, $debit_id, $invoice_id)
    {
        if (has_permission('purchase_debit_notes', '', 'delete')) {
            $this->purchase_model->delete_applied_debit($id, $debit_id, $invoice_id);
        }
        redirect(admin_url('purchase/debit_notes/' . $debit_id));
    }

    /**
     * { mark debit note open }
     *
     * @param        $id     The identifier
     */
    public function mark_debit_note_open($id)
    {
        if (total_rows(db_prefix() . 'pur_debit_notes', ['status' => 3, 'id' => $id]) > 0 && has_permission('purchase_debit_notes', '', 'edit')) {
            $this->purchase_model->mark_debit_note($id, 1);
        }

        redirect(admin_url('purchase/debit_notes/' . $id));
    }

    /**
     * { mark debit note void }
     *
     * @param        $id     The identifier
     */
    public function mark_debit_note_void($id)
    {
        $debit_note = $this->purchase_model->get_debit_note($id);
        if ($debit_note->status != 2 && $debit_note->status != 3 && !$debit_note->debits_used && has_permission('purchase_debit_notes', '', 'edit')) {
            $this->purchase_model->mark_debit_note($id, 3);
        }
        redirect(admin_url('purchase/debit_notes/' . $id));
    }

    /**
     * { refund }
     *
     * @param        $id         The identifier
     * @param        $refund_id  The refund identifier
     */
    public function refund_debit_note($id, $refund_id = null)
    {
        if (has_permission('purchase_debit_notes', '', 'edit')) {
            $this->load->model('payment_modes_model');
            if (!$refund_id) {
                $data['payment_modes'] = $this->payment_modes_model->get('', [
                    'expenses_only !=' => 1,
                ]);
            } else {
                $data['refund']        = $this->purchase_model->get_refund($refund_id);
                $data['payment_modes'] = $this->payment_modes_model->get('', [], true, true);
                $i                     = 0;
                foreach ($data['payment_modes'] as $mode) {
                    if ($mode['active'] == 0 && $data['refund']->payment_mode != $mode['id']) {
                        unset($data['payment_modes'][$i]);
                    }
                    $i++;
                }
            }

            $data['debit_note'] = $this->purchase_model->get_debit_note($id);
            $this->load->view('debit_notes/refund', $data);
        }
    }

    /**
     * Creates a refund.
     *
     * @param        $debit_note_id  The debit note identifier
     */
    public function create_refund($debit_note_id)
    {
        if (has_permission('purchase_debit_notes', '', 'edit')) {
            $data                = $this->input->post();
            $data['refunded_on'] = to_sql_date($data['refunded_on']);
            $data['staff_id']    = get_staff_user_id();
            $success             = $this->purchase_model->create_refund($debit_note_id, $data);

            if ($success) {
                set_alert('success', _l('added_successfully', _l('refund')));
            }
        }

        redirect(admin_url('purchase/debit_notes/' . $debit_note_id));
    }

    /**
     * { edit refund }
     *
     * @param        $refund_id      The refund identifier
     * @param        $debit_note_id  The debit note identifier
     */
    public function edit_refund($refund_id, $debit_note_id)
    {
        if (has_permission('purchase_debit_notes', '', 'edit')) {
            $data                = $this->input->post();
            $data['refunded_on'] = to_sql_date($data['refunded_on']);
            $success             = $this->purchase_model->edit_refund($refund_id, $data);

            if ($success) {
                set_alert('success', _l('updated_successfully', _l('refund')));
            }
        }

        redirect(admin_url('purchase/debit_notes/' . $debit_note_id));
    }

    /**
     * { delete refund }
     *
     * @param        $refund_id       The refund identifier
     * @param        $credit_note_id  The credit note identifier
     */
    public function delete_debit_refund($refund_id, $debit_note_id)
    {
        if (has_permission('purchase_debit_notes', '', 'delete')) {
            $success = $this->purchase_model->delete_refund($refund_id, $debit_note_id);
            if ($success) {
                set_alert('success', _l('deleted', _l('refund')));
            }
        }
        redirect(admin_url('purchase/debit_notes/' . $debit_note_id));
    }

    /**
     * { delete attachment }
     *
     * @param        $id     The identifier
     */
    public function delete_debit_attachment($id)
    {
        $this->load->model('misc_model');
        $file = $this->misc_model->get_file($id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo $this->purchase_model->delete_attachment($id);
        } else {
            ajax_access_denied();
        }
    }

    /* Generates credit note PDF and send to email */
    public function debit_note_pdf($id)
    {
        
        if (!$id) {
            redirect(admin_url('purchase/debit_notes'));
        }
        $debit_note        = $this->purchase_model->get_debit_note($id);
        $debit_note_number = format_debit_note_number($debit_note->id);

        try {
            $pdf = debit_note_pdf($debit_note);
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== false) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }

        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf->Output(mb_strtoupper(slug_it($debit_note_number)) . '.pdf', $type);
    }

    /**
     * Sends a purchase order.
     * @return redirect
     */
    public function send_debit_note(){
        if($this->input->post()){
            $data = $this->input->post();
            $data['content'] = $this->input->post('content', false);
            $send = $this->purchase_model->send_debit_note($data);
            if($send){
                set_alert('success',_l('send_debit_note_successfully'));
                
            }else{
                set_alert('warning',_l('send_debit_note_fail'));
            }
            redirect(admin_url('purchase/debit_notes/'.$data['debit_note_id']));
            
        }
    }

    /**
     * { apply debits }
     *
     * @param        $invoice_id  The invoice identifier
     */
    public function apply_debits($invoice_id)
    {
        $total_debits_applied = 0;
        foreach ($this->input->post('amount') as $debit_id => $amount) {
            $success = $this->purchase_model->apply_debits($debit_id, [
            'invoice_id' => $invoice_id,
            'amount'     => $amount,
        ]);
            if ($success) {
                $total_debits_applied++;
            }
        }

        if ($total_debits_applied > 0) {
            $this->purchase_model->update_pur_invoice_status($invoice_id);
            set_alert('success', _l('invoice_credits_applied'));
        }
        redirect(admin_url('purchase/purchase_invoice/' . $invoice_id));
    }

    /**
     * { delete invoice applied debit }
     *
     * @param        $id          The identifier
     * @param        $debit_id    The debit identifier
     * @param        $invoice_id  The invoice identifier
     */
    public function delete_invoice_applied_debit($id, $debit_id, $invoice_id)
    {
        if (has_permission('purchase_debit_notes', '', 'delete')) {
            $this->purchase_model->delete_applied_debit($id, $debit_id, $invoice_id);
        }
        redirect(admin_url('purchase/purchase_invoice/' . $invoice_id));
    }

     /**
     * { table vendor pur order }
     *
     * @param      <type>  $vendor  The vendor
     */
    public function table_vendor_pur_invoices($vendor){
        $this->app->get_table_data(module_views_path('purchase', 'invoices/table_pur_invoices'),['vendor' => $vendor]);
    }

     /**
     * { table vendor debit notes }
     *
     * @param      <type>  $vendor  The vendor
     */
    public function table_vendor_debit_notes($vendor){
        $this->app->get_table_data(module_views_path('purchase', 'debit_notes/table_debit_notes'),['vendor' => $vendor]);
    }

    /**
     * { statement }
     */
    public function statement()
    {
        if (!has_permission('purchase_vendors', '', 'view') && !is_admin()) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad error');
            echo _l('access_denied');
            die;
        }

        $vendor_id = $this->input->get('vendor_id');
        $from        = $this->input->get('from');
        $to          = $this->input->get('to');

        $data['statement'] = $this->purchase_model->get_statement($vendor_id, to_sql_date($from), to_sql_date($to));

        $data['from'] = $from;
        $data['to']   = $to;

        $viewData['html'] = $this->load->view('vendors/groups/_statement', $data, true);

        echo json_encode($viewData);
    }

    /**
     * { statement pdf }
     */
    public function statement_pdf(){
        $vendor_id = $this->input->get('vendor_id');

        if (!has_permission('purchase_vendors', '', 'view') && !is_admin()) {
            set_alert('danger', _l('access_denied'));
            redirect(admin_url('purchase/vendor/' . $vendor_id));
        }

        $from = $this->input->get('from');
        $to   = $this->input->get('to');

        $data['statement'] = $this->purchase_model->get_statement($vendor_id, to_sql_date($from), to_sql_date($to));

        try {
            $pdf = purchase_statement_pdf($data['statement']);
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== false) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }

        $type = 'D';
        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf->Output(slug_it(_l('vendor_statement') . '-' . $data['statement']['client']->company) . '.pdf', $type);
    }

    /**
     * Sends a purchase statment.
     * @return redirect
     */
    public function send_statement(){
        $vendor_id = $this->input->get('vendor_id');

        if (!has_permission('purchase_vendors', '', 'view')) {
            set_alert('danger', _l('access_denied'));
            redirect(admin_url('purchase/vendor/' . $vendor_id));
        }

        $data = $this->input->post();

        $from = $this->input->get('from');
        $to   = $this->input->get('to');

        $data['from'] = $from;
        $data['to'] = $to;

        $data['content'] = $this->input->post('content', false);
        $data['vendor_id'] = $vendor_id;
        $success = $this->purchase_model->send_statement_to_email($data);

        if ($success) {
            set_alert('success', _l('statement_sent_to_vendor_success'));
        } else {
            set_alert('danger', _l('statement_sent_to_vendor_fail'));
        }

        redirect(admin_url('purchase/vendor/' . $vendor_id . '?group=purchase_statement'));
    }

    /**
     * permission modal
     * @return [type] 
     */
    public function permission_modal()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $this->load->model('staff_model');

        if ($this->input->post('slug') === 'update') {
            $staff_id = $this->input->post('staff_id');
            $role_id = $this->input->post('role_id');

            $data = [ 'funcData' => ['staff_id'=> isset($staff_id) ? $staff_id : null ] ];

            if(isset($staff_id)) {
                $data['member']  = $this->staff_model->get($staff_id);
            }

            $data['roles_value']         = $this->roles_model->get();
            $data['staffs']  = purchase_get_staff_id_dont_permissions();
            $add_new = $this->input->post('add_new');

            if($add_new == ' hide'){
                $data['add_new']        = ' hide';
                $data['display_staff']  = '';
            }else{
                $data['add_new'] = '';
                $data['display_staff']  = ' hide';
            }


            $this->load->view('includes/permissions_modal', $data);
        }
    }

    public function permission_table()
    {
        if ($this->input->is_ajax_request()) {

            $select = [
                'staffid',
                'CONCAT(firstname," ",lastname) as full_name',
                'firstname', //for role name
                'email',
                'phonenumber',
            ];
            $where = [];
            $where[] = 'AND '.db_prefix().'staff.admin != 1';

            $arr_staff_id = purchase_get_staff_id_permissions();

            if(count($arr_staff_id) > 0){
                $where[] = 'AND '.db_prefix().'staff.staffid IN (' . implode(', ', $arr_staff_id) . ')';
            }else{
                $where[] = 'AND '.db_prefix().'staff.staffid IN ("")';
            }

            $aColumns     = $select;
            $sIndexColumn = 'staffid';
            $sTable       = db_prefix() . 'staff';
            $join         = ['LEFT JOIN ' . db_prefix() . 'roles ON ' . db_prefix() . 'roles.roleid = ' . db_prefix() . 'staff.role'];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [ db_prefix() . 'roles.name as role_name', db_prefix() . 'staff.role']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $not_hide = '';

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = '<a href="' . admin_url('staff/member/' . $aRow['staffid']) . '">' . $aRow['full_name']  . '</a>';

                $row[] = $aRow['role_name'];
                $row[] = $aRow['email'];
                $row[] = $aRow['phonenumber'];

                $options ='';

                if(is_admin() || has_permission('purchase_settings', '', 'edit')){
                    $options = icon_btn('#', 'fa fa-pencil-square', 'btn-default', [
                        'title'   => _l('edit'),
                        'onclick' => 'permissions_update(' . $aRow['staffid'] . ', '.$aRow['role'].', '.$not_hide.'); return false;',
                    ]);
                }

                if(is_admin() || has_permission('purchase_settings', '', 'edit')){
                    $options .= icon_btn('purchase/delete_purchase_permission/' . $aRow['staffid'], 'fa fa-remove', 'btn-danger _delete', ['title' => _l('delete')]);
                }

                $row[] = $options;

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * staff id changed
     * @param  [type] $staff_id 
     * @return [type]           
     */
    public function staff_id_changed($staff_id)
    {   
        $role_id = '';
        $status = 'false';
        $r_permission=[];

        $staff  = $this->staff_model->get($staff_id);

        if($staff){
            if(count($staff->permissions) > 0){
                foreach ($staff->permissions as $permission) {
                    $r_permission[$permission['feature']][] = $permission['capability'];
                }
            }

            $role_id = $staff->role;
            $status = 'true';

        }

        if(count($r_permission) > 0){
            $data=['role_id'   => $role_id, 'status'    => $status, 'permission' => 'true', 'r_permission' => $r_permission];
        }else{
            $data=['role_id'   => $role_id, 'status'    => $status, 'permission' => 'false', 'r_permission' => $r_permission];
        }

        echo json_encode($data); 
        die;
    }


    /**
     * purchase update permissions
     * @param  string $id 
     * @return [type]     
     */
    public function purchase_update_permissions($id = '')
    {
        if (!is_admin() && !has_permission('purchase_settings', '', 'edit')) {
            access_denied('purchase');
        }
        $data = $this->input->post();

        if(!isset($id) || $id == ''){
            $id   = $data['staff_id'];
        }


        if(isset($id) && $id != ''){

            $data = hooks()->apply_filters('before_update_staff_member', $data, $id);

            if (is_admin()) {
                if (isset($data['administrator'])) {
                    $data['admin'] = 1;
                    unset($data['administrator']);
                } else {
                    if ($id != get_staff_user_id()) {
                        if ($id == 1) {
                            return [
                                'cant_remove_main_admin' => true,
                            ];
                        }
                    } else {
                        return [
                            'cant_remove_yourself_from_admin' => true,
                        ];
                    }
                    $data['admin'] = 0;
                }
            }

            $this->db->where('staffid', $id);
            $this->db->update(db_prefix() . 'staff', [
                'role'  => $data['role']
            ]);

            $response = $this->staff_model->update_permissions((isset($data['admin']) && $data['admin'] == 1 ? [] : $data['permissions']), $id);
        }else{
            $this->load->model('roles_model');

            $role_id = $data['role'];
            unset($data['role']);
            unset($data['staff_id']);

            $data['update_staff_permissions'] = true;

            $response = $this->roles_model->update($data, $role_id);
        }

        if (is_array($response)) {
            if (isset($response['cant_remove_main_admin'])) {
                set_alert('warning', _l('staff_cant_remove_main_admin'));
            } elseif (isset($response['cant_remove_yourself_from_admin'])) {
                set_alert('warning', _l('staff_cant_remove_yourself_from_admin'));
            }
        } elseif ($response == true) {
            set_alert('success', _l('updated_successfully', _l('staff_member')));
        }
        redirect(admin_url('purchase/setting?group=permissions'));

    }


    /**
     * delete purchase permission
     * @param  [type] $id 
     * @return [type]     
     */
    public function delete_purchase_permission($id)
    {
        if(!is_admin() && !has_permission('purchase_settings', '', 'edit')) {
            access_denied('purchase');
        }

        $response = $this->purchase_model->delete_hr_profile_permission($id);

        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('pur_is_referenced', _l('permissions')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('permissions')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('permissions')));
        }
        redirect(admin_url('purchase/setting?group=permissions'));

    }

    /**
     * { update customfield invoice }
     *
     * @param        $id     The identifier
     */
    public function update_customfield_invoice($id){
        if($this->input->post()){
            $data = $this->input->post();
            $success = $this->purchase_model->update_customfield_invoice($id,$data);
            if($success){
                $message = _l('updated_successfully');
                set_alert('success', $message);
            }
            redirect(admin_url('purchase/purchase_invoice/'.$id));
        }
    }

    /**
     * { refresh order value }
     */
    public function refresh_order_value($po_id){
        $success = false;
        if($po_id != ''){
            $success = $this->purchase_model->refresh_order_value($po_id);
        }

        echo json_encode([
            'success' => $success,
        ]);
    }

    /**
     * Gets the purchase request row template.
     */
    public function get_purchase_request_row_template(){
        $name = $this->input->post('name');
        $item_text = $this->input->post('item_text');
        $unit_price = $this->input->post('unit_price');
        $quantity = $this->input->post('quantity');
        $unit_name = $this->input->post('unit_name');
        $unit_id = $this->input->post('unit_id');
        $into_money = $this->input->post('into_money');
        $item_key = $this->input->post('item_key');
        $tax_value = $this->input->post('tax_value');
        $tax_name = $this->input->post('taxname');
        $total = $this->input->post('total');
        $item_code = $this->input->post('item_code');
        $currency_rate = $this->input->post('currency_rate');
        $to_currency = $this->input->post('to_currency');
        
        echo $this->purchase_model->create_purchase_request_row_template( $name, $item_code, $item_text, $unit_price, $quantity, $unit_name, $unit_id, $into_money, $item_key, $tax_value, $total, $tax_name, '', '', false, $currency_rate, $to_currency);
    }

    /**
     * wh commodity code search
     * @return [type] 
     */
    public function pur_commodity_code_search($type = 'purchase_price', $can_be = 'can_be_purchased', $vendor = '')
    {
        if ($this->input->post() && $this->input->is_ajax_request()) {
            echo json_encode($this->purchase_model->pur_commodity_code_search($this->input->post('q'), $type, $can_be, false, $vendor));
        }
    }

    /**
     * wh commodity code search
     * @return [type] 
     */
    public function pur_commodity_code_search_vendor_item($type = 'purchase_price', $can_be = 'can_be_purchased', $group = '')
    {
        if ($this->input->post() && $this->input->is_ajax_request()) {
            echo json_encode($this->purchase_model->pur_commodity_code_search($this->input->post('q'), $type, $can_be, false, '', $group));
        }
    }

    /**
     * Gets the item by identifier.
     *
     * @param          $id             The identifier
     * @param      bool|int  $get_warehouse  The get warehouse
     * @param      bool      $warehouse_id   The warehouse identifier
     */
    public function get_item_by_id($id, $currency_rate = 1)
    {
        if ($this->input->is_ajax_request()) {
            $item                     = $this->purchase_model->get_item_v2($id);
            $item->long_description   = nl2br($item->long_description ?? '');

            if($currency_rate != 1){
                $item->purchase_price = round(($item->purchase_price*$currency_rate), 2);
            }
            
            $html = '<option value=""></option>';
           
            $item->warehouses_html = $html;

            echo json_encode($item);
        }
    }

    /**
     * Gets the quotation row template.
     */
    public function get_quotation_row_template(){
        $name = $this->input->post('name');
        $item_name = $this->input->post('item_name');
        $quantity = $this->input->post('quantity');
        $unit_name = $this->input->post('unit_name');
        $unit_price = $this->input->post('unit_price');
        $taxname = $this->input->post('taxname');
        $item_code = $this->input->post('item_code');
        $unit_id = $this->input->post('unit_id');
        $tax_rate = $this->input->post('tax_rate');
        $discount = $this->input->post('discount');
        $item_key = $this->input->post('item_key');
        $currency_rate = $this->input->post('currency_rate');
        $to_currency = $this->input->post('to_currency');

        echo $this->purchase_model->create_quotation_row_template($name, $item_name, $quantity, $unit_name, $unit_price, $taxname, $item_code, $unit_id, $tax_rate, '', $discount, '', '', '', '', '', $item_key, false, $currency_rate, $to_currency);
    }

    /**
     * Gets the purchase order row template.
     */
    public function get_purchase_order_row_template(){
        $name = $this->input->post('name');
        $item_name = $this->input->post('item_name');
        $item_description = $this->input->post('item_description');
        $quantity = $this->input->post('quantity');
        $unit_name = $this->input->post('unit_name');
        $unit_price = $this->input->post('unit_price');
        $taxname = $this->input->post('taxname');
        $item_code = $this->input->post('item_code');
        $unit_id = $this->input->post('unit_id');
        $tax_rate = $this->input->post('tax_rate');
        $discount = $this->input->post('discount');
        $item_key = $this->input->post('item_key');
        $currency_rate = $this->input->post('currency_rate');
        $to_currency = $this->input->post('to_currency');

        echo $this->purchase_model->create_purchase_order_row_template($name, $item_name, $item_description, $quantity, $unit_name, $unit_price, $taxname, $item_code, $unit_id, $tax_rate, '', $discount, '', '', '', '', '', $item_key, false, $currency_rate, $to_currency);
    }

    /**
     * currency rate table
     * @return [type] 
     */
    public function currency_rate_table(){
        $this->app->get_table_data(module_views_path('purchase', 'includes/currencies/currency_rate_table'));
    }

    /**
     * update automatic conversion
     */
    public function update_setting_currency_rate(){
        $data = $this->input->post();
        $success = $this->purchase_model->update_setting_currency_rate($data);
        if($success == true){
            $message = _l('updated_successfully', _l('setting'));
            set_alert('success', $message);
        }
        redirect(admin_url('purchase/setting?group=currency_rates'));
    }

    /**
     * Gets all currency rate online.
     */
    public function get_all_currency_rate_online()
    {
        $result = $this->purchase_model->get_all_currency_rate_online();
        if($result){
            set_alert('success', _l('updated_successfully', _l('pur_currency_rates')));
        }
        else{
            set_alert('warning', _l('no_data_changes', _l('pur_currency_rates')));                  
        }

        redirect(admin_url('purchase/setting?group=currency_rates'));
    }

    /**
     * update currency rate
     * @return [type] 
     */
    public function update_currency_rate($id)
    {
        if($this->input->post()){
            $data = $this->input->post();

            $result =  $this->purchase_model->update_currency_rate($data, $id);
            if($result){
                set_alert('success', _l('updated_successfully', _l('pur_currency_rates')));
            }
            else{
                set_alert('warning', _l('no_data_changes', _l('pur_currency_rates')));                  
            }
        }

        redirect(admin_url('purchase/setting?group=currency_rates'));
    }

    /**
     * Gets the currency rate online.
     *
     * @param        $id     The identifier
     */
    public function get_currency_rate_online($id)
    {
            $result =  $this->purchase_model->get_currency_rate_online($id);
            echo json_encode(['value' => $result]);
            die;
    }


    /**
     * delete currency
     * @param  [type] $id 
     * @return [type]     
     */
    public function delete_currency_rate($id){
        if($id != ''){
            $result =  $this->purchase_model->delete_currency_rate($id);
            if($result){
                set_alert('success', _l('deleted_successfully', _l('pur_currency_rates')));
            }
            else{
                set_alert('danger', _l('deleted_failure', _l('pur_currency_rates')));                   
            }
        }
        redirect(admin_url('purchase/setting?group=currency_rates'));
    }

    /**
     * currency rate modal
     * @return [type] 
     */
    public function currency_rate_modal()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $id=$this->input->post('id');

        $data=[];
        $data['currency_rate'] = $this->purchase_model->get_currency_rate($id);

        $this->load->view('includes/currencies/currency_rate_modal', $data);
    }

    /**
     * currency rate table
     * @return [type] 
     */
    public function currency_rate_logs_table(){
        $this->app->get_table_data(module_views_path('purchase', 'includes/currencies/currency_rate_logs_table'));
    }

    /**
     * { pur inv payment change }
     *
     * @param        $invoice_id  The invoice identifier
     */
    public function pur_inv_payment_change($invoice_id){
        $amount = purinvoice_left_to_pay($invoice_id);

        echo json_encode([
            'amount' => $amount,
        ]);
    }

     /**
     * Adds a payment on PO.
     *
     * @param      <type>  $pur_order  The purchase order id
     * @return  redirect
     */
    public function add_payment_on_po_with_inv($pur_order){
         if ($this->input->post()) {
            $data = $this->input->post();
            $message = '';
            $success = $this->purchase_model->add_payment_on_po_with_inv($data);
            if ($success) {
                $message = _l('added_successfully', _l('payment'));
            }
            set_alert('success', $message);
            redirect(admin_url('purchase/purchase_order/'.$pur_order));
        }
    }


    /**
     * { table vendor pur order }
     *
     * @param      <type>  $vendor  The vendor
     */
    public function table_project_pur_order($project_id){
        $this->app->get_table_data(module_views_path('purchase', 'purchase_order/table_pur_order'),['project' => $project_id]);
    }

    /**
     * Gets the project information.
     */
    public function get_project_info($pur_order){
        $po = $this->purchase_model->get_pur_order($pur_order);

        $this->load->model('projects_model');

        $base_currency = get_base_currency();

        $currency = $base_currency->id;

        $project_id = '';
        $customer = '';
        if($po->project != 0){
            $project = $this->projects_model->get($po->project);

            if($project){
                $project_id = $po->project;
                $customer = $project->clientid;
            }
        }

        if($po->currency != 0){
            $currency = $po->currency;
        }

        echo json_encode([
            'project_id' => $project_id,
            'customer' => $customer,
            'currency' => $currency,
            'vendor' => $po->vendor
        ]);
    }

    /**
     * Returns orders.
     */
    public function order_returns($id = ''){
        $data['title'] = _l('pur_return_orders');

        $data['delivery_id'] = $id;

        $data['from_date'] = _d(date('Y-m-d', strtotime( date('Y-m-d') . "-15 day")));
        $data['to_date'] = _d(date('Y-m-d'));

        $data['vendors'] = $this->purchase_model->get_vendor();

        //display packing list not yet approval
        $data['rel_type'] = 'purchase_return_order';

        $this->load->view('return_orders/manage', $data);
    }

    /**
     * table manage packing list
     * @return [type] 
     */
    public function table_manage_order_return()
    {
        $this->app->get_table_data(module_views_path('purchase', 'return_orders/table_order_return'));
    }

    /**
     * save and send request send mail
     * @return [type] 
     */
    public function save_and_send_request_send_mail($data ='') {
        if ((isset($data)) && $data != '') {
            $this->purchase_model->send_mail($data);

            $success = 'success';
            echo json_encode([
                'success' => $success,
            ]);
        }
    }

    /**
     * { order return }
     */
    public function order_return($order_return_type = 'purchasing_return_order', $id = ''){
        $this->load->model('clients_model');

        if($id == ''){
            $data['title'] = _l('add_new_order_return');
        }else{
            $data['title'] = _l('update_order_return');
        }

        if ($this->input->post()) {
            $message = '';
            $data = $this->input->post();

            if (!$this->input->post('id')) {
               
                $mess = $this->purchase_model->add_order_return($data, $data['rel_type']);
                
                if ($mess) {
                    if($data['save_and_send_request'] == 'true'){
                        $this->save_and_send_request_send_mail(['rel_id' => $mess, 'rel_type' => 'order_return', 'addedfrom' => get_staff_user_id()]);
                    }
                    set_alert('success', _l('added_successfully'));
                } else {
                    set_alert('warning', _l('pur_add_order_return_failed'));
                }

                redirect(admin_url('purchase/order_returns/'.$mess));

            }else{
                $id = $this->input->post('id');

                $mess = $this->purchase_model->update_order_return($data, $data['rel_type'], $id);
                
                if($data['save_and_send_request'] == 'true'){
                    $this->save_and_send_request_send_mail(['rel_id' => $id, 'rel_type' => 'order_return', 'addedfrom' => get_staff_user_id()]);
                }

                if ($mess) {
                    set_alert('success', _l('updated_successfully'));
                } else {
                    set_alert('warning', _l('pur_update_order_return_failed'));
                }
                redirect(admin_url('purchase/order_returns/'.$id));
            }

        }

        $data['ajaxItems'] = false;
        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->purchase_model->pur_get_grouped('can_be_purchased');
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }

        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();

        $data['order_return_name_ex'] = 'ORDER_RETURN' . date('YmdHi');
        $data['clients'] = $this->clients_model->get();

        $order_return_row_template = $this->purchase_model->create_order_return_row_template('purchasing_return_order');

        if($id != ''){

            $order_return = $this->purchase_model->get_order_return($id);
            if (!$order_return) {
                blank_page('Order Return Not Found', 'danger');
            }
            $data['order_return_detail'] = $this->purchase_model->get_order_return_detail($id);
            $data['order_return'] = $order_return;

            if (count($data['order_return_detail']) > 0) {
                $index_receipt = 0;
                foreach ($data['order_return_detail'] as $order_return_detail) {
                    $index_receipt++;
                    $unit_name = pur_get_unit_name($order_return_detail['unit_id']);
                    $taxname = '';
                    $expiry_date = null;
                    $lot_number = null;
                    $commodity_name = $order_return_detail['commodity_name'];
                    
                    if(strlen($commodity_name) == 0){
                        $commodity_name = pur_get_item_variatiom($order_return_detail['commodity_code']);
                    }

                    $order_return_row_template .= $this->purchase_model->create_order_return_row_template($order_return->rel_type, $order_return_detail['rel_type_detail_id'], 'items[' . $index_receipt . ']', $commodity_name, $order_return_detail['quantity'], $unit_name, $order_return_detail['unit_price'], $taxname, $order_return_detail['commodity_code'], $order_return_detail['unit_id'] , $order_return_detail['tax_rate'], $order_return_detail['total_amount'], $order_return_detail['discount'], $order_return_detail['discount_total'], $order_return_detail['total_after_discount'], $order_return_detail['reason_return'], $order_return_detail['sub_total'],$order_return_detail['tax_name'],$order_return_detail['tax_id'], $order_return_detail['id'], true);
                    
                }
            }
        }

        $data['order_return_row_template'] = $order_return_row_template;

        $this->load->view('return_orders/order_return', $data);
    }

    /**
     * order return get item data
     * @param  string $delivery_id 
     * @return [type]              
     */
    public function order_return_get_item_data($rel_id = 0, $rel_type = '', $return_type = 'fully')
    {
        if ($this->input->is_ajax_request()) {
            $data = [];
            if($rel_type == 'purchasing_return_order' ){
                $data = $this->purchase_model->pur_order_detail_order_return($rel_id, $return_type);
            }
            if($data){
                $po = $this->purchase_model->get_pur_order($rel_id);
                $base_currency = get_base_currency();
                if($po->currency != 0){
                    $base_currency = pur_get_currency_by_id($po->currency);
                }

                $discount_total = 0;
                if($po){
                    $discount_total = $po->discount_total;
                }

                $result = [
                    'discount_total' => $discount_total,
                    'discount_type' => $po->discount_type,
                    'company_id' => $data['company_id'] ? $data['company_id'] : '',
                    'email' => $data['email'] ? $data['email'] : '',
                    'phonenumber' => $data['phonenumber'] ? $data['phonenumber'] : '',
                    'order_number' => $data['order_number'] ? $data['order_number'] : '',
                    'order_date' => $data['order_date'] ? $data['order_date'] : '',
                    'number_of_item' => $data['number_of_item'] ? $data['number_of_item'] : '',
                    'order_total' => $data['order_total'] ? $data['order_total'] : '',
                    'datecreated' => $data['datecreated'] ? $data['datecreated'] : '',
                    'additional_discount' => $data['additional_discount'] ? $data['additional_discount'] : '',
                    'main_additional_discount' => $data['main_additional_discount'] ? $data['main_additional_discount'] : '',
                    'result' => $data['result'] ? $data['result'] : '',
                    'currency' => $base_currency->id,
                    'return_type' => $return_type,
                ];
                echo json_encode($result);
                die;
            }
        }
    }

    /**
     * wh client data
     * @param  [type] $customer_id 
     * @return [type]              
     */
    public function pur_client_data($customer_id, $rel_type)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('clients_model');

            $phonenumber = '';
            $email = '';
            $currency = '';
            $fee_return_order = 0;
            $return_policies_information = '';
            if($rel_type == 'purchasing_return_order'){
                                    $this->load->model('purchase/purchase_model');
                $vendor = $this->purchase_model->get_vendor($customer_id);
                if($vendor){
                    $phonenumber = $vendor->phonenumber;
                    $contacts = $this->purchase_model->get_contacts($customer_id);
                    if(count($contacts) > 0){
                        $email = $contacts[0]['email'];
                    }
                    $currency = $vendor->default_currency;
                    $fee_return_order = $vendor->return_order_fee;
                    $return_policies_information = $vendor->return_policies;
                }
            }

            echo json_encode([
                'fee_return_order' => $fee_return_order,
                'phonenumber' => $phonenumber,
                'email' => $email,
                'currency' => $currency,
                'return_policies_information' => $return_policies_information,
            ]);
        }
    }

    /**
     * view order return
     * @param  [type] $id 
     * @return [type]     
     */
    public function view_order_return($id)
    {
        //approval
        $send_mail_approve = $this->session->userdata("send_mail_approve");
        if ((isset($send_mail_approve)) && $send_mail_approve != '') {
            $data['send_mail_approve'] = $send_mail_approve;
            $this->session->unset_userdata("send_mail_approve");
        }
        $this->load->model('clients_model');

        $data['get_staff_sign'] = $this->purchase_model->get_staff_sign($id, 'order_return');
        $data['check_approve_status'] = $this->purchase_model->check_approval_details($id, 'order_return');
        $data['list_approve_status'] = $this->purchase_model->get_list_approval_details($id, 'order_return');


        //get vaule render dropdown select

        $data['units_code_name'] = $this->purchase_model->get_units_code_name();

        $data['order_return_detail'] = $this->purchase_model->get_order_return_detail($id);
        $data['order_return'] = $this->purchase_model->get_order_return($id);
        $data['order_return_refunds'] = $this->purchase_model->get_order_return_refunds($id);

        $data['activity_log'] = $this->purchase_model->pur_get_activity_log($id,'order_return');

        $data['title'] = _l('pur_order_return');
        $check_appr = $this->purchase_model->get_approve_setting('order_return');
        $data['check_appr'] = $check_appr;
        $data['tax_data'] = $this->purchase_model->get_html_tax_order_return($id);
        $this->load->model('currencies_model');

        $base_currency = $this->currencies_model->get_base_currency();
        if($data['order_return']->currency != 0){
            $base_currency = pur_get_currency_by_id($data['order_return']->currency);
        }

        $data['base_currency'] = $base_currency;

        $this->load->view('return_orders/view_order_return', $data);

    }

    /**
     * order return check before approval
     * @return [type] 
     */
    public function order_return_check_before_approval()
    {
        $data = $this->input->post();
            // packing list
            //check before send request approval
        if( $data['order_rel_type'] == 'manual'){
            echo json_encode([
                'success' => true,
                'message' => '',
            ]);
            die;
        }

    }

    /**
     * add activity
     */
    public function pur_add_activity()
    {
        $goods_delivery_id = $this->input->post('goods_delivery_id');


        if ($this->input->post()) {
            $description = $this->input->post('activity');
            $rel_type = $this->input->post('rel_type');
            $aId     = $this->purchase_model->log_pur_activity($goods_delivery_id, $rel_type, $description);
            
            if($aId){
                $status = true;
                $message = _l('added_successfully');
            }else{
                $status = false;
                $message = _l('added_failed');
            }

            echo json_encode([
                'status' => $status,
                'message' => $message,
            ]);
        }
    }

    /**
     * delete activitylog
     * @param  [type] $id 
     * @return [type]     
     */
    public function delete_activitylog($id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        $delete = $this->purchase_model->delete_activitylog($id);
        if($delete){
            $status = true;
        }else{
            $status = false;
        }

        echo json_encode([
            'success' => $status,
        ]);
    }

    /**
     * delete order return
     * @param  [type] $id 
     * @return [type]     
     */
    public function delete_order_return($id) {

        if(!has_permission('purchase_order_return', '', 'delete')  &&  !is_admin()) {
            access_denied('purchase]');
        }

        $response = $this->purchase_model->delete_order_return($id);
        if ($response == true) {
            set_alert('success', _l('deleted'));
        } else {
            set_alert('warning', _l('problem_deleting'));
        }
        redirect(admin_url('purchase/order_returns'));
    }

    /**
     * order return pdf
     * @param  [type] $id 
     * @return [type]     
     */
    public function order_return_pdf($id)
    {
        if (!$id) {
            redirect(admin_url('purchase/order_returns'));
        }
        $this->load->model('clients_model');
        $this->load->model('currencies_model');

        $order_return_number = '';
        $order_return = $this->purchase_model->get_order_return($id);
        $order_return->client = $this->clients_model->get($order_return->company_id);
        $order_return->order_return_detail = $this->purchase_model->get_order_return_detail($id);

        $order_return->base_currency = $this->currencies_model->get_base_currency();
        if($order_return->currency != 0){
            $order_return->base_currency = pur_get_currency_by_id($order_return->currency);
        }

        $order_return->tax_data = $this->purchase_model->get_html_tax_order_return($id);
        $order_return->clientid = $order_return->company_id;


        if($order_return){
            $order_return_number .= $order_return->order_return_number.' - '.$order_return->order_return_name;
        }
        try {
            $pdf = $this->purchase_model->order_return_pdf($order_return);

        } catch (Exception $e) {
            echo pur_html_entity_decode($e->getMessage());
            die;
        }

        $type = 'D';
        ob_end_clean();

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf->Output(mb_strtoupper(slug_it($order_return_number)).'.pdf', $type);
    }

    /**
     * open warehouse modal
     * @return [type] 
     */
    public function open_warehouse_modal()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $id = $this->input->post('order_return_id');

        $data = [];
        $data['title'] = _l('select_warehouse_to_create_inventory_delivery');
        $data['id'] = $id;
        $data['warehouses'] = [];
        if(get_status_modules_pur('warehouse')){
            $this->load->model('warehouse/warehouse_model');
            $data['warehouses'] = $this->warehouse_model->get_warehouse();
        }

        $this->load->view('return_orders/select_warehouse_modal', $data);
    }
/**
     * Gets the vendor shared.
     *
     * @param        $pr_id  The pr identifier
     */
    public function get_vendor_shared($pr_id){
        $purchase_request = $this->purchase_model->get_purchase_request($pr_id);
        $vendor_str = $purchase_request->send_to_vendors;
        $vendor_arr = [];

        if($vendor_str != ''){
            $vendor_arr = explode(',', $vendor_str);
        }

        echo json_encode([
            'shared_vendor' => $vendor_str,
        ]);
    }

    public function share_request(){
        if($this->input->post()){
            $data = $this->input->post();
            $success = $this->purchase_model->share_request_to_vendor($data);

            if($success){
                set_alert('success', _l('share_request_successfully') );
            }

            redirect(admin_url('purchase/purchase_request'));
        }
    }

    /**
     * { preview purchase order file }
     *
     * @param      <type>  $id      The identifier
     * @param      <type>  $rel_id  The relative identifier
     * @return  view
     */
    public function file_purrequest($id, $rel_id)
    {
        $data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
        $data['current_user_is_admin']             = is_admin();
        $data['file'] = $this->purchase_model->get_file($id, $rel_id);
        if (!$data['file']) {
            header('HTTP/1.0 404 Not Found');
            die;
        }
        $this->load->view('purchase_request/_file', $data);
    }

    /**
     * { delete purchase order attachment }
     *
     * @param      <type>  $id     The identifier
     */
    public function delete_purrequest_attachment($id)
    {
        $this->load->model('misc_model');
        $file = $this->misc_model->get_file($id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo json_encode(['success' => $this->purchase_model->delete_purrequest_attachment($id) ]);
        } else {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }
    }


    /**
     * { change delivery status }
     *
     * @param      integer  $status     The status
     * @param         $pur_order  The pur order
     * @return     json
     */
    public function change_pr_approve_status($status, $pur_request){
        $success = $this->purchase_model->change_pr_approve_status($status, $pur_request);
        $message = '';
        $html = '';
        $status_str = '';
        $class = '';
        if($success == true){
            $message = _l('change_approval_status_successfully');
        }else{
            $message = _l('change_approval_status_fail');
        }

        if(has_permission('purchase_orders', '', 'edit') || is_admin()){
            $html .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
            $html .= '<a href="#" class="dropdown-toggle text-dark" id="tablePurOderStatus-' . $pur_request . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
            $html .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
            $html .= '</a>';

            $html .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tablePurOderStatus-' . $pur_request . '">';

            if($status == 1){
                $html .= '<li>
                          <a href="#" onclick="change_pr_approve_status( 2 ,' . $pur_request . '); return false;">
                             ' ._l('purchase_approved') . '
                          </a>
                       </li>';
                $html .= '<li>
                          <a href="#" onclick="change_pr_approve_status( 3 ,' . $pur_request . '); return false;">
                             ' ._l('purchase_reject') . '
                          </a>
                       </li>';

                $status_str = _l('purchase_draft');
                $class = 'label-primary';
            }else if($status == 2){
                $html .= '<li>
                          <a href="#" onclick="change_pr_approve_status( 1 ,' . $pur_request . '); return false;">
                             ' ._l('purchase_not_yet_approve') . '
                          </a>
                       </li>';
                $html .= '<li>
                          <a href="#" onclick="change_pr_approve_status( 3 ,' . $pur_request . '); return false;">
                             ' ._l('purchase_reject') . '
                          </a>
                       </li>';
                $status_str = _l('purchase_approved');
                $class = 'label-success';
            }else if($status == 3){ 
                $html .= '<li>
                          <a href="#" onclick="change_pr_approve_status( 1 ,' . $pur_request . '); return false;">
                             ' ._l('purchase_draft') . '
                          </a>
                       </li>';
                $html .= '<li>
                          <a href="#" onclick="change_pr_approve_status( 2 ,' . $pur_request . '); return false;">
                             ' ._l('purchase_approved') . '
                          </a>
                       </li>';

                $status_str = _l('purchase_reject');
                $class = 'label-warning';
            }
               

            $html .= '</ul>';
            $html .= '</div>';
        }

        echo json_encode([
            'success' => $success,
            'status_str' => $status_str,
            'class' => $class,
            'mess' => $message,
            'html' => $html,
        ]);
    }

    /**
     * { table vendor pur order }
     *
     * @param      <type>  $vendor  The vendor
     */
    public function table_project_pur_contract($project_id){
        $this->app->get_table_data(module_views_path('purchase', 'contracts/table_contracts'),['project' => $project_id]);
    }

    /**
     * { table vendor pur order }
     *
     * @param      <type>  $vendor  The vendor
     */
    public function table_project_pur_request($project_id){
        $this->app->get_table_data(module_views_path('purchase', 'purchase_request/table_pur_request'),['project' => $project_id]);
    }


    /**
     * Gets the purchase order row template.
     */
    public function get_purchase_invoice_row_template(){
        $name = $this->input->post('name');
        $item_name = $this->input->post('item_name');
        $item_description = $this->input->post('item_description');
        $quantity = $this->input->post('quantity');
        $unit_name = $this->input->post('unit_name');
        $unit_price = $this->input->post('unit_price');
        $taxname = $this->input->post('taxname');
        $item_code = $this->input->post('item_code');
        $unit_id = $this->input->post('unit_id');
        $tax_rate = $this->input->post('tax_rate');
        $discount = $this->input->post('discount');
        $item_key = $this->input->post('item_key');
        $currency_rate = $this->input->post('currency_rate');
        $to_currency = $this->input->post('to_currency');

        echo $this->purchase_model->create_purchase_invoice_row_template($name, $item_name, $item_description, $quantity, $unit_name, $unit_price, $taxname, $item_code, $unit_id, $tax_rate, '', $discount, '', '', '', '', '', $item_key, false, $currency_rate, $to_currency );
    }

    /**
     * { mark return order as }
     *
     * @param        $status  The status
     * @param        $id      The identifier
     */
    public function mark_return_order_as($status, $id){
        $this->db->where('id', $id);
        $this->db->update(db_prefix().'wh_order_returns', ['status' => $status]);
        if($this->db->affected_rows() > 0){
            hooks()->do_action('after_pur_return_order_status_changed', ['id' => $id, 'status' => $status]);

            set_alert('success', _l('change_order_return_status_successfully'));
        }

        redirect(admin_url('purchase/order_returns/'.$id));
    }

     /**
     * { refund }
     *
     * @param        $id         The identifier
     * @param        $refund_id  The refund identifier
     */
    public function refund_order_return($id, $refund_id = null)
    {
        if (has_permission('purchase_order_return', '', 'edit')) {
            $this->load->model('payment_modes_model');
            if (!$refund_id) {
                $data['payment_modes'] = $this->payment_modes_model->get('', [
                    'expenses_only !=' => 1,
                ]);
            } else {
                $data['refund']        = $this->purchase_model->get_order_return_refund($refund_id);
                $data['payment_modes'] = $this->payment_modes_model->get('', [], true, true);
                $i                     = 0;
                foreach ($data['payment_modes'] as $mode) {
                    if ($mode['active'] == 0 && $data['refund']->payment_mode != $mode['id']) {
                        unset($data['payment_modes'][$i]);
                    }
                    $i++;
                }
            }

            $data['order_return'] = $this->purchase_model->get_order_return($id);
            $this->load->view('return_orders/refund', $data);
        }
    }

    /**
     * Creates a refund.
     *
     * @param        $order_return_id  The debit note identifier
     */
    public function create_order_return_refund($order_return_id)
    {
        if (has_permission('purchase_order_return', '', 'edit')) {
            $data                = $this->input->post();
            $data['refunded_on'] = to_sql_date($data['refunded_on']);
            $data['staff_id']    = get_staff_user_id();
            $success             = $this->purchase_model->create_order_return_refund($order_return_id, $data);

            if ($success) {
                set_alert('success', _l('added_successfully', _l('refund')));
            }
        }

        redirect(admin_url('purchase/order_returns/' . $order_return_id));
    }

    /**
     * { edit refund }
     *
     * @param        $refund_id      The refund identifier
     * @param        $order_return_id  The debit note identifier
     */
    public function edit_order_return_refund($refund_id, $order_return_id)
    {
        if (has_permission('purchase_order_return', '', 'edit')) {
            $data                = $this->input->post();
            $data['refunded_on'] = to_sql_date($data['refunded_on']);
            $success             = $this->purchase_model->edit_order_return_refund($refund_id, $data);

            if ($success) {
                set_alert('success', _l('updated_successfully', _l('refund')));
            }
        }

        redirect(admin_url('purchase/order_returns/' . $order_return_id));
    }


    /**
     * { delete refund }
     *
     * @param        $refund_id       The refund identifier
     * @param        $credit_note_id  The credit note identifier
     */
    public function delete_order_return_refund($refund_id, $order_return_id)
    {
        if (has_permission('purchase_debit_notes', '', 'delete')) {
            $success = $this->purchase_model->delete_order_return_refund($refund_id, $order_return_id);
            if ($success) {
                set_alert('success', _l('deleted', _l('refund')));
            }
        }
        redirect(admin_url('purchase/order_returns/' . $order_return_id));
    }

    /**
     * { update delivery status }
     *
     * @param      <type>  $pur_order  The pur order
     * @param      <type>  $status     The status
     */
    public function mark_pur_order_as( $status, $pur_order){
        
        $this->db->where('id', $pur_order);
        $this->db->update(db_prefix().'pur_orders', ['order_status' => $status]);
        if ($this->db->affected_rows() > 0) {
            if($status == 'delivered'){
                $this->db->where('id', $pur_order);
                $this->db->update(db_prefix().'pur_orders', ['delivery_status' => 1, 'delivery_date' => date('Y-m-d')]);
            }else{
                $this->db->where('id', $pur_order);
                $this->db->update(db_prefix().'pur_orders', ['delivery_status' => 0]);
            }

            set_alert('success', _l('updated_successfully', _l('order_status')));
        }

        redirect(admin_url('purchase/purchase_order/' . $pur_order));
    }

     /**
     * { table vendor debit notes }
     *
     * @param      <type>  $vendor  The vendor
     */
    public function table_vendor_quoations($vendor){
        $this->app->get_table_data(module_views_path('purchase', 'quotations/table_estimates'),['vendor' => $vendor]);
    }

    /**
     * Gets the currency rate.
     *
     * @param        $currency_id  The currency identifier
     */
    public function get_currency_rate($currency_id){
        $base_currency = get_base_currency();

        $pr_currency = pur_get_currency_by_id($currency_id);

        $currency_rate = 1;
        $convert_str = ' ('.$base_currency->name.' => '.$base_currency->name.')'; 
        $currency_name = '('.$base_currency->name.')';
        if($base_currency->id != $pr_currency->id){
            $currency_rate = pur_get_currency_rate($base_currency->name, $pr_currency->name);
            $convert_str = ' ('.$base_currency->name.' => '.$pr_currency->name.')'; 
            $currency_name = '('.$pr_currency->name.')';
        }

        echo json_encode([
            'currency_rate' => $currency_rate,
            'convert_str' => $convert_str,
            'currency_name' => $currency_name,
        ]);

    }

    /**
     * { vendor contract change }
     *
     * @param        $vendor  The vendor
     */
    public function vendor_contract_change($vendor)
    {
        $pur_orders = $this->purchase_model->get_pur_order_approved_by_vendor($vendor);

        $html = '<option value=""></option>';
        foreach($pur_orders as $po){
            $html .= '<option value="'.$po['id'].'">'.$po['pur_order_number'].'</option>';
        }

        echo json_encode(['html' => $html]);
    }



    /* Change client status / active / inactive */
    public function change_vendor_status($id, $status)
    {
        if ($this->input->is_ajax_request()) {

            $this->db->where('userid', $id);
            $this->db->update(db_prefix() . 'pur_vendor', [
                'active' => $status,
            ]);
        }
    }

    /**
     * { confirm registration }
     *
     * @param        $vendor_id  The client identifier
     */
    public function confirm_registration($vendor_id)
    {
        if (!is_admin() && !has_permission('purchase_settings', '', 'edit') ) {
            access_denied('Vendor Confirm Registration, ID: ' . $vendor_id);
        }
        $success = $this->purchase_model->confirm_registration($vendor_id);
        if($success){
            set_alert('success', _l('vendor_registration_successfully_confirmed'));
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * { purchase order setting }
     * @return  json
     */
    public function allow_vendors_to_register(){
        $data = $this->input->post();
        if($data != 'null'){
            $value = $this->purchase_model->update_pc_options_setting($data);
            if($value){
                $success = true;
                $message = _l('updated_successfully');
            }else{
                $success = false;
                $message = _l('updated_false');
            }
            echo json_encode([
                'message' => $message,
                'success' => $success,
            ]);
            die;
        }
    }

    /**
     * Sends a purchase order.
     * @return redirect
     */
    public function send_contract(){
        if($this->input->post()){
            $data = $this->input->post();
            $data['content'] = $this->input->post('content', false);
            $send = $this->purchase_model->send_contract($data);
            if($send){
                set_alert('success',_l('send_contract_successfully'));
                
            }else{
                set_alert('warning',_l('send_contract_fail'));
            }
            redirect(admin_url('purchase/contract/'.$data['contract_id']));
            
        }
    }

    /**
     * Get the template modal content
     *
     * @return string
     */
    public function modal_template()
    {
        $this->load->model('templates_model');
        $data['rel_type'] = $this->input->post('rel_type');

        // When modal is submitted, it returns to the proposal/contract that was being edited.
        $data['rel_id'] = $this->input->post('rel_id');

        if ($this->input->post('slug') == 'new') {
            $data['title'] = _l('add_template');
        } elseif ($this->input->post('slug') == 'edit') {
            $data['title'] = _l('edit_template');
            $data['id']    = $this->input->post('id');
            $this->authorize($data['id']);
            $data['template'] = $this->templates_model->get($data['id']);
        }

        $this->load->view('templates/template', $data);
    }

    /**
     * Manage template
     *
     * @param  int|null $id
     *
     */
    public function template($id = null)
    {
        $this->load->model('templates_model');
        $content = $this->input->post('content', false);
        $content = html_purify($content);

        $data['name']      = $this->input->post('name');
        $data['content']   = $content;
        $data['addedfrom'] = get_staff_user_id();
        $data['type']      = $this->input->post('rel_type');

        // so when modal is submitted, it returns to the proposal/contract that was being edited.
        $rel_id = $this->input->post('rel_id');

        if (is_numeric($id)) {
            $this->authorize($id);
            $success = $this->templates_model->update($id, $data);
            $message = _l('template_updated');
        } else {
            $success = $this->templates_model->create($data);
            $message = _l('template_added');
        }

        if ($success) {
            set_alert('success', $message);
        }

        redirect(admin_url('purchase/contract/' . $rel_id));
    }

    /**
     * { request quotation pdf }
     *
     * @param      <type>  $id     The identifier
     * @return pdf output
     */
    public function compare_quotation_pdf($id)
    {
        if (!$id) {
            redirect(admin_url('purchase/purchase_request'));
        }

        $pur_request = $this->purchase_model->get_compare_quotation_pdf_html($id);

        try {
            $pdf = $this->purchase_model->compare_quotation_pdf($pur_request);
        } catch (Exception $e) {
            echo pur_html_entity_decode($e->getMessage());
            die;
        }
        
        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf->Output('compare_quotation.pdf', $type);
    }

    /**
     * { purchase_invoice_pdf }
     */
    public function purchase_invoice_pdf($invoice_id){
        if (!$invoice_id) {
            redirect(admin_url('purchase/invoices'));
        }

        $purchase_invoice = $this->purchase_model->get_purchase_invoice_pdf_html($invoice_id);

        try {
            $pdf = $this->purchase_model->purchase_invoice_pdf($purchase_invoice);
        } catch (Exception $e) {
            echo pur_html_entity_decode($e->getMessage());
            die;
        }
        
        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf->Output('purchase_invoice.pdf', $type);
    }

}