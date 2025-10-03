<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(__DIR__ . '/traits/PerfexSaasMailTemplate.php');

/**
 * Email template class for email sent to admin on instance removal
 */
class Customer_removed_instance_for_admin extends App_mail_template
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
    public $slug = 'company-instance-removed-for-admin';
}
