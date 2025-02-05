<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Inventory_warning_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
            [
                'name'      => 'Staff name',
                'key'       => '{staff_name}',
                'available' => [
                    'inventory_warning',
                ],
            ],
            
            [
                'name'      => 'Notification content',
                'key'       => '{notification_content}',
                'available' => [
                    'inventory_warning',
                ],
            ],
            
        ];
    }


    /**
     * Merge field for appointments
     * @param  mixed $teampassword 
     * @return array
     */
    public function format($notification_info)
    {
        $fields = [];

        if (!$notification_info) {
            return $fields;
        }

        $fields['{staff_name}']                  =   $notification_info->staff_name ;

        $fields['{notification_content}']                  =  $notification_info->string_notification ;

        return $fields;
    }


}
