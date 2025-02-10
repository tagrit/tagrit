<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * This class describes a mpesa express daraja api. 
 * A customer class wrapping around Mpesa Express api
 */
class Mpesa
{
    public $settings;

    //Mpesa IP addresses
    const MPESA_LIVE_IP_ADDRESSES = [
        '196.201.214.200',
        '196.201.214.206',
        '196.201.213.114',
        '196.201.214.207',
        '196.201.214.208',
        '196. 201.213.44',
        '196.201.212.127',
        '196.201.212.138',
        '196.201.212.129',
        '196.201.212.136',
        '196.201.212.74',
        '196.201.212.69'
    ];

    /**
     * Make instance
     *
     * @param array $settings
     * 
     * /*
     * array of parameters:
     *   [
     *        'mode' => 'sandbox'|'live',
     *        'consumer_key' => '',
     *        'consumer_secret' => '',
     *        'phone_number' => '', //admin mpesa phone number
     *        'short_code' => '',
     *        'stk_pass_key' => '', //LIPA stk push password
     *        'logger' => 'log', //callback function for logging     
     *   ]
     */
    public function __construct($settings)
    {
        $this->settings = (object)$settings;
    }


    /**
     * Get url of an endpoint using endpoint key
     *
     * @param string $endpoint
     * @return string
     */
    public function getUrl($endpoint)
    {
        $urls = [
            "access_token" => 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials',
            "stk_push" => 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest',
            "query" => 'https://api.safaricom.co.ke/mpesa/stkpushquery/v2/query',
        ];

        if (empty($this->settings->mode) || $this->settings->mode == 'sandbox') {
            $urls = [
                "access_token" => 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials',
                "stk_push" => 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest',
                "query" => 'https://sandbox.safaricom.co.ke/mpesa/stkpushquery/v2/query',
            ];
        }

        return $urls[$endpoint];
    }


    /**
     * Create and return access token form keys
     *
     * @return string
     */
    public function getAccessToken()
    {
        $mpesa = $this->settings;
        $consumer_key = $mpesa->consumer_key;
        $consumer_secret = $mpesa->consumer_secret;
        $auth = base64_encode("$consumer_key:$consumer_secret");
        $token = '';

        try {

            $query = $this->httpRequest($this->getUrl('access_token'), [
                'headers' => ["Authorization: Basic $auth"],
            ]);

            if (!isset($query->access_token))
                throw new \Exception($query->errorMessage, 1);

            $token = $query->access_token;
        } catch (\Throwable $th) {

            if (is_callable($this->settings->logger)) {

                call_user_func($this->settings->logger, [$th->getMessage()]);
            }
        }

        return $token;
    }

    /**
     * Make lipa push
     *
     * @param float $amount
     * @param string $ref_id
     * @param string $description
     * @param string $customer_mpesa_phone_number
     * @param string $callback_url
     * @param string $timestamp
     * @param string $accessToken
     * @return object
     */
    public function stkPush(
        float $amount,
        string $ref_id,
        string $description,
        string $customer_mpesa_phone_number,
        string $callback_url,
        $timestamp = null,
        $access_token = null
    ) {

        $timestamp = $timestamp ?? date("YmdHis", time());

        $mpesa = $this->settings;
        $short_code = $mpesa->short_code;
        $stk_pass_key = $mpesa->stk_pass_key;
        $transaction_type = $mpesa->transaction_type;
        $party_b = $mpesa->party_b;

        if (empty($transaction_type)) {
            $transaction_type = "CustomerPayBillOnline";
        }

        if (empty($party_b)) {
            $party_b = $short_code;
        }

        $password = base64_encode($short_code . $stk_pass_key . $timestamp);

        $mpesa_payload = [
            "BusinessShortCode" => $short_code,
            "Password" => $password,
            "Timestamp" => $timestamp,
            "TransactionType" => $transaction_type,
            "Amount" => $amount,
            "PartyA" => $customer_mpesa_phone_number,
            "PartyB" => $party_b,
            "PhoneNumber" => $customer_mpesa_phone_number,
            "CallBackURL" => $callback_url,
            "AccountReference" => $ref_id,
            "TransactionDesc" => $description
        ];

        $success = false;
        $message = '';
        $id = '';

        try {

            $accessToken = $access_token ?? $this->getAccessToken();

            if (empty($accessToken))
                throw new \Exception(_l('error getting access code.'), 1);

            $query = $this->httpRequest($this->getUrl('stk_push'), [
                'headers' => [
                    "Authorization: Bearer $accessToken",
                    'Content-Type: application/json'
                ],
                'method' => 'POST',
                'data' => json_encode($mpesa_payload)
            ]);

            $success = ($query->ResponseCode ?? $query->responseCode ?? $query->errorCode) == 0;
            $message = $query->CustomerMessage ?? $query->responseDesc ?? $query->errorMessage;
            $id = $query->CheckoutRequestID ?? $query->responseId ?? $query->requestId;
        } catch (\Throwable $th) {

            if (is_callable($this->settings->logger)) {

                call_user_func($this->settings->logger, [$th->getMessage()]);
            }

            $message = $th->getMessage();
        }

        return (object) [
            'success' => $success,
            'message' => $message,
            'ref_id' => $id
        ];
    }

