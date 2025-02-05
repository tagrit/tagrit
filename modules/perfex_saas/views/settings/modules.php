<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();
$modules = $CI->perfex_saas_model->modules();
$currency = get_base_currency();
?>
<div class="tw-flex tw-flex-col">
    <div class="row">
        <div class="col-xs-12">
            <?php render_yes_no_option('perfex_saas_enable_custom_module_request', _l('perfex_saas_enable_custom_module_request'), _l('perfex_saas_enable_custom_module_request_hint')); ?>
        </div>
        <div class="col-xs-12">
            <?= $label = _l('perfex_saas_custom_module_request_form') . perfex_saas_form_label_hint('perfex_saas_custom_module_request_form_hint'); ?>
            <a href="<?= admin_url('leads/form'); ?>" target="_blank"
                class="tw-ml-3"><?= _l('perfex_saas_create_marketplace_form'); ?></a>
            <?= render_input('settings[perfex_saas_custom_module_request_form]', '', get_option('perfex_saas_custom_module_request_form')); ?>
        </div>
    </div>
    <div class="tw-mt-4 tw-mb-4">
        <hr />
    </div>

    <?php render_yes_no_option('perfex_saas_allow_default_modules_on_marketplace', _l('perfex_saas_allow_default_modules_on_marketplace')); ?>

    <?php render_yes_no_option('perfex_saas_enable_tenant_admin_modules_page', _l('perfex_saas_enable_tenant_admin_modules_page')); ?>

    <div class="tw-mt-4 tw-mb-4">
        <hr />
        <hr />
    </div>
    <div class="tw-mt-4 tw-mb-4">
        <div class="tw-overflow-y-auto tw-mt-4 tw-flex tw-flex-col tw-gap-6" style="max-height: 70vh;">
            <?php foreach ($modules as $module_id => $value) :
                $billing_mode = $value['billing_mode'] ?? 'billing_cycle';
            ?>
            <div class="tw-mb-4 tw-flex tw-flex-wrap tw-items-start tw-justify-between">
                <label class="col-sm-4"><?= $value['headers']['module_name']; ?></label>
                <div class="col-sm-8">
                    <div class="tw-gap-2 tw-flex tw-flex-col">
                        <input data-toggle="tooltip" data-title="<?= _l('perfex_saas_custom_modules_name'); ?>"
                            name="settings[perfex_saas_custom_modules_name][<?= $module_id; ?>]"
                            value="<?= $value['custom_name']; ?>" class="form-control" />
                        <textarea data-toggle="tooltip" data-title="<?= _l('perfex_saas_description'); ?>"
                            name="settings[perfex_saas_modules_marketplace][<?= $module_id; ?>][description]"
                            class="form-control"
                            placeholder="<?= _l('perfex_saas_description'); ?>"><?= $value['description'] ?? ($value['headers']['description'] ?? ''); ?></textarea>

                        <div class="input-group" data-toggle="tooltip" data-title="<?= _l('perfex_saas_price'); ?>">
                            <input min="0" step="0.01" type="number"
                                name="settings[perfex_saas_modules_marketplace][<?= $module_id; ?>][price]"
                                value="<?= $value['price'] ?? ''; ?>" class="form-control"
                                placeholder="<?= _l('perfex_saas_price'); ?>" />
                            <span class="input-group-addon tw-px-2 tw-border-l-0">
                                <?= $currency->name; ?>

                                <span>/</span>
                                <select class="input-control tw-border-neutral-300 tw-border tw-rounded-md"
                                    name="settings[perfex_saas_modules_marketplace][<?= $module_id; ?>][billing_mode]"
                                    required>
                                    <option value="billing_cycle"
                                        <?= $billing_mode == 'biling_cycle' ? 'selected' : ''; ?>>
                                        <?= _l('perfex_saas_billing_cycle'); ?></option>
                                    <option value="lifetime" <?= $billing_mode == 'lifetime' ? 'selected' : ''; ?>>
                                        <?= _l('perfex_saas_lifetime'); ?></option>
                                </select>
                            </span>
                        </div>
                        <div>
                            <input data-toggle="tooltip" data-title="<?= _l('perfex_saas_image_url'); ?>"
                                placeholder="<?= base_url('media/public/someimage.jpg'); ?>"
                                name="settings[perfex_saas_modules_marketplace][<?= $module_id; ?>][image]"
                                value="<?= $value['image'] ?? ''; ?>" class="form-control" />
                        </div>
                    </div>
                </div>
            </div>
            <hr class="col-xs-12" />
            <?php endforeach ?>
        </div>
    </div>
</div>