<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php $CI = &get_instance(); ?>

<!-- Render option for seedings -->
<?php
$key = 'perfex_saas_tenant_seeding_source';
$value = get_option($key);
echo render_select(
    'settings[' . $key . ']',
    [
        ['key' => PERFEX_SAAS_SEED_SOURCE_MASTER, 'label' => _l('perfex_saas_tenant_seeding_source_master')],
        ['key' => PERFEX_SAAS_SEED_SOURCE_TENANT, 'label' => _l('perfex_saas_tenant_seeding_source_tenant')],
        ['key' => PERFEX_SAAS_SEED_SOURCE_FILE, 'label' => _l('perfex_saas_tenant_seeding_source_file')],
    ],
    ['key', ['label']],
    perfex_saas_input_label_with_hint($key, $key . '_hint'),
    empty($value) ? PERFEX_SAAS_SEED_SOURCE_MASTER : $value,
    ['onchange' => "saasSetActiveSeedingOption(this)"],
    [],
    '',
    '',
    false
);
?>

<!-- shared tables selection for setting up tenants -->
<div class="tw-mt-10 tw-mb-10 tables seeding-sources tenant">
    <div class="form-group">
        <label for="w-full">
            <?= _l('perfex_saas_seeding_tenant_select'); ?>
        </label>
        <?php $seeding_tenant_slug = get_option('perfex_saas_seeding_tenant'); ?>
        <?php $CI->load->view(PERFEX_SAAS_MODULE_NAME . '/includes/tenant_select', ['name' => 'settings[perfex_saas_seeding_tenant]', 'value' => $seeding_tenant_slug, 'id' => 'perfex_saas_seeding_tenant']); ?>
    </div>
</div>

<!-- shared tables selection for setting up tenants -->
<div class="tw-mt-10 tw-mb-10 tables seeding-sources master tenant">
    <div class="tw-flex tw-items-center">
        <?php render_yes_no_option('perfex_saas_enable_tenant_seeding_caching', _l('perfex_saas_enable_tenant_seeding_caching'), _l('perfex_saas_enable_tenant_seeding_caching_hint')); ?>
        <div>
            <button type="submit" name="clear-seed-cache" class="btn btn-danger btn-xs"><i class="fa fa-refresh"></i>
                <?= _l('clear'); ?></button>
        </div>
    </div>
    <div class="tw-mt-4 tw-mb-4">
        <hr />
    </div>

    <?php $tables = $CI->db->list_tables(); ?>
    <?php
    $dbprefix = perfex_saas_master_db_prefix();
    $default_tables = array_keys(perfex_saas_default_seed_tables());
    $selected_tables = get_option('perfex_saas_tenants_seed_tables');
    $selected_tables = empty($selected_tables) ? $default_tables : (array)json_decode($selected_tables);
    $restricted_tables = [$dbprefix . 'options', $dbprefix . 'sessions', $dbprefix . 'activity_log', $dbprefix . 'vault', $dbprefix . 'staff'];
    ?>
    <button type="button" class="btn btn-danger pull-right"
        onclick="saasResetSeedSelection()"><?= _l('perfex_saas_reset_to_default'); ?></button>
    <label for="perfex_saas_filter w-full">
        <?= perfex_saas_input_label_with_hint('perfex_saas_settings_tenants_seed', 'perfex_saas_settings_tenants_seed_tooltip'); ?>
    </label>
    <br />
    <small class="text-danger"><?= _l('perfex_saas_settings_tenants_seed_hint'); ?></small>
    <input type="text" class="form-control tw-mb-2 tw-mt-4 perfex_saas_filter"
        placeholder="<?= _l('perfex_saas_filter_tenants_seed'); ?>" />

    <div class="perfex_saas_filterables perfex_saas_settings_tenants_seed tw-overflow-y-auto" style="height:50vh">
        <div class="row w-ml-1 tw-mr-1">
            <?php foreach ($tables as $table) :
                if (in_array($table, $restricted_tables) || !str_starts_with($table, $dbprefix)) continue;
                $key = $table;
                $name = str_ireplace([$dbprefix, '_'], ["", " "], $table); ?>
            <div class="col-md-4 col-sm-6 col-xs-12 item">
                <div class="tw-flex form-group share-row">
                    <label class="tw-capitalize text-capitalize" data-toggle="tooltip" data-title="<?= $key; ?>">
                        <div class="checkbox checkbox-inline share-checkbox">
                            <input type="checkbox" name="settings[perfex_saas_tenants_seed_tables][]"
                                <?= in_array($key, $selected_tables) ? 'checked' : ''; ?> value="<?= $key ?>" />
                            <!-- ensure white space between the label -->
                            <label class="tw-capitalize"> </label>
                        </div>
                        <?= $name ?>
                    </label>
                </div>
            </div>
            <?php endforeach ?>
        </div>
    </div>
