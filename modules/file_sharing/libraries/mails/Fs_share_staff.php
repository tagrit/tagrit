<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Fs_share_staff extends App_mail_template
{
    protected $for = 'staff';

    protected $staff;

    public $slug = 'fs-share-staff';

    public function __construct($staff)
    {
        parent::__construct();

        $this->staff = $staff;
    }
    public function build()
    {
        $this->to($this->staff->mail_to);
        $this->set_merge_fields('fs_share_staff_merge_fields', $this->staff);
    }
}
