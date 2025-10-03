<?php

defined('BASEPATH') or exit('No direct script access allowed');
$CI = &get_instance();
$currency = get_base_currency();
$showing_subscribed_card = true;

$custom_repeat = $package->metadata->invoice->recurring == 'custom';
$interval = $custom_repeat ? $package->metadata->invoice->repeat_every_custom : $package->metadata->invoice->recurring;
$interval_type = $custom_repeat ? $package->metadata->invoice->repeat_type_custom . 's' : 'months';
$billing_cycle = strtolower(($interval > 1 ? $interval : '') . _l($interval > 1 ? "perfex_saas_" . $interval_type : "perfex_saas_" . $interval_type . '_singular'));
if (!empty($package->metadata->is_liftetime_deal))
    $billing_cycle = _l('perfex_saas_interval_lifetime');

$subscribed = !empty($invoice->{perfex_saas_column('packageid')}) && $invoice->{perfex_saas_column('packageid')} == $package->id;

$on_trial = perfex_saas_invoice_is_on_trial($invoice);

$days_left = '';
$invoice_days_left = '';

if (isset($invoice)) {
    // Convert the due date string to a DateTime object
    $_duedate = new DateTime($invoice->duedate);
    $now = new DateTime(); // Current date and time

    // Check if the due date is in the future
    if ($_duedate > $now) {
        // Calculate the number of days left if the invoice is on trial
        $days_left = $on_trial ? (int)$_duedate->diff($now)->days : '';
        $invoice_days_left = (int)$_duedate->diff($now)->days;
    }
}


$limitations = $package->metadata->limitations;
$discounts = $package->metadata->formatted_discounts ?? [];

if (isset($package->metadata->storage_limit->unit)) {
    $storage = (int)($package->metadata->storage_limit->size ?? 0);
    $limitations = array_merge(
        (array)$package->metadata->limitations ?? [],
        ['storage' => $storage === -1 ? _l('perfex_saas_unlimited') : $storage . ' ' . ($package->metadata->storage_limit->unit ?? 'B')],
    );
}

$extra_instance_unit_price = $package->metadata->limitations_unit_price->{'tenant_instance'} ?? '';
if ($extra_instance_unit_price !== '') {
    $max_instance_limit = (int)($package->metadata->max_instance_limit ?? 1);
    $limitations = array_merge(
        ['tenant_instance' => $max_instance_limit],
        (array)$limitations ?? [],
    );
}

$unlimited_resources = [];

$na = _l('perfex_saas_na');

$modules = $CI->perfex_saas_model->modules();
$purchased_modules = $invoice->purchased_modules;
$allow_module_request = get_option('perfex_saas_enable_custom_module_request') == "1";
$module_request_url = get_option('perfex_saas_custom_module_request_form');
$module_request_url = empty($module_request_url) ? site_url('clients/open_ticket?request_custom_module&title=' . _l('perfex_saas_custom_module_request')) : $module_request_url;
$allow_module_marketplace = ($package->metadata->disable_module_marketplace ?? '') !== 'yes';

$services = $CI->perfex_saas_model->services();
$allow_service_request = get_option('perfex_saas_enable_custom_service_request') == "1";
$service_request_url = get_option('perfex_saas_custom_service_request_form');
$service_request_url = empty($service_request_url) ? site_url('clients/open_ticket?request_custom_service&title=' . _l('perfex_saas_custom_service_request')) : $service_request_url;
$purchased_services = $invoice->purchased_services ?? [];
$allow_service_marketplace = ($package->metadata->disable_service_marketplace ?? '') !== 'yes';

$taxes = $package->metadata->invoice->taxname ?? [];

// Deferred drafted invoice.
if (!$on_trial && !empty($invoice->status) && $invoice->status == Invoices_model::STATUS_DRAFT) {
    if (!empty($invoice->duedate) && date('Y-m-d') >= $invoice->duedate) {
        update_invoice_status($invoice->id, true);
    }
}

$single_pricing_mode = perfex_saas_is_single_package_mode();
$can_customize = $single_pricing_mode || ($package->metadata->allow_customization ?? 'yes') !== 'no';
if ($can_customize && $invoice && $invoice->subscription_id && ($invoice->subscription_status != 'active' || !empty($invoice->subscription_ends_at)))
    $can_customize = false;
?>


