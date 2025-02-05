<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="mtop15 preview-top-wrapper">
    <div class="row">
        <div class="col-md-3">
            <div class="mbot30">
                <div class="subscription-html-logo">
                    <?= get_dark_company_logo(); ?>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="top" data-sticky data-sticky-class="preview-sticky-header">
        <div class="container preview-sticky-container">
            <div class="sm:tw-flex sm:tw-justify-between -tw-mx-4">
                <div class="sm:tw-self-end">
                    <h4 class="bold tw-my-0 subscription-html-name">
                        <?= e($subscription->name); ?></h4>
                    <div class="proposal-html-description">
                        <?= process_text_content_for_display($subscription->description); ?>
                    </div>
                </div>
                <div class="tw-flex tw-items-end tw-space-x-2 tw-mt-3 sm:tw-mt-0">
                    <?php
          if (! empty($publishableKey)) {
              if (empty($subscription->stripe_subscription_id)) {
                  echo form_open(site_url('subscription/subscribe/' . $hash), ['class' => 'action-button', 'id' => 'sourceForm', 'onsubmit' => 'document.getElementById(\'subscribe-button\').setAttribute(\'disabled\', true);']);
                  echo '<button type="submit" name="subscribe" id="subscribe-button" value="true" class="btn btn-success action-button">';
                  echo _l('subscribe');
                  echo '</button>';
                  echo form_close();
              } elseif (isset($stripeSubscription) && $stripeSubscription->status === 'incomplete') {
                  echo '<a href="' . $stripeSubscription->latest_invoice->hosted_invoice_url . '" class="btn btn-primary">' . _l('subscription_complete_payment') . '</a>';
              }
          }
if (can_logged_in_contact_view_subscriptions()) {
    ?>
                    <a href="<?= site_url('clients/subscriptions/'); ?>"
                        class="btn btn-default action-button go-to-portal">
                        <?= _l('client_go_to_dashboard'); ?>
                    </a>
                    <?php
} ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="clearfix"></div>
<div class="panel_s tw-mt-6">
    <div class="panel-body">
        <div class="col-md-10 col-md-offset-1">
            <div class="row mtop20">
                <div class="col-md-6 col-sm-6 transaction-html-info-col-left">
                    <address class="subscription-html-company-info tw-text-neutral-500 tw-text-normal">
                        <?= format_organization_info(); ?>
                    </address>
                </div>
                <div class="col-sm-6 text-right transaction-html-info-col-right">
                    <span
                        class="bold subscription-html-bill-to tw-text-neutral-700"><?= _l('invoice_bill_to'); ?></span>
                    <address class="subscription-html-customer-billing-info tw-text-neutral-500 tw-text-normal">
                        <?= format_customer_info($invoice, 'invoice', 'billing'); ?>
                    </address>
                    <!-- shipping details -->
                    <?php if (isset($invoice->include_shipping) && $invoice->include_shipping == 1 && $invoice->show_shipping_on_invoice == 1) { ?>
                    <span
                        class="bold subscription-html-ship-to tw-text-neutral-700"><?= _l('ship_to'); ?></span>
                    <address class="subscription-html-customer-shipping-info tw-text-neutral-500 tw-text-normal">
                        <?= format_customer_info($invoice, 'invoice', 'shipping'); ?>
                    </address>
                    <?php } ?>
                    <p class="tw-mb-0 tw-text-normal subscription-number">
                        <span class="tw-font-medium tw-text-neutral-700">
                            <?= _l('subscription'); ?>
                            #:
                        </span>
                        <?= e($subscription->id); ?>
                    </p>

                    <p class="tw-mb-0 tw-text-normal subscription-date">
                        <span class="tw-font-medium tw-text-neutral-700">
                            <?= _l('subscription_date'); ?>:
                        </span>
                        <?= e(! empty($subscription->stripe_subscription_id) && ! empty($subscription->date_subscribed)
                   // late webhook check
                   ? _d(date('Y-m-d', strtotime($subscription->date_subscribed)))
                   : _d(date('Y-m-d')));
