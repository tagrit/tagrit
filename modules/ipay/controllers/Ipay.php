<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ipay extends App_Controller
{

  protected $ipay_url = 'https://payments.ipayafrica.com/v3/ke';

  public function make_payment($invoiceid, $hash)
  {
    check_invoice_restrictions($invoiceid, $hash);

    $this->load->model('invoices_model');
    $invoice = $this->invoices_model->get($invoiceid);

    load_client_language($invoice->clientid);

    $data['invoice'] = $invoice;
    $data['total']   = $this->session->userdata('ipay_total');

    //Ipay Params:
    $live = $this->ipay_gateway->getSetting('test_mode_enabled') == '1' ? '0' : '1';
    $mm   = 1;
    $mb   = 1;
    $dc   = 1;
    $cc   = 1;


      if (is_client_logged_in()) {
        $contact = $this->clients_model->get_contact(get_contact_user_id());
      } else {
        if (total_rows(db_prefix().'contacts', ['userid' => $invoice->clientid]) == 1) {
          $contact = $this->clients_model->get_contact(get_primary_contact_user_id($invoice->clientid));
        }
      }

      if (isset($contact) && $contact) {
        $data['firstname']   = $contact->firstname;
        $data['lastname']    = $contact->lastname;
        $data['email']       = $contact->email;
        $data['phonenumber'] = $contact->phonenumber;
      }


    if (!empty($invoice)) {
          
          //Ipay Params:
          $oid  = $invoice->id;
          $inv  = format_invoice_number($invoice->id);
          $ttl  = $data['total'];
          $tel  = $data['phonenumber'];
          $eml  = $data['email'];


      /**
       * incase of any dashes in the telephone number the code below removes them
       * @var [string]
       */
      $tel  = str_replace("-", "", $tel);
      $tel  = str_replace( array(' ', '<', '>', '&', '{', '}', '*', "+", '!', '@', '#', "$", '%', '^', '&'), "", $tel );
      
      $vid  = $this->ipay_gateway->getSetting('vendor_id');
      
      $curr = $this->ipay_gateway->getSetting('currencies');
      
      /**
       * $p1, $p2, $p3, $p4  are optional fields. Allow sending & receiving your custom parameters
       * Each option should not exceed 20 characters.
       */
      $p1   = '';
      $p2   = '';
      $p3   = ''; 
      $p4   = '';
      

      /**
       * [$callbk holds the callback URL]
       */
      $callbk = site_url('ipay/callback/'.$invoice->id.'/'.$invoice->hash);
      
      $cst  = 1;
      $crl  = 0;
      $autopay  = 1;

      
      /**
       * [$hsh holds the merchant's secret key]
       */
      $hsh = $this->ipay_gateway->getSetting('security_key');
      
      //The data string values
      $datastring =$live.$oid.$inv.$ttl.$tel.$eml.$vid.$curr.$p1.$p2.$p3.$p4.$callbk.$cst.$crl;

      //Setting the hashing algorithm to SHA1
      $hashid = hash_hmac('sha1', $datastring, $hsh);

      /*URLENCODE*/
      $cbk = urlencode($callbk);

      $data['url'] = $this->ipay_url.'?live='.$live.'&oid='.$oid.'&inv='.$inv.'&ttl='.$ttl.'&tel='.$tel.'&eml='.$eml.'&vid='.$vid.'&p1='.$p1.'&p2='.$p2.'&p3='.$p3.'&p4='.$p4.'&crl='.$crl.'&cbk='.$cbk.'&cst='.$cst.'&curr='.$curr.'&hsh='.$hashid.'&autopay='.$autopay;
          }

          echo $this->get_html($data);
        }

        public function get_html($data)
        { 
               ob_start(); ?>
               <?php echo payment_gateway_head(_l('payment_for_invoice') . ' ' . format_invoice_number($data['invoice']->id)); ?>
               <link rel="stylesheet" type="text/css" href="<?php echo module_dir_url('ipay', 'assets/ipay.css') ?>">
               <body class="gateway-pesapal">
                 <div class="container">
                  <div class="col-md-8 col-md-offset-2 mtop30">
                   <div class="mbot30 text-center">
                    <?php echo payment_gateway_logo(); ?>
                  </div>
                  <div class="row">
                    <div class="panel_s">
                     <div class="panel-body">
                      <h3 class="no-margin">
                       <b><?php echo _l('payment_for_invoice'); ?> </b>
                       <a href="<?php echo site_url('invoice/' . $data['invoice']->id . '/' . $data['invoice']->hash); ?>">
                         <b><?php echo format_invoice_number($data['invoice']->id); ?></b>
                       </a>
                     </h3>
                     <h4><?php echo _l('payment_total', app_format_money($data['total'], $data['invoice']->currency_name)); ?></h4>
                     <hr />
                     <form action="<?= $data['url'] ?>" method="post">
                      <div class="row">
                        <div class="col-md-12">
                          <img src="<?php echo module_dir_url('ipay', 'assets/ipay.png'); ?>" class="img_center">
                        </div>
                      </div>
                          <div class="buttons">
                              <div class="pull-right">
                                  <input type="submit" value="Continue" class="btn btn-warning" />
                              </div>
                          </div>
                      </form>
                   </div>
                 </div>
                   </div>
                 </div>
               </div>
               <?php echo payment_gateway_scripts(); ?>
               <?php echo payment_gateway_footer(); ?>
               <?php
               $contents = ob_get_contents();
               ob_end_clean();

               return $contents;
      }


  public function callback($invoiceid, $hash){
    
    check_invoice_restrictions($invoiceid, $hash);

    $this->load->model('invoices_model');
    $invoice = $this->invoices_model->get($invoiceid);

    load_client_language($invoice->clientid);

    /**
     * these values below are picked from the incoming URL and assigned to variables
     * that wewill use in our security check URL
     *
     * The value of the parameter “vendor”, in the url being opened above, is your iPay assigned Vendor ID
     */
    $request  = 'vendor='.urlencode($this->ipay_gateway->getSetting('vendor_id'));
    $request  .= '&id='.urlencode($this->input->get('id', TRUE));
    $request  .= '&ivm='.urlencode($this->input->get('ivm', TRUE));
    $request  .= '&qwh='.urlencode($this->input->get('qwh', TRUE));
    $request  .= '&afd='.urlencode($this->input->get('afd', TRUE));
    $request  .= '&poi='.urlencode($this->input->get('poi', TRUE));
    $request  .= '&uyt='.urlencode($this->input->get('uyt', TRUE));
    $request  .= '&ifd='.urlencode($this->input->get('ifd', TRUE));


    $ipnurl   = "https://www.ipayafrica.com/ipn/?".$request;


    $paymentmethod = $this->input->get('channel', TRUE);


    /**
     * If the payment mode is LIVE, it gets the payment status.
     * If the plugin is on test mode, it always gives a successful response.
     */
    if($this->ipay_gateway->getSetting('test_mode_enabled') === '0'){

      $fp     = fopen($ipnurl, "rb");
      $response = stream_get_contents($fp, -1, -1);
      fclose($fp);

    }elseif($this->ipay_gateway->getSetting('test_mode_enabled') === '1'){
      $response   = "aei7p7yrx4ae34";
    }

   
    /**
     * Success
     * The transaction is valid. Therefore you can update this transaction.
     */
    if ($response === 'aei7p7yrx4ae34') {
       
              $success = $this->ipay_gateway->addPayment(
                [
                  'amount'        => $this->input->get('mc', TRUE),
                  'invoiceid'     => $invoiceid,
                  'transactionid' => $this->input->get('txncd', TRUE),
                  'paymentmethod' => $paymentmethod,//$hashInfo['transaction_mode'],
                  ]
                );
                if ($success) {
                    set_alert('success', _l('online_payment_recorded_success'));
                } else {
                    set_alert('danger', _l('online_payment_recorded_success_fail_database'));
                }

    } 


    /**
     * Pending
     * Incoming Mobile Money Transaction Not found. The user should try again after 5 Minutes.
     */
    elseif ($response === 'bdi6p2yy76etrs') {
       set_alert("warning", "Incoming Mobile Money Transaction Not found.");      
    }


    /**
     * Used:
     * This code has been used already. A notification of this transaction sent to the merchant.
     */
    elseif ($response === 'cr5i3pgy9867e1') {
      set_alert("danger", "This code has been used already.");
    }


    /**
     * More
     * The amount that you have sent via mobile money is MORE than what was required to validate this transaction
     */
    elseif ($response === 'eq3i7p5yt7645e') {

              $success = $this->ipay_gateway->addPayment(
                [
                  'amount'        => $this->input->get('mc', TRUE),
                  'invoiceid'     => $invoiceid,
                  'transactionid' => $this->input->get('txncd', TRUE),
                  'paymentmethod' => $paymentmethod,
                  ]
                );
                if ($success) {      
                  set_alert("warning", _l('online_payment_recorded_success'). ", But the amount that you have sent via mobile money is MORE than what was required to validate this transaction.");
                } else {
                    set_alert('danger', _l('online_payment_recorded_success_fail_database'));
                }     
    
    }


    /**
     * Less
     * The amount that you have sent via mobile money is LESS than what was required to validate
     */
    elseif ($response === 'dtfi4p7yty45wq') {

      set_alert("warning", " Less: The amount that you have sent via mobile money is LESS than what was required to validate this transaction. "); 
    }


    /**
     * Failed transaction
     * Not all parameters fulfilled. A notification of this transaction sent to the merchant.
     */
    elseif ($response === 'fe2707etr5s4wq') {
      set_alert('danger', _l('online_payment_recorded_success_fail_database'));
    }

        $this->session->unset_userdata('ipay_total');
        redirect(site_url('invoice/' . $invoiceid . '/' . $hash));
  }
}