<div class="panel ps">
    <div class="panel-body min-h-50-vh">
        <div class="tw-flex tw-flex-col tw-justify-center tw-items-center tw-mb-3">
            <h1 class="tw-mt-0">
                <?php if ($subscribed) : ?>
                    <i class="fa fa-check-circle text-success <?= perfex_saas_is_single_package_mode() ? 'fa-2x' : ''; ?>"></i>
                <?php endif; ?>
                <?php if (!perfex_saas_is_single_package_mode()) echo $package->name; ?>
            </h1>
            <div>
                <span class="tw-bg-neutral-700 tw-text-lg tw-text-white badge badge-primary tw-font-bold">
                    <?= app_format_money($package->price, $currency); ?>
                    <span data-subtotal></span>
                    <small class="text-lowercase">/ <?= $billing_cycle; ?></small>
                </span>
            </div>
            <?php if (!$on_trial) : ?>
                <?php require(__DIR__ . '/../packages/next_invoice_date.php'); ?>
            <?php endif; ?>
            <?php include(__DIR__ . '/includes/invoice_notification.php'); ?>
        </div>


        <?= form_open('', ['method' => 'POST']); ?>
        <div class="row">
            <div class="col-md-8 col-lg-9">
                <div class="table-responsive">
                    <table class="table table-bordered table-condensed tw-mb-1">
                        <thead>
                            <tr>
                                <th><?= _l('perfex_saas_feature_or_module'); ?></th>
                                <th><?= _l('perfex_saas_current_limit'); ?></th>
                                <th><?= _l('perfex_saas_extra_limit'); ?></th>
                                <th><?= _l('perfex_saas_unit_price'); ?>/<?= $billing_cycle; ?></th>
                                <th><?= _l('perfex_saas_price_addition'); ?>/<?= $billing_cycle; ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($limitations as $resources => $limit) :
                                $is_unlimited = (int)$limit === -1;
                                $is_storage = $resources === 'storage';
                                $is_unlimited_storage = $is_storage && $limit == _l('perfex_saas_unlimited');
                                $current_extra_limit = $invoice->custom_limits->{$resources} ?? '';
                                $unit_price = $package->metadata->limitations_unit_price->{$resources} ?? '';
                                if ($is_unlimited) {
                                    $unlimited_resources[] = _l('perfex_saas_limit_' . $resources);
                                    continue;
                                }

                                if ($is_storage) {
                                    if (!isset($package->metadata->storage_limit->unit_price))
                                        continue;

                                    $unit_price = $package->metadata->storage_limit->unit_price;
                                }

                                if ($unit_price === "") { // Price not set
                                    continue;
                                }

                                $unit_price = (float)$unit_price;
                            ?>
                                <tr>
                                    <td><?= _l('perfex_saas_limit_' . $resources); ?></td>
                                    <td><?= $limit; ?></td>
                                    <td>
                                        <?php if (!$is_unlimited && !$is_unlimited_storage) : ?>

                                            <div class="<?= $is_storage ? 'input-group' : ''; ?>">
                                                <input type="number" min="0" step="1" class="form-control feature-limit tw-p-1" name="custom_limits[<?= $resources; ?>]" value="<?= $current_extra_limit; ?>" data-unit-price="<?= $unit_price; ?>" data-id="<?= $resources; ?>">
                                                <?php if ($is_storage) : ?>
                                                    <span class="input-group-addon tw-px-1 tw-border-l-0"><?= $package->metadata->storage_limit->unit; ?></span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="base-price"><?= app_format_money($unit_price, $currency); ?></span>

                                        <!-- Display discount -->
                                        <?php if (!empty($discounts->{$resources})) : ?>
                                            <div class="tw-mt-1 tw-flex tw-flex-col text-info discount text-lowercase <?= $resources; ?>">
                                                <?php
                                                asort($discounts->{$resources});
                                                foreach ($discounts->{$resources} as $key => $value) : ?>
                                                    <span class="tw-text-xs <?= $resources . $key; ?>">
                                                        <?= $value['unit']; ?>+
                                                        <span class="tw-ml-1"><?= app_format_money($unit_price - (($unit_price * ((float)$value['percent']) / 100)), $currency); ?>
                                                            <sup>(<?= $value['percent']; ?>% off)</sup></span>
                                                    </span>
                                                <?php endforeach ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="price-addition" data-price="0"><?= app_format_money(0, $currency); ?></span>
                                    </td>
                                </tr>
                            <?php endforeach ?>

                            <!-- unlimited  resources -->

                            <?php
                            // Add free modules to unlimited resources list
                            if (!empty($package->modules)) :
                                foreach ($package->modules as $value) :
                                    $unlimited_resources[] = $modules[$value]['custom_name'] ?? $value;
                                endforeach;
                            endif;
                            ?>
                            <tr>
                                <td class="!tw-max-w-xs">
                                    <?= trim(implode(', ', $unlimited_resources)); ?></td>
                                <td><?= _l('perfex_saas_unlimited'); ?></td>
                                <td><?= $na; ?></td>
                                <td><?= app_format_money("0", $currency); ?></td>
                                <td><?= app_format_money("0", $currency); ?></td>
                            </tr>
                            <!-- Add more rows for other features -->

                            <!-- Add more rows for other free base modules -->
                            <?php if (!empty((array)$purchased_modules) || $allow_module_marketplace) : ?>
                                <tr>
                                    <td colspan="5" class="text-center">
                                        <div class="tw-flex tw-gap-3 tw-items-center tw-justify-between">
                                            <strong class="tw-mb-0 tw-text-base"><?= _l('perfex_saas_premium_modules'); ?></strong>
                                            <?php if ($allow_module_marketplace) : ?>
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#moduleModal">
                                                    <?= _l('perfex_saas_select_modules'); ?>
                                                </button>
                                            <?php endif ?>
                                        </div>
                                    </td>
                                </tr>

                                <tr id="paid-modules">
                                    <td colspan="5" class="text-center tw-border-0 !tw-p-0">
                                    </td>
                                </tr>
                            <?php endif ?>
                            <!-- end paid modules -->

                            <!-- paid services -->
                            <?php if (!empty((array)$purchased_services) || $allow_service_marketplace) : ?>
                                <tr>
                                    <td colspan="5" class="text-center">
                                        <div class="tw-flex tw-gap-3 tw-items-center tw-justify-between">
                                            <strong class="tw-mb-0 tw-text-base"><?= _l('perfex_saas_premium_services'); ?></strong>
                                            <?php if ($allow_service_marketplace) : ?>
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#serviceModal">
                                                    <?= _l('perfex_saas_select_services'); ?>
                                                </button>
                                            <?php endif ?>
                                        </div>
                                    </td>
                                </tr>

                                <tr id="paid-services">
                                    <td colspan="5" class="text-center tw-border-0 !tw-p-0">
                                    </td>
                                </tr>
                            <?php endif ?>
                            <!-- end paid services -->

                        </tbody>

                    </table>
                </div>
                <?php if (!empty((array)$purchased_services) || $allow_service_marketplace) : ?>
                    <p><?= _l('perfex_saas_package_custom_table_notice'); ?></p>
                <?php endif ?>
            </div>
            <div class="col-md-3">
                <div class="summary-card">
                    <div class="tw-bg-neutral-200 tw-p-1 tw-mb-4">
                        <table class="table table-condensed">
                            <tfoot>
                                <tr>
                                    <th colspan="4"><?= _l('perfex_saas_base_price'); ?></th>
                                    <td><span class="baseprice"><?= app_format_money($package->price, $currency); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th colspan="4"><?= _l('perfex_saas_subtotal'); ?></th>
                                    <td><span class="subtotal"><?= app_format_money('0.00', $currency); ?></span></td>
                                </tr>
                                <tr>
                                    <th colspan="4"><?= _l('perfex_saas_module_subtotal'); ?></th>
                                    <td><span class="modules-subtotal"><?= app_format_money('0.00', $currency); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th colspan="4"><?= _l('perfex_saas_service_subtotal'); ?></th>
                                    <td><span class="services-subtotal"><?= app_format_money('0.00', $currency); ?></span>
                                    </td>
                                </tr>
                                <?php if (!empty($taxes)) : ?>
                                    <?php
                                    foreach ($taxes as $key => $tax) :
                                        $tax = explode('|', $tax);
                                        $tax_amount = (float)end($tax);
                                    ?>
                                        <tr>
                                            <th colspan="4"><?= $tax[0]; ?> (<?= $tax_amount; ?>%)</th>
                                            <td><span class="tax-subtotal" data-percent="<?= $tax_amount; ?>"><?= app_format_money('0.00', $currency); ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <tr>
                                    <th colspan="4"><?= _l('perfex_saas_total'); ?></th>
                                    <td><strong><span id="total-amount"><?= app_format_money('0.00', $currency); ?> /
                                                <?= $billing_cycle; ?></span></strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="tw-flex tw-flex-col tw-gap-2 tw-justify-end tw-w-full">
                        <?php if ($can_customize) : ?>
                            <button class="btn btn-success tw-w-full" onclick="return confirm('<?= perfex_saas_ecape_js_attr(_l('perfex_saas_confirm_customize_package')); ?>');"><?= _l($on_trial ? 'perfex_saas_subscribe' : 'perfex_saas_update_subscription'); ?></button>
                        <?php endif; ?>
                        <?php if ((int)get_option('perfex_saas_allow_customer_cancel_subscription') && !$on_trial) : ?>
                            <?php require(__DIR__ . '/../includes/cancel_subscription_button.php'); ?>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>
        <?= form_close(); ?>
    </div>