    /**
     * Query a transaction.
     * This allow to check the progress or status of a transaction
     *
     * @param string $ref_id
     * @param string $timestamp
     * @param string $accessToken
     * @return object
     */
    public function query(
        string $ref_id,
        string $timestamp,
        $access_token = null
    ) {

        $timestamp = $timestamp ?? date("YmdHis", time());

        $mpesa = $this->settings;
        $short_code = $mpesa->short_code;
        $stk_pass_key = $mpesa->stk_pass_key;
        $password = base64_encode($short_code . $stk_pass_key . $timestamp);

        $mpesa_payload = [
            "BusinessShortCode" => $short_code,
            "Password" => $password,
            "Timestamp" => $timestamp,
            "CheckoutRequestID" => $ref_id,
        ];

        $success = false;
        $message = '';
        $id = '';
        $mpesaReceiptNumber = '';

        try {

            $accessToken = $access_token ?? $this->getAccessToken();

            if (empty($accessToken))
                throw new \Exception(_l('error getting access code.'), 1);

            $query = $this->httpRequest($this->getUrl('query'), [
                'headers' => [
                    "Authorization: Bearer $accessToken",
                    'Content-Type: application/json'
                ],
                'method' => 'POST',
                'data' => json_encode($mpesa_payload)
            ]);

            $success = ($query->ResultCode ?? '') == 0 && !empty($query->MpesaReceiptNumber);
            $message = $query->ResultDesc ?? '';
            $id = $query->CheckoutRequestID ?? '';
            $mpesaReceiptNumber = $query->MpesaReceiptNumber ?? '';
        } catch (\Throwable $th) {

            if (is_callable($this->settings->logger)) {

                call_user_func($this->settings->logger, [$th->getMessage()]);
            }

            $message = $th->getMessage();
        }

        return (object) [
            'success' => $success,
            'message' => $message,
            'ref_id' => $id,
            'receipt_number' => $mpesaReceiptNumber
        ];
    }

    /**
     * Determine if webook callback notification is truly from mpesa
     *
     * @return boolean
     */
    public function isValidCallback()
    {

        //validate by ip whitelisting
        $validIP = $this->getIPAdress(true);

        if (empty($validIP))
            return false;

        return in_array($validIP, self::MPESA_LIVE_IP_ADDRESSES);
    }


    /**
     * Get ip address from request
     * 
     * Ref: https://www.javatpoint.com/how-to-get-the-ip-address-in-php
     *
     * @return string
     */
    public function getIPAdress($require_valid = false)
    {
        //whether ip is from the share internet  
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        //whether ip is from the proxy  
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        //whether ip is from the remote address  
        else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        if ($require_valid && !filter_var($ip, FILTER_VALIDATE_IP))
            return '';

        return $ip;
    }

    /**
     * make http request using curl
     *
     * @param string $url
     * @param array $options
     * @throws Exception
     * @return object
     */
    private function httpRequest($url, $options)
    {
        /* eCurl */
        $curl = curl_init($url);

        $verify_ssl = (int)($options['sslverify'] ?? 0);
        $timeout = (int)($options['timeout'] ?? 30);

        if ($options) {

            $method = strtoupper($options["method"] ?? "GET");

            /* Data */
            $data = @$options["data"];

            /* Headers */
            $headers = (array)@$options["headers"];

            /* Set JSON data to POST */
            if ($method === "POST") {
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }

            /* Define content type */
            if ($headers)
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYHOST => $verify_ssl,
            CURLOPT_TIMEOUT        => (int)$timeout,
        ]);


        /* make request */
        $result = curl_exec($curl);

        /* errro */
        $error  = '';

        if (!$curl || !$result) {
            $error = 'Curl Error - "' . curl_error($curl) . '" - Code: ' . curl_errno($curl);
            throw new \Exception($error, 1);
        }

        /* close curl */
        curl_close($curl);

        return (object)json_decode($result);
    }
}