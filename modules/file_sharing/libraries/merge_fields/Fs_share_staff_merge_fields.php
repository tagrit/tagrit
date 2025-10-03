<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Fs_share_staff_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
            [
                'name'      => 'Sender',
                'key'       => '{sender}',
                'available' => [
                    'file_sharing',
                ],
            ],
            [
                'name'      => 'Receiver',
                'key'       => '{receiver}',
                'available' => [
                    'file_sharing',
                ],
            ],
            [
                'name'      => 'File name',
                'key'       => '{file_name}',
                'available' => [
                    'file_sharing',
                ],
            ],
            [
                'name'      => 'Share link',
                'key'       => '{share_link}',
                'available' => [
                    'file_sharing',
                ],
            ]
        ];
    }

    /**
     * Merge field for appointments
     * @param  mixed $file_sharing 
     * @return array
     */
    public function format($share_obj)
    {
        $fields['{sender}']   = $share_obj->sender;
        $fields['{receiver}'] = $share_obj->receiver;
        $fields['{file_name}']                  =   $share_obj->file_name;
        $fields['{share_link}']            = $share_obj->share_link;      
        return $fields;
    }
}
