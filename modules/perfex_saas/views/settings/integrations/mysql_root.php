<?php

defined('BASEPATH') or exit('No direct script access allowed');
?>

<div class="tw-flex tw-flex-col">

    <!-- Mysql root settings-->
    <h3><?= _l('perfex_saas_mysql_root_settings'); ?></h3>
    <p class="tw-mb-4"><?= _l('perfex_saas_mysql_root_settings_hint'); ?></p>
    <div class="row mtop25 tw-mb-4">
        <div class="col-md-4 border-right">
            <span><?php echo _l('perfex_saas_enabled?'); ?></span>
        </div>
        <div class="col-md-2">
            <div class="onoffswitch">
                <input type="checkbox" id="perfex_saas_mysql_root_enabled" data-perm-id="3" class="onoffswitch-checkbox"
                    <?php if (get_option('perfex_saas_mysql_root_enabled') == '1') {
                                                                                                                                echo 'checked';
                                                                                                                            } ?> value="1" name="settings[perfex_saas_mysql_root_enabled]">
                <label class="onoffswitch-label" for="perfex_saas_mysql_root_enabled"></label>
            </div>
        </div>
    </div>
    <?php
    $CI = &get_instance();
    $fields = ['perfex_saas_mysql_root_host', 'perfex_saas_mysql_root_port', 'perfex_saas_mysql_root_username', 'perfex_saas_mysql_root_password'];
    $encrypted_fields = ['perfex_saas_mysql_root_password'];
    $attrs = [
        'perfex_saas_mysql_root_password' => ['type' => 'password'],
        'perfex_saas_mysql_root_host' => ['data-default-value' => 'localhost'],
        'perfex_saas_mysql_root_username' => ['data-default-value' => 'root'],
        'perfex_saas_mysql_root_port' => ['data-default-value' => '3306'],
    ];

    foreach ($fields as $key => $field) {
        $value = get_option($field);
        if (empty($value)) {
            $value = $attrs[$field]['data-default-value'] ?? '';
        }

        if (!empty($value) && in_array($field, $encrypted_fields))
            $value = $CI->encryption->decrypt($value);

        $label = _l($field) . perfex_saas_form_label_hint($field . '_hint');
        echo render_input('settings[' . $field . ']', $label, $value, $attrs[$field]['type'] ?? 'text', $attrs[$field] ?? []);
    } ?>

    <?php $separate_db_user_enabled = get_option('perfex_saas_mysql_root_enable_separate_user') != '0'; ?>
    <div class="row mtop25 tw-mb-4">
        <div class="col-md-4 border-right">
            <span><?php echo _l('perfex_saas_mysql_root_enable_separate_user'); ?></span>
        </div>
        <div class="col-md-2">
            <input type="hidden" value="<?= $separate_db_user_enabled ? '1' : '0'; ?>"
                id="perfex_saas_mysql_root_enable_separate_user"
                name="settings[perfex_saas_mysql_root_enable_separate_user]" />
            <div class="onoffswitch">
                <input type="checkbox" id="perfex_saas_mysql_root_enable_separate_user_switch"
                    class="onoffswitch-checkbox" <?= $separate_db_user_enabled ? 'checked' : ''; ?> value="1">
                <label class="onoffswitch-label" for="perfex_saas_mysql_root_enable_separate_user_switch"></label>
            </div>
        </div>
        <div class="col-md-12 text-warning"><?= _l('perfex_saas_mysql_root_enable_separate_user_hint'); ?></div>
    </div>

    <div class="tw-flex tw-justify-end">
        <button onclick="testMysqlRootIntegration()" class="btn btn-danger btn-sm"
            type="button"><?= _l('perfex_saas_test'); ?></button>
    </div>

    <div class="tw-mt-4 tw-mb-4">
        <hr />
    </div>

</div>

<script>
"use strict";

function testMysqlRootIntegration() {
    const data = {};
    const button = $("#mysql_root button");

    $("#mysql_root input").each(function() {
        data[this.name] = this.value;
    });

    button.attr('disabled', true);
    $.post(admin_url + '<?= PERFEX_SAAS_ROUTE_NAME; ?>/integrations/test_mysql_root', data)
        .done(function(response) {
            button.removeAttr('disabled');
            response = JSON.parse(response);
            alert_float(response.status, response.message, 10000);
        }).fail(function(error) {
            button.removeAttr('disabled');
        });
}
document.getElementById('perfex_saas_mysql_root_enable_separate_user_switch').addEventListener('change', function() {
    let switchValue = this.checked ? 1 : 0;
    document.getElementById('perfex_saas_mysql_root_enable_separate_user').value = switchValue;
});
</script>