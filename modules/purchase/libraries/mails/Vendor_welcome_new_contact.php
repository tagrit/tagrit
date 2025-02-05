<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Vendor_welcome_new_contact extends App_mail_template
{
    protected $for = 'vendor';

    protected $contact;

    public $slug = 'new-contact-created';

    public $rel_type = 'contact';

    public function __construct($contact)
    {
        parent::__construct();
        $this->contact = $contact;
    }

    public function build()
    {
        $this->to($this->contact->email)
        ->set_rel_id($this->contact->id)
        ->set_merge_fields('vendor_merge_fields', $this->contact, $this->contact->userid, $this->contact->id);
    }
}