?>
                    </p>

                    <?php if (! empty($subscription->date)) { ?>
                    <p class="tw-mb-0 tw-text-normal subscription-first-billing-date">
                        <span class="tw-font-medium tw-text-neutral-700">
                            <?= _l('first_billing_date'); ?>:
                        </span>
                        <?php if (! empty($subscription->stripe_subscription_id)) {
                            echo e(_d($subscription->date));
                        } else {
                            if ($subscription->date <= date('Y-m-d')) {
                                echo e(_d(date('Y-m-d')));
                            } else {
                                echo e(_d($subscription->date));
                            }
                        } ?>
                    </p>
                    <?php } ?>
                    <?php if ($invoice->project_id && get_option('show_project_on_invoice') == 1) { ?>
                    <p class="tw-mb-0 tw-text-normal subscription-project">
                        <span
                            class="tw-font-medium tw-text-neutral-700"><?= _l('project'); ?>:</span>
                        <?= e(get_project_name_by_id($invoice->project_id)); ?>
                    </p>
                    <?php } ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <?php
                        $items = get_items_table_data($invoice, 'invoice');
echo $items->table();
?>
                    </div>
                </div>
                <div class="col-md-6 col-md-offset-6">
                    <table class="table text-right tw-text-normal">
                        <tbody>
                            <tr id="subtotal">
                                <td>
                                    <span
                                        class="bold tw-text-neutral-700"><?= _l('invoice_subtotal'); ?></span>
                                </td>
                                <td class="subtotal">
                                    <?= e(app_format_money($invoice->subtotal, $invoice->currency_name)); ?>
                                </td>
                            </tr>
                            <?php
        foreach ($items->taxes() as $tax) {
            echo '<tr class="tax-area"><td class="bold !tw-text-neutral-700">' . e($tax['taxname']) . ' (' . e(app_format_number($tax['taxrate'])) . '%)</td><td>' . e(app_format_money($tax['total_tax'], $invoice->currency_name)) . '</td></tr>';
        }
?>
                            <tr>
                                <td><span
                                        class="bold tw-text-neutral-700"><?= _l('invoice_total'); ?></span>
                                </td>
                                <td class="total">
                                    <?= e(app_format_money($invoice->total, $invoice->currency_name)); ?>
                                </td>
                            </tr>
                            <?php if (get_option('show_amount_due_on_invoice') == 1
                  && $invoice->status != Invoices_model::STATUS_CANCELLED
                  && empty($subscription->stripe_subscription_id)) {
                                ?>
                            <tr>
                                <td>
                                    <span
                                        class="<?= $invoice->total_left_to_pay > 0 ? 'text-danger ' : ''; ?> bold">
                                        <?= _l('invoice_amount_due'); ?>
                                    </span>
                                </td>
                                <td>
                                    <span
                                        class="<?= $invoice->total_left_to_pay > 0 ? 'text-danger ' : ''; ?>">
                                        <?= e(app_format_money($invoice->total_left_to_pay, $invoice->currency_name)); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php
                            } ?>
                        </tbody>
                    </table>
                </div>
                <?php if (! empty($invoice->clientnote)) {
                    ?>
                <div class="col-md-12 subscription-html-note">
                    <b><?= _l('invoice_note'); ?></b><br /><br /><?= e($invoice->clientnote); ?>
                </div>
                <?php
                } ?>
                <?php if (! empty($invoice->terms) || ! empty($subscription->terms)) { ?>
                <div class="col-md-12 subscription-html-terms-and-conditions">
                    <hr />
                    <b><?= _l('terms_and_conditions'); ?></b><br /><br />
                    <?= process_text_content_for_display(empty($subscription->terms)
                       ? $invoice->terms
                       : $subscription->terms);
                    ?>
                </div>
                <?php } ?>
                <?php if (count($child_invoices) > 0) { ?>
                <div class="col-md-12 subscription-child-invoices">
                    <hr />
                    <b><?= _l('invoices'); ?></b>
                    <br />
                    <table class="table">
                        <thead>
                            <tr>
                                <th><?= _l('invoice_add_edit_number'); ?>
                                </th>
                                <th><?= _l('invoice_dt_table_heading_date'); ?>
                                </th>
                                <th><?= _l('invoice_total'); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($child_invoices as $child_invoice) { ?>
                            <tr>
                                <td>
                                    <a href="<?= site_url('invoice/' . $child_invoice->id . '/' . $child_invoice->hash); ?>"
                                        target="_blank">
                                        <?= e(format_invoice_number($child_invoice->id)); ?>
                                    </a>
                                </td>
                                <td><?= e($child_invoice->date); ?>
                                </td>
                                <td><?= e(app_format_money($child_invoice->total, $child_invoice->currency_name)); ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        new Sticky('[data-sticky]');
    });
</script>