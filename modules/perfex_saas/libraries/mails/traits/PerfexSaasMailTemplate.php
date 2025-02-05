<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Email template class for all mail sent through the saas.
 * The class is inherited by all saas email templates.
 */
trait PerfexSaasMailTemplate
{

    /**
     * Tenant email address
     *
     * @var string
     */
    protected $contact_email;

    /**
     * The ID of the client/tenant.
     *
     * @var mixed
     */
    protected $client_id;

    /**
     * The ID of the client/tenant contact.
     *
     * @var mixed
     */
    protected $contact_id;

    /**
     * Instance
     *
     * @var mixed
     */
    protected $instance_data;
    protected $other_extra_data;


    /**
     * The constructor.
     * This is called when perfex is creating instance of this template
     *
     * @param string $contact_email
     * @param int $client_id
     * @param int $contact_id
     * @param mixed $instance_data
     */
    public function __construct($contact_email, $client_id, $contact_id, $instance_data, $other_extra_data = [])
    {
        parent::__construct();

        $this->contact_email = $contact_email;
        $this->contact_id    = $contact_id;
        $this->client_id     = $client_id;
        $this->instance_data = $instance_data;
        $this->other_extra_data = $other_extra_data;
    }

    /**
     * Build the email message.
     */
    public function build()
    {
        // Load required libraries
        $this->ci->load->library('merge_fields/client_merge_fields');
        $this->ci->load->library(PERFEX_SAAS_MODULE_NAME . '/merge_fields/perfex_saas_company_merge_fields');

        // Set email properties
        $this->to($this->contact_email)                                // Set the recipient email address
            ->set_rel_id($this->contact_id)                            // Set the relationship ID
            ->set_merge_fields('client_merge_fields', $this->client_id, $this->contact_id)   // Set merge fields for client
            ->set_merge_fields('perfex_saas_company_merge_fields', $this->instance_data, $this->other_extra_data);   // Set merge fields for Perfex SaaS company
    }
}
