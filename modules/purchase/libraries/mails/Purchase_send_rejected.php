<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Purchase_send_rejected extends App_mail_template
{

    protected $data;

    public $slug = 'purchase-send-rejected';

    public function __construct($data)
    {
        parent::__construct();

        $this->data = $data;
        // For SMS and merge fields for email
        $this->set_merge_fields('purchase_approve_merge_fields', $this->data);
    }
    public function build()
    {
        $this->to($this->data->mail_to);
    }
}
