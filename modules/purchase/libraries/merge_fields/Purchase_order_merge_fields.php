<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Purchase_order_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
            [
                'name'      => 'PO number',
                'key'       => '{po_number}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-order-to-contact',
                ],
            ],
            [
                'name'      => 'Public link',
                'key'       => '{public_link}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-order-to-contact',
                ],
            ],
            [
                'name'      => 'PO name',
                'key'       => '{po_name}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-order-to-contact',
                ],
            ],
            [
                'name'      => 'PO tax value',
                'key'       => '{po_tax_value}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-order-to-contact',
                ],
            ],
            [
                'name'      => 'PO subtotal',
                'key'       => '{po_subtotal}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-order-to-contact',
                ],
            ],
            [
                'name'      => 'PO value',
                'key'       => '{po_value}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-order-to-contact',
                ],
            ],
            [
                'name'      => 'Additional content',
                'key'       => '{additional_content}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-order-to-contact',
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
        $po_id = $data->po_id;
        $this->ci->load->model('purchase/purchase_model');


        $fields = [];

        $this->ci->db->where('id', $po_id);

        $po = $this->ci->db->get(db_prefix() . 'pur_orders')->row();


        if (!$po) {
            return $fields;
        }

        $fields['{public_link}']                  = site_url('purchase/vendors_portal/pur_order/' . $po->id.'/'.$po->hash);
        $fields['{po_name}']                  =  $po->pur_order_name;
        $fields['{po_number}']                  =  $po->pur_order_number;
        $fields['{po_value}']                   =  app_format_money($po->total, '');
        $fields['{po_tax_value}']                   =  app_format_money($po->total_tax, '');
        $fields['{po_subtotal}']                   =  app_format_money($po->subtotal, '');
        $fields['{additional_content}'] = $data->content;

        return $fields;
    }
}
