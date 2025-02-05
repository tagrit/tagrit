<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(__DIR__ . '/traits/PerfexSaasMailTemplate.php');

/**
 * Email template class for email sent to the customer on instance removal
 */
class Customer_instance_auto_removal_notice extends App_mail_template
{
    use PerfexSaasMailTemplate;

    /**
     * @inheritDoc
     */
    public $rel_type = 'contact';

    /**
     * @inheritDoc
     */
    protected $for = 'customer';

    /**
     * @inheritDoc
     */
    public $slug = 'company-instance-auto-removal-notice';
}
