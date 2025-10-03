<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<link href="<?= module_dir_url(MPESA_GATEWAY_MODULE_NAME, 'assets/mpesa.css'); ?>" rel="stylesheet" type="text/css" />
<script src="<?= module_dir_url(MPESA_GATEWAY_MODULE_NAME, 'assets/mpesa.js'); ?>"></script>

<?php
$usdToKes = (float)($ci->mpesa_gateway->getSetting('usd_to_kes') ?? 0);

$clientPhone = '';
if (is_client_logged_in()) {
    $contact_id = get_contact_user_id();
    $clientPhone  = get_instance()->db->get_where(db_prefix() . 'contacts', ['id' => $contact_id])->row('phonenumber');
    if (empty($clientPhone)) {
        // get company details
        $client_id = get_client_user_id();
        $clientPhone  = get_instance()->db->get_where(db_prefix() . 'clients', ['userid' => $client_id])->row('phonenumber');
    }
}
?>
<!-- mpesa modal -->
<div class="modal fade justify-content-center align-items-end align-items-md-center" id="mpesa-modal" tabindex="-1"
    role="dialog">
    <div class="modal-dialog modal-sm m-0">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" aria-label="Close" onclick="mpesaPay.closeModal()"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= _l('mpesa'); ?></h4>
            </div>
            <div class="modal-body mb-10">


                <div class="form-group">
                    <label class="control-label" for="example-input-normal"><?= _l('phone_number') ?></label>
                    <input type="text" name="mpesa_phone_number" value="<?= $clientPhone; ?>" class="form-control"
                        placeholder="+254712345678" required>
                </div>

                <div id="mpesa-timer" class="d-none text-center">
                    <span class="rouned loading primary hourglass"></span>
                    <p class="mt-2">
                        <span id="mpesa-amount-2" class="font-weight-bold"></span><br />
                        <?= _l('waiting_mpesa_pin_prompt'); ?>
                    </p>

                    <span id="time" class="font-weight-bold"></span> remains
                </div>
                <button type="button" class="btn btn-primary btn-block" id="pay-with-stk-push"
                    onclick="mpesaPay.pay_with_stk_push();" disabled>Pay <span id="mpesa-amount"></span></button>

                <p id="conversion-block" class="mt-2"></p>
            </div>
        </div>
    </div>
</div>

<script>
"use strict";

let currencyCode = '<?= $invoice->currency_name; ?>';
let oneUsdInKes = <?= $usdToKes; ?>;
let pgId = "<?= $ci->mpesa_gateway->getId(); ?>";

// Initiate mpesa
$(function() {
    $("#online_payment_form").on('submit', function(e) {
        if ($("[name=paymentmode]:checked").val() == pgId) {
            e.preventDefault();
            pay_with_mpesa_stk_push(
                "#online_payment_form",
                "<?= base_url('mpesa_gateway/process/verify/') ?>",
                currencyCode,
                oneUsdInKes,
                "<?= $invoice->total_left_to_pay; ?>"
            );
        }
    })
});
</script>