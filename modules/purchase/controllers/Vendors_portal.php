<?php

defined('BASEPATH') or exit('No direct script access allowed');

use app\services\ValidatesContact;

class Vendors_portal extends App_Controller
{
    /**
     * @since  2.3.3
     */
    

    public $template = [];

    public $data = [];

    public $use_footer = true;

    public $use_submenu = true;

    public $use_navigation = true;

    public function __construct()
    {
        parent::__construct();


        if (is_staff_logged_in()
            && $this->app->is_db_upgrade_required($this->current_db_version)) {
            redirect(admin_url());
        }

        $this->load->library('app_vendor_area_constructor');

        if (method_exists($this, 'validateContact')) {
            $this->validateContact();
        }

        $this->load->model('purchase_model');
    }

    public function layout($notInThemeViewFiles = false)
    {
        /**
         * Navigation and submenu
         * @deprecated 2.3.2
         * @var boolean
         */

        $this->data['use_navigation'] = $this->use_navigation == true;
        $this->data['use_submenu']    = $this->use_submenu == true;

        /**
         * @since  2.3.2 new variables
         * @var array
         */
        $this->data['navigationEnabled'] = $this->use_navigation == true;
        $this->data['subMenuEnabled']    = $this->use_submenu == true;

        /**
         * Theme head file
         * @var string
         */
        $this->template['head'] = $this->load->view('vendor_portal/head', $this->data, true);

        $GLOBALS['customers_head'] = $this->template['head'];

        /**
         * Load the template view
         * @var string
         */
        $module                       = CI::$APP->router->fetch_module();
        $this->data['current_module'] = $module;

        $viewPath = !is_null($module) || $notInThemeViewFiles ? $this->view : 'vendor_portal/' . $this->view;

        $this->template['view']    = $this->load->view($viewPath, $this->data, true);
        $GLOBALS['customers_view'] = $this->template['view'];

        /**
         * Theme footer
         * @var string
         */
        $this->template['footer'] = $this->use_footer == true
        ? $this->load->view('vendor_portal/footer', $this->data, true)
        : '';
        $GLOBALS['customers_footer'] = $this->template['footer'];

        /**
         * @deprecated 2.3.0
         * Theme scripts.php file is no longer used since vresion 2.3.0, add app_customers_footer() in themes/[theme]/index.php
         * @var string
         */
        $this->template['scripts'] = '';
        if (file_exists(VIEWPATH . 'vendor_portal/scripts.php')) {
            if (ENVIRONMENT != 'production') {
                trigger_error(sprintf('%1$s', 'Clients area theme file scripts.php file is no longer used since version 2.3.0, add app_customers_footer() in themes/[theme]/index.php. You can check the original theme index.php for example.'));
            }

            $this->template['scripts'] = $this->load->view('vendor_portal/scripts', $this->data, true);
        }

        /**
         * Load the theme compiled template
         */
        $this->load->view('vendor_portal/index', $this->template);
    }

    /**
     * Sets view data
     * @param  array $data
     * @return core/ClientsController
     */
    public function data($data)
    {
        if (!is_array($data)) {
            return false;
        }

        $this->data = array_merge($this->data, $data);

        return $this;
    }

