<?php

defined('BASEPATH') || exit('No direct script access allowed');
require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../third_party/node.php';
use Firebase\JWT\JWT as Mailbox_JWT;
use Firebase\JWT\Key as Mailbox_Key;
use WpOrg\Requests\Requests as Mailbox_Requests;

class Mailbox_aeiou
{
    public static function getPurchaseData($code)
    {
        $givemecode = Mailbox_Requests::get(GIVE_ME_CODE)->body;
        $bearer     = get_instance()->session->has_userdata('bearer') ? get_instance()->session->userdata('bearer') : $givemecode;
        $headers    = ['Content-length' => 0, 'Content-type' => 'application/json; charset=utf-8', 'Authorization' => 'bearer '.$bearer];
        $verify_url = 'https://api.envato.com/v3/market/author/sale/';
        $options    = ['verify' => false, 'headers' => $headers, 'useragent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13'];
        $response   = Mailbox_Requests::get($verify_url.'?code='.$code, $headers, $options);

        return ($response->success) ? json_decode($response->body) : false;
    }

    public static function verifyPurchase($code)
    {
        return null;
    }

    public function validatePurchase($module_name)
    {
        return true;
    }
}