</div>

<!-- shared settings selection -->
<div class="tw-mt-10 tw-mb-10 options seeding-sources master">
    <?php
    $options = $CI->perfex_saas_model->shared_options();
    $default_options = $CI->perfex_saas_model->sensitive_shared_options();
    $default_options = array_column($default_options, 'key');
    $selected_options = get_option('perfex_saas_sensitive_options');
    $selected_options = empty($selected_options) ? $default_options : (array)json_decode($selected_options);
    ?>
    <button type="button" class="btn btn-danger pull-right"
        onclick="saasResetSeedOptionsSelection()"><?= _l('perfex_saas_reset_to_default'); ?></button>
    <label for="perfex_saas_filter w-full">
        <?= _l('perfex_saas_settings_sensitive_options'); ?>
    </label>
    <br />
    <small class="text-danger"><?= _l('perfex_saas_settings_sensitive_options_hint'); ?></small>
    <input type="text" class="form-control tw-mb-2 tw-mt-4 perfex_saas_filter"
        placeholder="<?= _l('perfex_saas_filter_tenants_seed'); ?>" />

    <div class="perfex_saas_filterables perfex_saas_sensitive_options tw-overflow-y-auto" style="height:35vh">
        <div class="row w-ml-1 tw-mr-1">
            <?php foreach ($options as $option) :
                $key = $option->key;
                $name = $option->name;
                if (strpos($key, PERFEX_SAAS_MODULE_NAME)) continue; ?>
            <div class="col-md-4 col-sm-6 col-xs-12 item">
                <div class="tw-flex form-group share-row">
                    <label class="tw-capitalize text-capitalize" data-toggle="tooltip" data-title="<?= $key; ?>">
                        <div class="checkbox checkbox-inline share-checkbox">
                            <input type="checkbox" name="settings[perfex_saas_sensitive_options][]"
                                <?= in_array($key, $selected_options) ? 'checked' : ''; ?> value="<?= $key ?>" />
                            <!-- ensure white space between the label -->
                            <label class="tw-capitalize"> </label>
                        </div>
                        <?= $name ?>
                    </label>
                </div>
            </div>
            <?php endforeach ?>
        </div>
    </div>
</div>
<div class="tw-mt-4 tw-mb-4 seeding-sources master">
    <hr />
</div>

<!-- shared tables selection for setting up tenants -->
<div class="tw-mt-10 tw-mb-10 tables seeding-sources file">
    <?= _l('perfex_saas_tenant_seeding_from_file_note', '<span class="text-danger">' . str_ireplace(FCPATH, '', module_dir_path(PERFEX_SAAS_MODULE_NAME, 'migrations/default_seeds/')) . '</span>'); ?>
</div>

<script>
"use strict";

function saasResetSeedSelection() {
    let defautlTables = <?= json_encode($default_tables); ?>;
    $('.perfex_saas_filterables.perfex_saas_settings_tenants_seed input:checkbox').prop('checked', false);
    for (let i = 0; i < defautlTables.length; i++) {
        const table = defautlTables[i];
        $(`.perfex_saas_filterables.perfex_saas_settings_tenants_seed input:checkbox[value=${table}]`).prop('checked',
            true);
    }
}

function saasResetSeedOptionsSelection() {
    let defaultOptions = <?= json_encode($default_options); ?>;
    $(this).attr("disabled", "disabled");
    $('.options .perfex_saas_filterables.perfex_saas_sensitive_options input:checkbox').prop('checked', false);
    for (let i = 0; i < defaultOptions.length; i++) {
        const option = defaultOptions[i];
        $(`.options .perfex_saas_filterables.perfex_saas_sensitive_options input:checkbox[value=${option}]`).prop(
            'checked', true);
    }
}

function saasSetActiveSeedingOption(selectInput) {
    console.log(selectInput.value)
    $(".seeding-sources").hide();
    $("#perfex_saas_seeding_tenant").removeAttr("required");
    $(".seeding-sources." + selectInput.value).show();
    if (selectInput.value == 'tenant') {
        $("#perfex_saas_seeding_tenant").attr("required", "required");
    }
}
document.addEventListener("DOMContentLoaded", function() {
    saasSetActiveSeedingOption(
        document.querySelector('select[name="settings[perfex_saas_tenant_seeding_source]"]')
    );
});
</script>