    /**
     * Set view to load
     * @param  string $view view file
     * @return core/ClientsController
     */
    public function view($view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Sets view title
     * @param  string $title
     * @return core/ClientsController
     */
    public function title($title)
    {
        $this->data['title'] = $title;

        return $this;
    }

    /**
     * Disables theme navigation
     * @return core/ClientsController
     */
    public function disableNavigation()
    {
        $this->use_navigation = false;

        return $this;
    }

    /**
     * Disables theme navigation
     * @return core/ClientsController
     */
    public function disableSubMenu()
    {
        $this->use_submenu = false;

        return $this;
    }

    /**
    * Disables theme footer
    * @return core/ClientsController
    */
    public function disableFooter()
    {
        $this->use_footer = false;

        return $this;
    }
    /**
     * { index }
     */
    public function index()
    {
        if(is_vendor_logged_in()){
            $data['is_home'] = true;    

            $data['project_statuses'] = $this->projects_model->get_project_statuses();
            $data['title']            = get_vendor_company_name(get_vendor_user_id());
            $data['payment'] = $this->purchase_model->get_payment_by_vendor_v2(get_vendor_user_id());
            $data['pur_order'] = $this->purchase_model->get_pur_order_by_vendor(get_vendor_user_id());

            $this->data($data);
            $this->view('vendor_portal/home');
            $this->layout();
        }else{
            redirect(site_url('purchase/authentication_vendor'));
        }
    }

    /**
     * { terms and conditions }
     */
    public function terms_and_conditions(){
        $data['title'] = _l('terms_and_conditions');
        $data['terms'] = get_purchase_option('terms_and_conditions');
        $this->data($data);
        $this->view('vendor_portal/terms_and_conditions');
        $this->layout();
    }

    /**
     * { profile contact }
     */
    public function profile()
    {
        if (!is_vendor_logged_in() && !is_staff_logged_in()) {
            
            redirect(site_url('purchase/authentication_vendor/login'));
        }
        if ($this->input->post('profile')) {
            $this->form_validation->set_rules('firstname', _l('client_firstname'), 'required');
            $this->form_validation->set_rules('lastname', _l('client_lastname'), 'required');

            $this->form_validation->set_message('contact_email_profile_unique', _l('form_validation_is_unique'));
            $this->form_validation->set_rules('email', _l('clients_email'), 'required|valid_email');
            if ($this->form_validation->run() !== false) {

                handle_vendor_contact_profile_image_upload(get_vendor_contact_user_id());

                $data = $this->input->post();

                $success = $this->purchase_model->update_contact([
                    'firstname'          => $this->input->post('firstname'),
                    'lastname'           => $this->input->post('lastname'),
                    'title'              => $this->input->post('title'),
                    'email'              => $this->input->post('email'),
                    'phonenumber'        => $this->input->post('phonenumber'),
                    'direction'          => $this->input->post('direction'),
                  
                ], get_vendor_contact_user_id(), true);

                if ($success == true) {
                    set_alert('success', _l('clients_profile_updated'));
                }

                redirect(site_url('purchase/vendors_portal/profile'));
            }
        } elseif ($this->input->post('change_password')) {
            $this->form_validation->set_rules('oldpassword', _l('clients_edit_profile_old_password'), 'required');
            $this->form_validation->set_rules('newpassword', _l('clients_edit_profile_new_password'), 'required');
            $this->form_validation->set_rules('newpasswordr', _l('clients_edit_profile_new_password_repeat'), 'required|matches[newpassword]');
            if ($this->form_validation->run() !== false) {
                $success = $this->purchase_model->change_contact_password(
                    get_vendor_contact_user_id(),
                    $this->input->post('oldpassword', false),
                    $this->input->post('newpasswordr', false)
                );

                if (is_array($success) && isset($success['old_password_not_match'])) {
                    set_alert('danger', _l('client_old_password_incorrect'));
                } elseif ($success == true) {
                    set_alert('success', _l('client_password_changed'));
                }

                redirect(site_url('purchase/vendors_portal/profile'));
            }
        }
        $data['contact'] = $this->purchase_model->get_contact(get_vendor_contact_user_id());
        $data['title'] = _l('clients_profile_heading');
        $this->data($data);
        $this->view('vendor_portal/vendors/profile_contact');
        $this->layout();
    }

    /**
     * { company profile }
     */
    public function company()
    {
        if (!is_vendor_logged_in() && !is_staff_logged_in()) {
            
            redirect(site_url('purchase/authentication_vendor/login'));
        }
        if ($this->input->post() && is_primary_contact_pur()) {
            if (get_option('company_is_required') == 1) {
                $this->form_validation->set_rules('company', _l('clients_company'), 'required');
            }

            if (active_clients_theme() == 'perfex') {
                // Fix for custom fields checkboxes validation
                $this->form_validation->set_rules('company_form', '', 'required');
            }

           

            if ($this->form_validation->run() !== false) {
                $data['company'] = $this->input->post('company');

                if (!is_null($this->input->post('vat'))) {
                    $data['vat'] = $this->input->post('vat');
                }

                if (!is_null($this->input->post('default_language'))) {
                    $data['default_language'] = $this->input->post('default_language');
                }

                if (!is_null($this->input->post('custom_fields'))) {
                    $data['custom_fields'] = $this->input->post('custom_fields');
                }

                $data['phonenumber'] = $this->input->post('phonenumber');
                $data['website']     = $this->input->post('website');
                $data['country']     = $this->input->post('country');
                $data['city']        = $this->input->post('city');
                $data['address']     = $this->input->post('address');
                $data['zip']         = $this->input->post('zip');
                $data['state']       = $this->input->post('state');

                if (get_option('allow_primary_contact_to_view_edit_billing_and_shipping') == 1
                    && is_primary_contact_pur()) {

                    // Dynamically get the billing and shipping values from $_POST
                    for ($i = 0; $i < 2; $i++) {
                        $prefix = ($i == 0 ? 'billing_' : 'shipping_');
                        foreach (['street', 'city', 'state', 'zip', 'country'] as $field) {
                            $data[$prefix . $field] = $this->input->post($prefix . $field);
                        }
                    }
                }

                $success = $this->purchase_model->update_vendor($data, get_vendor_user_id());
                if ($success == true) {
                    set_alert('success', _l('clients_profile_updated'));
                }

                redirect(site_url('purchase/vendors_portal/company'));
            }
        }

        $data['client'] = $this->purchase_model->get_vendor(get_vendor_user_id());
        $data['title'] = _l('client_company_info');
        $this->data($data);
        $this->view('vendor_portal/vendors/company_profile');
        $this->layout();
    }

    /**
     * Removes a profile image.
     */
    public function remove_profile_image()
    {
        $id = get_vendor_contact_user_id();

        if (file_exists(PURCHASE_MODULE_UPLOAD_FOLDER.'/contact_profile/' . $id)) {
            delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER.'/contact_profile/'. $id);
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'pur_contacts', [
            'profile_image' => null,
        ]);

        if ($this->db->affected_rows() > 0) {
            redirect(site_url('purchase/vendors_portal/profile'));
        }
        redirect(site_url('purchase/vendors_portal/profile'));
    }

