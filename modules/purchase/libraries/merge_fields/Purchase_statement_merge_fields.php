<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Purchase_statement_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
            [
                'name'      => 'Contact Firstname',
                'key'       => '{contact_firstname}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-statement-to-contact',
                ],
            ],
            [
                'name'      => 'Contact Lastname',
                'key'       => '{contact_lastname}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-statement-to-contact',
                ],
            ],
            [
                'name'      => 'Statement From',
                'key'       => '{statement_from}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-statement-to-contact',
                ],
            ],
            [
                'name'      => 'Statement To',
                'key'       => '{statement_to}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-statement-to-contact',
                ],
            ],
            [
                'name'      => 'Statement Balance Due',
                'key'       => '{statement_balance_due}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-statement-to-contact',
                ],
            ],
            [
                'name'      => 'Additional content',
                'key'       => '{additional_content}',
                'available' => [
                    
                ],
                'templates' => [
                    'purchase-statement-to-contact',
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
        $fields = [];


        $statement = $data->statement;

        $fields['{contact_firstname}']                  = $data->contact->firstname;
        $fields['{contact_lastname}']                  =  $data->contact->lastname;
        $fields['{statement_from}']              = _d($statement->from);
        $fields['{statement_to}']                = _d($statement->to);
        $fields['{statement_balance_due}']       = app_format_money($statement->balance_due, $statement->currency->name);
        $fields['{additional_content}'] = $data->content;

        return $fields;
    }
}
