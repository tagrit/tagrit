<?php defined('BASEPATH') or exit('No direct script access allowed');

$package_stripe_settings = (object)($package->metadata->stripe ?? []);
$modules = $this->perfex_saas_model->modules();
$services = $this->perfex_saas_model->services();
$discounts = (object)($package->metadata->discounts ?? ['limits' => []]);

$stripe_pricing_select = function ($name) use ($stripe_plans, $package_stripe_settings) {
    $value = $package_stripe_settings->manual_pricing->{$name} ?? '';
    if (empty($value))
        $value = $package_stripe_settings->pricing->{$name} ?? '';

    return $this->load->view(
        '_stripe_pricing_select',
        [
            'stripe_plans' => $stripe_plans,
            'selected_value' => $value,
            'name' => "metadata[stripe][manual_pricing][$name]",
        ],
        true
    );
};
?>
<div class="row">
    <div class="col-md-12">

        <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700 tw-flex tw-items-center tw-space-x-2">
            <span>
                <?= $package->name; ?>
            </span>
        </h4>

        <?= form_open($this->uri->uri_string(), ['id' => 'stripe_pricing_form_' . $package->slug]); ?>

        <?php echo form_hidden('id', $package->id); ?>

        <!-- Row grid -->
        <div class="row">

            <!-- Baseic package info -->
            <div class="col-lg-7">

                <div class="panel_s toggle">
                    <div class="panel-body">
                        <div class="row mtop25 tw-mb-4">
                            <div class="col-md-6 border-right">
                                <span><?php echo perfex_saas_input_label_with_hint('perfex_saas_stripe_subscription_enabled') ?></span>
                            </div>
                            <div class="col-md-2">
                                <div class="onoffswitch">
                                    <input type="checkbox" id="switch_<?= $package->slug; ?>"
                                        class="onoffswitch-checkbox enable"
                                        <?php if (isset($package_stripe_settings->enabled) && $package_stripe_settings->enabled == '1') {
                                                                                                                                        echo 'checked';
                                                                                                                                    } ?> value="1"
                                        name="metadata[stripe][enabled]">
                                    <label class="onoffswitch-label" for="switch_<?= $package->slug; ?>"></label>
                                </div>
                            </div>
                        </div>
                        <div class="row mtop25 tw-mb-4 enable-deps">
                            <div class="col-md-6 border-right">
                                <span><?php echo perfex_saas_input_label_with_hint('perfex_saas_stripe_autosync_enabled') ?></span>
                            </div>
                            <div class="col-md-2">
                                <div class="onoffswitch">
                                    <input type="checkbox" id="sync_<?= $package->slug; ?>"
                                        class="onoffswitch-checkbox sync"
                                        <?php if (($package_stripe_settings->sync ?? '1') == '1') {
                                                                                                                                    echo 'checked';
                                                                                                                                } ?> value="1"
                                        name="metadata[stripe][sync]">
                                    <label class="onoffswitch-label" for="sync_<?= $package->slug; ?>"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel_s enable-deps">
                    <div class="panel-body">
                        <div class="form-group select-placeholder pricing sync-deps">
                            <label><?= _l('perfex_saas_base_price'); ?></label>
                            <?php echo $stripe_pricing_select('default'); ?>
                        </div>

                        <?php
                        $proration_behavior_options = [
                            [
                                'key' => 'always_invoice',
                                'label' => _l('perfex_saas_stripe_prorate_always_invoice')
                            ],
                            [
                                'key' => 'create_prorations',
                                'label' => _l('perfex_saas_stripe_prorate_create_prorations')
                            ],
                            [
                                'key' => 'none',
                                'label' => _l('perfex_saas_stripe_prorate_none')
                            ],
                        ];
                        $selected = (!empty($package_stripe_settings->proration_behavior) ? $package_stripe_settings->proration_behavior : 'none');
                        echo render_select('metadata[stripe][proration_behavior]', $proration_behavior_options, ['key', ['label']], _l('perfex_saas_stripe_prorate') . '<a
                                    href="https://stripe.com/docs/billing/subscriptions/prorations" target="_blank"><i
                                        class="fa fa-link"></i></a>', $selected);
                        ?>

                        <?php
                        $s_attrs = ['disabled' => true, 'data-show-subtext' => true, 'id' => 'currency_' . $package->slug];
                        $selected = $package_stripe_settings->currency ?? '';
                        foreach ($currencies as $currency) {
                            if ($currency['isdefault'] == 1) {
                                $s_attrs['data-base'] = $currency['id'];
                            }

                            if ($currency['id'] == ($package_stripe_settings->currency ?? '')) {
                                $selected = $currency['id'];
                            } else if ($currency['isdefault'] == 1) {
                                $selected = $currency['id'];
                            }
                        }
                        ?>
                        <?php echo render_select('metadata[stripe][currency]', $currencies, ['id', 'name', 'symbol'], 'currency', $selected, $s_attrs, [], '', 'ays-ignore'); ?>

                        <!-- Taxes -->
                        <div class="row sync-deps">
                            <div class="col-md-12">
                                <div class="form-group select-placeholder">
                                    <label class="control-label" for="tax"><?php echo _l('tax'); ?> (Stripe)</label>
                                    <select class="selectpicker" data-width="100%" name="metadata[stripe][taxes][]"
                                        data-none-selected-text="<?php echo _l('no_tax'); ?>" multiple>
                                        <option value=""></option>
                                        <?php foreach ($stripe_tax_rates->data as $tax) {
                                            if ($tax->inclusive) {
                                                continue;
                                            }
                                            if (!$tax->active) {
                                                if (!in_array($tax->id, ($package_stripe_settings->taxes ?? []))) {
                                                    continue;
                                                }
                                            } ?>
                                        <option value="<?php echo e($tax->id); ?>"
                                            data-subtext="<?php echo !empty($tax->country) ? $tax->country : ''; ?>"
                                            <?= in_array($tax->id, ($package_stripe_settings->taxes ?? [])) ? 'selected' : ''; ?>>
                                            <?php echo e($tax->display_name); ?>
                                            <?php echo !empty($tax->jurisdiction) ? ' - ' . $tax->jurisdiction . ' ' : ''; ?>
                                            (<?php echo e($tax->percentage); ?>%)
                                            <?php if (!$tax->active) echo ' - Inactive'; ?>
                                        </option>
                                        <?php
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <?php $value = ($package_stripe_settings->terms ?? ''); ?>
                        <?php echo render_textarea('metadata[stripe][terms]', 'terms_and_conditions', $value, ['placeholder' => _l('subscriptions_terms_info'), 'id' => 'terms_' . $package->slug], [], '', 'ays-ignore'); ?>
                    </div>
                </div>

                <div class="pricing sync-deps">
                    <!-- Modules pricing -->
                    <div class="panel_s enable-deps">
                        <div class="panel-body">

                            <h4 class="tw-mb-4">
                                <?= _l('perfex_saas_modules'); ?>
                            </h4>
                            <div class="mtop15"></div>
                            <hr />
                            <div class="modules-price-list">
                                <?php foreach ($modules as $key => $module) :  ?>
                                <?php $value = (float)($package->metadata->limitations_unit_price->{$key} ?? 0);
                                    if (!$value) continue; ?>

                                <div class="row tw-flex  tw-items-center tw-mb-2" data-repeat-id="<?= $key; ?>">
                                    <div class="col-md-6">
                                        <?= $module['custom_name']; ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo $stripe_pricing_select($key); ?>
                                    </div>
                                </div>

                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Services pricing -->
                    <div class="panel_s enable-deps">
                        <div class="panel-body">

                            <h4 class="tw-mb-4">
                                <?= _l('perfex_saas_services_price_per_cycle', ''); ?>
                                (<?= _l('perfex_saas_billing_cycle'); ?>)
                            </h4>
                            <div class="mtop15"></div>

                            <hr />
                            <?php foreach ($services as $key => $service) :  ?>
                            <?php $value = (float)($package->metadata->limitations_unit_price->{$key} ?? 0);
                                if (!$value || $service['billing_mode'] === 'lifetime') continue; ?>

                            <div class="row tw-flex  tw-items-center tw-mb-2" data-repeat-id="<?= $key; ?>">
                                <div class="col-md-6">
                                    <?= $service['name']; ?>
                                </div>
                                <div class="col-md-6">
                                    <?php echo $stripe_pricing_select($key); ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Right column: Resource quota -->
            <div class="col-lg-5 pricing sync-deps">
                <div class="panel_s enable-deps">
                    <div class="panel-body">

                        <h3 class="tw-mt-0">
                            <?= _l('perfex_saas_quota'); ?>
                        </h3>

                        <!--max instance limit -->
                        <div class="tw-mt-4 tw-mb-8">
                            <div class="form-group select-placeholder">
                                <label><?= _l('perfex_saas_instance_unit_price'); ?></label>
                                <?php echo $stripe_pricing_select('tenant_instance'); ?>
                            </div>
                        </div>

                        <!-- Limitations -->
                        <div class="tw-mt-4 tw-mb-4">
                            <hr />
                        </div>
                        <div class="row">

                            <?php foreach (PERFEX_SAAS_LIMIT_FILTERS_TABLES_MAP as $event => $limit) : ?>
                            <?php $value = (isset($package->metadata->limitations->{$limit}) ? $package->metadata->limitations->{$limit} : -1); ?>
                            <?php
                                $unlimited = (int)$value == -1;
                                if ($unlimited) continue;
                                ?>
                            <div class="col-md-6 col-xs-12 tw-mb-4">
                                <?php $value_unit_price = $package->metadata->limitations_unit_price->{$limit} ?? ''; ?>
                                <div class="form-group select-placeholder">
                                    <label><?php echo _l('perfex_saas_limit_' . $limit); ?></label>
                                    <?php echo $stripe_pricing_select($limit); ?>
                                </div>

                            </div>
                            <?php endforeach ?>

                            <div class="col-md-6 col-xs-12">
                                <?php
                                $value = (isset($package->metadata->storage_limit->size) ? $package->metadata->storage_limit->size : -1);
                                $unlimited = (int)$value == -1;
                                if (!$unlimited) {
                                ?>
                                <div class="form-group select-placeholder">
                                    <label><?php echo _l('perfex_saas_limit_storage'); ?></label>
                                    <?php echo $stripe_pricing_select('storage'); ?>
                                </div>
                                <?php } ?>
                            </div>

                        </div>

                    </div>
                </div>


                <!-- Discounts -->
                <div class="panel_s enable-deps">
                    <div class="panel-body">

                        <h4 class="tw-mb-4">
                            <?= _l('perfex_saas_pricing_discounts'); ?>
                        </h4>
                        <div class="mtop15"></div>
                        <hr />
                        <?php foreach ($discounts->limits as $index => $limit) : ?>
                        <?php $units = $discounts->units[$index];
                            $percent = $discounts->percents[$index]; ?>
                        <div class="row tw-flex tw-items-center tw-mb-2">
                            <div class="col-md-6">
                                <?= _l('perfex_saas_limit_' . $limit); ?> ( <?= $units; ?>+ )
                            </div>
                            <div class="col-md-6">
                                <?php echo $stripe_pricing_select($limit . '_' . $units); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
            <!-- End right column -->
        </div>
        <!-- end row-->

        <div class="text-center">
            <button type="submit" data-loading-text="<?= _l('perfex_saas_saving...'); ?>"
                data-form="#stripe_pricing_form_<?= $package->slug; ?>" class="btn btn-primary mtop15
                                    mbot15"><?= _l('perfex_saas_save_stripe_pricing_btn', $package->name); ?></button>
        </div>
        <?= form_close(); ?>

    </div>
</div>