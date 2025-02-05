<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Fs_share_public extends App_mail_template
{
    protected $public;

    public $slug = 'fs-share-public';

    public function __construct($public)
    {
        parent::__construct();

        $this->public = $public;
        // For SMS and merge fields for email
        $this->set_merge_fields('fs_share_staff_merge_fields', $this->public);
    }
    public function build()
    {
        $this->to($this->public->mail_to);
    }
}
