<?php
defined('BASEPATH') or exit('No direct script access allowed');

$storage = (int)($package->metadata->storage_limit->size ?? 0);
$limitations = array_merge(
    (array)$package->metadata->limitations ?? [],
    ['storage' => $storage === -1 ? _l('perfex_saas_unlimited') : $storage . ' ' . ($package->metadata->storage_limit->unit ?? 'B')],
);


$disabled_default_modules = [];
if (!empty($invoice))
    $disabled_default_modules = perfex_saas_tenant_disabled_default_modules((object)['package_invoice' => $invoice]);
else
    $disabled_default_modules = perfex_saas_alias_disabled_default_modules((array)($package->metadata->disabled_default_modules ?? []));

$custom_repeat = $package->metadata->invoice->recurring == 'custom';
$interval = $custom_repeat ? $package->metadata->invoice->repeat_every_custom : $package->metadata->invoice->recurring;
$interval_type = $custom_repeat ? $package->metadata->invoice->repeat_type_custom . 's' : 'months';

$can_customize = $single_pricing_mode || ($package->metadata->allow_customization ?? 'yes') !== 'no';
if ($can_customize && !empty($invoice) && $invoice->subscription_id && ($invoice->subscription_status != 'active' || !empty($invoice->subscription_ends_at)))
    $can_customize = false;

$interval_title = $interval > 1 ? $interval : '';
if (!empty($package->metadata->is_liftetime_deal))
    $interval_title = _l('perfex_saas_interval_lifetime');
else
    $interval_title .= ' ' . _l($interval > 1 ? "perfex_saas_" . $interval_type : "perfex_saas_" . $interval_type . '_singular');

?>

