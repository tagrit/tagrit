<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller for making and verify payment through Mpesa gateway.
 */

class Process extends App_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('invoices_model');
    }

    /**
     * Method for making a payment.
     *
     * @return mixed
     */
    public function make_payment()
    {
        $data = $this->session->userdata('payment_data');
        $invoice = $data['invoice'];
        $amount = (float)$data['amount'];
        $customer_mpesa_phone_number = $data["phone_number"];

        //ensure phone number is provided.
        if (empty($customer_mpesa_phone_number)) {
            $error = 'invalid phone number';
            if ($this->input->is_ajax_request()) {
                echo json_encode(['message' => $error]);
                exit;
            }

            //notify and redirect back
            $this->session->set_flashdata($error, true);
            return redirect(site_url('invoice/' . $invoice->id . '/' . $invoice->hash));
        }


        try {
            //ensure amount is in kes
            $amount = $this->mpesa_gateway->get_amount_in_kes($amount, $invoice->currency_name);

            check_invoice_restrictions($data['invoiceid'], $data['hash']);
            $invoice = $data['invoice'];
            load_client_language($invoice->clientid);

            $mpesa = $this->mpesa_gateway->getClient();
            $timestamp = date("YmdHis", time());

            $callback_url = site_url('mpesa_gateway/process/webhook', 'https');
            $ref_id = $invoice->clientid . '|' . $timestamp;

            //send stk push to user
            $payment = $mpesa->stkPush(
                $amount,
                $ref_id,
                get_option('invoice_company_name') . format_invoice_number($invoice->id),
                $customer_mpesa_phone_number,
                $callback_url,
                $timestamp
            );

            //STK push sent to users phone successfully
            if ($payment->success) {

                $payment_ref = $payment->ref_id;

                //ensure the success ref is not sent to client, this is part of a protective measure since mpesa dont have means of signing webhook
                unset($payment->ref_id);

                //log to db
                if ($this->db->insert('mpesa_gateway_transactions', [
                    'invoice_id' => $invoice->id,
                    'ref_id' => $payment_ref,
                    'status' => 'pending',
                    'phone' => $customer_mpesa_phone_number,
                    'amount' => $amount,
                    'timestamp' => $timestamp
                ]))
                    $payment->ref_id = $this->db->insert_id();
            }

            //return response to UI for better experience
            echo json_encode($payment);
            exit;
        } catch (\Throwable $th) {

            echo json_encode(['message' => $th->getMessage()]);
            exit;
        }
    }


    /**
     * Verify a transaction by its log id
     *
     * @param string $txnId
     * @param string $redirect
     * @param string $scenario
     * @return void
     */
    function verify($txnId, $redirect = '', $scenario = '')
    {

        $log = $this->db->where('id', $txnId)->get('mpesa_gateway_transactions')->row();

        $id = null;
        $success = false;
        $requery = false;

        if ($log) {

            $status = $log->status;
            if ($status !== "pending") {
                $id = $log->id;
            } else {
                $requery = true;
            }

            if ($scenario === 'admin') $requery = true;

            if ($requery) {

                //if pending, query transaction from daraja api directly
                $verification = $this->mpesa_gateway->getClient()->query($log->ref_id, $log->timestamp);

                if (!empty($verification->ref_id)) {

                    $log_update_data = [
                        'status' => $verification->success ? Invoices_model::STATUS_PAID : 'failed',
                        'receipt_number' => $verification->receipt_number,
                    ];

                    if (!empty($verification->message))
                        $log_update_data['description'] = $verification->message;

                    //update the payment log
                    $this->db->update(
                        'mpesa_gateway_transactions',
                        $log_update_data,
                        ['id' => $log->id]
                    );

                    //attach to invoice if success
                    if ($verification->success) {

                        $invoice = $this->invoices_model->get($log->invoice_id);

                        //convert amount back to invoice currency from KES
                        $amountInInvoiceCurrency = $this->mpesa_gateway->get_amount_in_currency($log->amount, $invoice->currency_name);

                        //fullfil if not yet done
                        $this->load->model('payments_model');
                        if (
                            !empty($verification->receipt_number) &&
                            !$this->payments_model->transaction_exists($verification->receipt_number)
                        )
                            $this->mpesa_gateway->addPayment(
                                [
                                    'amount'        => $amountInInvoiceCurrency,
                                    'invoiceid'     => $invoice->id,
                                    'transactionid' => $verification->receipt_number,
                                ]
                            );
                    }

                    $log = $this->db->where('id', $txnId)->get('mpesa_gateway_transactions')->row();
                    $id = $log->id;
                }
            }

            $success = $log->status == Invoices_model::STATUS_PAID;
        }

        //redirect
        if (!empty($redirect)) {
            set_alert($success ? 'success' : 'danger', $log->description);
            return redirect($_SERVER['HTTP_REFERER'] ?? admin_url(MPESA_GATEWAY_MODULE_NAME . '/logs'));
        }

        echo json_encode($id ? ['id' => $id, 'success' => $success, 'message' => $log->description] : []);
        exit();
    }


    /**
     * Function to listen to webhook callback from Mpesa.
     * It receive  the payload and execute "verify" method.
     *
     * @return mixed
     */
    function webhook()
    {
        //get the payload
        $payload = $this->input->raw_input_stream;
        log_message('debug', '🔔  Mpesa Webhook received! ' . $payload);

        $event = (object)json_decode($payload);

        if (isset($event->Body->stkCallback)) {

            log_message('debug', 'Executing hook: ' . $event->Body->stkCallback->ResultDesc);

            try {

                $mpesa = $this->mpesa_gateway->getClient();
                $stk = $event->Body->stkCallback;
                $transactionReference = $stk->CheckoutRequestID;

                //ensure the ref is in the log
                $log = $this->db->where('ref_id', $transactionReference)->get('mpesa_gateway_transactions')->row();
                if (!$log)
                    throw new \Exception("MpesaWH: Invalid payment reference", 1);

                //validate request is truly from mpesa
                if ($mpesa->settings->mode != "sandbox" && !$mpesa->isValidCallback()) {

                    $ip = $mpesa->getIPAdress();
                    throw new \Exception("Mpesa: Request source is unkown ($ip) for $transactionReference", 1);
                }

                return $this->verify($log->id);
            } catch (\Exception $e) {

                log_message('error', $e->getMessage());
                set_status_header(500);

                echo json_encode(['error' => $e->getMessage()]);
                exit();
            }
        }
    }
}