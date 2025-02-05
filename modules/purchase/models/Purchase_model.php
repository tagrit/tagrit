<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * This class describes a purchase model.
 */
class Purchase_model extends App_Model
{   
    private $shipping_fields = ['shipping_street', 'shipping_city', 'shipping_city', 'shipping_state', 'shipping_zip', 'shipping_country'];

    private $contact_columns;

    public function __construct()
    {
        parent::__construct();
        
        $this->contact_columns = hooks()->apply_filters('contact_columns', ['firstname', 'lastname', 'email', 'phonenumber', 'title', 'password', 'send_set_password_email', 'donotsendwelcomeemail', 'permissions', 'direction', 'invoice_emails', 'estimate_emails', 'credit_note_emails', 'contract_emails', 'task_emails', 'project_emails', 'ticket_emails', 'is_primary']);
    }

    /**
     * Gets the vendor.
     *
     * @param      string        $id     The identifier
     * @param      array|string  $where  The where
     *
     * @return     <type>        The vendor or list vendors.
     */
    public function get_vendor($id = '', $where = [])
    {
        $this->db->select(implode(',', prefixed_table_fields_array(db_prefix() . 'pur_vendor')) . ',' . get_sql_select_vendor_company());

        

        if (is_numeric($id)) {

            $this->db->join(db_prefix() . 'countries', '' . db_prefix() . 'countries.country_id = ' . db_prefix() . 'pur_vendor.country', 'left');
            $this->db->join(db_prefix() . 'pur_contacts', '' . db_prefix() . 'pur_contacts.userid = ' . db_prefix() . 'pur_vendor.userid AND is_primary = 1', 'left');

            if ((is_array($where) && count($where) > 0) || (is_string($where) && $where != '')) {
                $this->db->where($where);
            }

            $this->db->where(db_prefix().'pur_vendor.userid', $id);
            $vendor = $this->db->get(db_prefix() . 'pur_vendor')->row();

            if ($vendor && get_option('company_requires_vat_number_field') == 0) {
                $vendor->vat = null;
            }


            return $vendor;

        }else{


            if(!has_permission('purchase_vendors', '', 'view') && is_staff_logged_in()){

                $this->db->join(db_prefix() . 'countries', '' . db_prefix() . 'countries.country_id = ' . db_prefix() . 'pur_vendor.country', 'left');
                $this->db->join(db_prefix() . 'pur_contacts', '' . db_prefix() . 'pur_contacts.userid = ' . db_prefix() . 'pur_vendor.userid AND is_primary = 1', 'left');

                if ((is_array($where) && count($where) > 0) || (is_string($where) && $where != '')) {
                    $this->db->where($where);
                }

                $this->db->where(db_prefix().'pur_vendor.userid IN (SELECT vendor_id FROM '.db_prefix().'pur_vendor_admin WHERE staff_id=' . get_staff_user_id() . ')');
            }else{
                $this->db->join(db_prefix() . 'countries', '' . db_prefix() . 'countries.country_id = ' . db_prefix() . 'pur_vendor.country', 'left');
                $this->db->join(db_prefix() . 'pur_contacts', '' . db_prefix() . 'pur_contacts.userid = ' . db_prefix() . 'pur_vendor.userid AND is_primary = 1', 'left');

                if ((is_array($where) && count($where) > 0) || (is_string($where) && $where != '')) {
                    $this->db->where($where);
                }
            }
        }

        $this->db->order_by('company', 'asc');

        return $this->db->get(db_prefix() . 'pur_vendor')->result_array();
    }

    /**
     * Gets the contacts.
     *
     * @param      string  $vendor_id  The vendor identifier
     * @param      array   $where      The where
     *
     * @return     <type>  The contacts.
     */
    public function get_contacts($vendor_id = '', $where = ['active' => 1])
    {
        $this->db->where($where);
        if ($vendor_id != '') {
            $this->db->where('userid', $vendor_id);
        }
        $this->db->order_by('is_primary', 'DESC');

        return $this->db->get(db_prefix() . 'pur_contacts')->result_array();
    }

    /**
     * Gets the contact.
     *
     * @param      <type>  $id     The identifier
     *
     * @return     <type>  The contact.
     */
    public function get_contact($id)
    {
        $this->db->where('id', $id);

        return $this->db->get(db_prefix() . 'pur_contacts')->row();
    }

    /**
     * Gets the primary contacts.
     *
     * @param      <type>  $id     The identifier
     *
     * @return     <type>  The primary contacts.
     */
    public function get_primary_contacts($id)
    {
        $this->db->where('userid', $id);
        $this->db->where('is_primary', 1);
        return $this->db->get(db_prefix() . 'pur_contacts')->row();
    }

    /**
     * Adds a vendor.
     *
     * @param      <type>   $data       The data
     * @param      integer  $client_id  The client identifier
     *
     * @return     integer  ( id vendor )
     */
    public function add_vendor($data, $client_id = null,$client_or_lead_convert_request = false)
    {
        
        if (isset($data['balance'])) {
            $data['balance'] = str_replace(',', '', $data['balance']);
            if($data['balance'] != '' && $data['balance'] > 0){
                if($data['balance_as_of'] != ''){
                    $data['balance_as_of'] = to_sql_date($data['balance_as_of']);
                }else{
                    $data['balance_as_of'] = date('Y-m-d');
                }
            }else{
                unset($data['balance']);
                unset($data['balance_as_of']);
            }
        }

        $contact_data = [];
        foreach ($this->contact_columns as $field) {
            if (isset($data[$field])) {
                $contact_data[$field] = $data[$field];
                // Phonenumber is also used for the company profile
                if ($field != 'phonenumber') {
                    unset($data[$field]);
                }
            }
        }
        // From customer profile register
        if (isset($data['contact_phonenumber'])) {
            $contact_data['phonenumber'] = $data['contact_phonenumber'];
            unset($data['contact_phonenumber']);
        }

        if (isset($data['is_primary'])) {
            $contact_data['is_primary'] = $data['is_primary'];
            unset($data['is_primary']);
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        if(isset($data['category']) && count($data['category']) > 0){
            $data['category'] = implode(',', $data['category']);
        }

        if (isset($data['groups_in'])) {
            $groups_in = $data['groups_in'];
            unset($data['groups_in']);
        }

        $data = $this->check_zero_columns($data);

        $data['datecreated'] = date('Y-m-d H:i:s');

        if (is_staff_logged_in()) {
            $data['addedfrom'] = get_staff_user_id();
        }

        // New filter action
     

        if(isset($client_id) && $client_id > 0){
            $userid = $client_id;
        } else {
            $this->db->insert(db_prefix() . 'pur_vendor', $data);
            $userid = $this->db->insert_id();

            hooks()->do_action('after_pur_vendor_created', [
                'id'            => $userid,
                'data'          => $data,
            ]);
        }
        
        if ($userid) {
            if (isset($custom_fields)) {
                $_custom_fields = $custom_fields;
                // Possible request from the register area with 2 types of custom fields for contact and for comapny/customer
                if (count($custom_fields) == 1) {
                    unset($custom_fields);
                    $custom_fields['vendors']                = $_custom_fields['vendors'];
                } 

                handle_custom_fields_post($userid, $custom_fields);
            }
                
            /**
             * Used in Import, Lead Convert, Register
             */
            if ($client_or_lead_convert_request == true) {
                $contact_id = $this->add_contact($contact_data, $userid, $client_or_lead_convert_request);
            }
            
            /**
             * Used in Import, Lead Convert, Register
             */        

            $log = 'ID: ' . $userid;

            $isStaff = null;
            if (!is_vendor_logged_in() && is_staff_logged_in()) {
                $log .= ', From Staff: ' . get_staff_user_id();
                $isStaff = get_staff_user_id();
            }

            
        }

        return $userid;
    }

    /**
     * { update vendor }
     *
     * @param      <type>   $data            The data
     * @param      <type>   $id              The identifier
     * @param      boolean  $client_request  The client request
     *
     * @return     boolean 
     */
    public function update_vendor($data, $id, $client_request = false)
    {
        if(isset($data['DataTables_Table_0_length'])){
            unset($data['DataTables_Table_0_length']);
        }

        if (isset($data['balance'])) {
            $data['balance'] = str_replace(',', '', $data['balance']);
            if($data['balance'] != '' && $data['balance'] > 0){
                if($data['balance_as_of'] != ''){
                    $data['balance_as_of'] = to_sql_date($data['balance_as_of']);
                }else{
                    $data['balance_as_of'] = date('Y-m-d');
                }
            }else{
                unset($data['balance']);
                unset($data['balance_as_of']);
            }
        }
        
        if (isset($data['update_all_other_transactions'])) {
            $update_all_other_transactions = true;
            unset($data['update_all_other_transactions']);
        }

        if (isset($data['update_credit_notes'])) {
            $update_credit_notes = true;
            unset($data['update_credit_notes']);
        }

        $affectedRows = 0;
        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }

        if(isset($data['category']) && count($data['category']) > 0){
            $data['category'] = implode(',', $data['category']);
        }

        $data = $this->check_zero_columns($data);

        $data = hooks()->apply_filters('before_pur_vendor_updated', $data, $id);

        $this->db->where('userid', $id);
        $this->db->update(db_prefix() . 'pur_vendor', $data);

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }


        if ($affectedRows > 0) {
            hooks()->do_action('after_pur_vendor_updated', $id);


            return true;
        }

        return false;
    }

    /**
     * { check zero columns }
     *
     * @param      <type>  $data   The data
     *
     * @return     array  
     */
    private function check_zero_columns($data)
    {
        if (!isset($data['show_primary_contact'])) {
            $data['show_primary_contact'] = 0;
        }

        if (isset($data['default_currency']) && $data['default_currency'] == '' || !isset($data['default_currency'])) {
            $data['default_currency'] = 0;
        }

        if (isset($data['country']) && $data['country'] == '' || !isset($data['country'])) {
            $data['country'] = 0;
        }

        if (isset($data['billing_country']) && $data['billing_country'] == '' || !isset($data['billing_country'])) {
            $data['billing_country'] = 0;
        }

        if (isset($data['shipping_country']) && $data['shipping_country'] == '' || !isset($data['shipping_country'])) {
            $data['shipping_country'] = 0;
        }

        return $data;
    }

    /**
     * Gets the vendor admins.
     *
     * @param      <type>  $id     The identifier
     *
     * @return     <type>  The vendor admins.
     */
    public function get_vendor_admins($id)
    {
        $this->db->where('vendor_id', $id);

        return $this->db->get(db_prefix() . 'pur_vendor_admin')->result_array();
    }


    /**
     * { assign vendor admins }
     *
     * @param      <type>   $data   The data
     * @param      <type>   $id     The identifier
     *
     * @return     boolean 
     */
    public function assign_vendor_admins($data, $id)
    {
        $affectedRows = 0;

        if (count($data) == 0) {
            $this->db->where('vendor_id', $id);
            $this->db->delete(db_prefix() . 'pur_vendor_admin');
            if ($this->db->affected_rows() > 0) {
                $affectedRows++;
            }
        } else {
            $current_admins     = $this->get_vendor_admins($id);
            $current_admins_ids = [];
            foreach ($current_admins as $c_admin) {
                array_push($current_admins_ids, $c_admin['staff_id']);
            }
            foreach ($current_admins_ids as $c_admin_id) {
                if (!in_array($c_admin_id, $data['customer_admins'])) {
                    $this->db->where('staff_id', $c_admin_id);
                    $this->db->where('vendor_id', $id);
                    $this->db->delete(db_prefix() . 'pur_vendor_admin');
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
            foreach ($data['customer_admins'] as $n_admin_id) {
                if (total_rows(db_prefix() . 'pur_vendor_admin', [
                    'vendor_id' => $id,
                    'staff_id' => $n_admin_id,
                ]) == 0) {
                    $this->db->insert(db_prefix() . 'pur_vendor_admin', [
                        'vendor_id'   => $id,
                        'staff_id'      => $n_admin_id,
                        'date_assigned' => date('Y-m-d H:i:s'),
                    ]);
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
        }
        if ($affectedRows > 0) {
            return true;
        }

        return false;
    }
    
    /**
     * { delete vendor }
     *
     * @param      <type>   $id     The identifier
     *
     * @return     boolean  
     */
    public function delete_vendor($id)
    {
        $affectedRows = 0;

        hooks()->do_action('before_client_deleted', $id);

        $last_activity = get_last_system_activity_id();
        $company       = get_company_name($id);

        $this->db->where('userid', $id);
        $this->db->delete(db_prefix() . 'pur_vendor');
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
            // Delete all user contacts
            $this->db->where('userid', $id);
            $contacts = $this->db->get(db_prefix() . 'pur_contacts')->result_array();
            foreach ($contacts as $contact) {
                $this->delete_contact($contact['id']);
            }

            $this->db->where('relid', $id);
            $this->db->where('fieldto', 'vendor');
            $this->db->delete(db_prefix() . 'customfieldsvalues');

            $this->db->where('vendor_id', $id);
            $this->db->delete(db_prefix() . 'pur_vendor_admin');

            $this->db->where('rel_id',$id);
            $this->db->where('rel_type','pur_vendor');
            $this->db->delete(db_prefix().'files');
            if ($this->db->affected_rows() > 0) {
                $affectedRows++;
            }

            if (is_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_vendor/'. $id)) {
                delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_vendor/'. $id);
            }

            $this->db->where('rel_type','pur_vendor');
            $this->db->where('rel_id',$id);
            $this->db->delete(db_prefix().'notes');
        }
        if ($affectedRows > 0) {
            hooks()->do_action('after_client_deleted', $id);

            return true;
        }

        return false;
    }

    /**
     * Adds a contact.
     *
     * @param      <type>   $data                The data
     * @param      <type>   $customer_id         The customer identifier
     * @param      boolean  $not_manual_request  Not manual request
     *
     * @return     boolean  or contact id
     */
    public function add_contact($data, $customer_id, $not_manual_request = false)
    {
        $send_set_password_email = isset($data['send_set_password_email']) ? true : false;

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        if (isset($data['permissions'])) {
            $permissions = $data['permissions'];
            unset($data['permissions']);
        }

        $data['email_verified_at'] = date('Y-m-d H:i:s');

        if (isset($data['fakeusernameremembered'])) {
            unset($data['fakeusernameremembered']);
        }
        if (isset($data['fakepasswordremembered'])) {
            unset($data['fakepasswordremembered']);
        }

        if (isset($data['is_primary'])) {
            $data['is_primary'] = 1;
            $this->db->where('userid', $customer_id);
            $this->db->update(db_prefix() . 'pur_contacts', [
                'is_primary' => 0,
            ]);
        } else {
            $data['is_primary'] = 0;
        }

        $password_before_hash = '';
        $data['userid']       = $customer_id;
        if (isset($data['password'])) {
            $password_before_hash = $data['password'];
            $data['password'] = app_hash_password($data['password']);
        }

        $data['datecreated'] = date('Y-m-d H:i:s');

        $data['email'] = trim($data['email']);


        $this->db->insert(db_prefix() . 'pur_contacts', $data);
        $contact_id = $this->db->insert_id();

        if ($contact_id) {

            if (isset($custom_fields)) {
                handle_custom_fields_post($contact_id, $custom_fields);
            }
            
            if(get_option('send_email_welcome_for_new_contact') == 1){
                $this->send_contact_welcome_mail($data, $password_before_hash, $contact_id);
            }

            return $contact_id;
        }

        return false;
    }

    /**
     * Sends a contact welcome mail.
     */
    public function send_contact_welcome_mail($data, $password_before_hash, $contact_id){
        $this->load->model('emails_model');
        
        $contact = $this->get_contact($contact_id);


        if($contact){
            $contact->password_before_hash = $password_before_hash;
            $template = mail_template('vendor_welcome_new_contact', 'purchase', $contact);
            $template->send();
        }

        return true;
    }

    /**
     * { update contact }
     *
     * @param      <type>   $data            The data
     * @param      <type>   $id              The identifier
     * @param      boolean  $client_request  The client request
     *
     * @return     boolean 
     */
    public function update_contact($data, $id, $client_request = false)
    {
        $affectedRows = 0;
        $contact      = $this->get_contact($id);
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password']             = app_hash_password($data['password']);
            $data['last_password_change'] = date('Y-m-d H:i:s');
        }

        if (isset($data['fakeusernameremembered'])) {
            unset($data['fakeusernameremembered']);
        }
        if (isset($data['fakepasswordremembered'])) {
            unset($data['fakepasswordremembered']);
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }


        $send_set_password_email = isset($data['send_set_password_email']) ? true : false;
        $set_password_email_sent = false;
      
        $data['is_primary'] = isset($data['is_primary']) ? 1 : 0;

        // Contact cant change if is primary or not
        if ($client_request == true) {
            unset($data['is_primary']);
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'pur_contacts', $data);

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
            if (isset($data['is_primary']) && $data['is_primary'] == 1) {
                $this->db->where('userid', $contact->userid);
                $this->db->where('id !=', $id);
                $this->db->update(db_prefix() . 'pur_contacts', [
                    'is_primary' => 0,
                ]);
            }
        }

       
        if ($affectedRows > 0 ) {
            return true;
        } 

        return false;
    }

    /**
     * { delete contact }
     *
     * @param      <type>   $id     The identifier
     *
     * @return     boolean  
     */
    public function delete_contact($id)
    {
        hooks()->do_action('before_delete_contact', $id);

        $this->db->where('id', $id);
        $result      = $this->db->get(db_prefix() . 'pur_contacts')->row();
        $customer_id = $result->userid;

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'pur_contacts');

        if ($this->db->affected_rows() > 0) {
            
            hooks()->do_action('contact_deleted', $id, $result);

            return true;
        }

        return false;
    }

    /**
     * Gets the approval setting.
     *
     * @param      string  $id     The identifier
     *
     * @return     <type>  The approval setting.
     */
    public function get_approval_setting($id = '')
    {
        if(is_numeric($id)){
            $this->db->where('id', $id);
            return $this->db->get(db_prefix().'pur_approval_setting')->row();
        }
        return $this->db->get(db_prefix().'pur_approval_setting')->result_array();
    }

    /**
     * Adds an approval setting.
     *
     * @param      <type>   $data   The data
     *
     * @return     boolean 
     */
    public function add_approval_setting($data)
    {
        unset($data['approval_setting_id']);

        if(isset($data['approver'])){
            $setting = [];
            foreach ($data['approver'] as $key => $value) {
                $node = [];
                $node['approver'] = $data['approver'][$key];
                $node['staff'] = $data['staff'][$key];
                $node['action'] = $data['action'][$key];

                $setting[] = $node;
            }
            unset($data['approver']);
            unset($data['staff']);
            unset($data['action']);
        }
        $data['setting'] = json_encode($setting);

        $this->db->insert(db_prefix() .'pur_approval_setting', $data);
        $insert_id = $this->db->insert_id();
        if($insert_id){
            return true;
        }
        return false;
    }

    /**
     * { edit approval setting }
     *
     * @param      <type>   $id     The identifier
     * @param      <type>   $data   The data
     *
     * @return     boolean  
     */
    public function edit_approval_setting($id, $data)
    {
        unset($data['approval_setting_id']);

        if(isset($data['approver'])){
            $setting = [];
            foreach ($data['approver'] as $key => $value) {
                $node = [];
                $node['approver'] = $data['approver'][$key];
                $node['staff'] = $data['staff'][$key];
                $node['action'] = $data['action'][$key];

                $setting[] = $node;
            }
            unset($data['approver']);
            unset($data['staff']);
            unset($data['action']);
        }
        $data['setting'] = json_encode($setting);

        $this->db->where('id', $id);
        $this->db->update(db_prefix() .'pur_approval_setting', $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * { delete approval setting }
     *
     * @param      <type>   $id     The identifier
     *
     * @return     boolean   
     */
    public function delete_approval_setting($id)
    {
        if(is_numeric($id)){
            $this->db->where('id', $id);
            $this->db->delete(db_prefix() .'pur_approval_setting');

            if ($this->db->affected_rows() > 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Gets the items.
     *
     * @return     <array>  The items.
     */
    public function get_items(){
       return $this->db->query('select id as id, CONCAT(commodity_code," - " ,description) as label from '.db_prefix().'items')->result_array();
    }

    /**
     * Gets the commodity code name.
     *
     * @return       The commodity code name.
     */
    public function get_commodity_code_name() {
        $arr_value = $this->db->query('select * from ' . db_prefix() . 'items where active = 1 order by id desc')->result_array();
        return $this->item_to_variation($arr_value);

    }

    /**
     * { item to variation }
     *
     * @param        $array_value  The array value
     *
     * @return     array   
     */
    public function item_to_variation($array_value)
    {
        $new_array=[];
        foreach ($array_value as $key =>  $values) {

            $name = '';
            if($values['attributes'] != null && $values['attributes'] != ''){
                $attributes_decode = json_decode($values['attributes']);

                foreach ($attributes_decode as $n_value) {
                    if(is_array($n_value)){
                        foreach ($n_value as $n_n_value) {
                            if(strlen($name) > 0){
                                $name .= '#'.$n_n_value->name.' ( '.$n_n_value->option.' ) ';
                            }else{
                                $name .= ' #'.$n_n_value->name.' ( '.$n_n_value->option.' ) ';
                            }
                        }
                    }else{

                        if(strlen($name) > 0){
                            $name .= '#'.$n_value->name.' ( '.$n_value->option.' ) ';
                        }else{
                            $name .= ' #'.$n_value->name.' ( '.$n_value->option.' ) ';
                        }
                    }
                }


            }
            array_push($new_array, [
                'id' => $values['id'],
                'label' => $values['commodity_code'].'_'.$values['description'],

            ]);
        }
        return $new_array;
    }
    /**
     * Gets the items by vendor.
     *
     * @return     <array>  The items.
     */
    public function get_items_by_vendor($vendor){
       return $this->db->query('select id as id, CONCAT(commodity_code," - " ,description) as label from '.db_prefix().'items where id IN ( select items from '.db_prefix().'pur_vendor_items where vendor = '.$vendor.' )')->result_array();
    }

    /**
     * Gets the items by vendor variations.
     *
     * @return       The items.
     */
    public function get_items_by_vendor_variation($vendor){
       $arr_value = $this->db->query('select * from ' . db_prefix() . 'items where active = 1 AND id IN ( select items from '.db_prefix().'pur_vendor_items where vendor = '.$vendor.' ) order by id desc')->result_array();
        return $this->item_to_variation($arr_value);
    }

    /**
     * Gets the items by identifier.
     *
     * @param      <type>  $id     The identifier
     *
     * @return     <row>  The items by identifier.
     */
    public function get_items_by_id($id){
        $this->db->where('id',$id);
        return $this->db->get(db_prefix().'items')->row();
    }

    /**
     * Gets the units by identifier.
     *
     * @param      <type>  $id     The identifier
     *
     * @return     <row>  The units by identifier.
     */
    public function get_units_by_id($id){
        $this->db->where('unit_type_id',$id);
        return $this->db->get(db_prefix().'ware_unit_type')->row();
    }

    /**
     * Gets the units.
     *
     * @return     <array>  The list units.
     */
    public function get_units(){
        return $this->db->query('select unit_type_id as id, unit_name as label from '.db_prefix().'ware_unit_type')->result_array();
    }

    /**
     * { items change event}
     *
     * @param      <type>  $code   The code
     *
     * @return     <row>  ( item )
     */
    public function items_change($code){
        $this->db->where('id',$code);
        $rs = $this->db->get(db_prefix().'items')->row();

        $this->db->where('unit_type_id',$rs->unit_id);
        $unit = $this->db->get(db_prefix().'ware_unit_type')->row();

        if($unit){
            $rs->unit = $unit->unit_name;
        }else{
            $rs->unit = '';
        }
        
        if(get_status_modules_pur('warehouse') == true){
            $this->db->where('commodity_id',$code);
            $commo = $this->db->get(db_prefix().'inventory_manage')->result_array();
            $rs->inventory = 0;
            if(count($commo) > 0){
                foreach($commo as $co){
                    $rs->inventory += $co['inventory_number'];
                }
            }       
        }else{
            $rs->inventory = 0;
        }

        return $rs;
    }

    /**
     * Gets the purchase request.
     *
     * @param      string  $id     The identifier
     *
     * @return     <row or array>  The purchase request.
     */
    public function get_purchase_request($id = ''){
        if($id == ''){
            if(!has_permission('purchase_request', '', 'view') && is_staff_logged_in()){

                $or_where = '';
                $list_vendor = get_vendor_admin_list(get_staff_user_id());
                foreach($list_vendor as $vendor_id){
                    $or_where .= ' OR find_in_set('.$vendor_id.', ' . db_prefix() . 'pur_request.send_to_vendors)';
                }
                $this->db->where('(' . db_prefix() . 'pur_request.requester = '.get_staff_user_id().  $or_where. ')');
            }

            return $this->db->get(db_prefix().'pur_request')->result_array();
        }else{
            $this->db->where('id',$id);
            return $this->db->get(db_prefix().'pur_request')->row();
        }
    }

    /**
     * Gets the pur request detail.
     *
     * @param      <int>  $pur_request  The pur request
     *
     * @return     <array>  The pur request detail.
     */
    public function get_pur_request_detail($pur_request){
        $this->db->where('pur_request',$pur_request);
        $pur_request_lst = $this->db->get(db_prefix().'pur_request_detail')->result_array();

        foreach($pur_request_lst as $key => $detail){
            $pur_request_lst[$key]['into_money'] = (float) $detail['into_money'];
            $pur_request_lst[$key]['total'] = (float) $detail['total'];
            $pur_request_lst[$key]['unit_price'] = (float) $detail['unit_price'];
            $pur_request_lst[$key]['tax_value'] = (float) $detail['tax_value'];
        }

        return $pur_request_lst;
    }

    /**
     * Gets the pur request detail in estimate.
     *
     * @param      <int>  $pur_request  The pur request
     *
     * @return     <array>  The pur request detail in estimate.
     */
    public function get_pur_request_detail_in_estimate($pur_request){
        
        $pur_request_lst = $this->db->query('SELECT item_code, prq.unit_id as unit_id, unit_price, quantity, into_money, long_description as description, prq.tax as tax, tax_name, tax_rate, item_text, tax_value, total as total_money, total as total FROM '.db_prefix().'pur_request_detail prq LEFT JOIN '.db_prefix().'items it ON prq.item_code = it.id WHERE prq.pur_request = '.$pur_request)->result_array();

        foreach($pur_request_lst as $key => $detail){
            $pur_request_lst[$key]['into_money'] = (float) $detail['into_money'];
            $pur_request_lst[$key]['total'] = (float) $detail['total'];
            $pur_request_lst[$key]['total_money'] = (float) $detail['total_money'];
            $pur_request_lst[$key]['unit_price'] = (float) $detail['unit_price'];
            $pur_request_lst[$key]['tax_value'] = (float) $detail['tax_value'];
        }

        return $pur_request_lst;
    }


    /**
     * Gets the pur request detail in po.
     *
     * @param      <int>  $pur_request  The pur request
     *
     * @return     <array>  The pur request detail in po.
     */
    public function get_pur_request_detail_in_po($pur_request){
        
        $pur_request_lst = $this->db->query('SELECT item_code, prq.unit_id as unit_id, unit_price, quantity, into_money, long_description as description, prq.tax as tax, tax_name, tax_rate, item_text, tax_value, total as total_money, total as total FROM '.db_prefix().'pur_request_detail prq LEFT JOIN '.db_prefix().'items it ON prq.item_code = it.id WHERE prq.pur_request = '.$pur_request)->result_array();

        foreach($pur_request_lst as $key => $detail){
            $pur_request_lst[$key]['into_money'] = (float) $detail['into_money'];
            $pur_request_lst[$key]['total'] = (float) $detail['total'];
            $pur_request_lst[$key]['total_money'] = (float) $detail['total_money'];
            $pur_request_lst[$key]['unit_price'] = (float) $detail['unit_price'];
            $pur_request_lst[$key]['tax_value'] = (float) $detail['tax_value'];
        }

        return $pur_request_lst;
    }
    /**
     * Gets the pur estimate detail in order.
     *
     * @param      <int>  $pur_estimate  The pur estimate
     *
     * @return     <array>  The pur estimate detail in order.
     */
    public function get_pur_estimate_detail_in_order($pur_estimate){
        $estimates = $this->db->query('SELECT * FROM '.db_prefix().'pur_estimate_detail prq WHERE prq.pur_estimate = '.$pur_estimate)->result_array();

        foreach($estimates as $key => $detail){
            $estimates[$key]['discount_money'] = (float) $detail['discount_money'];
            $estimates[$key]['into_money'] = (float) $detail['into_money'];
            $estimates[$key]['total'] = (float) $detail['total'];
            $estimates[$key]['total_money'] = (float) $detail['total_money'];
            $estimates[$key]['unit_price'] = (float) $detail['unit_price'];
            $estimates[$key]['tax_value'] = (float) $detail['tax_value'];
        }

        return $estimates;
    }

    /**
     * Gets the pur estimate detail.
     *
     * @param      <int>  $pur_request  The pur request
     *
     * @return     <array>  The pur estimate detail.
     */
    public function get_pur_estimate_detail($pur_request){
        $this->db->where('pur_estimate',$pur_request);
        $estimate_details = $this->db->get(db_prefix().'pur_estimate_detail')->result_array();

        foreach($estimate_details as $key => $detail){
            $estimate_details[$key]['discount_money'] = (float) $detail['discount_money'];
            $estimate_details[$key]['into_money'] = (float) $detail['into_money'];
            $estimate_details[$key]['total'] = (float) $detail['total'];
            $estimate_details[$key]['total_money'] = (float) $detail['total_money'];
            $estimate_details[$key]['unit_price'] = (float) $detail['unit_price'];
            $estimate_details[$key]['tax_value'] = (float) $detail['tax_value'];
        }

        return $estimate_details;
    }

    /**
     * Gets the pur order detail.
     *
     * @param      <int>  $pur_request  The pur request
     *
     * @return     <array>  The pur order detail.
     */
    public function get_pur_order_detail($pur_request){
        $this->db->where('pur_order',$pur_request);
        $pur_order_details = $this->db->get(db_prefix().'pur_order_detail')->result_array();

        foreach($pur_order_details as $key => $detail){
            $pur_order_details[$key]['discount_money'] = (float) $detail['discount_money'];
            $pur_order_details[$key]['into_money'] = (float) $detail['into_money'];
            $pur_order_details[$key]['total'] = (float) $detail['total'];
            $pur_order_details[$key]['total_money'] = (float) $detail['total_money'];
            $pur_order_details[$key]['unit_price'] = (float) $detail['unit_price'];
            $pur_order_details[$key]['tax_value'] = (float) $detail['tax_value'];
        }

        return $pur_order_details;
    }

    /**
     * Gets the tax rate by identifier.
     */
    public function get_tax_rate_by_id($tax_ids){
        $rate_str = '';
        if($tax_ids != ''){
            $tax_ids = explode('|', $tax_ids);
            foreach($tax_ids as $key => $tax){
                $this->db->where('id', $tax);
                $tax_if = $this->db->get(db_prefix().'taxes')->row();
                if(($key + 1) < count($tax_ids)){
                    $rate_str .= $tax_if->taxrate.'|';
                }else{
                    $rate_str .= $tax_if->taxrate;
                }
            }
        }
        return $rate_str;
    }

    /**
     * Adds a pur request.
     *
     * @param      <array>   $data   The data
     *
     * @return     boolean  
     */
    public function add_pur_request($data){
        $data['request_date'] = date('Y-m-d H:i:s');
        $check_appr = $this->get_approve_setting('pur_request');
        $data['status'] = 1;
        if($check_appr && $check_appr != false){
            $data['status'] = 1;
        }else{
            $data['status'] = 2;
        }

        $detail_data = [];
        if(isset($data['newitems'])){
            $detail_data = $data['newitems'];
            unset($data['newitems']);
        }

        $data['to_currency'] = $data['currency'];

        unset($data['item_text']);
        unset($data['unit_price']);
        unset($data['quantity']);
        unset($data['into_money']);
        unset($data['tax_select']);
        unset($data['tax_value']);
        unset($data['total']);
        unset($data['item_select']);
        unset($data['item_code']);
        unset($data['unit_name']);
        unset($data['request_detail']);
        unset($data['unit_id']);
  

        if(isset($data['send_to_vendors']) && count($data['send_to_vendors']) > 0){
            $data['send_to_vendors'] = implode(',', $data['send_to_vendors']);
        }

        $data['subtotal'] = reformat_currency_pur($data['subtotal'], $data['currency']);

        if(isset($data['total_mn'])){
            $data['total'] = reformat_currency_pur($data['total_mn'], $data['currency']);    
            unset($data['total_mn']);
        }

        $data['total_tax'] = $data['total'] - $data['subtotal'];
       

        $dpm_name = department_pur_request_name($data['department']);
        $prefix = get_purchase_option('pur_request_prefix');

        $this->db->where('pur_rq_code',$data['pur_rq_code']);
        $check_exist_number = $this->db->get(db_prefix().'pur_request')->row();

        while($check_exist_number) {
          $data['number'] = $data['number'] + 1;
          $data['pur_rq_code'] =  $prefix.'-'.str_pad($data['number'],5,'0',STR_PAD_LEFT).'-'.date('M-Y').'-'.$dpm_name;
          $this->db->where('pur_rq_code',$data['pur_rq_code']);
          $check_exist_number = $this->db->get(db_prefix().'pur_request')->row();
        }

        $data['hash'] = app_generate_hash();
      
        $this->db->insert(db_prefix().'pur_request',$data);
        $insert_id = $this->db->insert_id();
        if($insert_id){

            // Update next purchase order number in settings
            $next_number = $data['number']+1;
            $this->db->where('option_name', 'next_pr_number');
            $this->db->update(db_prefix() . 'purchase_option',['option_val' =>  $next_number,]);

            if(count($detail_data) > 0){
                foreach($detail_data as $key => $rqd){
                    $dt_data = [];
                    $dt_data['pur_request'] = $insert_id;
                    $dt_data['item_code'] = $rqd['item_code'];
                    $dt_data['unit_id'] = isset($rqd['unit_id']) ? $rqd['unit_id'] : null;
                    $dt_data['unit_price'] = $rqd['unit_price'];
                    $dt_data['into_money'] = $rqd['into_money'];
                    $dt_data['total'] = $rqd['total'];
                    $dt_data['tax_value'] = $rqd['tax_value'];
                    $dt_data['item_text'] = nl2br($rqd['item_text']);

                    $tax_money = 0;
                    $tax_rate_value = 0;
                    $tax_rate = null;
                    $tax_id = null;
                    $tax_name = null;

                    if(isset($rqd['tax_select'])){
                        $tax_rate_data = $this->pur_get_tax_rate($rqd['tax_select']);
                        $tax_rate_value = $tax_rate_data['tax_rate'];
                        $tax_rate = $tax_rate_data['tax_rate_str'];
                        $tax_id = $tax_rate_data['tax_id_str'];
                        $tax_name = $tax_rate_data['tax_name_str'];
                    }

                    $dt_data['tax'] = $tax_id;
                    $dt_data['tax_rate'] = $tax_rate;
                    $dt_data['tax_name'] = $tax_name;

                    $dt_data['quantity'] = ($rqd['quantity'] != ''&& $rqd['quantity'] != null) ? $rqd['quantity'] : 0;

                    if($data['status'] == 2 && ($rqd['item_code'] == '' || $rqd['item_code'] == null) ){
                        $item_data['description'] = $rqd['item_text'];
                        $item_data['purchase_price'] = $rqd['unit_price'];
                        $item_data['unit_id'] = $rqd['unit_id'];
                        $item_data['rate'] = '';
                        $item_data['sku_code'] = '';
                        $item_data['commodity_barcode'] = $this->generate_commodity_barcode();
                        $item_data['commodity_code'] = $this->generate_commodity_barcode();
                        $item_id = $this->add_commodity_one_item($item_data);
                        if($item_id){
                           $dt_data['item_code'] = $item_id; 
                        }
                        
                    }

                    $this->db->insert(db_prefix().'pur_request_detail', $dt_data);
                }
                

            }

            return $insert_id;
        }
        return false;
    }

    /**
     * { update pur request }
     *
     * @param      <array>   $data   The data
     * @param      <int>   $id     The identifier
     *
     * @return     boolean   
     */
    public function update_pur_request($data,$id){
        $affectedRows = 0;
        $purq = $this->get_purchase_request($id);

        $data['subtotal'] = reformat_currency_pur($data['subtotal'], $data['currency']);

        $data['to_currency'] = $data['currency'];

        $new_purchase_request = [];
        if (isset($data['newitems'])) {
            $new_purchase_request = $data['newitems'];
            unset($data['newitems']);
        }

        $update_purchase_request = [];
        if (isset($data['items'])) {
            $update_purchase_request = $data['items'];
            unset($data['items']);
        }

        $remove_purchase_request = [];
        if (isset($data['removed_items'])) {
            $remove_purchase_request = $data['removed_items'];
            unset($data['removed_items']);
        }

        unset($data['item_text']);
        unset($data['unit_price']);
        unset($data['quantity']);
        unset($data['into_money']);
        unset($data['tax_select']);
        unset($data['tax_value']);
        unset($data['total']);
        unset($data['item_select']);
        unset($data['item_code']);
        unset($data['unit_name']);
        unset($data['request_detail']);
        unset($data['isedit']);
        unset($data['unit_id']);

        if(isset($data['send_to_vendors']) && count($data['send_to_vendors']) > 0){
            $data['send_to_vendors'] = implode(',', $data['send_to_vendors']);
        }

        if(isset($data['total_mn'])){
            $data['total'] = reformat_currency_pur($data['total_mn'], $data['currency']);    
            unset($data['total_mn']);
        }

        $data['total_tax'] = (float)$data['total'] -  (float)$data['subtotal'];

        if(isset($data['from_items'])){
            $data['from_items'] = 1;
        }else{
            $data['from_items'] = 0;
        }

        
        
        $this->db->where('id',$id);
        $this->db->update(db_prefix().'pur_request',$data);
        if($this->db->affected_rows() > 0){
            $affectedRows++;
        }

        if(count($new_purchase_request) > 0){
            foreach($new_purchase_request as $key => $rqd){
                $dt_data = [];
                $dt_data['pur_request'] = $id;
                $dt_data['item_code'] = $rqd['item_code'];
                $dt_data['unit_id'] = isset($rqd['unit_id']) ? $rqd['unit_id'] : null;
                $dt_data['unit_price'] = $rqd['unit_price'];
                $dt_data['into_money'] = $rqd['into_money'];
                $dt_data['total'] = $rqd['total'];
                $dt_data['tax_value'] = $rqd['tax_value'];
                $dt_data['item_text'] = nl2br($rqd['item_text']);

                $tax_money = 0;
                $tax_rate_value = 0;
                $tax_rate = null;
                $tax_id = null;
                $tax_name = null;

                if(isset($rqd['tax_select'])){
                    $tax_rate_data = $this->pur_get_tax_rate($rqd['tax_select']);
                    $tax_rate_value = $tax_rate_data['tax_rate'];
                    $tax_rate = $tax_rate_data['tax_rate_str'];
                    $tax_id = $tax_rate_data['tax_id_str'];
                    $tax_name = $tax_rate_data['tax_name_str'];
                }

                $dt_data['tax'] = $tax_id;
                $dt_data['tax_rate'] = $tax_rate;
                $dt_data['tax_name'] = $tax_name;

                $dt_data['quantity'] = ($rqd['quantity'] != ''&& $rqd['quantity'] != null) ? $rqd['quantity'] : 0;

                if($purq->status == 2 && ($rqd['item_code'] == '' || $rqd['item_code'] == null) ){
                    $item_data['description'] = $rqd['item_text'];
                    $item_data['purchase_price'] = $rqd['unit_price'];
                    $item_data['unit_id'] = $rqd['unit_id'];
                    $item_data['rate'] = '';
                    $item_data['sku_code'] = '';
                    $item_data['commodity_barcode'] = $this->generate_commodity_barcode();
                    $item_data['commodity_code'] = $this->generate_commodity_barcode();
                    $item_id = $this->add_commodity_one_item($item_data);
                    if($item_id){
                       $rq_detail[$key]['item_code'] = $item_id; 
                    }
                }

                $_new_detail_id = $this->db->insert(db_prefix().'pur_request_detail', $dt_data);
                if($_new_detail_id){
                    $affectedRows++;
                }
            }
        }

        if(count($update_purchase_request) > 0){
            foreach($update_purchase_request as $_key => $rqd){
                $dt_data = [];
                $dt_data['pur_request'] = $id;
                $dt_data['item_code'] = $rqd['item_code'];
                $dt_data['unit_id'] = isset($rqd['unit_id']) ? $rqd['unit_id'] : null;
                $dt_data['unit_price'] = $rqd['unit_price'];
                $dt_data['into_money'] = $rqd['into_money'];
                $dt_data['total'] = $rqd['total'];
                $dt_data['tax_value'] = $rqd['tax_value'];
                $dt_data['item_text'] = nl2br($rqd['item_text']);

                $tax_money = 0;
                $tax_rate_value = 0;
                $tax_rate = null;
                $tax_id = null;
                $tax_name = null;

                if(isset($rqd['tax_select'])){
                    $tax_rate_data = $this->pur_get_tax_rate($rqd['tax_select']);
                    $tax_rate_value = $tax_rate_data['tax_rate'];
                    $tax_rate = $tax_rate_data['tax_rate_str'];
                    $tax_id = $tax_rate_data['tax_id_str'];
                    $tax_name = $tax_rate_data['tax_name_str'];
                }

                $dt_data['tax'] = $tax_id;
                $dt_data['tax_rate'] = $tax_rate;
                $dt_data['tax_name'] = $tax_name;

                $dt_data['quantity'] = ($rqd['quantity'] != ''&& $rqd['quantity'] != null) ? $rqd['quantity'] : 0;

                if($purq->status == 2 && ($rqd['item_code'] == '' || $rqd['item_code'] == null) ){
                    $item_data['description'] = $rqd['item_text'];
                    $item_data['purchase_price'] = $rqd['unit_price'];
                    $item_data['unit_id'] = $rqd['unit_id'];
                    $item_data['rate'] = '';
                    $item_data['sku_code'] = '';
                    $item_data['commodity_barcode'] = $this->generate_commodity_barcode();
                    $item_data['commodity_code'] = $this->generate_commodity_barcode();
                    $item_id = $this->add_commodity_one_item($item_data);
                    if($item_id){
                       $dt_data['item_code'] = $item_id; 
                    }
                }

                $this->db->where('prd_id', $rqd['id']);
                $this->db->update(db_prefix().'pur_request_detail', $dt_data);
                if($this->db->affected_rows() > 0){
                    $affectedRows++;
                }
            }
        }

        if(count($remove_purchase_request) > 0){ 
            foreach($remove_purchase_request as $remove_id){
                $this->db->where('prd_id', $remove_id);
                if ($this->db->delete(db_prefix() . 'pur_request_detail')) {
                    $affectedRows++;
                }
            }
        }


        if($affectedRows > 0){
            return true;
        }
        return false;
    }

    /**
     * { delete pur request }
     *
     * @param      <int>   $id     The identifier
     *
     * @return     boolean  
     */
    public function delete_pur_request($id){
        $affectedRows = 0;
        $this->db->where('id',$id);
        $this->db->delete(db_prefix().'pur_request');
        if($this->db->affected_rows() > 0){
            $affectedRows++;
        }

        $this->db->where('rel_id',$id);
        $this->db->where('rel_type','pur_request');
        $this->db->delete(db_prefix().'files');
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        if (is_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_request/'. $id)) {
            delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_request/'. $id);
        }

        $this->db->where('pur_request',$id);
        $this->db->delete(db_prefix().'pur_request_detail');
        if($this->db->affected_rows() > 0){
            $affectedRows++;
        }

         if($affectedRows > 0){
            return true;
        }
        return false;
    }

    /**
     * { change status pur request }
     *
     * @param      <type>   $status  The status
     * @param      <type>   $id      The identifier
     *
     * @return     boolean 
     */
    public function change_status_pur_request($status,$id){
        if($status == 2){
            $this->update_item_pur_request($id);
        }

        $this->db->where('id',$id);
        $this->db->update(db_prefix().'pur_request',['status' => $status]);
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }

    /**
     * Gets the pur request by status.
     *
     * @param      <type>  $status  The status
     *
     * @return     <array>  The pur request by status.
     */
    public function get_pur_request_by_status($status){
        

        if(has_permission('purchase_request', '', 'view_own') && !is_admin() && is_staff_logged_in()){
            $or_where = '';
            $list_vendor = get_vendor_admin_list(get_staff_user_id());
            foreach($list_vendor as $vendor_id){
                $or_where .= ' OR find_in_set('.$vendor_id.', ' . db_prefix() . 'pur_request.send_to_vendors)';
            }
            $this->db->where('(' . db_prefix() . 'pur_request.requester = '.get_staff_user_id().  $or_where. ')');
        }
        $this->db->where('status',$status);

        return $this->db->get(db_prefix().'pur_request')->result_array();
    }

    /**
     * { function_description }
     *
     * @param      <type>  $data   The data
     *
     * @return     <array> data
     */
    private function map_shipping_columns($data)
    {
        if (!isset($data['include_shipping'])) {
            foreach ($this->shipping_fields as $_s_field) {
                if (isset($data[$_s_field])) {
                    $data[$_s_field] = null;
                }
            }
            $data['show_shipping_on_estimate'] = 1;
            $data['include_shipping']          = 0;
        } else {
            $data['include_shipping'] = 1;
            // set by default for the next time to be checked
            if (isset($data['show_shipping_on_estimate']) && ($data['show_shipping_on_estimate'] == 1 || $data['show_shipping_on_estimate'] == 'on')) {
                $data['show_shipping_on_estimate'] = 1;
            } else {
                $data['show_shipping_on_estimate'] = 0;
            }
        }

        return $data;
    }

    /**
     * Gets the estimate.
     *
     * @param      string  $id     The identifier
     * @param      array   $where  The where
     *
     * @return     <row , array>  The estimate, list estimate.
     */
    public function get_estimate($id = '', $where = [])
    {
        $this->db->select('*,' . db_prefix() . 'currencies.id as currencyid, ' . db_prefix() . 'pur_estimates.id as id, '.db_prefix().'pur_estimates.currency as currency , ' . db_prefix() . 'currencies.name as currency_name');
        $this->db->from(db_prefix() . 'pur_estimates');
        $this->db->join(db_prefix() . 'currencies', db_prefix() . 'currencies.id = ' . db_prefix() . 'pur_estimates.currency', 'left');
        $this->db->where($where);
        if (is_numeric($id)) {
            $this->db->where(db_prefix() . 'pur_estimates.id', $id);
            $estimate = $this->db->get()->row();
            if ($estimate) {
                
                $estimate->visible_attachments_to_customer_found = false;
                
                $estimate->items = get_items_by_type('pur_estimate', $id);

                if ($estimate->pur_request != 0) {
                   
                    $estimate->pur_request = $this->get_purchase_request($estimate->pur_request);
                }else{
                    $estimate->pur_request = '';
                }

                $estimate->vendor = $this->get_vendor($estimate->vendor);
                if (!$estimate->vendor) {
                    $estimate->vendor          = new stdClass();
                    $estimate->vendor->company = $estimate->deleted_customer_name;
                }
            }

            return $estimate;
        }
        $this->db->order_by('number,YEAR(date)', 'desc');

        return $this->db->get()->result_array();
    }

    /**
     * Gets the pur order.
     *
     * @param      <int>  $id     The identifier
     *
     * @return     <row>  The pur order.
     */
    public function get_pur_order($id){
        $this->db->where('id',$id);
        return $this->db->get(db_prefix().'pur_orders')->row();
    }


    /**
     * Adds an estimate.
     *
     * @param      <type>   $data   The data
     *
     * @return     boolean  or in estimate
     */
    public function add_estimate($data)
    {   

        unset($data['item_select']);
        unset($data['item_name']);
        unset($data['total']);
        unset($data['quantity']);
        unset($data['unit_price']);
        unset($data['unit_name']);
        unset($data['item_code']);
        unset($data['unit_id']);
        unset($data['discount']);
        unset($data['into_money']);
        unset($data['tax_rate']);
        unset($data['tax_name']);
        unset($data['discount_money']);
        unset($data['total_money']);
        unset($data['additional_discount']);
        unset($data['tax_value']);
        if(isset($data['tax_select'])){
            unset($data['tax_select']);
        }

        $check_appr = $this->get_approve_setting('pur_quotation');
        $data['status'] = 1;
        if($check_appr && $check_appr != false){
            $data['status'] = 1;
        }else{
            $data['status'] = 2;
        }

        $data['to_currency'] = $data['currency'];

        $data['date'] = to_sql_date($data['date']);
        $data['expirydate'] = to_sql_date($data['expirydate']);

        $data['datecreated'] = date('Y-m-d H:i:s');

        $data['addedfrom'] = get_staff_user_id();

        $data['prefix'] = get_option('estimate_prefix');

        $data['number_format'] = get_option('estimate_number_format');

        $this->db->where('prefix',$data['prefix']);
        $this->db->where('number',$data['number']);
        $check_exist_number = $this->db->get(db_prefix().'pur_estimates')->row();

        while($check_exist_number) {
          $data['number'] = $data['number'] + 1;
          
          $this->db->where('prefix',$data['prefix']);
          $this->db->where('number',$data['number']);
          $check_exist_number = $this->db->get(db_prefix().'pur_estimates')->row();
        }

        $save_and_send = isset($data['save_and_send']);

        $data['hash'] = app_generate_hash();

        $data = $this->map_shipping_columns($data);

        $es_detail = [];
        if (isset($data['newitems'])) {
            $es_detail = $data['newitems'];
            unset($data['newitems']);
        }

        if (isset($data['shipping_street'])) {
            $data['shipping_street'] = trim($data['shipping_street']);
            $data['shipping_street'] = nl2br($data['shipping_street']);
        }

        if(isset($data['dc_total'])){
            $data['discount_total'] = $data['dc_total'];
            unset($data['dc_total']);
        }

        if(isset($data['dc_percent'])){
            $data['discount_percent'] = $data['dc_percent'];
            unset($data['dc_percent']);
        }
        
        if(isset($data['total_mn'])){
            $data['subtotal'] = $data['total_mn'];
            unset($data['total_mn']);
        }

        if(isset($data['grand_total'])){
            $data['total'] = $data['grand_total'];
            unset($data['grand_total']);
        }

        $this->db->insert(db_prefix() . 'pur_estimates', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            $total = [];
            $total['total_tax'] = 0;
    
            if(count($es_detail) > 0){
                foreach($es_detail as $key => $rqd){
   
                    $dt_data = [];
                    $dt_data['pur_estimate'] = $insert_id;
                    $dt_data['item_code'] = $rqd['item_code'];
                    $dt_data['unit_id'] = isset($rqd['unit_id']) ? $rqd['unit_id'] : null;
                    $dt_data['unit_price'] = $rqd['unit_price'];
                    $dt_data['into_money'] = $rqd['into_money'];
                    $dt_data['total'] = $rqd['total'];
                    $dt_data['tax_value'] = $rqd['tax_value'];
                    $dt_data['item_name'] = $rqd['item_name'];
                    $dt_data['total_money'] = $rqd['total_money'];
                    $dt_data['discount_money'] = $rqd['discount_money'];
                    $dt_data['discount_%'] = $rqd['discount'];

                    $tax_money = 0;
                    $tax_rate_value = 0;
                    $tax_rate = null;
                    $tax_id = null;
                    $tax_name = null;

                    if(isset($rqd['tax_select'])){
                        $tax_rate_data = $this->pur_get_tax_rate($rqd['tax_select']);
                        $tax_rate_value = $tax_rate_data['tax_rate'];
                        $tax_rate = $tax_rate_data['tax_rate_str'];
                        $tax_id = $tax_rate_data['tax_id_str'];
                        $tax_name = $tax_rate_data['tax_name_str'];
                    }

                    $dt_data['tax'] = $tax_id;
                    $dt_data['tax_rate'] = $tax_rate;
                    $dt_data['tax_name'] = $tax_name;

                    $dt_data['quantity'] = ($rqd['quantity'] != ''&& $rqd['quantity'] != null) ? $rqd['quantity'] : 0;

                    $this->db->insert(db_prefix().'pur_estimate_detail', $dt_data);


                    $total['total_tax'] += $rqd['tax_value'];
                }
            }

            $this->db->where('id',$insert_id);
            $this->db->update(db_prefix().'pur_estimates',$total);

            if(is_numeric($data['buyer']) && $data['buyer'] > 0){
                $notified = add_notification([
                'description'     => _l('purchase_quotation_added', format_pur_estimate_number($insert_id)),
                'touserid'        => $data['buyer'],
                'link'            => 'purchase/quotations/'.$insert_id,
                'additional_data' => serialize([
                    format_pur_estimate_number($insert_id),
                ]),
                ]);
                if ($notified) {
                    pusher_trigger_notification([$data['buyer']]);
                }
            }

            return $insert_id;
        }

        return false;
    }

    /**
     * { update estimate }
     *
     * @param      <type>   $data   The data
     * @param      <type>   $id     The identifier
     *
     * @return     boolean  
     */
    public function update_estimate($data, $id)
    {
        $data['date'] = to_sql_date($data['date']);
        $data['expirydate'] = to_sql_date($data['expirydate']);
        $affectedRows = 0;

        $new_quote = [];
        if (isset($data['newitems'])) {
            $new_quote = $data['newitems'];
            unset($data['newitems']);
        }

        $update_quote = [];
        if (isset($data['items'])) {
            $update_quote = $data['items'];
            unset($data['items']);
        }

        $remove_quote = [];
        if (isset($data['removed_items'])) {
            $remove_quote = $data['removed_items'];
            unset($data['removed_items']);
        }

        $data['to_currency'] = $data['currency'];

        unset($data['item_select']);
        unset($data['item_name']);
        unset($data['total']);
        unset($data['quantity']);
        unset($data['unit_price']);
        unset($data['unit_name']);
        unset($data['item_code']);
        unset($data['unit_id']);
        unset($data['discount']);
        unset($data['into_money']);
        unset($data['tax_rate']);
        unset($data['tax_name']);
        unset($data['discount_money']);
        unset($data['total_money']);
        unset($data['additional_discount']);
        unset($data['tax_value']);

        if(isset($data['tax_select'])){
            unset($data['tax_select']);
        }

        $data['number'] = trim($data['number']);

        $original_estimate = $this->get_estimate($id);

        $original_status = $original_estimate->status;

        $original_number = $original_estimate->number;

        $original_number_formatted = format_estimate_number($id);

        $data = $this->map_shipping_columns($data);
        
        unset($data['isedit']);


        if(isset($data['total_mn'])){
            $data['subtotal'] = $data['total_mn'];
            unset($data['total_mn']);
        }

        if(isset($data['grand_total'])){
            $data['total'] = $data['grand_total'];
            unset($data['grand_total']);
        }

        if(isset($data['dc_total'])){
            $data['discount_total'] = $data['dc_total'];
            unset($data['dc_total']);
        }


        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'pur_estimates', $data);

        if ($this->db->affected_rows() > 0) {
            if ($original_status != $data['status']) {
                if ($data['status'] == 2) {
                    $this->db->where('id', $id);
                    $this->db->update(db_prefix() . 'pur_estimates', ['sent' => 1, 'datesend' => date('Y-m-d H:i:s')]);
                }
            }
            $affectedRows++;
        }

        if(count($new_quote) > 0){
            foreach($new_quote as $key => $rqd){

                $dt_data = [];
                $dt_data['pur_estimate'] = $id;
                $dt_data['item_code'] = $rqd['item_code'];
                $dt_data['unit_id'] = isset($rqd['unit_id']) ? $rqd['unit_id'] : null;
                $dt_data['unit_price'] = $rqd['unit_price'];
                $dt_data['into_money'] = $rqd['into_money'];
                $dt_data['total'] = $rqd['total'];
                $dt_data['tax_value'] = $rqd['tax_value'];
                $dt_data['item_name'] = $rqd['item_name'];
                $dt_data['total_money'] = $rqd['total_money'];
                $dt_data['discount_money'] = $rqd['discount_money'];
                $dt_data['discount_%'] = $rqd['discount'];

                $tax_money = 0;
                $tax_rate_value = 0;
                $tax_rate = null;
                $tax_id = null;
                $tax_name = null;

                if(isset($rqd['tax_select'])){
                    $tax_rate_data = $this->pur_get_tax_rate($rqd['tax_select']);
                    $tax_rate_value = $tax_rate_data['tax_rate'];
                    $tax_rate = $tax_rate_data['tax_rate_str'];
                    $tax_id = $tax_rate_data['tax_id_str'];
                    $tax_name = $tax_rate_data['tax_name_str'];
                }

                $dt_data['tax'] = $tax_id;
                $dt_data['tax_rate'] = $tax_rate;
                $dt_data['tax_name'] = $tax_name;

                $dt_data['quantity'] = ($rqd['quantity'] != ''&& $rqd['quantity'] != null) ? $rqd['quantity'] : 0;

                $this->db->insert(db_prefix().'pur_estimate_detail', $dt_data);
                $new_quote_insert_id = $this->db->insert_id();
                if($new_quote_insert_id){
                    $affectedRows++;
                }
                
            }

        }

        if(count($update_quote) > 0){
            foreach($update_quote as $_key => $rqd){
                $dt_data = [];
                $dt_data['pur_estimate'] = $id;
                $dt_data['item_code'] = $rqd['item_code'];
                $dt_data['unit_id'] = isset($rqd['unit_id']) ? $rqd['unit_id'] : null;
                $dt_data['unit_price'] = $rqd['unit_price'];
                $dt_data['into_money'] = $rqd['into_money'];
                $dt_data['total'] = $rqd['total'];
                $dt_data['tax_value'] = $rqd['tax_value'];
                $dt_data['item_name'] = $rqd['item_name'];
                $dt_data['total_money'] = $rqd['total_money'];
                $dt_data['discount_money'] = $rqd['discount_money'];
                $dt_data['discount_%'] = $rqd['discount'];

                $tax_money = 0;
                $tax_rate_value = 0;
                $tax_rate = null;
                $tax_id = null;
                $tax_name = null;

                if(isset($rqd['tax_select'])){
                    $tax_rate_data = $this->pur_get_tax_rate($rqd['tax_select']);
                    $tax_rate_value = $tax_rate_data['tax_rate'];
                    $tax_rate = $tax_rate_data['tax_rate_str'];
                    $tax_id = $tax_rate_data['tax_id_str'];
                    $tax_name = $tax_rate_data['tax_name_str'];
                }

                $dt_data['tax'] = $tax_id;
                $dt_data['tax_rate'] = $tax_rate;
                $dt_data['tax_name'] = $tax_name;

                $dt_data['quantity'] = ($rqd['quantity'] != ''&& $rqd['quantity'] != null) ? $rqd['quantity'] : 0;

                $this->db->where('id', $rqd['id']);
                $this->db->update(db_prefix().'pur_estimate_detail', $dt_data);
                if($this->db->affected_rows() > 0){
                    $affectedRows++;
                }
            }
        }

        if(count($remove_quote) > 0){ 
            foreach($remove_quote as $remove_id){
                $this->db->where('id', $remove_id);
                if ($this->db->delete(db_prefix() . 'pur_estimate_detail')) {
                    $affectedRows++;
                }
            }
        }

        $quote_detail_after_update = $this->get_pur_estimate_detail($id);
        $total = [];
        $total['total_tax'] = 0;
        if(count($quote_detail_after_update) > 0){
            foreach($quote_detail_after_update as $dt){
                $total['total_tax'] += $dt['tax_value'];
            }
        }

        $this->db->where('id',$id);
        $this->db->update(db_prefix().'pur_estimates',$total);
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }
        
        if ($affectedRows > 0) {
            if(is_numeric($data['buyer']) && $data['buyer'] > 0){
                $notified = add_notification([
                'description'     => _l('purchase_quotation_updated', format_pur_estimate_number($id)),
                'touserid'        => $data['buyer'],
                'link'            => 'purchase/quotations/'.$id,
                'additional_data' => serialize([
                    format_pur_estimate_number($id),
                ]),
                ]);
                if ($notified) {
                    pusher_trigger_notification([$data['buyer']]);
                }
            }


            return true;
        }

        return false;
    }

    /**
     * Gets the estimate item.
     *
     * @param      <type>  $id     The identifier
     *
     * @return     <row>  The estimate item.
     */
    public function get_estimate_item($id)
    {
        $this->db->where('id', $id);

        return $this->db->get(db_prefix() . 'itemable')->row();
    }

    /**
     * { delete estimate }
     *
     * @param      string   $id            The identifier
     * @param      boolean  $simpleDelete  The simple delete
     *
     * @return     boolean  ( description_of_the_return_value )
     */
    public function delete_estimate($id, $simpleDelete = false)
    {
        
        
        hooks()->do_action('before_estimate_deleted', $id);

        $number = format_estimate_number($id);

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'pur_estimates');

        if ($this->db->affected_rows() > 0) {
           
            $this->db->where('pur_estimate', $id);
            $this->db->delete(db_prefix() . 'pur_estimate_detail');

            $this->db->where('relid IN (SELECT id from ' . db_prefix() . 'itemable WHERE rel_type="pur_estimate" AND rel_id="' . $id . '")');
            $this->db->where('fieldto', 'items');
            $this->db->delete(db_prefix() . 'customfieldsvalues');

            $this->db->where('rel_type', 'pur_estimate');
            $this->db->where('rel_id', $id);
            $this->db->delete(db_prefix() . 'taggables');

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'pur_estimate');
            $this->db->delete(db_prefix() . 'itemable');

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'pur_estimate');
            $this->db->delete(db_prefix() . 'item_tax');

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'pur_estimate');
            $this->db->delete(db_prefix() . 'sales_activity');

            return true;
        }

        return false;
    }

    /**
     * Gets the taxes.
     *
     * @return     <array>  The taxes.
     */
    public function get_taxes()
    {
       return $this->db->query('select id, CONCAT(name, "(", taxrate,"%)") as label, taxrate from '.db_prefix().'taxes')->result_array();
    }

    /**
     * Gets the total tax.
     *
     * @param      <type>   $taxes  The taxes
     *
     * @return     integer  The total tax.
     */
    public function get_total_tax($taxes){
        $rs = 0;
        foreach($taxes as $tax){
            $this->db->where('id',$tax);
            $this->db->select('taxrate');
            $ta = $this->db->get(db_prefix().'taxes')->row();
            $rs += $ta->taxrate;
        }
        return $rs;
    }

    /**
     * { change status pur estimate }
     *
     * @param      <type>   $status  The status
     * @param      <type>   $id      The identifier
     *
     * @return     boolean   
     */
    public function change_status_pur_estimate($status,$id){
        $this->db->where('id',$id);
        $this->db->update(db_prefix().'pur_estimates',['status' => $status]);
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }

    /**
     * { change status pur order }
     *
     * @param      <type>   $status  The status
     * @param      <type>   $id      The identifier
     *
     * @return     boolean  ( description_of_the_return_value )
     */
    public function change_status_pur_order($status,$id){
        $this->db->where('id',$id);
        $this->db->update(db_prefix().'pur_orders',['approve_status' => $status]);
        if($this->db->affected_rows() > 0){

            hooks()->do_action('after_purchase_order_approve', $id);

            // hooks()->apply_filters('create_goods_receipt',['status' => $status,'id' => $id]);
            return true;
        }
        return false;
    }

    /**
     * Gets the estimates by status.
     *
     * @param      <type>  $status  The status
     *
     * @return     <array>  The estimates by status.
     */
    public function get_estimates_by_status($status){
        $this->db->where('status',$status);
        if(!has_permission('purchase_quotations', '', 'view') && is_staff_logged_in()){
            $this->db->where('(' . db_prefix() . 'pur_estimates.addedfrom = '.get_staff_user_id().' OR ' . db_prefix() . 'pur_estimates.buyer = '.get_staff_user_id().' OR ' . db_prefix() . 'pur_estimates.vendor IN (SELECT vendor_id FROM ' . db_prefix() . 'pur_vendor_admin WHERE staff_id=' . get_staff_user_id() . '))');
        }

        return $this->db->get(db_prefix().'pur_estimates')->result_array();
    }

    /**
     * { estimate by vendor }
     *
     * @param      <type>  $vendor  The vendor
     *
     * @return     <array>  ( list estimate by vendor )
     */
    public function estimate_by_vendor($vendor){
        $this->db->where('vendor',$vendor);
        $this->db->where('status', 2);
        if(!has_permission('purchase_quotations', '', 'view') && is_staff_logged_in()){
            $this->db->where('(' . db_prefix() . 'pur_estimates.addedfrom = '.get_staff_user_id().' OR ' . db_prefix() . 'pur_estimates.buyer = '.get_staff_user_id().' OR ' . db_prefix() . 'pur_estimates.vendor IN (SELECT vendor_id FROM ' . db_prefix() . 'pur_vendor_admin WHERE staff_id=' . get_staff_user_id() . '))');
        }
        return $this->db->get(db_prefix().'pur_estimates')->result_array();
    }

    /**
     * Adds a pur order.
     *
     * @param      <array>   $data   The data
     *
     * @return     boolean , int id purchase order
     */
    public function add_pur_order($data){

        unset($data['item_select']);
        unset($data['item_name']);
        unset($data['description']);
        unset($data['total']);
        unset($data['quantity']);
        unset($data['unit_price']);
        unset($data['unit_name']);
        unset($data['item_code']);
        unset($data['unit_id']);
        unset($data['discount']);
        unset($data['into_money']);
        unset($data['tax_rate']);
        unset($data['tax_name']);
        unset($data['discount_money']);
        unset($data['total_money']);
        unset($data['additional_discount']);
        unset($data['tax_value']);
        if(isset($data['tax_select'])){
            unset($data['tax_select']);
        }

        $check_appr = $this->get_approve_setting('pur_order');
        $data['approve_status'] = 1;
        if($check_appr && $check_appr != false){
            $data['approve_status'] = 1;
        }else{
            $data['approve_status'] = 2;
        }

        $data['to_currency'] = $data['currency'];

        $order_detail = [];
        if(isset($data['newitems'])){
            $order_detail = $data['newitems'];
            unset($data['newitems']);
        }

        $prefix = get_purchase_option('pur_order_prefix');

        $this->db->where('pur_order_number',$data['pur_order_number']);
        $check_exist_number = $this->db->get(db_prefix().'pur_orders')->row();

        while($check_exist_number) {
          $data['number'] = $data['number'] + 1;
          $data['pur_order_number'] =  $prefix.'-'.str_pad($data['number'],5,'0',STR_PAD_LEFT).'-'.date('M-Y').'-'.get_vendor_company_name($data['vendor']);
          if(get_option('po_only_prefix_and_number') == 1){
            $data['pur_order_number'] =  $prefix.'-'.str_pad($data['number'],5,'0',STR_PAD_LEFT);
          }

          $this->db->where('pur_order_number',$data['pur_order_number']);
          $check_exist_number = $this->db->get(db_prefix().'pur_orders')->row();
        }

        $data['order_date'] = to_sql_date($data['order_date']);

        $data['delivery_date'] = to_sql_date($data['delivery_date']);

        $data['datecreated'] = date('Y-m-d H:i:s');

        $data['addedfrom'] = get_staff_user_id();

        $data['hash'] = app_generate_hash();

        $data['order_status'] = 'new';

        if(isset($data['clients']) && count($data['clients']) > 0){
            $data['clients'] = implode(',', $data['clients']);
        } 

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        $tags = '';
        if (isset($data['tags'])) {
            $tags = $data['tags'];
            unset($data['tags']);
        }

        if(isset($data['order_discount'])){
            $order_discount = $data['order_discount'];
            if($data['add_discount_type'] == 'percent'){
                $data['discount_percent'] = $order_discount;
            }

            unset($data['order_discount']);
        }

        unset($data['add_discount_type']);

        if(isset($data['dc_total'])){
            $data['discount_total'] = $data['dc_total'];
            unset($data['dc_total']);
        }

        if(isset($data['total_mn'])){
            $data['subtotal'] = $data['total_mn'];
            unset($data['total_mn']);
        }

        if(isset($data['grand_total'])){
            $data['total'] = $data['grand_total'];
            unset($data['grand_total']);
        }

        $this->db->insert(db_prefix() . 'pur_orders', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            // Update next purchase order number in settings
            $next_number = $data['number']+1;
            $this->db->where('option_name', 'next_po_number');
            $this->db->update(db_prefix() . 'purchase_option',['option_val' =>  $next_number,]);

            $total = [];
            $total['total_tax'] = 0;
            
            if(count($order_detail) > 0){
                foreach($order_detail as $key => $rqd){ 
                    $dt_data = [];
                    $dt_data['pur_order'] = $insert_id;
                    $dt_data['item_code'] = $rqd['item_code'];
                    $dt_data['unit_id'] = isset($rqd['unit_id']) ? $rqd['unit_id'] : null;
                    $dt_data['unit_price'] = $rqd['unit_price'];
                    $dt_data['into_money'] = $rqd['into_money'];
                    $dt_data['total'] = $rqd['total'];
                    $dt_data['tax_value'] = $rqd['tax_value'];
                    $dt_data['item_name'] = $rqd['item_name'];
                    $dt_data['description'] = nl2br($rqd['item_description']);
                    $dt_data['total_money'] = $rqd['total_money'];
                    $dt_data['discount_money'] = $rqd['discount_money'];
                    $dt_data['discount_%'] = $rqd['discount'];

                    $tax_money = 0;
                    $tax_rate_value = 0;
                    $tax_rate = null;
                    $tax_id = null;
                    $tax_name = null;

                    if(isset($rqd['tax_select'])){
                        $tax_rate_data = $this->pur_get_tax_rate($rqd['tax_select']);
                        $tax_rate_value = $tax_rate_data['tax_rate'];
                        $tax_rate = $tax_rate_data['tax_rate_str'];
                        $tax_id = $tax_rate_data['tax_id_str'];
                        $tax_name = $tax_rate_data['tax_name_str'];
                    }

                    $dt_data['tax'] = $tax_id;
                    $dt_data['tax_rate'] = $tax_rate;
                    $dt_data['tax_name'] = $tax_name;

                    $dt_data['quantity'] = ($rqd['quantity'] != ''&& $rqd['quantity'] != null) ? $rqd['quantity'] : 0;

                    $this->db->insert(db_prefix().'pur_order_detail', $dt_data);


                   
                }
            }

            handle_tags_save($tags, $insert_id, 'pur_order');

            if (isset($custom_fields)) {

                handle_custom_fields_post($insert_id, $custom_fields);
            }

            $_taxes = $this->get_html_tax_pur_order($insert_id);
            foreach($_taxes['taxes_val'] as $tax_val){
                $total['total_tax'] += $tax_val;
            }

            $this->db->where('id',$insert_id);
            $this->db->update(db_prefix().'pur_orders',$total);

            // warehouse module hook after purchase order add
            hooks()->do_action('after_purchase_order_add', $insert_id);

            return $insert_id;
        }

        return false;
    }

    /**
     * { update pur order }
     *
     * @param      <type>   $data   The data
     * @param      <type>   $id     The identifier
     *
     * @return     boolean 
     */
    public function update_pur_order($data, $id)
    {
        $affectedRows = 0;

        unset($data['item_select']);
        unset($data['item_name']);
        unset($data['description']);
        unset($data['total']);
        unset($data['quantity']);
        unset($data['unit_price']);
        unset($data['unit_name']);
        unset($data['item_code']);
        unset($data['unit_id']);
        unset($data['discount']);
        unset($data['into_money']);
        unset($data['tax_rate']);
        unset($data['tax_name']);
        unset($data['discount_money']);
        unset($data['total_money']);
        unset($data['additional_discount']);
        unset($data['tax_value']);
        unset($data['isedit']);
        if(isset($data['tax_select'])){
            unset($data['tax_select']);
        }

        $new_order = [];
        if(isset($data['newitems'])){
            $new_order = $data['newitems'];
            unset($data['newitems']);
        }

        $update_order = [];
        if(isset($data['items'])) {
            $update_order = $data['items'];
            unset($data['items']);
        }

        $remove_order = [];
        if(isset($data['removed_items'])){
            $remove_order = $data['removed_items'];
            unset($data['removed_items']);
        }

        $data['to_currency'] = $data['currency'];

        $prefix = get_purchase_option('pur_order_prefix');
        $data['pur_order_number'] = $data['pur_order_number'];

        $data['order_date'] = to_sql_date($data['order_date']);

        $data['delivery_date'] = to_sql_date($data['delivery_date']);

        $data['datecreated'] = date('Y-m-d H:i:s');

        $data['addedfrom'] = get_staff_user_id();

        if(isset($data['clients']) && count($data['clients']) > 0){
            $data['clients'] = implode(',', $data['clients']);
        }

        if(isset($data['order_discount'])){
            $order_discount = $data['order_discount'];
            if($data['add_discount_type'] == 'percent'){
                $data['discount_percent'] = $order_discount;
            }

            unset($data['order_discount']);
        }

        unset($data['add_discount_type']);

        if(isset($data['dc_total'])){
            $data['discount_total'] = $data['dc_total'];
            unset($data['dc_total']);
        }

        if(isset($data['total_mn'])){
            $data['subtotal'] = $data['total_mn'];
            unset($data['total_mn']);
        }

        if(isset($data['grand_total'])){
            $data['total'] = $data['grand_total'];
            unset($data['grand_total']);
        }

        if (isset($data['tags'])) {
            if (handle_tags_save($data['tags'], $id, 'pur_order')) {
                $affectedRows++;
            }
            unset($data['tags']);
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'pur_orders', $data);

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        if(count($new_order) > 0){
            foreach($new_order as $key => $rqd){

                $dt_data = [];
                $dt_data['pur_order'] = $id;
                $dt_data['item_code'] = $rqd['item_code'];
                $dt_data['unit_id'] = isset($rqd['unit_id']) ? $rqd['unit_id'] : null;
                $dt_data['unit_price'] = $rqd['unit_price'];
                $dt_data['into_money'] = $rqd['into_money'];
                $dt_data['total'] = $rqd['total'];
                $dt_data['tax_value'] = $rqd['tax_value'];
                $dt_data['item_name'] = $rqd['item_name'];
                $dt_data['total_money'] = $rqd['total_money'];
                $dt_data['discount_money'] = $rqd['discount_money'];
                $dt_data['discount_%'] = $rqd['discount'];
                $dt_data['description'] = nl2br($rqd['item_description']);

                $tax_money = 0;
                $tax_rate_value = 0;
                $tax_rate = null;
                $tax_id = null;
                $tax_name = null;

                if(isset($rqd['tax_select'])){
                    $tax_rate_data = $this->pur_get_tax_rate($rqd['tax_select']);
                    $tax_rate_value = $tax_rate_data['tax_rate'];
                    $tax_rate = $tax_rate_data['tax_rate_str'];
                    $tax_id = $tax_rate_data['tax_id_str'];
                    $tax_name = $tax_rate_data['tax_name_str'];
                }

                $dt_data['tax'] = $tax_id;
                $dt_data['tax_rate'] = $tax_rate;
                $dt_data['tax_name'] = $tax_name;

                $dt_data['quantity'] = ($rqd['quantity'] != ''&& $rqd['quantity'] != null) ? $rqd['quantity'] : 0;

                $this->db->insert(db_prefix().'pur_order_detail', $dt_data);
                $new_quote_insert_id = $this->db->insert_id();
                if($new_quote_insert_id){
                    $affectedRows++;
                }
                
            }

        }

        if(count($update_order) > 0){
            foreach($update_order as $_key => $rqd){
                $dt_data = [];
                $dt_data['pur_order'] = $id;
                $dt_data['item_code'] = $rqd['item_code'];
                $dt_data['unit_id'] = isset($rqd['unit_id']) ? $rqd['unit_id'] : null;
                $dt_data['unit_price'] = $rqd['unit_price'];
                $dt_data['into_money'] = $rqd['into_money'];
                $dt_data['total'] = $rqd['total'];
                $dt_data['tax_value'] = $rqd['tax_value'];
                $dt_data['item_name'] = $rqd['item_name'];
                $dt_data['total_money'] = $rqd['total_money'];
                $dt_data['discount_money'] = $rqd['discount_money'];
                $dt_data['discount_%'] = $rqd['discount'];
                $dt_data['description'] = nl2br($rqd['item_description']);

                $tax_money = 0;
                $tax_rate_value = 0;
                $tax_rate = null;
                $tax_id = null;
                $tax_name = null;

                if(isset($rqd['tax_select'])){
                    $tax_rate_data = $this->pur_get_tax_rate($rqd['tax_select']);
                    $tax_rate_value = $tax_rate_data['tax_rate'];
                    $tax_rate = $tax_rate_data['tax_rate_str'];
                    $tax_id = $tax_rate_data['tax_id_str'];
                    $tax_name = $tax_rate_data['tax_name_str'];
                }

                $dt_data['tax'] = $tax_id;
                $dt_data['tax_rate'] = $tax_rate;
                $dt_data['tax_name'] = $tax_name;

                $dt_data['quantity'] = ($rqd['quantity'] != ''&& $rqd['quantity'] != null) ? $rqd['quantity'] : 0;

                $this->db->where('id', $rqd['id']);
                $this->db->update(db_prefix().'pur_order_detail', $dt_data);
                if($this->db->affected_rows() > 0){
                    $affectedRows++;
                }
            }
        }

        if(count($remove_order) > 0){ 
            foreach($remove_order as $remove_id){
                $this->db->where('id', $remove_id);
                if ($this->db->delete(db_prefix() . 'pur_order_detail')) {
                    $affectedRows++;
                }
            }
        }

       
        $total = [];
        $total['total_tax'] = 0;
        $_taxes = $this->get_html_tax_pur_order($id);
        foreach($_taxes['taxes_val'] as $tax_val){
            $total['total_tax'] += $tax_val;
        }

        
        $this->db->where('id',$id);
        $this->db->update(db_prefix().'pur_orders',$total);
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }
        
        if ($affectedRows > 0) {
           

            return true;
        }

        return false;
    }

    /**
     * { delete pur order }
     *
     * @param      <type>   $id     The identifier
     *
     * @return     boolean 
     */
    public function delete_pur_order($id)
    {

        hooks()->do_action('before_pur_order_deleted', $id);

        $affectedRows = 0;
        $this->db->where('pur_order',$id);
        $this->db->delete(db_prefix().'pur_order_detail');
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        $this->db->where('rel_id',$id);
        $this->db->where('rel_type','pur_order');
        $this->db->delete(db_prefix().'files');
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        if (is_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_order/'. $id)) {
            delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_order/'. $id);
        }

        $this->db->where('pur_order',$id);
        $this->db->delete(db_prefix().'pur_order_payment');
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        $this->db->where('rel_type','purchase_order');
        $this->db->where('rel_id',$id);
        $this->db->delete(db_prefix().'notes');

        $this->db->where('rel_type','purchase_order');
        $this->db->where('rel_id',$id);
        $this->db->delete(db_prefix().'reminders');

        $this->db->where('fieldto','pur_order');
        $this->db->where('relid',$id);
        $this->db->delete(db_prefix().'customfieldsvalues');

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'pur_orders');

        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'pur_order');
        $this->db->delete(db_prefix() . 'taggables');
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        if($affectedRows > 0){
            return true;
        }
        return false;
    }

    /**
     * Gets the pur order approved.
     *
     * @return     <array>  The pur order approved.
     */
    public function get_pur_order_approved(){
        $this->db->where('approve_status', 2);
        if(!has_permission('purchase_orders', '', 'view') && is_staff_logged_in()){
            $this->db->where(' (' . db_prefix() . 'pur_orders.addedfrom = '.get_staff_user_id().' OR ' . db_prefix() . 'pur_orders.buyer = '.get_staff_user_id().' OR ' . db_prefix() . 'pur_orders.vendor IN (SELECT vendor_id FROM ' . db_prefix() . 'pur_vendor_admin WHERE staff_id=' . get_staff_user_id() . '))');
        }

        return $this->db->get(db_prefix().'pur_orders')->result_array();
    }

    /**
     * Gets the pur order approved.
     *
     * @return     <array>  The pur order approved.
     */
    public function get_pur_order_approved_by_vendor($vendor){
        if(is_staff_logged_in() && !has_permission('purchase_orders', '', 'view')){
            $this->db->where(' (' . db_prefix() . 'pur_orders.addedfrom = '.get_staff_user_id().' OR ' . db_prefix() . 'pur_orders.buyer = '.get_staff_user_id().' OR ' . db_prefix() . 'pur_orders.vendor IN (SELECT vendor_id FROM ' . db_prefix() . 'pur_vendor_admin WHERE staff_id=' . get_staff_user_id() . '))');
        }
        
        $this->db->where('approve_status', 2);
        $this->db->where('vendor', $vendor);
        
        return $this->db->get(db_prefix().'pur_orders')->result_array();
    }

    /**
     * Adds a contract.
     *
     * @param      <type>   $data   The data
     *
     * @return     boolean  ( false) or int id contract
     */
    public function add_contract($data){
        
        $data['contract_value'] = reformat_currency_pur($data['contract_value'], $data['currency']);
        $data['payment_amount'] = reformat_currency_pur($data['payment_amount'], $data['currency']);
        if(isset($data['currency'])){
            unset($data['currency']);
        }

        $project = $this->projects_model->get($data['project']);
        $vendor_name = get_vendor_company_name($data['vendor']);
        $ven_rs = strtoupper(str_replace(' ', '', $vendor_name));
        $ct_rs = strtoupper(str_replace(' ', '', $data['contract_name']));
        if($project && $data['project'] != ''){
            $pj_rs = strtoupper(str_replace(' ', '', $project->name));
            $data['contract_number'] = $pj_rs.'-'.$ct_rs.'-'.$ven_rs;
        }else{
            $data['contract_number'] = $ct_rs.'-'.$ven_rs;
        }

        $data['add_from'] = get_staff_user_id();
        $data['start_date'] = to_sql_date($data['start_date']);
        $data['end_date'] = to_sql_date($data['end_date']);
        $data['signed_date'] = to_sql_date($data['signed_date']);
        $this->db->insert(db_prefix().'pur_contracts',$data);
        $insert_id = $this->db->insert_id();
        if($insert_id){
            return $insert_id;
        }
        return false;
        
    }

    /**
     * { update contract }
     *
     * @param      <type>   $data   The data
     * @param      <type>   $id     The identifier
     *
     * @return     boolean 
     */
    public function update_contract($data,$id) {
        $data['contract_value'] = reformat_currency_pur($data['contract_value'], $data['currency']);
        $data['payment_amount'] = reformat_currency_pur($data['payment_amount'], $data['currency']);

        if(isset($data['currency'])){
            unset($data['currency']);
        }
        $project = $this->projects_model->get($data['project']);
        $vendor_name = get_vendor_company_name($data['vendor']);
        $ven_rs = strtoupper(str_replace(' ', '', $vendor_name));
        $ct_rs = strtoupper(str_replace(' ', '', $data['contract_name']));
        if($project){
            $pj_rs = strtoupper(str_replace(' ', '', $project->name ?? ''));
            $data['contract_number'] = $pj_rs.'-'.$ct_rs.'-'.$ven_rs;
        }else{
            $data['contract_number'] = $ct_rs.'-'.$ven_rs;
        }

        $data['add_from'] = get_staff_user_id();
        $data['start_date'] = to_sql_date($data['start_date']);
        $data['end_date'] = to_sql_date($data['end_date']);
        if(isset($data['time_payment'])){
            $data['time_payment'] = to_sql_date($data['time_payment']);
        }

        $data['signed_date'] = to_sql_date($data['signed_date']);
        $this->db->where('id',$id);
        $this->db->update(db_prefix().'pur_contracts',$data);
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }

    /**
     * { delete contract }
     *
     * @param      <type>   $id     The identifier
     *
     * @return     boolean   
     */
    public function delete_contract($id){
        $this->db->where('rel_id',$id);
        $this->db->where('rel_type','pur_contract');
        $this->db->delete(db_prefix().'files');
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        if (is_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_contract/'. $id)) {
            delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_contract/'. $id);
        }

        if (is_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/contract_sign/'. $id)) {
            delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/contract_sign/'. $id);
        }

        $this->db->where('id',$id);
        $this->db->delete(db_prefix().'pur_contracts');
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }

    /**
     * Gets the html vendor.
     *
     * @param      <type>  $vendor  The vendor
     *
     * @return     string  The html vendor.
     */
    public function get_html_vendor($vendor){
        
        $vendors = $this->get_vendor($vendor);
        $html = '<table class="table border table-striped ">
                            <tbody>
                               <tr class="project-overview">';
        $html .= '<td width="20%" class="bold">'._l('company').'</td>';
        $html .= '<td>'.$vendors->company.'</td>';
        $html .= '<td width="20%" class="bold">'._l('phonenumber').'</td>';
        $html .= '<td>'.$vendors->phonenumber.'</td>';                               
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td width="20%" class="bold">'._l('city').'</td>';
        $html .= '<td>'.$vendors->city.'</td>';
        $html .= '<td width="20%" class="bold">'._l('address').'</td>';
        $html .= '<td>'.$vendors->address.'</td>';                               
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td width="20%" class="bold">'._l('client_vat_number').'</td>';
        $html .= '<td>'.$vendors->vat.'</td>';
        $html .= '<td width="20%" class="bold">'._l('website').'</td>';
        $html .= '<td>'.$vendors->website.'</td>';                               
        $html .= '</tr>';
        $html .= '</tbody>
                </table>';

        return $html;
    }

    /**
     * Gets the contract.
     *
     * @param      string  $id     The identifier
     *
     * @return     <row>,<array>  The contract.
     */
    public function get_contract($id = ''){
        if($id == ''){
            if(!has_permission('purchase_contracts', '', 'view') && is_staff_logged_in()){
                $this->db->where('(' . db_prefix() . 'pur_contracts.add_from = '.get_staff_user_id().' OR ' . db_prefix() . 'pur_contracts.vendor IN (SELECT vendor_id FROM ' . db_prefix() . 'pur_vendor_admin WHERE staff_id=' . get_staff_user_id() . '))');
            }

            return  $this->db->get(db_prefix().'pur_contracts')->result_array();
        }else{
            $this->db->where('id',$id);
            return $this->db->get(db_prefix().'pur_contracts')->row();
        }
    }

    /**
     * { sign contract }
     *
     * @param      <type>   $contract  The contract
     * @param      <type>   $status    The status
     *
     * @return     boolean 
     */
    public function sign_contract($contract,$status){
        $this->db->where('id',$contract);
        $this->db->update(db_prefix().'pur_contracts',[
            'signed_status' => $status,
            'signed_date' => date('Y-m-d'),
            'signer' => get_staff_user_id(),
        ]);
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }

    /**
     * { check approval details }
     *
     * @param      <type>          $rel_id    The relative identifier
     * @param      <type>          $rel_type  The relative type
     *
     * @return     boolean|string 
     */
    public function check_approval_details($rel_id, $rel_type){
        $this->db->where('rel_id', $rel_id);
        $this->db->where('rel_type', $rel_type);
        $approve_status = $this->db->get(db_prefix().'pur_approval_details')->result_array();
        if(count($approve_status) > 0){
            foreach ($approve_status as $value) {
                if($value['approve'] == -1){
                    return 'reject';
                }
                if($value['approve'] == 0){
                    $value['staffid'] = explode(', ',$value['staffid']);
                    return $value;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Gets the list approval details.
     *
     * @param      <type>  $rel_id    The relative identifier
     * @param      <type>  $rel_type  The relative type
     *
     * @return     <array>  The list approval details.
     */
    public function get_list_approval_details($rel_id, $rel_type){
        $this->db->select('*');
        $this->db->where('rel_id', $rel_id);
        $this->db->where('rel_type', $rel_type);
        return $this->db->get(db_prefix().'pur_approval_details')->result_array();
    }

    /**
     * Sends a request approve.
     *
     * @param      <type>   $data   The data
     *
     * @return     boolean   
     */
    public function send_request_approve($data){
        if(!isset($data['status'])){
            $data['status'] = '';
        }
        $date_send = date('Y-m-d H:i:s');
        $data_new = $this->get_approve_setting($data['rel_type'], $data['status']);
        if(!$data_new){
            return false;
        }
        $this->delete_approval_details($data['rel_id'], $data['rel_type']);
        $list_staff = $this->staff_model->get();
        $list = [];
        $staff_addedfrom = $data['addedfrom'];
        $sender = get_staff_user_id();
        
        foreach ($data_new as $value) {
            $row = [];
            
            if($value->approver !== 'staff'){
            $value->staff_addedfrom = $staff_addedfrom;
            $value->rel_type = $data['rel_type'];
            $value->rel_id = $data['rel_id'];
            
                $approve_value = $this->get_staff_id_by_approve_value($value, $value->approver);

                if(is_numeric($approve_value) && $approve_value != 0){
                    $approve_value = $this->staff_model->get($approve_value)->email;
                }else{

                    $this->db->where('rel_id', $data['rel_id']);
                    $this->db->where('rel_type', $data['rel_type']);
                    $this->db->delete('tblpur_approval_details');


                    return $value->approver;
                }
                $row['approve_value'] = $approve_value;
            
            $staffid = $this->get_staff_id_by_approve_value($value, $value->approver);
            
            if(empty($staffid)){
                $this->db->where('rel_id', $data['rel_id']);
                $this->db->where('rel_type', $data['rel_type']);
                $this->db->delete('tblpur_approval_details');


                return $value->approver;
            }

                $row['action'] = $value->action;
                $row['staffid'] = $staffid;
                $row['date_send'] = $date_send;
                $row['rel_id'] = $data['rel_id'];
                $row['rel_type'] = $data['rel_type'];
                $row['sender'] = $sender;
                $this->db->insert('tblpur_approval_details', $row);

            }else if($value->approver == 'staff'){
                $row['action'] = $value->action;
                $row['staffid'] = $value->staff;
                $row['date_send'] = $date_send;
                $row['rel_id'] = $data['rel_id'];
                $row['rel_type'] = $data['rel_type'];
                $row['sender'] = $sender;

                $this->db->insert('tblpur_approval_details', $row);
            }
        }
        return true;
    }

    /**
     * Gets the approve setting.
     *
     * @param      <type>   $type    The type
     * @param      string   $status  The status
     *
     * @return     boolean  The approve setting.
     */
    public function get_approve_setting($type, $status = ''){
        $this->db->select('*');
        $this->db->where('related', $type);
        $approval_setting = $this->db->get('tblpur_approval_setting')->row();
        if($approval_setting){
            return json_decode($approval_setting->setting);
        }else{
            return false;
        }
    }

    /**
     * { delete approval details }
     *
     * @param      <type>   $rel_id    The relative identifier
     * @param      <type>   $rel_type  The relative type
     *
     * @return     boolean  ( description_of_the_return_value )
     */
    public function delete_approval_details($rel_id, $rel_type)
    {
        $this->db->where('rel_id', $rel_id);
        $this->db->where('rel_type', $rel_type);
        $this->db->delete(db_prefix().'pur_approval_details');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * Gets the staff identifier by approve value.
     *
     * @param      <type>  $data           The data
     * @param      string  $approve_value  The approve value
     *
     * @return     array   The staff identifier by approve value.
     */
    public function get_staff_id_by_approve_value($data, $approve_value){
        $list_staff = $this->staff_model->get();
        $list = [];
        $staffid = [];

        $this->load->model('departments_model');
        $this->load->model('staff_model');
        
        if($approve_value == 'head_of_department'){
            $staffid = $this->departments_model->get_staff_departments($data->staff_addedfrom)[0]['manager_id'];
        }elseif($approve_value == 'direct_manager'){
            $staffid = $this->staff_model->get($data->staff_addedfrom)->team_manage;
        }
        
        return $staffid;
    }

    /**
     * Gets the staff sign.
     *
     * @param      <type>  $rel_id    The relative identifier
     * @param      <type>  $rel_type  The relative type
     *
     * @return     array   The staff sign.
     */
    public function get_staff_sign($rel_id, $rel_type){
        $this->db->select('*');

        $this->db->where('rel_id', $rel_id);
        $this->db->where('rel_type', $rel_type);
        $this->db->where('action', 'sign');    
        $approve_status = $this->db->get(db_prefix().'pur_approval_details')->result_array();
        if(isset($approve_status))
        {
            $array_return = [];
            foreach ($approve_status as $key => $value) {
               array_push($array_return, $value['staffid']);
            }
            return $array_return;
        }
        return [];
    }


    /**
     * Sends a mail.
     *
     * @param      <type>  $data   The data
     */
    public function send_mail($data, $staffid = ''){
        $this->load->model('emails_model');
        if(!isset($data['status'])){
            $data['status'] = '';
        }

        if($staffid == ''){
            $staff_id = $staffid;
        }else{
            $staff_id = get_staff_user_id();
        }

        $get_staff_enter_charge_code = '';
        $mes = 'notify_send_request_approve_project';
        $staff_addedfrom = 0;
        $additional_data = $data['rel_type'];
        $object_type = $data['rel_type'];
        switch ($data['rel_type']) {
            case 'pur_request':
                $staff_addedfrom = $this->get_purchase_request($data['rel_id'])->requester;
                $additional_data = $this->get_purchase_request($data['rel_id'])->pur_rq_name;
                $list_approve_status = $this->get_list_approval_details($data['rel_id'],$data['rel_type']);
                $mes = 'notify_send_request_approve_pur_request';
                $mes_approve = 'notify_send_approve_pur_request';
                $mes_reject = 'notify_send_rejected_pur_request';
                $link = 'purchase/view_pur_request/' . $data['rel_id'];
                break;

            case 'pur_quotation':
                $staff_addedfrom = $this->get_estimate($data['rel_id'])->addedfrom;
                $additional_data = format_pur_estimate_number($data['rel_id']);
                $list_approve_status = $this->get_list_approval_details($data['rel_id'],$data['rel_type']);
                $mes = 'notify_send_request_approve_pur_quotation';
                $mes_approve = 'notify_send_approve_pur_quotation';
                $mes_reject = 'notify_send_rejected_pur_quotation';
                $link = 'purchase/quotations/' . $data['rel_id'];
                break;

            case 'pur_order':
                $pur_order = $this->get_pur_order($data['rel_id']);
                $staff_addedfrom = $pur_order->addedfrom;
                $additional_data = $pur_order->pur_order_number;
                $list_approve_status = $this->get_list_approval_details($data['rel_id'],$data['rel_type']);
                $mes = 'notify_send_request_approve_pur_order';
                $mes_approve = 'notify_send_approve_pur_order';
                $mes_reject = 'notify_send_rejected_pur_order';
                $link = 'purchase/purchase_order/' . $data['rel_id'];
                break;        
            case 'payment_request':
                $pur_inv = $this->get_payment_pur_invoice($data['rel_id']);
                $staff_addedfrom = $pur_inv->requester;
                $additional_data = _l('payment_for').' '.get_pur_invoice_number($pur_inv->pur_invoice);
                $list_approve_status = $this->get_list_approval_details($data['rel_id'],$data['rel_type']);
                $mes = 'notify_send_request_approve_pur_inv';
                $mes_approve = 'notify_send_approve_pur_inv';
                $mes_reject = 'notify_send_rejected_pur_inv';
                $link = 'purchase/payment_invoice/' . $data['rel_id'];
                break;

            case 'order_return':
                $order_return = $this->get_order_return($data['rel_id']);
                $staff_addedfrom = $order_return->staff_id;
                $additional_data = $order_return->order_return_number;
                $list_approve_status = $this->get_list_approval_details($data['rel_id'],$data['rel_type']);
                $mes = 'notify_send_request_approve_order_return';
                $mes_approve = 'notify_send_approve_order_return';
                $mes_reject = 'notify_send_rejected_order_return';
                $link = 'purchase/order_returns/' . $data['rel_id'];
                break;
            default:
                
                break;
        }


        $check_approve_status = $this->check_approval_details($data['rel_id'], $data['rel_type'], $data['status']);
        if(isset($check_approve_status['staffid'])){

        $mail_template = 'send-request-approve';

            if(!in_array(get_staff_user_id(),$check_approve_status['staffid'])){
                foreach ($check_approve_status['staffid'] as $value) {
                    $staff = $this->staff_model->get($value);
                    $notified = add_notification([
                    'description'     => $mes,
                    'touserid'        => $staff->staffid,
                    'link'            => $link,
                    'additional_data' => serialize([
                        $additional_data,
                    ]),
                    ]);
                    if ($notified) {
                        pusher_trigger_notification([$staff->staffid]);
                    }

                    $data_sm = [];
                    $data_sm['mail_to'] = $staff->email;
                    $data_sm['type'] = $type;
                    $data_sm['link'] = admin_url($link);
                    $data_sm['staff_name'] = $staff->firstname.' '.$staff->lastname;
                    $data_sm['from_staff_name'] = get_staff_full_name($staff_addedfrom);



                    //$this->emails_model->send_simple_email($staff->email, _l('request_approval'), _l('email_send_request_approve', $type) .' <a href="'.admin_url($link).'">'.admin_url($link).'</a> '._l('from_staff', get_staff_full_name($staff_addedfrom)));

                    $template = mail_template('request_approval', 'purchase', array_to_object($data_sm));
                    $template->send();
                }
            }
        }

        if(isset($data['approve'])){
            if($data['approve'] == 2){
                $mes = $mes_approve;
                $mail_template = 'purchase_send_approved';
            }else{
                $mes = $mes_reject;
                $mail_template = 'purchase_send_rejected';
            }

            
            $staff = $this->staff_model->get($staff_addedfrom);
            $notified = add_notification([
            'description'     => $mes,
            'touserid'        => $staff->staffid,
            'link'            => $link,
            'additional_data' => serialize([
                $additional_data,
            ]),
            ]);
            if ($notified) {
                pusher_trigger_notification([$staff->staffid]);
            }

            //$this->emails_model->send_simple_email($staff->email, _l('approval_notification'), _l($mail_template, $type.' <a href="'.admin_url($link).'">'.admin_url($link).'</a> ').' '._l('by_staff', get_staff_full_name(get_staff_user_id())));

            $data_sm_2 = [];
            $data_sm_2['mail_to'] = $staff->email;
            $data_sm_2['type'] = $type;
            $data_sm_2['link'] = admin_url($link);
            $data_sm_2['staff_name'] = $staff->firstname.' '.$staff->lastname;
            $data_sm_2['by_staff_name'] = get_staff_full_name(get_staff_user_id());

            $template = mail_template($mail_template, 'purchase', array_to_object($data_sm_2));
            $template->send();

            foreach($list_approve_status as $key => $value){
            $value['staffid'] = explode(', ',$value['staffid']);
                if($value['approve'] == 1 && !in_array(get_staff_user_id(),$value['staffid'])){
                    foreach ($value['staffid'] as $staffid) {
                      
                        $staff = $this->staff_model->get($staffid);
                        $notified = add_notification([
                        'description'     => $mes,
                        'touserid'        => $staff->staffid,
                        'link'            => $link,
                        'additional_data' => serialize([
                            $additional_data,
                        ]),
                        ]);
                        if ($notified) {
                            pusher_trigger_notification([$staff->staffid]);
                        }
                        
                        //$this->emails_model->send_simple_email($staff->email, _l('approval_notification'), _l($mail_template, $type. ' <a href="'.admin_url($link).'">'.admin_url($link).'</a>').' '._l('by_staff', get_staff_full_name($staff_id)));

                        $data_sm_3 = [];
                        $data_sm_3['mail_to'] = $staff->email;
                        $data_sm_3['type'] = $type;
                        $data_sm_3['link'] = admin_url($link);
                        $data_sm_3['staff_name'] = $staff->firstname.' '.$staff->lastname;
                        $data_sm_3['by_staff_name'] = get_staff_full_name(get_staff_user_id());

                        $template = mail_template($mail_template, 'purchase', array_to_object($data_sm_3));
                        $template->send();
                    }
                }
            }
        }
    }

    /**
     * { update approve request }
     *
     * @param      <type>   $rel_id    The relative identifier
     * @param      <type>   $rel_type  The relative type
     * @param      <type>   $status    The status
     *
     * @return     boolean
     */
    public function update_approve_request($rel_id , $rel_type, $status){ 
        $data_update = [];
        
        switch ($rel_type) {
            case 'pur_request':
                $data_update['status'] = $status;
                $this->update_item_pur_request($rel_id);
                $this->db->where('id', $rel_id);
                $this->db->update(db_prefix().'pur_request', $data_update);
                return true;
                break;
            case 'pur_quotation':
                $data_update['status'] = $status;
                $this->db->where('id', $rel_id);
                $this->db->update(db_prefix().'pur_estimates', $data_update);
                return true;
                break;
            case 'pur_order':
                $data_update['approve_status'] = $status;
                $this->db->where('id', $rel_id);
                $this->db->update(db_prefix().'pur_orders', $data_update);

                // warehouse module hook after purchase order approve
                hooks()->do_action('after_purchase_order_approve', $rel_id);

                return true;
                break;

            case 'order_return':
                $data_update['approval'] = $status;
                $this->db->where('id', $rel_id);
                $this->db->update(db_prefix().'wh_order_returns', $data_update);

                return true;
                break;
            case 'payment_request':
                $data_update['approval_status'] = $status;
                $this->db->where('id', $rel_id);
                $this->db->update(db_prefix().'pur_invoice_payment', $data_update);

                $this->update_invoice_after_approve($rel_id);

                // accounting module hook after purchase payment approve
                hooks()->do_action('after_purchase_payment_approve', $rel_id);
                
                return true;
                break;
            default:
                return false;
                break;
        }
    }

    /**
     * { update item pur request }
     *
     * @param      $id     The identifier
     */
    public function update_item_pur_request($id){
        $pur_rq = $this->get_purchase_request($id);
        if($pur_rq){
           
            $this->db->where('id',$id);
            $this->db->update(db_prefix().'pur_request',['from_items' => 1]);

            $pur_rqdt = $this->get_pur_request_detail($id);
            if(count($pur_rqdt) > 0){
                foreach($pur_rqdt as $rqdt){
                    if($rqdt['item_text'] != '' && ($rqdt['item_code'] == '' || $rqdt['item_code'] == null) ){
                        $item_data['description'] = $rqdt['item_text'];
                        $item_data['purchase_price'] = $rqdt['unit_price'];
                        $item_data['unit_id'] = $rqdt['unit_id'];
                        $item_data['rate'] = '';
                        $item_data['sku_code'] = '';
                        $item_data['commodity_barcode'] = $this->generate_commodity_barcode();
                        $item_data['commodity_code'] = $this->generate_commodity_barcode();
                        $item_id = $this->add_commodity_one_item($item_data);
                        $this->db->where('prd_id',$rqdt['prd_id']);
                        $this->db->update(db_prefix().'pur_request_detail',['item_code' => $item_id,]);
                    }
                }
            }
            
        }
    }

    /**
     * { update approval details }
     *
     * @param      <int>   $id     The identifier
     * @param      <type>   $data   The data
     *
     * @return     boolean 
     */
    public function update_approval_details($id, $data){
        $data['date'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        $this->db->update(db_prefix().'pur_approval_details', $data);
        if($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * { pur request pdf }
     *
     * @param      <type>  $pur_request  The pur request
     *
     * @return      ( pdf )
     */
    public function pur_request_pdf($pur_request)
    {
        return app_pdf('pur_request', module_dir_path(PURCHASE_MODULE_NAME, 'libraries/pdf/Pur_request_pdf'), $pur_request);
    }

    /**
     * Gets the pur request pdf html.
     *
     * @param      <type>  $pur_request_id  The pur request identifier
     *
     * @return     string  The pur request pdf html.
     */
    public function get_pur_request_pdf_html($pur_request_id){
        $this->load->model('departments_model');

        $pur_request = $this->get_purchase_request($pur_request_id);
        $project = $this->projects_model->get($pur_request->project);
        $project_name = '';
        if($project && isset($project->name)){
            $project_name = $project->name;
        }

        $tax_data = $this->get_html_tax_pur_request($pur_request_id);

        if($pur_request->currency != 0){
            $base_currency = pur_get_currency_by_id($pur_request->currency);
        }else{
            $base_currency = get_base_currency_pur();
        }
        

        $pur_request_detail = $this->get_pur_request_detail($pur_request_id);
        $company_name = get_option('invoice_company_name'); 
        $dpm_name = $this->departments_model->get($pur_request->department)->name;
        $address = get_option('invoice_company_address'); 
        $day = date('d',strtotime($pur_request->request_date));
        $month = date('m',strtotime($pur_request->request_date));
        $year = date('Y',strtotime($pur_request->request_date));
        $list_approve_status = $this->get_list_approval_details($pur_request_id,'pur_request');

    $html = '<table class="table">
        <tbody>
          <tr>
            <td rowspan="3" style="width: 30%" class="text-left">'.get_po_logo(get_option('pdf_logo_width')).'</td>
            <td align="right" class="font_td_cpn" style="width: 70%">'. _l('purchase_company_name').': '. $company_name.'</td>
            
          </tr>
          <tr>
            <td align="right" class="font_500">'. _l('address').': '. $address.'</td>
          </tr>
          <tr>
            <td align="right" class="font_500">'.$pur_request->pur_rq_code.'</td>
          </tr>
        </tbody>
      </table>
      <table class="table">
        <tbody>
          <tr>
            
            <td class="td_ali_font"><h2 class="h2_style">'.mb_strtoupper(_l('purchase_request')).'</h2></td>
           
          </tr>
          <tr>
            
            <td class="align_cen">'. _l('days').' '.$day.' '._l('month').' '.$month.' '._l('year') .' '.$year.'</td>
            
          </tr>
          
        </tbody>
      </table>
      <table class="table">
        <tbody>
          <tr>
            <td class="td_width_25"><h4>'. _l('requester').':</h4></td>
            <td class="td_width_75">'. get_staff_full_name($pur_request->requester).'</td>
          </tr>
          <tr>
            <td class="font_500"><h4>'. _l('department').':</h4></td>
            <td>'. $dpm_name.'</td>
          </tr>
          <tr>
            <td class="font_500"><h4>'. _l('type').':</h4></td>
            <td>'. _l($pur_request->type).'</td>
          </tr>
          <tr>
            <td class="font_500"><h4>'. _l('project').':</h4></td>
            <td>'.  $project_name.'</td>
          </tr>
        </tbody>
      </table>
      <br><br>
      ';

      $html .=  '<table class="table pur_request-item">
            <thead>
              <tr class="border_tr">
                <th align="left" class="width30 thead-dark">'._l('items').'</th>
                <th align="right" class="width15 thead-dark">'._l('purchase_unit_price').'</th>
                <th align="right" class="width15 thead-dark">'._l('purchase_quantity').'</th>
                <th align="right" class="width15 thead-dark">'._l('into_money').'</th>';
           
                    $html .= '<th align="right" class="width10 thead-dark">'._l('tax_value').'</th>';
                  
                $html .= '<th align="right" class="width15 thead-dark">'._l('total').'</th>
              </tr>
            </thead>
          <tbody>';

      $tmn = 0;  
      $_total = 0;  
      foreach($pur_request_detail as $row){
        $items = $this->get_items_by_id($row['item_code']);
        $units = $this->get_units_by_id($row['unit_id']);
        if($items){
            $unit_name = isset($units->unit_name) ? $units->unit_name : '';

            $html .= '<tr class="border_tr">
                <td class="width30" align="left">'.$items->commodity_code.' - '.$items->description.'</td>

                <td class="width15" align="right">'.app_format_money($row['unit_price'],$base_currency->symbol).'</td>
                <td class="width15" align="right">'.app_format_number($row['quantity'],'').' '.$unit_name.'</td>
                <td class="width15" align="right">'.app_format_money($row['into_money'],$base_currency->symbol).'</td>';
          
                    $html .= '<td class="width10" align="right">'.app_format_money($row['tax_value'],$base_currency->symbol).'</td>';
                
                $html .= '<td class="width15" align="right">'.app_format_money($row['total'],$base_currency->symbol).'</td>
              </tr>';
        }else{
            $unit_name = isset($units->unit_name) ? $units->unit_name : '';
            $html .= '<tr class="border_tr">
                <td class="width30" align="left">'.$row['item_text'].'</td>

                <td class="width15" align="right">'.app_format_money($row['unit_price'],$base_currency->symbol).'</td>
                <td class="width15" align="right">'.$row['quantity'].'</td>
                <td class="width15" align="right">'.app_format_money($row['into_money'],$base_currency->symbol).'</td>';
              
                    $html .= '<td class="width10" align="right">'.app_format_money($row['tax_value'],$base_currency->symbol).'</td>';
                
                $html .= '<td class="width15" align="right">'.app_format_money($row['total'],$base_currency->symbol).'</td>
              </tr>';
        }

        $tmn += $row['into_money'];
        $_total += $row['total'];
      }  
      $html .=  '</tbody>
      </table><br><br>';

      $html .= '<table class="table text-right"><tbody>';
      $html .= '<tr>
                 <td style="width: 33%"></td>
                 <td>'. _l('subtotal').'</td>
                 <td class="subtotal">
                    '. app_format_money($tmn, $base_currency->symbol).'
                 </td>
              </tr>';

      $html .= $tax_data['pdf_html'];
      $html .= '<tr>
                 <td style="width: 33%"></td>
                 <td>'. _l('total').'</td>
                 <td class="subtotal">
                    '. app_format_money($_total, $base_currency->symbol).'
                 </td>
              </tr>';

      $html .= ' </tbody></table>';

      $html .= '<br>
      <br>
      <br>
      <br>
      <table class="table">
        <tbody>
          <tr>';
     if(count($list_approve_status) > 0){
      
        foreach ($list_approve_status as $value) {
     $html .= '<td class="td_appr">';
        if($value['action'] == 'sign'){
            $html .= '<h3>'.mb_strtoupper(get_staff_full_name($value['staffid'])).'</h3>';
            if($value['approve'] == 2){ 
                $html .= '<img src="'.FCPATH.'modules/purchase/uploads/pur_request/signature/'.$pur_request->id.'/signature_'.$value['id'].'.png" class="img_style">';
            }
                
        }else{ 
        $html .= '<h3>'.mb_strtoupper(get_staff_full_name($value['staffid'])).'</h3>';
              if($value['approve'] == 2){ 
        $html .= '<img src="'.FCPATH .'modules/purchase/uploads/approval/approved.png" class="img_style">';
             }elseif($value['approve'] == 3){
        $html .= '<img src="'.FCPATH.'modules/purchase/uploads/approval/rejected.png" class="img_style">';
             }
              
                }
       $html .= '</td>';
        }
       
    
    
     } 
            $html .= '<td class="td_ali_font"><h3>'.mb_strtoupper(_l('purchase_requestor')).'</h3></td>
            <td class="td_ali_font"><h3>'.mb_strtoupper(_l('purchase_treasurer')).'</h3></td></tr>
        </tbody>
      </table>';
      $html .=  '<link href="' . FCPATH.'modules/purchase/assets/css/pur_order_pdf.css' . '"  rel="stylesheet" type="text/css" />';
      return $html;
    }

    /**
     * { request quotation pdf }
     *
     * @param      <type>  $pur_request  The pur request
     *
     * @return      ( pdf )
     */
    public function request_quotation_pdf($pur_request)
    {
        return app_pdf('pur_request', module_dir_path(PURCHASE_MODULE_NAME, 'libraries/pdf/Request_quotation_pdf'), $pur_request);
    }

    /**
     * Gets the request quotation pdf html.
     *
     * @param      <type>  $pur_request_id  The pur request identifier
     *
     * @return     string  The request quotation pdf html.
     */
    public function get_request_quotation_pdf_html($pur_request_id){
        $this->load->model('departments_model');

        $pur_request = $this->get_purchase_request($pur_request_id);
        $project = $this->projects_model->get($pur_request->project);
        $project_name = '';
        if($project && isset($project->name)){
            $project_name = $project->name;
        }

        $tax_data = $this->get_html_tax_pur_request($pur_request_id);
        if($pur_request->currency != 0){
            $base_currency = pur_get_currency_by_id($pur_request->currency);
        }else{
            $base_currency = get_base_currency_pur();
        }
        $pur_request_detail = $this->get_pur_request_detail($pur_request_id);
        $company_name = get_option('invoice_company_name'); 
        $dpm_name = $this->departments_model->get($pur_request->department)->name;
        $address = get_option('invoice_company_address'); 
        $day = date('d',strtotime($pur_request->request_date));
        $month = date('m',strtotime($pur_request->request_date));
        $year = date('Y',strtotime($pur_request->request_date));
        $list_approve_status = $this->get_list_approval_details($pur_request_id,'pur_request');

    $html = '<table class="table">
        <tbody>
          <tr>
            <td rowspan="3" style="width: 30%" class="text-left">'.get_po_logo(get_option('pdf_logo_width')).'</td>
            <td align="right" class="font_td_cpn" style="width: 70%">'. _l('purchase_company_name').': '. $company_name.'</td>
            
          </tr>
          <tr>
            <td align="right" class="font_500">'. _l('address').': '. $address.'</td>
          </tr>
          <tr>
            <td align="right" class="font_500">'.$pur_request->pur_rq_code.'</td>
          </tr>
        </tbody>
      </table>
      <table class="table">
        <tbody>
          <tr>
            
            <td class="td_ali_font"><h2 class="h2_style">'.mb_strtoupper(_l('purchase_request')).'</h2></td>
           
          </tr>
          <tr>
            
            <td class="align_cen">'. _l('days').' '.$day.' '._l('month').' '.$month.' '._l('year') .' '.$year.'</td>
            
          </tr>
          
        </tbody>
      </table>
      <table class="table">
        <tbody>
          <tr>
            <td class="td_width_25"><h4>'. _l('requester').':</h4></td>
            <td class="td_width_75">'. get_staff_full_name($pur_request->requester).'</td>
          </tr>
          <tr>
            <td class="font_500"><h4>'. _l('department').':</h4></td>
            <td>'. $dpm_name.'</td>
          </tr>
          <tr>
            <td class="font_500"><h4>'. _l('type').':</h4></td>
            <td>'. _l($pur_request->type).'</td>
          </tr>
          <tr>
            <td class="font_500"><h4>'. _l('project').':</h4></td>
            <td>'.  $project_name.'</td>
          </tr>
        </tbody>
      </table>
      <br><br>
      ';

      $html .=  '<table class="table pur_request-item">
            <thead>
              <tr class="border_tr">
                <th align="left" class="thead-dark width30">'._l('items').'</th>
                <th align="right" class="thead-dark width15">'._l('purchase_unit_price').'</th>
                <th align="right" class="thead-dark width15">'._l('purchase_quantity').'</th>
                <th align="right" class="thead-dark width15">'._l('into_money').'</th>';
              
                    $html .= '<th align="right" class="thead-dark width10">'._l('tax_value').'</th>';
                
                $html .= '<th align="right" class="thead-dark width15">'._l('total').'</th>
              </tr>
            </thead>
          <tbody>';

      $tmn = 0;    
      $_total = 0;
      foreach($pur_request_detail as $row){
        $items = $this->get_items_by_id($row['item_code']);
        $units = $this->get_units_by_id($row['unit_id']);
        if($items){

            $unit_name = isset($units->unit_name) ? $units->unit_name : '';
            $html .= '<tr class="border_tr">
                <td class="width30" align="left">'.$items->commodity_code.' - '.$items->description.'</td>
                <td class="width15" align="right">'.app_format_money($row['unit_price'],$base_currency->symbol).'</td>
                <td class="width15" align="right">'.app_format_number($row['quantity'],'').' '.$unit_name.'</td>
                <td class="width15" align="right">'.app_format_money($row['into_money'],$base_currency->symbol).'</td>';
                
                    $html .= '<td class="width10" align="right">'.app_format_money($row['tax_value'],$base_currency->symbol).'</td>';
                
                $html .= '<td class="width15" align="right">'.app_format_money($row['total'],$base_currency->symbol).'</td>
              </tr>';
        }else{
            $unit_name = isset($units->unit_name) ? $units->unit_name : '';
            $html .= '<tr class="border_tr">
                <td class="width30" align="left">'.$row['item_text'].'</td>
                <td class="width15" align="right">'.app_format_money($row['unit_price'],$base_currency->symbol).'</td>
                <td class="width15" align="right">'.$row['quantity'].'</td>
                <td class="width15" align="right">'.app_format_money($row['into_money'],$base_currency->symbol).'</td>';
              
                    $html .= '<td class="width10" align="right">'.app_format_money($row['tax_value'],$base_currency->symbol).'</td>';
                
                $html .= '<td class="width15" align="right">'.app_format_money($row['total'],$base_currency->symbol).'</td>
              </tr>';
        }
          $tmn += $row['into_money'];
          $_total += $row['total'];
      }  
      $html .=  '</tbody>
      </table><br><br>';

      $html .= '<table class="table text-right"><tbody>';
      $html .= '<tr>
                 <td style="width: 33%"></td>
                 <td>'. _l('subtotal').'</td>
                 <td class="subtotal">
                    '. app_format_money($tmn, $base_currency->symbol).'
                 </td>
              </tr>';

      $html .= $tax_data['pdf_html'];
      $html .= '<tr>
                 <td style="width: 33%"></td>
                 <td>'. _l('total').'</td>
                 <td class="subtotal">
                    '. app_format_money($_total, $base_currency->symbol).'
                 </td>
              </tr>';

      $html .= ' </tbody></table>';

      $html .=  '<link href="' . FCPATH.'modules/purchase/assets/css/pur_order_pdf.css' . '"  rel="stylesheet" type="text/css" />';
      return $html;
    }

    /**
     * Sends a request quotation.
     *
     * @param      <type>   $data   The data
     *
     * @return     boolean
     */
    public function send_request_quotation($data){
        $staff_id = get_staff_user_id();

        $inbox = array();

        $inbox['to'] = implode(',',$data['email']);
        $inbox['sender_name'] = get_staff_full_name($staff_id);
        $inbox['subject'] = _strip_tags($data['subject']);
        $inbox['body'] = _strip_tags($data['content']);        
        $inbox['body'] = nl2br_save_html($inbox['body']);
        $inbox['date_received']      = date('Y-m-d H:i:s');
        $inbox['from_email'] = get_option('smtp_email');
        
        if(strlen(get_option('smtp_host')) > 0 && strlen(get_option('smtp_password')) > 0 && strlen(get_option('smtp_username')) > 0){

            $ci = &get_instance();
            $ci->email->initialize();
            $ci->load->library('email');    
            $ci->email->clear(true);
            $ci->email->from($inbox['from_email'], $inbox['sender_name']);
            $ci->email->to($inbox['to']);
            
            $ci->email->subject($inbox['subject']);
            $ci->email->message($inbox['body']);
            
            $attachment_url = site_url(PURCHASE_PATH.'request_quotation/'.$data['pur_request_id'].'/'.str_replace(" ", "_", $_FILES['attachment']['name']));
            $ci->email->attach($attachment_url);

            return $ci->email->send(true);
        }
        
        return false;
    }

    /**
     * { update purchase setting }
     *
     * @param      <type>   $data   The data
     *
     * @return     boolean 
     */
    public function update_purchase_setting($data)
    {

        $affected_rows = 0;
        $val = $data['input_name_status'] == 'true' ? 1 : 0;
        if($data['input_name'] != 'show_purchase_tax_column' && $data['input_name'] != 'po_only_prefix_and_number' && $data['input_name'] != 'send_email_welcome_for_new_contact' && $data['input_name'] != 'reset_purchase_order_number_every_month'){
            $this->db->where('option_name',$data['input_name']);
            $this->db->update(db_prefix() . 'purchase_option', [
                    'option_val' => $val,
                ]);
            if ($this->db->affected_rows() > 0) {
                $affected_rows++;
            }
        }else{

            $this->db->where('name',$data['input_name']);
            $this->db->update(db_prefix() . 'options', [
                    'value' => $val,
                ]);
            if ($this->db->affected_rows() > 0) {
                $affected_rows++;
            }
        }

        if($affected_rows > 0){
            return true;
        }
        return false;

    }

    /**
     * { update purchase setting }
     *
     * @param      <type>   $data   The data
     *
     * @return     boolean 
     */
    public function update_pc_options_setting($data)
    {

            $val = $data['input_name_status'] == 'true' ? 1 : 0;
            $this->db->where('name',$data['input_name']);
            $this->db->update(db_prefix() . 'options', [
                    'value' => $val,
                ]);
            if ($this->db->affected_rows() > 0) {
                return true;
            }else{
                return false;
            }
    }


    /**
     * { update purchase setting }
     *
     * @param      <type>   $data   The data
     *
     * @return     boolean 
     */
    public function update_po_number_setting($data)
    {   
        $rs = 0;
        $this->db->where('option_name','create_invoice_by');
        $this->db->update(db_prefix() . 'purchase_option', [
                'option_val' => $data['create_invoice_by'],
            ]);
        if ($this->db->affected_rows() > 0) {
            $rs++;
        }
        
        $this->db->where('option_name','pur_request_prefix');
        $this->db->update(db_prefix() . 'purchase_option', [
                'option_val' => $data['pur_request_prefix'],
            ]);
        if ($this->db->affected_rows() > 0) {
            $rs++;
        }

        $this->db->where('option_name','pur_inv_prefix');
        $this->db->update(db_prefix() . 'purchase_option', [
                'option_val' => $data['pur_inv_prefix'],
            ]);
        if ($this->db->affected_rows() > 0) {
            $rs++;
        }

        $this->db->where('option_name','pur_order_prefix');
        $this->db->update(db_prefix() . 'purchase_option', [
                'option_val' => $data['pur_order_prefix'],
            ]);
        if ($this->db->affected_rows() > 0) {
            $rs++;
        }

        $this->db->where('option_name','terms_and_conditions');
        $this->db->update(db_prefix() . 'purchase_option', [
                'option_val' => $data['terms_and_conditions'],
            ]);
        if ($this->db->affected_rows() > 0) {
            $rs++;
        }

        $this->db->where('option_name','vendor_note');
        $this->db->update(db_prefix() . 'purchase_option', [
                'option_val' => $data['vendor_note'],
            ]);
        if ($this->db->affected_rows() > 0) {
            $rs++;
        }

        $this->db->where('option_name','next_po_number');
        $this->db->update(db_prefix() . 'purchase_option', [
                'option_val' => $data['next_po_number'],
            ]);
        if ($this->db->affected_rows() > 0) {
            $rs++;
        }

        $this->db->where('option_name','next_pr_number');
        $this->db->update(db_prefix() . 'purchase_option', [
                'option_val' => $data['next_pr_number'],
            ]);
        if ($this->db->affected_rows() > 0) {
            $rs++;
        }

        $this->db->where('name','pur_invoice_auto_operations_hour');
        $this->db->update(db_prefix() . 'options', [
                'value' => $data['pur_invoice_auto_operations_hour'],
            ]);
        if ($this->db->affected_rows() > 0) {
            $rs++;
        }

        $this->db->where('name','debit_note_prefix');
        $this->db->update(db_prefix() . 'options', [
                'value' => $data['debit_note_prefix'],
            ]);
        if ($this->db->affected_rows() > 0) {
            $rs++;
        }


        $this->db->where('name','pur_company_address');
        $this->db->update(db_prefix() . 'options', [
                'value' => $data['pur_company_address'],
            ]);
        if ($this->db->affected_rows() > 0) {
            $rs++;
        }


        $this->db->where('name','pur_company_city');
        $this->db->update(db_prefix() . 'options', [
                'value' => $data['pur_company_city'],
            ]);
        if ($this->db->affected_rows() > 0) {
            $rs++;
        }

        $this->db->where('name','pur_company_state');
        $this->db->update(db_prefix() . 'options', [
                'value' => $data['pur_company_state'],
            ]);
        if ($this->db->affected_rows() > 0) {
            $rs++;
        }

        $this->db->where('name','pur_company_zipcode');
        $this->db->update(db_prefix() . 'options', [
                'value' => $data['pur_company_zipcode'],
            ]);
        if ($this->db->affected_rows() > 0) {
            $rs++;
        }

        $this->db->where('name','pur_company_country_text');
        $this->db->update(db_prefix() . 'options', [
                'value' => $data['pur_company_country_text'],
            ]);
        if ($this->db->affected_rows() > 0) {
            $rs++;
        }

        $this->db->where('name','pur_company_country_code');
        $this->db->update(db_prefix() . 'options', [
                'value' => $data['pur_company_country_code'],
            ]);
        if ($this->db->affected_rows() > 0) {
            $rs++;
        }


        $this->db->where('rel_id', 0);
        $this->db->where('rel_type', 'po_logo');
        $avar = $this->db->get(db_prefix() . 'files')->row();

        if ($avar && (isset($_FILES['po_logo']['name']) && $_FILES['po_logo']['name'] != '')) {
            if (empty($avar->external)) {
                unlink(PURCHASE_MODULE_UPLOAD_FOLDER . '/po_logo/' . $avar->rel_id . '/' . $avar->file_name);
            }
            $this->db->where('id', $avar->id);
            $this->db->delete('tblfiles');

            if (is_dir(PURCHASE_MODULE_UPLOAD_FOLDER . '/po_logo/' . $avar->rel_id)) {
                // Check if no avars left, so we can delete the folder also
                $other_avars = list_files(PURCHASE_MODULE_UPLOAD_FOLDER . '/po_logo/' . $avar->rel_id);
                if (count($other_avars) == 0) {
                    // okey only index.html so we can delete the folder also
                    delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER . '/po_logo/' . $avar->rel_id);
                }
            }
        }

        if(handle_po_logo()){
            $rs++;
        }

        if($rs > 0){
            return true;
        }
        return false;
    }

    public function update_order_return_setting($data){
        $rs = 0;
        
        $this->db->where('name','pur_return_request_within_x_day');
        $this->db->update(db_prefix() . 'options', [
                'value' => $data['pur_return_request_within_x_day'],
            ]);
        if ($this->db->affected_rows() > 0) {
            $rs++;
        }

        $this->db->where('name','pur_fee_for_return_order');
        $this->db->update(db_prefix() . 'options', [
                'value' => $data['pur_fee_for_return_order'],
            ]);
        if ($this->db->affected_rows() > 0) {
            $rs++;
        }

        $this->db->where('name','pur_order_return_number_prefix');
        $this->db->update(db_prefix() . 'options', [
                'value' => $data['pur_order_return_number_prefix'],
            ]);
        if ($this->db->affected_rows() > 0) {
            $rs++;
        }

        $this->db->where('name','next_pur_order_return_number');
        $this->db->update(db_prefix() . 'options', [
                'value' => $data['next_pur_order_return_number'],
            ]);
        if ($this->db->affected_rows() > 0) {
            $rs++;
        }

        $this->db->where('name','pur_return_policies_information');
        $this->db->update(db_prefix() . 'options', [
                'value' => $data['pur_return_policies_information'],
            ]);
        if ($this->db->affected_rows() > 0) {
            $rs++;
        }

        if($rs > 0){
            return true;
        }
        return false;
    }

    /**
     * Gets the purchase order attachments.
     *
     * @param      <type>  $id     The purchase order
     *
     * @return     <type>  The purchase order attachments.
     */
    public function get_purchase_order_attachments($id){
   
        $this->db->where('rel_id',$id);
        $this->db->where('rel_type','pur_order');
        return $this->db->get(db_prefix().'files')->result_array();
    }

    /**
     * Gets the purchase order attachments.
     *
     * @param      <type>  $id     The purchase order
     *
     * @return     <type>  The purchase order attachments.
     */
    public function get_purchase_request_attachments($id){
   
        $this->db->where('rel_id',$id);
        $this->db->where('rel_type','pur_request');
        return $this->db->get(db_prefix().'files')->result_array();
    }

    /**
     * Gets the file.
     *
     * @param      <type>   $id      The file id
     * @param      boolean  $rel_id  The relative identifier
     *
     * @return     boolean  The file.
     */
    public function get_file($id, $rel_id = false)
    {
        $this->db->where('id', $id);
        $file = $this->db->get(db_prefix().'files')->row();

        if ($file && $rel_id) {
            if ($file->rel_id != $rel_id) {
                return false;
            }
        }
        return $file;
    }

    /**
     * Gets the part attachments.
     *
     * @param      <type>  $surope  The surope
     * @param      string  $id      The identifier
     *
     * @return     <type>  The part attachments.
     */
    public function get_purorder_attachments($surope, $id = '')
    {
        // If is passed id get return only 1 attachment
        if (is_numeric($id)) {
            $this->db->where('id', $id);
        } else {
            $this->db->where('rel_id', $assets);
        }
        $this->db->where('rel_type', 'pur_order');
        $result = $this->db->get(db_prefix().'files');
        if (is_numeric($id)) {
            return $result->row();
        }

        return $result->result_array();
    }

    /**
     * { delete purorder attachment }
     *
     * @param      <type>   $id     The identifier
     *
     * @return     boolean 
     */
    public function delete_purorder_attachment($id)
    {
        $attachment = $this->get_purorder_attachments('', $id);
        $deleted    = false;

        $this->db->where('id', $id);
        $this->db->delete(db_prefix().'files');        
        if ($attachment) {
            if (empty($attachment->external)) {
                unlink(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_order/'. $attachment->rel_id . '/' . $attachment->file_name);
            }
            $this->db->where('id', $attachment->id);
            $this->db->delete('tblfiles');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
            }

            if (is_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_order/'. $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_order/'. $attachment->rel_id);
                if (count($other_attachments) == 0) {
                    // okey only index.html so we can delete the folder also
                    delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_order/'. $attachment->rel_id);
                }
            }
        }

        return $deleted;
    }

    /**
     * Gets the part attachments.
     *
     * @param      <type>  $surope  The surope
     * @param      string  $id      The identifier
     *
     * @return     <type>  The part attachments.
     */
    public function get_purrequest_attachments($surope, $id = '')
    {
        // If is passed id get return only 1 attachment
        if (is_numeric($id)) {
            $this->db->where('id', $id);
        } else {
            $this->db->where('rel_id', $assets);
        }
        $this->db->where('rel_type', 'pur_request');
        $result = $this->db->get(db_prefix().'files');
        if (is_numeric($id)) {
            return $result->row();
        }

        return $result->result_array();
    }

    /**
     * { delete purorder attachment }
     *
     * @param      <type>   $id     The identifier
     *
     * @return     boolean 
     */
    public function delete_purrequest_attachment($id)
    {
        $attachment = $this->get_purrequest_attachments('', $id);
        $deleted    = false;

        $this->db->where('id', $id);
        $this->db->delete(db_prefix().'files');        
        if ($attachment) {
            if (empty($attachment->external)) {
                unlink(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_request/'. $attachment->rel_id . '/' . $attachment->file_name);
            }
            $this->db->where('id', $attachment->id);
            $this->db->delete('tblfiles');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
            }

            if (is_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_request/'. $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_request/'. $attachment->rel_id);
                if (count($other_attachments) == 0) {
                    // okey only index.html so we can delete the folder also
                    delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_request/'. $attachment->rel_id);
                }
            }
        }

        return true;
    }

    /**
     * Gets the payment purchase order.
     *
     * @param      <type>  $id     The purcahse order id
     *
     * @return     <type>  The payment purchase order.
     */
    public function get_payment_purchase_order($id){
        $this->db->where('pur_order',$id);
        return $this->db->get(db_prefix().'pur_order_payment')->result_array();
    }

    /**
     * Adds a payment.
     *
     * @param      <type>   $data       The data
     * @param      <type>   $pur_order  The pur order id
     *
     * @return     boolean  ( return id payment after insert )
     */
    public function add_payment($data, $pur_order){
        $data['date'] = to_sql_date($data['date']);
        $data['daterecorded'] = date('Y-m-d H:i:s');
        $data['amount'] = str_replace(',', '', $data['amount']);
        $data['pur_order'] = $pur_order;

        $this->db->insert(db_prefix().'pur_order_payment',$data);
        $insert_id = $this->db->insert_id();
        if($insert_id){
            hooks()->do_action('after_pur_order_payment_added', $insert_id);

            return $insert_id;
        }
        return false;
    }

    /**
     * { delete payment }
     *
     * @param      <type>   $id     The identifier
     *
     * @return     boolean  ( delete payment )
     */
    public function delete_payment($id){
        $this->db->where('id',$id);
        $this->db->delete(db_prefix().'pur_invoice_payment');
        if ($this->db->affected_rows() > 0) {
            hooks()->do_action('after_payment_pur_invoice_deleted', $id);

            return true;
        }
        return false;
    }

    /**
     * { purorder pdf }
     *
     * @param      <type>  $pur_request  The pur request
     *
     * @return     <type>  ( purorder pdf )
     */
    public function purorder_pdf($pur_order)
    {
        return app_pdf('pur_order', module_dir_path(PURCHASE_MODULE_NAME, 'libraries/pdf/Pur_order_pdf'), $pur_order);
    }


    /**
     * Gets the pur request pdf html.
     *
     * @param      <type>  $pur_request_id  The pur request identifier
     *
     * @return     string  The pur request pdf html.
     */
    public function get_purorder_pdf_html($pur_order_id){
        

        $pur_order = $this->get_pur_order($pur_order_id);
        $pur_order_detail = $this->get_pur_order_detail($pur_order_id);
        $list_approve_status = $this->get_list_approval_details($pur_order_id,'pur_order');

        $company_name = get_option('invoice_company_name'); 
        $vendor = $this->get_vendor($pur_order->vendor);
        $tax_data = $this->get_html_tax_pur_order($pur_order_id);
        $base_currency = get_base_currency_pur();
        if($pur_order->currency != 0){
            $base_currency = pur_get_currency_by_id($pur_order->currency);
        }

        $address = '';
        $vendor_name = '';

        $ship_to = $pur_order->shipping_address . ' '.  $pur_order->shipping_city.' '.$pur_order->shipping_state.' '.$pur_order->shipping_zip.' '.$pur_order->shipping_country_text.' '.get_country_name($pur_order->shipping_country) ;
        if($ship_to == ''){

            $ship_to = get_option('pur_company_address') . ' '.  get_option('pur_company_city').' '.get_option('pur_company_state').' '.get_option('pur_company_zipcode').' '.get_option('pur_company_country_text').' '.get_country_name(get_option('pur_company_country_code'));
            
            if($ship_to == ''){
                $ship_to = get_option('invoice_company_address') . ' '.  get_option('invoice_company_city').' '.get_option('company_state').' '.get_option('invoice_company_country_code') ;
            }
        }

        if($vendor){
            $countryName = '';
            if($country = get_country($vendor->country) ){
                $countryName = $country->short_name;
            }



            $address = $vendor->address.'<br>'. $vendor->city.'<br>'.$vendor->state. $vendor->zip.$countryName;
            $vendor_name = $vendor->company;

            $ship_country_name = '';
            if($ship_country = get_country($vendor->shipping_country)){
                $ship_country_name = $ship_country->short_name;
            }
            
        }

        $day = _d($pur_order->order_date);
       
    $html = '';     
    $html .= '<table class="table">
        <tbody>
          <tr>
            <td rowspan="6" class="text-left" style="width: 70%">
            '.get_po_logo(get_option('pdf_logo_width'), "img img-responsive").'
             <br>'.format_organization_info().'
            </td>
            <td class="text-right" style="width: 30%">
                <strong class="fsize20">'.mb_strtoupper(_l('purchase_order')).'</strong><br>
                <strong>'.mb_strtoupper($pur_order->pur_order_number).'</strong><br>
            </td>
          </tr>

          <tr>
            <td class="text-right" style="width: 30%">
                <br><strong>'._l('pur_vendor').'</strong>    
                <br>'. $vendor_name.'
                <br>'. strip_tags($address).'
            </td>
            <td></td>
          </tr>

          <tr>
            <td></td>
          </tr>
          <tr>
            <td class="text-right" style="width: 30%">
                <br><strong>'._l('pur_ship_to').'</strong>    
                <br>'. strip_tags($ship_to).'
                </td>
            <td></td>
          </tr>

          <tr>
            <td></td>
          </tr>
          <tr>
            <td class="text-right">'. _l('order_date').': '. $day.'</td>
            <td></td>
          </tr>
        </tbody>

      </table>
      <br><br><br>
      ';

      $html .=  '<table class="table purorder-item">
        <thead>
          <tr>
            <th class="thead-dark" style="width: 30%;">'._l('items').'</th>
            <th class="thead-dark" style="width: 15%;" align="right">'._l('purchase_unit_price').'</th>
            <th class="thead-dark" style="width: 15%;" align="right">'._l('purchase_quantity').'</th>';
         
            if(get_option('show_purchase_tax_column') == 1){ 

                $html .= '<th class="thead-dark" align="right" style="width: 10%;">'._l('tax').'</th>';
            }
 
            $html .= '<th class="thead-dark" align="right" style="width: 15%;">'._l('discount').'</th>
            <th class="thead-dark" align="right" style="width: 15%;">'._l('total').'</th>
          </tr>
          </thead>
          <tbody>';
        $t_mn = 0;
        $item_discount = 0;
      foreach($pur_order_detail as $row){
        $items = $this->get_items_by_id($row['item_code']);
        $des_html = ($items) ? $items->commodity_code.' - '.$items->description : $row['item_name'];

        $units = $this->get_units_by_id($row['unit_id']);
        $unit_name = isset($units->unit_name) ? $units->unit_name : '';
        
        $html .= '<tr nobr="true" class="sortable">
            <td style="width: 30%;"><strong>'.$des_html.'</strong><br><span>'.$row['description'].'</span></td>
            <td style="width: 15%;"align="right">'.app_format_money($row['unit_price'],$base_currency->symbol).'</td>
            <td style="width: 15%;" align="right">'.app_format_number($row['quantity'],'').' '. $unit_name.'</td>';
         
            if(get_option('show_purchase_tax_column') == 1){  
                $html .= '<td align="right" style="width: 10%;">'.app_format_money($row['total'] - $row['into_money'],$base_currency->symbol).'</td>';
            }
       
            $html .= '<td align="right" style="width: 15%;">'.app_format_money($row['discount_money'],$base_currency->symbol).'</td>
            <td align="right" style="width: 15%;">'.app_format_money($row['total_money'],$base_currency->symbol).'</td>
          </tr>';

        $t_mn += $row['total_money'];
        $item_discount += $row['discount_money'];
      }  
      $html .=  '</tbody>
      </table><br><br>';

      $html .= '<table class="table text-right"><tbody>';
      $html .= '<tr id="subtotal">
                    <td style="width: 33%"></td>
                     <td>'._l('subtotal').' </td>
                     <td class="subtotal">
                        '.app_format_money($pur_order->subtotal,$base_currency->symbol).'
                     </td>
                  </tr>';

      $html .= $tax_data['pdf_html'];

      if(($pur_order->discount_total + $item_discount) > 0){
        $html .= '
                  
                  <tr id="subtotal">
                  <td style="width: 33%"></td>
                     <td>'._l('discount_total(money)').'</td>
                     <td class="subtotal">
                        '.app_format_money(($pur_order->discount_total + $item_discount), $base_currency->symbol).'
                     </td>
                  </tr>';
      }

      if($pur_order->shipping_fee  > 0){
        $html .= '
                  
                  <tr id="subtotal">
                  <td style="width: 33%"></td>
                     <td>'._l('pur_shipping_fee').'</td>
                     <td class="subtotal">
                        '.app_format_money($pur_order->shipping_fee, $base_currency->symbol).'
                     </td>
                  </tr>';
      }
      $html .= '<tr id="subtotal">
                 <td style="width: 33%"></td>
                 <td>'. _l('total').'</td>
                 <td class="subtotal">
                    '. app_format_money($pur_order->total, $base_currency->symbol).'
                 </td>
              </tr>';

      $html .= ' </tbody></table><br><br>';

    $custom_fields = get_custom_fields('pur_order');
     foreach($custom_fields as $field){
      $value = get_custom_field_value($pur_order->id,$field['id'],'pur_order');
          if($value == ''){continue;}
      $html .= '<span class="">
      <strong>'.$field['name'].': </strong>'.$value.'
      </span><br>';
      } 

      $html .= '<div class="col-md-12 mtop15">
                        <h4>'. _l('terms_and_conditions').':</h4><p>'. $pur_order->terms .'</p>
                       
                     </div>';
      if(count($list_approve_status) > 0){
          $html .= '<br>
          <br>
          <br>
          <br>';

          $html .=  '<table class="table">
            <tbody>
              <tr>';

            foreach ($list_approve_status as $value) {
         $html .= '<td class="td_appr">';
            if($value['action'] == 'sign'){
                $html .= '<h3>'.mb_strtoupper(get_staff_full_name($value['staffid'])).'</h3>';
                if($value['approve'] == 2){ 
                    $html .= '<img src="'.FCPATH. 'modules/purchase/uploads/pur_order/signature/'.$pur_order->id.'/signature_'.$value['id'].'.png" class="img_style">';
                }
                    
            }else{ 
            $html .= '<h3>'.mb_strtoupper(get_staff_full_name($value['staffid'])).'</h3>';
                  if($value['approve'] == 2){ 
            $html .= '<img src="'.FCPATH.'modules/purchase/uploads/approval/approved.png" class="img_style">';
                 }elseif($value['approve'] == 3){
            $html .= '<img src="'.FCPATH.'modules/purchase/uploads/approval/rejected.png" class="img_style">';
                 }
                  
                    }
           $html .= '</td>';
            }
           
         $html .= '</tr>
            </tbody>
          </table>';
        
    }
      $html .=  '<link href="' . FCPATH.'modules/purchase/assets/css/pur_order_pdf.css' . '"  rel="stylesheet" type="text/css" />';
      return $html;
    }

    /**
     * clear signature
     *
     * @param      string   $id     The identifier
     *
     * @return     boolean  ( description_of_the_return_value )
     */
    public function clear_signature($id)
    {
        $this->db->select('signature');
        $this->db->where('id', $id);
        $contract = $this->db->get(db_prefix() . 'pur_contracts')->row();

        if ($contract) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'pur_contracts', ['signed_status' => 'not_signed']);

            if (!empty($contract->signature)) {
                unlink(PURCHASE_MODULE_UPLOAD_FOLDER.'/contract_sign/' . $id . '/' . $contract->signature);
            }

            return true;
        }


        return false;
    }

    /**
     * get data Purchase statistics by cost
     *
     * @param      string  $year   The year
     *
     * @return     array
     */
    public function cost_of_purchase_orders_analysis($year = '', $currency = ''){
        if($year == ''){
            $year = date('Y');
        }

        $base_currency = get_base_currency_pur();

        if($currency == $base_currency->id){
            $where = 'AND '.db_prefix().'pur_orders.currency IN (0, '.$currency.')';
        }else{
            $where =  'AND '.db_prefix().'pur_orders.currency = '.$currency;
        }


        $query = $this->db->query('SELECT DATE_FORMAT(order_date, "%m") AS month, Sum((SELECT SUM(total_money) as total FROM '.db_prefix().'pur_order_detail where pur_order = '.db_prefix().'pur_orders.id)) as total 
            FROM '.db_prefix().'pur_orders where DATE_FORMAT(order_date, "%Y") = '.$year.' '. $where.'
            group by month')->result_array();
        $result = [];
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $cost = [];
        $rs = 0;
        foreach ($query as $value) {
            if($value['total'] > 0){
                $result[$value['month'] - 1] =  (double)$value['total'];
            }
        }
        return $result;
    }

    /**
     * get data Purchase statistics by number of purchase orders
     *
     * @param      string  $year   The year
     *
     * @return     array
     */
    public function number_of_purchase_orders_analysis($year = ''){
        if($year == ''){
            $year = date('Y');
        }
        $query = $this->db->query('SELECT DATE_FORMAT(order_date, "%m") AS month, Count(*) as count 
            FROM '.db_prefix().'pur_orders where DATE_FORMAT(order_date, "%Y") = '.$year.'
            group by month')->result_array();
        $result = [];
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $cost = [];
        $rs = 0;
        foreach ($query as $value) {
            if($value['count'] > 0){
                $result[$value['month'] - 1] =  (int)$value['count'];
            }
        }
        return $result;
    }

    /**
     * Gets the payment by vendor.
     *
     * @param      <type>  $vendor  The vendor
     */
    public function get_payment_by_vendor($vendor){
        return  $this->db->query('select pop.pur_order, pop.id as pop_id, pop.amount, pop.date, pop.paymentmode, pop.transactionid, po.pur_order_name from '.db_prefix().'pur_order_payment pop left join '.db_prefix().'pur_orders po on po.id = pop.pur_order where po.vendor = '.$vendor)->result_array();
    }

/**
     * get unit add item 
     * @return array
     */
    public function get_unit_add_item()
    {
        return $this->db->query('select * from tblware_unit_type where display = 1 order by tblware_unit_type.order asc ')->result_array();
    }

    /**
     * get commodity
     * @param  boolean $id
     * @return array or object
     */
    public function get_item($id = false)
    {

        if (is_numeric($id)) {
        $this->db->where('id', $id);

            return $this->db->get(db_prefix() . 'items')->row();
        }
        if ($id == false) {
            return $this->db->query('select * from ' . db_prefix() . 'items where active = 1 order by id desc')->result_array();

        }

    }

    /**
     * get inventory commodity
     * @param  integer $commodity_id 
     * @return array            
     */
    public function get_inventory_item($commodity_id){
        $sql ='SELECT '.db_prefix().'warehouse.warehouse_code, sum(inventory_number) as inventory_number, unit_name FROM '.db_prefix().'inventory_manage 
            LEFT JOIN '.db_prefix().'items on '.db_prefix().'inventory_manage.commodity_id = '.db_prefix().'items.id 
            LEFT JOIN '.db_prefix().'ware_unit_type on '.db_prefix().'items.unit_id = '.db_prefix().'ware_unit_type.unit_type_id
            LEFT JOIN '.db_prefix().'warehouse on '.db_prefix().'inventory_manage.warehouse_id = '.db_prefix().'warehouse.warehouse_id
             where commodity_id = '.$commodity_id. ' group by '.db_prefix().'inventory_manage.warehouse_id';
        return  $this->db->query($sql)->result_array();


    }

    /**
     * get warehourse attachments
     * @param  integer $commodity_id 
     * @return array               
     */
    public function get_item_attachments($commodity_id){

        $this->db->order_by('dateadded', 'desc');
        $this->db->where('rel_id', $commodity_id);
        $this->db->where('rel_type', 'commodity_item_file');

        return $this->db->get(db_prefix() . 'files')->result_array();

    }

    /**
     * generate commodity barcode
     *
     * @return     string 
     */
    public function generate_commodity_barcode(){
        $item = false;
        do{
            $length = 11;
            $chars = '0123456789';
            $count = mb_strlen($chars);
            $password = '';
            for ($i = 0; $i < $length; $i++) {
                $index = rand(0, $count - 1);
                $password .= mb_substr($chars, $index, 1);
            }
            $this->db->where('commodity_barcode',$password);
            $item = $this->db->get(db_prefix().'items')->row();
        }while ($item);

        return $password;
    }

    /**
     * add commodity one item
     * @param array $data
     * @return integer 
     */
    public function add_commodity_one_item($data){
        /*add data tblitem*/
        $data['rate'] = $data['rate'];
        $data['purchase_price'] = $data['purchase_price'];
        $data['can_be_purchased'] = 'can_be_purchased';
        $data['can_be_sold'] = null;
        $data['can_be_manufacturing'] = null;
        $data['can_be_inventory'] = null;

        /*create sku code*/
        if($data['sku_code'] != ''){
            $data['sku_code'] = $data['sku_code'];
        }else{
            $data['sku_code'] = $this->create_sku_code('', '');
        }
        
        //update column unit name use sales/items
        $unit_type = get_unit_type_item($data['unit_id']);
        if($unit_type && !is_array($unit_type)){
            $data['unit'] = $unit_type->unit_name;
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        $this->db->insert(db_prefix().'items', $data);
        $insert_id = $this->db->insert_id();

        if($insert_id){
            if (isset($custom_fields)) {
                handle_custom_fields_post($insert_id, $custom_fields, true);
            }

            return $insert_id;
        }

        /*add data tblinventory*/
        return false;

    }


    /**
     * update commodity one item
     * @param  array $data 
     * @param  integer $id   
     * @return boolean        
     */
    public function update_commodity_one_item($data,$id){
        /*add data tblitem*/
        $affectedRows = 0;
        $data['rate'] = $data['rate'];
        $data['purchase_price'] = $data['purchase_price'];

        //update column unit name use sales/items
        $unit_type = get_unit_type_item($data['unit_id']);
        if($unit_type){
            $data['unit'] = $unit_type->unit_name;
        }


        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        $this->db->where('id',$id);
        $this->db->update(db_prefix().'items',$data);
        if($this->db->affected_rows() > 0){
            $affectedRows++;
        }

        if (isset($custom_fields)) {
            if (handle_custom_fields_post($id, $custom_fields, true)) {
                $affectedRows++;
            }
        }
        
        if($affectedRows > 0){
            return true;
        }
        return false;
    }

    /**
     * create sku code 
     * @param  int commodity_group 
     * @param  int sub_group 
     * @return string
     */
    public function  create_sku_code($commodity_group, $sub_group)
    {
        // input  commodity group, sub group
        //get commodity group from id
        $group_character = '';
        if(isset($commodity_group)){

            $sql_group_where = 'SELECT * FROM '.db_prefix().'items_groups where id = "'.$commodity_group.'"';
            $group_value = $this->db->query($sql_group_where)->row();
            if($group_value){

                if($group_value->commodity_group_code != ''){
                    $group_character = mb_substr($group_value->commodity_group_code, 0, 1, "UTF-8").'-';

                }
            }

        }

        //get sku code from sku id
        $sub_code = '';
        



        $sql_where = 'SELECT * FROM '.db_prefix().'items order by id desc limit 1';
        $last_commodity_id = $this->db->query($sql_where)->row();
        if($last_commodity_id){
            $next_commodity_id = (int)$last_commodity_id->id + 1;
        }else{
            $next_commodity_id = 1;
        }
        $commodity_id_length = strlen((string)$next_commodity_id);

        $commodity_str_betwen ='';

        $create_candidate_code='';

        switch ($commodity_id_length) {
            case 1:
                $commodity_str_betwen = '000';
                break;
            case 2:
                $commodity_str_betwen = '00';
                break;
            case 3:
                $commodity_str_betwen = '0';
                break;

            default:
                $commodity_str_betwen = '0';
                break;
        }

 
        return  $group_character.$sub_code.$commodity_str_betwen.$next_commodity_id; // X_X_000.id auto increment

        
    }


    /**
     * get commodity group add commodity
     * @return array
     */
    public function get_commodity_group_add_commodity()
    {

        return $this->db->query('select * from tblitems_groups where display = 1 order by tblitems_groups.order asc ')->result_array();
    }


    //delete _commodity_file file for any 
    /**
     * delete commodity file
     * @param  integer $attachment_id 
     * @return boolean                
     */
    public function delete_commodity_file($attachment_id)
    {
        $deleted    = false;
        $attachment = $this->get_commodity_attachments_delete($attachment_id);

        if ($attachment) {
            if (empty($attachment->external)) {
                if(file_exists(PURCHASE_MODULE_ITEM_UPLOAD_FOLDER .$attachment->rel_id.'/'.$attachment->file_name)){
                    unlink(PURCHASE_MODULE_ITEM_UPLOAD_FOLDER .$attachment->rel_id.'/'.$attachment->file_name);
                }else{
                    unlink('modules/warehouse/uploads/item_img/' .$attachment->rel_id.'/'.$attachment->file_name);
                }
            }
            $this->db->where('id', $attachment->id);
            $this->db->delete(db_prefix() . 'files');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
                log_activity('commodity Attachment Deleted [commodityID: ' . $attachment->rel_id . ']');
            }
            if(file_exists(PURCHASE_MODULE_ITEM_UPLOAD_FOLDER .$attachment->rel_id.'/'.$attachment->file_name)){
                if (is_dir(PURCHASE_MODULE_ITEM_UPLOAD_FOLDER .$attachment->rel_id)) {
                    // Check if no attachments left, so we can delete the folder also
                    $other_attachments = list_files(PURCHASE_MODULE_ITEM_UPLOAD_FOLDER .$attachment->rel_id);
                    if (count($other_attachments) == 0) {
                        // okey only index.html so we can delete the folder also
                        delete_dir(PURCHASE_MODULE_ITEM_UPLOAD_FOLDER .$attachment->rel_id);
                    }
                }
            }else{
                if (is_dir(site_url('modules/warehouse/uploads/item_img/') .$attachment->rel_id)) {
                    // Check if no attachments left, so we can delete the folder also
                    $other_attachments = list_files(site_url('modules/warehouse/uploads/item_img/') .$attachment->rel_id);
                    if (count($other_attachments) == 0) {
                        // okey only index.html so we can delete the folder also
                        delete_dir(site_url('modules/warehouse/uploads/item_img/') .$attachment->rel_id);
                    }
                }
            }
        }

        return $deleted;
    }

    /**
     * get commodity attachments delete
     * @param  integer $id 
     * @return object     
     */
    public function get_commodity_attachments_delete($id){

        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix() . 'files')->row();
        }
    }

    /**
     * get unit type
     * @param  boolean $id
     * @return array or object
     */
    public function get_unit_type($id = false)
    {

        if (is_numeric($id)) {
        $this->db->where('unit_type_id', $id);

            return $this->db->get(db_prefix() . 'ware_unit_type')->row();
        }
        if ($id == false) {
            return $this->db->query('select * from tblware_unit_type')->result_array();
        }

    }

    /**
     * add unit type 
     * @param array  $data
     * @param boolean $id
     * return boolean
     */
    public function add_unit_type($data, $id = false){
        
        $unit_type = str_replace(', ','|/\|',$data['hot_unit_type']);
        $data_unit_type = explode( ',', $unit_type);
        $results = 0;
        $results_update = '';
        $flag_empty = 0;

        
        foreach ($data_unit_type as  $unit_type_key => $unit_type_value) {
            if($unit_type_value == ''){
                    $unit_type_value = 0;
                }
            if(($unit_type_key+1)%6 == 0){
                $arr_temp['note'] = str_replace('|/\|',', ',$unit_type_value);
                
                if($id == false && $flag_empty == 1){
                    $this->db->insert(db_prefix().'ware_unit_type', $arr_temp);
                    $insert_id = $this->db->insert_id();
                    if($insert_id){
                        $results++;
                    }
                }
                if(is_numeric($id) && $flag_empty == 1){
                    $this->db->where('unit_type_id', $id);
                    $this->db->update(db_prefix() . 'ware_unit_type', $arr_temp);
                    if ($this->db->affected_rows() > 0) {
                        $results_update = true;
                    }else{
                        $results_update = false;
                    }
                }
                $flag_empty =0;
                $arr_temp = [];
            }else{

                switch (($unit_type_key+1)%6) {
                    case 1:
                     $arr_temp['unit_code'] = str_replace('|/\|',', ',$unit_type_value);

                        if($unit_type_value != '0'){
                            $flag_empty = 1;
                        }
                        break;
                    case 2:
                    $arr_temp['unit_name'] = str_replace('|/\|',', ',$unit_type_value);
                        break;
                    case 3:
                    $arr_temp['unit_symbol'] = $unit_type_value;
                        break;
                    case 4:
                    $arr_temp['order'] = $unit_type_value;
                        break;
                     case 5:
                     if($unit_type_value == 'yes'){
                        $display_value = 1;
                     }else{
                        $display_value = 0;
                     }
                    $arr_temp['display'] = $display_value;
                        break;
                }
            }

        }

        if($id == false){
            return $results > 0 ? true : false;
        }else{
            return $results_update ;
        }

    }

    /**
     * delete unit type
     * @param  integer $id
     * @return boolean
     */
    public function delete_unit_type($id){
        $this->db->where('unit_type_id', $id);
        $this->db->delete(db_prefix() . 'ware_unit_type');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * delete commodity
     * @param  integer $id
     * @return boolean
     */
    public function delete_commodity($id){
        $this->db->where('items', $id);
        $this->db->delete(db_prefix().'pur_vendor_items');

        $this->db->where('id', $id);
        $item = $this->db->get(db_prefix().'items')->row();
        if($item && $item->from_vendor_item != null){
            $this->db->where('id', $item->from_vendor_item);
            $this->db->update(db_prefix().'items_of_vendor', ['share_status' => 0]);
        }

        $this->db->where('relid', $id);
        $this->db->where('fieldto', 'items_pr');
        $this->db->delete(db_prefix() . 'customfieldsvalues');

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'items');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * { mark converted pur order }
     *
     * @param      <int>  $pur_order  The pur order
     * @param      <int>  $expense    The expense
     */
    public function mark_converted_pur_order($pur_order, $expense){
        $this->db->where('id',$pur_order);
        $this->db->update(db_prefix().'pur_orders',['expense_convert' => $expense]);
        if ($this->db->affected_rows() > 0) {
            // accouting module hook after expense converted
            hooks()->do_action('pur_after_expense_converted', $expense);

            return true;
        }
        return false;
    }

    /**
     * { delete purchase vendor attachment }
     *
     * @param      <type>   $id     The identifier
     *
     * @return     boolean  
     */
    public function delete_ic_attachment($id)
    {
        $attachment = $this->get_ic_attachments('', $id);
        $deleted    = false;
        if ($attachment) {
            if (empty($attachment->external)) {
                unlink(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_vendor/'. $attachment->rel_id . '/' . $attachment->file_name);
            }
            $this->db->where('id', $attachment->id);
            $this->db->delete('tblfiles');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
            }

            if (is_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_vendor/'. $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_vendor/'. $attachment->rel_id);
                if (count($other_attachments) == 0) {
                    // okey only index.html so we can delete the folder also
                    delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_vendor/'. $attachment->rel_id);
                }
            }
        }

        return $deleted;
    }

    /**
     * Gets the ic attachments.
     *
     * @param      <type>  $assets  The assets
     * @param      string  $id      The identifier
     *
     * @return     <type>  The ic attachments.
     */
    public function get_ic_attachments($assets, $id = '')
    {
        // If is passed id get return only 1 attachment
        if (is_numeric($id)) {
            $this->db->where('id', $id);
        } else {
            $this->db->where('rel_id', $assets);
        }
        $this->db->where('rel_type', 'pur_vendor');
        $result = $this->db->get('tblfiles');
        if (is_numeric($id)) {
            return $result->row();
        }

        return $result->result_array();
    }

    /**
     * Change contact password, used from client area
     * @param  mixed $id          contact id to change password
     * @param  string $oldPassword old password to verify
     * @param  string $newPassword new password
     * @return boolean
     */
    public function change_contact_password($id, $oldPassword, $newPassword)
    {
        // Get current password
        $this->db->where('id', $id);
        $client = $this->db->get(db_prefix() . 'pur_contacts')->row();

        if (!app_hasher()->CheckPassword($oldPassword, $client->password)) {
            return [
                'old_password_not_match' => true,
            ];
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'pur_contacts', [
            'last_password_change' => date('Y-m-d H:i:s'),
            'password'             => app_hash_password($newPassword),
        ]);

        if ($this->db->affected_rows() > 0) {
            log_activity('Contact Password Changed [ContactID: ' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * Gets the pur order by vendor.
     *
     * @param      <type>  $vendor  The vendor
     */
    public function get_pur_order_by_vendor($vendor){
        $this->db->where('vendor',$vendor);
        return $this->db->get(db_prefix().'pur_orders')->result_array();
    }

    public function get_contracts_by_vendor($vendor){
        $this->db->where('vendor',$vendor);
        return $this->db->get(db_prefix().'pur_contracts')->result_array();
    }

    /**
     * @param  integer ID
     * @param  integer Status ID
     * @return boolean
     * Update contact status Active/Inactive
     */
    public function change_contact_status($id, $status)
    {

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'pur_contacts', [
            'active' => $status,
        ]);
        if ($this->db->affected_rows() > 0) {
            
            return true;
        }

        return false;
    }

    /**
     * Gets the item by group.
     *
     * @param        $group  The group
     *
     * @return      The item by group.
     */
    public function get_item_by_group($group){
        $this->db->where('group_id',$group);
        return $this->db->get(db_prefix().'items')->result_array();
    }  

    /**
     * Adds vendor items.
     *
     * @param      $data   The data
     *
     * @return     boolean 
     */
    public function add_vendor_items($data){
        $rs = 0;
        $data['add_from'] = get_staff_user_id();
        $data['datecreate'] = date('Y-m-d');
        foreach($data['items'] as $val){
            $this->db->insert(db_prefix().'pur_vendor_items',[
                'vendor' => $data['vendor'],
                'group_items' => $data['group_item'],
                'items' => $val,
                'add_from' => $data['add_from'],
                'datecreate' => $data['datecreate'],
            ]);
            $insert_id = $this->db->insert_id();

            if($insert_id){
                $rs++;
            }
        }

        if($rs > 0){
            return true;
        }
        return false;
    } 

    /**
     * { delete vendor items }
     *
     * @param      <type>   $id     The identifier
     *
     * @return     boolean  
     */
    public function delete_vendor_items($id){
        $this->db->where('id',$id);
        $this->db->delete(db_prefix().'pur_vendor_items');
        if ($this->db->affected_rows() > 0) {
            
            return true;
        }
        return false;
    }

    /**
     * Gets the item by vendor.
     *
     * @param      $vendor  The vendor
     */
    public function get_item_by_vendor($vendor){
        
        $this->db->where('vendor',$vendor);
        return $this->db->get(db_prefix().'pur_vendor_items')->result_array();  
    }

    /**
     * Gets the items.
     *
     * @return     <array>  The items.
     */
    public function get_items_hs_vendor($vendor){
       return $this->db->query('select items as id, CONCAT(it.commodity_code," - " ,it.description) as label from '.db_prefix().'pur_vendor_items pit LEFT JOIN '.db_prefix().'items it ON it.id = pit.items where pit.vendor = '.$vendor)->result_array();
    }

    /**
     * get commodity group type
     * @param  boolean $id
     * @return array or object
     */
    public function get_commodity_group_type($id = false) {

        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix() . 'items_groups')->row();
        }
        if ($id == false) {
            return $this->db->query('select * from tblitems_groups')->result_array();
        }

    }

    /**
     * add commodity group type
     * @param array  $data
     * @param boolean $id
     * return boolean
     */
    public function add_commodity_group_type($data, $id = false) {
        $data['commodity_group'] = str_replace(', ', '|/\|', $data['hot_commodity_group_type']);

        $data_commodity_group_type = explode(',', $data['commodity_group']);
        $results = 0;
        $results_update = '';
        $flag_empty = 0;

        foreach ($data_commodity_group_type as $commodity_group_type_key => $commodity_group_type_value) {
            if ($commodity_group_type_value == '') {
                $commodity_group_type_value = 0;
            }
            if (($commodity_group_type_key + 1) % 5 == 0) {

                $arr_temp['note'] = str_replace('|/\|', ', ', $commodity_group_type_value);

                if ($id == false && $flag_empty == 1) {
                    $this->db->insert(db_prefix() . 'items_groups', $arr_temp);
                    $insert_id = $this->db->insert_id();
                    if ($insert_id) {
                        $results++;
                    }
                }
                if (is_numeric($id) && $flag_empty == 1) {
                    $this->db->where('id', $id);
                    $this->db->update(db_prefix() . 'items_groups', $arr_temp);
                    if ($this->db->affected_rows() > 0) {
                        $results_update = true;
                    } else {
                        $results_update = false;
                    }
                }

                $flag_empty = 0;
                $arr_temp = [];
            } else {

                switch (($commodity_group_type_key + 1) % 5) {
                case 1:
                    if(is_numeric($id)){
                        //update
                        $arr_temp['commodity_group_code'] = str_replace('|/\|', ', ', $commodity_group_type_value);
                            $flag_empty = 1;

                    }else{
                        //add
                        $arr_temp['commodity_group_code'] = str_replace('|/\|', ', ', $commodity_group_type_value);

                        if ($commodity_group_type_value != '0') {
                            $flag_empty = 1;
                        }
                        
                    }
                    break;
                case 2:
                    $arr_temp['name'] = str_replace('|/\|', ', ', $commodity_group_type_value);
                    break;
                case 3:
                    $arr_temp['order'] = $commodity_group_type_value;
                    break;
                case 4:
                    //display 1: display (yes) , 0: not displayed (no)
                    if ($commodity_group_type_value == 'yes') {
                        $display_value = 1;
                    } else {
                        $display_value = 0;
                    }
                    $arr_temp['display'] = $display_value;
                    break;
                }
            }

        }

        if ($id == false) {
            return $results > 0 ? true : false;
        } else {
            return $results_update;
        }

    }

    /**
     * delete commodity group type
     * @param  integer $id
     * @return boolean
     */
    public function delete_commodity_group_type($id) {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'items_groups');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * get sub group
     * @param  boolean $id
     * @return array  or object
     */
    public function get_sub_group($id = false) {

        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix() . 'wh_sub_group')->row();
        }
        if ($id == false) {
            return $this->db->query('select * from tblwh_sub_group')->result_array();
        }

    }

    /**
     * get item group
     * @return array 
     */
    public function get_item_group() {
        return $this->db->query('select id as id, CONCAT(name,"_",commodity_group_code) as label from ' . db_prefix() . 'items_groups')->result_array();
    }

    /**
     * add sub group
     * @param array  $data
     * @param boolean $id
     * @return boolean
     */
    public function add_sub_group($data, $id = false) {
        $commodity_type = str_replace(', ', '|/\|', $data['hot_sub_group']);

        $data_commodity_type = explode(',', $commodity_type);
        $results = 0;
        $results_update = '';
        $flag_empty = 0;

        foreach ($data_commodity_type as $commodity_type_key => $commodity_type_value) {
            if ($commodity_type_value == '') {
                $commodity_type_value = 0;
            }
            if (($commodity_type_key + 1) % 6 == 0) {
                $arr_temp['note'] = str_replace('|/\|', ', ', $commodity_type_value);

                if ($id == false && $flag_empty == 1) {
                    $this->db->insert(db_prefix() . 'wh_sub_group', $arr_temp);
                    $insert_id = $this->db->insert_id();
                    if ($insert_id) {
                        $results++;
                    }
                }
                if (is_numeric($id) && $flag_empty == 1) {
                    $this->db->where('id', $id);
                    $this->db->update(db_prefix() . 'wh_sub_group', $arr_temp);
                    if ($this->db->affected_rows() > 0) {
                        $results_update = true;
                    } else {
                        $results_update = false;
                    }
                }
                $flag_empty = 0;
                $arr_temp = [];
            } else {

                switch (($commodity_type_key + 1) % 6) {
                case 1:
                    $arr_temp['sub_group_code'] = str_replace('|/\|', ', ', $commodity_type_value);
                    if ($commodity_type_value != '0') {
                        $flag_empty = 1;
                    }
                    break;
                case 2:
                    $arr_temp['sub_group_name'] = str_replace('|/\|', ', ', $commodity_type_value);
                    break;
                case 3:
                    $arr_temp['group_id'] = $commodity_type_value;
                    break;
                case 4:
                    $arr_temp['order'] = $commodity_type_value;
                    break;
                case 5:
                    //display 1: display (yes) , 0: not displayed (no)
                    if ($commodity_type_value == 'yes') {
                        $display_value = 1;
                    } else {
                        $display_value = 0;
                    }
                    $arr_temp['display'] = $display_value;
                    break;
                }
            }

        }

        if ($id == false) {
            return $results > 0 ? true : false;
        } else {
            return $results_update;
        }

    }

    /**
     * delete_sub_group
     * @param  integer $id
     * @return boolean
     */
    public function delete_sub_group($id) {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'wh_sub_group');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * list subgroup by group
     * @param  integer $group 
     * @return string        
     */
    public function list_subgroup_by_group($group)
    {
        $this->db->where('group_id', $group);
        $arr_subgroup = $this->db->get(db_prefix().'wh_sub_group')->result_array();

        $options = '';
        if(count($arr_subgroup) > 0){
            foreach ($arr_subgroup as $value) {

              $options .= '<option value="' . $value['id'] . '">' . $value['sub_group_name'] . '</option>';
            }

        }
        return $options;

    }

    /**
     * get item tag filter
     * @return array 
     */
    public function get_item_tag_filter()
    {
        return $this->db->query('select * FROM '.db_prefix().'taggables left join '.db_prefix().'tags on '.db_prefix().'taggables.tag_id =' .db_prefix().'tags.id where '.db_prefix().'taggables.rel_type = "pur_order"')->result_array();
    }

    /**
     * Gets the pur contract attachment.
     *
     * @param        $id     The identifier
     */
    public function get_pur_contract_attachment($id){
        $this->db->where('rel_id',$id);
        $this->db->where('rel_type','pur_contract');
        return $this->db->get(db_prefix().'files')->result_array();
    }

    /**
     * Gets the pur contract attachments.
     *
     * @param        $assets  The assets
     * @param      string  $id      The identifier
     *
     * @return       The pur contract attachments.
     */
    public function get_pur_contract_attachments($assets, $id = '')
    {
        // If is passed id get return only 1 attachment
        if (is_numeric($id)) {
            $this->db->where('id', $id);
        } else {
            $this->db->where('rel_id', $assets);
        }
        $this->db->where('rel_type', 'pur_contract');
        $result = $this->db->get(db_prefix().'files');
        if (is_numeric($id)) {
            return $result->row();
        }

        return $result->result_array();
    }

    /**
     * { delete purchase contract attachment }
     *
     * @param         $id     The identifier
     *
     * @return     boolean  
     */
    public function delete_pur_contract_attachment($id)
    {
        $attachment = $this->get_pur_contract_attachments('', $id);
        $deleted    = false;
        if ($attachment) {
            if (empty($attachment->external)) {
                unlink(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_contract/'. $attachment->rel_id . '/' . $attachment->file_name);
            }
            $this->db->where('id', $attachment->id);
            $this->db->delete(db_prefix().'files');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
            }

            if (is_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_contract/'. $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_contract/'. $attachment->rel_id);
                if (count($other_attachments) == 0) {
                    // okey only index.html so we can delete the folder also
                    delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_contract/'. $attachment->rel_id);
                }
            }
        }

        return $deleted;
    }

    /**
     * Adds a vendor category.
     *
     * @param         $data   The data
     *
     * @return     id inserted 
     */
    public function add_vendor_category($data){
        $this->db->insert(db_prefix().'pur_vendor_cate',$data);
        $insert_id = $this->db->insert_id();
        if($insert_id){
            return $insert_id;
        }
        return false;
    }

    /**
     * { update vendor category }
     *
     * @param         $data   The data
     * @param        $id     The identifier
     *
     * @return     boolean   
     */
    public function update_vendor_category($data,$id){
        $this->db->where('id',$id);
        $this->db->update(db_prefix().'pur_vendor_cate',$data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * { delete vendor category }
     *
     * @param         $id     The identifier
     *
     * @return     boolean  
     */
    public function delete_vendor_category($id){
        $this->db->where('id',$id);
        $this->db->delete(db_prefix().'pur_vendor_cate');
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }

    /**
     * Gets the vendor category.
     *
     * @param      string  $id     The identifier
     *
     * @return       The vendor category.
     */
    public function get_vendor_category($id = ''){
        if($id != ''){
            $this->db->where('id',$id);
            return $this->db->get(db_prefix().'pur_vendor_cate')->row();
        }else{
            return $this->db->get(db_prefix().'pur_vendor_cate')->result_array();
        }
    }

    /**
     * Gets the purchase estimate attachments.
     *
     * @param        $id     The purchase estimate
     *
     * @return       The purchase estimate attachments.
     */
    public function get_purchase_estimate_attachments($id){
   
        $this->db->where('rel_id',$id);
        $this->db->where('rel_type','pur_estimate');
        return $this->db->get(db_prefix().'files')->result_array();
    }

    /**
     * Gets the purcahse estimate attachments.
     *
     * @param      <type>  $surope  The surope
     * @param      string  $id      The identifier
     *
     * @return     <type>  The part attachments.
     */
    public function get_estimate_attachments($surope, $id = '')
    {
        // If is passed id get return only 1 attachment
        if (is_numeric($id)) {
            $this->db->where('id', $id);
        } else {
            $this->db->where('rel_id', $assets);
        }
        $this->db->where('rel_type', 'pur_estimate');
        $result = $this->db->get(db_prefix().'files');
        if (is_numeric($id)) {
            return $result->row();
        }

        return $result->result_array();
    }

    /**
     * { delete estimate attachment }
     *
     * @param         $id     The identifier
     *
     * @return     boolean 
     */
    public function delete_estimate_attachment($id)
    {
        $attachment = $this->get_estimate_attachments('', $id);
        $deleted    = false;
        if ($attachment) {
            if (empty($attachment->external)) {
                unlink(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_estimate/'. $attachment->rel_id . '/' . $attachment->file_name);
            }
            $this->db->where('id', $attachment->id);
            $this->db->delete('tblfiles');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
            }

            if (is_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_estimate/'. $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_estimate/'. $attachment->rel_id);
                if (count($other_attachments) == 0) {
                    // okey only index.html so we can delete the folder also
                    delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_estimate/'. $attachment->rel_id);
                }
            }
        }

        return $deleted;
    }

    /**
     * { update customfield po }
     *
     * @param        $id     The identifier
     * @param        $data   The data
     */
    public function update_customfield_po($id, $data){

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                return true;
            }
        }
        return false;
    }

    /**
     * { PO voucher pdf }
     *
     * @param        $po_voucher  The Purchase order voucher
     *
     * @return      ( pdf )
     */
    public function povoucher_pdf($po_voucher)
    {
        return app_pdf('po_voucher', module_dir_path(PURCHASE_MODULE_NAME, 'libraries/pdf/Po_voucher_pdf'), $po_voucher);
    }

    /**
     * Gets the po voucher pdf html.
     *
     *
     *
     * @return     string  The request quotation pdf html.
     */
    public function get_po_voucher_html(){
        $this->load->model('departments_model');

        $po_voucher = $this->db->get(db_prefix().'pur_orders')->result_array();
        

        $company_name = get_option('invoice_company_name'); 
        
        $address = get_option('invoice_company_address'); 
        $day = date('d');
        $month = date('m');
        $year = date('Y');


    $html = '<table class="table">
        <tbody>
          <tr>
            <td class="font_td_cpn">'. _l('purchase_company_name').': '. $company_name.'</td>
            <td rowspan="2" width="" class="text-right">'.get_po_logo(get_option('pdf_logo_width')).'</td>
          </tr>
          <tr>
            <td class="font_500">'. _l('address').': '. $address.'</td>
          </tr>
         
        </tbody>
      </table>
      <table class="table">
        <tbody>
          <tr>
            
            <td class="td_ali_font"><h2 class="h2_style">'.mb_strtoupper(_l('po_voucher')).'</h2></td>
           
          </tr>
          <tr>
            
            <td class="align_cen">'. _l('days').' '.$day.' '._l('month').' '.$month.' '._l('year') .' '.$year.'</td>
            
          </tr>
          
        </tbody>
      </table><br><br><br>';

      $html .=  '<table class="table pur_request-item">
            <thead>
              <tr class="border_tr">
                <th align="left" class="thead-dark">'._l('purchase_order').'</th>
                <th  class="thead-dark">'._l('date').'</th>
                <th class="thead-dark">'._l('type').'</th>
                <th class="thead-dark">'._l('project').'</th>
                <th class="thead-dark">'._l('department').'</th>
                <th class="thead-dark">'._l('vendor').'</th>
                <th class="thead-dark">'._l('approval_status').'</th>
                <th class="thead-dark">'._l('delivery_status').'</th>
                <th class="thead-dark">'._l('payment_status').'</th>
              </tr>
            </thead>
          <tbody>';

      $tmn = 0;    
      foreach($po_voucher as $row){
        $paid = $row['total'] - purorder_left_to_pay($row['id']);
        $percent = 0;
        if($row['total'] > 0){
            $percent = ($paid / $row['total'] ) * 100;
        }

        $delivery_status = '';
        if($row['delivery_status'] == 0){
            $delivery_status = _l('undelivered');
        }else{
            $delivery_status = _l('delivered');
        }

        $project_name = '';
        $department_name = '';
        $vendor_name = get_vendor_company_name($row['vendor']);

        $project = $this->projects_model->get($row['project']);
        $department = $this->departments_model->get($row['department']);
        if($project){
            $project_name = $project->name;
        }

        if($department){
            $department_name = $department->name;
        }

        $html .= '<tr>
            <td>'.$row['pur_order_number'].'</td>
            <td>'._d($row['order_date']).'</td>
            <td>'._l($row['type']).'</td>
            <td>'.$project_name.'</td>
            <td>'.$department_name.'</td>
            <td>'.$vendor_name.'</td>
            <td>'.get_status_approve($row['approve_status']).'</td>
            <td>'.$delivery_status.'</td>
            <td align="right">'.$percent.'%</td>
          </tr>';
       
      }  
      $html .=  '</tbody>
      </table><br><br>';


      $html .=  '<link href="' . FCPATH.'modules/purchase/assets/css/pur_order_pdf.css' . '"  rel="stylesheet" type="text/css" />';
      return $html;
    }

    /**
     * Adds a pur invoice.
     *
     * @param        $data   The data
     */
    public function add_pur_invoice($data){

        unset($data['item_select']);
        unset($data['item_name']);
        unset($data['description']);
        unset($data['total']);
        unset($data['quantity']);
        unset($data['unit_price']);
        unset($data['unit_name']);
        unset($data['item_code']);
        unset($data['unit_id']);
        unset($data['discount']);
        unset($data['into_money']);
        unset($data['tax_rate']);
        unset($data['tax_name']);
        unset($data['discount_money']);
        unset($data['total_money']);
        unset($data['additional_discount']);
        unset($data['tax_value']);

        $order_detail = [];
        if(isset($data['newitems'])){
            $order_detail = $data['newitems'];
            unset($data['newitems']);
        }

        $data['to_currency'] = $data['currency'];

        if(isset($data['add_from'])){
            $data['add_from'] = $data['add_from'];
        }else{
            $data['add_from'] = get_staff_user_id();
            $data['add_from_type'] = 'admin';
        }
        $data['date_add'] = date('Y-m-d');
        $data['payment_status'] = 'unpaid';
        $prefix = get_purchase_option('pur_inv_prefix');

        $this->db->where('invoice_number',$data['invoice_number']);
        $check_exist_number = $this->db->get(db_prefix().'pur_invoices')->row();

        while($check_exist_number) {
          $data['number'] = $data['number'] + 1;
          $data['invoice_number'] =  $prefix.str_pad($data['number'],5,'0',STR_PAD_LEFT);
          $this->db->where('invoice_number',$data['invoice_number']);
          $check_exist_number = $this->db->get(db_prefix().'pur_invoices')->row();
        }

        $data['invoice_date'] = to_sql_date($data['invoice_date']);
        if($data['duedate'] != ''){
           $data['duedate'] = to_sql_date($data['duedate']); 
        }

        $data['transaction_date'] = to_sql_date($data['transaction_date']);

        if(isset($data['order_discount'])){
            $order_discount = $data['order_discount'];
            if($data['add_discount_type'] == 'percent'){
                $data['discount_percent'] = $order_discount;
            }

            unset($data['order_discount']);
        }

        unset($data['add_discount_type']);

        if(isset($data['dc_total'])){
            $data['discount_total'] = $data['dc_total'];
            unset($data['dc_total']);
        }

        if(isset($data['total_mn'])){
            $data['subtotal'] = $data['total_mn'];
            unset($data['total_mn']);
        }

        if(isset($data['grand_total'])){
            $data['total'] = $data['grand_total'];
            unset($data['grand_total']);
        }

        $tags = '';
        if (isset($data['tags'])) {
            $tags = $data['tags'];
            unset($data['tags']);
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        $this->db->insert(db_prefix().'pur_invoices',$data);
        $insert_id = $this->db->insert_id();
        if($insert_id){
            $next_number = $data['number']+1;
            $this->db->where('option_name', 'next_inv_number');
            $this->db->update(db_prefix() . 'purchase_option',['option_val' =>  $next_number,]);

            handle_tags_save($tags, $insert_id, 'pur_invoice');

            if (isset($custom_fields)) {
                handle_custom_fields_post($insert_id, $custom_fields);
            }

            $total = [];
            $total['tax'] = 0;

            $this->db->where('pur_invoice', $insert_id);
            $this->db->delete(db_prefix().'pur_invoice_details');

            if(count($order_detail) > 0){
                foreach($order_detail as $key => $rqd){ 
                    $dt_data = [];
                    $dt_data['pur_invoice'] = $insert_id;
                    $dt_data['item_code'] = $rqd['item_code'];
                    $dt_data['unit_id'] = isset($rqd['unit_id']) ? $rqd['unit_id'] : null;
                    $dt_data['unit_price'] = $rqd['unit_price'];
                    $dt_data['into_money'] = $rqd['into_money'];
                    $dt_data['total'] = $rqd['total'];
                    $dt_data['tax_value'] = $rqd['tax_value'];
                    $dt_data['item_name'] = $rqd['item_name'];
                    $dt_data['description'] = nl2br($rqd['item_description']);
                    $dt_data['total_money'] = $rqd['total_money'];
                    $dt_data['discount_money'] = $rqd['discount_money'];
                    $dt_data['discount_percent'] = $rqd['discount'];

                    $tax_money = 0;
                    $tax_rate_value = 0;
                    $tax_rate = null;
                    $tax_id = null;
                    $tax_name = null;

                    if(isset($rqd['tax_select'])){
                        $tax_rate_data = $this->pur_get_tax_rate($rqd['tax_select']);
                        $tax_rate_value = $tax_rate_data['tax_rate'];
                        $tax_rate = $tax_rate_data['tax_rate_str'];
                        $tax_id = $tax_rate_data['tax_id_str'];
                        $tax_name = $tax_rate_data['tax_name_str'];
                    }

                    $dt_data['tax'] = $tax_id;
                    $dt_data['tax_rate'] = $tax_rate;
                    $dt_data['tax_name'] = $tax_name;

                    $dt_data['quantity'] = ($rqd['quantity'] != ''&& $rqd['quantity'] != null) ? $rqd['quantity'] : 0;

                    $this->db->insert(db_prefix().'pur_invoice_details', $dt_data);


                    
                }
            }


            $_taxes = $this->get_html_tax_pur_invoice($insert_id);
            foreach($_taxes['taxes_val'] as $tax_val){
                $total['tax'] += $tax_val;
            }


            $this->db->where('id',$insert_id);
            $this->db->update(db_prefix().'pur_invoices',$total);

            hooks()->do_action('after_pur_invoice_added', $insert_id);

            return $insert_id;
        }
        return false;
    }

    /**
     * { update pur invoice }
     *
     * @param        $id     The identifier
     * @param        $data   The data
     */
    public function update_pur_invoice($id,$data){
        $data['invoice_date'] = to_sql_date($data['invoice_date']);
        $data['transaction_date'] = to_sql_date($data['transaction_date']);
        
        $affectedRows = 0;

        unset($data['item_select']);
        unset($data['item_name']);
        unset($data['description']);
        unset($data['total']);
        unset($data['quantity']);
        unset($data['unit_price']);
        unset($data['unit_name']);
        unset($data['item_code']);
        unset($data['unit_id']);
        unset($data['discount']);
        unset($data['into_money']);
        unset($data['tax_rate']);
        unset($data['tax_name']);
        unset($data['discount_money']);
        unset($data['total_money']);
        unset($data['additional_discount']);
        unset($data['tax_value']);

        unset($data['isedit']);

        if(isset($data['dc_total'])){
            $data['discount_total'] = $data['dc_total'];
            unset($data['dc_total']);
        }

        $data['to_currency'] = $data['currency'];

        if(isset($data['total_mn'])){
            $data['subtotal'] = $data['total_mn'];
            unset($data['total_mn']);
        }

        if(isset($data['grand_total'])){
            $data['total'] = $data['grand_total'];
            unset($data['grand_total']);
        }

        $new_order = [];
        if(isset($data['newitems'])){
            $new_order = $data['newitems'];
            unset($data['newitems']);
        }

        $update_order = [];
        if(isset($data['items'])) {
            $update_order = $data['items'];
            unset($data['items']);
        }

        $remove_order = [];
        if(isset($data['removed_items'])){
            $remove_order = $data['removed_items'];
            unset($data['removed_items']);
        }

        if($data['duedate'] != ''){
           $data['duedate'] = to_sql_date($data['duedate']); 
        }

        if(isset($data['order_discount'])){
            $order_discount = $data['order_discount'];
            if($data['add_discount_type'] == 'percent'){
                $data['discount_percent'] = $order_discount;
            }

            unset($data['order_discount']);
        }

        unset($data['add_discount_type']);

        if (isset($data['tags'])) {
            if (handle_tags_save($data['tags'], $id, 'pur_invoice')) {
                $affectedRows++;
            }
            unset($data['tags']);
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }

        if(count($new_order) > 0){
            foreach($new_order as $key => $rqd){

                $dt_data = [];
                $dt_data['pur_invoice'] = $id;
                $dt_data['item_code'] = $rqd['item_code'];
                $dt_data['unit_id'] = isset($rqd['unit_id']) ? $rqd['unit_id'] : null;
                $dt_data['unit_price'] = $rqd['unit_price'];
                $dt_data['into_money'] = $rqd['into_money'];
                $dt_data['total'] = $rqd['total'];
                $dt_data['tax_value'] = $rqd['tax_value'];
                $dt_data['item_name'] = $rqd['item_name'];
                $dt_data['total_money'] = $rqd['total_money'];
                $dt_data['discount_money'] = $rqd['discount_money'];
                $dt_data['discount_percent'] = $rqd['discount'];
                $dt_data['description'] = nl2br($rqd['item_description']);

                $tax_money = 0;
                $tax_rate_value = 0;
                $tax_rate = null;
                $tax_id = null;
                $tax_name = null;

                if(isset($rqd['tax_select'])){
                    $tax_rate_data = $this->pur_get_tax_rate($rqd['tax_select']);
                    $tax_rate_value = $tax_rate_data['tax_rate'];
                    $tax_rate = $tax_rate_data['tax_rate_str'];
                    $tax_id = $tax_rate_data['tax_id_str'];
                    $tax_name = $tax_rate_data['tax_name_str'];
                }

                $dt_data['tax'] = $tax_id;
                $dt_data['tax_rate'] = $tax_rate;
                $dt_data['tax_name'] = $tax_name;

                $dt_data['quantity'] = ($rqd['quantity'] != ''&& $rqd['quantity'] != null) ? $rqd['quantity'] : 0;

                $this->db->insert(db_prefix().'pur_invoice_details', $dt_data);
                $new_quote_insert_id = $this->db->insert_id();
                if($new_quote_insert_id){
                    $affectedRows++;
                }
                
            }

        }

        if(count($update_order) > 0){
            foreach($update_order as $_key => $rqd){
                $dt_data = [];
                $dt_data['pur_invoice'] = $id;
                $dt_data['item_code'] = $rqd['item_code'];
                $dt_data['unit_id'] = isset($rqd['unit_id']) ? $rqd['unit_id'] : null;
                $dt_data['unit_price'] = $rqd['unit_price'];
                $dt_data['into_money'] = $rqd['into_money'];
                $dt_data['total'] = $rqd['total'];
                $dt_data['tax_value'] = $rqd['tax_value'];
                $dt_data['item_name'] = $rqd['item_name'];
                $dt_data['total_money'] = $rqd['total_money'];
                $dt_data['discount_money'] = $rqd['discount_money'];
                $dt_data['discount_percent'] = $rqd['discount'];
                $dt_data['description'] = nl2br($rqd['item_description']);

                $tax_money = 0;
                $tax_rate_value = 0;
                $tax_rate = null;
                $tax_id = null;
                $tax_name = null;

                if(isset($rqd['tax_select'])){
                    $tax_rate_data = $this->pur_get_tax_rate($rqd['tax_select']);
                    $tax_rate_value = $tax_rate_data['tax_rate'];
                    $tax_rate = $tax_rate_data['tax_rate_str'];
                    $tax_id = $tax_rate_data['tax_id_str'];
                    $tax_name = $tax_rate_data['tax_name_str'];
                }

                $dt_data['tax'] = $tax_id;
                $dt_data['tax_rate'] = $tax_rate;
                $dt_data['tax_name'] = $tax_name;

                $dt_data['quantity'] = ($rqd['quantity'] != ''&& $rqd['quantity'] != null) ? $rqd['quantity'] : 0;

                $this->db->where('id', $rqd['id']);
                $this->db->update(db_prefix().'pur_invoice_details', $dt_data);
                if($this->db->affected_rows() > 0){
                    $affectedRows++;
                }
            }
        }

        if(count($remove_order) > 0){ 
            foreach($remove_order as $remove_id){
                $this->db->where('id', $remove_id);
                if ($this->db->delete(db_prefix() . 'pur_invoice_details')) {
                    $affectedRows++;
                }
            }
        }

        $this->db->where('id',$id);
        $this->db->update(db_prefix().'pur_invoices',$data);

        $total['tax'] = 0;
        $_taxes = $this->get_html_tax_pur_invoice($id);
        foreach($_taxes['taxes_val'] as $tax_val){
            $total['tax'] += $tax_val;
        }

        $this->db->where('id',$id);
        $this->db->update(db_prefix().'pur_invoices',$total);

        $this->update_pur_invoice_status($id);

        hooks()->do_action('after_pur_invoice_updated', $id);
        if($this->db->affected_rows() > 0){

            

            return true;
        }
        return false;
    }

    /**
     * Gets the pur invoice.
     *
     * @param      string  $id     The identifier
     *
     * @return       The pur invoice.
     */
    public function get_pur_invoice($id = ''){
        if($id != ''){
            $this->db->where('id',$id);
            return $this->db->get(db_prefix().'pur_invoices')->row();
        }else{
            return $this->db->get(db_prefix().'pur_invoices')->result_array();
        }
    }

    /**
     * { delete pur invoice }
     *
     * @param      <type>   $id     The identifier
     *
     * @return     boolean  
     */
    public function delete_pur_invoice($id){
        $this->db->where('rel_type','pur_invoice');
        $this->db->where('rel_id', $id);
        $this->db->delete(db_prefix().'taggables');

        $this->db->where('rel_type','pur_invoice');
        $this->db->where('rel_id', $id);
        $this->db->delete(db_prefix().'files');

        if (is_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_invoice/'. $id)) {
            delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_invoice/'. $id);
        }

        $this->db->where('fieldto','pur_invoice');
        $this->db->where('relid',$id);
        $this->db->delete(db_prefix().'customfieldsvalues');

        $this->db->select('id');
        $this->db->where('id IN (SELECT debit_id FROM ' . db_prefix() . 'pur_debits WHERE invoice_id=' . $this->db->escape_str($id) . ')');
        $linked_debit_notes = $this->db->get(db_prefix() . 'pur_debit_notes')->result_array();

        $this->db->where('invoice_id', $id);
        $this->db->delete(db_prefix() . 'pur_debits');

        foreach ($linked_debit_notes as $debit_note) {
            $this->update_debit_note_status($debit_note['id']);
        }

        $this->db->where('pur_invoice', $id);
        $this->db->delete(db_prefix().'pur_invoice_details');

        $this->db->where('id',$id);
        $this->db->delete(db_prefix().'pur_invoices');
        if($this->db->affected_rows() > 0){
            $payments = $this->get_payment_invoice($id);
            foreach($payments as $payment){
                $this->delete_payment_pur_invoice($payment['id']);
            }

            hooks()->do_action('after_pur_invoice_deleted', $id);

            return true;
        }
        return false;
    }

    /**
     * Gets the payment invoice.
     *
     * @param        $invoice  The invoice
     *
     * @return       The payment invoice.
     */
    public function get_payment_invoice($invoice){
        $this->db->where('pur_invoice',$invoice);
        return $this->db->get(db_prefix().'pur_invoice_payment')->result_array();
    }

    /**
     * Adds a invoice payment.
     *
     * @param         $data       The data
     * @param         $invoice  The invoice id
     *
     * @return     boolean  
     */
    public function add_invoice_payment($data, $invoice){
        $data['date'] = to_sql_date($data['date']);
        $data['daterecorded'] = date('Y-m-d H:i:s');
        
        $data['pur_invoice'] = $invoice;
        $data['approval_status'] = 1;
        $data['requester'] = get_staff_user_id();
        $check_appr = $this->get_approve_setting('payment_request');
        if($check_appr && $check_appr != false){
            $data['approval_status'] = 1;
        }else{
            $data['approval_status'] = 2;
        }

        $this->db->insert(db_prefix().'pur_invoice_payment',$data);
        $insert_id = $this->db->insert_id();
        if($insert_id){

            if($data['approval_status'] == 2){
                $pur_invoice = $this->get_pur_invoice($invoice);
                if($pur_invoice){
                    $status_inv = $pur_invoice->payment_status;
                    if(purinvoice_left_to_pay($invoice) > 0){
                        $status_inv = 'partially_paid';
                        if(purinvoice_left_to_pay($invoice) == $pur_invoice->total){
                            $status_inv = 'unpaid';
                        }
                    }else{
                        $status_inv = 'paid';
                    }
                    $this->db->where('id',$invoice);
                    $this->db->update(db_prefix().'pur_invoices', [ 'payment_status' => $status_inv, ]);
                }
            }

            hooks()->do_action('after_payment_pur_invoice_added', $insert_id);

            return $insert_id;
        }
        return false;
    }

    /**
     * { delete invoice payment }
     *
     * @param      <type>   $id     The identifier
     *
     * @return     boolean  ( delete payment )
     */
    public function delete_payment_pur_invoice($id){
        $payment = $this->get_payment_pur_invoice($id);

        $this->db->where('id',$id);
        $this->db->delete(db_prefix().'pur_invoice_payment');
        if ($this->db->affected_rows() > 0) {
            $pur_invoice = $this->get_pur_invoice($payment->pur_invoice);

            if($pur_invoice){
                $status_inv = $pur_invoice->payment_status;
                if(purinvoice_left_to_pay($payment->pur_invoice) > 0){
                    $status_inv = 'partially_paid';
                    if(purinvoice_left_to_pay($payment->pur_invoice) == $pur_invoice->total){
                        $status_inv = 'unpaid';
                    }
                }else{
                    $status_inv = 'paid';
                }

                $this->db->where('id',$payment->pur_invoice);
                $this->db->update(db_prefix().'pur_invoices', [ 'payment_status' => $status_inv ]);
            }

            if (is_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/payment_invoice/signature/'. $id)) {
                delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/payment_invoice/signature/'. $id);
            }

            hooks()->do_action('after_payment_pur_invoice_deleted', $id);

            return true;
        }
        return false;
    }

    /**
     * Gets the payment pur invoice.
     *
     * @param      string  $id     The identifier
     */
    public function get_payment_pur_invoice($id = ''){
        if($id != ''){
            $this->db->where('id',$id);
            return $this->db->get(db_prefix().'pur_invoice_payment')->row();
        }else{
            return $this->db->get(db_prefix().'pur_invoice_payment')->result_array();
        }
    }

    /**
     * { update invoice after approve }
     *
     * @param        $id     The identifier
     */
    public function update_invoice_after_approve($id){
        $payment = $this->get_payment_pur_invoice($id);

        if($payment){
            $pur_invoice = $this->get_pur_invoice($payment->pur_invoice);
            if($pur_invoice){
                $status_inv = $pur_invoice->payment_status;
                if(purinvoice_left_to_pay($payment->pur_invoice) > 0){
                    $status_inv = 'partially_paid';
                    if(purinvoice_left_to_pay($payment->pur_invoice) == $pur_invoice->total){
                        $status_inv = 'unpaid';
                    }
                }else{
                    $status_inv = 'paid';
                }
                $this->db->where('id',$payment->pur_invoice);
                $this->db->update(db_prefix().'pur_invoices', [ 'payment_status' => $status_inv, ]);
            }
        }
    }

     /**
     * Gets the purchase order attachments.
     *
     * @param      <type>  $id     The purchase order
     *
     * @return     <type>  The purchase order attachments.
     */
    public function get_purchase_invoice_attachments($id){
   
        $this->db->where('rel_id',$id);
        $this->db->where('rel_type','pur_invoice');
        return $this->db->get(db_prefix().'files')->result_array();
    }

    /**
     * Gets the inv attachments.
     *
     * @param      <type>  $surope  The surope
     * @param      string  $id      The identifier
     *
     * @return     <type>  The part attachments.
     */
    public function get_purinv_attachments($surope, $id = '')
    {
        // If is passed id get return only 1 attachment
        if (is_numeric($id)) {
            $this->db->where('id', $id);
        } else {
            $this->db->where('rel_id', $assets);
        }
        $this->db->where('rel_type', 'pur_invoice');
        $result = $this->db->get(db_prefix().'files');
        if (is_numeric($id)) {
            return $result->row();
        }

        return $result->result_array();
    }

    /**
     * { delete purchase invoice attachment }
     *
     * @param         $id     The identifier
     *
     * @return     boolean 
     */
    public function delete_purinv_attachment($id)
    {
        $attachment = $this->get_purinv_attachments('', $id);
        $deleted    = false;
        if ($attachment) {
            if (empty($attachment->external)) {
                unlink(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_invoice/'. $attachment->rel_id . '/' . $attachment->file_name);
            }
            $this->db->where('id', $attachment->id);
            $this->db->delete('tblfiles');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
            }

            if (is_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_invoice/'. $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_invoice/'. $attachment->rel_id);
                if (count($other_attachments) == 0) {
                    // okey only index.html so we can delete the folder also
                    delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/pur_invoice/'. $attachment->rel_id);
                }
            }
        }

        return $deleted;
    }

    /**
     * Gets the payment by contract.
     *
     * @param        $id     The identifier
     */
    public function get_payment_by_contract($id){
        return $this->db->query('select * from '.db_prefix().'pur_invoice_payment where pur_invoice IN ( select id from '.db_prefix().'pur_invoices where contract = '.$id.' )')->result_array();
    }

    /**
     * { purestimate pdf }
     *
     * @param        $pur_request  The pur request
     *
     * @return       ( purorder pdf )
     */
    public function purestimate_pdf($pur_estimate,$id)
    {
        return app_pdf('pur_estimate', module_dir_path(PURCHASE_MODULE_NAME, 'libraries/pdf/Pur_estimate_pdf'), $pur_estimate,$id);
    }


    /**
     * Gets the pur request pdf html.
     *
     * @param      <type>  $pur_request_id  The pur request identifier
     *
     * @return     string  The pur request pdf html.
     */
    public function get_purestimate_pdf_html($pur_estimate_id){
        

        $pur_estimate = $this->get_estimate($pur_estimate_id);
        $pur_estimate_detail = $this->get_pur_estimate_detail($pur_estimate_id);
        $company_name = get_option('invoice_company_name'); 
        
        $base_currency = get_base_currency_pur();
        if($pur_estimate->currency != 0){
            $base_currency = pur_get_currency_by_id($pur_estimate->currency);
        }

        $address = get_option('invoice_company_address'); 
        $day = date('d',strtotime($pur_estimate->date));
        $month = date('m',strtotime($pur_estimate->date));
        $year = date('Y',strtotime($pur_estimate->date));
        $tax_data = $this->get_html_tax_pur_estimate($pur_estimate_id);
        
    $html = '<table class="table">
        <tbody>
          <tr>
            <td class="font_td_cpn" style="width: 70%">'. _l('purchase_company_name').': '. $company_name.'</td>
            <td rowspan="2" style="width: 30%" class="text-right">'.get_po_logo(get_option('pdf_logo_width')).'</td>
            
          </tr>
          <tr>
            <td class="font_500">'. _l('address').': '. $address.'</td>
            <td></td>
            
          </tr>
        </tbody>
      </table>
      <table class="table">
        <tbody>
          <tr>
            
            <td class="td_ali_font"><h2 class="h2_style">'.mb_strtoupper(_l('estimate')).'</h2></td>
           
          </tr>
          <tr>
            
            <td class="align_cen">'. _l('days').' '.$day.' '._l('month').' '.$month.' '._l('year') .' '.$year.'</td>
            
          </tr>
          
        </tbody>
      </table>
      <table class="table">
        <tbody>
          <tr>
            <td class="td_width_25"><h4>'. _l('add_from').':</h4></td>
            <td class="td_width_75">'. get_staff_full_name($pur_estimate->addedfrom).'</td>
          </tr>
          <tr>
            <td class="td_width_25"><h4>'. _l('vendor').':</h4></td>
            <td class="td_width_75">'. get_vendor_company_name($pur_estimate->vendor->userid).'</td>
          </tr>
          
        </tbody>
      </table>

      <h3>
       '. pur_html_entity_decode(format_pur_estimate_number($pur_estimate_id)).'
       </h3>
      <br><br>
      ';

      $html .=  '<table class="table purorder-item">
        <thead>
          <tr>
            <th class="thead-dark">'._l('items').'</th>
            <th class="thead-dark" align="right">'._l('purchase_unit_price').'</th>
            <th class="thead-dark" align="right">'._l('purchase_quantity').'</th>';
         
            if(get_option('show_purchase_tax_column') == 1){    
                $html .= '<th class="thead-dark" align="right">'._l('tax').'</th>';
            }
 
            $html .= '<th class="thead-dark" align="right">'._l('discount').'</th>
            <th class="thead-dark" align="right">'._l('total').'</th>
          </tr>
          </thead>
          <tbody>';
        $t_mn = 0;
      foreach($pur_estimate_detail as $row){
        $items = $this->get_items_by_id($row['item_code']);
        $units = $this->get_units_by_id($row['unit_id']);
        $item_name = isset($items->commodity_code) ? $items->commodity_code.' - '.$items->description : $row['item_name'];

        $html .= '<tr nobr="true" class="sortable">
            <td >'.$item_name.'</td>
            <td align="right">'.app_format_money($row['unit_price'],$base_currency->symbol).'</td>
            <td align="right">'.$row['quantity'].'</td>';
         
            if(get_option('show_purchase_tax_column') == 1){  
                $html .= '<td align="right">'.app_format_money($row['total'] - $row['into_money'],$base_currency->symbol).'</td>';
            }
       
            $html .= '<td align="right">'.app_format_money($row['discount_money'],$base_currency->symbol).'</td>
            <td align="right">'.app_format_money($row['total_money'],$base_currency->symbol).'</td>
          </tr>';

        $t_mn += $row['total_money'];
      }  
      $html .=  '</tbody>
      </table><br><br>';

      $html .= '<table class="table text-right"><tbody>';
      $html .= '<tr id="subtotal">
                    <td style="width: 33%"></td>
                     <td>'._l('subtotal').' </td>
                     <td class="subtotal">
                        '.app_format_money($pur_estimate->subtotal,$base_currency->symbol).'
                     </td>
                  </tr>';
      $html .= $tax_data['pdf_html'];
      if($pur_estimate->discount_total > 0){
        $html .= '<tr id="subtotal">
                  <td style="width: 33%"></td>
                     <td>'._l('discount(money)').'</td>
                     <td class="subtotal">
                        '.app_format_money($pur_estimate->discount_total, $base_currency->symbol).'
                     </td>
                  </tr>';
      }
      if($pur_estimate->shipping_fee > 0){
        $html .= '<tr id="subtotal">
                  <td style="width: 33%"></td>
                     <td>'._l('pur_shipping_fee').'</td>
                     <td class="subtotal">
                        '.app_format_money($pur_estimate->shipping_fee, $base_currency->symbol).'
                     </td>
                  </tr>';
      }
      $html .= '<tr id="subtotal">
                 <td style="width: 33%"></td>
                 <td>'. _l('total').'</td>
                 <td class="subtotal">
                    '. app_format_money($pur_estimate->total, $base_currency->symbol).'
                 </td>
              </tr>';

      $html .= ' </tbody></table>';

      $html .= '<div class="col-md-12 mtop15">
                        <h4>'. _l('terms_and_conditions').': </h4><p>'. pur_html_entity_decode($pur_estimate->terms).'</p>
                       
                     </div>';
      $html .= '<br>
      <br>
      <br>
      <br>';
      $html .=  '<link href="' . FCPATH.'modules/purchase/assets/css/pur_order_pdf.css' . '"  rel="stylesheet" type="text/css" />';
      return $html;
    }

    /**
     * Sends a quotation.
     *
     * @param         $data   The data
     *
     * @return     boolean
     */
    public function send_quotation($data){
        $mail_data = [];
        $count_sent = 0;

        if(isset($data['attach_pdf'])){
            $pur_order = $this->get_purestimate_pdf_html($data['pur_estimate_id']);

            try {
                $pdf = $this->purestimate_pdf($pur_order, $data['pur_estimate_id']);
            } catch (Exception $e) {
                echo pur_html_entity_decode($e->getMessage());
                die;
            }

            $attach = $pdf->Output(format_pur_estimate_number($data['pur_estimate_id']) . '.pdf', 'S');
        }


        if(strlen(get_option('smtp_host')) > 0 && strlen(get_option('smtp_password')) > 0 ){
            foreach($data['send_to'] as $mail){

                $mail_data['pur_estimate_id'] = $data['pur_estimate_id'];
                $mail_data['content'] = $data['content'];
                $mail_data['mail_to'] = $mail;

                $template = mail_template('purchase_quotation_to_contact', 'purchase', array_to_object($mail_data));

                if(isset($data['attach_pdf'])){
                    $template->add_attachment([
                        'attachment' => $attach,
                        'filename'   => str_replace('/', '-', format_pur_estimate_number($data['pur_estimate_id']) . '.pdf'),
                        'type'       => 'application/pdf',
                    ]);
                }

                $rs = $template->send();

                if($rs){
                    $count_sent++;
                }
            }

            if($count_sent > 0){
                return true;
            }  
        }

        return false;
    }


    /**
     * Sends a purchase order.
     *
     * @param         $data   The data
     *
     * @return     boolean
     */
    public function send_po($data){
        $mail_data = [];
        $count_sent = 0;
        $po = $this->get_pur_order($data['po_id']);
        if(isset($data['attach_pdf'])){
            $pur_order = $this->get_purorder_pdf_html($data['po_id']);

            try {
                $pdf = $this->purorder_pdf($pur_order);
            } catch (Exception $e) {
                echo pur_html_entity_decode($e->getMessage());
                die;
            }

            $attach = $pdf->Output($po->pur_order_number . '.pdf', 'S');
        }


        if(strlen(get_option('smtp_host')) > 0 && strlen(get_option('smtp_password')) > 0 ){
            foreach($data['send_to'] as $mail){

                $mail_data['po_id'] = $data['po_id'];
                $mail_data['content'] = $data['content'];
                $mail_data['mail_to'] = $mail;

                $template = mail_template('purchase_order_to_contact', 'purchase', array_to_object($mail_data));

                if(isset($data['attach_pdf'])){
                    $template->add_attachment([
                        'attachment' => $attach,
                        'filename'   => str_replace('/', '-', $po->pur_order_number . '.pdf'),
                        'type'       => 'application/pdf',
                    ]);
                }

                $rs = $template->send();

                if($rs){
                    $count_sent++;
                }
            }

            if($count_sent > 0){
                return true;
            }  
        }

        return false;
    }

    /**
     * import xlsx commodity
     * @param  array $data
     * @return integer
     */
    public function import_xlsx_commodity($data) {

        //update column unit name use sales/items
        if(isset($data['unit_id'])){
            $unit_type = get_unit_type_item($data['unit_id']);
            if(isset($unit_type->unit_name)){
                $data['unit'] = $unit_type->unit_name;
            }
        }

        if($data['commodity_barcode'] != ''){
            $data['commodity_barcode'] = $data['commodity_barcode'];
        }else{
            $data['commodity_barcode'] = $this->generate_commodity_barcode();
        }
        
        
        /*create sku code*/
        if($data['sku_code'] != ''){
            $data['sku_code'] = str_replace(' ', '', $data['sku_code']) ;
        }else{
            //data sku_code = group_character.sub_code.commodity_str_betwen.next_commodity_id; // X_X_000.id auto increment
            $data['sku_code'] = $this->create_sku_code($data['group_id'], isset($data['sub_group']) ? $data['sub_group'] : '' );
            /*create sku code*/
        }

        if(get_option('barcode_with_sku_code') == 1){
            $data['commodity_barcode'] = $data['sku_code'];
        }
        
        /*check update*/

        $item = $this->db->query("select * from tblitems where commodity_code = '".$data['commodity_code']."'")->row();

        if($item){
            //check sku code dulicate
            if($this->check_sku_duplicate(['sku_code' => $data['sku_code'], 'item_id' => $item->id]) == false){
                return false;
            }

            if(isset($data['tags'])){
                $tags_value =  $data['tags'];
                unset($data['tags']);
            }else{
                $tags_value ='';
            }

            foreach ($data as $key => $data_value) {
                if(!isset($data_value)){
                    unset($data[$key]);
                }
            }

            $minimum_inventory = 0;
            if(isset($data['minimum_inventory'])){
                $minimum_inventory = $data['minimum_inventory'];
                 unset($data['minimum_inventory']);
            }

            //update
            $this->db->where('commodity_code', $data['commodity_code']);
            $this->db->update(db_prefix() . 'items', $data);

            if ($this->db->affected_rows() > 0) {
                return true;
            }
        }else{
            //check sku code dulicate
            if($this->check_sku_duplicate(['sku_code' => $data['sku_code'], 'item_id' => '']) == false){
                return false;
            }

            $sku_prefix = '';

         
            $sku_prefix = get_option('item_sku_prefix');
            

            $data['sku_code'] = $sku_prefix.$data['sku_code'];

            //insert
            $this->db->insert(db_prefix() . 'items', $data);
            $insert_id = $this->db->insert_id();

            return $insert_id;
        }
    }

    /**
     * check sku duplicate
     * @param  [type] $data 
     * @return [type]       
     */
    public function check_sku_duplicate($data)
    {   
        if(isset($data['item_id'])){
        //check update
            $this->db->where('sku_code', $data['sku_code']);
            $this->db->where('id != ', $data['item_id']);

            $items = $this->db->get(db_prefix() . 'items')->result_array();

            if(count($items) > 0){
                return false;
            }
            return true;

        }elseif(isset($data['sku_code'])){
        //check insert
            $this->db->where('sku_code', $data['sku_code']);
            $items = $this->db->get(db_prefix() . 'items')->row();
            if($items){
                return false;
            }
            return true;
        }

        return true;

    }

    /**
     * Removes a po logo.
     *
     * @return     boolean  
     */
    public function remove_po_logo(){

        $this->db->where('rel_id', 0);
        $this->db->where('rel_type', 'po_logo');
        $avar = $this->db->get(db_prefix() . 'files')->row();

        if ($avar) {
            if (empty($avar->external)) {
                unlink(PURCHASE_MODULE_UPLOAD_FOLDER . '/po_logo/' . $avar->rel_id . '/' . $avar->file_name);
            }
            $this->db->where('id', $avar->id);
            $this->db->delete('tblfiles');

            if (is_dir(PURCHASE_MODULE_UPLOAD_FOLDER . '/po_logo/' . $avar->rel_id)) {
                // Check if no avars left, so we can delete the folder also
                $other_avars = list_files(PURCHASE_MODULE_UPLOAD_FOLDER . '/po_logo/' . $avar->rel_id);
                if (count($other_avars) == 0) {
                    // okey only index.html so we can delete the folder also
                    delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER . '/po_logo/' . $avar->rel_id);
                }
            }
        }

        return true;
    }

    /**
     * { change delivery status }
     *
     * @param        $status  The status
     * @param        $id      The identifier
     * @return     boolean
     */
    public function change_delivery_status($status, $id){
        $this->db->where('id', $id);
        $this->db->update(db_prefix().'pur_orders', [ 'delivery_status' => $status]);
        if ($this->db->affected_rows() > 0) {
            if($status == 1){
                $this->db->where('id', $id);
                $this->db->update(db_prefix().'pur_orders', [ 'order_status' => 'delivered', 'delivery_date' => date('Y-m-d')]);
            }else{
                $this->db->where('id', $id);
                $this->db->update(db_prefix().'pur_orders', [ 'order_status' => 'confirmed']);
            }

            return true;
        }
        return false;
    }

    /**
     * { convert po payment }
     *
     * @param        $pur_order  The pur order
     */
    public function convert_po_payment($pur_order){
        $p_order_payment = $this->get_payment_purchase_order($pur_order);
        $po = $this->get_pur_order($pur_order);
        $po_payment_value = 0;
        if(count($p_order_payment) > 0){
            foreach($p_order_payment as $payment){
                $po_payment_value += $payment['amount'];
            }
        }

        if($po_payment_value > 0){
            $this->db->where('pur_order',$pur_order);
            $invs = $this->db->get(db_prefix().'pur_invoices')->result_array();
            if(count($invs) > 0){
                foreach($invs as $key => $inv){
                    if($inv['total'] >= $po_payment_value){
                        if(total_rows(db_prefix() . 'pur_invoice_payment', ['pur_invoice' => $inv['id']]) == 0){
                            $data_payment['amount'] = $po_payment_value;
                            $data_payment['date'] = date('Y-m-d');
                            $data_payment['paymentmode'] = '';
                            $data_payment['transactionid'] = '';
                            $data_payment['note'] = '';
                            $success = $this->add_invoice_payment($data_payment, $inv['id']);
                            if($success){
                                return true;
                            }
                        }
                        break;
                    }
                }
            }else{
                $prefix = get_purchase_option('pur_inv_prefix');
                $next_number = get_purchase_option('next_inv_number');
                $data_inv['number'] = $next_number;
                $data_inv['invoice_number'] = $prefix.str_pad($next_number,5,'0',STR_PAD_LEFT);
                $data_inv['invoice_date'] = date('Y-m-d');
                $data_inv['pur_order'] = $pur_order;
                $data_inv['subtotal'] = $po->total;
                $data_inv['tax_rate'] = '';
                $data_inv['tax'] = '';
                $data_inv['total'] = $po->total;
                $data_inv['adminnote'] = '';
                $data_inv['tags'] = '';
                $data_inv['transactionid'] = '';
                $data_inv['transaction_date'] = '';
                $data_inv['vendor_note'] = '';
                $data_inv['terms'] = '';
                $new_inv = $this->add_pur_invoice($data_inv);
                if($new_inv){
                    $data_payment['amount'] = $po_payment_value;
                    $data_payment['date'] = date('Y-m-d');
                    $data_payment['paymentmode'] = '';
                    $data_payment['transactionid'] = '';
                    $data_payment['note'] = '';
                    $success = $this->add_invoice_payment($data_payment, $new_inv);
                    if($success){
                        return true;
                    }
                }
                return false;
            }
        }

        return false;
    }

    /**
     * Gets the inv payment purchase order.
     *
     * @param        $pur_order  The pur order
     */
    public function get_inv_payment_purchase_order($pur_order){
        $this->db->where('pur_order', $pur_order);
        $list_inv = $this->db->get(db_prefix().'pur_invoices')->result_array();
        $data_rs = [];
        foreach($list_inv as $inv){
            $this->db->where('pur_invoice', $inv['id']);
            $inv_payments = $this->db->get(db_prefix().'pur_invoice_payment')->result_array();
            foreach($inv_payments as $payment){
                $data_rs[] = $payment;
            }
        }

        return $data_rs; 
    }

    /**
     * Gets the inv payment purchase order.
     *
     * @param        $pur_order  The pur order
     */
    public function get_inv_debit_purchase_order($pur_order){
        $this->db->where('pur_order', $pur_order);
        $list_inv = $this->db->get(db_prefix().'pur_invoices')->result_array();
        $data_rs = [];
        foreach($list_inv as $inv){
            $this->db->where('invoice_id', $inv['id']);
            $inv_debits = $this->db->get(db_prefix().'pur_debits')->result_array();
            foreach($inv_debits as $debit){
                $data_rs[] = $debit;
            }
        }

        return $data_rs; 
    }

    /**
     * get pur order approved for inv
     *
     * @return       The pur order approved.
     */
    public function get_pur_order_approved_for_inv(){
        $this->db->where('approve_status', 2);
        if(!has_permission('purchase_orders', '', 'view') && is_staff_logged_in()){
            $this->db->where(' (' . db_prefix() . 'pur_orders.addedfrom = '.get_staff_user_id().' OR ' . db_prefix() . 'pur_orders.buyer = '.get_staff_user_id().' OR ' . db_prefix() . 'pur_orders.vendor IN (SELECT vendor_id FROM ' . db_prefix() . 'pur_vendor_admin WHERE staff_id=' . get_staff_user_id() . '))');
        }
        $list_po = $this->db->get(db_prefix().'pur_orders')->result_array();
        $data_rs = [];
        if(count($list_po) > 0){
            foreach($list_po as $po){
                $this->db->where('pur_order', $po['id']);
                $list_inv = $this->db->get(db_prefix().'pur_invoices')->result_array();
                $total_inv_value = 0;
                foreach($list_inv as $inv){
                    $total_inv_value += $inv['total'];
                }

                if($total_inv_value < $po['total']){
                    $data_rs[] = $po;
                }
            }    
        }
        
        return $data_rs;
    }

    /**
     * get pur order approved for inv
     *
     * @return       The pur order approved.
     */
    public function get_pur_order_approved_for_inv_by_vendor($vendor){
        if(!has_permission('purchase_orders', '', 'view') && is_staff_logged_in()){
            $this->db->where(' (' . db_prefix() . 'pur_orders.addedfrom = '.get_staff_user_id().' OR ' . db_prefix() . 'pur_orders.buyer = '.get_staff_user_id().' OR ' . db_prefix() . 'pur_orders.vendor IN (SELECT vendor_id FROM ' . db_prefix() . 'pur_vendor_admin WHERE staff_id=' . get_staff_user_id() . '))');
        }
        
        $this->db->where('approve_status', 2);
        $this->db->where('vendor', $vendor);
        
        $list_po = $this->db->get(db_prefix().'pur_orders')->result_array();
        $data_rs = [];
        if(count($list_po) > 0){
            foreach($list_po as $po){
                $this->db->where('pur_order', $po['id']);
                $list_inv = $this->db->get(db_prefix().'pur_invoices')->result_array();
                $total_inv_value = 0;
                foreach($list_inv as $inv){
                    $total_inv_value += $inv['total'];
                }

                if($total_inv_value < $po['total']){
                    $data_rs[] = $po;
                }
            }    
        }
        
        return $data_rs;
    }

    /**
     * Gets the list pur orders.
     *
     * @return       The list pur orders.
     */
    public function get_list_pur_orders(){
        if(!has_permission('purchase_orders', '', 'view') && is_staff_logged_in()){
            $this->db->where(' (' . db_prefix() . 'pur_orders.addedfrom = '.get_staff_user_id().' OR ' . db_prefix() . 'pur_orders.buyer = '.get_staff_user_id().' OR ' . db_prefix() . 'pur_orders.vendor IN (SELECT vendor_id FROM ' . db_prefix() . 'pur_vendor_admin WHERE staff_id=' . get_staff_user_id() . '))');
        }
        return $this->db->get(db_prefix().'pur_orders')->result_array();
    }

    /**
     * Get  comments
     * @param  mixed $id  id
     * @return array
     */
    public function get_comments($id, $type)
    {
        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', $type);
        $this->db->order_by('dateadded', 'ASC');

        return $this->db->get(db_prefix() . 'pur_comments')->result_array();
    }

    /**
    * Add contract comment
    * @param mixed  $data   $_POST comment data
    * @param boolean $client is request coming from the client side
    */
    public function add_comment($data, $vendor = false)
    {
        if (is_staff_logged_in()) {
            $vendor = false;
        }

        if (isset($data['action'])) {
            unset($data['action']);
        }

        $data['dateadded'] = date('Y-m-d H:i:s');

        if ($vendor == false) {
            $data['staffid'] = get_staff_user_id();
        }else{
            $data['staffid'] = 0;
        }

        $data['content'] = nl2br($data['content']);
        $this->db->insert(db_prefix() . 'pur_comments', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {

            return true;
        }

        return false;
    }

    /**
     * { edit comment }
     *
     * @param         $data   The data
     * @param         $id     The identifier
     *
     * @return     boolean  
     */
    public function edit_comment($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'pur_comments', [
            'content' => nl2br($data['content']),
        ]);

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Remove comment
     * @param  mixed $id comment id
     * @return boolean
     */
    public function remove_comment($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'pur_comments');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * Gets the invoices by vendor.
     */
    public function get_invoices_by_vendor($vendor){
        $data_rs = [];
        $invs = $this->get_pur_invoice();
        if(count($invs) > 0){
            foreach($invs as $inv){
                if($inv['vendor'] != ''){
                    if($inv['vendor'] == $vendor){
                        $data_rs[] = $inv;
                    }
                }else{
                    if( $inv['pur_order'] != null && is_numeric($inv['pur_order'])){
                        $pur_order = $this->get_pur_order($inv['pur_order']);
                        if(isset($pur_order->vendor)){
                            if($pur_order->vendor == $vendor){
                                $data_rs[] = $inv;
                            }
                        }
                    }

                    if($inv['contract'] != null && is_numeric($inv['contract'])){
                        $contract = $this->get_contract($inv['contract']);
                        if(isset($contract->vendor)){
                            if($contract->vendor == $vendor){
                                $data_rs[] = $inv;
                            }
                        }
                    }
                }
            }
        }

        return $data_rs;
    }

    /**
     * Gets the html tax pur request.
     */
    public function get_html_tax_pur_request($id){
        $html = '';
        $preview_html = '';
        $pdf_html = '';
        $taxes = [];
        $t_rate = [];
        $tax_val = [];
        $tax_val_rs = [];
        $tax_name = [];
        $rs = [];

        $request = $this->get_purchase_request($id);

        $this->load->model('currencies_model');
        $base_currency = $this->currencies_model->get_base_currency();
        if($request->currency != 0 && $request->currency != null){
            $base_currency = pur_get_currency_by_id($request->currency);
        }

        $this->db->where('pur_request', $id);
        $details = $this->db->get(db_prefix().'pur_request_detail')->result_array();
        foreach($details as $row){
            if($row['tax'] != ''){
                $tax_arr = explode('|', $row['tax']);

                $tax_rate_arr = [];
                if($row['tax_rate'] != ''){
                    $tax_rate_arr = explode('|', $row['tax_rate']);
                }

                foreach($tax_arr as $k => $tax_it){
                    if(!isset($tax_rate_arr[$k]) ){
                        $tax_rate_arr[$k] = $this->tax_rate_by_id($tax_it);
                    }

                    if(!in_array($tax_it, $taxes)){
                        $taxes[$tax_it] = $tax_it;
                        $t_rate[$tax_it] = $tax_rate_arr[$k];
                        $tax_name[$tax_it] = $this->get_tax_name($tax_it).' ('.$tax_rate_arr[$k].'%)';
                    }
                }
            }
        }

        if(count($tax_name) > 0){
            foreach($tax_name as $key => $tn){
                $tax_val[$key] = 0;
                foreach($details as $row_dt){
                    if(!(strpos($row_dt['tax'] ?? '', $taxes[$key]) === false)){
                        $tax_val[$key] += ($row_dt['into_money']*$t_rate[$key]/100);
                    }
                }
                $pdf_html .= '<tr id="subtotal"><td width="33%"></td><td>'.$tn.'</td><td>'.app_format_money($tax_val[$key], $base_currency->symbol).'</td></tr>';
                $preview_html .= '<tr id="subtotal"><td>'.$tn.'</td><td>'.app_format_money($tax_val[$key], $base_currency->symbol).'</td><tr>';
                $html .= '<tr class="tax-area_pr"><td>'.$tn.'</td><td width="65%">'.app_format_money($tax_val[$key], '').' '.($base_currency->symbol).'</td></tr>';
                $tax_val_rs[] = $tax_val[$key];
            }
        }
        
        $rs['pdf_html'] = $pdf_html;
        $rs['preview_html'] = $preview_html;
        $rs['html'] = $html;
        $rs['taxes'] = $taxes;
        $rs['taxes_val'] = $tax_val_rs;
        return $rs;
    }

    /**
     * Gets the tax name.
     *
     * @param        $tax    The tax
     *
     * @return     string  The tax name.
     */
    public function get_tax_name($tax){
        $this->db->where('id', $tax);
        $tax_if = $this->db->get(db_prefix().'taxes')->row();
        if($tax_if){
            return $tax_if->name;
        }
        return '';
    }

    /**
     * Gets the invoice for pr.
     */
    public function get_invoice_for_pr(){
        $this->db->where('status != 6');
        $this->db->where('status != 5');
        $this->db->order_by('number', 'desc');
        return $this->db->get(db_prefix().'invoices')->result_array();
    }

    /**
     * Gets the tax of inv item.
     *
     * @param        $itemid   The itemid
     * @param        $invoice  The invoice
     *
     * @return       The tax of inv item.
     */
    public function get_tax_of_inv_item($itemid, $invoice){
        $this->db->where('itemid', $itemid);
        $this->db->where('rel_type', 'invoice');
        $this->db->where('rel_id', $invoice);
        return $this->db->get(db_prefix().'item_tax')->row();
    }

    /**
     * Gets the tax of inv item.
     *
     * @param        $itemid   The itemid
     * @param        $invoice  The invoice
     *
     * @return       The tax of inv item.
     */
    public function get_taxex_of_inv_item($itemid, $invoice){
        $this->db->where('itemid', $itemid);
        $this->db->where('rel_type', 'invoice');
        $this->db->where('rel_id', $invoice);
        return $this->db->get(db_prefix().'item_tax')->result_array();
    }

    /**
     * Gets the tax by tax name.
     *
     * @param        $taxname  The taxname
     */
    public function get_tax_by_tax_name($taxname){
        $this->db->where('name', $taxname);
        $tax = $this->db->get(db_prefix().'taxes')->row();
        if($tax){
            return $tax->id;
        }
        return '';
    }

    /**
     * Gets the inv by client for po.
     *
     * @param        $client  The client
     */
    public function get_inv_by_client_for_po($client){
        $this->db->where('status != 6');
        $this->db->where('status != 5');
        $this->db->where('clientid', $client);
        $this->db->order_by('number', 'desc');
        return $this->db->get(db_prefix().'invoices')->result_array();
    }

    /**
     * Creates an item by inv item.
     */
    public function create_item_by_inv_item($itemable_id){
        $this->db->where('id', $itemable_id);
        $inv_item = $this->db->get(db_prefix().'itemable')->row();

        $item_id = '';
        if($inv_item){
            $item_data['description'] = $inv_item->description;
            $item_data['long_description'] = $inv_item->long_description;
            $item_data['purchase_price'] = '';
            $item_data['rate'] = $inv_item->rate;
            $item_data['sku_code'] = '';
            $item_data['commodity_barcode'] = $this->generate_commodity_barcode();
            $item_data['commodity_code'] = $this->generate_commodity_barcode();
            $item_data['unit_id'] = '';
            $item_id = $this->add_commodity_one_item($item_data);
        }

        return $item_id;
    }

    /**
     * Gets the html tax pur order.
     */
    public function get_html_tax_pur_order($id){
        $html = '';
        $preview_html = '';
        $pdf_html = '';
        $taxes = [];
        $t_rate = [];
        $tax_val = [];
        $tax_val_rs = [];
        $tax_name = [];
        $rs = [];

        $order = $this->get_pur_order($id);

        $this->load->model('currencies_model');
        $base_currency = $this->currencies_model->get_base_currency();

        if($order->currency != 0 && $order->currency != null){
            $base_currency = pur_get_currency_by_id($order->currency);
        }


        $this->db->where('pur_order', $id);
        $details = $this->db->get(db_prefix().'pur_order_detail')->result_array();
        $item_discount = 0;

        foreach($details as $row){
            if($row['tax'] != ''){
                $tax_arr = explode('|', $row['tax']);

                $tax_rate_arr = [];
                if($row['tax_rate'] != ''){
                    $tax_rate_arr = explode('|', $row['tax_rate']);
                }

                foreach($tax_arr as $k => $tax_it){
                    if(!isset($tax_rate_arr[$k]) ){
                        $tax_rate_arr[$k] = $this->tax_rate_by_id($tax_it);
                    }

                    if(!in_array($tax_it, $taxes)){
                        $taxes[$tax_it] = $tax_it;
                        $t_rate[$tax_it] = $tax_rate_arr[$k];
                        $tax_name[$tax_it] = $this->get_tax_name($tax_it).' ('.$tax_rate_arr[$k].'%)';
                    }
                }
            }

            $item_discount += $row['discount_money'];
        }

        if(count($tax_name) > 0){
            $discount_total = $item_discount + $order->discount_total;

            foreach($tax_name as $key => $tn){
                $tax_val[$key] = 0;
                foreach($details as $row_dt){
                    if(!(strpos($row_dt['tax'] ?? '', $taxes[$key]) === false)){
                        $total = ($row_dt['into_money']*$t_rate[$key]/100);

                        if($order->discount_type == 'before_tax'){
                            $t = 0;
                            if($order->subtotal > 0){
                                $t     = ($discount_total / $order->subtotal) * 100;
                            }
                            $tax_val[$key] += ($total - $total * $t / 100);
                        }else{
                           $tax_val[$key] += $total;
                        }
                    }
                }

     

                $pdf_html .= '<tr id="subtotal"><td width="33%"></td><td>'.$tn.'</td><td>'.app_format_money($tax_val[$key], '').'</td></tr>';
                $preview_html .= '<tr id="subtotal"><td>'.$tn.'</td><td>'.app_format_money($tax_val[$key], $base_currency->name).'</td><tr>';
                $html .= '<tr class="tax-area_pr"><td>'.$tn.'</td><td width="65%">'.app_format_money($tax_val[$key], '').' '.($base_currency->name).'</td></tr>';
                $tax_val_rs[] = $tax_val[$key];
            }
        }
        
        $rs['pdf_html'] = $pdf_html;
        $rs['preview_html'] = $preview_html;
        $rs['html'] = $html;
        $rs['taxes'] = $taxes;
        $rs['taxes_val'] = $tax_val_rs;
        return $rs;
    }

    /**
     * Gets the html tax pur order.
     */
    public function get_html_tax_pur_invoice($id){
        $html = '';
        $preview_html = '';
        $pdf_html = '';
        $taxes = [];
        $t_rate = [];
        $tax_val = [];
        $tax_val_rs = [];
        $tax_name = [];
        $rs = [];

        $invoice = $this->get_pur_invoice($id);

        $this->load->model('currencies_model');
        $base_currency = $this->currencies_model->get_base_currency();

        if($invoice->currency != 0 && $invoice->currency != null){
            $base_currency = pur_get_currency_by_id($invoice->currency);
        }


        $this->db->where('pur_invoice', $id);
        $details = $this->db->get(db_prefix().'pur_invoice_details')->result_array();

        $item_discount = 0;
        foreach($details as $row){
            if($row['tax'] != ''){
                $tax_arr = explode('|', $row['tax']);

                $tax_rate_arr = [];
                if($row['tax_rate'] != ''){
                    $tax_rate_arr = explode('|', $row['tax_rate']);
                }

                foreach($tax_arr as $k => $tax_it){
                    if(!isset($tax_rate_arr[$k]) ){
                        $tax_rate_arr[$k] = $this->tax_rate_by_id($tax_it);
                    }

                    if(!in_array($tax_it, $taxes)){
                        $taxes[$tax_it] = $tax_it;
                        $t_rate[$tax_it] = $tax_rate_arr[$k];
                        $tax_name[$tax_it] = $this->get_tax_name($tax_it).' ('.$tax_rate_arr[$k].'%)';
                    }
                }
            }

            $item_discount += $row['discount_money'];
        }

        if(count($tax_name) > 0){
            $discount_total = $item_discount + $invoice->discount_total;
            foreach($tax_name as $key => $tn){
                $tax_val[$key] = 0;
                foreach($details as $row_dt){
                    if(!(strpos($row_dt['tax'] ?? '', $taxes[$key]) === false)){
                        $total = ($row_dt['into_money']*$t_rate[$key]/100);
                        if($invoice->discount_type == 'before_tax'){
                            $t     = ($discount_total / $invoice->subtotal) * 100;
                            $tax_val[$key] += ($total - $total * $t / 100);
                        }else{
                           $tax_val[$key] += $total;
                        }
                    }
                }
                $pdf_html .= '<tr id="subtotal"><td width="33%"></td><td>'.$tn.'</td><td>'.app_format_money($tax_val[$key], '').'</td></tr>';
                $preview_html .= '<tr id="subtotal"><td>'.$tn.'</td><td>'.app_format_money($tax_val[$key], $base_currency->name).'</td><tr>';
                $html .= '<tr class="tax-area_pr"><td>'.$tn.'</td><td width="65%">'.app_format_money($tax_val[$key], '').' '.($base_currency->name).'</td></tr>';
                $tax_val_rs[] = $tax_val[$key];
            }
        }
        
        $rs['pdf_html'] = $pdf_html;
        $rs['preview_html'] = $preview_html;
        $rs['html'] = $html;
        $rs['taxes'] = $taxes;
        $rs['taxes_val'] = $tax_val_rs;
        return $rs;
    }

    /**
     * Gets the html tax pur estimate.
     */
    public function get_html_tax_pur_estimate($id){
        $html = '';
        $preview_html = '';
        $pdf_html = '';
        $taxes = [];
        $t_rate = [];
        $tax_val = [];
        $tax_val_rs = [];
        $tax_name = [];
        $rs = [];

        $estimate = $this->get_estimate($id);

        $this->load->model('currencies_model');
        $base_currency = $this->currencies_model->get_base_currency();

        if($estimate->currency != 0 && $estimate->currency != null){
            $base_currency = pur_get_currency_by_id($estimate->currency);
        }

        $this->db->where('pur_estimate', $id);
        $details = $this->db->get(db_prefix().'pur_estimate_detail')->result_array();

        $item_discount = 0;
        foreach($details as $row){
            if($row['tax'] != ''){
                $tax_arr = explode('|', $row['tax']);

                $tax_rate_arr = [];
                if($row['tax_rate'] != ''){
                    $tax_rate_arr = explode('|', $row['tax_rate']);
                }

                foreach($tax_arr as $k => $tax_it){
                    if(!isset($tax_rate_arr[$k]) ){
                        $tax_rate_arr[$k] = $this->tax_rate_by_id($tax_it);
                    }

                    if(!in_array($tax_it, $taxes)){
                        $taxes[$tax_it] = $tax_it;
                        $t_rate[$tax_it] = $tax_rate_arr[$k];
                        $tax_name[$tax_it] = $this->get_tax_name($tax_it).' ('.$tax_rate_arr[$k].'%)';
                    }
                }
            }

            $item_discount += $row['discount_money'];
        }

        if(count($tax_name) > 0){
            $discount_total =  $estimate->discount_total;
            foreach($tax_name as $key => $tn){
                $tax_val[$key] = 0;
                foreach($details as $row_dt){
                    if(!(strpos($row_dt['tax'] ?? '', $taxes[$key]) === false)){
                        $total = ($row_dt['into_money']*$t_rate[$key]/100);
                        if($estimate->discount_type == 'before_tax'){
                            $t     = ($discount_total / $estimate->subtotal) * 100;
                            $tax_val[$key] += ($total - $total * $t / 100);
                        }else{
                           $tax_val[$key] += $total;
                        }
                    }
                }
                $pdf_html .= '<tr id="subtotal"><td width="33%"></td><td>'.$tn.'</td><td>'.app_format_money($tax_val[$key], $base_currency->symbol).'</td></tr>';
                $preview_html .= '<tr id="subtotal"><td>'.$tn.'</td><td>'.app_format_money($tax_val[$key], $base_currency->symbol).'</td><tr>';
                $html .= '<tr class="tax-area_pr"><td>'.$tn.'</td><td width="65%">'.app_format_money($tax_val[$key], '').' '.($base_currency->symbol).'</td></tr>';
                $tax_val_rs[] = $tax_val[$key];
            }
        }
        
        $rs['pdf_html'] = $pdf_html;
        $rs['preview_html'] = $preview_html;
        $rs['html'] = $html;
        $rs['taxes'] = $taxes;
        $rs['taxes_val'] = $tax_val_rs;
        return $rs;
    }

    /**
     * { tax rate by id }
     *
     * @param        $tax_id  The tax identifier
     */
    public function tax_rate_by_id($tax_id){
        $this->db->where('id', $tax_id);
        $tax = $this->db->get(db_prefix().'taxes')->row();
        if($tax){
            return $tax->taxrate;
        }
        return 0;
    }

    /**
     * Gets the payment invoices by vendor.
     */
    public function get_payment_invoices_by_vendor($vendor){
        $invoices = $this->get_invoices_by_vendor($vendor);
        $data_rs = array();
        if(count($invoices)  > 0){
            foreach($invoices as $inv){
                $payments = $this->get_payment_invoice($inv['id']);
                if(count($invoices)  > 0){
                    foreach($payments as $pm){
                        $data_rs[] = $pm; 
                    }
                }
            }
        }

        return $data_rs;
    }

   

    /**
     * commodity udpate profit rate
     * @param  [type] $id      
     * @param  [type] $percent 
     * @param  [type] $type    
     * @return [type]          
     */
    public function commodity_udpate_profit_rate($id, $percent, $type)
    {   
        if(get_status_modules_pur('warehouse') == true){
            //warehouse active
            $the_fractional_part = get_option('warehouse_the_fractional_part');
            $integer_part = get_option('warehouse_integer_part');

            $affected_rows=0;
            $item = $this->get_item($id);
            $profit_rate=0;

            $this->load->model('warehouse/warehouse_model');

            if($item){
                $selling_price = (float)$item->rate;
                $purchase_price = (float)$item->purchase_price;

                if($type == 'selling_percent'){
                //selling_percent
                    $new_selling_price = $selling_price + $selling_price*(float)$percent/100;

                    if($integer_part != '0'){
                        $integer_part = 0 - (int)($integer_part);
                        $new_selling_price = round($new_selling_price, $integer_part);
                    }

                    $profit_rate = $this->warehouse_model->caculator_profit_rate_model($purchase_price, $new_selling_price);

                    $this->db->where('id', $id);
                    $this->db->update(db_prefix().'items', ['rate' => $new_selling_price, 'profif_ratio' => $profit_rate]);
                    if ($this->db->affected_rows() > 0) {
                        $affected_rows++;
                    }

                }else{
                //purchase_percent
                    $new_purchase_price = $purchase_price + $purchase_price*(float)$percent/100;

                    if($integer_part != '0'){
                        $integer_part = 0 - (int)($integer_part);
                        $new_purchase_price = round($new_purchase_price, $integer_part);
                    }

                    $profit_rate = $this->warehouse_model->caculator_profit_rate_model($new_purchase_price, $selling_price);

                    $this->db->where('id', $id);
                    $this->db->update(db_prefix().'items', ['purchase_price' => $new_purchase_price, 'profif_ratio' => $profit_rate]);
                    if ($this->db->affected_rows() > 0) {
                        $affected_rows++;
                    }

                }

            }
        }else{


            $affected_rows=0;
            $item = $this->get_item($id);
            $profit_rate=0;

            if($item){
                $selling_price = (float)$item->rate;
                $purchase_price = (float)$item->purchase_price;

                if($type == 'selling_percent'){
                //selling_percent
                    $new_selling_price = $selling_price + $selling_price*(float)$percent/100;

                    $this->db->where('id', $id);
                    $this->db->update(db_prefix().'items', ['rate' => $new_selling_price]);
                    if ($this->db->affected_rows() > 0) {
                        $affected_rows++;
                    }

                }else{
                //purchase_percent
                    $new_purchase_price = $purchase_price + $purchase_price*(float)$percent/100;

                    $this->db->where('id', $id);
                    $this->db->update(db_prefix().'items', ['purchase_price' => $new_purchase_price]);
                    if ($this->db->affected_rows() > 0) {
                        $affected_rows++;
                    }

                }

            }
        }

        if($affected_rows > 0){
            return true;
        }
        return false;

    }

    /**
     * Sends a purchase order.
     *
     * @param         $data   The data
     *
     * @return     boolean
     */
    public function send_pr($data){
        $mail_data = [];
        $count_sent = 0;
        $po = $this->get_purchase_request($data['pur_request_id']);
        if(isset($data['attach_pdf'])){
            $pur_order = $this->get_pur_request_pdf_html($data['pur_request_id']);

            try {
                $pdf = $this->pur_request_pdf($pur_order);
            } catch (Exception $e) {
                echo pur_html_entity_decode($e->getMessage());
                die;
            }

            $attach = $pdf->Output($po->pur_rq_code . '.pdf', 'S');
        }


        if(strlen(get_option('smtp_host')) > 0 && strlen(get_option('smtp_password')) > 0 ){
            foreach($data['send_to'] as $mail){

                $mail_data['pur_request_id'] = $data['pur_request_id'];
                $mail_data['content'] = $data['content'];
                $mail_data['mail_to'] = $mail;

                $template = mail_template('purchase_request_to_contact', 'purchase', array_to_object($mail_data));

                if(isset($data['attach_pdf'])){
                    $template->add_attachment([
                        'attachment' => $attach,
                        'filename'   => str_replace('/', '-', $po->pur_rq_code . '.pdf'),
                        'type'       => 'application/pdf',
                    ]);
                }

                $rs = $template->send();

                if($rs){
                    $count_sent++;
                }
            }

            if($count_sent > 0){
                return true;
            }  
        }

        return false;
    }

    /**
     * { clone_item }
     */
    public function clone_item($id){
        $current_items = $this->get_item($id);
        $item_attachments = $this->get_item_attachments($id);


        if($current_items){
            $item_data['description'] = $current_items->description;
            $item_data['purchase_price'] = $current_items->purchase_price;
            $item_data['unit_id'] = $current_items->unit_id;
            $item_data['rate'] = $current_items->rate;
            $item_data['sku_code'] = '';
            $item_data['commodity_barcode'] = $this->generate_commodity_barcode();
            $item_data['commodity_code'] = $this->generate_commodity_barcode();
            if(get_status_modules_wh('warehouse')){ 
                $item_data['group_id'] = $current_items->group_id;
                $item_data['sub_group'] = $current_items->sub_group;
                $item_data['tax'] = $current_items->tax;
                $item_data['commodity_type'] = $current_items->commodity_type;
                $item_data['warehouse_id'] = $current_items->warehouse_id;
                $item_data['profif_ratio'] = $current_items->profif_ratio;
                $item_data['origin'] = $current_items->origin;
                $item_data['style_id'] = $current_items->style_id;
                $item_data['model_id'] = $current_items->model_id;
                $item_data['size_id'] = $current_items->size_id;
                $item_data['color'] = $current_items->color;
                $item_data['guarantee'] = $current_items->guarantee;
                $item_data['without_checking_warehouse'] = $current_items->without_checking_warehouse;
                $item_data['long_description'] = $current_items->long_description;
            }
            $item_id = $this->add_commodity_one_item($item_data);
            if($item_id){
                if(count($item_attachments) > 0){
                    $source = PURCHASE_MODULE_UPLOAD_FOLDER.'/item_img/'.$id;
                    if(!is_dir($source)){
                        if(get_status_modules_wh('warehouse')){
                            $source = WAREHOUSE_MODULE_UPLOAD_FOLDER.'/item_img/'.$id;
                        }
                    }
                    $destination = PURCHASE_MODULE_UPLOAD_FOLDER.'/item_img/'.$item_id;
                    if(xcopy($source, $destination)){
                        foreach($item_attachments as $attachment){
                        
                        
                            $attachment_db   = [];
                            $attachment_db[] = [
                                'file_name' => $attachment['file_name'],
                                'filetype'  => $attachment['filetype'],
                                ];

                            $this->misc_model->add_attachment_to_database($item_id, 'commodity_item_file', $attachment_db);
                        }
                    }
                }
                
                if(isset($current_items->from_vendor_item) && is_numeric($current_items->from_vendor_item)){
                    $vendor_image = $this->purchase_model->get_vendor_item_file($current_items->from_vendor_item);
                    if(count($vendor_image) > 0){
                        $source = PURCHASE_MODULE_UPLOAD_FOLDER.'/vendor_items/'.$current_items->from_vendor_item;

                        $destination = PURCHASE_MODULE_UPLOAD_FOLDER.'/item_img/'.$item_id;

                        if(xcopy($source, $destination)){
                            foreach($vendor_image as $attachment){
                                $attachment_db   = [];
                                $attachment_db[] = [
                                    'file_name' => $attachment['file_name'],
                                    'filetype'  => $attachment['filetype'],
                                    ];

                                $this->misc_model->add_attachment_to_database($item_id, 'commodity_item_file', $attachment_db);
                            }
                        }
                    }
                }
                

                if(get_status_modules_wh('warehouse')){ 
                    $this->db->where('relid', $current_items->id);
                    $this->db->where('fieldto', 'items_pr');
                    $customfields = $this->db->get(db_prefix().'customfieldsvalues')->result_array();
                    if(count($customfields) > 0){
                        foreach($customfields as $cf){
                            $this->db->insert(db_prefix().'customfieldsvalues', [
                                'relid' => $item_id,
                                'fieldid' => $cf['fieldid'],
                                'fieldto' => $cf['fieldto'],
                                'value' => $cf['value']
                            ]);
                        }
                    }

                    $this->db->where('rel_id', $current_items->id);
                    $this->db->where('rel_type', 'item_tags');
                    $tags = $this->db->get(db_prefix().'taggables')->result_array();
                    if(count($tags) > 0){
                        foreach($tags as $tag){
                            $this->db->insert(db_prefix().'taggables', [
                                'rel_id' => $item_id,
                                'rel_type' => $tag['rel_type'],
                                'tag_id' => $tag['tag_id'],
                                'tag_order' => $tag['tag_order']
                            ]);

                        }
                    }
                }

                return true;
            }
        }

        return false;
    }

    /**
     * { recurring purchase invoice }
     *
     * 
     */
    public function recurring_purchase_invoice(){
        $invoice_hour_auto_operations = get_option('pur_invoice_auto_operations_hour');

        if (!$this->shouldRunAutomations($invoice_hour_auto_operations)) {
            return;
        }

        $this->db->select('id,recurring,invoice_date,last_recurring_date,number,duedate,recurring_type,add_from, contract');
        $this->db->from(db_prefix() . 'pur_invoices');
        $this->db->where('recurring !=', 0);
        $this->db->where('(cycles != total_cycles OR cycles=0)');
        $invoices = $this->db->get()->result_array();
        $total_renewed      = 0;
        foreach ($invoices as $invoice) {
            $contract_inv = $this->get_contract($invoice['contract']);

            if(isset($contract_inv) && !is_array($contract_inv) && ($contract_inv->end_date >= date('Y-m-d') || $contract_inv->end_date == '' || $contract_inv->end_date == null)){
                // Current date
                $date = new DateTime(date('Y-m-d'));
                // Check if is first recurring
                if (!$invoice['last_recurring_date'] || $invoice['last_recurring_date'] == '' || $invoice['last_recurring_date'] == null) {
                    $last_recurring_date = date('Y-m-d', strtotime($invoice['invoice_date']));
                } else {
                    $last_recurring_date = date('Y-m-d', strtotime($invoice['last_recurring_date']));
                }
               
                $invoice['recurring_type'] = 'MONTH';
                

                $re_create_at = date('Y-m-d', strtotime('+' . $invoice['recurring'] . ' ' . strtoupper($invoice['recurring_type']), strtotime($last_recurring_date)));

                if (date('Y-m-d') >= $re_create_at) {

                    // Recurring invoice date is okey lets convert it to new invoice
                    $_invoice                     = $this->get_pur_invoice($invoice['id']);
                    $new_invoice_data             = [];
                    $prefix = get_purchase_option('pur_inv_prefix');
                    $new_invoice_data['number']   = get_purchase_option('next_inv_number');
                    $new_invoice_data['invoice_number']   = $prefix.str_pad($new_invoice_data['number'],5,'0',STR_PAD_LEFT);

                    $new_invoice_data['invoice_date']     = _d($re_create_at);
                    $new_invoice_data['duedate']  = null;
                    $new_invoice_data['contract']  = $_invoice->contract;
                    $new_invoice_data['vendor']  = $_invoice->vendor;
                    $new_invoice_data['transactionid']  = $_invoice->transactionid;
                    $new_invoice_data['transaction_date']  = $_invoice->transaction_date;

                    if ($_invoice->duedate && $_invoice->duedate != '' && $_invoice->duedate != null) {
                        // Now we need to get duedate from the old invoice and calculate the time difference and set new duedate
                        // Ex. if the first invoice had duedate 20 days from now we will add the same duedate date but starting from now
                        $dStart                      = new DateTime($invoice['invoice_date']);
                        $dEnd                        = new DateTime($invoice['duedate']);
                        $dDiff                       = $dStart->diff($dEnd);
                        $new_invoice_data['duedate'] = _d(date('Y-m-d', strtotime('+' . $dDiff->days . ' DAY', strtotime($re_create_at))));
                    } 


                    $new_invoice_data['subtotal']         = $_invoice->subtotal;
                    $new_invoice_data['total']            = $_invoice->total;
                    $new_invoice_data['tax']         = $_invoice->tax;
                    $new_invoice_data['tax_rate']         = $_invoice->tax_rate;
                    $new_invoice_data['discount_total']         = $_invoice->discount_total;
                    $new_invoice_data['discount_percent']         = $_invoice->discount_percent;

                    $new_invoice_data['terms']            = clear_textarea_breaks($_invoice->terms);

                    // Determine status based on settings
                    $new_invoice_data['payment_status'] = 'unpaid';
                    $new_invoice_data['vendor_note']            = clear_textarea_breaks($_invoice->vendor_note);
                    $new_invoice_data['adminnote']             = clear_textarea_breaks($_invoice->adminnote);
                    $new_invoice_data['is_recurring_from']     = $_invoice->id;
                    $new_invoice_data['date_add']     = $re_create_at;
                    $new_invoice_data['add_from']     = $_invoice->add_from;
                    $new_invoice_data['currency']     = $_invoice->currency;
                
                    $id = $this->add_pur_invoice($new_invoice_data);
                    if ($id) {
                        $inv_details = $this->get_pur_invoice_detail($invoice['id']);
                        if(count($inv_details)){
                            foreach($inv_details as $inv_detail){
                                $inv_detail_data = [];
                                $inv_detail_data['pur_invoice'] = $id;
                                $inv_detail_data['item_code'] = $inv_detail['item_code'];
                                $inv_detail_data['description'] = $inv_detail['description'];
                                $inv_detail_data['unit_id'] = $inv_detail['unit_id'];
                                $inv_detail_data['unit_price'] = $inv_detail['unit_price'];
                                $inv_detail_data['quantity'] = $inv_detail['quantity'];
                                $inv_detail_data['into_money'] = $inv_detail['into_money'];
                                $inv_detail_data['tax'] = $inv_detail['tax'];
                                $inv_detail_data['total'] = $inv_detail['total'];
                                $inv_detail_data['discount_percent'] = $inv_detail['discount_percent'];
                                $inv_detail_data['discount_money'] = $inv_detail['discount_money'];
                                $inv_detail_data['total_money'] = $inv_detail['total_money'];
                                $inv_detail_data['tax_value'] = $inv_detail['tax_value'];
                                $inv_detail_data['tax_rate'] = $inv_detail['tax_rate'];
                                $inv_detail_data['tax_name'] = $inv_detail['tax_name'];
                                $inv_detail_data['item_name'] = $inv_detail['item_name'];

                                $this->db->insert(db_prefix().'pur_invoice_details', $inv_detail_data);
                            }
                        }

                        $tags = get_tags_in($_invoice->id, 'pur_invoice');
                        handle_tags_save($tags, $id, 'pur_invoice');

                        // Increment total renewed invoices
                        $total_renewed++;
                        // Update last recurring date to this invoice
                        $this->db->where('id', $invoice['id']);
                        $this->db->update(db_prefix() . 'pur_invoices', [
                            'last_recurring_date' => to_sql_date($re_create_at),
                        ]);

                        $this->db->where('id', $invoice['id']);
                        $this->db->set('total_cycles', 'total_cycles+1', false);
                        $this->db->update(db_prefix() . 'pur_invoices');
                    }
                }
            }
        }
    }

    /**
     * { shouldRunAutomations }
     *
     * @param      int|string  $auto_operation_hour  The automatic operation hour
     *
     * @return     bool        
     */
    private function shouldRunAutomations($auto_operation_hour)
    {
        if ($auto_operation_hour == '') {
            $auto_operation_hour = 9;
        }

        $auto_operation_hour = intval($auto_operation_hour);
        $hour_now            = date('G');

        if ($hour_now != $auto_operation_hour) {
            return false;
        }

        return true;
    }

    /**
     * { update compare quote }
     *
     * @param        $pur_request  The pur request
     * @param        $data         The data
     */
    public function update_compare_quote($pur_request, $data){
        if(!$pur_request){
            return false;
        }

        $affected_rows = 0;
        $this->db->where('id',$pur_request );
        $this->db->update(db_prefix().'pur_request', ['compare_note' => $data['compare_note']]);
        if($this->db->affected_rows() > 0){
            $affected_rows++;
        }

        if(count($data['mark_a_contract']) > 0){
            foreach($data['mark_a_contract'] as $key => $mark){
                $this->db->where('id', $key);
                $this->db->update(db_prefix().'pur_estimates', ['make_a_contract' => $mark]);
                if($this->db->affected_rows() > 0){
                    $affected_rows++;
                }
            }
        }

        if($affected_rows > 0){
            return true;
        }
        return false;
    }

    /**
     *  Get vendor billing details
     * @param   mixed $id   vendor id
     * @return  array
     */
    public function get_vendor_billing_and_shipping_details($id)
    {
        $this->db->select('billing_street,billing_city,billing_state,billing_zip,billing_country,shipping_street,shipping_city,shipping_state,shipping_zip,shipping_country');
        $this->db->from(db_prefix() . 'pur_vendor');
        $this->db->where('userid', $id);

        $result = $this->db->get()->result_array();
        if (count($result) > 0) {
            $result[0]['billing_street']  = clear_textarea_breaks($result[0]['billing_street']);
            $result[0]['shipping_street'] = clear_textarea_breaks($result[0]['shipping_street']);
        }

        return $result;
    }

    /**
     * Adds a debit note.
     *
     * @param        $data   The data
     */
    public function add_debit_note($data){
        $save_and_send = isset($data['save_and_send']);

        $data['prefix']        = get_option('debit_note_prefix');
        $data['number_format'] = get_option('debit_note_number_format');
        $data['datecreated']   = date('Y-m-d H:i:s');
        $data['addedfrom']     = get_staff_user_id();
        $data['date'] = to_sql_date($data['date']);

        $data['status'] = 1;

        $items = [];
        if (isset($data['newitems'])) {
            $items = $data['newitems'];
            unset($data['newitems']);
        }

        $data = $this->map_shipping_columns_debit_note($data);

        if(isset($data['description'])){
            unset($data['description']);
        }

        if(isset($data['item_select'])){
            unset($data['item_select']);
        }

        if(isset($data['long_description'])){
            unset($data['long_description']);
        }

        if(isset($data['quantity'])){
            unset($data['quantity']);
        }

        if(isset($data['unit'])){
            unset($data['unit']);
        }

        if(isset($data['rate'])){
            unset($data['rate']);
        }

        if(isset($data['taxname'])){
            unset($data['taxname']);
        }

        $this->db->insert(db_prefix() . 'pur_debit_notes', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {

            // Update next credit note number in settings
            $this->db->where('name', 'next_debit_note_number');
            $this->db->set('value', 'value+1', false);
            $this->db->update(db_prefix() . 'options');

            foreach ($items as $key => $item) {
                if ($itemid = add_new_sales_item_post($item, $insert_id, 'debit_note')) {
                    _maybe_insert_post_item_tax($itemid, $item, $insert_id, 'debit_note');
                }
            }

            update_sales_total_tax_column($insert_id, 'debit_note', db_prefix() . 'pur_debit_notes');


            return $insert_id;
        }

        return false;
    }

    /**
     * { function_description }
     *
     * @param      <type>  $data   The data
     *
     * @return     <array> data
     */
    private function map_shipping_columns_debit_note($data)
    {
        if (!isset($data['include_shipping'])) {
            foreach ($this->shipping_fields as $_s_field) {
                if (isset($data[$_s_field])) {
                    $data[$_s_field] = null;
                }
            }
            $data['show_shipping_on_debit_note'] = 1;
            $data['include_shipping']          = 0;
        } else {
            $data['include_shipping'] = 1;
            // set by default for the next time to be checked
            if (isset($data['show_shipping_on_debit_note']) && ($data['show_shipping_on_debit_note'] == 1 || $data['show_shipping_on_debit_note'] == 'on')) {
                $data['show_shipping_on_debit_note'] = 1;
            } else {
                $data['show_shipping_on_debit_note'] = 0;
            }
        }

        return $data;
    }

    /**
     * Get credit note/s
     * @param  mixed $id    credit note id
     * @param  array  $where perform where
     * @return mixed
     */
    public function get_debit_note($id = '', $where = [])
    {
        $this->db->select('*,' . db_prefix() . 'currencies.id as currencyid, ' . db_prefix() . 'pur_debit_notes.id as id, ' . db_prefix() . 'currencies.name as currency_name');
        $this->db->from(db_prefix() . 'pur_debit_notes');
        $this->db->join(db_prefix() . 'currencies', '' . db_prefix() . 'currencies.id = ' . db_prefix() . 'pur_debit_notes.currency', 'left');
        $this->db->where($where);

        if (is_numeric($id)) {
            $this->db->where(db_prefix() . 'pur_debit_notes.id', $id);
            $debit_note = $this->db->get()->row();
            if ($debit_note) {
                $debit_note->refunds       = $this->get_refunds($id);
                $debit_note->total_refunds = $this->total_refunds_by_debit_note($id);

                $debit_note->applied_debits   = $this->get_applied_debits($id);
                $debit_note->remaining_debits = $this->total_remaining_debits_by_debit_note($id);
                $debit_note->debit_used      = $this->total_debits_used_by_debit_note($id);

                $debit_note->items  = get_items_by_type('debit_note', $id);
                $debit_note->vendor = $this->get_vendor($debit_note->vendorid);

                if (!$debit_note->vendor) {
                    $debit_note->vendor          = new stdClass();
                    $debit_note->vendor->company = $debit_note->deleted_vendor_name;
                }
                $debit_note->attachments = $this->get_attachments($id);
            }

            return $debit_note;
        }

        $this->db->order_by('number,YEAR(date)', 'desc');

        return $this->db->get()->result_array();
    }

    /**
     * Gets the refunds.
     *
     * @param        $debit_note_id  The debit note identifier
     *
     * @return       The refunds.
     */
    public function get_refunds($debit_note_id)
    {
        $this->db->select(prefixed_table_fields_array(db_prefix() . 'pur_debits_refunds', true) . ',' . db_prefix() . 'payment_modes.id as payment_mode_id, ' . db_prefix() . 'payment_modes.name as payment_mode_name');
        $this->db->where('debit_note_id', $debit_note_id);

        $this->db->join(db_prefix() . 'payment_modes', db_prefix() . 'payment_modes.id = ' . db_prefix() . 'pur_debits_refunds.payment_mode', 'left');

        $this->db->order_by('refunded_on', 'desc');

        $refunds = $this->db->get(db_prefix() . 'pur_debits_refunds')->result_array();

        $this->load->model('payment_modes_model');
        $payment_gateways = $this->payment_modes_model->get_payment_gateways(true);
        $i                = 0;

        foreach ($refunds as $refund) {
            if (is_null($refund['payment_mode_id'])) {
                foreach ($payment_gateways as $gateway) {
                    if ($refund['payment_mode'] == $gateway['id']) {
                        $refunds[$i]['payment_mode_id']   = $gateway['id'];
                        $refunds[$i]['payment_mode_name'] = $gateway['name'];
                    }
                }
            }
            $i++;
        }

        return $refunds;
    }

    /**
     * { total refunds by debit note }
     *
     * @param        $id     The identifier
     *
     * @return       total
     */
    private function total_refunds_by_debit_note($id)
    {
        return sum_from_table(db_prefix() . 'pur_debits_refunds', [
                'field' => 'amount',
                'where' => ['debit_note_id' => $id],
            ]);
    }

    /**
     * Gets the applied debits.
     *
     * @param        $debit_id  The debit identifier
     *
     * @return       The applied debits.
     */
    public function get_applied_debits($debit_id)
    {
        $this->db->where('debit_id', $debit_id);
        $this->db->order_by('date', 'desc');

        return $this->db->get(db_prefix() . 'pur_debits')->result_array();
    }

    /**
     * { total remaining credits by credit note }
     *
     * @param        $credit_note_id  The credit note identifier
     *
     * @return       remaining
     */
    public function total_remaining_debits_by_debit_note($debit_note_id)
    {
        $this->db->select('total,id');
        $this->db->where('id', $debit_note_id);
        $debits = $this->db->get(db_prefix() . 'pur_debit_notes')->result_array();

        $total = $this->calc_remaining_debits($debits);

        return $total;
    }

    /**
     * Calculates the remaining debits.
     *
     * @param       $debits  The debits
     *
     * @return     int     The remaining debits.
     */
    private function calc_remaining_debits($debits)
    {
        $total       = 0;
        $credits_ids = [];

        $bcadd = function_exists('bcadd');
        foreach ($debits as $debit) {
            if ($bcadd) {
                $total = bcadd($total, $debit['total'], get_decimal_places());
            } else {
                $total += $debit['total'];
            }
            array_push($credits_ids, $debit['id']);
        }

        if (count($credits_ids) > 0) {
            $this->db->where('debit_id IN (' . implode(', ', $credits_ids) . ')');
            $applied_credits = $this->db->get(db_prefix() . 'pur_debits')->result_array();
            $bcsub           = function_exists('bcsub');
            foreach ($applied_credits as $debit) {
                if ($bcsub) {
                    $total = bcsub($total, $debit['amount'], get_decimal_places());
                } else {
                    $total -= $debit['amount'];
                }
            }

            foreach ($credits_ids as $credit_note_id) {
                $total_refunds_by_debit_note = $this->total_refunds_by_debit_note($credit_note_id);
                if ($bcsub) {
                    $total = bcsub($total, $total_refunds_by_debit_note ?? '', get_decimal_places());
                } else {
                    $total -= $total_refunds_by_debit_note;
                }
            }
        }

        return $total;
    }

    /**
     * { total debits used by debit note }
     *
     * @param        $id     The identifier
     *
     * @return      total 
     */
    private function total_debits_used_by_debit_note($id)
    {
        return sum_from_table(db_prefix() . 'pur_debits', [
                'field' => 'amount',
                'where' => ['debit_id' => $id],
            ]);
    }

    public function get_debit_note_statuses()
    {
        return [
            [
                'id'             => 1,
                'color'          => '#03a9f4',
                'name'           => _l('credit_note_status_open'),
                'order'          => 1,
                'filter_default' => true,
                ],
             [
                'id'             => 2,
                'color'          => '#84c529',
                'name'           => _l('credit_note_status_closed'),
                'order'          => 2,
                'filter_default' => true,
             ],
             [
                'id'             => 3,
                'color'          => '#777',
                'name'           => _l('credit_note_status_void'),
                'order'          => 3,
                'filter_default' => false,
             ],
        ];
    }

    /**
     * Gets the attachments.
     *
     * @param        $credit_note_id  The credit note identifier
     *
     * @return       The attachments.
     */
    public function get_attachments($credit_note_id)
    {
        $this->db->where('rel_id', $credit_note_id);
        $this->db->where('rel_type', 'debit_note');

        return $this->db->get(db_prefix() . 'files')->result_array();
    }


    public function get_available_debitable_invoices($debit_note_id)
    {
        $has_permission_view = has_permission('purchase_debit_notes', '', 'view');

       
        $this->db->select('vendorid');
        $this->db->where('id', $debit_note_id);
        $debit_note = $this->db->get(db_prefix() . 'pur_debit_notes')->row();

        $this->db->select('' . db_prefix() . 'pur_invoices.id as id, invoice_number, payment_status, total, invoice_date, '.db_prefix().'pur_invoices.currency as invoice_currency');
        $this->db->where('vendor', $debit_note->vendorid);
        $this->db->where('payment_status IN ("unpaid", "partially_paid")');
        $invoices = $this->db->get(db_prefix() . 'pur_invoices')->result_array();

        foreach ($invoices as $key => $invoice) {
            $invoices[$key]['total_left_to_pay'] = purinvoice_left_to_pay($invoice['id']);
            $invoices[$key]['currency_name'] = get_base_currency_pur()->name;
        }

        return $invoices;
    }

    /**
     * Gets the credits years.
     *
     * @return       The credits years.
     */
    public function get_debits_years()
    {
        return $this->db->query('SELECT DISTINCT(YEAR(date)) as year FROM ' . db_prefix() . 'pur_debit_notes ORDER BY year DESC')->result_array();
    }

    /**
     * Update debit note
     * @param  mixed $data $_POST data
     * @param  mixed $id   id
     * @return boolean
     */
    public function update_debit_note($data, $id)
    {
        $affectedRows  = 0;
        $save_and_send = isset($data['save_and_send']);

        $data['date'] = to_sql_date($data['date']);

        $items = [];
        if (isset($data['items'])) {
            $items = $data['items'];
            unset($data['items']);
        }

        $newitems = [];
        if (isset($data['newitems'])) {
            $newitems = $data['newitems'];
            unset($data['newitems']);
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }

        if(isset($data['item_select'])){
            unset($data['item_select']);
        }

        if(isset($data['description'])){
            unset($data['description']);
        }

        if(isset($data['long_description'])){
            unset($data['long_description']);
        }

        if(isset($data['quantity'])){
            unset($data['quantity']);
        }

        if(isset($data['unit'])){
            unset($data['unit']);
        }

        if(isset($data['rate'])){
            unset($data['rate']);
        }

        if(isset($data['taxname'])){
            unset($data['taxname']);
        }

        if(isset($data['isedit'])){
            unset($data['isedit']);
        }

        $data = $this->map_shipping_columns_debit_note($data);

        $hook = hooks()->apply_filters('before_update_debit_note', [
            'data'          => $data,
            'items'         => $items,
            'newitems'      => $newitems,
            'removed_items' => isset($data['removed_items']) ? $data['removed_items'] : [],
        ], $id);

        $data                  = $hook['data'];
        $items                 = $hook['items'];
        $newitems              = $hook['newitems'];
        $data['removed_items'] = $hook['removed_items'];

        // Delete items checked to be removed from database
        foreach ($data['removed_items'] as $remove_item_id) {
            if (handle_removed_sales_item_post($remove_item_id, 'debit_note')) {
                $affectedRows++;
            }
        }
        unset($data['removed_items']);

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'pur_debit_notes', $data);

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        foreach ($items as $key => $item) {
            if (update_sales_item_post($item['itemid'], $item)) {
                $affectedRows++;
            }

            if (!isset($item['taxname']) || (isset($item['taxname']) && count($item['taxname']) == 0)) {
                if (delete_taxes_from_item($item['itemid'], 'debit_note')) {
                    $affectedRows++;
                }
            } else {
                $item_taxes        = get_debit_note_item_taxes($item['itemid']);
                $_item_taxes_names = [];
                foreach ($item_taxes as $_item_tax) {
                    array_push($_item_taxes_names, $_item_tax['taxname']);
                }

                $i = 0;
                foreach ($_item_taxes_names as $_item_tax) {
                    if (!in_array($_item_tax, $item['taxname'])) {
                        $this->db->where('id', $item_taxes[$i]['id'])
                            ->delete(db_prefix() . 'item_tax');
                        if ($this->db->affected_rows() > 0) {
                            $affectedRows++;
                        }
                    }
                    $i++;
                }
                if (_maybe_insert_post_item_tax($item['itemid'], $item, $id, 'debit_note')) {
                    $affectedRows++;
                }
            }
        }

        foreach ($newitems as $key => $item) {
            if ($new_item_added = add_new_sales_item_post($item, $id, 'debit_note')) {
                _maybe_insert_post_item_tax($new_item_added, $item, $id, 'debit_note');
                $affectedRows++;
            }
        }


        if ($affectedRows > 0) {
            $this->update_debit_note_status($id);
            update_sales_total_tax_column($id, 'debit_note', db_prefix() . 'pur_debit_notes');
        }

        if ($affectedRows > 0) {
            return true;
        }

        return false;
    }


    /**
    * Delete debit note
    * @param  mixed $id credit note id
    * @return boolean
    */
    public function delete_debit_note($id, $simpleDelete = false)
    {

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'pur_debit_notes');
        if ($this->db->affected_rows() > 0) {
            $current_debit_note_number = get_option('next_debit_note_number');

            if ($current_credit_note_number > 1 && $simpleDelete == false && is_last_credit_note($id)) {
                // Decrement next credit note number
                $this->db->where('name', 'next_debit_note_number');
                $this->db->set('value', 'value-1', false);
                $this->db->update(db_prefix() . 'options');
            }

            $this->db->where('debit_id', $id);
            $this->db->delete(db_prefix() . 'pur_debits');

            $this->db->where('debit_note_id', $id);
            $this->db->delete(db_prefix() . 'pur_debits_refunds');

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'debit_note');
            $this->db->delete(db_prefix() . 'itemable');

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'debit_note');
            $this->db->delete(db_prefix() . 'item_tax');

            $attachments = $this->get_attachments($id);
            foreach ($attachments as $attachment) {
                $this->delete_attachment($attachment['id']);
            }

            $this->db->where('rel_type', 'debit_note');
            $this->db->where('rel_id', $id);
            $this->db->delete(db_prefix() . 'reminders');


            return true;
        }

        return false;
    }

    /**
     * Gets the applied invoice debits.
     *
     * @param        $invoice_id  The invoice identifier
     *
     * @return       The applied invoice debits.
     */
    public function get_applied_invoice_debits($invoice_id)
    {
        $this->db->order_by('date', 'desc');
        $this->db->where('invoice_id', $invoice_id);

        return $this->db->get(db_prefix() . 'pur_debits')->result_array();
    }

    /**
     * { apply debits }
     *
     * @param        $id     The identifier
     * @param        $data   The data
     *
     * @return     bool    
     */
    public function apply_debits($id, $data)
    {
        if ($data['amount'] == 0) {
            return false;
        }

        $this->db->insert(db_prefix() . 'pur_debits', [
            'invoice_id'   => $data['invoice_id'],
            'debit_id'    => $id,
            'staff_id'     => get_staff_user_id(),
            'date'         => date('Y-m-d'),
            'date_applied' => date('Y-m-d H:i:s'),
            'amount'       => $data['amount'],
        ]);

        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            $this->update_debit_note_status($id);
        }

        return $insert_id;
    }

    /**
     * { function_description }
     *
     * @param        $id     The identifier
     *
     * @return       bool
     */
    public function update_debit_note_status($id)
    {
        $total_refunds_by_debit_note = $this->total_refunds_by_debit_note($id);
        $total_debits_used           = $this->total_debits_used_by_debit_note($id);

        $status = 1;

        // sum from table returns null if nothing found
        if ($total_debits_used || $total_refunds_by_debit_note) {
            $compare = $total_debits_used + $total_refunds_by_debit_note;

            $this->db->select('total');
            $this->db->where('id', $id);
            $debit = $this->db->get(db_prefix() . 'pur_debit_notes')->row();

            if ($debit) {
                if (function_exists('bccomp')) {
                    if (bccomp($debit->total, $compare, get_decimal_places()) === 0) {
                        $status = 2;
                    }
                } else {
                    if ($debit->total == $compare) {
                        $status = 2;
                    }
                }
            }
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'pur_debit_notes', ['status' => $status]);

        return $this->db->affected_rows() > 0 ? true : false;
    }

    /**
     * { update pur invoice status }
     *
     * @param        $id     The identifier
     */
    public function update_pur_invoice_status($id){
        $pur_invoice = $this->get_pur_invoice($id);
        if($pur_invoice){
            $status_inv = $pur_invoice->payment_status;

            $left_to_pay = purinvoice_left_to_pay($id);
            if($left_to_pay > 0 && $left_to_pay < $pur_invoice->total){
                $status_inv = 'partially_paid';
            }else if($left_to_pay > 0 && $left_to_pay == $pur_invoice->total){
                $status_inv = 'unpaid';
            }else if($left_to_pay == 0){
                $status_inv = 'paid';
            }
            $this->db->where('id',$id);
            $this->db->update(db_prefix().'pur_invoices', [ 'payment_status' => $status_inv, ]);
        }
    }

    /**
     * { delete applied credit }
     *
     * @param        $id          The identifier
     * @param        $debit_id   The credit identifier
     * @param        $invoice_id  The invoice identifier
     */
    public function delete_applied_debit($id, $debit_id, $invoice_id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'pur_debits');
        if ($this->db->affected_rows() > 0) {
            $this->update_debit_note_status($debit_id);
            $this->update_pur_invoice_status($invoice_id);
        }
    }

    /**
     * { mark }
     *
     * @param        $id      The identifier
     * @param        $status  The status
     *
     * @return       ( bool )
     */
    public function mark_debit_note($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'pur_debit_notes', ['status' => $status]);

        return $this->db->affected_rows() > 0 ? true : false;
    }

    /**
     * Gets the refund.
     *
     * @param        $id     The identifier
     *
     * @return       The refund.
     */
    public function get_refund($id)
    {
        $this->db->where('id', $id);

        return $this->db->get(db_prefix() . 'pur_debits_refunds')->row();
    }

    /**
     * Creates a refund.
     *
     * @param        $id     The identifier
     * @param        $data   The data
     *
     * @return     bool    
     */
    public function create_refund($id, $data)
    {
        if ($data['amount'] == 0) {
            return false;
        }

        $data['note'] = trim($data['note']);

        $this->db->insert(db_prefix() . 'pur_debits_refunds', [
            'created_at'     => date('Y-m-d H:i:s'),
            'debit_note_id' => $id,
            'staff_id'       => $data['staff_id'],
            'refunded_on'    => $data['refunded_on'],
            'payment_mode'   => $data['payment_mode'],
            'amount'         => $data['amount'],
            'note'           => nl2br($data['note']),
        ]);

        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            $this->update_debit_note_status($id);
        }

        return $insert_id;
    }

    /**
     * { edit refund }
     *
     * @param        $id     The identifier
     * @param        $data   The data
     *
     * @return     bool    
     */
    public function edit_refund($id, $data)
    {
        if ($data['amount'] == 0) {
            return false;
        }

        $refund = $this->get_refund($id);

        $data['note'] = trim($data['note']);

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'pur_debits_refunds', [
            'refunded_on'  => $data['refunded_on'],
            'payment_mode' => $data['payment_mode'],
            'amount'       => $data['amount'],
            'note'         => nl2br($data['note']),
        ]);

        $insert_id = $this->db->insert_id();

        if ($this->db->affected_rows() > 0) {
            $this->update_debit_note_status($refund->debit_note_id);
        }

        return $insert_id;
    }

    /**
     * { delete refund }
     *
     * @param        $refund_id       The refund identifier
     * @param        $debit_note_id  The debit note identifier
     *
     * @return     bool    
     */
    public function delete_refund($refund_id, $debit_note_id)
    {
        $this->db->where('id', $refund_id);
        $this->db->delete(db_prefix() . 'pur_debits_refunds');
        if ($this->db->affected_rows() > 0) {
            $this->update_debit_note_status($debit_note_id);
            return true;
        }

        return false;
    }

    /**
    *  Delete credit note attachment
    * @param   mixed $id  attachmentid
    * @return  boolean
    */
    public function delete_attachment($id)
    {
        $attachment = $this->misc_model->get_file($id);

        $deleted = false;
        if ($attachment) {
            if (empty($attachment->external)) {
                unlink(get_upload_path_by_type('debit_note') . $attachment->rel_id . '/' . $attachment->file_name);
            }
            $this->db->where('id', $attachment->id);
            $this->db->delete(db_prefix() . 'files');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
            }
            if (is_dir(get_upload_path_by_type('debit_note') . $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(get_upload_path_by_type('debit_note') . $attachment->rel_id);
                if (count($other_attachments) == 0) {
                    // okey only index.html so we can delete the folder also
                    delete_dir(get_upload_path_by_type('debit_note') . $attachment->rel_id);
                }
            }
        }

        return $deleted;
    }


    /**
     * Sends a debit note.
     *
     * @param         $data   The data
     *
     * @return     boolean
     */
    public function send_debit_note($data){
        $mail_data = [];
        $count_sent = 0;
        $debit_note = $this->get_debit_note($data['debit_note_id']);
        if(isset($data['attach_pdf'])){
            

            try {
                $pdf = debit_note_pdf($debit_note);
            } catch (Exception $e) {
                echo pur_html_entity_decode($e->getMessage());
                die;
            }

            $attach = $pdf->Output(format_debit_note_number( $debit_note->id) . '.pdf', 'S');
        }


        if(strlen(get_option('smtp_host')) > 0 && strlen(get_option('smtp_password')) > 0 ){
            foreach($data['send_to'] as $mail){

                $mail_data['debit_note_id'] = $data['debit_note_id'];
                $mail_data['content'] = $data['content'];
                $mail_data['mail_to'] = $mail;

                $template = mail_template('debit_note_to_contact', 'purchase', array_to_object($mail_data));

                if(isset($data['attach_pdf'])){
                    $template->add_attachment([
                        'attachment' => $attach,
                        'filename'   => str_replace('/', '-', format_debit_note_number( $debit_note->id) . '.pdf'),
                        'type'       => 'application/pdf',
                    ]);
                }

                $rs = $template->send();

                if($rs){
                    $count_sent++;
                }
            }

            if($count_sent > 0){
                return true;
            }  
        }

        return false;
    }

    /**
     * { total remaining debits by vendor }
     *
     * @param        $vendor_id  The customer identifier
     *
     * @return       ( total )
     */
    public function total_remaining_debits_by_vendor($vendor_id, $currency)
    {
        $base_currency = get_base_currency_pur();
        if($currency == 0){
            $currency = $base_currency->id;
        }

        $this->db->select('total,id');
        $this->db->where('vendorid', $vendor_id);
        $this->db->where('currency', $currency);
        $this->db->where('status', 1);

        $debits = $this->db->get(db_prefix() . 'pur_debit_notes')->result_array();

        $total = $this->calc_remaining_debits($debits);

        return $total;
    }

    /**
     * Gets the open debits.
     *
     * @param        $customer_id  The customer identifier
     *
     * @return       The open credits.
     */
    public function get_open_debits($vendor_id)
    {
        
        $this->db->where('status', 1);
        $this->db->where('vendorid', $vendor_id);

        $debits = $this->db->get(db_prefix() . 'pur_debit_notes')->result_array();

        foreach ($debits as $key => $debit) {
            $debits[$key]['available_debits'] = $this->calculate_available_debits($debit['id'], $debit['total']);
        }

        return $debits;
    }

    /**
     * Calculates the available debits.
     *
     * @param          $debit_id      The debit identifier
     * @param      bool      $debit_amount  The debit amount
     *
     * @return     bool|int  The available debits.
     */
    private function calculate_available_debits($debit_id, $debit_amount = false)
    {
        if ($debit_amount === false) {
            $this->db->select('total')
            ->from(db_prefix() . 'pur_debit_notes')
            ->where('id', $debit_id);

            $debit_amount = $this->db->get()->row()->total;
        }

        $available_total = $debit_amount;

        $bcsub           = function_exists('bcsub');
        $applied_debits = $this->get_applied_debits($debit_id);


        foreach ($applied_debits as $debit) {
            if ($bcsub) {
                $available_total = bcsub($available_total, $debit['amount'], get_decimal_places());
            } else {
                $available_total -= $debit['amount'];
            }
        }

        $total_refunds = $this->total_refunds_by_debit_note($debit_id);

        if ($total_refunds) {
            if ($bcsub) {
                $available_total = bcsub($available_total, $total_refunds, get_decimal_places());
            } else {
                $available_total -= $total_refunds;
            }
        }

        return $available_total;
    }

    /**
     * Get venor statement formatted
     * @param  mixed $customer_id vendor id
     * @param  string $from        date from
     * @param  string $to          date to
     * @return array
     */
    public function get_statement($vendor_id, $from, $to)
    {
        if (!class_exists('Invoices_model', false)) {
            $this->load->model('invoices_model');
        }

        $from = to_sql_date($from);
        $to = to_sql_date($to);

        $this->load->model('currencies_model');
        $currency = $this->currencies_model->get_base_currency();
        $base_currency = $this->currencies_model->get_base_currency();
        $vendor_currency = get_vendor_currency($vendor_id);
        if($vendor_currency != 0){
            $currency = $this->currencies_model->get($vendor_currency);
        }

        $sql = 'SELECT
        ' . db_prefix() . 'pur_invoices.id as invoice_id,
        ' . db_prefix() . 'pur_invoices.invoice_date as date,
        ' . db_prefix() . 'pur_invoices.duedate,
        concat(' . db_prefix() . 'pur_invoices.invoice_date, \' \', RIGHT(' . db_prefix() . 'pur_invoices.date_add,LOCATE(\' \',' . db_prefix() . 'pur_invoices.date_add) - 3)) as tmp_date,
        ' . db_prefix() . 'pur_invoices.duedate as duedate,
        ' . db_prefix() . 'pur_invoices.total as invoice_amount
        FROM ' . db_prefix() . 'pur_invoices WHERE vendor =' . $this->db->escape_str($vendor_id);

        if ($from == $to) {
            $sqlDate = 'invoice_date="' . $this->db->escape_str($from) . '"';
        } else {
            $sqlDate = '(invoice_date BETWEEN "' . $this->db->escape_str($from) . '" AND "' . $this->db->escape_str($to) . '")';
        }

        if ($from == $to) {
            $sqlDateDebit = 'date="' . $this->db->escape_str($from) . '"';
        } else {
            $sqlDateDebit = '(date BETWEEN "' . $this->db->escape_str($from) . '" AND "' . $this->db->escape_str($to) . '")';
        }

        $sql .= ' AND ' . $sqlDate;

        if($currency->id == $base_currency->id){
            $sql .= ' AND '.db_prefix().'pur_invoices.currency IN (0, '.$base_currency->id.')';
        }else{
             $sql .= ' AND '.db_prefix().'pur_invoices.currency = '.$currency->id;
        }

        $invoices = $this->db->query($sql . '
            ORDER By invoice_date DESC')->result_array();

        // Debit notes
        $sql_debit_notes = 'SELECT
        ' . db_prefix() . 'pur_debit_notes.id as debit_note_id,
        ' . db_prefix() . 'pur_debit_notes.date as date,
        concat(' . db_prefix() . 'pur_debit_notes.date, \' \', RIGHT(' . db_prefix() . 'pur_debit_notes.datecreated,LOCATE(\' \',' . db_prefix() . 'pur_debit_notes.datecreated) - 3)) as tmp_date,
        ' . db_prefix() . 'pur_debit_notes.total as debit_note_amount
        FROM ' . db_prefix() . 'pur_debit_notes WHERE vendorid =' . $this->db->escape_str($vendor_id) . ' AND status != 3';

        $sql_debit_notes .= ' AND ' . $sqlDateDebit;

        if($currency->id == $base_currency->id){
            $sql_debit_notes .= ' AND '.db_prefix().'pur_debit_notes.currency IN (0, '.$base_currency->id.')';
        }else{
             $sql_debit_notes .= ' AND '.db_prefix().'pur_debit_notes.currency = '.$currency->id;
        }
        $debit_notes = $this->db->query($sql_debit_notes)->result_array();

        // Debits applied
        $sql_debits_applied = 'SELECT
        ' . db_prefix() . 'pur_debits.id as debit_id,
        invoice_id as debit_invoice_id,
        ' . db_prefix() . 'pur_debits.debit_id as debit_applied_debit_note_id,
        ' . db_prefix() . 'pur_debits.date as date,
        concat(' . db_prefix() . 'pur_debits.date, \' \', RIGHT(' . db_prefix() . 'pur_debits.date_applied,LOCATE(\' \',' . db_prefix() . 'pur_debits.date_applied) - 3)) as tmp_date,
        ' . db_prefix() . 'pur_debits.amount as debit_amount
        FROM ' . db_prefix() . 'pur_debits
        JOIN ' . db_prefix() . 'pur_debit_notes ON ' . db_prefix() . 'pur_debit_notes.id = ' . db_prefix() . 'pur_debits.debit_id
        ';

        $sql_debits_applied .= '
        WHERE vendorid =' . $this->db->escape_str($vendor_id);

        $sqlDateDebitsAplied = str_replace('date', db_prefix() . 'pur_debits.date', $sqlDateDebit);

        $sql_debits_applied .= ' AND ' . $sqlDateDebitsAplied;

        if($currency->id == $base_currency->id){
            $sql_debits_applied .= ' AND '.db_prefix().'pur_debit_notes.currency IN (0, '.$base_currency->id.')';
        }else{
             $sql_debits_applied .= ' AND '.db_prefix().'pur_debit_notes.currency = '.$currency->id;
        }
        $debits_applied = $this->db->query($sql_debits_applied)->result_array();

        // Replace error ambigious column in where clause
        $sqlDatePayments = str_replace('invoice_date', db_prefix() . 'pur_invoice_payment.date', $sqlDate);

        $sql_pay = '';
        if($currency->id == $base_currency->id){
            $sql_pay .= ' AND '.db_prefix().'pur_invoices.currency IN (0, '.$base_currency->id.')';
        }else{
             $sql_pay .= ' AND '.db_prefix().'pur_invoices.currency = '.$currency->id;
        }

        $sql_payments = 'SELECT
        ' . db_prefix() . 'pur_invoice_payment.id as payment_id,
        ' . db_prefix() . 'pur_invoice_payment.date as date,
        concat(' . db_prefix() . 'pur_invoice_payment.date, \' \', RIGHT(' . db_prefix() . 'pur_invoice_payment.daterecorded,LOCATE(\' \',' . db_prefix() . 'pur_invoice_payment.daterecorded) - 3)) as tmp_date,
        ' . db_prefix() . 'pur_invoice_payment.pur_invoice as payment_invoice_id,
        ' . db_prefix() . 'pur_invoice_payment.amount as payment_total
        FROM ' . db_prefix() . 'pur_invoice_payment
        JOIN ' . db_prefix() . 'pur_invoices ON ' . db_prefix() . 'pur_invoices.id = ' . db_prefix() . 'pur_invoice_payment.pur_invoice
        WHERE ' . $sqlDatePayments . ' AND ' . db_prefix() . 'pur_invoices.vendor = ' . $this->db->escape_str($vendor_id) . ' '.$sql_pay.' AND approval_status = 2
        ORDER by ' . db_prefix() . 'pur_invoice_payment.date DESC';

        $payments = $this->db->query($sql_payments)->result_array();

        $sqlDebitNoteRefunds = str_replace('date', 'refunded_on', $sqlDateDebit);

        $sql_refunds_sub_query = '';

        if($currency->id == $base_currency->id){
            $sql_refunds_sub_query .= ' AND '.db_prefix().'pur_debit_notes.currency IN (0, '.$base_currency->id.')';
        }else{
             $sql_refunds_sub_query .= ' AND '.db_prefix().'pur_debit_notes.currency = '.$currency->id;
        }

        $sql_debit_notes_refunds = 'SELECT id as debit_note_refund_id,
        debit_note_id as refund_debit_note_id,
        amount as refund_amount,
        concat(' . db_prefix() . 'pur_debits_refunds.refunded_on, \' \', RIGHT(' . db_prefix() . 'pur_debits_refunds.created_at,LOCATE(\' \',' . db_prefix() . 'pur_debits_refunds.created_at) - 3)) as tmp_date,
        refunded_on as date FROM ' . db_prefix() . 'pur_debits_refunds
        WHERE ' . $sqlDebitNoteRefunds . ' AND debit_note_id IN (SELECT id FROM ' . db_prefix() . 'pur_debit_notes WHERE vendorid=' . $this->db->escape_str($vendor_id) . ' '.$sql_refunds_sub_query.')
        ';


        $debit_notes_refunds = $this->db->query($sql_debit_notes_refunds)->result_array();

        // merge results
        $merged = array_merge($invoices, $payments, $debit_notes, $debits_applied, $debit_notes_refunds);

        // sort by date
        usort($merged, function ($a, $b) {
            // fake date select sorting
            return strtotime($a['tmp_date']) - strtotime($b['tmp_date']);
        });

        // Define final result variable
        $result = [];
        // Store in result array key
        $result['result'] = $merged;

        // Invoiced amount during the period
        $sql_invoiced_amount = '';
        if($currency->id == $base_currency->id){
            $sql_invoiced_amount .= ' AND '.db_prefix().'pur_invoices.currency IN (0, '.$base_currency->id.')';
        }else{
             $sql_invoiced_amount .= ' AND '.db_prefix().'pur_invoices.currency = '.$currency->id;
        }
        $result['invoiced_amount'] = $this->db->query('SELECT
        SUM(' . db_prefix() . 'pur_invoices.total) as invoiced_amount
        FROM ' . db_prefix() . 'pur_invoices
        WHERE vendor = ' . $this->db->escape_str($vendor_id) . '
        AND ' . $sqlDate . ''.$sql_invoiced_amount)
            ->row()->invoiced_amount;

        if ($result['invoiced_amount'] === null) {
            $result['invoiced_amount'] = 0;
        }


        $sql_debit_notes_amount = '';
        if($currency->id == $base_currency->id){
            $sql_debit_notes_amount .= ' AND '.db_prefix().'pur_debit_notes.currency IN (0, '.$base_currency->id.')';
        }else{
             $sql_debit_notes_amount .= ' AND '.db_prefix().'pur_debit_notes.currency = '.$currency->id;
        }
        $result['debit_notes_amount'] = $this->db->query('SELECT
        SUM(' . db_prefix() . 'pur_debit_notes.total) as debit_notes_amount
        FROM ' . db_prefix() . 'pur_debit_notes
        WHERE vendorid = ' . $this->db->escape_str($vendor_id) . '
        AND ' . $sqlDateDebit . ' AND status != 3'. $sql_debit_notes_amount)
            ->row()->debit_notes_amount;

        if ($result['debit_notes_amount'] === null) {
            $result['debit_notes_amount'] = 0;
        }
    
        
        $sql_refunds_amount = '';
        if($currency->id == $base_currency->id){
            $sql_refunds_amount .= ' AND '.db_prefix().'pur_debit_notes.currency IN (0, '.$base_currency->id.')';
        }else{
             $sql_refunds_amount .= ' AND '.db_prefix().'pur_debit_notes.currency = '.$currency->id;
        }
        $result['refunds_amount'] = $this->db->query('SELECT
        SUM(' . db_prefix() . 'pur_debits_refunds.amount) as refunds_amount
        FROM ' . db_prefix() . 'pur_debits_refunds
        WHERE ' . $sqlDebitNoteRefunds . ' AND debit_note_id IN (SELECT id FROM ' . db_prefix() . 'pur_debit_notes WHERE vendorid=' . $this->db->escape_str($vendor_id) . ' '.$sql_refunds_amount.')
        ')->row()->refunds_amount;

        if ($result['refunds_amount'] === null) {
            $result['refunds_amount'] = 0;
        }
        

        $result['invoiced_amount'] = $result['invoiced_amount'] - $result['debit_notes_amount'];

        // Amount paid during the period

        $sql_amount_paid = '';
        if($currency->id == $base_currency->id){
            $sql_amount_paid .= ' AND '.db_prefix().'pur_invoices.currency IN (0, '.$base_currency->id.')';
        }else{
             $sql_amount_paid .= ' AND '.db_prefix().'pur_invoices.currency = '.$currency->id;
        }
        $result['amount_paid'] = $this->db->query('SELECT
        SUM(' . db_prefix() . 'pur_invoice_payment.amount) as amount_paid
        FROM ' . db_prefix() . 'pur_invoice_payment
        JOIN ' . db_prefix() . 'pur_invoices ON ' . db_prefix() . 'pur_invoices.id = ' . db_prefix() . 'pur_invoice_payment.pur_invoice
        WHERE ' . $sqlDatePayments . ' AND ' . db_prefix() . 'pur_invoices.vendor = ' . $this->db->escape_str($vendor_id).' '.$sql_amount_paid .' AND approval_status = 2' )
            ->row()->amount_paid;

        if ($result['amount_paid'] === null) {
            $result['amount_paid'] = 0;
        }


        $sql_inv_beginning_balance = '';
        if($currency->id == $base_currency->id){
            $sql_inv_beginning_balance .= ' AND '.db_prefix().'pur_invoices.currency IN (0, '.$base_currency->id.')';
        }else{
             $sql_inv_beginning_balance .= ' AND '.db_prefix().'pur_invoices.currency = '.$currency->id;
        }

        $sql_db_beginning_balance = '';
        if($currency->id == $base_currency->id){
            $sql_db_beginning_balance .= ' AND '.db_prefix().'pur_debit_notes.currency IN (0, '.$base_currency->id.')';
        }else{
             $sql_db_beginning_balance .= ' AND '.db_prefix().'pur_debit_notes.currency = '.$currency->id;
        }

        // Beginning balance is all invoices amount before the FROM date - payments received before FROM date
        $result['beginning_balance'] = $this->db->query('
            SELECT (
            COALESCE(SUM(' . db_prefix() . 'pur_invoices.total),0) - (
            (
            SELECT COALESCE(SUM(' . db_prefix() . 'pur_invoice_payment.amount),0)
            FROM ' . db_prefix() . 'pur_invoice_payment
            JOIN ' . db_prefix() . 'pur_invoices ON ' . db_prefix() . 'pur_invoices.id = ' . db_prefix() . 'pur_invoice_payment.pur_invoice
            WHERE ' . db_prefix() . 'pur_invoice_payment.date < "' . $this->db->escape_str($from) . '"
            AND ' . db_prefix() . 'pur_invoices.vendor =' . $this->db->escape_str($vendor_id) . ' '.$sql_inv_beginning_balance.'
            ) + (
                SELECT COALESCE(SUM(' . db_prefix() . 'pur_debit_notes.total),0)
                FROM ' . db_prefix() . 'pur_debit_notes
                WHERE ' . db_prefix() . 'pur_debit_notes.date < "' . $this->db->escape_str($from) . '"
                AND ' . db_prefix() . 'pur_debit_notes.vendorid=' . $this->db->escape_str($vendor_id) . ' '.$sql_db_beginning_balance.'
            )
        )
            )
            as beginning_balance FROM ' . db_prefix() . 'pur_invoices
            WHERE invoice_date < "' . $this->db->escape_str($from) . '"
            AND vendor = ' . $this->db->escape_str($vendor_id).' '. $sql_inv_beginning_balance)->row()->beginning_balance;

        if ($result['beginning_balance'] === null) {
            $result['beginning_balance'] = 0;
        }

        $dec = get_decimal_places();

        if (function_exists('bcsub')) {
            $result['balance_due'] = bcsub($result['invoiced_amount'], $result['amount_paid'], $dec);
            $result['balance_due'] = bcadd($result['balance_due'], $result['beginning_balance'], $dec);
            $result['balance_due'] = bcadd($result['balance_due'], $result['refunds_amount'], $dec);
        } else {
            $result['balance_due'] = number_format($result['invoiced_amount'] - $result['amount_paid'], $dec, '.', '');
            $result['balance_due'] = $result['balance_due'] + number_format($result['beginning_balance'], $dec, '.', '');
            $result['balance_due'] = $result['balance_due'] + number_format($result['refunds_amount'], $dec, '.', '');
        }

        // Subtract amount paid - refund, because the refund is not actually paid amount
        $result['amount_paid'] = $result['amount_paid'] - $result['refunds_amount'];

        $result['vendor_id'] = $vendor_id;
        $result['client']    = $this->get_vendor($vendor_id);
        $result['from']      = $from;
        $result['to']        = $to;
        

        $result['currency'] = $currency;

        return $result;
    }

    /**
     * Send vendor statement to email
     * @return boolean
     */
    public function send_statement_to_email($data)
    {
       $mail_data = [];
        $count_sent = 0;

        if(isset($data['attach_pdf'])){
            $statement = $this->get_statement($data['vendor_id'], $data['from'], $data['to']);

            try {
                $pdf = purchase_statement_pdf($statement);
            } catch (Exception $e) {
                echo pur_html_entity_decode($e->getMessage());
                die;
            }
            $pdf_file_name = slug_it(_l('vendor_statement') . '-' . $statement['client']->company);

            $attach = $pdf->Output($pdf_file_name . '.pdf', 'S');
        }


        if(strlen(get_option('smtp_host')) > 0 && strlen(get_option('smtp_password')) > 0 ){
            foreach($data['send_to'] as $mail){

                
                $mail_data['content'] = $data['content'];
                $mail_data['mail_to'] = $mail;
                $mail_data['statement'] = $statement;

                $this->db->where('email', $mail);
                $mail_data['contact'] = $this->db->get(db_prefix().'pur_contacts')->row();

                $template = mail_template('purchase_statement_to_contact', 'purchase', array_to_object($mail_data));

                if(isset($data['attach_pdf'])){
                    $template->add_attachment([
                        'attachment' => $attach,
                        'filename'   => str_replace('/', '-', $pdf_file_name . '.pdf'),
                        'type'       => 'application/pdf',
                    ]);
                }

                $rs = $template->send();

                if($rs){
                    $count_sent++;
                }
            }

            if($count_sent > 0){
                return true;
            }  
        }

        return false;


    }

    /**
     * delete purchase permission
     * @param  [type] $id 
     * @return [type]     
     */
    public function delete_purchase_permission($id)
    {
        $str_permissions ='';
        foreach (list_purchase_permisstion() as $per_key =>  $per_value) {
            if(strlen($str_permissions) > 0){
                $str_permissions .= ",'".$per_value."'";
            }else{
                $str_permissions .= "'".$per_value."'";
            }
        }

        $sql_where = " feature IN (".$str_permissions.") ";

        $this->db->where('staff_id', $id);
        $this->db->where($sql_where);
        $this->db->delete(db_prefix() . 'staff_permissions');

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * { update customfield invoice }
     *
     * @param        $id     The identifier
     * @param        $data   The data
     */
    public function update_customfield_invoice($id, $data){

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                return true;
            }
        }
        return false;
    }

    /**
     * { refresh order value }
     */
    public function refresh_order_value($po_id){
        $purchase_order = $this->get_pur_order($po_id);
        $purchase_order_detail = $this->get_pur_order_detail($po_id);
        $affected_rows = 0;
        $has_change = 0;

        if(count($purchase_order_detail) > 0){

            $subtotal = 0;
            $total_tax = 0;
            $total = 0;
            $discount_ = 0;
            $final_total = 0;
            foreach($purchase_order_detail as $order_detail){
                $item = $this->get_items_by_id($order_detail['item_code']);
                if($item){
                    if($item->purchase_price != $order_detail['unit_price']){

                        $into_money = $item->purchase_price * $order_detail['quantity'];
                        $tax_value = 0;


                        if($order_detail['tax_rate'] != ''){
                            $tax_data = explode('|', $order_detail['tax_rate']);
                            foreach($tax_data as $rate){
                                if($purchase_order->discount_type == 'after_tax' || $purchase_order->discount_type == '' || $purchase_order->discount_type == null){
                                    $tax_value += $rate*$into_money / 100;
                                }
                            }
                        }

                        
                        
                        $discount_tt = ($order_detail['discount_money'] != '' && $order_detail['discount_money'] > 0) ? $order_detail['discount_money'] : 0;
                        if($order_detail['discount_%'] != '' && $order_detail['discount_%'] > 0){
                            if($purchase_order->discount_type == 'before_tax' ){
                                $discount_tt = $order_detail['discount_%'] * $into_money / 100;
                            }else if($purchase_order->discount_type == 'after_tax' || $purchase_order->discount_type == '' || $purchase_order->discount_type == null){
                                $total_include_tax = $into_money + $tax_value;
                                $discount_tt = $order_detail['discount_%'] * $total_include_tax / 100;
                            } 
                        }

                        if($order_detail['tax_rate'] != ''){
                            if($purchase_order->discount_type == 'before_tax'){
                                $after_dc_amount = $into_money - $discount_tt;
                                $tax_data = explode('|', $order_detail['tax_rate']);
                                foreach($tax_data as $rate){
                                    $tax_value += $rate*$after_dc_amount/100;
                                }

                            }
                        }

                        $total = $into_money + $tax_value;
                        $total_money = $total;


                        if($discount_tt != '' && $discount_tt > 0){
                            $total_money = $total - $discount_tt;
                        }

                        $final_total += $total_money;

                        $this->db->where('pur_order', $po_id);
                        $this->db->where('item_code', $item->id);
                        $this->db->update(db_prefix().'pur_order_detail',[
                            'unit_price' => $item->purchase_price,
                            'into_money' => $into_money,
                            'tax_value' => $tax_value,
                            'total' => $total,
                            'total_money' => $total_money,
                            'discount_money' => $discount_tt,

                        ]);
                        if ($this->db->affected_rows() > 0) {
                            $affected_rows++;
                        }

                        $subtotal += $into_money;
                        $total_tax += $tax_value;
                        $discount_ += $discount_tt;

                        $has_change++;
                    }
                }
            }

            if($has_change > 0 ){
            
                $_taxes = $this->get_html_tax_pur_order($po_id);
                $_total_tax = 0;
                foreach($_taxes['taxes_val'] as $tax_val){
                    $_total_tax += $tax_val;
                }

                $this->db->where('id', $po_id);
                $this->db->update(db_prefix().'pur_orders', [
                    'subtotal' => $subtotal,
                    'total_tax' => $_total_tax,
                    'total' => $final_total,
                    'discount_total' => 0,
                ]);
                if ($this->db->affected_rows() > 0) {
                    $affected_rows++;
                }
            }
        }

        if($affected_rows > 0){
            return true;
        }

        return false;
    }

    /**
     * wh get grouped
     * @return [type] 
     */
    public function pur_get_grouped($can_be = '', $search_all = false, $vendor = '')
    {
        $items = [];
        $this->db->order_by('name', 'asc');
        $groups = $this->db->get(db_prefix() . 'items_groups')->result_array();

        array_unshift($groups, [
            'id'   => 0,
            'name' => '',
        ]);

        foreach ($groups as $group) {
            $this->db->select('*,' . db_prefix() . 'items_groups.name as group_name,' . db_prefix() . 'items.id as id');
            if(strlen($can_be) > 0){
                $this->db->where(db_prefix().'items.can_be_purchased', $can_be);
            }
         
            if($vendor != ''){
                $this->db->where(db_prefix().'items.id in (SELECT items from '.db_prefix().'pur_vendor_items WHERE vendor = '.$vendor.')');
            }

            $this->db->where('group_id', $group['id']);
            $this->db->where(db_prefix().'items.active', 1);
            $this->db->join(db_prefix() . 'items_groups', '' . db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id', 'left');
            $this->db->order_by('description', 'asc');

            $_items = $this->db->get(db_prefix() . 'items')->result_array();

            if (count($_items) > 0) {
                $items[$group['id']] = [];
                foreach ($_items as $i) {
                    array_push($items[$group['id']], $i);
                }
            }
        }

        return $items;
    }

    /**
     * Creates a purchase request row template.
     *
     * @param      array   $unit_data  The unit data
     * @param      string  $name       The name
     */
    public function create_purchase_request_row_template($name = '', $item_code = '', $item_text = '', $unit_price = '', $quantity = '', $unit_name = '', $unit_id = '', $into_money = '', $item_key = '', $tax_value = '', $total = '', $tax_name = '', $tax_rate = '', $tax_id = '', $is_edit = false, $currency_rate = 1, $to_currency = ''){
        $this->load->model('invoice_items_model');
        $row = '';

        $name_item_code = 'item_code';
        $name_item_text = 'item_text';
        $name_unit_id = 'unit_id';
        $name_unit_name = 'unit_name';
        $name_unit_price = 'unit_price';
        $name_quantity = 'quantity';
        $name_into_money = 'into_money';
        $name_tax = 'tax';
        $name_tax_value = 'tax_value';
        $name_tax_name = 'tax_name';
        $name_tax_rate = 'tax_rate';
        $name_tax_id_select = 'tax_select';
        $name_total = 'total';

        $array_rate_attr = [ 'min' => '0.0', 'step' => 'any'];
        $array_qty_attr = [ 'min' => '0.0', 'step' => 'any'];
        $array_subtotal_attr = ['readonly' => true];

        $text_right_class = 'text-right';

        if ($name == '') {
            $row .= '<tr class="main">
                  <td></td>';
            $vehicles = [];
            $array_attr = ['placeholder' => _l('unit_price')];

            $manual             = true;
            $invoice_item_taxes = '';
            $total = '';
            $into_money = 0;
        } else {
            $manual             = false;
            $row .= '<tr class="sortable item">
                    <td class="dragger"><input type="hidden" class="order" name="' . $name . '[order]"><input type="hidden" class="ids" name="' . $name . '[id]" value="' . $item_key . '"></td>';
            $name_item_code = $name . '[item_code]';
            $name_item_text = $name . '[item_text]';
            $name_unit_id = $name . '[unit_id]';
            $name_unit_name = $name . '[unit_name]';
            $name_unit_price = $name . '[unit_price]';
            $name_quantity = $name . '[quantity]';
            $name_into_money = $name . '[into_money]';
            $name_tax = $name .'[tax]';
            $name_tax_value = $name . '[tax_value]';
            $name_tax_name = $name . '[tax_name]';
            $name_tax_rate = $name . '[tax_rate]';
            $name_tax_id_select = $name . '[tax_select][]';
            $name_total = $name . '[total]';

            $array_rate_attr = ['onblur' => 'pur_calculate_total();', 'onchange' => 'pur_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('unit_price')];

            $array_qty_attr = ['onblur' => 'pur_calculate_total();', 'onchange' => 'pur_calculate_total();', 'min' => '0.0' , 'step' => 'any',  'data-quantity' => (float)$quantity];

            $tax_money = 0;
            $tax_rate_value = 0;

            if($is_edit){
                $invoice_item_taxes = pur_convert_item_taxes($tax_id, $tax_rate, $tax_name);
                $arr_tax_rate = explode('|', $tax_rate ?? '');
                foreach ($arr_tax_rate as $key => $value) {
                    $tax_rate_value += (float)$value;
                }
            }else{
                $invoice_item_taxes = $tax_name;
                $tax_rate_data = $this->pur_get_tax_rate($tax_name);
                $tax_rate_value = $tax_rate_data['tax_rate'];
            }

            if((float)$tax_rate_value != 0){
                $tax_money = (float)$unit_price * (float)$quantity * (float)$tax_rate_value / 100;
               
                $amount = (float)$unit_price * (float)$quantity + (float)$tax_money;
            }else{
                
                $amount = (float)$unit_price * (float)$quantity;
            }

            $into_money = (float)$unit_price * (float)$quantity;
            $total = $amount;

        }


        $row .= '<td class="">' . render_textarea($name_item_text, '', $item_text, ['rows' => 2, 'placeholder' => _l('pur_item_name')] ) . '</td>';
        $row .= '<td class="rate">' . render_input($name_unit_price, '', $unit_price, 'number', $array_rate_attr, [], 'no-margin', $text_right_class) ;
        if( $unit_price != ''){
            $original_price = round( ($unit_price/$currency_rate), 2);
            $base_currency = get_base_currency();
            if($to_currency != 0 && $to_currency != $base_currency->id){
                $row .= render_input('original_price', '',app_format_money($original_price, $base_currency), 'text', ['data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => _l('original_price'), 'disabled' => true], [], 'no-margin', 'input-transparent text-right pur_input_none');
            }

            $row .= '<input class="hide" name="og_price" disabled="true" value="'.$original_price.'">';
        }

        $row .=  '</td>';

        $row .= '<td class="quantities">' . 
        render_input($name_quantity, '', $quantity, 'number', $array_qty_attr, [], 'no-margin', $text_right_class) . 
        render_input($name_unit_name, '', $unit_name, 'text', ['placeholder' => _l('unit'), 'readonly' => true], [], 'no-margin', 'input-transparent text-right pur_input_none').
        '</td>';

        $row .= '<td class="into_money">' . render_input($name_into_money, '', $into_money, 'number', $array_subtotal_attr, [], '', $text_right_class) . '</td>';
        $row .= '<td class="taxrate">' . $this->get_taxes_dropdown_template($name_tax_id_select, $invoice_item_taxes, 'invoice', $item_key, true, $manual) . '</td>';
        $row .= '<td class="tax_value">' . render_input($name_tax_value, '', $tax_value, 'number', $array_subtotal_attr, [], '', $text_right_class) . '</td>';
        $row .= '<td class="hide item_code">' . render_input($name_item_code, '', $item_code, 'text', ['placeholder' => _l('item_code')]) . '</td>';
        $row .= '<td class="hide unit_id">' . render_input($name_unit_id, '', $unit_id, 'text', ['placeholder' => _l('unit_id')]) . '</td>';
        $row .= '<td class="_total">' . render_input($name_total, '', $total, 'number', $array_subtotal_attr, [], '', $text_right_class) . '</td>';

        if ($name == '') {
            $row .= '<td><button type="button" onclick="pur_add_item_to_table(\'undefined\',\'undefined\'); return false;" class="btn pull-right btn-info"><i class="fa fa-check"></i></button></td>';
        } else {
            $row .= '<td><a href="#" class="btn btn-danger pull-right" onclick="pur_delete_item(this,' . $item_key . ',\'.invoice-item\'); return false;"><i class="fa fa-trash"></i></a></td>';
        }
        $row .= '</tr>';
        return $row;
    }

    /**
     * wh get tax rate
     * @param  [type] $taxname 
     * @return [type]          
     */
    public function pur_get_tax_rate($taxname)
    {   
        $tax_rate = 0;
        $tax_rate_str = '';
        $tax_id_str = '';
        $tax_name_str = '';
        //var_dump($taxname); die;
        if(is_array($taxname)){
            foreach ($taxname as $key => $value) {
                $_tax = explode("|", $value);
                if(isset($_tax[1])){
                    $tax_rate += (float)$_tax[1];
                    if(strlen($tax_rate_str) > 0){
                        $tax_rate_str .= '|'.$_tax[1];
                    }else{
                        $tax_rate_str .= $_tax[1];
                    }

                    $this->db->where('name', $_tax[0]);
                    $taxes = $this->db->get(db_prefix().'taxes')->row();
                    if($taxes){
                        if(strlen($tax_id_str) > 0){
                            $tax_id_str .= '|'.$taxes->id;
                        }else{
                            $tax_id_str .= $taxes->id;
                        }
                    }

                    if(strlen($tax_name_str) > 0){
                        $tax_name_str .= '|'.$_tax[0];
                    }else{
                        $tax_name_str .= $_tax[0];
                    }
                }
            }
        }
        return ['tax_rate' => $tax_rate, 'tax_rate_str' => $tax_rate_str, 'tax_id_str' => $tax_id_str, 'tax_name_str' => $tax_name_str];
    }


    /**
     * get taxes dropdown template
     * @param  [type]  $name     
     * @param  [type]  $taxname  
     * @param  string  $type     
     * @param  string  $item_key 
     * @param  boolean $is_edit  
     * @param  boolean $manual   
     * @return [type]            
     */
    public function get_taxes_dropdown_template($name, $taxname, $type = '', $item_key = '', $is_edit = false, $manual = false)
    {
        // if passed manually - like in proposal convert items or project
        if($taxname != '' && !is_array($taxname)){
            $taxname = explode(',', $taxname);
        }

        if ($manual == true) {
            // + is no longer used and is here for backward compatibilities
            if (is_array($taxname) || strpos($taxname, '+') !== false) {
                if (!is_array($taxname)) {
                    $__tax = explode('+', $taxname);
                } else {
                    $__tax = $taxname;
                }
                // Multiple taxes found // possible option from default settings when invoicing project
                $taxname = [];
                foreach ($__tax as $t) {
                    $tax_array = explode('|', $t);
                    if (isset($tax_array[0]) && isset($tax_array[1])) {
                        array_push($taxname, $tax_array[0] . '|' . $tax_array[1]);
                    }
                }
            } else {
                $tax_array = explode('|', $taxname);
                // isset tax rate
                if (isset($tax_array[0]) && isset($tax_array[1])) {
                    $tax = get_tax_by_name($tax_array[0]);
                    if ($tax) {
                        $taxname = $tax->name . '|' . $tax->taxrate;
                    }
                }
            }
        }
        // First get all system taxes
        $this->load->model('taxes_model');
        $taxes = $this->taxes_model->get();
        $i     = 0;
        foreach ($taxes as $tax) {
            unset($taxes[$i]['id']);
            $taxes[$i]['name'] = $tax['name'] . '|' . $tax['taxrate'];
            $i++;
        }
        if ($is_edit == true) {

            // Lets check the items taxes in case of changes.
            // Separate functions exists to get item taxes for Invoice, Estimate, Proposal, Credit Note
            $func_taxes = 'get_' . $type . '_item_taxes';
            if (function_exists($func_taxes)) {
                $item_taxes = call_user_func($func_taxes, $item_key);
            }

            foreach ($item_taxes as $item_tax) {
                $new_tax            = [];
                $new_tax['name']    = $item_tax['taxname'];
                $new_tax['taxrate'] = $item_tax['taxrate'];
                $taxes[]            = $new_tax;
            }
        }

        // In case tax is changed and the old tax is still linked to estimate/proposal when converting
        // This will allow the tax that don't exists to be shown on the dropdowns too.
        if (is_array($taxname)) {
            foreach ($taxname as $tax) {
                // Check if tax empty
                if ((!is_array($tax) && $tax == '') || is_array($tax) && $tax['taxname'] == '') {
                    continue;
                };
                // Check if really the taxname NAME|RATE don't exists in all taxes
                if (!value_exists_in_array_by_key($taxes, 'name', $tax)) {
                    if (!is_array($tax)) {
                        $tmp_taxname = $tax;
                        $tax_array   = explode('|', $tax);
                    } else {
                        $tax_array   = explode('|', $tax['taxname']);
                        $tmp_taxname = $tax['taxname'];
                        if ($tmp_taxname == '') {
                            continue;
                        }
                    }
                    $taxes[] = ['name' => $tmp_taxname, 'taxrate' => $tax_array[1]];
                }
            }
        }

        // Clear the duplicates
        $taxes = $this->pur_uniqueByKey($taxes, 'name');

        $select = '<select class="selectpicker display-block taxes" data-width="100%" name="' . $name . '" multiple data-none-selected-text="' . _l('no_tax') . '">';

        foreach ($taxes as $tax) {
            $selected = '';
            if (is_array($taxname)) {
                foreach ($taxname as $_tax) {
                    if (is_array($_tax)) {
                        if ($_tax['taxname'] == $tax['name']) {
                            $selected = 'selected';
                        }
                    } else {
                        if ($_tax == $tax['name']) {
                            $selected = 'selected';
                        }
                    }
                }
            } else {
                if ($taxname == $tax['name']) {
                    $selected = 'selected';
                }
            }

            $select .= '<option value="' . $tax['name'] . '" ' . $selected . ' data-taxrate="' . $tax['taxrate'] . '" data-taxname="' . $tax['name'] . '" data-subtext="' . $tax['name'] . '">' . $tax['taxrate'] . '%</option>';
        }
        $select .= '</select>';

        return $select;
    }

    /**
     * wh uniqueByKey
     * @param  [type] $array 
     * @param  [type] $key   
     * @return [type]        
     */
    public function pur_uniqueByKey($array, $key)
    {
        $temp_array = [];
        $i          = 0;
        $key_array  = [];

        foreach ($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i]  = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }

        return $temp_array;
    }

    /**
     * { purchase commodity code search }
     *
     * @param        $q           The quarter
     * @param        $type        The type
     * @param      string  $can_be      Indicates if be
     * @param      bool    $search_all  The search all
     */
    public function pur_commodity_code_search($q, $type, $can_be = '', $search_all = false, $vendor = '', $group = ''){
        $this->db->select('rate, id, description as name, long_description as subtext, commodity_code, purchase_price');
        
        $this->db->group_start();
        $this->db->like('description', $q);
        $this->db->or_like('long_description', $q);
        $this->db->or_like('commodity_code', $q);
        $this->db->or_like('sku_code', $q);
        $this->db->group_end();
        if(strlen($can_be) > 0){
            $this->db->where($can_be, $can_be);
        }
        $this->db->where('active', 1);

        if($vendor != ''){
            $this->db->where('id in (SELECT items from '.db_prefix().'pur_vendor_items WHERE vendor = '.$vendor.')');
        }

        if($group != ''){
            $this->db->where('group_id', $group);
        }

        $this->db->order_by('id', 'desc');
        $this->db->limit(500);

        $items = $this->db->get(db_prefix() . 'items')->result_array();

        foreach ($items as $key => $item) {
            $items[$key]['subtext'] = strip_tags(mb_substr($item['subtext'] ?? '', 0, 200)) . '...';
            if($type == 'rate'){
                $items[$key]['name']    = '(' . app_format_number($item['rate']) . ') ' .$item['commodity_code'];
            }else{
                $items[$key]['name']    = '(' . app_format_number($item['purchase_price']) . ') ' .$item['commodity_code'] .' '.$item['name'];
            }

        }

        return $items;
    }

    /**
     * Gets the item v 2.
     *
     * @param      string  $id     The identifier
     *
     * @return       The item v 2.
     */
    public function get_item_v2($id = '')
    {
        $columns             = $this->db->list_fields(db_prefix() . 'items');
        $rateCurrencyColumns = '';
        foreach ($columns as $column) {
            if (strpos($column, 'rate_currency_') !== false) {
                $rateCurrencyColumns .= $column . ',';
            }
        }
        $this->db->select($rateCurrencyColumns . '' . db_prefix() . 'items.id as itemid,rate,
            t1.taxrate as taxrate,t1.id as taxid,t1.name as taxname,
            t2.taxrate as taxrate_2,t2.id as taxid_2,t2.name as taxname_2,
            CONCAT(commodity_code,"_",description) as code_description,long_description,group_id,' . db_prefix() . 'items_groups.name as group_name,unit,'.db_prefix().'ware_unit_type.unit_name as unit_name, purchase_price, unit_id, guarantee');
        $this->db->from(db_prefix() . 'items');
        $this->db->join('' . db_prefix() . 'taxes t1', 't1.id = ' . db_prefix() . 'items.tax', 'left');
        $this->db->join('' . db_prefix() . 'taxes t2', 't2.id = ' . db_prefix() . 'items.tax2', 'left');
        $this->db->join(db_prefix() . 'items_groups', '' . db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id', 'left');
        $this->db->join(db_prefix() . 'ware_unit_type', '' . db_prefix() . 'ware_unit_type.unit_type_id = ' . db_prefix() . 'items.unit_id', 'left');
        $this->db->order_by('description', 'asc');
        if (is_numeric($id)) {
            $this->db->where(db_prefix() . 'items.id', $id);

            return $this->db->get()->row();
        }

        return $this->db->get()->result_array();
    }

    /**
     * row item to variation
     * @param  [type] $item_value 
     * @return [type]             
     */
    public function row_item_to_variation($item_value)
    {
        if($item_value){

                $name = '';
                if($item_value->attributes != null && $item_value->attributes != ''){
                    $attributes_decode = json_decode($item_value->attributes);

          


                }

                $item_value->new_description = $item_value->description;
                
        }

        return $item_value;
    }

    /**
     * Creates a quotation row template.
     *
     * @param      string      $name            The name
     * @param      string      $item_name       The item name
     * @param      int|string  $quantity        The quantity
     * @param      string      $unit_name       The unit name
     * @param      int|string  $unit_price      The unit price
     * @param      string      $taxname         The taxname
     * @param      string      $item_code       The item code
     * @param      string      $unit_id         The unit identifier
     * @param      string      $tax_rate        The tax rate
     * @param      string      $total_money     The total money
     * @param      string      $discount        The discount
     * @param      string      $discount_money  The discount money
     * @param      string      $total           The total
     * @param      string      $into_money      Into money
     * @param      string      $tax_id          The tax identifier
     * @param      string      $tax_value       The tax value
     * @param      string      $item_key        The item key
     * @param      bool        $is_edit         Indicates if edit
     *
     * @return     string      
     */
    public function create_quotation_row_template($name = '', $item_name = '', $quantity = '', $unit_name = '', $unit_price = '', $taxname = '',  $item_code = '', $unit_id = '', $tax_rate = '', $total_money = '', $discount = '', $discount_money = '', $total = '', $into_money = '', $tax_id = '', $tax_value = '', $item_key = '',$is_edit = false, $currency_rate = 1, $to_currency = '') {
        
        $this->load->model('invoice_items_model');
        $row = '';

        $name_item_code = 'item_code';
        $name_item_name = 'item_name';
        $name_unit_id = 'unit_id';
        $name_unit_name = 'unit_name';
        $name_quantity = 'quantity';
        $name_unit_price = 'unit_price';
        $name_tax_id_select = 'tax_select';
        $name_tax_id = 'tax_id';
        $name_total = 'total';
        $name_tax_rate = 'tax_rate';
        $name_tax_name = 'tax_name';
        $name_tax_value = 'tax_value';
        $array_attr = [];
        $array_attr_payment = ['data-payment' => 'invoice'];
        $name_into_money = 'into_money';
        $name_discount = 'discount';
        $name_discount_money = 'discount_money';
        $name_total_money = 'total_money';

        $array_available_quantity_attr = [ 'min' => '0.0', 'step' => 'any', 'readonly' => true];
        $array_qty_attr = [ 'min' => '0.0', 'step' => 'any'];
        $array_rate_attr = [ 'min' => '0.0', 'step' => 'any'];
        $array_discount_attr = [ 'min' => '0.0', 'step' => 'any'];
        $array_discount_money_attr = [ 'min' => '0.0', 'step' => 'any'];
        $str_rate_attr = 'min="0.0" step="any"';

        $array_subtotal_attr = ['readonly' => true];
        $text_right_class = 'text-right';

        if ($name == '') {
            $row .= '<tr class="main">
                  <td></td>';
            $vehicles = [];
            $array_attr = ['placeholder' => _l('unit_price')];
         
            $manual             = true;
            $invoice_item_taxes = '';
            $amount = '';
            $sub_total = 0;

        } else {
            $row .= '<tr class="sortable item">
                    <td class="dragger"><input type="hidden" class="order" name="' . $name . '[order]"><input type="hidden" class="ids" name="' . $name . '[id]" value="' . $item_key . '"></td>';
            $name_item_code = $name . '[item_code]';
            $name_item_name = $name . '[item_name]';
            $name_unit_id = $name . '[unit_id]';
            $name_unit_name = '[unit_name]';
            $name_quantity = $name . '[quantity]';
            $name_unit_price = $name . '[unit_price]';
            $name_tax_id_select = $name . '[tax_select][]';
            $name_tax_id = $name . '[tax_id]';
            $name_total = $name . '[total]';
            $name_tax_rate = $name . '[tax_rate]';
            $name_tax_name = $name .'[tax_name]';
            $name_into_money = $name .'[into_money]';
            $name_discount = $name .'[discount]';
            $name_discount_money = $name .'[discount_money]';
            $name_total_money = $name . '[total_money]';
            $name_tax_value = $name. '[tax_value]';
      
           
            $array_qty_attr = ['onblur' => 'pur_calculate_total();', 'onchange' => 'pur_calculate_total();', 'min' => '0.0' , 'step' => 'any',  'data-quantity' => (float)$quantity];
            

            $array_rate_attr = ['onblur' => 'pur_calculate_total();', 'onchange' => 'pur_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('rate')];
            $array_discount_attr = ['onblur' => 'pur_calculate_total();', 'onchange' => 'pur_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('discount')];

            $array_discount_money_attr = ['onblur' => 'pur_calculate_total(1);', 'onchange' => 'pur_calculate_total(1);', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('discount')];

            $manual             = false;

            $tax_money = 0;
            $tax_rate_value = 0;

            if($is_edit){
                $invoice_item_taxes = pur_convert_item_taxes($tax_id, $tax_rate, $taxname);
                $arr_tax_rate = explode('|', $tax_rate ?? '');
                foreach ($arr_tax_rate as $key => $value) {
                    $tax_rate_value += (float)$value;
                }
            }else{
                $invoice_item_taxes = $taxname;
                $tax_rate_data = $this->pur_get_tax_rate($taxname);
                $tax_rate_value = $tax_rate_data['tax_rate'];
            }

            if((float)$tax_rate_value != 0){
                $tax_money = (float)$unit_price * (float)$quantity * (float)$tax_rate_value / 100;
                $goods_money = (float)$unit_price * (float)$quantity + (float)$tax_money;
                $amount = (float)$unit_price * (float)$quantity + (float)$tax_money;
            }else{
                $goods_money = (float)$unit_price * (float)$quantity;
                $amount = (float)$unit_price * (float)$quantity;
            }

            $sub_total = (float)$unit_price * (float)$quantity;
            $amount = app_format_number($amount);

        }
 

        $row .= '<td class="">' . render_textarea($name_item_name, '', $item_name, ['rows' => 2, 'placeholder' => _l('pur_item_name'), 'readonly' => true] ) . '</td>';

        $row .= '<td class="rate">' . render_input($name_unit_price, '', $unit_price, 'number', $array_rate_attr, [], 'no-margin', $text_right_class);
        if( $unit_price != ''){
            $original_price = ($currency_rate > 0) ? round( ($unit_price/$currency_rate), 2) : 0;
            $base_currency = get_base_currency();
            if($to_currency != 0 && $to_currency != $base_currency->id){
                $row .= render_input('original_price', '',app_format_money($original_price, $base_currency), 'text', ['data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => _l('original_price'), 'disabled' => true], [], 'no-margin', 'input-transparent text-right pur_input_none');
            }

            $row .= '<input class="hide" name="og_price" disabled="true" value="'.$original_price.'">';
        }

        $row .=  '</td>';
       
        $row .= '<td class="quantities">' . 
        render_input($name_quantity, '', $quantity, 'number', $array_qty_attr, [], 'no-margin', $text_right_class) . 
        render_input($name_unit_name, '', $unit_name, 'text', ['placeholder' => _l('unit'), 'readonly' => true], [], 'no-margin', 'input-transparent text-right pur_input_none').
        '</td>';
        $row .= '<td class="into_money">' . $into_money . '</td>';
        
        $row .= '<td class="taxrate">' . $this->get_taxes_dropdown_template($name_tax_id_select, $invoice_item_taxes, 'invoice', $item_key, true, $manual) . '</td>';

        $row .= '<td class="tax_value">' . render_input($name_tax_value, '', $tax_value, 'number', $array_subtotal_attr, [], '', $text_right_class) . '</td>';

        $row .= '<td class="_total" align="right">' . $total . '</td>';

        if($discount_money > 0){
            $discount = '';
        }

        $row .= '<td class="discount">' . render_input($name_discount, '', $discount, 'number', $array_discount_attr, [], '', $text_right_class) . '</td>';
        $row .= '<td class="discount_money" align="right">' . render_input($name_discount_money, '', $discount_money, 'number', $array_discount_money_attr, [], '', $text_right_class.' item_discount_money') . '</td>';
        $row .= '<td class="label_total_after_discount" align="right">' . $total_money . '</td>';

        $row .= '<td class="hide commodity_code">' . render_input($name_item_code, '', $item_code, 'text', ['placeholder' => _l('commodity_code')]) . '</td>';
        $row .= '<td class="hide unit_id">' . render_input($name_unit_id, '', $unit_id, 'text', ['placeholder' => _l('unit_id')]) . '</td>';

        $row .= '<td class="hide _total_after_tax">' . render_input($name_total, '', $total, 'number', []) . '</td>';

        //$row .= '<td class="hide discount_money">' . render_input($name_discount_money, '', $discount_money, 'number', []) . '</td>';
        $row .= '<td class="hide total_after_discount">' . render_input($name_total_money, '', $total_money, 'number', []) . '</td>';
        $row .= '<td class="hide _into_money">' . render_input($name_into_money, '', $into_money, 'number', []) . '</td>';

        if ($name == '') {
            $row .= '<td><button type="button" onclick="pur_add_item_to_table(\'undefined\',\'undefined\'); return false;" class="btn pull-right btn-info"><i class="fa fa-check"></i></button></td>';
        } else {
            $row .= '<td><a href="#" class="btn btn-danger pull-right" onclick="pur_delete_item(this,' . $item_key . ',\'.invoice-item\'); return false;"><i class="fa fa-trash"></i></a></td>';
        }
        $row .= '</tr>';
        return $row;
    }

    /**
     * Creates a purchase order row template.
     *
     * @param      string      $name              The name
     * @param      string      $item_name         The item name
     * @param      string      $item_description  The item description
     * @param      int|string  $quantity          The quantity
     * @param      string      $unit_name         The unit name
     * @param      int|string  $unit_price        The unit price
     * @param      string      $taxname           The taxname
     * @param      string      $item_code         The item code
     * @param      string      $unit_id           The unit identifier
     * @param      string      $tax_rate          The tax rate
     * @param      string      $total_money       The total money
     * @param      string      $discount          The discount
     * @param      string      $discount_money    The discount money
     * @param      string      $total             The total
     * @param      string      $into_money        Into money
     * @param      string      $tax_id            The tax identifier
     * @param      string      $tax_value         The tax value
     * @param      string      $item_key          The item key
     * @param      bool        $is_edit           Indicates if edit
     *
     * @return     string      
     */
    public function create_purchase_order_row_template($name = '', $item_name = '', $item_description = '', $quantity = '', $unit_name = '', $unit_price = '', $taxname = '',  $item_code = '', $unit_id = '', $tax_rate = '', $total_money = '', $discount = '', $discount_money = '', $total = '', $into_money = '', $tax_id = '', $tax_value = '', $item_key = '',$is_edit = false, $currency_rate = 1, $to_currency = '') {
        
        $this->load->model('invoice_items_model');
        $row = '';

        $name_item_code = 'item_code';
        $name_item_name = 'item_name';
        $name_item_description = 'description';
        $name_unit_id = 'unit_id';
        $name_unit_name = 'unit_name';
        $name_quantity = 'quantity';
        $name_unit_price = 'unit_price';
        $name_tax_id_select = 'tax_select';
        $name_tax_id = 'tax_id';
        $name_total = 'total';
        $name_tax_rate = 'tax_rate';
        $name_tax_name = 'tax_name';
        $name_tax_value = 'tax_value';
        $array_attr = [];
        $array_attr_payment = ['data-payment' => 'invoice'];
        $name_into_money = 'into_money';
        $name_discount = 'discount';
        $name_discount_money = 'discount_money';
        $name_total_money = 'total_money';

        $array_available_quantity_attr = [ 'min' => '0.0', 'step' => 'any', 'readonly' => true];
        $array_qty_attr = [ 'min' => '0.0', 'step' => 'any'];
        $array_rate_attr = [ 'min' => '0.0', 'step' => 'any'];
        $array_discount_attr = [ 'min' => '0.0', 'step' => 'any'];
        $array_discount_money_attr = [ 'min' => '0.0', 'step' => 'any'];
        $str_rate_attr = 'min="0.0" step="any"';

        $array_subtotal_attr = ['readonly' => true];
        $text_right_class = 'text-right';

        if ($name == '') {
            $row .= '<tr class="main">
                  <td></td>';
            $vehicles = [];
            $array_attr = ['placeholder' => _l('unit_price')];
         
            $manual             = true;
            $invoice_item_taxes = '';
            $amount = '';
            $sub_total = 0;

        } else {
            $row .= '<tr class="sortable item">
                    <td class="dragger"><input type="hidden" class="order" name="' . $name . '[order]"><input type="hidden" class="ids" name="' . $name . '[id]" value="' . $item_key . '"></td>';
            $name_item_code = $name . '[item_code]';
            $name_item_name = $name . '[item_name]';
            $name_item_description = $name . '[item_description]';
            $name_unit_id = $name . '[unit_id]';
            $name_unit_name = '[unit_name]';
            $name_quantity = $name . '[quantity]';
            $name_unit_price = $name . '[unit_price]';
            $name_tax_id_select = $name . '[tax_select][]';
            $name_tax_id = $name . '[tax_id]';
            $name_total = $name . '[total]';
            $name_tax_rate = $name . '[tax_rate]';
            $name_tax_name = $name .'[tax_name]';
            $name_into_money = $name .'[into_money]';
            $name_discount = $name .'[discount]';
            $name_discount_money = $name .'[discount_money]';
            $name_total_money = $name . '[total_money]';
            $name_tax_value = $name. '[tax_value]';
      
           
            $array_qty_attr = ['onblur' => 'pur_calculate_total();', 'onchange' => 'pur_calculate_total();', 'min' => '0.0' , 'step' => 'any',  'data-quantity' => (float)$quantity];
            

            $array_rate_attr = ['onblur' => 'pur_calculate_total();', 'onchange' => 'pur_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('rate')];
            $array_discount_attr = ['onblur' => 'pur_calculate_total();', 'onchange' => 'pur_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('discount')];

            $array_discount_money_attr = ['onblur' => 'pur_calculate_total(1);', 'onchange' => 'pur_calculate_total(1);', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('discount')];


            $manual             = false;

            $tax_money = 0;
            $tax_rate_value = 0;

            if($is_edit){
                $invoice_item_taxes = pur_convert_item_taxes($tax_id, $tax_rate, $taxname);
                $arr_tax_rate = explode('|', $tax_rate ?? '');
                foreach ($arr_tax_rate as $key => $value) {
                    $tax_rate_value += (float)$value;
                }
            }else{
                $invoice_item_taxes = $taxname;
                $tax_rate_data = $this->pur_get_tax_rate($taxname);
                $tax_rate_value = $tax_rate_data['tax_rate'];
            }

            if((float)$tax_rate_value != 0){
                $tax_money = (float)$unit_price * (float)$quantity * (float)$tax_rate_value / 100;
                $goods_money = (float)$unit_price * (float)$quantity + (float)$tax_money;
                $amount = (float)$unit_price * (float)$quantity + (float)$tax_money;
            }else{
                $goods_money = (float)$unit_price * (float)$quantity;
                $amount = (float)$unit_price * (float)$quantity;
            }

            $sub_total = (float)$unit_price * (float)$quantity;
            $amount = app_format_number($amount);

        }
 

        $row .= '<td class="">' . render_textarea($name_item_name, '', $item_name, ['rows' => 2, 'placeholder' => _l('pur_item_name'), 'readonly' => true] ) . '</td>';

        $row .= '<td class="">' . render_textarea($name_item_description, '', $item_description, ['rows' => 2, 'placeholder' => _l('item_description')] ) . '</td>';

        $row .= '<td class="rate">' . render_input($name_unit_price, '', $unit_price, 'number', $array_rate_attr, [], 'no-margin', $text_right_class);

        if( $unit_price != ''){
            $original_price = ($currency_rate > 0) ? round( ($unit_price/$currency_rate), 2) : 0;
            $base_currency = get_base_currency();
            if($to_currency != 0 && $to_currency != $base_currency->id){
                $row .= render_input('original_price', '',app_format_money($original_price, $base_currency), 'text', ['data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => _l('original_price'), 'disabled' => true], [], 'no-margin', 'input-transparent text-right pur_input_none');
            }

            $row .= '<input class="hide" name="og_price" disabled="true" value="'.$original_price.'">';
        }
       
        $row .= '<td class="quantities">' . 
        render_input($name_quantity, '', $quantity, 'number', $array_qty_attr, [], 'no-margin', $text_right_class) . 
        render_input($name_unit_name, '', $unit_name, 'text', ['placeholder' => _l('unit'), 'readonly' => true], [], 'no-margin', 'input-transparent text-right pur_input_none').
        '</td>';
        
        $row .= '<td class="taxrate">' . $this->get_taxes_dropdown_template($name_tax_id_select, $invoice_item_taxes, 'invoice', $item_key, true, $manual) . '</td>';

        $row .= '<td class="tax_value">' . render_input($name_tax_value, '', $tax_value, 'number', $array_subtotal_attr, [], '', $text_right_class) . '</td>';

        $row .= '<td class="_total" align="right">' . $total . '</td>';

        if($discount_money > 0){
            $discount = '';
        }

        $row .= '<td class="discount">' . render_input($name_discount, '', $discount, 'number', $array_discount_attr, [], '', $text_right_class) . '</td>';
        $row .= '<td class="discount_money" align="right">' . render_input($name_discount_money, '', $discount_money, 'number', $array_discount_money_attr, [], '', $text_right_class.' item_discount_money') . '</td>';
        $row .= '<td class="label_total_after_discount" align="right">' . app_format_number($total_money) . '</td>';

        $row .= '<td class="hide commodity_code">' . render_input($name_item_code, '', $item_code, 'text', ['placeholder' => _l('commodity_code')]) . '</td>';
        $row .= '<td class="hide unit_id">' . render_input($name_unit_id, '', $unit_id, 'text', ['placeholder' => _l('unit_id')]) . '</td>';

        $row .= '<td class="hide _total_after_tax">' . render_input($name_total, '', $total, 'number', []) . '</td>';

        //$row .= '<td class="hide discount_money">' . render_input($name_discount_money, '', $discount_money, 'number', []) . '</td>';
        $row .= '<td class="hide total_after_discount">' . render_input($name_total_money, '', $total_money, 'number', []) . '</td>';
        $row .= '<td class="hide _into_money">' . render_input($name_into_money, '', $into_money, 'number', []) . '</td>';

        if ($name == '') {
            $row .= '<td><button type="button" onclick="pur_add_item_to_table(\'undefined\',\'undefined\'); return false;" class="btn pull-right btn-info"><i class="fa fa-check"></i></button></td>';
        } else {
            $row .= '<td><a href="#" class="btn btn-danger pull-right" onclick="pur_delete_item(this,' . $item_key . ',\'.invoice-item\'); return false;"><i class="fa fa-trash"></i></a></td>';
        }
        $row .= '</tr>';
        return $row;
    }

    /**
     * Gets the purchase request by vendor.
     *
     * @param        $vendorid  The vendorid
     */
    public function get_purchase_request_by_vendor($vendorid){
        $this->db->where('find_in_set('.$vendorid.', send_to_vendors)');
        $this->db->where('status', 2);
        return $this->db->get(db_prefix().'pur_request')->result_array();
    }

    /**
     * Gets the vendor item.
     *
     * @param        $vendorid  The vendorid
     *
     * @return       The vendor item.
     */
    public function get_vendor_item($vendorid){
        $this->db->where('vendor_id', $vendorid);
        return $this->db->get(db_prefix().'items_of_vendor')->result_array();
    }

    /**
     * Adds a vendor item.
     */
    public function add_vendor_item($data, $vendor_id){
        $data['vendor_id'] = $vendor_id;

        if(isset($data['attachments'])){
            unset($data['attachments']);
        }

        if($data['sku_code'] != ''){
            $data['sku_code'] = $data['sku_code'];
        }else{
            $data['sku_code'] = $this->create_vendor_item_sku_code('', '');
        }
        
        //update column unit name use sales/items
        $unit_type = get_unit_type_item($data['unit_id']);
        if($unit_type && !is_array($unit_type)){
            $data['unit'] = $unit_type->unit_name;
        }

        $this->db->insert(db_prefix().'items_of_vendor', $data);
        $insert_id = $this->db->insert_id();

        if($insert_id){
            return $insert_id;
        }
        return false;
    }

    /**
     * { update vendor item }
     *
     * @param        $data   The data
     * @param        $id     The identifier
     */
    public function update_vendor_item($data, $id){
        $unit_type = get_unit_type_item($data['unit_id']);
        if($unit_type && !is_array($unit_type)){
            $data['unit'] = $unit_type->unit_name;
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix().'items_of_vendor', $data);
        if ($this->db->affected_rows() > 0) { 

            $vendor_currency_id = get_vendor_currency(get_vendor_user_id());

            $base_currency = get_base_currency_pur();
            $vendor_currency = get_base_currency_pur();
            if($vendor_currency_id != 0){
                $vendor_currency = pur_get_currency_by_id($vendor_currency_id);
            }

            $convert_rate = 1;
            if($base_currency->name != $vendor_currency->name){
                $convert_rate = pur_get_currency_rate($vendor_currency->name, $base_currency->name);
            }

            $purchase_price = round(($data['rate'] * $convert_rate), 2);


            $data['purchase_price'] = $purchase_price;
            $data['rate'] = '';

            $this->db->where('from_vendor_item', $id);
            $this->db->update(db_prefix().'items', $data);

            return true;
        }
        return false;

    }


    /**
     * create sku code 
     * @param  int commodity_group 
     * @param  int sub_group 
     * @return string
     */
    public function  create_vendor_item_sku_code($commodity_group, $sub_group)
    {
        // input  commodity group, sub group
        //get commodity group from id
        $group_character = '';
        if(isset($commodity_group)){

            $sql_group_where = 'SELECT * FROM '.db_prefix().'items_groups where id = "'.$commodity_group.'"';
            $group_value = $this->db->query($sql_group_where)->row();
            if($group_value){

                if($group_value->commodity_group_code != ''){
                    $group_character = mb_substr($group_value->commodity_group_code, 0, 1, "UTF-8").'-';
                }
            }
        }
        //get sku code from sku id
        $sub_code = '';
        
        $sql_where = 'SELECT * FROM '.db_prefix().'items_of_vendor order by id desc limit 1';
        $last_commodity_id = $this->db->query($sql_where)->row();
        if($last_commodity_id){
            $next_commodity_id = (int)$last_commodity_id->id + 1;
        }else{
            $next_commodity_id = 1;
        }
        $commodity_id_length = strlen((string)$next_commodity_id);

        $commodity_str_betwen ='';

        $create_candidate_code='';

        switch ($commodity_id_length) {
            case 1:
                $commodity_str_betwen = '000';
                break;
            case 2:
                $commodity_str_betwen = '00';
                break;
            case 3:
                $commodity_str_betwen = '0';
                break;

            default:
                $commodity_str_betwen = '0';
                break;
        }
        return  $group_character.$sub_code.$commodity_str_betwen.$next_commodity_id; // X_X_000.id auto increment
    }

    public function get_item_of_vendor($item_id){
        $this->db->where('id', $item_id);
        return $this->db->get(db_prefix().'items_of_vendor')->row();
    }

    /**
     * { delete vendor item }
     *
     * @param        $item_id    The item identifier
     * @param        $vendor_id  The vendor identifier
     */
    public function delete_vendor_item($item_id, $vendor_id){
        $item = $this->get_item_of_vendor($item_id);
        if(!$item->vendor_id || $item->vendor_id != $vendor_id){
            return false;
        }

        $this->db->where('id', $item_id);
        $this->db->delete(db_prefix().'items_of_vendor');
        if($this->db->affected_rows() > 0){

            $this->db->where('rel_id',$item_id);
            $this->db->where('rel_type','vendor_items');
            $this->db->delete(db_prefix().'files');
            if ($this->db->affected_rows() > 0) {
                $affectedRows++;
            }

            if (is_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/vendor_items/'. $item_id)) {
                delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/vendor_items/'. $item_id);
            }

            return true;
        }
        return false;
    }

    /**
     * Gets the vendor item file.
     */
    public function get_vendor_item_file($item_id){
        $this->db->order_by('dateadded', 'desc');
        $this->db->where('rel_id', $item_id);
        $this->db->where('rel_type', 'vendor_items');

        return $this->db->get(db_prefix() . 'files')->result_array();
    }

    /**
     * { share vendor item }
     *
     * @param        $item_id  The item identifier
     */
    public function share_vendor_item($item_id){
        $item = $this->get_item_of_vendor($item_id);

        $vendor_currency_id = get_vendor_currency($item->vendor_id);

        $base_currency = get_base_currency_pur();
        $vendor_currency = get_base_currency_pur();
        if($vendor_currency_id != 0){
            $vendor_currency = pur_get_currency_by_id($vendor_currency_id);
        }

        $convert_rate = 1;
        if($base_currency->name != $vendor_currency->name){
            $convert_rate = pur_get_currency_rate($vendor_currency->name, $base_currency->name);
        }

        $purchase_price = round(($item->rate * $convert_rate), 2);

        $item_data['description'] = $item->description;
        $item_data['purchase_price'] = $purchase_price;
        $item_data['unit_id'] = $item->unit_id;
        $item_data['sku_code'] = $item->sku_code;
        $item_data['commodity_barcode'] = $item->commodity_barcode;
        $item_data['commodity_code'] = $item->commodity_code;
        $item_data['sku_name'] = $item->sku_name;
        $item_data['sub_group'] = $item->sub_group;
        $item_data['unit'] = $item->unit;
        $item_data['group_id'] = $item->group_id;
        $item_data['long_description'] = $item->long_description;
        $item_data['from_vendor_item'] = $item->id;
        $item_data['rate'] = '';
        $item_data['tax'] = $item->tax;
        $item_data['tax2'] = $item->tax2;

        $item_id_rs = $this->add_commodity_one_item($item_data);

        if($item_id){
            $this->db->insert(db_prefix().'pur_vendor_items', [
                'vendor' => $item->vendor_id,
                'items' => $item_id_rs,
                'datecreate' => date('Y-m-d'),
                'add_from' => 0
            ]);

            $this->db->where('id', $item_id);
            $this->db->update(db_prefix().'items_of_vendor', ['share_status' => 1]);

            return true;
        }

        return false;
    }

    /**
     * Adds a payment on po.
     *
     * @param        $data      The data
     * @param        $purorder  The purorder
     */
    public function add_payment_on_po($data, $purorder){
        $pur_order = $this->get_pur_order($purorder);

        if(!$purorder){
            return false;
        }

        $inv_data = [];

        $prefix = get_purchase_option('pur_inv_prefix');
        $next_number = get_purchase_option('next_inv_number');

        $inv_data['invoice_number'] = $prefix.str_pad($next_number,5,'0',STR_PAD_LEFT);
        $inv_data['number'] = $next_number;

        $this->db->where('invoice_number',$inv_data['invoice_number']);
        $check_exist_number = $this->db->get(db_prefix().'pur_invoices')->row();

        while($check_exist_number) {
          $inv_data['number'] = $inv_data['number'] + 1;
          $inv_data['invoice_number'] =  $prefix.str_pad($inv_data['number'],5,'0',STR_PAD_LEFT);
          $this->db->where('invoice_number',$inv_data['invoice_number']);
          $check_exist_number = $this->db->get(db_prefix().'pur_invoices')->row();
        }

        $pur_order_detail = $this->get_pur_order_detail($purorder);

        $inv_data['add_from'] = get_staff_user_id();
        $inv_data['add_from_type'] = 'admin';
        $inv_data['vendor'] = $pur_order->vendor;
        $inv_data['subtotal'] = $pur_order->subtotal;
        $inv_data['tax'] = $pur_order->total_tax;
        $inv_data['total'] = $pur_order->total;
        $inv_data['discount_percent'] = $pur_order->discount_percent;
        $inv_data['discount_total'] = $pur_order->discount_total;
        $inv_data['transaction_date'] = date('Y-m-d');
        $inv_data['invoice_date'] = date('Y-m-d');
        $inv_data['duedate'] = to_sql_date($data['date']);
        $inv_data['payment_status'] = 'unpaid';
        $inv_data['date_add'] = date('Y-m-d');
        $inv_data['pur_order'] = $purorder;
        $inv_data['discount_type'] = $pur_order->discount_type;
        $inv_data['currency'] = isset($pur_order->currency) ? $pur_order->currency : get_vendor_currency($pur_order->vendor);

        $this->db->insert(db_prefix().'pur_invoices', $inv_data);
        $insert_id = $this->db->insert_id();
        if($insert_id){
            $next_number = $inv_data['number']+1;
            $this->db->where('option_name', 'next_inv_number');
            $this->db->update(db_prefix() . 'purchase_option',['option_val' =>  $next_number,]);

            if(count($pur_order_detail) > 0){
                foreach($pur_order_detail as $order_detail){
                    $inv_detail_data = [];
                    $inv_detail_data['pur_invoice'] = $insert_id;
                    $inv_detail_data['item_code'] = $order_detail['item_code'];
                    $inv_detail_data['description'] = $order_detail['description'];
                    $inv_detail_data['unit_id'] = $order_detail['unit_id'];
                    $inv_detail_data['unit_price'] = $order_detail['unit_price'];
                    $inv_detail_data['quantity'] = $order_detail['quantity'];
                    $inv_detail_data['into_money'] = $order_detail['into_money'];
                    $inv_detail_data['tax'] = $order_detail['tax'];
                    $inv_detail_data['total'] = $order_detail['total'];
                    $inv_detail_data['discount_percent'] = $order_detail['discount_%'];
                    $inv_detail_data['discount_money'] = $order_detail['discount_money'];
                    $inv_detail_data['total_money'] = $order_detail['total_money'];
                    $inv_detail_data['tax_value'] = $order_detail['tax_value'];
                    $inv_detail_data['tax_rate'] = $order_detail['tax_rate'];
                    $inv_detail_data['tax_name'] = $order_detail['tax_name'];
                    $inv_detail_data['item_name'] = $order_detail['item_name'];

                    $this->db->insert(db_prefix().'pur_invoice_details', $inv_detail_data);

                }
            }

            $payment_id = $this->add_invoice_payment($data, $insert_id);
            if($payment_id){
                return $payment_id;
            }
            return false;
        }

        return false;

    }

    /**
     * check auto create currency rate
     * @return [type]
     */
    public function check_auto_create_currency_rate() {
        $this->load->model('currencies_model');
        $currency_rates = $this->get_currency_rate();
        $currencies = $this->currencies_model->get();
        $currency_generator = $this->currency_generator($currencies);

        foreach ($currency_rates as $key => $currency_rate) {
            if (isset($currency_generator[$currency_rate['from_currency_id'] . '_' . $currency_rate['to_currency_id']])) {
                unset($currency_generator[$currency_rate['from_currency_id'] . '_' . $currency_rate['to_currency_id']]);
            }
        }

        //if have API, will get currency rate from here
        if (count($currency_generator) > 0) {
            $this->db->insert_batch(db_prefix() . 'currency_rates', $currency_generator);
        }

        return true;
    }

    /**
     * currency generator
     * @param  $variants
     * @param  integer $i
     * @return 
     */
    public function currency_generator($currencies) {

        $currency_rates = [];

        foreach ($currencies as $key_1 => $value_1) {
            foreach ($currencies as $key_2 => $value_2) {
                if ($value_1['id'] != $value_2['id']) {
                    $currency_rates[$value_1['id'] . '_' . $value_2['id']] = [
                        'from_currency_id' => $value_1['id'],
                        'from_currency_name' => $value_1['name'],
                        'from_currency_rate' => 1,
                        'to_currency_id' => $value_2['id'],
                        'to_currency_name' => $value_2['name'],
                        'to_currency_rate' => 0,
                        'date_updated' => date('Y-m-d H:i:s'),
                    ];
                }

            }
        }

        return $currency_rates;
    }

    /**
     * get currency rate
     * @param  boolean $id
     * @return [type]
     */
    public function get_currency_rate($id = false) {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'currency_rates')->row();
        }

        if ($id == false) {
            return $this->db->query('select * from ' . db_prefix() . 'currency_rates')->result_array();
        }
    }

    /**
     * update currency rate setting
     *
     * @param      array   $data   The data
     *
     * @return     boolean
     */
    public function update_setting_currency_rate($data) {
        $affectedRows = 0;
        if (!isset($data['cr_automatically_get_currency_rate'])) {
            $data['cr_automatically_get_currency_rate'] = 0;
        }

        foreach ($data as $key => $value) {
            $this->db->where('name', $key);
            $this->db->update(db_prefix() . 'options', [
                'value' => $value,
            ]);
            if ($this->db->affected_rows() > 0) {
                $affectedRows++;
            }
        }

        if ($affectedRows > 0) {
            return true;
        }
        return false;
    }

    /**
     * Gets the currency rate online.
     *
     * @param        $id     The identifier
     *
     * @return     bool    The currency rate online.
     */
    public function get_currency_rate_online($id) {
        $currency_rate = $this->get_currency_rate($id);

        if ($currency_rate) {
            return $this->currency_converter($currency_rate->from_currency_name, $currency_rate->to_currency_name);
        }

        return false;
    }

    /**
     * Gets all currency rate online.
     *
     * @return     bool  All currency rate online.
     */
    public function get_all_currency_rate_online() {
        $currency_rates = $this->get_currency_rate();
        $affectedRows = 0;
        foreach ($currency_rates as $currency_rate) {
            $rate = $this->currency_converter($currency_rate['from_currency_name'], $currency_rate['to_currency_name']);

            $data_update = ['to_currency_rate' => $rate];
            $success = $this->update_currency_rate($data_update, $currency_rate['id']);

            if ($success) {
                $affectedRows++;
            }
        }

        if ($affectedRows > 0) {
            return true;
        }

        return true;
    }

    /**
     * update currency rate
     * @param  [type] $data
     * @return [type]
     */
    public function update_currency_rate($data, $id) {

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'currency_rates', ['to_currency_rate' => $data['to_currency_rate'], 'date_updated' => date('Y-m-d H:i:s')]);
        if ($this->db->affected_rows() > 0) {
            $this->db->where('id', $id);
            $current_rate = $this->db->get(db_prefix() . 'currency_rates')->row();

            $data_log['from_currency_id'] = $current_rate->from_currency_id;
            $data_log['from_currency_name'] = $current_rate->from_currency_name;
            $data_log['to_currency_id'] = $current_rate->to_currency_id;
            $data_log['to_currency_name'] = $current_rate->to_currency_name;
            $data_log['from_currency_rate'] = isset($data['from_currency_rate']) ? $data['from_currency_rate'] : '';
            $data_log['to_currency_rate'] = isset($data['to_currency_rate']) ? $data['to_currency_rate'] : '';
            $data_log['date'] = date('Y-m-d H:i:s');
            $this->db->insert(db_prefix() . 'currency_rate_logs', $data_log);
            return true;
        }
        return false;
    }

     /**
     * [currency_converter description]
     * @param  string $from   Currency Code
     * @param  string $to     Currency Code
     * @param  float $amount
     * @return float        
     */
    public function currency_converter($from,$to,$amount = 1)
    {   
        $url = "https://www.google.com/finance/quote/$from-$to";
        $response = $this->api_get($url);
        $string1 = explode('class="YMlKec fxKbKc">', $response);

        if(isset($string1[1])){

            $rate = explode('</div>', $string1[1]);

            if(isset($rate[0])){
                $result = $rate[0] * $amount;
                
                return $result;
            }
        }

        return false;
    }

    /**
     * api get
     * @param  string $url
     * @return string
     */
    public function api_get($url) {
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 120);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 120);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);

        return curl_exec($curl);
    }

    /**
     * delete currency rate
     * @param  [type] $id
     * @return [type]
     */
    public function delete_currency_rate($id) {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'currency_rates');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * { cronjob currency rates }
     *
     * @param        $manually  The manually
     *
     * @return     bool    
     */
    public function cronjob_currency_rates($manually) {
        $currency_rates = $this->get_currency_rate();
        foreach ($currency_rates as $currency_rate) {
            $data_insert = $currency_rate;
            $data_insert['date'] = date('Y-m-d');
            unset($data_insert['date_updated']);
            unset($data_insert['id']);

            $this->db->insert(db_prefix() . 'currency_rate_logs', $data_insert);
        }

        if (get_option('cr_automatically_get_currency_rate') == 1) {
            $this->get_all_currency_rate_online();
        }

        $asm_global_amount_expiration = get_option('cr_global_amount_expiration');
        if ($asm_global_amount_expiration != 0 && $asm_global_amount_expiration != '') {
            $this->db->where('date < "' . date('Y-m-d', strtotime(date('Y-m-d') . ' - ' . $asm_global_amount_expiration . ' days')).'"');
            $this->db->delete(db_prefix() . 'currency_rate_logs');
        }
        update_option('cr_date_cronjob_currency_rates', date('Y-m-d'));

        return true;
    }

    /**
     * Gets the invoices by po.
     */
    public function get_invoices_by_po($po_id){
        $this->db->where('pur_order', $po_id);
        return $this->db->get(db_prefix().'pur_invoices')->result_array();
    }

    /**
     * Adds a payment on po with inv.
     *
     * @param        $data   The data
     *
     * @return     bool    ( description_of_the_return_value )
     */
    public function add_payment_on_po_with_inv($data){
        $invoice = $data['pur_invoice'];
        unset($data['pur_invoice']);

        $payment_id = $this->add_invoice_payment($data, $invoice);

        if($payment_id){
            return true;
        }
        return false;
    }

    /**
     * { confirm order }
     */
    public function confirm_order($pur_order){
        $this->db->where('id', $pur_order);
        $this->db->update(db_prefix().'pur_orders', [ 'order_status' =>  'confirmed']);
        if ($this->db->affected_rows() > 0) {

            return true;
        }

    }

    /**
     * Gets the pur order files.
     *
     * @param        $pur_order  The pur order
     */
    public function get_pur_order_files($pur_order){
        $this->db->where('rel_id', $pur_order);
        $this->db->where('rel_type', 'pur_order');
        return $this->db->get(db_prefix().'files')->result_array();
    }

    public function create_order_return_row_template($rel_type, $rel_type_detail_id = '', $name = '', $commodity_name = '', $quantities = '', $unit_name = '', $unit_price = '', $taxname = '',  $commodity_code = '', $unit_id = '', $tax_rate = '', $total_amount = '', $discount = '', $discount_total = '', $total_after_discount = '', $reason_return = '', $sub_total = '', $tax_name = '', $tax_id = '', $item_key = '',$is_edit = false, $max_qty = false, $return_type = 'fully') {
        
        $this->load->model('invoice_items_model');
        $row = '';

        $name_commodity_code = 'commodity_code';
        $name_commodity_name = 'commodity_name';
        $name_unit_id = 'unit_id';
        $name_unit_name = 'unit_name';
        $name_quantities = 'quantity';
        $name_unit_price = 'unit_price';
        $name_tax_id_select = 'tax_select';
        $name_tax_id = 'tax_id';
        $name_total_amount = 'total_amount';
        $name_note = 'note';
        $name_tax_rate = 'tax_rate';
        $name_tax_name = 'tax_name';
        $array_attr = [];
        $array_attr_payment = ['data-payment' => 'invoice'];
        $name_sub_total = 'sub_total';
        $name_discount = 'discount';
        $name_discount_total = 'discount_total';
        $name_total_after_discount = 'total_after_discount';
        $name_rel_type_detail_id = 'rel_type_detail_id';
        $name_reason_return = 'reason_return';

        $array_qty_attr = [ 'min' => '0.0', 'step' => 'any'];
        $array_rate_attr = [ 'min' => '0.0', 'step' => 'any'];
        $array_discount_attr = [ 'min' => '0.0', 'step' => 'any'];
        $str_rate_attr = 'min="0.0" step="any"';


        if ($name == '') {
            if($rel_type == 'manual'){
                $row .= '<tr class="main">
                <td></td>';
            }else{
                $row .= '<tr class="main hide">
                <td></td>';
            }

            $vehicles = [];
            $array_attr = ['placeholder' => _l('unit_price')];
            $warehouse_id_name_attr = [];
            $manual             = true;
            $invoice_item_taxes = '';
            $amount = '';
            $sub_total = 0;

        } else {
            $row .= '<tr class="sortable item">
                    <td class="dragger"><input type="hidden" class="order" name="' . $name . '[order]"><input type="hidden" class="ids" name="' . $name . '[id]" value="' . $item_key . '"></td>';
            $name_commodity_code = $name . '[commodity_code]';
            $name_commodity_name = $name . '[commodity_name]';
            $name_unit_id = $name . '[unit_id]';
            $name_unit_name = '[unit_name]';
            $name_quantities = $name . '[quantity]';
            $name_unit_price = $name . '[unit_price]';
            $name_tax_id_select = $name . '[tax_select][]';
            $name_tax_id = $name . '[tax_id]';
            $name_total_amount = $name . '[total_amount]';
            $name_note = $name . '[note]';
            $name_tax_rate = $name . '[tax_rate]';
            $name_tax_name = $name .'[tax_name]';
            $name_sub_total = $name .'[sub_total]';
            $name_discount = $name .'[discount]';
            $name_discount_total = $name .'[discount_total]';
            $name_total_after_discount = $name .'[total_after_discount]';
            $name_rel_type_detail_id = $name .'[rel_type_detail_id]';
            $name_reason_return = $name .'[reason_return]';

            if($rel_type == 'sales_return_order'){
                if($max_qty){
                    $array_qty_attr = ['onblur' => 'pur_sale_order_calculate_total();', 'onchange' => 'pur_sale_order_calculate_total();', 'min' => '0.0' , 'max' => (float)$max_qty, 'step' => 'any',  'data-quantity' => (float)$quantities];
                }else{
                    $array_qty_attr = ['onblur' => 'pur_sale_order_calculate_total();', 'onchange' => 'pur_sale_order_calculate_total();', 'min' => '0.0' , 'step' => 'any',  'data-quantity' => (float)$quantities];
                }

                $array_rate_attr = ['onblur' => 'pur_sale_order_calculate_total();', 'onchange' => 'pur_sale_order_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('rate'), 'readonly' => true];
                $array_discount_attr = ['onblur' => 'pur_sale_order_calculate_total();', 'onchange' => 'pur_sale_order_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('discount'), 'readonly' => true];

            }else{

                if($max_qty){
                    if($return_type == 'fully'){
                        $array_qty_attr = ['onblur' => 'pur_calculate_total();', 'onchange' => 'pur_calculate_total();', 'min' => '0.0' , 'max' => (float)$max_qty, 'step' => 'any',  'data-quantity' => (float)$quantities, 'readonly' => true];
                    }else{
                        $array_qty_attr = ['onblur' => 'pur_calculate_total();', 'onchange' => 'pur_calculate_total();', 'min' => '0.0' , 'max' => (float)$max_qty, 'step' => 'any',  'data-quantity' => (float)$quantities];
                    }
                    
                }else{
                    if($return_type == 'fully'){
                        $array_qty_attr = ['onblur' => 'pur_calculate_total();', 'onchange' => 'pur_calculate_total();', 'min' => '0.0' , 'step' => 'any',  'data-quantity' => (float)$quantities, 'readonly' => true];
                    }else{
                        $array_qty_attr = ['onblur' => 'pur_calculate_total();', 'onchange' => 'pur_calculate_total();', 'min' => '0.0' , 'step' => 'any',  'data-quantity' => (float)$quantities];
                    }
                }

                $array_rate_attr = ['onblur' => 'pur_calculate_total();', 'onchange' => 'pur_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('rate'), 'readonly' => true];
                $array_discount_attr = ['onblur' => 'pur_calculate_total();', 'onchange' => 'pur_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('discount'), 'readonly' => true];
            }


            $manual             = false;

            $tax_money = 0;
            $tax_rate_value = 0;

            if($is_edit){
                $invoice_item_taxes = pur_convert_item_taxes($tax_id, $tax_rate, $taxname);
                $arr_tax_rate = explode('|', $tax_rate ?? '');
                foreach ($arr_tax_rate as $key => $value) {
                    $tax_rate_value += (float)$value;
                }
            }else{
                $invoice_item_taxes = $taxname;
                $tax_rate_data = $this->pur_get_tax_rate($taxname);
                $tax_rate_value = $tax_rate_data['tax_rate'];
            }

            if((float)$tax_rate_value != 0){
                $tax_money = (float)$unit_price * (float)$quantities * (float)$tax_rate_value / 100;
                $goods_money = (float)$unit_price * (float)$quantities + (float)$tax_money;
                $amount = (float)$unit_price * (float)$quantities;
            }else{
                $goods_money = (float)$unit_price * (float)$quantities;
                $amount = (float)$unit_price * (float)$quantities;
            }

            $sub_total = (float)$unit_price * (float)$quantities;
            $amount = app_format_number($amount);

        }

        $row .= '<td class="">' . render_textarea($name_commodity_name, '', $commodity_name, ['rows' => 2, 'placeholder' => _l('item_description_placeholder'), 'readonly' => true] ) . '</td>';

        $row .= '<td class="quantities">' . 
        render_input($name_quantities, '', $quantities, 'number', $array_qty_attr, [], 'no-margin').
        render_input($name_unit_name, '', $unit_name, 'text', ['placeholder' => _l('unit'), 'readonly' => true], [], 'no-margin', 'input-transparent text-right wh_input_none').
         '</td>';

        $row .= '<td class="rate">' . render_input($name_unit_price, '', $unit_price, 'number', $array_rate_attr) . '</td>';
        $row .= '<td class="amount" align="right">' . $amount . '</td>';
        $row .= '<td class="taxrate">' . $this->get_taxes_dropdown_template_readonly($name_tax_id_select, $invoice_item_taxes, 'invoice', $item_key, true, $manual) . '</td>';
        $row .= '<td class="hide">' . $this->get_taxes_dropdown_template($name_tax_id_select, $invoice_item_taxes, 'invoice', $item_key, true, $manual) . '</td>';

        
        $row .= '<td class="discount">' . render_input($name_discount, '', $discount, 'number', $array_discount_attr) . '</td>';
        $row .= '<td class="label_discount_money" align="right">' . $discount_total . '</td>';
        $row .= '<td class="label_total_after_discount" align="right">' . $amount . '</td>';

        $row .= '<td class="hide commodity_code">' . render_input($name_commodity_code, '', $commodity_code, 'text', ['placeholder' => _l('commodity_code')]) . '</td>';
        $row .= '<td class="hide unit_id">' . render_input($name_unit_id, '', $unit_id, 'text', ['placeholder' => _l('unit_id')]) . '</td>';
        $row .= '<td class="hide discount_money">' . render_input($name_discount_total, '', $discount_total, 'number', []) . '</td>';
        $row .= '<td class="hide total_after_discount">' . render_input($name_total_after_discount, '', $total_after_discount, 'number', []) . '</td>';
        $row .= '<td class="hide">' . render_input($name_rel_type_detail_id, '', $rel_type_detail_id, 'number') . '</td>';
        $row .= '<td class="hide">' . render_textarea($name_reason_return, '', $reason_return, ['rows' => 2, 'placeholder' => _l('item_reason_return')] ) . '</td>';


        if($rel_type == 'sales_return_order'){
            if ($name == '') {
                $row .= '<td><button type="button" onclick="wh_sales_order_add_item_to_table(\'undefined\',\'undefined\'); return false;" class="btn pull-right btn-info"><i class="fa fa-check"></i></button></td>';
            } else {
                $row .= '<td><a href="#" class="btn btn-danger pull-right" onclick="wh_sales_order_delete_item(this,' . $item_key . ',\'.invoice-item\'); return false;"><i class="fa fa-trash"></i></a></td>';
            }
        }else{
            if ($name == '') {
                $row .= '<td><button type="button" onclick="wh_add_item_to_table(\'undefined\',\'undefined\'); return false;" class="btn pull-right btn-info"><i class="fa fa-check"></i></button></td>';
            } else {
                if($return_type == 'fully'){
                    $row .= '<td><a href="#" disabled="true" class="btn btn-danger delete-item-order pull-right" onclick="wh_delete_item(this,' . $item_key . ',\'.invoice-item\'); return false;"><i class="fa fa-trash"></i></a></td>';
                }else{
                    $row .= '<td><a href="#" class="btn btn-danger delete-item-order pull-right" onclick="wh_delete_item(this,' . $item_key . ',\'.invoice-item\'); return false;"><i class="fa fa-trash"></i></a></td>';
                }
            }
        }

        $row .= '</tr>';
        return $row;
    }

    /**
     * Gets the pur order for order return.
     */
    public function get_pur_order_for_order_return(){


        $this->db->where('delivery_status', 1);
        $this->db->where('order_status', 'delivered');

        if(!has_permission('purchase_orders', '', 'view') && is_staff_logged_in()){
            $this->db->where(' (' . db_prefix() . 'pur_orders.addedfrom = '.get_staff_user_id().' OR ' . db_prefix() . 'pur_orders.buyer = '.get_staff_user_id().' OR ' . db_prefix() . 'pur_orders.vendor IN (SELECT vendor_id FROM ' . db_prefix() . 'pur_vendor_admin WHERE staff_id=' . get_staff_user_id() . '))');
        }

        $pur_orders = $this->db->get(db_prefix().'pur_orders')->result_array();

        foreach($pur_orders as $key => $order){
            $vendor = $this->get_vendor($order['vendor']);
            $within_day = get_option('pur_return_request_within_x_day');
            if($vendor && $vendor->return_within_day != null && $vendor->return_within_day != 0){
                $within_day = $vendor->return_within_day;
            }

            if($order['delivery_date'] == null || $order['delivery_date'] == '' || ($order['delivery_date'] != '' &&  date('Y-m-d', strtotime('+'.$within_day.' days', strtotime($order['delivery_date']))) < date('Y-m-d')) ) {
                unset($pur_orders[$key]);
            }
        }

        return $pur_orders;
    }

    /**
     * omni sale detail order return
     * @param  [type] $id 
     * @return [type]              
     */
    public function pur_order_detail_order_return($id, $return_type = 'fully') {

        $company_id = '';
        $email = '';
        $phonenumber = '';
        $order_number = '';
        $order_date = '';
        $number_of_item = '';
        $order_total = '';
        $datecreated = '';
        $main_additional_discount = 0;
        $additional_discount = 0;
        $row_template = '';
        $pur_order = $this->get_pur_order($id);
        if($pur_order){
            $company_id = $pur_order->vendor;
            $vendor = $this->get_vendor($company_id);
            $contacts = $this->get_contacts($company_id);
            if(count($contacts) > 0){
                $email = $contacts[0]['email'];
            }
            $phonenumber = $vendor->phonenumber;
            $order_number = $pur_order->pur_order_number;
            $order_date = $pur_order->datecreated;
            $order_total = $pur_order->total;
            $datecreated = date('Y-m-d H-i-s');
            $main_additional_discount = $pur_order->discount_total;
            $additional_discount = $pur_order->discount_total;
            $row_template = '';
            $count_item = 0;
            $order_detail_data = $this->get_pur_order_detail($id);
            foreach ($order_detail_data as $key => $row) {   
                $count_item++;
                $unit_name = '';
                $tax_id = '';
                $unit_id = '';
                $commodity_code = '';
                $item = $this->get_product($row['item_code']);

                if($item){
                    $tax_name = '';
                    $taxrate = '';
                    $tax = $this->get_tax_info_by_product($id);
                    if($tax){
                        $tax_id = $tax->id;
                    }
                    $commodity_code = $item->id;
                }

                $data_unit = get_unit_type_item($row['unit_id']);
                if($data_unit){
                    $unit_name = $data_unit->unit_name;
                }   


                $taxname = $row['tax_name'];
                $tax_rate = $row['tax_rate'];
                $total_amount = $row['quantity'] * $row['unit_price'];
                $discount = $row['discount_%'];
                $discount_total = $row['discount_money'];
                $total_after_discount = '';
                $sub_total = '';
                $tax_name = $row['tax'];
                $tax_id = $row['tax_value'];
                $row_template .= $this->create_order_return_row_template('purchasing_return_order', $row['id'], 'newitems['.$row['id'].']', $row['item_name'], $row['quantity'], $unit_name, $row['unit_price'], $taxname,  $commodity_code, $unit_id, $tax_rate, $total_amount, $discount, $discount_total, $total_after_discount, '', $sub_total, $tax_name, $tax_id, $row['id'], true, false, $return_type);
            }
            $number_of_item = $count_item;

        }
        $data['company_id'] = $company_id;
        $data['email'] = $email;
        $data['phonenumber'] = $phonenumber;
        $data['order_number'] = $order_number;
        $data['order_date'] = $order_date;
        $data['number_of_item'] = $number_of_item;
        $data['order_total'] = $order_total;
        $data['datecreated'] = $datecreated;
        $data['main_additional_discount'] = $main_additional_discount;
        $data['additional_discount'] = $additional_discount;
        $data['result'] = $row_template;
        return $data;
    }

    /**
     *  get product   
     * @param  int $id    
     * @return  object or array object       
     */
    public function get_product($id = ''){
        if($id != ''){
            $this->db->select(db_prefix() . 'ware_unit_type.unit_name'.','.db_prefix() . 'items.*');
            $this->db->join(db_prefix() . 'ware_unit_type', db_prefix() . 'ware_unit_type.unit_type_id=' . db_prefix() . 'items.unit_id', 'left');
            $this->db->where('id',$id);
            return $this->db->get(db_prefix().'items')->row();
        }
        else{     
            return $this->db->get(db_prefix().'items')->result_array();
        }
    }

    /**
     * get tax info by product
     * @return  object $tax           
     */
    public function get_tax_info_by_product($id_product){
        if($id_product!=''){
            $product = $this->get_product($id_product);
            if($product)
            {
                if($product->tax != '' && $product->tax)
                {
                    $this->db->where('id', $product->tax);              
                    return $this->db->get(db_prefix().'taxes')->row();
                }
            }
        }
    }

    /**
     * create order return code
     * @return [type] 
     */
    public function create_order_return_code()
    {
        $goods_code = get_purchase_option_v2('pur_order_return_number_prefix') . (get_purchase_option_v2('next_pur_order_return_number'));
        return $goods_code;
    }

    /**
     * [add add order return
     * @param [type] $data     
     * @param [type] $rel_type 
     */
    public function add_order_return($data, $rel_type)
    {
        $order_return_details = [];
        if (isset($data['newitems'])) {
            $order_return_details = $data['newitems'];
            unset($data['newitems']);
        }

        unset($data['item_select']);
        unset($data['commodity_name']);
        unset($data['quantity']);
        unset($data['unit_price']);
        unset($data['unit_name']);
        unset($data['commodity_code']);
        unset($data['unit_id']);
        unset($data['discount']);
        unset($data['tax_rate']);
        unset($data['tax_name']);
        unset($data['rel_type_detail_id']);
        unset($data['item_reason_return']);
        unset($data['reason_return']);
        if(isset($data['save_and_send_request'])){
            unset($data['save_and_send_request']);
        }

        if(isset($data['main_additional_discount'])){
            unset($data['main_additional_discount']);
        }
        
        $check_appr = $this->get_approve_setting('order_return');
        $data['approval'] = 0;
        if ($check_appr && $check_appr != false) {
            $data['approval'] = 0;
        } else {
            $data['approval'] = 1;
        }

        if(isset($data['edit_approval'])){
            unset($data['edit_approval']);
        }

        $purchase_order = $this->get_pur_order($data['rel_id']);
        $data['currency'] = $purchase_order->currency;

        $data['status'] = 'draft';
        $data['order_return_number'] = $this->create_order_return_code();
        $data['total_amount']   = $data['total_amount'];
        $data['discount_total'] = $data['discount_total'];
        $data['total_after_discount'] = $data['total_after_discount'];
        $data['staff_id'] = get_staff_user_id();
 
        $data['datecreated'] = to_sql_date($data['datecreated'], true);

        if($data['order_date'] != null){
            $data['order_date'] = to_sql_date($data['order_date'], true);
        }

        if(isset($data['order_discount'])){
            unset($data['order_discount']);
        } 

        $data['return_policies_information'] = get_option('return_policies_information');
        $this->db->insert(db_prefix() . 'wh_order_returns', $data);
        $insert_id = $this->db->insert_id();

        /*update save note*/
        if (isset($insert_id)) {
            $this->db->where('id', $data['rel_id']);
            $this->db->update(db_prefix().'pur_orders', ['order_status' => 'return']);

            if($rel_type == 'manual'){
                //CASE: add manual
                foreach ($order_return_details as $order_return_detail) {
                    $order_return_detail['order_return_id'] = $insert_id;

                    $tax_money = 0;
                    $tax_rate_value = 0;
                    $tax_rate = null;
                    $tax_id = null;
                    $tax_name = null;
                    if(isset($order_return_detail['tax_select'])){
                        $tax_rate_data = $this->pur_get_tax_rate($order_return_detail['tax_select']);
                        $tax_rate_value = $tax_rate_data['tax_rate'];
                        $tax_rate = $tax_rate_data['tax_rate_str'];
                        $tax_id = $tax_rate_data['tax_id_str'];
                        $tax_name = $tax_rate_data['tax_name_str'];
                    }

                    if((float)$tax_rate_value != 0){
                        $tax_money = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'] * (float)$tax_rate_value / 100;
                        $total_money = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'] + (float)$tax_money;
                        $amount = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'] + (float)$tax_money;
                    }else{
                        $total_money = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'];
                        $amount = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'];
                    }

                    $sub_total = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'];

                    $order_return_detail['tax_id'] = $tax_id;
                    $order_return_detail['total_amount'] = $total_money;
                    $order_return_detail['tax_rate'] = $tax_rate;
                    $order_return_detail['sub_total'] = $sub_total;
                    $order_return_detail['tax_name'] = $tax_name;

                    unset($order_return_detail['order']);
                    unset($order_return_detail['id']);
                    unset($order_return_detail['tax_select']);
                    unset($order_return_detail['unit_name']);

                    $this->db->insert(db_prefix() . 'wh_order_return_details', $order_return_detail);
                }
            }elseif($rel_type == 'purchasing_return_order'){
                //CASE: add from Purchase order - Purchase

                foreach ($order_return_details as $order_return_detail) {
                    $order_return_detail['order_return_id'] = $insert_id;

                    $tax_money = 0;
                    $tax_rate_value = 0;
                    $tax_rate = null;
                    $tax_id = null;
                    $tax_name = null;
                    if(isset($order_return_detail['tax_select'])){
                        $tax_rate_data = $this->pur_get_tax_rate($order_return_detail['tax_select']);
                        $tax_rate_value = $tax_rate_data['tax_rate'];
                        $tax_rate = $tax_rate_data['tax_rate_str'];
                        $tax_id = $tax_rate_data['tax_id_str'];
                        $tax_name = $tax_rate_data['tax_name_str'];
                    }

                    if((float)$tax_rate_value != 0){
                        $tax_money = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'] * (float)$tax_rate_value / 100;
                        $total_money = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'] + (float)$tax_money;
                        $amount = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'] + (float)$tax_money;
                    }else{
                        $total_money = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'];
                        $amount = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'];
                    }

                    $sub_total = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'];

                    $order_return_detail['tax_id'] = $tax_id;
                    $order_return_detail['total_amount'] = $total_money;
                    $order_return_detail['tax_rate'] = $tax_rate;
                    $order_return_detail['sub_total'] = $sub_total;
                    $order_return_detail['tax_name'] = $tax_name;
                    $order_return_detail['rel_type_detail_id'] = $data['rel_id'];

                    unset($order_return_detail['order']);
                    unset($order_return_detail['id']);
                    unset($order_return_detail['tax_select']);
                    unset($order_return_detail['unit_name']);

                    $this->db->insert(db_prefix() . 'wh_order_return_details', $order_return_detail);
                }
                
            }elseif($rel_type == 'sales_return_order'){
                //CASE: add from Sales order - Omni sale
                foreach ($order_return_details as $order_return_detail) {
                    $order_return_detail['order_return_id'] = $insert_id;

                    $tax_money = 0;
                    $tax_rate_value = 0;
                    $tax_rate = null;
                    $tax_id = null;
                    $tax_name = null;
                    if(isset($order_return_detail['tax_select'])){
                        $tax_rate_data = $this->pur_get_tax_rate($order_return_detail['tax_select']);
                        $tax_rate_value = $tax_rate_data['tax_rate'];
                        $tax_rate = $tax_rate_data['tax_rate_str'];
                        $tax_id = $tax_rate_data['tax_id_str'];
                        $tax_name = $tax_rate_data['tax_name_str'];
                    }

                    if((float)$tax_rate_value != 0){
                        $tax_money = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'] * (float)$tax_rate_value / 100;
                        $total_money = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'] + (float)$tax_money;
                        $amount = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'] + (float)$tax_money;
                    }else{
                        $total_money = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'];
                        $amount = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'];
                    }

                    $sub_total = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'];

                    $order_return_detail['tax_id'] = $tax_id;
                    $order_return_detail['total_amount'] = $total_money;
                    $order_return_detail['tax_rate'] = $tax_rate;
                    $order_return_detail['sub_total'] = $sub_total;
                    $order_return_detail['tax_name'] = $tax_name;

                    unset($order_return_detail['order']);
                    unset($order_return_detail['id']);
                    unset($order_return_detail['tax_select']);
                    unset($order_return_detail['unit_name']);

                    $this->db->insert(db_prefix() . 'wh_order_return_details', $order_return_detail);
                }
            }

            $data_log = [];
            $data_log['rel_id'] = $insert_id;
            $data_log['rel_type'] = 'order_returns';
            $data_log['staffid'] = get_staff_user_id();
            $data_log['date'] = date('Y-m-d H:i:s');
            $data_log['note'] = "order_returns";
            $this->add_activity_log($data_log);

            /*update next number setting*/
            $this->update_purchase_setting_v2(['pur_next_order_return_number' =>  (int)get_purchase_option('pur_next_order_return_number')+1]);

            //send request approval
            if($save_and_send_request == 'true'){
                $this->send_request_approve(['rel_id' => $insert_id, 'rel_type' => 'order_return', 'addedfrom' => $data['staff_id']]);
            }
        }

        //approval if not approval setting
        if (isset($insert_id)) {
            if ($data['approval'] == 1) {
                $this->update_approve_request($insert_id, 'order_return', 1);
            }

            hooks()->do_action('after_pur_order_return_added', $insert_id);
        }

        return $insert_id > 0 ? $insert_id : false;
    }

    /**
     * update inventory setting
     * @param  array $data 
     * @return boolean       
     */
    public function update_purchase_setting_v2($data)
    {
        $affected_rows=0;
        foreach ($data as $key => $value) {

            $this->db->where('name',$key);
            $this->db->update(db_prefix() . 'options', [
                'value' => $value,
            ]);

            if ($this->db->affected_rows() > 0) {
                $affected_rows++;
            }

        }

        if($affected_rows > 0){
            return true;
        }else{
            return false;
        }
    }

    /**
     * get order return
     * @param  [type] $id 
     * @return [type]     
     */
    public function get_order_return($id)
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'wh_order_returns')->row();
        }
        if ($id == false) {
            return $this->db->query('select * from '.db_prefix().'wh_order_returns')->result_array();
        }
    }

    /**
     * get unit code name
     * @return array
     */
    public function get_units_code_name() {
        return $this->db->query('select unit_type_id as id, unit_name as label from ' . db_prefix() . 'ware_unit_type')->result_array();
    }

    /**
     * get order return detail
     * @param  [type] $id 
     * @return [type]     
     */
    public function get_order_return_detail($id) {
        if (is_numeric($id)) {
            $this->db->where('order_return_id', $id);

            return $this->db->get(db_prefix() . 'wh_order_return_details')->result_array();
        }
        if ($id == false) {
            return $this->db->query('select * from '.db_prefix().'wh_order_return_details')->result_array();
        }
    }

    /**
     * get html tax order return
     * @param  [type] $id 
     * @return [type]     
     */
    public function get_html_tax_order_return($id)
    {
        $html = '';
        $html_currency = '';
        $preview_html = '';
        $pdf_html = '';
        $taxes = [];
        $t_rate = [];
        $tax_val = [];
        $tax_val_rs = [];
        $tax_name = [];
        $rs = [];
        $pdf_html_currency = '';

        $this->load->model('currencies_model');
        $_order_return = $this->get_order_return($id);
        $base_currency = $this->currencies_model->get_base_currency();
        if($_order_return->currency != 0){
            $base_currency = pur_get_currency_by_id($_order_return->currency);
        }

        $details = $this->get_order_return_detail($id);

        foreach($details as $row){
            if($row['tax_id'] != ''){
                $tax_arr = explode('|', $row['tax_id']);

                $tax_rate_arr = [];
                if($row['tax_rate'] != ''){
                    $tax_rate_arr = explode('|', $row['tax_rate']);
                }

                foreach($tax_arr as $k => $tax_it){
                    if(!isset($tax_rate_arr[$k]) ){
                        $tax_rate_arr[$k] = $this->tax_rate_by_id($tax_it);
                    }

                    if(!in_array($tax_it, $taxes)){
                        $taxes[$tax_it] = $tax_it;
                        $t_rate[$tax_it] = $tax_rate_arr[$k];
                        $tax_name[$tax_it] = $this->get_tax_name($tax_it).' ('.$tax_rate_arr[$k].'%)';
                    }
                }
            }
        }

        if(count($tax_name) > 0){
            $discount_total = $_order_return->discount_total;
            foreach($tax_name as $key => $tn){
                $tax_val[$key] = 0;
                foreach($details as $row_dt){
                    if(!(strpos($row_dt['tax_id'] ?? '', $taxes[$key]) === false)){

                        $total = ($row_dt['quantity']*$row_dt['unit_price']*$t_rate[$key]/100);

                        if($_order_return->discount_type == 'before_tax'){
                            $t     = ($discount_total / $_order_return->subtotal) * 100;
                            $tax_val[$key] += ($total - $total * $t / 100);
                        }else{
                           $tax_val[$key] += $total;
                        }
                    }
                }
                $pdf_html .= '<tr id="subtotal"><td ></td><td></td><td></td><td class="text_left">'.$tn.'</td><td class="text_right">'.app_format_money($tax_val[$key], $base_currency->symbol).'</td></tr>';
                $preview_html .= '<tr id="subtotal"><td>'.$tn.'</td><td>'.app_format_money($tax_val[$key], '').'</td><tr>';
                $html .= '<tr class="tax-area_pr"><td>'.$tn.'</td><td width="65%">'.app_format_money($tax_val[$key], '').'</td></tr>';
                $html_currency .= '<tr class="tax-area_pr"><td>'.$tn.'</td><td width="65%">'.app_format_money($tax_val[$key], $base_currency->symbol).'</td></tr>';
                $tax_val_rs[] = $tax_val[$key];
                $pdf_html_currency .= '<tr ><td align="right" width="85%">'.$tn.'</td><td align="right" width="15%">'.app_format_money($tax_val[$key], $base_currency->symbol).'</td></tr>';
            }
        }

        $rs['pdf_html'] = $pdf_html;
        $rs['preview_html'] = $preview_html;
        $rs['html'] = $html;
        $rs['taxes'] = $taxes;
        $rs['taxes_val'] = $tax_val_rs;
        $rs['html_currency'] = $html_currency;
        $rs['pdf_html_currency'] = $pdf_html_currency;
        return $rs;
    }

    /**
     * add activity log
     * @param array $data
     * return boolean
     */
    public function add_activity_log($data) {
        $this->db->insert(db_prefix() . 'pur_activity_log', $data);
        return true;
    }

    /**
     * update order return
     * @param  [type]  $data     
     * @param  [type]  $rel_type 
     * @param  boolean $id       
     * @return [type]            
     */
    public function update_order_return($data, $rel_type,  $id = false)
    {
        $results=0;

        $order_returns = [];
        $update_order_returns = [];
        $remove_order_returns = [];
        if(isset($data['isedit'])){
            unset($data['isedit']);
        }

        if (isset($data['newitems'])) {
            $order_returns = $data['newitems'];
            unset($data['newitems']);
        }

        if (isset($data['items'])) {
            $update_order_returns = $data['items'];
            unset($data['items']);
        }
        if (isset($data['removed_items'])) {
            $remove_order_returns = $data['removed_items'];
            unset($data['removed_items']);
        }

        unset($data['item_select']);
        unset($data['commodity_name']);
        unset($data['quantity']);
        unset($data['unit_price']);
        unset($data['unit_name']);
        unset($data['commodity_code']);
        unset($data['unit_id']);
        unset($data['discount']);
        unset($data['tax_rate']);
        unset($data['tax_name']);
        unset($data['rel_type_detail_id']);
        unset($data['item_reason_return']);
        unset($data['reason_return']);

        if(isset($data['save_and_send_request'])){
            unset($data['save_and_send_request']);
        }

        if(isset($data['main_additional_discount'])){
            unset($data['main_additional_discount']);
        }

        $check_appr = $this->get_approve_setting('order_return');
        $data['approval'] = 0;
        if ($check_appr && $check_appr != false) {
            $data['approval'] = 0;
        } else {
            $data['approval'] = 1;
        }

        if(isset($data['edit_approval'])){
            unset($data['edit_approval']);
        }

        $purchase_order = $this->get_pur_order($data['rel_id']);
        $data['currency'] = $purchase_order->currency;

        $data['total_amount']   = $data['total_amount'];
        $data['discount_total'] = $data['discount_total'];
        $data['total_after_discount'] = $data['total_after_discount'];
        $data['staff_id'] = get_staff_user_id();
        $data['datecreated'] = to_sql_date($data['datecreated'], true);
        if($data['order_date'] != null){
            $data['order_date'] = to_sql_date($data['order_date'], true);
        }

        $order_return_id = $data['id'];
        unset($data['id']);

        $this->db->where('id', $order_return_id);
        $this->db->update(db_prefix() . 'wh_order_returns', $data);
        if ($this->db->affected_rows() > 0) {
            $results++;
        }

        /*update order return*/
        if($rel_type == 'manual'){
            //CASE: add manual
            foreach ($update_order_returns as $order_return) {
                $tax_money = 0;
                $tax_rate_value = 0;
                $tax_rate = null;
                $tax_id = null;
                $tax_name = null;
                if(isset($order_return['tax_select'])){
                    $tax_rate_data = $this->wh_get_tax_rate($order_return['tax_select']);
                    $tax_rate_value = $tax_rate_data['tax_rate'];
                    $tax_rate = $tax_rate_data['tax_rate_str'];
                    $tax_id = $tax_rate_data['tax_id_str'];
                    $tax_name = $tax_rate_data['tax_name_str'];
                }

                if((float)$tax_rate_value != 0){
                    $tax_money = (float)$order_return['unit_price'] * (float)$order_return['quantity'] * (float)$tax_rate_value / 100;
                    $total_money = (float)$order_return['unit_price'] * (float)$order_return['quantity'] + (float)$tax_money;
                    $amount = (float)$order_return['unit_price'] * (float)$order_return['quantity'] + (float)$tax_money;
                }else{
                    $total_money = (float)$order_return['unit_price'] * (float)$order_return['quantity'];
                    $amount = (float)$order_return['unit_price'] * (float)$order_return['quantity'];
                }

                $sub_total = (float)$order_return['unit_price'] * (float)$order_return['quantity'];

                $order_return['tax_id'] = $tax_id;
                $order_return['total_amount'] = $total_money;
                $order_return['tax_rate'] = $tax_rate;
                $order_return['sub_total'] = $sub_total;
                $order_return['tax_name'] = $tax_name;


                unset($order_return['order']);
                unset($order_return['tax_select']);
                unset($order_return['unit_name']);


                $this->db->where('id', $order_return['id']);
                if ($this->db->update(db_prefix() . 'wh_order_return_details', $order_return)) {
                    $results++;
                }
            }
        }else if($rel_type == 'purchasing_return_order'){
            foreach ($update_order_returns as $order_return) {
                $tax_money = 0;
                $tax_rate_value = 0;
                $tax_rate = null;
                $tax_id = null;
                $tax_name = null;
                if(isset($order_return['tax_select'])){
                    $tax_rate_data = $this->wh_get_tax_rate($order_return['tax_select']);
                    $tax_rate_value = $tax_rate_data['tax_rate'];
                    $tax_rate = $tax_rate_data['tax_rate_str'];
                    $tax_id = $tax_rate_data['tax_id_str'];
                    $tax_name = $tax_rate_data['tax_name_str'];
                }

                if((float)$tax_rate_value != 0){
                    $tax_money = (float)$order_return['unit_price'] * (float)$order_return['quantity'] * (float)$tax_rate_value / 100;
                    $total_money = (float)$order_return['unit_price'] * (float)$order_return['quantity'] + (float)$tax_money;
                    $amount = (float)$order_return['unit_price'] * (float)$order_return['quantity'] + (float)$tax_money;
                }else{
                    $total_money = (float)$order_return['unit_price'] * (float)$order_return['quantity'];
                    $amount = (float)$order_return['unit_price'] * (float)$order_return['quantity'];
                }

                $sub_total = (float)$order_return['unit_price'] * (float)$order_return['quantity'];

                $order_return['tax_id'] = $tax_id;
                $order_return['total_amount'] = $total_money;
                $order_return['tax_rate'] = $tax_rate;
                $order_return['sub_total'] = $sub_total;
                $order_return['tax_name'] = $tax_name;
                $order_return_detail['rel_type_detail_id'] = $data['rel_id'];

                unset($order_return['order']);
                unset($order_return['tax_select']);
                unset($order_return['unit_name']);


                $this->db->where('id', $order_return['id']);
                if ($this->db->update(db_prefix() . 'wh_order_return_details', $order_return)) {
                    $results++;
                }
            }
        }


        // delete order return handle for 3 case add manual, add from Purchase order - Purchase, add from Sales order - Omni sale
        foreach ($remove_order_returns as $order_return_detail_id) {
            $this->db->where('id', $order_return_detail_id);
            if ($this->db->delete(db_prefix() . 'wh_order_return_details')) {
                $results++;
            }
        }

        // Add order return
        if($rel_type == 'manual'){
            //CASE: add manual

            foreach ($order_returns as $order_return_detail) {
                $order_return_detail['order_return_id'] = $order_return_id;

                $tax_money = 0;
                $tax_rate_value = 0;
                $tax_rate = null;
                $tax_id = null;
                $tax_name = null;
                if(isset($order_return_detail['tax_select'])){
                    $tax_rate_data = $this->wh_get_tax_rate($order_return_detail['tax_select']);
                    $tax_rate_value = $tax_rate_data['tax_rate'];
                    $tax_rate = $tax_rate_data['tax_rate_str'];
                    $tax_id = $tax_rate_data['tax_id_str'];
                    $tax_name = $tax_rate_data['tax_name_str'];
                }

                if((float)$tax_rate_value != 0){
                    $tax_money = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'] * (float)$tax_rate_value / 100;
                    $total_money = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'] + (float)$tax_money;
                    $amount = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'] + (float)$tax_money;
                }else{
                    $total_money = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'];
                    $amount = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'];
                }

                $sub_total = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'];

                $order_return_detail['tax_id'] = $tax_id;
                $order_return_detail['total_amount'] = $total_money;
                $order_return_detail['tax_rate'] = $tax_rate;
                $order_return_detail['sub_total'] = $sub_total;
                $order_return_detail['tax_name'] = $tax_name;

                unset($order_return_detail['order']);
                unset($order_return_detail['id']);
                unset($order_return_detail['tax_select']);
                unset($order_return_detail['unit_name']);

                $this->db->insert(db_prefix() . 'wh_order_return_details', $order_return_detail);

                if($this->db->insert_id()){
                    $results++;
                }
            }
        }else if($rel_type == 'purchasing_return_order'){
            foreach ($order_returns as $order_return_detail) {
                $order_return_detail['order_return_id'] = $order_return_id;

                $tax_money = 0;
                $tax_rate_value = 0;
                $tax_rate = null;
                $tax_id = null;
                $tax_name = null;
                if(isset($order_return_detail['tax_select'])){
                    $tax_rate_data = $this->wh_get_tax_rate($order_return_detail['tax_select']);
                    $tax_rate_value = $tax_rate_data['tax_rate'];
                    $tax_rate = $tax_rate_data['tax_rate_str'];
                    $tax_id = $tax_rate_data['tax_id_str'];
                    $tax_name = $tax_rate_data['tax_name_str'];
                }

                if((float)$tax_rate_value != 0){
                    $tax_money = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'] * (float)$tax_rate_value / 100;
                    $total_money = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'] + (float)$tax_money;
                    $amount = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'] + (float)$tax_money;
                }else{
                    $total_money = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'];
                    $amount = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'];
                }

                $sub_total = (float)$order_return_detail['unit_price'] * (float)$order_return_detail['quantity'];

                $order_return_detail['tax_id'] = $tax_id;
                $order_return_detail['total_amount'] = $total_money;
                $order_return_detail['tax_rate'] = $tax_rate;
                $order_return_detail['sub_total'] = $sub_total;
                $order_return_detail['tax_name'] = $tax_name;
                $order_return_detail['rel_type_detail_id'] = $data['rel_id'];

                unset($order_return_detail['order']);
                unset($order_return_detail['id']);
                unset($order_return_detail['tax_select']);
                unset($order_return_detail['unit_name']);

                $this->db->insert(db_prefix() . 'wh_order_return_details', $order_return_detail);

                if($this->db->insert_id()){
                    $results++;
                }
            }
        }
        

        // TODO send request approval
        if($save_and_send_request == 'true'){
            $this->send_request_approve(['rel_id' => $order_return_id, 'rel_type' => 'order_return', 'addedfrom' => $data['staff_id']]);
        }

        //approval if not approval setting
        if (isset($order_return_id)) {
            if ($data['approval'] == 1) {
                $this->update_approve_request($order_return_id, 'order_return', 1);
            }

            hooks()->do_action('after_pur_order_return_updated', $id);
        }

        return $results > 0 ? true : false;
    }

    /**
     * wh get activity log
     * @param  [type] $id   
     * @param  [type] $type 
     * @return [type]       
     */
    public function pur_get_activity_log($id, $rel_type)
    {
        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', $rel_type);
        $this->db->order_by('date', 'ASC');

        return $this->db->get(db_prefix() . 'wh_goods_delivery_activity_log')->result_array();
    }


    /**
     * log wh activity
     * @param  [type] $id              
     * @param  [type] $description     
     * @param  string $additional_data 
     * @return [type]                  
     */
    public function log_pur_activity($id, $rel_type, $description, $date = '')
    {
        if(strlen($date) == 0){
            $date = date('Y-m-d H:i:s');
        }
        $log = [
            'date'            => $date,
            'description'     => $description,
            'rel_id'          => $id,
            'rel_type'          => $rel_type,
            'staffid'         => get_staff_user_id(),
            'full_name'       => get_staff_full_name(get_staff_user_id()),
        ];

        $this->db->insert(db_prefix() . 'wh_goods_delivery_activity_log', $log);
        $insert_id = $this->db->insert_id();
        if($insert_id){
            return true;
        }
        return false;
    }

    /**
     * delete activitylog
     * @param  [type] $id 
     * @return [type]     
     */
    public function delete_activitylog($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'wh_goods_delivery_activity_log');

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }


    /**
     * delete order return
     * @param  [type] $id 
     * @return [type]     
     */
    public function delete_order_return($id) {
        hooks()->do_action('before_pur_order_return_deleted', $id);
        
        $affected_rows = 0;

        $order_return = $this->get_order_return($id);

        $this->db->where('id', $order_return->rel_id);
        $this->db->update(db_prefix().'pur_orders', ['order_status' => 'delivered']);
        if ($this->db->affected_rows() > 0) {
            $affected_rows++;
        }

        $this->db->where('order_return_id', $id);
        $this->db->delete(db_prefix() . 'wh_order_return_details');
        if ($this->db->affected_rows() > 0) {
            $affected_rows++;
        }

        $this->db->where('order_return_id', $id);
        $this->db->delete(db_prefix() . 'wh_order_returns_refunds');
        if ($this->db->affected_rows() > 0) {
            $affected_rows++;
        }

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'wh_order_returns');
        if ($this->db->affected_rows() > 0) {
            $affected_rows++;
        }

        if ($affected_rows > 0) {
            hooks()->do_action('after_pur_order_return_deleted', $id);

            return true;
        }
        return false;
    }

    /**
     * order return pdf
     * @param  [type] $order_return 
     * @return [type]               
     */
    public function order_return_pdf($order_return) {
        return app_pdf('order_return', module_dir_path(PURCHASE_MODULE_NAME, 'libraries/pdf/Order_pdf.php'), $order_return);
    }

    /**
     * Gets the order returns for vendor.
     */
    public function get_order_returns_for_vendor($vendor_id){
        $this->db->where('rel_type', 'purchasing_return_order');
        $this->db->where('company_id', $vendor_id);

        return $this->db->get(db_prefix().'wh_order_returns')->result_array();
    }

    /**
     * { share request to vendor }
     */
    public function share_request_to_vendor($data){
        $vendor_str = implode(',', $data['send_to_vendors']);

        $this->db->where('id', $data['pur_request_id']);
        $this->db->update(db_prefix().'pur_request', ['send_to_vendors' => $vendor_str]);

        if($this->db->affected_rows() > 0){
            return true;
        }

        return false;
    }

    /**
     * Gets the pur order files.
     *
     * @param        $pur_order  The pur order
     */
    public function get_pur_request_files($pur_request){
        $this->db->where('rel_id', $pur_request);
        $this->db->where('rel_type', 'pur_request');
        return $this->db->get(db_prefix().'files')->result_array();
    }

    /**
     * { change delivery status }
     *
     * @param        $status  The status
     * @param        $id      The identifier
     * @return     boolean
     */
    public function change_pr_approve_status($status, $id){
        $this->db->where('id', $id);
        $this->db->update(db_prefix().'pur_request', [ 'status' => $status]);
        if ($this->db->affected_rows() > 0) {
            
            return true;
        }
        return false;
    }

    /**
     * Creates a purchase order row template.
     *
     * @param      string      $name              The name
     * @param      string      $item_name         The item name
     * @param      string      $item_description  The item description
     * @param      int|string  $quantity          The quantity
     * @param      string      $unit_name         The unit name
     * @param      int|string  $unit_price        The unit price
     * @param      string      $taxname           The taxname
     * @param      string      $item_code         The item code
     * @param      string      $unit_id           The unit identifier
     * @param      string      $tax_rate          The tax rate
     * @param      string      $total_money       The total money
     * @param      string      $discount          The discount
     * @param      string      $discount_money    The discount money
     * @param      string      $total             The total
     * @param      string      $into_money        Into money
     * @param      string      $tax_id            The tax identifier
     * @param      string      $tax_value         The tax value
     * @param      string      $item_key          The item key
     * @param      bool        $is_edit           Indicates if edit
     *
     * @return     string      
     */
    public function create_purchase_invoice_row_template($name = '', $item_name = '', $item_description = '', $quantity = '', $unit_name = '', $unit_price = '', $taxname = '',  $item_code = '', $unit_id = '', $tax_rate = '', $total_money = '', $discount = '', $discount_money = '', $total = '', $into_money = '', $tax_id = '', $tax_value = '', $item_key = '',$is_edit = false, $currency_rate = 1, $to_currency = '') {
        
        $this->load->model('invoice_items_model');
        $row = '';

        $name_item_code = 'item_code';
        $name_item_name = 'item_name';
        $name_item_description = 'description';
        $name_unit_id = 'unit_id';
        $name_unit_name = 'unit_name';
        $name_quantity = 'quantity';
        $name_unit_price = 'unit_price';
        $name_tax_id_select = 'tax_select';
        $name_tax_id = 'tax_id';
        $name_total = 'total';
        $name_tax_rate = 'tax_rate';
        $name_tax_name = 'tax_name';
        $name_tax_value = 'tax_value';
        $array_attr = [];
        $array_attr_payment = ['data-payment' => 'invoice'];
        $name_into_money = 'into_money';
        $name_discount = 'discount';
        $name_discount_money = 'discount_money';
        $name_total_money = 'total_money';

        $array_available_quantity_attr = [ 'min' => '0.0', 'step' => 'any', 'readonly' => true];
        $array_qty_attr = [ 'min' => '0.0', 'step' => 'any'];
        $array_rate_attr = [ 'min' => '0.0', 'step' => 'any'];
        $array_discount_attr = [ 'min' => '0.0', 'step' => 'any'];
        $array_discount_money_attr = [ 'min' => '0.0', 'step' => 'any'];
        $str_rate_attr = 'min="0.0" step="any"';

        $array_subtotal_attr = ['readonly' => true];
        $text_right_class = 'text-right';

        if ($name == '') {
            $row .= '<tr class="main">
                  <td></td>';
            $vehicles = [];
            $array_attr = ['placeholder' => _l('unit_price')];
         
            $manual             = true;
            $invoice_item_taxes = '';
            $amount = '';
            $sub_total = 0;

        } else {
            $row .= '<tr class="sortable item">
                    <td class="dragger"><input type="hidden" class="order" name="' . $name . '[order]"><input type="hidden" class="ids" name="' . $name . '[id]" value="' . $item_key . '"></td>';
            $name_item_code = $name . '[item_code]';
            $name_item_name = $name . '[item_name]';
            $name_item_description = $name . '[item_description]';
            $name_unit_id = $name . '[unit_id]';
            $name_unit_name = '[unit_name]';
            $name_quantity = $name . '[quantity]';
            $name_unit_price = $name . '[unit_price]';
            $name_tax_id_select = $name . '[tax_select][]';
            $name_tax_id = $name . '[tax_id]';
            $name_total = $name . '[total]';
            $name_tax_rate = $name . '[tax_rate]';
            $name_tax_name = $name .'[tax_name]';
            $name_into_money = $name .'[into_money]';
            $name_discount = $name .'[discount]';
            $name_discount_money = $name .'[discount_money]';
            $name_total_money = $name . '[total_money]';
            $name_tax_value = $name. '[tax_value]';
      
           
            $array_qty_attr = ['onblur' => 'pur_calculate_total();', 'onchange' => 'pur_calculate_total();', 'min' => '0.0' , 'step' => 'any',  'data-quantity' => (float)$quantity];
            

            $array_rate_attr = ['onblur' => 'pur_calculate_total();', 'onchange' => 'pur_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('rate')];
            $array_discount_attr = ['onblur' => 'pur_calculate_total();', 'onchange' => 'pur_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('discount')];

            $array_discount_money_attr = ['onblur' => 'pur_calculate_total(1);', 'onchange' => 'pur_calculate_total(1);', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('discount')];


            $manual             = false;

            $tax_money = 0;
            $tax_rate_value = 0;

            if($is_edit){
                $invoice_item_taxes = pur_convert_item_taxes($tax_id, $tax_rate, $taxname);
                $arr_tax_rate = explode('|', $tax_rate ?? '');
                foreach ($arr_tax_rate as $key => $value) {
                    $tax_rate_value += (float)$value;
                }
            }else{
                $invoice_item_taxes = $taxname;
                $tax_rate_data = $this->pur_get_tax_rate($taxname);
                $tax_rate_value = $tax_rate_data['tax_rate'];
            }

            if((float)$tax_rate_value != 0){
                $tax_money = (float)$unit_price * (float)$quantity * (float)$tax_rate_value / 100;
                $goods_money = (float)$unit_price * (float)$quantity + (float)$tax_money;
                $amount = (float)$unit_price * (float)$quantity + (float)$tax_money;
            }else{
                $goods_money = (float)$unit_price * (float)$quantity;
                $amount = (float)$unit_price * (float)$quantity;
            }

            $sub_total = (float)$unit_price * (float)$quantity;
            $amount = app_format_number($amount);

        }
 

        $row .= '<td class="">' . render_textarea($name_item_name, '', $item_name, ['rows' => 2, 'placeholder' => _l('pur_item_name')] ) . '</td>';

        $row .= '<td class="">' . render_textarea($name_item_description, '', $item_description, ['rows' => 2, 'placeholder' => _l('item_description')] ) . '</td>';

        $row .= '<td class="rate">' . render_input($name_unit_price, '', $unit_price, 'number', $array_rate_attr, [], 'no-margin', $text_right_class);
        if( $unit_price != ''){
            $original_price = ($currency_rate > 0) ? round( ($unit_price/$currency_rate), 2) : 0;
            $base_currency = get_base_currency();
            if($to_currency != 0 && $to_currency != $base_currency->id){
                $row .= render_input('original_price', '',app_format_money($original_price, $base_currency), 'text', ['data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => _l('original_price'), 'disabled' => true], [], 'no-margin', 'input-transparent text-right pur_input_none');
            }

            $row .= '<input class="hide" name="og_price" disabled="true" value="'.$original_price.'">';
        }
       
        $row .= '<td class="quantities">' . 
        render_input($name_quantity, '', $quantity, 'number', $array_qty_attr, [], 'no-margin', $text_right_class) . 
        render_input($name_unit_name, '', $unit_name, 'text', ['placeholder' => _l('unit'), 'readonly' => true], [], 'no-margin', 'input-transparent text-right pur_input_none').
        '</td>';
        
        $row .= '<td class="taxrate">' . $this->get_taxes_dropdown_template($name_tax_id_select, $invoice_item_taxes, 'invoice', $item_key, true, $manual) . '</td>';

        $row .= '<td class="tax_value">' . render_input($name_tax_value, '', $tax_value, 'number', $array_subtotal_attr, [], '', $text_right_class) . '</td>';

        $row .= '<td class="_total" align="right">' . $total . '</td>';

        if($discount_money > 0){
            $discount = '';
        }

        $row .= '<td class="discount">' . render_input($name_discount, '', $discount, 'number', $array_discount_attr, [], '', $text_right_class) . '</td>';
        $row .= '<td class="discount_money" align="right">' . render_input($name_discount_money, '', $discount_money, 'number', $array_discount_money_attr, [], '', $text_right_class.' item_discount_money') . '</td>';
        $row .= '<td class="label_total_after_discount" align="right">' . app_format_number($total_money) . '</td>';

        $row .= '<td class="hide commodity_code">' . render_input($name_item_code, '', $item_code, 'text', ['placeholder' => _l('commodity_code')]) . '</td>';
        $row .= '<td class="hide unit_id">' . render_input($name_unit_id, '', $unit_id, 'text', ['placeholder' => _l('unit_id')]) . '</td>';

        $row .= '<td class="hide _total_after_tax">' . render_input($name_total, '', $total, 'number', []) . '</td>';

        //$row .= '<td class="hide discount_money">' . render_input($name_discount_money, '', $discount_money, 'number', []) . '</td>';
        $row .= '<td class="hide total_after_discount">' . render_input($name_total_money, '', $total_money, 'number', []) . '</td>';
        $row .= '<td class="hide _into_money">' . render_input($name_into_money, '', $into_money, 'number', []) . '</td>';

        if ($name == '') {
            $row .= '<td><button type="button" onclick="pur_add_item_to_table(\'undefined\',\'undefined\'); return false;" class="btn pull-right btn-info"><i class="fa fa-check"></i></button></td>';
        } else {
            $row .= '<td><a href="#" class="btn btn-danger pull-right" onclick="pur_delete_item(this,' . $item_key . ',\'.invoice-item\'); return false;"><i class="fa fa-trash"></i></a></td>';
        }
        $row .= '</tr>';
        return $row;
    }

    /**
     * Gets the pur order detail.
     *
     * @param      <int>  $pur_request  The pur request
     *
     * @return     <array>  The pur order detail.
     */
    public function get_pur_invoice_detail($pur_request){
        $this->db->where('pur_invoice',$pur_request);
        $pur_invoice_details = $this->db->get(db_prefix().'pur_invoice_details')->result_array();

        foreach($pur_invoice_details as $key => $detail){
            $pur_invoice_details[$key]['discount_money'] = (float) $detail['discount_money'];
            $pur_invoice_details[$key]['into_money'] = (float) $detail['into_money'];
            $pur_invoice_details[$key]['total'] = (float) $detail['total'];
            $pur_invoice_details[$key]['total_money'] = (float) $detail['total_money'];
            $pur_invoice_details[$key]['unit_price'] = (float) $detail['unit_price'];
            $pur_invoice_details[$key]['tax_value'] = (float) $detail['tax_value'];
        }

        return $pur_invoice_details;
    }

    /**
     * Gets the order return refunds.
     *
     * @param        $order_return  The order return
     *
     * @return       The order return refunds.
     */
    public function get_order_return_refunds($order_return){
        $this->db->select(prefixed_table_fields_array(db_prefix() . 'wh_order_returns_refunds', true) . ',' . db_prefix() . 'payment_modes.id as payment_mode_id, ' . db_prefix() . 'payment_modes.name as payment_mode_name');
        $this->db->where('order_return_id', $order_return);

        $this->db->join(db_prefix() . 'payment_modes', db_prefix() . 'payment_modes.id = ' . db_prefix() . 'wh_order_returns_refunds.payment_mode', 'left');

        $this->db->order_by('refunded_on', 'desc');

        $refunds = $this->db->get(db_prefix() . 'wh_order_returns_refunds')->result_array();

        $this->load->model('payment_modes_model');
        $payment_gateways = $this->payment_modes_model->get_payment_gateways(true);
        $i                = 0;

        foreach ($refunds as $refund) {
            if (is_null($refund['payment_mode_id'])) {
                foreach ($payment_gateways as $gateway) {
                    if ($refund['payment_mode'] == $gateway['id']) {
                        $refunds[$i]['payment_mode_id']   = $gateway['id'];
                        $refunds[$i]['payment_mode_name'] = $gateway['name'];
                    }
                }
            }
            $i++;
        }

        return $refunds;
    }


    /**
     * Creates a refund.
     *
     * @param        $id     The identifier
     * @param        $data   The data
     *
     * @return     bool    
     */
    public function create_order_return_refund($id, $data)
    {
        if ($data['amount'] == 0) {
            return false;
        }

        $data['note'] = trim($data['note']);

        $this->db->insert(db_prefix() . 'wh_order_returns_refunds', [
            'created_at'     => date('Y-m-d H:i:s'),
            'order_return_id' => $id,
            'staff_id'       => $data['staff_id'],
            'refunded_on'    => $data['refunded_on'],
            'payment_mode'   => $data['payment_mode'],
            'amount'         => $data['amount'],
            'note'           => nl2br($data['note']),
        ]);

        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            $remaining_refund = get_order_return_remaining_refund($id);
            $status = 'confirm';
            if($remaining_refund > 0){
                $status = 'processing';
            }else{
                $status = 'finish';
            }

            $this->db->where('id', $id);
            $this->db->update(db_prefix().'wh_order_returns', ['status' => $status]);

            hooks()->do_action('after_pur_refund_added', $insert_id);
        }

        return $insert_id;
    }

    /**
     * { edit refund }
     *
     * @param        $id     The identifier
     * @param        $data   The data
     *
     * @return     bool    
     */
    public function edit_order_return_refund($id, $data)
    {
        if ($data['amount'] == 0) {
            return false;
        }

        $data['note'] = trim($data['note']);

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'wh_order_returns_refunds', [
            'refunded_on'  => $data['refunded_on'],
            'payment_mode' => $data['payment_mode'],
            'amount'       => $data['amount'],
            'note'         => nl2br($data['note']),
        ]);

        $insert_id = $this->db->insert_id();

        if ($this->db->affected_rows() > 0) {
            $remaining_refund = get_order_return_remaining_refund($id);
            $status = 'confirm';
            if($remaining_refund > 0){
                $status = 'processing';
            }else{
                $status = 'finish';
            }

            $this->db->where('id', $id);
            $this->db->update(db_prefix().'wh_order_returns', ['status' => $status]);

            hooks()->do_action('after_pur_refund_updated', $id);

        }

        return $insert_id;
    }

    /**
     * { delete refund }
     *
     * @param        $refund_id       The refund identifier
     * @param        $debit_note_id  The debit note identifier
     *
     * @return     bool    
     */
    public function delete_order_return_refund($refund_id, $order_return_id)
    {
        $this->db->where('id', $refund_id);
        $this->db->delete(db_prefix() . 'wh_order_returns_refunds');
        if ($this->db->affected_rows() > 0) {
            $remaining_refund = get_order_return_remaining_refund($order_return_id);
            $status = 'confirm';
            if($remaining_refund > 0){
                $status = 'processing';
            }else{
                $status = 'finish';
            }

            $this->db->where('id', $order_return_id);
            $this->db->update(db_prefix().'wh_order_returns', ['status' => $status]);

            hooks()->do_action('after_pur_refund_deleted', $refund_id);

            return true;
        }

        return false;
    }

    /**
     * Gets the refund.
     *
     * @param        $id     The identifier
     *
     * @return       The refund.
     */
    public function get_order_return_refund($id)
    {
        $this->db->where('id', $id);

        return $this->db->get(db_prefix() . 'wh_order_returns_refunds')->row();
    }


     /**
     * get taxes dropdown template
     * @param  [type]  $name     
     * @param  [type]  $taxname  
     * @param  string  $type     
     * @param  string  $item_key 
     * @param  boolean $is_edit  
     * @param  boolean $manual   
     * @return [type]            
     */
    public function get_taxes_dropdown_template_readonly($name, $taxname, $type = '', $item_key = '', $is_edit = false, $manual = false)
    {
        // if passed manually - like in proposal convert items or project
        if($taxname != '' && !is_array($taxname)){
            $taxname = explode(',', $taxname);
        }

        if ($manual == true) {
            // + is no longer used and is here for backward compatibilities
            if (is_array($taxname) || strpos($taxname, '+') !== false) {
                if (!is_array($taxname)) {
                    $__tax = explode('+', $taxname);
                } else {
                    $__tax = $taxname;
                }
                // Multiple taxes found // possible option from default settings when invoicing project
                $taxname = [];
                foreach ($__tax as $t) {
                    $tax_array = explode('|', $t);
                    if (isset($tax_array[0]) && isset($tax_array[1])) {
                        array_push($taxname, $tax_array[0] . '|' . $tax_array[1]);
                    }
                }
            } else {
                $tax_array = explode('|', $taxname);
                // isset tax rate
                if (isset($tax_array[0]) && isset($tax_array[1])) {
                    $tax = get_tax_by_name($tax_array[0]);
                    if ($tax) {
                        $taxname = $tax->name . '|' . $tax->taxrate;
                    }
                }
            }
        }
        // First get all system taxes
        $this->load->model('taxes_model');
        $taxes = $this->taxes_model->get();
        $i     = 0;
        foreach ($taxes as $tax) {
            unset($taxes[$i]['id']);
            $taxes[$i]['name'] = $tax['name'] . '|' . $tax['taxrate'];
            $i++;
        }
        if ($is_edit == true) {

            // Lets check the items taxes in case of changes.
            // Separate functions exists to get item taxes for Invoice, Estimate, Proposal, Credit Note
            $func_taxes = 'get_' . $type . '_item_taxes';
            if (function_exists($func_taxes)) {
                $item_taxes = call_user_func($func_taxes, $item_key);
            }

            foreach ($item_taxes as $item_tax) {
                $new_tax            = [];
                $new_tax['name']    = $item_tax['taxname'];
                $new_tax['taxrate'] = $item_tax['taxrate'];
                $taxes[]            = $new_tax;
            }
        }

        // In case tax is changed and the old tax is still linked to estimate/proposal when converting
        // This will allow the tax that don't exists to be shown on the dropdowns too.
        if (is_array($taxname)) {
            foreach ($taxname as $tax) {
                // Check if tax empty
                if ((!is_array($tax) && $tax == '') || is_array($tax) && $tax['taxname'] == '') {
                    continue;
                };
                // Check if really the taxname NAME|RATE don't exists in all taxes
                if (!value_exists_in_array_by_key($taxes, 'name', $tax)) {
                    if (!is_array($tax)) {
                        $tmp_taxname = $tax;
                        $tax_array   = explode('|', $tax);
                    } else {
                        $tax_array   = explode('|', $tax['taxname']);
                        $tmp_taxname = $tax['taxname'];
                        if ($tmp_taxname == '') {
                            continue;
                        }
                    }
                    $taxes[] = ['name' => $tmp_taxname, 'taxrate' => $tax_array[1]];
                }
            }
        }

        // Clear the duplicates
        $taxes = $this->pur_uniqueByKey($taxes, 'name');

        $select = '<select class="selectpicker display-block taxes" disabled="true" data-width="100%" name="' . $name . '" multiple data-none-selected-text="' . _l('no_tax') . '">';

        foreach ($taxes as $tax) {
            $selected = '';
            if (is_array($taxname)) {
                foreach ($taxname as $_tax) {
                    if (is_array($_tax)) {
                        if ($_tax['taxname'] == $tax['name']) {
                            $selected = 'selected';
                        }
                    } else {
                        if ($_tax == $tax['name']) {
                            $selected = 'selected';
                        }
                    }
                }
            } else {
                if ($taxname == $tax['name']) {
                    $selected = 'selected';
                }
            }

            $select .= '<option value="' . $tax['name'] . '" ' . $selected . ' data-taxrate="' . $tax['taxrate'] . '" data-taxname="' . $tax['name'] . '" data-subtext="' . $tax['name'] . '">' . $tax['taxrate'] . '%</option>';
        }
        $select .= '</select>';

        return $select;
    }

    /**
     * Gets the estimate html by pr vendor.
     *
     * @param        $pur_request  The pur request
     * @param      string  $vendor       The vendor
     *
     * @return     string  The estimate html by pr vendor.
     */
    public function get_estimate_html_by_pr_vendor($pur_request, $vendor = '')
    {
        $this->db->where('pur_request', $pur_request);
        $this->db->where('status', 2);
        if($vendor != ''){
            $this->db->where('vendor', $vendor);
        }

        $estimates = $this->db->get(db_prefix().'pur_estimates')->result_array();

        $html = '<option value=""></option>';
        foreach($estimates as $es){
            $html .= '<option value="'.$es['id'].'">'.format_pur_estimate_number($es['id']).'</option>';
        }

        return $html;
    }

    /**
     * Gets the sale estimate for pr.
     */
    public function get_sale_estimate_for_pr(){
        $this->db->where('status != 3');
        $this->db->order_by('number', 'desc');
        return $this->db->get(db_prefix().'estimates')->result_array();
    }

    /**
     * delete hr profile permission
     * @param  [type] $id 
     * @return [type]     
     */
    public function delete_hr_profile_permission($id)
    {
        $str_permissions ='';
        foreach (list_purchase_permisstion() as $per_key =>  $per_value) {
            if(strlen($str_permissions) > 0){
                $str_permissions .= ",'".$per_value."'";
            }else{
                $str_permissions .= "'".$per_value."'";
            }
        }

        $sql_where = " feature IN (".$str_permissions.") ";

        $this->db->where('staff_id', $id);
        $this->db->where($sql_where);
        $this->db->delete(db_prefix() . 'staff_permissions');

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * { confirm registration }
     *
     * @param      <type>  $vendor_id  The client identifier
     *
     * @return     bool    ( description_of_the_return_value )
     */
    public function confirm_registration($vendor_id)
    {
        $contact_id = pur_get_primary_contact_user_id($vendor_id);
        $this->db->where('userid', $vendor_id);
        $this->db->update(db_prefix() . 'pur_vendor', ['active' => 1, 'registration_confirmed' => 1]);

        $this->db->where('id', $contact_id);
        $this->db->update(db_prefix() . 'pur_contacts', ['active' => 1]);

        $contact = $this->get_contact($contact_id);

        if ($contact) {
            $template = mail_template('vendor_registration_confirmed', 'purchase', $contact);

            $template->send();

            return true;
        }

        return false;
    }


    /**
     * When vendor register, mark the contact and the vendor as inactive and set the registration_confirmed field to 0
     * @param  mixed $vendor_id  the vendor id
     * @return boolean
     */
    public function require_confirmation($vendor_id)
    {
        $contact_id = pur_get_primary_contact_user_id($vendor_id);
        $this->db->where('userid', $vendor_id);
        $this->db->update(db_prefix() . 'pur_vendor', ['active' => 0, 'registration_confirmed' => 0]);

        $this->db->where('id', $contact_id);
        $this->db->update(db_prefix() . 'pur_contacts', ['active' => 0]);

        return true;
    }


    /**
     * Sends a purchase order.
     *
     * @param         $data   The data
     *
     * @return     boolean
     */
    public function send_contract($data){
        $mail_data = [];
        $count_sent = 0;
        $contract = $this->get_contract($data['contract_id']);
        if(isset($data['attach_pdf'])){

            try {
                $pdf = pur_contract_pdf($contract);
            } catch (Exception $e) {
                echo pur_html_entity_decode($e->getMessage());
                die;
            }

            $attach = $pdf->Output($contract->contract_number . '.pdf', 'S');
        }


        if(strlen(get_option('smtp_host')) > 0 && strlen(get_option('smtp_password')) > 0 ){
            foreach($data['send_to'] as $mail){

                $mail_data['contract_id'] = $data['contract_id'];
                $mail_data['content'] = $data['content'];
                $mail_data['mail_to'] = $mail;

                $template = mail_template('purchase_contract_to_contact', 'purchase', array_to_object($mail_data));

                if(isset($data['attach_pdf'])){
                    $template->add_attachment([
                        'attachment' => $attach,
                        'filename'   => str_replace('/', '-', $contract->contract_number . '.pdf'),
                        'type'       => 'application/pdf',
                    ]);
                }

                $rs = $template->send();

                if($rs){
                    $count_sent++;
                }
            }

            if($count_sent > 0){
                return true;
            }  
        }

        return false;
    }

    /**
     * get job position training de
     * @param  integer $id 
     * @return object      
     */
    public function get_item_longdescriptions($id){

        $this->db->where('id', $id);
        return  $this->db->get(db_prefix() . 'items')->row();

    }

    /**
     * { request quotation pdf }
     *
     * @param      <type>  $pur_request  The pur request
     *
     * @return      ( pdf )
     */
    public function compare_quotation_pdf($pur_request)
    {
        return app_pdf('pur_request', module_dir_path(PURCHASE_MODULE_NAME, 'libraries/pdf/Compare_quotation_pdf'), $pur_request);
    }

    /**
     * Gets the request quotation pdf html.
     *
     * @param      <type>  $pur_request_id  The pur request identifier
     *
     * @return     string  The request quotation pdf html.
     */
    public function get_compare_quotation_pdf_html($pur_request_id){
        $this->load->model('departments_model');

        $pur_request = $this->get_purchase_request($pur_request_id);
        $project = $this->projects_model->get($pur_request->project);
        $project_name = '';
        if($project && isset($project->name)){
            $project_name = $project->name;
        }

        $tax_data = $this->get_html_tax_pur_request($pur_request_id);
        if($pur_request->currency != 0){
            $base_currency = pur_get_currency_by_id($pur_request->currency);
        }else{
            $base_currency = get_base_currency_pur();
        }
        $pur_request_detail = $this->get_pur_request_detail($pur_request_id);
        $company_name = get_option('invoice_company_name'); 
        $dpm_name = $this->departments_model->get($pur_request->department)->name;
        $address = get_option('invoice_company_address'); 
        $day = date('d',strtotime($pur_request->request_date));
        $month = date('m',strtotime($pur_request->request_date));
        $year = date('Y',strtotime($pur_request->request_date));
        $list_approve_status = $this->get_list_approval_details($pur_request_id,'pur_request');

        $quotations = get_quotations_by_pur_request($pur_request_id);

    $html = '<table class="table">
        <tbody>
          <tr>
            <td class="font_td_cpn" style="width: 70%">'. _l('purchase_company_name').': '. $company_name.'</td>
            <td rowspan="3" style="width: 30%" class="text-right">'.get_po_logo(get_option('pdf_logo_width')).'</td>
          </tr>
          <tr>
            <td class="font_500">'. _l('address').': '. $address.'</td>
          </tr>
          <tr>
            <td class="font_500"><strong>'.$pur_request->pur_rq_code.'</strong></td>
          </tr>
        </tbody>
      </table>
      <table class="table">
        <tbody>
          <tr>
            
            <td class="td_ali_font"><h2 class="h2_style">'.mb_strtoupper(_l('compare_quotes')).'</h2></td>
           
          </tr>
          <tr>
            
            <td class="align_cen">'. _l('days').' '.$day.' '._l('month').' '.$month.' '._l('year') .' '.$year.'</td>
            
          </tr>
          
        </tbody>
      </table>
      <table class="table">
        <tbody>
          <tr>
            <td class="td_width_25"><h4>'. _l('requester').':</h4></td>
            <td class="td_width_75">'. get_staff_full_name($pur_request->requester).'</td>
          </tr>
          <tr>
            <td class="font_500"><h4>'. _l('department').':</h4></td>
            <td>'. $dpm_name.'</td>
          </tr>
      
        </tbody>
      </table>
      <br><br>
      ';

        $html .= '<table border="1" class="table compare_quotes_table">
                              <thead class="bold">
                               <tr class="">';
        $html .= '<th class="width4" rowspan="2" scope="col"><span class="bold">'. _l('items').'</span></th>';
        $html .= '<th class="width4" rowspan="2" scope="col"><span class="bold">'. _l('pur_qty').'</span></th>';
        $html .= '<th class="width15" rowspan="2" scope="col"><span class="bold">'._l('description').'</span></th>';

        foreach($quotations as $quote){
            $html .= '<th colspan="2" class="text-center"><span class="bold text-danger">'.format_pur_estimate_number($quote['id']). ' - '.get_vendor_company_name($quote['vendor']).'</span></th>';
        }
        $html .= '</tr><tr class="">';
        foreach($quotations as $quote){
            $html .= '<th class="text-right"><span class="bold">'. _l('purchase_unit_price').'</span></th>';
            $html .= '<th class="text-right"><span class="bold">'._l('total').'</span></th>';
        }
        
        $html .=  '</tr>
                </thead>
                <tbody>';
                        
        foreach($pur_request_detail as $key => $item){
            $html .= '<tr class="">';
            $html .= '<td class="width4">'.pur_html_entity_decode($key + 1).'</td>';
            $unit_name = isset(get_unit_type_item($item['unit_id'])->unit_name) ? get_unit_type_item($item['unit_id'])->unit_name : '';
            $html .= '<td class="width4">'.pur_html_entity_decode($item['quantity']).' '.$unit_name.'</td>';
            $item_name = isset(get_item_hp($item['item_code'])->description) ? get_item_hp($item['item_code'])->description : '';

            $html .= '<td class="width15">'.pur_html_entity_decode($item_name).'</td>';

            foreach($quotations as $quote){
                                   
                $_currency = $base_currency;
                if($quote['currency'] != 0){
                  $_currency = pur_get_currency_by_id($quote['currency']);
                }
                $item_quote = get_item_detail_in_quote($item['item_code'], $quote['id']);
                if(isset($item_quote)){
                    $html .= '<td class="text-right">'. app_format_money($item_quote->unit_price, $_currency->name).'</td>';
                    $html .= '<td class="text-right">'. app_format_money($item_quote->total_money, $_currency->name).'</td>';
                }else{
                    $html .= '<td>-</td>
                             <td>-</td>';
                }
            }

            $html .= '</tr>';
        }
        $html .= '<tr class="">';
        $html .= '<td colspan="3" class="text-center height_50"><span class="bold">'. _l('mark_a_contract').'</span></td>';
        foreach($quotations as $quote){
            $html .= '<td colspan="2"> '.pur_html_entity_decode($quote['make_a_contract']).'</td>';
        }
        $html .= '</tr>';
        $html .= '<tr class="">';
        $html .=  '<td colspan="3" class="text-center height_50"><span class="bold">'. _l('total_purchase_amount').'</span></td>';
        foreach($quotations as $quote){ 
            
            $_currency = $base_currency;
            if($quote['currency'] != 0){
              $_currency = pur_get_currency_by_id($quote['currency']);
            }

            $html .= '<td colspan="2" class="text-right">';
            $html .= '<span class="bold text-info">'. app_format_money($quote['total'], $_currency->name).'</span>';
            
            if($_currency->id != $base_currency->id){
                $convert_rate = pur_get_currency_rate($_currency->name, $base_currency->name);
                $convert_value = round(($quote['total'] * $convert_rate), 2 );
                $html .='<br><i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="'._l('pur_convert_from').' '. $_currency->name. ' '._l('pur_to'). ' '.$base_currency->name.' '._l('pur_with_currency_rate').': '.$convert_rate. '"></i>&nbsp;&nbsp;<span class="bold text-info">'.app_format_money($convert_value, $base_currency->name).'</span>';
            }
  
            $html .= '</td>';
        }
        $html .= '</tr>
                    </tbody></table>';

        $html .= '<div class="col-md-12 mtop15">
                        <h4>'. _l('comparison_notes').':</h4><p>'. $pur_request->compare_note .'</p>
                       
                     </div>';            

      $html .=  '<link href="' . FCPATH.'modules/purchase/assets/css/pur_order_pdf.css' . '"  rel="stylesheet" type="text/css" />';
      return $html;
    }

    /**
     * Gets the purcahse estimate attachments.
     *
     * @param      <type>  $surope  The surope
     * @param      string  $id      The identifier
     *
     * @return     <type>  The part attachments.
     */
    public function get_vendor_item_attachments($surope, $id = '')
    {
        // If is passed id get return only 1 attachment
        if (is_numeric($id)) {
            $this->db->where('id', $id);
        } else {
            $this->db->where('rel_id', $assets);
        }
        $this->db->where('rel_type', 'vendor_items');
        $result = $this->db->get(db_prefix().'files');
        if (is_numeric($id)) {
            return $result->row();
        }

        return $result->result_array();
    }

     /**
     * { delete estimate attachment }
     *
     * @param         $id     The identifier
     *
     * @return     boolean 
     */
    public function delete_vendor_item_file($id)
    {
        $attachment = $this->get_vendor_item_attachments('', $id);
        $deleted    = false;
        if ($attachment) {
            if (empty($attachment->external)) {
                unlink(PURCHASE_MODULE_UPLOAD_FOLDER .'/vendor_items/'. $attachment->rel_id . '/' . $attachment->file_name);
            }
            $this->db->where('id', $attachment->id);
            $this->db->delete('tblfiles');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
            }

            if (is_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/vendor_items/'. $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(PURCHASE_MODULE_UPLOAD_FOLDER .'/vendor_items/'. $attachment->rel_id);
                if (count($other_attachments) == 0) {
                    // okey only index.html so we can delete the folder also
                    delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER .'/vendor_items/'. $attachment->rel_id);
                }
            }
        }

        return $deleted;
    }

    /**
     * Gets the pur order search.
     *
     * @param        $q      The quarter
     */
    public function get_pur_order_search($q){
        $this->db->where('1=1 AND (pur_order_number LIKE "%'.$this->db->escape_like_str($q).'%")');
        return $this->db->get(db_prefix().'pur_orders')->result_array();
    }

    /**
     * Gets the pur order search.
     *
     * @param        $q      The quarter
     */
    public function get_estimate_search($q){
        $this->db->where('1=1 AND (prefix LIKE "%'.$this->db->escape_like_str($q).'%" OR number LIKE "%'.$this->db->escape_like_str($q).'%")');
        return $this->db->get(db_prefix().'pur_estimates')->result_array();
    }

    /**
     * Gets the contract seach.
     *
     * @param        $q      The quarter
     *
     * @return       The contract seach.
     */
    public function get_contract_seach($q){
        $this->db->where('1=1 AND (contract_number LIKE "%'.$this->db->escape_like_str($q).'%" OR contract_name LIKE "%'.$this->db->escape_like_str($q).'%")');
        return $this->db->get(db_prefix().'pur_contracts')->result_array();
    }

    /**
     * Gets the contract seach.
     *
     * @param        $q      The quarter
     *
     * @return       The contract seach.
     */
    public function get_pur_invoice_search($q){
        $this->db->where('1=1 AND (invoice_number LIKE "%'.$this->db->escape_like_str($q).'%" OR vendor_invoice_number LIKE "%'.$this->db->escape_like_str($q).'%")');
        return $this->db->get(db_prefix().'pur_invoices')->result_array();
    }

    /**
     * Gets the pur order search.
     *
     * @param        $q      The quarter
     */
    public function get_debit_note_search($q){
        $this->db->where('1=1 AND (number LIKE "%'.$this->db->escape_like_str($q).'%")');
        return $this->db->get(db_prefix().'pur_debit_notes')->result_array();
    }

    /**
     * { request quotation pdf }
     *
     * @param      <type>  $pur_request  The pur request
     *
     * @return      ( pdf )
     */
    public function purchase_invoice_pdf($pur_invoice)
    {
        return app_pdf('pur_invoice', module_dir_path(PURCHASE_MODULE_NAME, 'libraries/pdf/Purchase_invoice_pdf'), $pur_invoice);
    }

    /**
     * Gets the purchase invoice pdf html.
     */
    public function get_purchase_invoice_pdf_html($invoice_id){

        $invoice = $this->get_pur_invoice($invoice_id);


        $pur_order = $this->get_pur_order($invoice->pur_order);
        $invoice_detail = $this->get_pur_invoice_detail($invoice_id);

        $company_name = get_option('invoice_company_name'); 
        $vendor = $this->get_vendor($invoice->vendor);
        $tax_data = $this->get_html_tax_pur_invoice($invoice_id);
        $base_currency = get_base_currency_pur();
        if($invoice->currency != 0){
            $base_currency = pur_get_currency_by_id($invoice->currency);
        }

        $address = '';
        $vendor_name = '';

        $ship_to = '';

        if($pur_order){
            $ship_to = $pur_order->shipping_address . ' '.  $pur_order->shipping_city.' '.$pur_order->shipping_state.' '.$pur_order->shipping_zip.' '.get_country_name($pur_order->shipping_country);
        }


        if($ship_to == ''){

            $ship_to = get_option('pur_company_address') . ' '.  get_option('pur_company_city').' '.get_option('pur_company_state').' '.get_option('pur_company_zipcode').' '.get_country_name(get_option('pur_company_country_code'));
            
            if($ship_to == ''){
                $ship_to = get_option('invoice_company_address') . ' '.  get_option('invoice_company_city').' '.get_option('company_state').' '.get_option('invoice_company_country_code') ;
            }
        }

        if($vendor){
            $countryName = '';
            if($country = get_country($vendor->country) ){
                $countryName = $country->short_name;
            }

            $address = $vendor->address.', '.$countryName;
            $vendor_name = $vendor->company;

            $ship_country_name = '';
            if($ship_country = get_country($vendor->shipping_country)){
                $ship_country_name = $ship_country->short_name;
            }
            
        }

        $day = _d($invoice->invoice_date);
       
        $html = '';     
        $html .= '<table class="table">
            <tbody>
              <tr>
                <td rowspan="6" class="text-left" style="width: 70%">
                '.get_po_logo(get_option('pdf_logo_width'), "img img-responsive").'
                 <br>'.format_organization_info().'
                </td>
                <td class="text-right" style="width: 30%">
                    <strong class="fsize20">'.mb_strtoupper(_l('purchase_invoice')).'</strong><br>
                    <strong>'.mb_strtoupper($invoice->invoice_number).'</strong><br>
                </td>
              </tr>

              <tr>
                <td class="text-right" style="width: 30%">
                    <br><strong>'._l('pur_vendor').'</strong>    
                    <br>'. $vendor_name.'
                    <br>'. strip_tags($address).'
                </td>
                <td></td>
              </tr>

              <tr>
                <td></td>
              </tr>
              <tr>
                <td class="text-right" style="width: 30%">
                    <br><strong>'._l('pur_ship_to').'</strong>    
                    <br>'. strip_tags($ship_to).'
                    </td>
                <td></td>
              </tr>

              <tr>
                <td></td>
              </tr>
              <tr>
                <td class="text-right">'. _l('invoice_date').': '. $day.'</td>
                <td></td>
              </tr>
            </tbody>

          </table>
          <br><br><br>
          ';

        $html .=  '<table class="table purorder-item">
        <thead>
          <tr>
            <th class="thead-dark" style="width: 30%;">'._l('items').'</th>
            <th class="thead-dark" style="width: 15%;" align="right">'._l('purchase_unit_price').'</th>
            <th class="thead-dark" style="width: 15%;" align="right">'._l('purchase_quantity').'</th>';
         
            if(get_option('show_purchase_tax_column') == 1){ 

                $html .= '<th class="thead-dark" align="right" style="width: 10%;">'._l('tax').'</th>';
            }
 
            $html .= '<th class="thead-dark" align="right" style="width: 15%;">'._l('discount').'</th>
            <th class="thead-dark" align="right" style="width: 15%;">'._l('total').'</th>
          </tr>
          </thead>
          <tbody>';
        $t_mn = 0;
        $item_discount = 0;
        foreach($invoice_detail as $row){
            $items = $this->get_items_by_id($row['item_code']);
            $des_html = ($items) ? $items->commodity_code.' - '.$items->description : $row['item_name'];

            $units = $this->get_units_by_id($row['unit_id']);
            $unit_name = isset($units->unit_name) ? $units->unit_name : '';
        
            $html .= '<tr nobr="true" class="sortable">
                <td style="width: 30%;"><strong>'.$des_html.'</strong><br><span>'.$row['description'].'</span></td>
                <td style="width: 15%;"align="right">'.app_format_money($row['unit_price'],$base_currency->symbol).'</td>
                <td style="width: 15%;" align="right">'.app_format_number($row['quantity'],'').' '. $unit_name.'</td>';
         
            if(get_option('show_purchase_tax_column') == 1){  
                $html .= '<td align="right" style="width: 10%;">'.app_format_money($row['total'] - $row['into_money'],$base_currency->symbol).'</td>';
            }
       
            $html .= '<td align="right" style="width: 15%;">'.app_format_money($row['discount_money'],$base_currency->symbol).'</td>
            <td align="right" style="width: 15%;">'.app_format_money($row['total_money'],$base_currency->symbol).'</td>
          </tr>';

            $t_mn += $row['total_money'];
            $item_discount += $row['discount_money'];
        }  
        $html .=  '</tbody>
                </table><br><br>';

        $html .= '<table class="table text-right"><tbody>';
        $html .= '<tr id="subtotal">
                    <td style="width: 33%"></td>
                     <td>'._l('subtotal').' </td>
                     <td class="subtotal">
                        '.app_format_money($invoice->subtotal,$base_currency->symbol).'
                     </td>
                  </tr>';

        $html .= $tax_data['pdf_html'];

        if(($invoice->discount_total + $item_discount) > 0){
            $html .= '
                  
                  <tr id="subtotal">
                  <td style="width: 33%"></td>
                     <td>'._l('discount_total(money)').'</td>
                     <td class="subtotal">
                        '.app_format_money(($invoice->discount_total + $item_discount), $base_currency->symbol).'
                     </td>
                  </tr>';
        }

        if($invoice->shipping_fee  > 0){
            $html .= '
                  
                  <tr id="subtotal">
                  <td style="width: 33%"></td>
                     <td>'._l('pur_shipping_fee').'</td>
                     <td class="subtotal">
                        '.app_format_money($invoice->shipping_fee, $base_currency->symbol).'
                     </td>
                  </tr>';
        }
        $html .= '<tr id="subtotal">
                 <td style="width: 33%"></td>
                 <td>'. _l('total').'</td>
                 <td class="subtotal">
                    '. app_format_money($invoice->total, $base_currency->symbol).'
                 </td>
              </tr>';

        $html .= ' </tbody></table>';

        $html .= '<div class="col-md-12 mtop15">
                        <h4>'. _l('terms_and_conditions').':</h4><p>'. $invoice->terms .'</p>
                       
                     </div>';

        $html .=  '<link href="' . FCPATH.'modules/purchase/assets/css/pur_order_pdf.css' . '"  rel="stylesheet" type="text/css" />';
        return $html;

    }

    /**
     * Gets the payment by vendor.
     *
     * @param      <type>  $vendor  The vendor
     */
    public function get_payment_by_vendor_v2($vendor){
        return  $this->db->query('select pop.pur_invoice, pop.id as pop_id, pop.amount, pop.date, pop.paymentmode, pop.transactionid from '.db_prefix().'pur_invoice_payment pop left join '.db_prefix().'pur_invoices po on po.id = pop.pur_invoice where po.vendor = '.$vendor)->result_array();
    }
}