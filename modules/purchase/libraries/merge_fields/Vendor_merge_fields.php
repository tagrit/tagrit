<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Vendor_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
            [
                'name'      => 'Vendor Name',
                'key'       => '{companyname}',
                'available' => [
                   
                ],
                'templates' => [
                    'vendor-registration-confirmed',
                    'new-contact-created',
                ],
            ],
            [
                'name'      => 'Contact first name',
                'key'       => '{contact_firstname}',
                'available' => [
                   
                ],
                'templates' => [
                    'vendor-registration-confirmed',
                    'new-contact-created',
                ],
            ],
            [
                'name'      => 'Contact last name',
                'key'       => '{contact_lastname}',
                'available' => [
                   
                ],
                'templates' => [
                    'vendor-registration-confirmed',
                    'new-contact-created',
                ],
            ],
            [
                'name'      => 'Vendor Portal Link',
                'key'       => '{vendor_portal_link}',
                'available' => [
                   
                ],
                'templates' => [
                    'vendor-registration-confirmed',
                    'new-contact-created',
                ],
            ],
            [
                'name'      => 'Password',
                'key'       => '{password}',
                'available' => [
                   
                ],
                'templates' => [
                    'new-contact-created',
                ],
            ],
        ];
    }

    /**
     * Merge field for appointments
     * @param  mixed $teampassword 
     * @return array
     */
    public function format($_contact, $vendor_id, $contact_id = '')
    {

        $this->ci->db->where('id', $contact_id);
        $contact = $this->ci->db->get(db_prefix() . 'pur_contacts')->row();

        if ($contact) {
            $fields['{contact_firstname}']          = $contact->firstname;
            $fields['{contact_lastname}']           = $contact->lastname;
        }

        $fields['{password}'] = '';
        if( isset($_contact->password_before_hash) ){
            $fields['{password}'] = $_contact->password_before_hash;
        }

        $fields['{companyname}']          = get_vendor_company_name($vendor_id);

        $fields['{vendor_portal_url}']          = site_url('purchase/authentication_vendor/login');
        $fields['{vendor_portal_link}']          = site_url('purchase/authentication_vendor/login');

        return $fields;
    }
}
