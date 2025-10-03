<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Fs_share_client extends App_mail_template
{
    protected $for = 'client';

    protected $client;

    public $slug = 'fs-share-client';

    public function __construct($client)
    {
        parent::__construct();

        $this->client = $client;
        // For SMS and merge fields for email
        $this->set_merge_fields('fs_share_staff_merge_fields', $this->client);
    }
    public function build()
    {
        $this->to($this->client->mail_to);
    }
}
