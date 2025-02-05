<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Purchase_approve_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
            [
                'name'      => 'Link',
                'key'       => '{link}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-send-rejected',
                    'purchase-send-approved',
                ],
            ],
            [
                'name'      => 'By staff',
                'key'       => '{by_staff_name}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-send-rejected',
                    'purchase-send-approved',
                ],
            ],
            [
                'name'      => 'Staff name',
                'key'       => '{staff_name}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-send-rejected',
                    'purchase-send-approved',
                ],
            ],
            [
                'name'      => 'Purchase Request/Pruchase Order/Quotation/Payment Request',
                'key'       => '{type}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-send-rejected',
                    'purchase-send-approved',
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
       
        $fields['{link}']                  = $data->link;
        $fields['{by_staff_name}']                  =  $data->by_staff_name;
        $fields['{type}']                   =  $data->type;
       

        return $fields;
    }
}
