<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();
$default_service_id  = 'serv' . time();
$default_service = [$default_service_id => ['name' => '', 'description' => '', 'price' => '']];
$services = array_merge($default_service, $CI->perfex_saas_model->services());
$currency = get_base_currency();
?>
<div class="tw-flex tw-flex-col">
    <div class="row">
        <div class="col-xs-12">
            <?php render_yes_no_option('perfex_saas_enable_custom_service_request', _l('perfex_saas_enable_custom_service_request'), _l('perfex_saas_enable_custom_service_request_hint')); ?>
        </div>
        <div class="col-xs-12">
            <?= $label = _l('perfex_saas_custom_service_request_form') . perfex_saas_form_label_hint('perfex_saas_custom_service_request_form_hint'); ?>
            <a href="<?= admin_url('leads/form'); ?>" target="_blank"
                class="tw-ml-3"><?= _l('perfex_saas_create_marketplace_form'); ?></a>
            <?= render_input('settings[perfex_saas_custom_service_request_form]', '', get_option('perfex_saas_custom_service_request_form')); ?>
        </div>
    </div>
    <div class="tw-mt-4 tw-mb-4">
        <hr />
        <hr />
    </div>
    <div class="tw-mt-4 tw-mb-4">
        <div class="tw-flex tw-justify-end tw-mb-2">
            <button type="button" id="add-service"
                class="btn btn-primary pull-right"><?= _l('perfex_saas_add_new_service'); ?> <i
                    class="fa fa-plus"></i></button>

        </div>
        <div class="tw-mt-4 tw-flex tw-flex-col tw-gap-6 services">
            <!-- placehodler input to allow full clearing/removal of services -->
            <input type="hidden" name="settings[perfex_saas_custom_services][]" />
            <?php
            foreach ($services as $service_id => $value) : $billing_mode = $value['billing_mode'] ?? 'billing_cycle'; ?>
            <?php if ($service_id === $default_service_id) echo "<template id='service-template' data-id='$service_id'>"; ?>
            <div class="service" data-id="<?= $service_id; ?>">
                <div class="tw-mb-4 tw-flex tw-flex-wrap tw-items-start tw-justify-between">
                    <label class="col-sm-4">
                        <span><?= $value['name']; ?></span>
                        <button type="button" onclick="removeServiceRow(this);"
                            class="btn btn-danger btn-xs pull-right delete"><i class="fa fa-trash"></i></button>
                    </label>
                    <div class="col-sm-8">
                        <input data-toggle="tooltip" data-title="<?= _l('perfex_saas_service_name'); ?>"
                            name="settings[perfex_saas_custom_services][<?= $service_id; ?>][name]"
                            value="<?= $value['name']; ?>" class="form-control"
                            placeholder="<?= _l('perfex_saas_service_name'); ?>" required />
                        <textarea data-toggle="tooltip" data-title="<?= _l('perfex_saas_description'); ?>"
                            name="settings[perfex_saas_custom_services][<?= $service_id; ?>][description]"
                            class="form-control"
                            placeholder="<?= _l('perfex_saas_description'); ?>"><?= $value['description'] ?? ''; ?></textarea>
                        <div class="input-group" data-toggle="tooltip" data-title="<?= _l('perfex_saas_price'); ?>">
                            <input min="0" step="0.01" type="number"
                                name="settings[perfex_saas_custom_services][<?= $service_id; ?>][price]"
                                value="<?= $value['price'] ?? ''; ?>" class="form-control"
                                placeholder="<?= _l('perfex_saas_price'); ?>" required />
                            <span class="input-group-addon tw-px-2 tw-border-l-0">
                                <?= $currency->name; ?>

                                <span>/</span>
                                <select class="input-control tw-border-neutral-300 tw-border tw-rounded-md"
                                    name="settings[perfex_saas_custom_services][<?= $service_id; ?>][billing_mode]"
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
                                name="settings[perfex_saas_custom_services][<?= $service_id; ?>][image]"
                                value="<?= $value['image'] ?? ''; ?>" class="form-control" />
                        </div>
                    </div>
                </div>
                <hr class="col-xs-12" />
            </div>
            <?php if ($service_id === $default_service_id) echo '</template>'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function getRndInteger(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }
    $("#add-service").on('click', function() {
        let template = $("template#service-template").clone();

        let newId = 'serv' + (Math.floor(Date.now() / getRndInteger(10, 100)) + getRndInteger(10,
            1000));
        let id = template.attr('data-id');

        if (id && !$("[data-id='" + newId + "']").length && $(".services .service").length < 50) {
            template = template.html();
            template = template.replaceAll(`][${id}]`, `][${newId}]`);
            template = $(template);
            template.attr('data-id', newId);
            template.find("label span").text("");
            template.find("input,textarea").val("");

            $(".services").append(template);
        }
    });
});

function removeServiceRow(obj) {
    if (confirm(appLang['confirm_action_prompt'])) {
        $(obj).closest('.service').remove();
    }
}
</script>