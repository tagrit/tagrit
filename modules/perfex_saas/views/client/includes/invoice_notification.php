<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
if (!empty($invoice) && $invoice->is_private != '1') {
    $target = '';

    $can_show_trail_note = $on_trial;
    if ($on_trial) {
        $trail_note_show_period = (int)(perfex_saas_is_tenant() ?
            perfex_saas_tenant_get_super_option('perfex_saas_trial_notification_day') :
            get_option('perfex_saas_trial_notification_day'));

        $can_show_trail_note = $trail_note_show_period >= 0;
        if ($trail_note_show_period > 0) {
            $can_show_trail_note = $days_left <= $trail_note_show_period;
        }
    }


    $subscribe_url_endpoint = 'clients/packages/' . $invoice->slug . '/select';
    $subscribe_url = perfex_saas_default_base_url($subscribe_url_endpoint);

    $invoice_pay_endpoint = perfex_saas_get_invoice_payment_endpoint($invoice);
    $invoice_pay_url = perfex_saas_default_base_url($invoice_pay_endpoint);

    if (perfex_saas_is_tenant()) {
        $target = 'target="_blank"';

        $client_bridge = perfex_saas_tenant_is_enabled('client_bridge');
        if ($client_bridge) {
            $target = '';
            $base_url = admin_url('billing/my_account?redirect=');
            $subscribe_url = $base_url . $subscribe_url_endpoint;
            $invoice_pay_url = $base_url . $invoice_pay_url;
        }
    }
?>

<!-- Invoice notification -->
<div class="ps <?= perfex_saas_is_tenant() ? 'col-md-12' : ''; ?>">
    <?php if ($on_trial && $can_show_trail_note) : ?>
    <div class="alert alert-<?= $days_left > 0 ? 'warning' : 'danger'; ?>  tw-mt-5">

        <?php if ($days_left > 0) : ?>
        <?= perfex_saas_is_single_package_mode() ?
                        _l('perfex_saas_trial_invoice_not_single_pricing', [$invoice_days_left]) :
                        _l('perfex_saas_trial_invoice_not', [$invoice->name, _d($invoice->duedate), $invoice_days_left]);
                    ?>
        <?php else : ?>
        <?= _l('perfex_saas_trial_invoice_over_not'); ?>
        <?php endif; ?>

        <a onclick="return confirm('<?= perfex_saas_ecape_js_attr(_l('perfex_saas_upgrade_confirm_text')); ?>')"
            href="<?= $subscribe_url; ?>" class="fs-5 text-danger" <?= $target; ?>>
            <?= _l('perfex_saas_click_here_to_subscribe'); ?>
        </a>
    </div>
    <?php endif; ?>

    <?php if (!$on_trial && !in_array($invoice->status, [Invoices_model::STATUS_PAID, Invoices_model::STATUS_DRAFT])) :
        ?>
    <div class="alert alert-danger tw-mt-5">
        <?= _l('perfex_saas_outstanding_invoice_not'); ?> <a href="<?= $invoice_pay_url; ?>"
            <?= $target; ?>><?= _l('perfex_saas_click_here_to_pay'); ?></a>
    </div>
    <?php endif ?>
</div>
<?php } ?>


<!-- Cancelled invoice notification -->
<?php
if ((empty($invoice) || (!empty($invoice->subscription_id) && $invoice->subscription_status != 'active')) && !perfex_saas_is_tenant() && (int)get_option('perfex_saas_allow_customer_cancel_subscription')) {
    $_cancelled_invoice = get_instance()->perfex_saas_model->get_company_cancelled_invoice(get_client_user_id());
    if ($_cancelled_invoice) {
        $message = _l('perfex_saas_cancelled_subscription_client_not', base_url('clients/my_account/resume_subscription'));
        if (!empty($message) && $message !== 'perfex_saas_cancelled_subscription_client_not') {
?>
<div class="ps">
    <div class="alert alert-danger tw-mt-5">
        <?= $message; ?>
    </div>
</div>
<?php
        }
    }
};
?>