</div>

<?php if (!$can_customize) : ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $(".panel.ps input, .panel.ps button").attr('disabled', 'disabled');
            $(".panel.ps form").on('submit', function(e) {
                e.preventDefault();
                return false;
            })
        });
    </script>
<?php endif; ?>

<script>
    const billingCycle = "<?= $billing_cycle; ?>";
    const basePrice = parseFloat(<?= $package->price; ?>);
    const discounts = <?= json_encode($discounts); ?>;

    const getDiscountedUnitPrice = (resources, unitPrice, newLimit) => {

        if (discounts?.[resources]) {
            let units = Object.keys(discounts[resources]).sort((a, b) => {
                return b - a
            });
            for (let index = 0; index < units.length; index++) {
                const level = parseInt(units[index]);

                document.querySelectorAll(`.discount.${resources} span`).forEach((v) => v.classList.remove('strike'));

                if (newLimit >= level) {
                    const discount = discounts[resources][units[index]];
                    const percent = parseFloat(discount.percent) / 100;
                    unitPrice = unitPrice - (percent * unitPrice);
                    document.querySelector(`.${resources+''+level}`).classList.add('strike');
                    break;
                }
            }
        }
        return unitPrice;
    }

    // JavaScript logic for calculating totals and managing modules
    const featureLimitInputs = document.querySelectorAll('.feature-limit');
    const unitPriceElements = document.querySelectorAll('td:nth-child(4)');
    const priceAdditionElements = document.querySelectorAll('.price-addition');
    const featureSubtotalElement = document.querySelector('.subtotal');
    const totalAmountElement = document.getElementById('total-amount');
    const taxElements = document.querySelectorAll('.tax-subtotal');


    // Totals
    let featureSubtotal = 0;
    let taxSubtotal = 0;

    const getTotalAmount = () => {
        let total = featureSubtotal;

        let totalElements = document.querySelectorAll('[data-market-group-total]');
        totalElements.forEach(element => {
            let subtotal = parseFloat(element.dataset.marketGroupTotal);
            total += subtotal;
        });
        return total;
    }

    const setTotalAmount = () => {
        let total = getTotalAmount() + basePrice;
        // Apply taxes
        taxSubtotal = 0;
        taxElements.forEach((tax) => {
            let taxAmount = (parseFloat(tax.dataset.percent) / 100) * total;
            taxSubtotal += taxAmount;
            tax.textContent = `${appFormatMoney.format(taxAmount)}`;
        });

        totalAmountElement.textContent =
            `${appFormatMoney.format(total+taxSubtotal)} / ${billingCycle}`;
    };
