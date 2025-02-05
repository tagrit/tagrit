<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Purchase_contract_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
            [
                'name'      => 'Contract number',
                'key'       => '{contract_number}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-contract-to-contact',
                ],
            ],
            [
                'name'      => 'Contract link',
                'key'       => '{contract_link}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-contract-to-contact',
                ],
            ],
            [
                'name'      => 'Contract name',
                'key'       => '{contract_name}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-contract-to-contact',
                ],
            ],
            [
                'name'      => 'Contract value',
                'key'       => '{contract_value}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-contract-to-contact',
                ],
            ],
            [
                'name'      => 'Additional content',
                'key'       => '{additional_content}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-contract-to-contact',
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
        $contract_id = $data->contract_id;


        $fields = [];

        $this->ci->db->where('id', $contract_id);
        $contract = $this->ci->db->get(db_prefix() . 'pur_contracts')->row();


        if (!$contract) {
            return $fields;
        }

        $fields['{contract_link}']                  = site_url('purchase/vendors_portal/view_contract/' . $contract->id);
        $fields['{contract_name}']                  =  $contract->contract_name;
        $fields['{contract_number}']                  =  $contract->contract_number;
        $fields['{contract_value}']                   =  app_format_money($contract->contract_value, '');
        $fields['{additional_content}'] = $data->content;

        return $fields;
    }
}
