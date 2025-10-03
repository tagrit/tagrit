<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Purchase_request_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
            [
                'name'      => 'PR number',
                'key'       => '{pr_number}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-request-to-contact',
                ],
            ],
            [
                'name'      => 'PR Public link',
                'key'       => '{public_link}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-request-to-contact',
                ],
            ],
            [
                'name'      => 'PR name',
                'key'       => '{pr_name}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-request-to-contact',
                ],
            ],
            [
                'name'      => 'PR tax value',
                'key'       => '{pr_tax_value}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-request-to-contact',
                ],
            ],
            [
                'name'      => 'PR subtotal',
                'key'       => '{pr_subtotal}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-request-to-contact',
                ],
            ],
            [
                'name'      => 'PR value',
                'key'       => '{pr_value}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-request-to-contact',
                ],
            ],
            [
                'name'      => 'Additional content',
                'key'       => '{additional_content}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-request-to-contact',
                ],
            ],
        ];
    }

    /**
     * Merge field for appointments
     * @param  mixed $teampassword 
     * @return array
     */
    public function format($data)
    {
        $po_id = $data->pur_request_id;
        $this->ci->load->model('purchase/purchase_model');


        $fields = [];

        $this->ci->db->where('id', $po_id);

        $po = $this->ci->db->get(db_prefix() . 'pur_request')->row();


        if (!$po) {
            return $fields;
        }

        $fields['{public_link}']                  = site_url('purchase/vendors_portal/pur_request/' . $po->id.'/'.$po->hash);
        $fields['{pr_name}']                  =  $po->pur_rq_name;
        $fields['{pr_number}']                  =  $po->pur_rq_code;
        $fields['{pr_value}']                   =  app_format_money($po->total, '');
        $fields['{pr_tax_value}']                   =  app_format_money($po->total_tax, '');
        $fields['{pr_subtotal}']                   =  app_format_money($po->subtotal, '');
        $fields['{additional_content}'] = $data->content;

        return $fields;
    }
}