</script>

<?php require(__DIR__ . '/includes/modules.php'); ?>
<?php require(__DIR__ . '/includes/services.php'); ?>

<script>
    featureLimitInputs.forEach((input, index) => {
        input.addEventListener('input', () => {
            const resources = input.dataset.id;
            const newLimit = parseInt(input.dataset.quantity ?? (input.value.length ? input.value : "0"));
            let unitPrice = parseFloat(input.dataset.unitPrice);
            // get discounted price
            unitPrice = getDiscountedUnitPrice(resources, unitPrice, newLimit);

            const priceAddition = unitPrice * newLimit;
            priceAdditionElements[index].textContent = `${appFormatMoney.format(priceAddition)}`;
            priceAdditionElements[index].dataset.price = priceAddition;

            // Calculate feature subtotal
            featureSubtotal = 0;
            priceAdditionElements.forEach(element => {
                featureSubtotal += parseFloat(element.dataset.price);
            });

            featureSubtotalElement.textContent = `${appFormatMoney.format(featureSubtotal)}`;

            // Calculate total amount
            setTotalAmount();
        });
    });

    // Initiate total amount
    setTotalAmount();

    // Trigger summation of features using js
    document.querySelectorAll(".feature-limit").forEach((input) => input.dispatchEvent(new Event('input', {
        bubbles: true
    })));

    setTimeout(() => {
        document.querySelectorAll("[data-subtotal]").forEach((input) => input.textContent =
            ` + ${appFormatMoney.format(getTotalAmount())}`);
    }, 100);
</script>