<div class=" panel_s tw-p-4 tw-py-2
    <?= $package->is_default == '1' ? 'tw-bg-neutral-300' : 'tw-bg-neutral-100' ?> tw-flex tw-flex-col
    tw-justify-between"
    <?= str_starts_with($package_group, 'private_') ? '' : 'data-package-group="' . e($package_group ?? '') . '"'; ?>>
    <div class="panel_body tw-flex tw-flex-col tw-items-center tw-justify-center text-center">
        <h3>
            <?= $package->name; ?>
            <?php if ($package->is_private) : ?>
            <i class="fa fa-user-secret"></i>
            <?php endif; ?>
            <?php if ($subscribed) : ?>
            <i class="fa fa-check-circle text-success"></i>
            <?php endif; ?>
        </h3>
        <div>
            <span class="tw-bg-neutral-700 tw-text-lg tw-text-white badge badge-primary tw-font-bold">
                <?= app_format_money($package->price, $currency); ?>
                <small class="text-lowercase">/
                    <?= $interval_title; ?>
                </small>
            </span>
        </div>

        <div class="tw-mt-2 tw-mb-4"><?= @html_purify($package->description); ?></div>

        <!-- modules and limiation list -->
        <?php $module_display_option = ($package->metadata->show_modules_list ?? 'yes'); ?>
        <?php if ($module_display_option !== 'no') : ?>
        <div class="tw-flex tw-justify-center tw-w-full">
            <ul class="tw-grid tw-grid-cols-2 tw-gap-2">
                <?php
                    $key = 0;
                    $limit_display_option = $package->metadata->show_limits_on_package ?? 'yes_3';
                    if ($limit_display_option !== 'no' && !empty($limitations)) :
                        foreach ($limitations as $feature => $limit) :
                            if (in_array($feature, $disabled_default_modules)) continue;
                    ?>
                <li class="text-left text-capitalize <?= ($key + 1) % 2 ? 'tw-mr-2' : 'tw-ml-2'; ?>">
                    <?= $limit_display_option === "yes_2" ||  $limit_display_option === "yes_4" ? '<span><i class="fa fa-check"></i></span>' : ''; ?>
                    <?= $limit_display_option === "yes_2" ||  $limit_display_option === "yes_3" ? ((int)$limit === -1 ? _l('perfex_saas_unlimited') : $limit) : ''; ?>
                    <?= _l($feature, '', false); ?>
                </li>
                <?php $key++;
                        endforeach;
                    endif;
                    ?>

                <?php
                    if (!empty($package->modules)) :
                        $key = isset($key) ? ((int)$key + 1) : 0;
                        foreach ($package->modules as $key => $value) : ?>
                <li class="text-left text-capitalize <?= ((int)$key + 1) % 2 ? 'tw-mr-2' : 'tw-ml-2'; ?>">
                    <?= $module_display_option === "yes_4" ? '<span><i class="fa fa-check"></i></span>' : ''; ?>
                    <?= $modules[$value]['custom_name'] ?? $value; ?>
                </li>
                <?php
                        endforeach;
                    endif; ?>
            </ul>
        </div>
        <?php endif ?>
    </div>

    <!-- Package action -->
    <?php if ($is_client) : ?>

    <div class="panel_footer tw-flex tw-justify-center tw-mt-4">

        <?php if (empty($invoice) && $_can_trial) : ?>

        <a href="<?= base_url('clients/packages/' . $package->slug . '/select'); ?>"
            class="btn btn-primary tw-text-wrap">
            <?= $package->trial_period > 0 ? _l('perfex_saas_start_trial', $package->trial_period) : _l('perfex_saas_subscribe'); ?>
        </a>

        <?php elseif ($subscribed) : ?>

        <div class="tw-flex tw-flex-col">
            <?php if ($on_trial) : ?>

            <a href="<?= base_url('clients/packages/' . $invoice->slug . '/select'); ?>"
                class="btn btn-danger tw-text-wrap"
                <?php if ($on_trial) echo 'onclick="return confirm(\'' . perfex_saas_ecape_js_attr(_l('perfex_saas_upgrade_confirm_text')) . '\')"'; ?>>
                <i class="fa fa-check"></i>
                <?= $days_left > 0 ? _l('perfex_saas_view_subscription_invoice_trial', $days_left) : _l('perfex_saas_view_subscription_trial_over'); ?>
            </a>
            <?php else : ?>

            <?php require('next_invoice_date.php'); ?>

            <?php endif ?>
            <?php if ($can_customize) : ?>
            <a href="<?= base_url('clients/my_account'); ?>" class="btn btn-info mtop10">
                <i class="fa fa-cogs"></i>
                <?= _l('perfex_saas_pricing_customize'); ?>
            </a>
            <?php endif ?>

            <?php if ((int)get_option('perfex_saas_allow_customer_cancel_subscription') && !$on_trial) : ?>
            <?php require(__DIR__ . '/../includes/cancel_subscription_button.php'); ?>
            <?php endif ?>
        </div>

        <?php else : ?>

        <a onclick="return confirm('<?= perfex_saas_ecape_js_attr(_l('perfex_saas_upgrade_confirm_text')); ?>')"
            href="<?= base_url('clients/packages/' . $package->slug . '/select'); ?>" class="btn btn-primary">
            <?= empty($invoice) ? _l('perfex_saas_subscribe') : ($package->price >= ($invoice->price ?? 0) ? _l('perfex_saas_upgrade') : _l('perfex_saas_downgrade')); ?>
        </a>

        <?php endif ?>

    </div>
    <?php else : ?>
    <div class="panel_footer tw-flex tw-justify-between tw-mt-4">
        <div class="tw-flex  tw-space-x-2">
            <?php $stat = $CI->perfex_saas_model->package_stats((int)$package->id); ?>
            <?php if (in_array($package->db_scheme, ['single_pool', 'shard'])) : ?>
            <span data-title="<?= _l('perfex_saas_total_db_pools'); ?>" data-toggle="tooltip">
                <i class="fa fa-database"></i>
                <?= count($package->db_pools); ?>
            </span>

            <span data-title="<?= _l('perfex_saas_total_instances_on_pool'); ?>" data-toggle="tooltip">
                <i class="fa fa-users"></i>
                <?= $stat->total_pool_population; ?>
            </span>
            <?php endif; ?>

            <span data-title="<?= _l('perfex_saas_package_total_attached_invoices'); ?>" data-toggle="tooltip">
                <i class="fa fa-dollar"></i>
                <?= $stat->total_invoices; ?>
            </span>


        </div>
        <div class="tw-flex tw-space-x-2">

            <?php if (staff_can('create', 'perfex_saas_packages')) : ?>
            <!-- copy to clipboad -->
            <a href="#" data-success-text="<?= _l('perfex_saas_copied'); ?>"
                data-text="<?= site_url('authentication/register') . '?' . perfex_saas_route_id_prefix('plan') . '=' . $package->slug; ?>"
                onclick="return false;" data-toggle="tooltip"
                data-title="<?= _l('perfex_saas_package_copy_to_clipboard'); ?>"
                class="btn btn-secondary btn-xs copy-to-clipboard">
                <i class="fa fa-share-alt"></i>
            </a>
            <!-- clone -->
            <a href="<?= admin_url(PERFEX_SAAS_ROUTE_NAME . '/packages/clone/' . $package->id); ?>"
                data-toggle="tooltip" data-title="<?= _l('perfex_saas_clone'); ?>" class="btn btn-secondary btn-xs"><i
                    class="fa fa-copy"></i></a>
            <?php endif ?>

            <?php if (staff_can('edit', 'perfex_saas_packages')) : ?>
            <a href="<?= admin_url(PERFEX_SAAS_ROUTE_NAME . '/packages/edit/' . $package->id); ?>" data-toggle="tooltip"
                data-title="<?= _l('perfex_saas_edit'); ?>" class="btn btn-primary btn-xs"><i class="fa fa-pen"></i></a>
            <?php endif ?>

            <?php if (staff_can('delete', 'perfex_saas_packages')) : ?>
            <?= form_open(admin_url(PERFEX_SAAS_ROUTE_NAME . '/packages/delete')); ?>
            <?= form_hidden('id', $package->id); ?>
            <button class="btn btn-danger btn-xs  _delete" data-toggle="tooltip"
                data-title="<?= _l('perfex_saas_delete'); ?>"><i class="fa fa-trash"></i></button>
            <?= form_close(); ?>
            <?php endif ?>

        </div>
    </div>
    <?php endif; ?>
</div>