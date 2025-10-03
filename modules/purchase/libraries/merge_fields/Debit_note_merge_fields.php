<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Debit_note_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
            [
                'name'      => 'Debit Note number',
                'key'       => '{dn_number}',
                'available' => [
                   
                ],
                'templates' => [
                    'debit-note-to-contact',
                ],
            ],
            [
                'name'      => 'DN tax value',
                'key'       => '{po_tax_value}',
                'available' => [
                   
                ],
                'templates' => [
                    'debit-note-to-contact',
                ],
            ],
            [
                'name'      => 'DN subtotal',
                'key'       => '{dn_subtotal}',
                'available' => [
                   
                ],
                'templates' => [
                    'debit-note-to-contact',
                ],
            ],
            [
                'name'      => 'DN value',
                'key'       => '{dn_value}',
                'available' => [
                   
                ],
                'templates' => [
                    'debit-note-to-contact',
                ],
            ],
            [
                'name'      => 'Additional content',
                'key'       => '{additional_content}',
                'available' => [
                   
                ],
                'templates' => [
                    'debit-note-to-contact',
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
        $debit_note_id = $data->debit_note_id;
        $this->ci->load->model('purchase/purchase_model');


        $fields = [];

        $debit_note = $this->ci->purchase_model->get_debit_note($debit_note_id);


        if (!$debit_note) {
            return $fields;
        }

        $fields['{dn_number}']                  =  format_debit_note_number($debit_note->id);
        $fields['{po_value}']                   =  app_format_money($debit_note->total, '');
        $fields['{po_tax_value}']                   =  app_format_money($debit_note->total_tax, '');
        $fields['{po_subtotal}']                   =  app_format_money($debit_note->subtotal, '');
        $fields['{additional_content}'] = $data->content;

        return $fields;
    }
}