    /**
     * { change language }
     *
     * @param      string  $lang   The language
     */
    public function change_language($lang = '')
    {
       

        $this->db->where('userid', get_vendor_user_id());
        $this->db->update(db_prefix() . 'pur_vendor', ['default_language' => $lang]);

        if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect(site_url('purchase/vendors_portal'));
        }
    }

    /**
     * { Purchase order }
     */
    public function purchase_order(){
        if (!is_vendor_logged_in() && !is_staff_logged_in()) {
            
            redirect(site_url('purchase/authentication_vendor/login'));
        }

        $data['title']            = _l('purchase_order');
      
        $data['pur_order'] = $this->purchase_model->get_pur_order_by_vendor(get_vendor_user_id());

        $this->data($data);
        $this->view('vendor_portal/purchase_order');
        $this->layout();
    }

    /**
     * { list contracts }
     */
    public function contracts(){
        if (!is_vendor_logged_in() && !is_staff_logged_in()) {
            
            redirect(site_url('purchase/authentication_vendor/login'));
        }

        $data['title']            = _l('contracts');
      
        $data['contracts'] = $this->purchase_model->get_contracts_by_vendor(get_vendor_user_id());

        $this->data($data);
        $this->view('vendor_portal/contracts');
        $this->layout();
    }

    /**
     * { list items }
     */
    public function items(){
        if (!is_vendor_logged_in() && !is_staff_logged_in()) {
            
            redirect(site_url('purchase/authentication_vendor/login'));
        }

        $data['title']            = _l('items');
        
        
        $data['items'] = $this->purchase_model->get_vendor_item(get_vendor_user_id());

        $data['external_items'] = $this->purchase_model->get_item_by_vendor(get_vendor_user_id());

        $this->data($data);
        $this->view('vendor_portal/items');
        $this->layout();
    }

    /**
     * { list quotations }
     */
    public function quotations(){
        if (!is_vendor_logged_in() && !is_staff_logged_in()) {
            
            redirect(site_url('purchase/authentication_vendor/login'));
        }

        $data['title']            = _l('quotations');
      
        $data['quotations'] = $this->purchase_model->get_estimate('',['vendor' => get_vendor_user_id()]);

        $this->data($data);
        $this->view('vendor_portal/quotations');
        $this->layout();
    }

    /**
     * { list payments }
     */
    public function payments(){
        if (!is_vendor_logged_in() && !is_staff_logged_in()) {
            
            redirect(site_url('purchase/authentication_vendor/login'));
        }

        $data['title']            = _l('payments');
      
        $data['payments'] = $this->purchase_model->get_payment_by_vendor(get_vendor_user_id());

        $this->data($data);
        $this->view('vendor_portal/payments');
        $this->layout();
    }

    

    /**
     * Uploads an attachment.
     *
     * @param      <type>  $id     The identifier
     */
    public function upload_attachment()
    {
       $check = handle_pur_vendor_attachments_upload(get_vendor_user_id());
    }

    /**
     * { preview file pur vendor }
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


        $this->load->view('vendor_portal/_file',$data);
      
    }

    /**
     * Adds an update quotation.
     *
     * @param      string  $id     The identifier
     */
    public function add_update_quotation($id = '',$view = ''){

        if (!is_vendor_logged_in() ) {
            
            redirect(site_url('purchase/authentication_vendor/login'));
        }

        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();
        $data['base_currency'] = $this->currencies_model->get_base_currency();

        $pur_quotation_row_template = $this->purchase_model->create_quotation_row_template();

        if ($id == '') {
            $title = _l('create_new_estimate');
        } else {
            $estimate = $this->purchase_model->get_estimate($id);
            $data['estimate'] = $estimate;
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

            $data['tax_data'] = $this->purchase_model->get_html_tax_pur_estimate($id);
            
            $data['edit']     = true;
            $title            = _l('edit', _l('estimate_lowercase'));
        }
        if ($this->input->get('customer_id')) {
            $data['customer_id'] = $this->input->get('customer_id');
        }

        if ($this->input->post()) {
            $action = $this->input->post('action');

            switch ($action) {
             case 'quo_comment':
                    // comment is blank
                    if (!$this->input->post('content')) {
                        redirect($this->uri->uri_string());
                    }
                    $data_cmt                = $this->input->post();
                    $data_cmt['rel_id'] = $id;
                    $data_cmt['rel_type'] = 'pur_quotation';
                    $this->purchase_model->add_comment($data_cmt, true);
                    redirect($this->uri->uri_string() . '?tab=discussion');

                    break;
            }
        }

        $data['ajaxItems'] = false;
        if(total_rows(db_prefix().'pur_vendor_items', ['vendor' => get_vendor_user_id()]) <= ajax_on_total_items()){ 
            $data['items'] = $this->purchase_model->pur_get_grouped('can_be_purchased', false, get_vendor_user_id());
        }else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }

        $data['pur_request'] = $this->purchase_model->get_purchase_request_by_vendor(get_vendor_user_id());

        $data['pur_quotation_row_template'] = $pur_quotation_row_template;

        $this->load->model('taxes_model');
        $data['taxes'] = $this->purchase_model->get_taxes();
        
        $data['vendor_currency'] = get_vendor_currency(get_vendor_user_id());

        $this->load->model('invoice_items_model');

        
        $data['items_groups'] = $this->invoice_items_model->get_groups();

        $data['comments'] = $this->purchase_model->get_comments($id, 'pur_quotation');
        $data['view'] = $view;
        $data['staff']             = $this->purchase_model->get_vendor_admins(get_vendor_user_id());
        $data['vendors'] = $this->purchase_model->get_vendor();

        $data['units'] = $this->purchase_model->get_units();
        

        $data['title']             = $title;
       

        $this->data($data);
        $this->view('vendor_portal/estimate');
        $this->layout();
    }

    /**
     * { view quotation }
     *
     * @param        $id     The identifier
     */
    public function view_quotation($id)
    {
        if (!is_vendor_logged_in() ) {
            
            redirect(site_url('purchase/authentication_vendor/login'));
        }
        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();

        $estimate = $this->purchase_model->get_estimate($id);

        if($estimate->currency != 0){
            $data['base_currency'] = pur_get_currency_by_id($estimate->currency);
        }

        $data['estimate'] = $estimate;
        $data['estimate_detail'] = $this->purchase_model->get_pur_estimate_detail($id);
        $data['comments'] = $this->purchase_model->get_comments($id, 'pur_quotation');
        $data['tax_data'] = $this->purchase_model->get_html_tax_pur_estimate($id);
        $data['title'] = format_pur_estimate_number($id);

        $files = $this->purchase_model->get_purchase_estimate_attachments($id);
        $data['files'] = $files;

        if ($this->input->post()) {
            $action = $this->input->post('action');

            switch ($action) {
             case 'quo_comment':
                    // comment is blank
                    if (!$this->input->post('content')) {
                        redirect($this->uri->uri_string());
                    }
                    $data_cmt                = $this->input->post();
                    $data_cmt['rel_id'] = $id;
                    $data_cmt['rel_type'] = 'pur_quotation';
                    $this->purchase_model->add_comment($data_cmt, true);
                    redirect($this->uri->uri_string() . '?tab=discussion');

                    break;
            }
        }

        $this->data($data);
        $this->view('vendor_portal/view_estimate');
        $this->layout();
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
     * { tax change event }
     *
     * @param      <type>  $tax    The tax
     * @return   json
     */
    public function tax_change($tax){
        $taxes = explode('%7C', $tax);
        $total_tax = $this->purchase_model->get_total_tax($taxes);
        
        echo json_encode([
            'total_tax' => $total_tax,
        ]);
    }

    /**
     * { quotation form }
     *
     * @param      string  $id     The identifier
     */
    public function quotation_form($id='')
    {
        if (!is_vendor_logged_in() && !is_staff_logged_in()) {
            
            redirect(site_url('purchase/authentication_vendor/login'));
        }

        if ($this->input->post()) {
            $estimate_data = $this->input->post();
            $estimate_data['vendor'] = get_vendor_user_id();
            $estimate_data['terms'] = $this->input->post('terms', false);
            if ($id == '') {
           
                $id = $this->purchase_model->add_estimate($estimate_data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('estimate')));
                    
                    redirect(site_url('purchase/vendors_portal/view_quotation/' . $id));
                    
                }
            } else {
            
                $success = $this->purchase_model->update_estimate($estimate_data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('estimate')));
                }
                redirect(site_url('purchase/vendors_portal/view_quotation/' . $id));
                
            }
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
        if (!is_vendor_logged_in() && !is_staff_logged_in()) {
            
            redirect(site_url('purchase/authentication_vendor/login'));
        }

        if (!$id) {
            redirect(site_url('purchase/vendors_portal/quotations'));
        }
        $success = $this->purchase_model->delete_estimate($id);
        if (is_array($success)) {
            set_alert('warning', _l('is_invoiced_estimate_delete_error'));
        } elseif ($success == true) {
            set_alert('success', _l('deleted', _l('estimate')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('estimate_lowercase')));
        }
        redirect(site_url('purchase/vendors_portal/quotations'));
    }

    /**
     * { view purchase order }
     */
    public function pur_order($id,$hash=''){
        if(!is_vendor_logged_in()){
            check_pur_order_restrictions($id, $hash);
        }

        $data['pur_order_detail'] = $this->purchase_model->get_pur_order_detail($id);
        $data['pur_order'] = $this->purchase_model->get_pur_order($id);
        $title = _l('pur_order_detail');

        if ($this->input->post()) {
            $action = $this->input->post('action');

            switch ($action) {
             case 'po_comment':
                    // comment is blank
                    if (!$this->input->post('content')) {
                        redirect($this->uri->uri_string());
                    }
                    $data_cmt                = $this->input->post();
                    $data_cmt['rel_id'] = $id;
                    $data_cmt['rel_type'] = 'pur_order';
                    $this->purchase_model->add_comment($data_cmt, true);
                    redirect($this->uri->uri_string() . '?tab=discussion');

                    break;
            }
        }

        $files = $this->purchase_model->get_pur_order_files($id);
        $data['files'] = $files;

        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();

        $data['tax_data'] = $this->purchase_model->get_html_tax_pur_order($id);
        $data['comments'] = $this->purchase_model->get_comments($id, 'pur_order');
        $data['taxes'] = $this->purchase_model->get_taxes();
        $data['staff']             = $this->staff_model->get('', ['active' => 1]);
        $data['vendors'] = $this->purchase_model->get_vendor();
        $data['estimates'] = $this->purchase_model->get_estimates_by_status(2);
        $data['units'] = $this->purchase_model->get_units();
        $data['items'] = $this->purchase_model->get_items();
        $data['title'] = $title;

        $this->data($data);
        $this->view('vendor_portal/pur_order');
        $this->layout();
    }

    /**
     * { view purchase request }
     */
    public function pur_request($id,$hash){

        check_pur_request_restrictions($id, $hash);
        
        $this->load->model('departments_model');
        $this->load->model('currencies_model');

        $data['pur_request_detail'] = $this->purchase_model->get_pur_request_detail($id);
        $data['pur_request'] = $this->purchase_model->get_purchase_request($id);
        $data['title'] = $data['pur_request']->pur_rq_name;
        $data['departments'] = $this->departments_model->get();
        $data['units'] = $this->purchase_model->get_units();
        $data['items'] = $this->purchase_model->get_items();
        
        $data['check_appr'] = $this->purchase_model->get_approve_setting('pur_request');
        $data['get_staff_sign'] = $this->purchase_model->get_staff_sign($id,'pur_request');
        $data['check_approve_status'] = $this->purchase_model->check_approval_details($id,'pur_request');
        $data['list_approve_status'] = $this->purchase_model->get_list_approval_details($id,'pur_request');

        $data['base_currency'] = $this->currencies_model->get_base_currency();
        if($data['pur_request']->currency != 0){
            $data['base_currency'] = pur_get_currency_by_id($data['pur_request']->currency);
        }

        $data['taxes'] = $this->purchase_model->get_taxes();
        $data['taxes_data'] = $this->purchase_model->get_html_tax_pur_request($id);

        $files = $this->purchase_model->get_pur_request_files($id);
        $data['files'] = $files;

        $this->data($data);
        $this->view('vendor_portal/pur_request');
        $this->layout();
    }

    /**
     * { view contract }
     *
     * @param        $id     The identifier
     */
    public function view_contract($id){
        $contract = $this->purchase_model->get_contract($id);
        $data['contract']  = $contract;
        if (!$contract) {
            show_404();
        }

        if ($this->input->post()) {
            $action = $this->input->post('action');

            switch ($action) {
             case 'contract_comment':
                    // comment is blank
                    if (!$this->input->post('content')) {
                        redirect($this->uri->uri_string());
                    }
                    $data_cmt                = $this->input->post();
                    $data_cmt['rel_id'] = $id;
                    $data_cmt['rel_type'] = 'pur_contract';
                    $this->purchase_model->add_comment($data_cmt, true);
                    redirect($this->uri->uri_string() . '?tab=discussion');

                    break;

             case 'sign_contract':
                    process_digital_signature_image($this->input->post('signature', false), PURCHASE_MODULE_UPLOAD_FOLDER .'/vendor_sign/'.$id);
                    $this->db->where('id', $id);
                    $this->db->update(db_prefix().'pur_contracts', array_merge(get_acceptance_info_array(), [
                        'signed' => 1,
                    ]));

                    // Notify contract creator that customer signed the contract
                    pur_send_contract_signed_notification_to_staff($id);

                    set_alert('success', _l('document_signed_successfully'));
                    redirect($_SERVER['HTTP_REFERER']);
            }
        }

        $this->disableNavigation();
        $this->disableSubMenu();

        $data['attachments'] = $this->purchase_model->get_pur_contract_attachment($id);
        $data['title']     = $contract->contract_number;

        $data['bodyclass'] = 'contract contract-view';

        $data['identity_confirmation_enabled'] = true;
        $data['bodyclass'] .= ' identity-confirmation';
        $this->app_scripts->theme('sticky-js','assets/plugins/sticky/sticky.js');
        $data['comments'] = $this->purchase_model->get_comments($id, 'pur_contract');
        //add_views_tracking('proposal', $id);
        hooks()->do_action('contract_html_viewed', $id);
        $this->app_css->remove('reset-css','customers-area-default');
        $data                      = hooks()->apply_filters('contract_customers_area_view_data', $data);
        $this->data($data);
        no_index_customers_area();
        $this->view('vendor_portal/contracthtml');
        $this->layout();
    }

    /**
     * { invoices }
     */
    public function invoices(){
        if (!is_vendor_logged_in() && !is_staff_logged_in()) {
            redirect(site_url('purchase/authentication_vendor/login'));
        }

        $data['title'] = _l('pur_invoices');
        $data['invoices'] = $this->purchase_model->get_invoices_by_vendor(get_vendor_user_id());

        $this->data($data);
        $this->view('vendor_portal/invoices/manage');
        $this->layout();
    }

    /**
     * { invoice }
     *
     * @param        $id     The identifier
     */
    public function invoice($id){
        $invoice = $this->purchase_model->get_pur_invoice($id);
        $data['pur_invoice'] = $invoice;
        if (!$invoice) {
            show_404();
        }

        if ($this->input->post()) {
            $action = $this->input->post('action');

            switch ($action) {
             case 'inv_comment':
                    // comment is blank
                    if (!$this->input->post('content')) {
                        redirect($this->uri->uri_string());
                    }
                    $data_cmt                = $this->input->post();
                    $data_cmt['rel_id'] = $id;
                    $data_cmt['rel_type'] = 'pur_invoice';
                    $this->purchase_model->add_comment($data_cmt, true);
                    redirect($this->uri->uri_string() . '?tab=discussion');

                    break;
            }
        }

        $data['invoice_detail'] = $this->purchase_model->get_pur_invoice_detail($id);
        $data['tax_data'] = $this->purchase_model->get_html_tax_pur_invoice($id);

        $data['payment'] = $this->purchase_model->get_payment_invoice($id);
        $data['comments'] = $this->purchase_model->get_comments($id, 'pur_invoice');
        $data['title'] = $invoice->invoice_number;
        $this->data($data);
        $this->view('vendor_portal/invoices/invoice');
        $this->layout();
    }

    /**
     * { purchase request }
     */
    public function purchase_request(){
        if (!is_vendor_logged_in() && !is_staff_logged_in()) {
            redirect(site_url('purchase/authentication_vendor/login'));
        }

        $data['title'] = _l('purchase_request');
        $data['purchase_request'] = $this->purchase_model->get_purchase_request_by_vendor(get_vendor_user_id());

        $this->data($data);
        $this->view('vendor_portal/purchase_request/manage');
        $this->layout();
    }

    /**
     * 
     * Adds update items.
     *
     * @param      string  $id     The identifier
     */
    public function add_update_items($id = ''){
        if (!is_vendor_logged_in() && !is_staff_logged_in()) {
            redirect(site_url('purchase/authentication_vendor/login'));
        }

        $vendor_id = get_vendor_user_id();

        if($id == ''){
            $data['title'] = _l('pur_add_item');
        }else{
            $data['title'] = _l('pur_update_item');
            $data['item'] = $this->purchase_model->get_item_of_vendor($id);
            $data['files'] = $this->purchase_model->get_vendor_item_file($id);
        }

        if($this->input->post()){
            $item_data = $this->input->post();
            if($id == ''){
                
                $item_id = $this->purchase_model->add_vendor_item($item_data, $vendor_id);
                if($item_id){
                    handle_vendor_item_attachment($item_id);

                    set_alert('success', _l('added_successfully'));
                }
            }else{
                if($data['item']->vendor_id != $vendor_id){

                    set_alert('warning', _l('item_not_found'));

                    redirect(site_url('purchase/vendors_portal/items'));
                }

                $success = $this->purchase_model->update_vendor_item($item_data, $id);

                $handled = handle_vendor_item_attachment($id);
                if($success || $handled){
                    set_alert('success', _l('updated_successfully'));
                }
            }

            redirect(site_url('purchase/vendors_portal/items'));
        }

        $data['units'] = $this->purchase_model->get_unit_add_item();
        $data['taxes'] = $this->purchase_model->get_taxes();
        $data['commodity_groups'] = $this->purchase_model->get_commodity_group_add_commodity();
        $data['sub_groups'] = $this->purchase_model->get_sub_group();

        $this->data($data);
        $this->view('vendor_portal/items/item');
        $this->layout();
    }

    /**
     * { delete vendor item }
     *
     * @param        $item_id  The item identifier
     */
    public function delete_vendor_item($item_id){
        if (!is_vendor_logged_in()) {
            redirect(site_url('purchase/authentication_vendor/login'));
        }

        $vendor_id = get_vendor_user_id();

        if(!$item_id){
            redirect(site_url('purchase/vendors_portal/items'));
        }

        $delete = $this->purchase_model->delete_vendor_item($item_id, $vendor_id);
        if($delete){
            set_alert('success', _l('deleted_successfully'));
        }

        redirect(site_url('purchase/vendors_portal/items'));

    }

    /**
     * { detail item }
     */
    public function detail_item($item_id){
        if (!is_vendor_logged_in()) {
            redirect(site_url('purchase/authentication_vendor/login'));
        }
        $vendor_id = get_vendor_user_id();

        $data['item'] = $this->purchase_model->get_item_of_vendor($item_id);

        $data['commodity_file'] = $this->purchase_model->get_vendor_item_file($item_id);

        $data['item_from'] = 'vendor';

        if(!isset($data['item']->vendor_id) || $data['item']->vendor_id != $vendor_id){

            $items = $this->purchase_model->get_item_by_vendor($vendor_id);

            $itds = [];
            foreach($items as $it){
                $itds[] = $it['items'];
            }

            if(!in_array($item_id, $itds)){
                set_alert('warning', _l('item_not_found'));
                redirect(site_url('purchase/vendors_portal/items'));
            }

            $this->db->where('id', $item_id);
            $data['item'] = $this->db->get(db_prefix().'items')->row();

            $data['item_file'] = $this->purchase_model->get_item_attachments($item_id);

            if(is_numeric($data['item']->from_vendor_item)){
                $data['vendor_image'] = $this->purchase_model->get_vendor_item_file($data['item']->from_vendor_item);
            }

            $data['item_from'] = 'admin';
        }



        $data['title'] = $data['item']->commodity_code;

        $this->data($data);
        $this->view('vendor_portal/items/detail_item');
        $this->layout();
    }

    /**
     * { share_item }
     */
    public function share_item($item_id){
        if (!is_vendor_logged_in()) {
            redirect(site_url('purchase/authentication_vendor/login'));
        }
        $vendor_id = get_vendor_user_id();

        $item = $this->purchase_model->get_item_of_vendor($item_id);
        if($item->vendor_id != $vendor_id){
            set_alert('warning', _l('item_not_found'));
            redirect(site_url('purchase/vendors_portal/items'));
        }

        $shared = $this->purchase_model->share_vendor_item($item_id);
        if($shared){
            set_alert('success', _l('shared_successfully'));
        }

        redirect(site_url('purchase/vendors_portal/items'));
    }

    /**
     * Gets the currency.
     *
     * @param      <type>  $id     The identifier
     */
    public function get_currency($id)
    {
        echo json_encode(get_currency($id));
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
            $item->long_description   = nl2br($item->long_description);
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

        echo $this->purchase_model->create_quotation_row_template($name, $item_name, $quantity, $unit_name, $unit_price, $taxname, $item_code, $unit_id, $tax_rate, '', $discount, '', '', '', '', '', $item_key, false, $currency_rate, $to_currency );
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
     * Adds an update invoice.
     */
    public function add_update_invoice($id = ''){
        if (!is_vendor_logged_in()) {
            redirect(site_url('purchase/authentication_vendor/login'));
        }
        $vendor_id = get_vendor_user_id();


        $data['contracts'] = $this->purchase_model->get_contracts_by_vendor($vendor_id);
        $data['taxes'] = $this->purchase_model->get_taxes();

        $pur_invoice_row_template = $this->purchase_model->create_purchase_invoice_row_template();

        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();

        if($id == ''){
            $data['title'] = _l('pur_add_invoice');
            $data['pur_orders'] = $this->purchase_model->get_pur_order_approved_for_inv_by_vendor($vendor_id);
            
        }else{
            $data['pur_orders'] = $this->purchase_model->get_pur_order_approved_by_vendor($vendor_id);
            $data['title'] = _l('pur_update_invoice');
            $invoice = $this->purchase_model->get_pur_invoice($id);

            if($invoice->vendor != $vendor_id){
                set_alert('invoice_not_found');
                redirect(site_url('purchase/vendors_portal/invoices'));
            }

            $data['pur_invoice'] = $invoice;
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
        }

        $data['pur_invoice_row_template'] = $pur_invoice_row_template;

        $data['ajaxItems'] = false;
        if(total_rows(db_prefix().'pur_vendor_items', ['vendor' => get_vendor_user_id()]) <= ajax_on_total_items()){ 
            $data['items'] = $this->purchase_model->pur_get_grouped('can_be_purchased', false, get_vendor_user_id());
        }else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }

        $data['currencies'] = $this->currencies_model->get();

        $data['vendor_currency'] = get_vendor_currency(get_vendor_user_id());

        $this->data($data);
        $this->view('vendor_portal/invoices/add_update_invoice');
        $this->layout();
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
     * { pur invoice form }
     * @return redirect
     */
    public function pur_invoice_form(){
        if (!is_vendor_logged_in()) {
            redirect(site_url('purchase/authentication_vendor/login'));
        }
        $vendor_id = get_vendor_user_id();

        if($this->input->post()){
            $data = $this->input->post();
            if($data['id'] == ''){
                unset($data['id']);
                $data['add_from'] = get_vendor_contact_user_id();
                $data['add_from_type'] = 'vendor';
                $data['vendor'] = $vendor_id;
                $mess = $this->purchase_model->add_pur_invoice($data);
                if ($mess) {
                    handle_pur_invoice_file($mess);
                    set_alert('success', _l('added_successfully'));

                } else {
                    set_alert('warning', _l('add_purchase_invoice_fail'));
                }
                redirect(site_url('purchase/vendors_portal/invoices'));
            }else{
                $id = $data['id'];
                unset($data['id']);
                handle_pur_invoice_file($id);
                $success = $this->purchase_model->update_pur_invoice($id, $data);
                if($success){
                    set_alert('success', _l('updated_successfully') );
                }else{
                    set_alert('warning', _l('update_purchase_invoice_fail'));
                }
                redirect(site_url('purchase/vendors_portal/invoices'));
            }
        }
    }

    /**
     * { delete invoice }
     *
     * @param      <type>  $id     The identifier
     */
    public function delete_invoice($id){
        if (!is_vendor_logged_in()) {
            redirect(site_url('purchase/authentication_vendor/login'));
        }
        $vendor_id = get_vendor_user_id();

        $invoice = $this->purchase_model->get_pur_invoice($id);

        if($invoice->vendor != $vendor_id){
            redirect(site_url('purchase/vendors_portal/invoices'));
        }

        if (!$id) {
            redirect(site_url('purchase/vendors_portal/invoices'));
        }
        $response = $this->purchase_model->delete_pur_invoice($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced'));
        } elseif ($response == true) {
            set_alert('success', _l('deleted'));
        } else {
            set_alert('warning', _l('problem_deleting'));
        }
        redirect(site_url('purchase/Vendors_portal/invoices'));
    }

    /**
     * { confirm order }
     *
     * @param        $pur_order  The pur order
     */
    public function confirm_order($pur_order){
        if (!is_vendor_logged_in()) {
            redirect(site_url('purchase/authentication_vendor/login'));
        }

        $success = $this->purchase_model->confirm_order($pur_order);

        echo json_encode(['success' => $success ]);
    }

    /**
     * { update delivery status }
     *
     * @param      <type>  $pur_order  The pur order
     * @param      <type>  $status     The status
     */
    public function update_delivery_status($pur_order, $status){
        if (!is_vendor_logged_in()) {
            redirect(site_url('purchase/authentication_vendor/login'));
        }

        $success = $this->purchase_model->change_delivery_status($status, $pur_order);

        echo json_encode(['success' => $success ]);
    }


    /**
     * { update delivery date }
     *
     * @param      <type>  $pur_order  The pur order
     * @param      <type>  $status     The status
     */
    public function update_delivery_date($pur_order){
        if (!is_vendor_logged_in()) {
            redirect(site_url('purchase/authentication_vendor/login'));
        }
        $success = false;
        $date = $this->input->post('date');
        $this->db->where('id', $pur_order);
        $this->db->update(db_prefix().'pur_orders', ['delivery_date'=> to_sql_date($date)]);
        if($this->db->affected_rows() > 0){
            $success = true;
        }


        echo json_encode(['success' => $success ]);
    }

    /**
     * { update delivery date on list }
     * @return redirect
     */
    public function update_delivery_date_on_list(){
        if (!is_vendor_logged_in()) {
            redirect(site_url('purchase/authentication_vendor/login'));
        }

        $data = $this->input->post();

        $success = false;
        $this->db->where('id', $data['order_id']);
        $this->db->update(db_prefix().'pur_orders', ['delivery_date'=> to_sql_date($data['delivery_date'])]);
        if($this->db->affected_rows() > 0){
            $success = true;
        }


        if($success == true){
            set_alert('success', _l('update_delivery_date_successfully'));
        }

        redirect(site_url('purchase/vendors_portal/purchase_order'));
    }

    /**
     * Uploads files.
     *
     * @param      string  $pur_order  The pur order
     */
    public function upload_files($pur_order)
    {
        $success = false;
        
        $success = handle_vendor_po_attachments_upload(get_vendor_user_id(), true, $pur_order);
        

        if ($success) {
            
            set_alert('success', _l('uploaded_successfully'));
        }

        redirect(site_url('purchase/vendors_portal/pur_order/'.$pur_order.'?tab=attachment'));
    }

    /**
     * Uploads files.
     *
     * @param      string  $pur_estimate  The pur order
     */
    public function upload_estimate_files($pur_estimate)
    {
        $success = false;
        
        $success = handle_vendor_estimate_attachments_upload(get_vendor_user_id(), true, $pur_estimate);
        

        if ($success) {
            
            set_alert('success', _l('uploaded_successfully'));
        }

        redirect(site_url('purchase/vendors_portal/view_quotation/'.$pur_estimate.'?tab=attachment'));
    }

    /**
     * { delete_po_file }
     *
     * @param      <type>  $id         The identifier
     * @param      string  $pur_order  The pur order
     */
    public function delete_po_file($id, $pur_order){
        if (!is_vendor_logged_in()) {
            redirect(site_url('purchase/authentication_vendor/login'));
        }

        $this->db->where('id', $id);
        $file = $this->db->get(db_prefix().'files')->row();

        $contact_id = get_vendor_contact_user_id();

        if($file->contact_id != $contact_id){
            set_alert('warning', _l('file_not_found'));
            redirect(site_url('purchase/vendors_portal/pur_order/'.$pur_order.'?tab=attachment'));
        }

        $this->purchase_model->delete_purorder_attachment($id);

        redirect(site_url('purchase/vendors_portal/pur_order/'.$pur_order.'?tab=attachment'));
    }

    /**
     * { delete_estimate_file }
     *
     * @param      <type>  $id         The identifier
     * @param      string  $pur_order  The pur order
     */
    public function delete_estimate_file($id, $pur_estimate){
        if (!is_vendor_logged_in()) {
            redirect(site_url('purchase/authentication_vendor/login'));
        }

        $this->db->where('id', $id);
        $file = $this->db->get(db_prefix().'files')->row();

        $contact_id = get_vendor_contact_user_id();

        if($file->contact_id != $contact_id){
            set_alert('warning', _l('file_not_found'));
            redirect(site_url('purchase/vendors_portal/view_quotation/'.$pur_estimate.'?tab=attachment'));
        }

        $this->purchase_model->delete_estimate_attachment($id);

        redirect(site_url('purchase/vendors_portal/view_quotation/'.$pur_estimate.'?tab=attachment'));
    }

    /**
     * { delete_vendor_item_file }
     *
     * @param      <type>  $id         The identifier
     * @param      string  $pur_order  The pur order
     */
    public function delete_vendor_item_file($id, $item_id){
        if (!is_vendor_logged_in()) {
            redirect(site_url('purchase/authentication_vendor/login'));
        }

        $this->db->where('id', $id);
        $file = $this->db->get(db_prefix().'files')->row();

        $contact_id = get_vendor_contact_user_id();


        $this->purchase_model->delete_vendor_item_file($id);

        redirect(site_url('purchase/vendors_portal/add_update_items/'.$item_id));
    }

    /**
     * { order returns }
     */
    public function order_returns(){
        if (!is_vendor_logged_in()) {
            redirect(site_url('purchase/authentication_vendor/login'));
        }

        $vendor_id = get_vendor_user_id();

        $data['order_returns'] = $this->purchase_model->get_order_returns_for_vendor($vendor_id);

        $data['title'] = _l('pur_order_returns');

        $this->data($data);
        $this->view('vendor_portal/order_returns/manage');
        $this->layout();
    }

    /**
     * { order return }
     *
     * @param        $id     The identifier
     */
    public function order_return($id){
        if (!is_vendor_logged_in()) {
            redirect(site_url('purchase/authentication_vendor/login'));
        }

        $data['order_return'] = $this->purchase_model->get_order_return($id);
        $data['order_return_detail'] = $this->purchase_model->get_order_return_detail($id);

        $data['title'] = $data['order_return']->order_return_name;

        $this->data($data);
        $this->view('vendor_portal/order_returns/detail');
        $this->layout();
    }

    /**
     * { setting language }
     */
    public function setting_language(){
        if (!is_vendor_logged_in()) {
            redirect(site_url('purchase/authentication_vendor/login'));
        }

        $vendor_id = get_vendor_user_id();

        if($this->input->post()){
            $default_language = $this->input->post('default_language');
            $this->db->where('userid', $vendor_id);
            $this->db->update(db_prefix().'pur_vendor', ['default_language' => $default_language]);

            if($this->db->affected_rows() > 0){
                set_alert('success', _l('change_language_successfully'));
                redirect(site_url('purchase/vendors_portal/setting_language'));
            }
        }

        $data['vendor'] = $this->purchase_model->get_vendor($vendor_id);
        $data['title'] = _l('change_language');

        $this->data($data);
        $this->view('vendor_portal/change_language');
        $this->layout();
    }

    /**
     * Uploads pr files.
     *
     * @param      string  $pur_request  The pur request
     * @param      string  $hash         The hash
     */
    public function upload_pr_files($pur_request, $hash)
    {
        $success = false;
        
        $success = handle_vendor_pr_attachments_upload(get_vendor_user_id(), true, $pur_request);
        

        if ($success) {
            
            set_alert('success', _l('uploaded_successfully'));
        }

        redirect(site_url('purchase/vendors_portal/pur_request/'.$pur_request.'/'.$hash.'?tab=attachment'));
    }

    /**
     * { function_description }
     *
     * @param        $id         The identifier
     * @param      string  $pur_order  The pur order
     */
    public function delete_pr_file($id, $pur_request, $hash){
        if (!is_vendor_logged_in()) {
            redirect(site_url('purchase/authentication_vendor/login'));
        }

        $this->db->where('id', $id);
        $file = $this->db->get(db_prefix().'files')->row();

        $contact_id = get_vendor_contact_user_id();

        if($file->contact_id != $contact_id){
            set_alert('warning', _l('file_not_found'));
            redirect(site_url('purchase/vendors_portal/pur_request/'.$pur_request.'/'.$hash.'?tab=attachment'));
        }

        $this->purchase_model->delete_purrequest_attachment($id);

        redirect(site_url('purchase/vendors_portal/pur_request/'.$pur_request.'/'.$hash.'?tab=attachment'));
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

        echo $this->purchase_model->create_purchase_invoice_row_template($name, $item_name, $item_description, $quantity, $unit_name, $unit_price, $taxname, $item_code, $unit_id, $tax_rate, '', $discount, '', '', '', '', '', $item_key,  false, $currency_rate, $to_currency );
    }

    /**
     * { update order return status }
     *
     * @param        $order_id  The order identifier
     * @param        $status    The status
     */
    public function update_order_return_status($order_id, $status)
    {
        if (!is_vendor_logged_in()) {
            redirect(site_url('purchase/authentication_vendor/login'));
        }

        $success = false;
        $this->db->where('id', $order_id);
        $this->db->update(db_prefix().'wh_order_returns', ['status' => $status]);
        if($this->db->affected_rows() > 0){
            $success = true;
        }

        echo json_encode([
            'success' => $success,
        ]);

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
}