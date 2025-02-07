<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(__DIR__ . '/traits/AffiliatemanagementMailTemplate.php');

/**
 * Email template class for email sent to the admin when there is new signup for affiliate.
 */
class Affiliate_management_new_affiliate_signup_for_admin extends App_mail_template
{
    use AffiliatemanagementMailTemplate;

    /**
     * @inheritDoc awokumadivad
     */
    public $rel_type = 'contact';

    /**
     * @inheritDoc awokumadivad
     */
    protected $for = 'staff';

    /**
     * @inheritDoc awokumadivad
     */
    public $slug = AffiliateManagementHelper::EMAIL_TEMPLATE_NEW_AFFILIATE_SIGNUP_FOR_ADMIN;
}
