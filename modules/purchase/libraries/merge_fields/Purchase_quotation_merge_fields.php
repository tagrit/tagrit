<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Purchase_quotation_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
            [
                'name'      => 'PQ number',
                'key'       => '{pq_number}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-quotation-to-contact',
                ],
            ],
            [
                'name'      => 'Quotation link',
                'key'       => '{quotation_link}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-quotation-to-contact',
                ],
            ],
            [
                'name'      => 'PQ tax value',
                'key'       => '{pq_tax_value}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-quotation-to-contact',
                ],
            ],
            [
                'name'      => 'PQ subtotal',
                'key'       => '{pq_subtotal}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-quotation-to-contact',
                ],
            ],
            [
                'name'      => 'PQ value',
                'key'       => '{pq_value}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-quotation-to-contact',
                ],
            ],
            [
                'name'      => 'Additional content',
                'key'       => '{additional_content}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-quotation-to-contact',
                ],
            ],
        ];
    }

    /**
     * Merge field for appointments
     * @param  mixed $data 
     * @return array
     */
    public function format($data)
    {
        $po_id = $data->pur_estimate_id;
        $this->ci->load->model('purchase/purchase_model');


        $fields = [];

        $this->ci->db->where('id', $po_id);

        $po = $this->ci->db->get(db_prefix() . 'pur_estimates')->row();


        if (!$po) {
            return $fields;
        }

        $fields['{quotation_link}']                  = site_url('purchase/vendors_portal/add_update_quotation/' . $po->id.'/1');
        $fields['{pq_number}']                  =  format_pur_estimate_number($po_id);
        $fields['{pq_value}']                   =  app_format_money($po->total, '');
        $fields['{pq_tax_value}']                   =  app_format_money($po->total_tax, '');
        $fields['{pq_subtotal}']                   =  app_format_money($po->subtotal, '');
        $fields['{additional_content}'] = $data->content;

        return $fields;
    }
}
