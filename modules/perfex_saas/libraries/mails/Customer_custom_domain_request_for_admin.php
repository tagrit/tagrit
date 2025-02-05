<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(__DIR__ . '/traits/PerfexSaasMailTemplate.php');

/**
 * Email template class for email sent to admin on every new custom domain request
 */
class Customer_custom_domain_request_for_admin extends App_mail_template
{
    use PerfexSaasMailTemplate;

    /**
     * @inheritDoc
     */
    public $rel_type = 'contact';

    /**
     * @inheritDoc
     */
    protected $for = 'staff';

    /**
     * @inheritDoc
     */
    public $slug = 'company-instance-custom-domain-request-for-admin';
}