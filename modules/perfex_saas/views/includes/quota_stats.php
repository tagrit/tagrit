<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$tenant = !empty($tenant) ? $tenant : perfex_saas_tenant();
$package_quota = $tenant->package_invoice->metadata->limitations ?? [];
$usage_limits = perfex_saas_get_tenant_quota_usage($tenant);
$display  = $tenant->package_invoice->metadata->dashboard_quota_visibility ?? '';
if ($display === 'no-for-all') return;
if (is_admin() && $display === "no" &&  $tenant->package_invoice->metadata->client_theme !== "agency") return;
$disabled_default_modules = perfex_saas_tenant_disabled_default_modules($tenant);

$hidden_quota_widgets = $tenant->package_invoice->metadata->hidden_quota_widgets ?? [];
?>

<div class="widget relative tw-mb-4" id="widget-saas_top_stats"
    data-name="<?= _l('perfex_saas_tenant_quota_dashboard'); ?>">
    <div class="widget-dragger ui-sortable-handle"></div>

    <div class="tw-flex tw-justify-between tw-items-center">

        <h4><?= _l('perfex_saas_tenant_quota_dashboard'); ?></h4>

        <!-- customize package quota accessibility link -->
        <?php if (perfex_saas_is_single_package_mode() || ($tenant->package_invoice->metadata->allow_customization ?? 'yes') !== 'no') {
            $target = '';
            $url = base_url('clients/my_account');
            if (perfex_saas_is_tenant()) {
                $target = '_blank';
                $url = perfex_saas_tenant_is_enabled('client_bridge') ? admin_url('billing/my_account?redirect=clients/my_account') : perfex_saas_default_base_url('clients/my_account');
            }
        ?>
        <a href="<?= $url; ?>" class="" target="<?= $target; ?>">
            <?= _l('perfex_saas_pricing_customize'); ?> <i class="fa fa-edit"></i>
        </a>
        <?php } ?>
    </div>

    <div class="row">

        <?php foreach ($usage_limits as $resources => $usage) : ?>
        <?php
            $quota = perfex_saas_tenant_resources_quota($tenant, $resources);

            $unlimited = $quota === -1;
            $usage_percent = $unlimited ? 0 : ($quota > 0 ? number_format(($usage * 100) / $quota, 2) : 0);
            $color = $usage_percent < 50 ? "green" : ($usage_percent > 90 ? 'red' : '#ca8a03');

            if ($unlimited && $display === 'limited-only' || in_array($resources, $disabled_default_modules)) continue;

            if (in_array($resources, $hidden_quota_widgets)) continue;
            ?>
        <div class="col-xs-12 col-md-3 col-sm-4 tw-mb-2">
            <div class="top_stats_wrapper" <?= $usage_percent > 95 ? "style='border-color:$color;'" : ''; ?>>
                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center mr-2">
                        <?= _l('perfex_saas_limit_' . $resources); ?>
                    </div>
                    <span
                        class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"><?= $usage; ?>/<?= $unlimited ? '<i class="fa fa-infinity"></i>' : $quota; ?></span>
                </div>
                <div class="progress tw-mb-0 tw-mt-5 progress-bar-mini">
                    <div class="progress-bar no-percent-text not-dynamic" style="background:<?= $color; ?>"
                        role="progressbar" aria-valuenow="<?= $usage_percent; ?>" aria-valuemin="0" aria-valuemax="100"
                        style="width: 0%" data-percent="<?php echo $usage_percent; ?>">
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- storage -->
        <?php
        $quota = perfex_saas_tenant_storage_limit($tenant);
        $unlimited = perfex_saas_tenant_storage_is_unlimited($tenant);
        $display_storage = $unlimited && $display === 'limited-only' ? false : true;
        if ($display_storage && in_array('storage', $hidden_quota_widgets))
            $display_storage = false;
        ?>
        <?php if ($display_storage) {
            $quota_in_byte = $unlimited ? 0 : perfex_saas_convert_formatted_size_to_bytes($quota);
            $usage_in_byte = perfex_saas_tenant_used_storage($tenant);
            $usage = perfex_saas_format_storage_size($usage_in_byte);
            $usage_percent = $quota_in_byte  > 0 ? number_format(($usage_in_byte * 100) / $quota_in_byte, 2) : 0;
            $color = $usage_percent < 50 ? "green" : ($usage_percent > 90 ? 'red' : '#ca8a03');
        ?>
        <div class="col-xs-12 col-md-3 col-sm-4 tw-mb-2">
            <div class="top_stats_wrapper" <?= $usage_percent > 95 ? "style='border-color:$color;'" : ''; ?>>
                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center mr-2">
                        <?= _l('perfex_saas_limit_storage'); ?>
                    </div>
                    <span
                        class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"><?= $usage; ?>/<?= $unlimited ? '<i class="fa fa-infinity"></i>' : $quota; ?></span>
                </div>
                <div class="progress tw-mb-0 tw-mt-5 progress-bar-mini">
                    <div class="progress-bar no-percent-text not-dynamic" style="background:<?= $color; ?>"
                        role="progressbar" aria-valuenow="<?= $usage_percent; ?>" aria-valuemin="0" aria-valuemax="100"
                        style="width: 0%" data-percent="<?php echo $usage_percent; ?>">
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>

    </div>
</div>