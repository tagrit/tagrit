<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(__DIR__ . '/traits/PerfexSaasMailTemplate.php');

/**
 * Email template class for email sent to the admin on new instance creation
 */
class Customer_deployed_instance_for_admin extends App_mail_template
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
    public $slug = 'company-instance-deployed-for-admin';
}
