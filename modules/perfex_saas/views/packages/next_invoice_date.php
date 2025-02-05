<?php
$next_date = false;

if (!empty($invoice->next_billing_cycle)) {
    $next_date = is_numeric($invoice->next_billing_cycle) ? date('Y-m-d', $invoice->next_billing_cycle) : $invoice->next_billing_cycle;
} else if (empty($invoice->subscription_id)) {
    if ($invoice->recurring > 0 || $invoice->is_recurring_from != null || $invoice->cycles == 0 || $invoice->cycles != $invoice->total_cycles) {
        $next_date = perfex_saas_get_recurring_invoice_next_date($invoice);
    }
}

if (!empty($invoice->subscription_ends_at)) $next_date = false;
?>

<?php
if (!empty($next_date)) {
    $datediff = strtotime($next_date) - time();
    $next_days_left = ($datediff / (60 * 60 * 24));
    echo '<a class="text-center" href="' . base_url(perfex_saas_get_invoice_payment_endpoint($invoice)) . '">';
    echo '<div class="mbot10"><span class="label label-' . ($next_days_left < 5 ? 'warning' : 'success') . ' tw-ml-3" data-toggle="tooltip" data-title="' . _l('perfex_saas_view_subscription_invoice') . '"><i class="fa-regular fa-eye fa-fw tw-mr-1"></i> ' . _l('next_invoice_date', '&nbsp;<b>' . e(_d($next_date)) . '</b>') . '</span></div>';
    echo '</a>';
}
?>