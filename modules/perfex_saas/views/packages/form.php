<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php
$currency = $this->currencies_model->get_base_currency();
$cycle_period = _l('perfex_saas_billing_cycle');
$yes_no_options = $this->perfex_saas_model->yes_no_options();
$select_attr = ['multiple' => 'true', 'allow_blank' => 'true', 'class' => 'selectpicker display-block', 'data-actions-box' => 'true', 'data-width' => '100%'];
$single_pricing_mode = perfex_saas_is_single_package_mode();
$hidden_quota_widgets_options = [];
?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">

                <?php if (!$single_pricing_mode) : ?>
                <h4
                    class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700 tw-flex tw-items-center tw-space-x-2">
                    <span>
                        <?= isset($package) ? $package->name : _l('perfex_saas_new_package'); ?>
                    </span>
                </h4>
                <?php endif; ?>


                <?= validation_errors('<div class="alert alert-danger text-center">', '</div>'); ?>
                <?php $this->load->view('authentication/includes/alerts'); ?>

                <?= form_open($this->uri->uri_string(), ['id' => 'packages_form']); ?>

                <?php if (isset($package)) echo form_hidden('id', $package->id); ?>

                <!-- Row grid -->
                <div class="row">

                    <!-- Baseic package info -->
                    <div class="col-lg-7">
                        <div class="panel_s">
                            <div class="panel-body">

                                <?php if ($single_pricing_mode) : ?>
                                <input name="name" type="hidden" value="<?= $package->name ?? 'Pricing'; ?>" />

                                <?php else : ?>

                                <?php $value = (isset($package) ? $package->name : ''); ?>
                                <?= render_input('name', 'name', $value); ?>

                                <?php $value = (isset($package) ? $package->description : ''); ?>
                                <?= render_textarea('description', 'perfex_saas_description', $value, [], [], 'tinymce tinymce-manual'); ?>

                                <?php endif; ?>

                                <?php $value = (isset($package) ? $package->price : ''); ?>
                                <?= render_input('price', _l('perfex_saas_base_price') . ' (' . $currency->name . ')', $value, 'number', ['min' => 0, 'step' => '0.01']); ?>

                                <?php $value = (isset($package) ? $package->trial_period : ''); ?>
                                <?= render_input('trial_period', 'perfex_saas_trial_period', $value, 'number', ['step' => '1']); ?>

                                <div class="tw-mt-8 tw-mb-8  tw-pt-3">
                                    <!-- Invoice interval period handling -->
                                    <?php if (isset($package)) $invoice = $package->metadata->invoice; ?>

                                    <?php $is_custom_interval = isset($invoice) && $invoice->recurring == 'custom'; ?>
                                    <div class="form-group select-placeholder">
                                        <div class="tw-flex tw-justify-between">
                                            <label for="recurring" class="control-label">

                                                <small class="req text-danger">*</small>
                                                <?= _l('perfex_saas_invoice_add_edit_recurring'); ?>
                                            </label>
                                            <label for="lifetime" class="control-label tw-flex">
                                                <?= _l('perfex_saas_mark_as_lifetime'); ?>
                                                <?php $checked = isset($package->metadata->is_liftetime_deal) && $package->metadata->is_liftetime_deal == '1'; ?>
                                                <div class="tw-pl-2">
                                                    <div class="onoffswitch">
                                                        <input type="checkbox" id="is_liftetime_deal"
                                                            class="onoffswitch-checkbox"
                                                            <?= $checked ? 'checked' : ''; ?> value="1"
                                                            name="metadata[is_liftetime_deal]">
                                                        <label class="onoffswitch-label"
                                                            for="is_liftetime_deal"></label>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                        <select class="selectpicker" data-width="100%"
                                            name="metadata[invoice][recurring]"
                                            data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>" required>
                                            <?php for ($i = 0; $i <= 12; $i++) : ?>
                                            <?php
                                                $selected = isset($invoice) && !$is_custom_interval && $invoice->recurring == $i ? 'selected' : '';

                                                $reccuring_string = $i == 0 ?  _l('dropdown_non_selected_tex') : ($i == 1 ? _l('invoice_add_edit_recurring_month', $i) : _l('invoice_add_edit_recurring_months', $i));
                                                ?>
                                            <option value="<?= $i == 0 ? '' : $i; ?>" <?= $selected; ?>>
                                                <?= $reccuring_string; ?>
                                            </option>
                                            <?php endfor ?>
                                            <!-- custom select -->
                                            <option value="custom" <?php if (isset($invoice) && $invoice->recurring != 0 && $is_custom_interval) {
                                                                        echo 'selected';
                                                                    } ?>>
                                                <?= _l('perfex_saas_recurring_custom'); ?>
                                            </option>
                                        </select>
                                    </div>

                                    <!-- custom select inputs -->
                                    <div class="row recurring_custom <?php if ((isset($invoice) && !$is_custom_interval) || (!isset($invoice))) {
                                                                            echo 'hide';
                                                                        } ?>">
                                        <div class="col-md-6">
                                            <?php $value = (isset($invoice) && $is_custom_interval ? $invoice->repeat_every_custom : 1); ?>
                                            <?= render_input('metadata[invoice][repeat_every_custom]', '', $value, 'number', ['min' => 1]); ?>
                                        </div>

                                        <div class="col-md-6">
                                            <select name="metadata[invoice][repeat_type_custom]" id="repeat_type_custom"
                                                class="selectpicker" data-width="100%"
                                                data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>">
                                                <option value="day" <?php if ($is_custom_interval && $invoice->repeat_type_custom == 'day') {
                                                                        echo 'selected';
                                                                    } ?>>
                                                    <?= _l('perfex_saas_invoice_recurring_days'); ?>
                                                </option>
                                                <option value="week" <?php if ($is_custom_interval == 1 && $invoice->repeat_type_custom == 'week') {
                                                                            echo 'selected';
                                                                        } ?>>
                                                    <?= _l('perfex_saas_invoice_recurring_weeks'); ?></option>
                                                <option value="month" <?php if ($is_custom_interval && $invoice->repeat_type_custom == 'month') {
                                                                            echo 'selected';
                                                                        } ?>>
                                                    <?= _l('perfex_saas_invoice_recurring_months'); ?></option>
                                                <option value="year" <?php if ($is_custom_interval && $invoice->repeat_type_custom == 'year') {
                                                                            echo 'selected';
                                                                        } ?>>
                                                    <?= _l('perfex_saas_invoice_recurring_years'); ?></option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label><?= _l('invoice_table_tax_heading'); ?></label>
                                        <?php
                                        $default_tax = isset($invoice->taxname) ? $invoice->taxname : '';
                                        $select      = '<select class="selectpicker display-block tax main-tax" data-width="100%" name="metadata[invoice][taxname][]" multiple data-none-selected-text="' . _l('no_tax') . '">';
                                        foreach ($taxes as $tax) {
                                            $selected = '';
                                            if (is_array($default_tax)) {
                                                if (in_array($tax['name'] . '|' . $tax['taxrate'], $default_tax)) {
                                                    $selected = ' selected ';
                                                }
                                            }
                                            $select .= '<option value="' . $tax['name'] . '|' . $tax['taxrate'] . '"' . $selected . 'data-taxrate="' . $tax['taxrate'] . '" data-taxname="' . $tax['name'] . '" data-subtext="' . $tax['name'] . '">' . $tax['taxrate'] . '%</option>';
                                        }
                                        $select .= '</select>';
                                        echo $select;
                                        ?>
                                    </div>

                                    <div
                                        class="form-group tw-mb-8 <?= !empty($all_payment_modes) ? ' select-placeholder' : ''; ?>">
                                        <label for="allowed_payment_modes"
                                            class="control-label"><?= _l('perfex_saas_package_allowed_payment_modes'); ?></label>
                                        <?php
                                        $select = '<select class="selectpicker display-block" data-actions-box="true" data-width="100%" name="metadata[invoice][allowed_payment_modes][]" multiple data-none-selected-text="' . _l('dropdown_non_selected_tex') . '">';
                                        foreach ($all_payment_modes as $key => $pmode) {
                                            $selected = (empty($invoice->allowed_payment_modes) && $pmode['selected_by_default'] == 1) || in_array($pmode['id'], $invoice->allowed_payment_modes ?? []) ? ' selected' : '';
                                            $select .= '<option value="' . $pmode['id'] . '"' . $selected . '>' . $pmode['name'] . '</option>';
                                        }
                                        $select .= '</select>';
                                        echo $select; ?>
                                    </div>
                                </div>

                                <div class="tw-mt-8 tw-mb-8 tw-pt-3">
                                    <!-- client view theme -->
                                    <?php $value = (isset($package->metadata->client_theme) ? $package->metadata->client_theme : 'single'); ?>
                                    <?php $client_themes = [
                                        ['key' => 'agency', 'label' => _l('perfex_saas_package_client_theme_agency')],
                                        ['key' => 'single', 'label' => _l('perfex_saas_package_client_theme_single')],
                                    ]; ?>
                                    <?= render_select('metadata[client_theme]', $client_themes, ['key', ['label']], _l('perfex_saas_package_client_theme') . perfex_saas_form_label_hint('perfex_saas_package_client_theme_hint'), $value, [], [], '', '', false); ?>
                                </div>


                                <!-- enable checkboxes -->
                                <div class="row tw-mt-8 tw-mb-8  tw-pt-3">
                                    <div class="col-sm-6 col-md-3">
                                        <?php $checked = isset($package->status) ? ($package->status == '1' ? 'checked' : '') : 'checked'; ?>
                                        <div class="checkbox checkbox-inline form-group" data-toggle="tooltip"
                                            data-title="<?= _l('perfex_saas_enabled_hint'); ?>">
                                            <input type="checkbox" value="1" name="status" <?= $checked ?>>
                                            <label for="ts_rel_to_project"><?= _l('perfex_saas_enabled?'); ?></label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-3">
                                        <?php $checked = isset($package) && $package->is_default == '1' ? 'checked' : ''; ?>
                                        <div class="checkbox checkbox-inline form-group" data-toggle="tooltip"
                                            data-title="<?= _l('perfex_saas_is_default_hint'); ?>">
                                            <input type="checkbox" value="1" name="is_default" <?= $checked ?>>
                                            <label for="ts_rel_to_project"><?= _l('perfex_saas_is_default'); ?></label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-3">
                                        <?php $checked = isset($package) && $package->is_private == '1' ? 'checked' : ''; ?>
                                        <div class="checkbox checkbox-inline form-group" data-toggle="tooltip"
                                            data-title="<?= _l('perfex_saas_is_private_hint'); ?>">
                                            <input type="checkbox" value="1" name="is_private" <?= $checked ?>>
                                            <label for="ts_rel_to_project"><?= _l('perfex_saas_is_private'); ?></label>
                                        </div>
                                    </div>
                                </div>


                                <!-- accessibility-->
                                <div class="tw-mt-8 tw-mb-8 tw-pt-3">
                                    <!-- Subdomain -->
                                    <div class="row">
                                        <div class="col-md-6 mtop10 border-right">
                                            <span><?= _l('perfex_saas_enable_subdomain') . perfex_saas_form_label_hint('perfex_saas_enable_subdomain_hint'); ?></span>
                                        </div>
                                        <div class="col-md-6 mtop10">
                                            <?php $checked = isset($package->metadata->enable_subdomain) && $package->metadata->enable_subdomain == '1'; ?>
                                            <div class="onoffswitch">
                                                <input type="checkbox" id="enable_subdomain" data-perm-id="ps1"
                                                    class="onoffswitch-checkbox" <?= $checked ? 'checked' : ''; ?>
                                                    value="1" name="metadata[enable_subdomain]">
                                                <label class="onoffswitch-label" for="enable_subdomain"></label>
                                            </div>
                                        </div>
                                    </div>


                                    <!-- Custom domain -->
                                    <div class="row">
                                        <div class="col-md-6 mtop10 border-right">
                                            <span><?= _l('perfex_saas_enable_custom_domain') . perfex_saas_form_label_hint('perfex_saas_enable_custom_domain_hint'); ?></span>
                                        </div>
                                        <div class="col-md-6 mtop10">
                                            <?php $checked = isset($package->metadata->enable_custom_domain) && $package->metadata->enable_custom_domain == '1'; ?>
                                            <div class="onoffswitch">
                                                <input type="checkbox" id="enable_custom_domain" data-perm-id="ps2"
                                                    class="onoffswitch-checkbox" <?= $checked ? 'checked' : ''; ?>
                                                    value="1" name="metadata[enable_custom_domain]">
                                                <label class="onoffswitch-label" for="enable_custom_domain"></label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mtop10 border-right">
                                            <span><?= _l('perfex_saas_autoapprove_custom_domain') . perfex_saas_form_label_hint('perfex_saas_autoapprove_custom_domain_hint'); ?></span>
                                        </div>
                                        <div class="col-md-6 mtop10">
                                            <?php $checked = isset($package->metadata->autoapprove_custom_domain) && $package->metadata->autoapprove_custom_domain == '1'; ?>
                                            <div class="onoffswitch">
                                                <input type="checkbox" id="autoapprove_custom_domain" data-perm-id="ps3"
                                                    class="onoffswitch-checkbox" <?= $checked ? 'checked' : ''; ?>
                                                    value="1" name="metadata[autoapprove_custom_domain]">
                                                <label class="onoffswitch-label"
                                                    for="autoapprove_custom_domain"></label>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <!-- modules selection -->
                                <div class="tw-mt-8 tw-mb-8">
                                    <?php $selected = (isset($package) ? $package->modules : ''); ?>
                                    <?php $modules = $this->perfex_saas_model->modules(); ?>
                                    <?= perfex_saas_render_select('modules[]', $modules, ['system_name', ['custom_name']], 'modules', $selected, $select_attr); ?>
                                </div>

                                <div class="tw-mt-8 tw-mb-8">
                                    <?php $selected = (!empty($package->metadata->show_modules_list) ? $package->metadata->show_modules_list : 'yes'); ?>
                                    <?php $display_options = array_merge([
                                        ["label" => _l("perfex_saas_package_display_yes_4"), "key" => "yes_4"],
                                    ], $yes_no_options); ?>
                                    <?= render_select('metadata[show_modules_list]', $display_options, ['key', ['label']], 'perfex_saas_show_modules_list', $selected); ?>
                                </div>

                                <div class="tw-mt-8 tw-mb-8">
                                    <?php $selected = (!empty($package->metadata->show_limits_on_package) ? $package->metadata->show_limits_on_package : 'yes_3'); ?>
                                    <?php $display_options = array_merge([
                                        ["label" => _l("perfex_saas_package_display_yes_2"), "key" => "yes_2"],
                                        ["label" => _l("perfex_saas_package_display_yes_3"), "key" => "yes_3"],
                                        ["label" => _l("perfex_saas_package_display_yes_4"), "key" => "yes_4"],
                                    ], $yes_no_options); ?>
                                    <?= render_select('metadata[show_limits_on_package]', $display_options, ['key', ['label']], 'perfex_saas_show_limits_on_package', $selected); ?>
                                </div>

                                <!-- disabled default modules -->
                                <div class="tw-mt-8 tw-mb-8">
                                    <?php $selected = $package->metadata->disabled_default_modules ?? ''; ?>
                                    <?php $default_modules = $this->perfex_saas_model->default_modules(); ?>
                                    <?php $label =  _l('perfex_saas_disabled_default_modules') . perfex_saas_form_label_hint('perfex_saas_disabled_default_modules_hint'); ?>
                                    <?= perfex_saas_render_select('metadata[disabled_default_modules][]', $default_modules, ['system_name', ['custom_name']], $label, $selected, $select_attr); ?>
                                </div>
                            </div>
                        </div>


                        <!-- Modules pricing -->
                        <div class="panel_s">
                            <div class="panel-body">

                                <h4 class="tw-mb-4">
                                    <?= _l('perfex_saas_modules_price_per_cycle', $cycle_period); ?>
                                </h4>
                                <p><?= _l('perfex_saas_modules_price_per_cycle_hint'); ?></p>
                                <div class="mtop15"></div>
                                <div class="row form-group tw-flex tw-items-center tw-mb-2"
                                    data-repeat-list="#modules-price-list">
                                    <div class="col-md-4" data-toggle="tooltip"
                                        data-title="<?= _l('perfex_saas_select_an_option'); ?>">
                                        <select class="form-control tw-p-1" data-repeat-list-input=""
                                            data-repeat-list-id="">
                                            <?php foreach ($modules as $key => $module) : ?>
                                            <option value="<?= $key; ?>"><?= $module['custom_name']; ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-3 input-group" data-toggle="tooltip"
                                        data-title="<?= _l('perfex_saas_price'); ?>">
                                        <input type="number" step="0.01" min="1" class="form-control tw-p-1"
                                            data-repeat-list-input="metadata[limitations_unit_price][$data-repeat-list-id]" />
                                        <span
                                            class="input-group-addon tw-px-2 tw-border-l-0"><?= $currency->name; ?></span>
                                    </div>
                                    <button type="button" class="tw-rounded btn btn-secondary tw-ml-2"><i
                                            class="fa fa-plus"></i></button>
                                </div>

                                <hr />
                                <div id="modules-price-list">
                                    <?php foreach ($modules as $key => $module) :  ?>
                                    <?php $value = (float)($package->metadata->limitations_unit_price->{$key} ?? 0);
                                        if (!$value) continue; ?>

                                    <div class="row tw-flex  tw-items-center tw-mb-2" data-repeat-id="<?= $key; ?>">
                                        <div class="col-md-4">
                                            <?= $module['custom_name']; ?>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="hidden" name="metadata[limitations_unit_price][<?= $key; ?>]"
                                                value="<?= $value; ?>" /> <?= $value; ?> <?= $currency->name; ?>
                                        </div>
                                        <button class="btn btn-danger remove-parent tw-ml-2" type="button">-</button>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Services pricing -->
                        <?php $services = $this->perfex_saas_model->services(); ?>
                        <div class="panel_s">
                            <div class="panel-body">

                                <h4 class="tw-mb-4">
                                    <?= _l('perfex_saas_services_price_per_cycle', $cycle_period); ?>
                                </h4>
                                <p><?= _l('perfex_saas_services_price_per_cycle_hint'); ?></p>
                                <div class="mtop15"></div>
                                <div class="row form-group tw-flex tw-items-center tw-mb-2"
                                    data-repeat-list="#services-price-list">
                                    <div class="col-md-4" data-toggle="tooltip"
                                        data-title="<?= _l('perfex_saas_select_an_option'); ?>">
                                        <select class="form-control tw-p-1" data-repeat-list-input=""
                                            data-repeat-list-id="">
                                            <?php foreach ($services as $key => $service) : ?>
                                            <option value="<?= $key; ?>"><?= $service['name']; ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-3 input-group" data-toggle="tooltip"
                                        data-title="<?= _l('perfex_saas_price'); ?>">
                                        <input type="number" step="0.01" min="1" class="form-control tw-p-1"
                                            data-repeat-list-input="metadata[limitations_unit_price][$data-repeat-list-id]" />
                                        <span
                                            class="input-group-addon tw-px-2 tw-border-l-0"><?= $currency->name; ?></span>
                                    </div>
                                    <button type="button" class="tw-rounded btn btn-secondary tw-ml-2"><i
                                            class="fa fa-plus"></i></button>
                                </div>

                                <hr />
                                <div id="services-price-list">
                                    <?php foreach ($services as $key => $service) :  ?>
                                    <?php $value = (float)($package->metadata->limitations_unit_price->{$key} ?? 0);
                                        if (!$value) continue; ?>

                                    <div class="row tw-flex  tw-items-center tw-mb-2" data-repeat-id="<?= $key; ?>">
                                        <div class="col-md-4">
                                            <?= $service['name']; ?>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="hidden" name="metadata[limitations_unit_price][<?= $key; ?>]"
                                                value="<?= $value; ?>" /> <?= $value; ?> <?= $currency->name; ?>
                                        </div>
                                        <button class="btn btn-danger remove-parent tw-ml-2" type="button">-</button>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>



                    <!-- Right column: Resource quota -->
                    <div class="col-lg-5">
                        <div class="panel_s">
                            <div class="panel-body">

                                <h3 class="tw-mt-0">
                                    <?= _l('perfex_saas_quota'); ?>
                                </h3>

                                <!--max instance limit -->
                                <div class="tw-mt-4 tw-mb-8">
                                    <?php $value = (isset($package->metadata->max_instance_limit) ? $package->metadata->max_instance_limit : 1); ?>
                                    <?= render_input('metadata[max_instance_limit]', _l('perfex_saas_instance_cap') . perfex_saas_form_label_hint('perfex_saas_instance_cap_hint'), $value, 'number', ['step' => '1']); ?>

                                    <?php $value = $package->metadata->limitations_unit_price->{'tenant_instance'} ?? ''; ?>
                                    <?= render_input('metadata[limitations_unit_price][tenant_instance]', _l('perfex_saas_instance_unit_price') . ' (' . $currency->name . ')' . perfex_saas_form_label_hint('perfex_saas_instance_unit_price_hint'), $value, 'number', ['min' => 0, 'step' => '0.01']); ?>
                                </div>

                                <!-- Limitations -->
                                <h4 class="tw-mb-4">
                                    <?= _l('perfex_saas_limitations_package') . ' ' . perfex_saas_form_label_hint('perfex_saas_limitations_package_hint'); ?>

                                </h4>
                                <div>
                                    <?php $limit = $package->metadata->resource_limit_period ?? 'lifetime'; ?>
                                    <?= render_select('metadata[resource_limit_period]', [
                                        ['key' => 'lifetime', 'label' => _l('perfex_saas_lifetime')],
                                        ['key' => 'billing_cycle', 'label' => _l('perfex_saas_billing_cycle')],
                                    ], ['key', ['label']], _l('perfex_saas_resources_limit_period') . perfex_saas_form_label_hint('perfex_saas_resoures_limit_period_hint'), $limit); ?>
                                </div>
                                <div class="tw-mt-4 tw-mb-4">
                                    <hr />
                                </div>
                                <div class="row" id="package-qouta">

                                    <?php foreach (PERFEX_SAAS_LIMIT_FILTERS_TABLES_MAP as $event => $limit) : ?>
                                    <div class="col-md-6 col-xs-12 tw-mb-4">
                                        <?php $value = (isset($package->metadata->limitations->{$limit}) ? $package->metadata->limitations->{$limit} : -1); ?>
                                        <?php
                                            $unlimited = (int)$value == -1;
                                            $value_unit_price = $package->metadata->limitations_unit_price->{$limit} ?? '';
                                            $label = _l('perfex_saas_limit_' . $limit);
                                            $hidden_quota_widgets_options[] = ['label' => $label, 'key' => $limit];
                                            ?>
                                        <div class="form-group">
                                            <label for="<?= $limit; ?>" class="control-label">
                                                <?php echo $label; ?>
                                            </label>
                                            <div class="input-group">
                                                <span
                                                    class="input-group-addon tw-px-2 tw-border-r-0"><?= _l('perfex_saas_limit'); ?></span>
                                                <input type="number" value="<?= $value; ?>" step="1" min="-1"
                                                    class="form-control" name="<?= "metadata[limitations][$limit]"; ?>"
                                                    <?= $unlimited ? "readonly" : ""; ?>>
                                                <span class="input-group-addon tw-px-2 tw-border-l-0">

                                                    <a href="#metered" class="mark_metered" data-toggle="tooltip"
                                                        data-title="<?= _l('perfex_saas_mark_limited'); ?>"
                                                        onclick="return false;"
                                                        style="<?= $unlimited ? '' : 'display:none'; ?>">
                                                        <i class="<?= 'fa fa-dashboard'; ?>"></i>
                                                    </a>

                                                    <a href="#infinity" class="mark_infinity" data-toggle="tooltip"
                                                        data-title="<?= _l('perfex_saas_mark_unlimited'); ?>"
                                                        onclick="return false;"
                                                        style="<?= $unlimited ? 'display:none' : ''; ?>">
                                                        <i class="<?= 'fa fa-infinity'; ?>"></i>
                                                    </a>
                                                </span>
                                            </div>
                                            <div class="input-group" data-toggle="tooltip"
                                                data-title="<?= _l('perfex_saas_limit_extra_hint', $cycle_period); ?>">
                                                <span
                                                    class="input-group-addon tw-px-2 tw-border-r-0"><?= _l('perfex_saas_limit_extra'); ?></span>
                                                <input type="number" value="<?= $value_unit_price; ?>" step="0.01"
                                                    min="0" class="form-control"
                                                    name="<?= "metadata[limitations_unit_price][$limit]"; ?>">
                                                <span
                                                    class="input-group-addon tw-px-2 tw-border-l-0"><?= $currency->name; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach ?>

                                    <div class="col-md-6 col-xs-12">
                                        <?php
                                        $value = (isset($package->metadata->storage_limit->size) ? $package->metadata->storage_limit->size : -1);
                                        $unlimited = (int)$value == -1;
                                        $value_unit_price = (isset($package->metadata->storage_limit->unit_price) ? $package->metadata->storage_limit->unit_price : '');
                                        $units = perfex_saas_storage_size_units();
                                        $value_unit = (isset($package->metadata->storage_limit->unit) ? $package->metadata->storage_limit->unit : $units[0]);
                                        $hidden_quota_widgets_options[] = ['label' => _l('perfex_saas_limit_storage'), 'key' => 'storage'];
                                        ?>
                                        <div class="form-group">
                                            <label for="<?= $limit; ?>" class="control-label tw-justify-start">
                                                <span>
                                                    <?php echo _l('perfex_saas_limit_storage'); ?><?php echo perfex_saas_form_label_hint('perfex_saas_limit_storage_hint', $cycle_period); ?>
                                                </span>
                                            </label>
                                            <div class="input-group">
                                                <span
                                                    class="input-group-addon tw-px-2 tw-border-r-0"><?= _l('perfex_saas_limit'); ?></span>
                                                <input type="number" value="<?= $value; ?>" step="1" min="-1"
                                                    class="form-control" name="<?= "metadata[storage_limit][size]"; ?>"
                                                    <?= $unlimited ? "readonly" : ""; ?>>
                                                <span class="input-group-addon tw-px-2 tw-border-l-0">
                                                    <select class="input-control" name="metadata[storage_limit][unit]">
                                                        <?php foreach ($units as $unit) : if ($unit == 'B' || $unit == 'KB') continue; ?>
                                                        <option <?= $value_unit == $unit ? 'selected' : ''; ?>>
                                                            <?= $unit ?>
                                                        </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </span>
                                                <span class="input-group-addon tw-px-2 tw-border-l-0">

                                                    <a href="#metered" class="mark_metered" data-toggle="tooltip"
                                                        data-title="<?= _l('perfex_saas_mark_limited'); ?>"
                                                        onclick="return false;"
                                                        style="<?= $unlimited ? '' : 'display:none'; ?>">
                                                        <i class="<?= 'fa fa-dashboard'; ?>"></i>
                                                    </a>

                                                    <a href="#infinity" class="mark_infinity" data-toggle="tooltip"
                                                        data-title="<?= _l('perfex_saas_mark_unlimited'); ?>"
                                                        onclick="return false;"
                                                        style="<?= $unlimited ? 'display:none' : ''; ?>">
                                                        <i class="<?= 'fa fa-infinity'; ?>"></i>
                                                    </a>
                                                </span>
                                            </div>
                                            <div class="input-group" data-toggle="tooltip"
                                                data-title="<?= _l('perfex_saas_limit_extra_hint', $cycle_period); ?>">
                                                <span
                                                    class="input-group-addon tw-px-2 tw-border-r-0"><?= _l('perfex_saas_limit_extra'); ?></span>
                                                <input type="number" value="<?= $value_unit_price; ?>" step="0.01"
                                                    min="0" class="form-control"
                                                    name="<?= "metadata[storage_limit][unit_price]"; ?>">
                                                <span
                                                    class="input-group-addon tw-px-2 tw-border-l-0"><?= $currency->name; ?></span>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="tw-mt-4 tw-mb-4">
                                    <hr />
                                </div>

                                <!-- tenant dashboard quota visibility -->
                                <div class="row">
                                    <div class="col-md-12 mtop10 col-xs-12">
                                        <?php $quota_display = isset($package->metadata->dashboard_quota_visibility) ? $package->metadata->dashboard_quota_visibility : 'limited-only'; ?>
                                        <?= render_select('metadata[dashboard_quota_visibility]', [
                                            ['key' => 'yes', 'label' => _l('perfex_saas_quota_display_all')],
                                            ['key' => 'limited-only', 'label' => _l('perfex_saas_quota_display_limited_only')],
                                            ['key' => 'no', 'label' => _l('perfex_saas_quota_display_no')],
                                            ['key' => 'no-for-all', 'label' => _l('perfex_saas_quota_display_no_for_all')]
                                        ], ['key', ['label']], 'perfex_saas_tenant_dashboard_quota_visibility', $quota_display); ?>
                                    </div>

                                    <div class="col-md-12 mtop10 col-xs-12">
                                        <?php $hidden_quota_widgets = isset($package->metadata->hidden_quota_widgets) ? $package->metadata->hidden_quota_widgets : []; ?>
                                        <?= perfex_saas_render_select('metadata[hidden_quota_widgets][]', $hidden_quota_widgets_options, ['key', ['label']], 'perfex_saas_tenant_hidden_quota_widgets', $hidden_quota_widgets, $select_attr); ?>
                                    </div>


                                </div>

                            </div>
                        </div>

                        <!-- Hidden settings/customer_profile/project tabs -->
                        <div class="panel_s">
                            <div class="panel-body">

                                <h4 class="tw-mb-4">
                                    <?= _l('perfex_saas_tabs_visibility_control'); ?>
                                </h4>
                                <p><?= _l('perfex_saas_tabs_visibility_control_hint'); ?></p>
                                <div class="mtop15"></div>
                                <hr />

                                <?php
                                foreach (perfex_saas_app_tabs_group() as $group) :
                                    $selected = $package->metadata->hidden_app_tabs->{$group} ?? [];
                                ?>
                                <div class="form-group">
                                    <label
                                        for="hide-tab-<?= $group; ?>"><?= _l('perfex_saas_tabs_group_' . $group); ?></label>
                                    <input name="metadata[hidden_app_tabs][<?= $group; ?>][]" value="" type="hidden" />
                                    <select name="metadata[hidden_app_tabs][<?= $group; ?>][]"
                                        class="form-control selectpicker" multiple="multiple"
                                        id="hide-tab-<?= $group; ?>"
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">

                                        <?php
                                            $children = $this->app_tabs->get($group);
                                            if ($group === 'settings' && method_exists($this->app, 'get_settings_sections')) {
                                                $children = perfex_saas_app_settings_tabs();
                                            }

                                            foreach ($children as $key => $value) :
                                                $slug = $value['slug'];
                                                if (stripos($slug, PERFEX_SAAS_MODULE_NAME) !== false) continue;
                                            ?>
                                        <option <?= in_array($slug, $selected) ? 'selected' : ''; ?>
                                            value="<?= $slug; ?>">
                                            <?= $value['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <?php endforeach; ?>

                            </div>
                        </div>
                    </div>
                    <!-- End right column -->

                    <!-- Discounts -->
                    <div class="col-lg-<?= $single_pricing_mode ? '7' : '5'; ?>" id="discounts">
                        <div class="panel_s">
                            <div class="panel-body">

                                <h4 class="tw-mb-4">
                                    <?= _l('perfex_saas_pricing_discounts'); ?>
                                </h4>
                                <p><?= _l('perfex_saas_pricing_discounts_hint'); ?></p>
                                <div class="mtop15"></div>
                                <div class="tw-flex tw-items-center tw-justify-between tw-mb-2"
                                    data-repeat-list="#discount-list">
                                    <div class="col-md-4" data-toggle="tooltip"
                                        data-title="<?= _l('perfex_saas_select_an_option'); ?>">
                                        <select class="form-control tw-p-1"
                                            data-repeat-list-input="metadata[discounts][limits][]">
                                            <option value="tenant_instance">
                                                <?= _l('perfex_saas_limit_tenant_instance'); ?>
                                            </option>
                                            <option value="storage"><?= _l('perfex_saas_limit_storage'); ?></option>

                                            <?php foreach (PERFEX_SAAS_LIMIT_FILTERS_TABLES_MAP as $event => $limit) : ?>
                                            <option value="<?= $limit; ?>"><?= _l('perfex_saas_limit_' . $limit); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3" data-toggle="tooltip"
                                        data-title="<?= _l('perfex_saas_minimum_unit'); ?>">
                                        <input type="number" step="1" min="2" class="form-control tw-p-1" value="5"
                                            data-repeat-list-input="metadata[discounts][units][]" />
                                    </div>

                                    <div class="col-md-3">
                                        <div class="input-group" data-class="col-md-3" data-toggle="tooltip"
                                            data-title="<?= _l('perfex_saas_discount_percent'); ?>">
                                            <input type="number" step="0.01" min="1" class="form-control tw-p-1"
                                                data-repeat-list-input="metadata[discounts][percents][]" />
                                            <span class="input-group-addon tw-px-2 tw-border-l-0">%</span>
                                        </div>
                                    </div>
                                    <div>
                                        <button type="button" class="tw-rounded btn btn-secondary tw-ml-2"><i
                                                class="fa fa-plus"></i></button>
                                    </div>
                                </div>

                                <hr />
                                <div id="discount-list">
                                    <?php $discounts = (object)($package->metadata->discounts ?? ['limits' => []]); ?>
                                    <?php foreach ($discounts->limits as $index => $limit) : ?>
                                    <?php $units = $discounts->units[$index];
                                        $percent = $discounts->percents[$index]; ?>
                                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-2">
                                        <div class="col-md-4">
                                            <input type="hidden" name="metadata[discounts][limits][]"
                                                value="<?= $limit; ?>" />
                                            <?= _l('perfex_saas_limit_' . $limit); ?>
                                        </div>
                                        <div class="col-md-3"><input type="hidden" name="metadata[discounts][units][]"
                                                value="<?= $units; ?>" />
                                            <?= $units; ?>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="hidden" name="metadata[discounts][percents][]"
                                                value="<?= $percent; ?>" /><?= $percent; ?>%
                                        </div>
                                        <button class="btn btn-danger remove-parent tw-ml-2" type="button">-</button>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Advance info -->
                    <div class="col-md-12">
                        <div class="panel_s">
                            <div class="panel-body">
                                <!-- DB scheme -->
                                <?php $value = (isset($package) ? $package->db_scheme : ''); ?>
                                <?php $db_schemes = $this->perfex_saas_model->db_schemes(); ?>
                                <?= render_select('db_scheme', $db_schemes, ['key', ['label']], 'perfex_saas_db_scheme', $value, [], [], '', '', false); ?>

                                <!-- DB pools -->
                                <div
                                    class="form-group tw-mt-10 tw-mb-8 db_pools <?= in_array($value, ['shard', 'single_pool']) ? '' : 'hidden'; ?>">
                                    <label><?= _l('perfex_saas_db_pools'); ?></label>
                                    <div class="tw-flex tw-items-center tw-mt-4 tw-justify-center">
                                        <div class="tw-flex tw-space-x-2 pool-template tw-items-center">
                                            <?= render_input('db_pools[host][]', 'perfex_saas_db_host', '', 'text', ['placeholder' => 'localhost']); ?>
                                            <?= render_input('db_pools[user][]', 'perfex_saas_db_user', '', 'text', ['placeholder' => 'root']); ?>
                                            <?= render_input('db_pools[password][]', 'perfex_saas_db_password', '', 'text', ['placeholder' => 'password']); ?>
                                            <?= render_input('db_pools[dbname][]', 'perfex_saas_db_name', ''); ?>
                                        </div>
                                        <div class="tw-mt-2 tw-ml-2">
                                            <button type="button" class="btn pull-right btn-primary" id="add-pool"><i
                                                    class="fa fa-check"></i></button>
                                        </div>
                                    </div>

                                    <div id="pools"
                                        class="tw-flex tw-flex-col tw-items-center tw-mt-4 tw-justify-center tw-mx-auto">
                                        <hr class="tw-mt-4 tw-mb-4" />
                                        <?php $pools = !empty($package->db_pools) ? $package->db_pools : (!empty($this->session->flashdata('db_pools')) ? $this->session->flashdata('db_pools') : []); ?>
                                        <?php if (!empty($pools)) : ?>
                                        <?php foreach ($pools as $key => $pool) : $pool = (object)$pool; ?>
                                        <div class="tw-flex tw-space-x-2 tw-items-center">
                                            <?= render_input('db_pools[host][]', '', $pool->host, 'text', ['placeholder' => 'localhost']); ?>
                                            <?= render_input('db_pools[user][]', '', $pool->user, 'text', ['placeholder' => 'root']); ?>
                                            <?= render_input('db_pools[password][]', '', $pool->password, 'text', ['placeholder' => 'password']); ?>
                                            <?= render_input('db_pools[dbname][]', '', $pool->dbname); ?>
                                            <div class="tw-mb-4">
                                                <button type="button" class="btn pull-right btn-danger remove-pool">
                                                    <i class="fa fa-times"></i></button>
                                            </div>
                                        </div>
                                        <?php endforeach ?>
                                        <?php endif ?>
                                    </div>
                                </div>

                                <a href="javascript:;" class="text-primary tw-mt-8 tw-mb-2 tw-block"
                                    id="advance-settings-toggle">
                                    <?= _l('perfex_saas_advance_settings'); ?>
                                </a>
                                <div class="advance-settings hidden">

                                    <!-- shared settings selection -->
                                    <div class="tw-mt-10 tw-mb-10">
                                        <?php $selected_shared = (isset($package->metadata->shared_settings->shared) ? $package->metadata->shared_settings->shared : set_value('metadata[shared_settings][shared][]', [])); ?>
                                        <?php $selected_masked = (isset($package->metadata->shared_settings->masked) ? $package->metadata->shared_settings->masked : set_value('metadata[shared_settings][masked][]', [])); ?>
                                        <?php $selected_enforced = (isset($package->metadata->shared_settings->enforced) ? $package->metadata->shared_settings->enforced : set_value('metadata[shared_settings][enforced][]', [])); ?>
                                        <?php $options = $this->perfex_saas_model->shared_options(); ?>

                                        <label for="sharedfilter"><?= _l('perfex_saas_package_shared_settings'); ?>
                                            <span data-toggle="tooltip"
                                                data-title="<?= _l('perfex_saas_package_shared_settings_hint'); ?>"><i
                                                    class="fa fa-question-circle"></i></span></label>
                                        <input id="sharedfilter" type="text" class="form-control tw-mb-2"
                                            placeholder="<?= _l('perfex_saas_filter_shared_settings'); ?>" />
                                        <div class="shared_settings tw-overflow-y-auto" style="height:35vh">
                                            <div class="row w-ml-1 tw-mr-1">
                                                <?php foreach ($options as $option) :
                                                    $key = $option->key;
                                                    $name = $option->name; ?>
                                                <div class="col-md-4 col-sm-6 col-xs-12 item">
                                                    <div class="tw-flex form-group share-row">
                                                        <div data-toggle="tooltip"
                                                            data-title="<?= _l('perfex_saas_share'); ?>">
                                                            <div class="checkbox checkbox-inline share-checkbox">
                                                                <input type="checkbox"
                                                                    name="metadata[shared_settings][shared][]"
                                                                    <?= in_array($key, $selected_shared) ? 'checked' : ''; ?>
                                                                    value="<?= $key ?>" />
                                                                <!-- ensure white space between the label -->
                                                                <label class="tw-capitalize"> </label>
                                                            </div>
                                                        </div>
                                                        <div data-toggle="tooltip"
                                                            data-title="<?= _l('perfex_saas_mask'); ?>">
                                                            <div class="checkbox checkbox-inline mask-checkbox">
                                                                <input type="checkbox"
                                                                    name="metadata[shared_settings][masked][]"
                                                                    <?= in_array($key, $selected_masked) ? 'checked' : ''; ?>
                                                                    value="<?= $key ?>" />
                                                                <!-- ensure white space between the label -->
                                                                <label class=""> </label>
                                                            </div>
                                                        </div>
                                                        <div data-toggle="tooltip"
                                                            data-title="<?= _l('perfex_saas_enforce'); ?>">
                                                            <div class="checkbox checkbox-inline enforce-checkbox">
                                                                <input type="checkbox"
                                                                    name="metadata[shared_settings][enforced][]"
                                                                    <?= in_array($key, $selected_enforced) ? 'checked' : ''; ?>
                                                                    value="<?= $key ?>" />
                                                                <!-- ensure white space between the label -->
                                                                <label class=""> </label>
                                                            </div>
                                                        </div>

                                                        <label
                                                            class="tw-capitalize text-capitalize"><?= $name ?></label>
                                                    </div>
                                                </div>
                                                <?php endforeach ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Customization -->
                                    <?php $selected = $package->metadata->allow_customization ?? 'yes'; ?>
                                    <?= render_select('metadata[allow_customization]', $yes_no_options, ['key', ['label']], 'perfex_saas_allow_package_customization', $selected); ?>

                                    <!-- disable module marketplace -->
                                    <?php $selected = $package->metadata->disable_module_marketplace ?? 'no'; ?>
                                    <?= render_select('metadata[disable_module_marketplace]', $yes_no_options, ['key', ['label']], 'perfex_saas_disable_module_marketplace_for_package', $selected); ?>

                                    <!-- disable service marketplace -->
                                    <?php $selected = $package->metadata->disable_service_marketplace ?? 'no'; ?>
                                    <?= render_select('metadata[disable_service_marketplace]', $yes_no_options, ['key', ['label']], 'perfex_saas_disable_service_marketplace_for_package', $selected); ?>

                                    <!-- assigned client ids -->
                                    <?php
                                    $selected = $package->metadata->assigned_clients ?? [];
                                    if (is_string($selected)) $selected = json_decode($selected);
                                    $this->load->view('includes/client_select', ['name' => 'metadata[assigned_clients][]', 'label' => perfex_saas_input_label_with_hint('perfex_saas_package_assigned_clients'), 'value' => $selected]);
                                    ?>

                                    <!-- sales agent -->
                                    <?php
                                    $i = 0;
                                    $selected = '';
                                    if (!empty($invoice->sale_agent)) {
                                        foreach ($staff as $member) {

                                            if ($invoice->sale_agent == $member['staffid']) {
                                                $selected = $member['staffid'];
                                            }

                                            $i++;
                                        }
                                    }
                                    echo render_select('metadata[invoice][sale_agent]', $staff, ['staffid', ['firstname', 'lastname']], 'sale_agent_string', $selected);
                                    ?>

                                    <div class="tw-mt-4 tw-mb-4">
                                        <hr />
                                    </div>

                                    <!-- Slug customization -->
                                    <?php if (!$single_pricing_mode) : ?>
                                    <?php $value = (isset($package) ? $package->slug : ''); ?>
                                    <?= render_input('slug', 'perfex_saas_slug', $value); ?>
                                    <?php endif ?>

                                    <div class="tw-mt-4 tw-mb-4">
                                        <hr />
                                    </div>

                                    <!-- priority -->
                                    <?php $value = (isset($package->metadata->priority) ? $package->metadata->priority : 1); ?>
                                    <?= render_input('metadata[priority]', _l('perfex_saas_package_priority'), $value, 'number', ['min' => 0, 'step' => '1']); ?>


                                    <!-- Custom tenant seed source -->
                                    <div class="form-group">
                                        <label for="w-full">
                                            <?php $key = 'perfex_saas_tenant_seeding_source'; ?>
                                            <?= perfex_saas_input_label_with_hint($key, $key . '_hint'); ?>
                                        </label>
                                        <?php $seeding_tenant_slug = $package->metadata->seeding_tenant ?? ''; ?>
                                        <?php $this->load->view(PERFEX_SAAS_MODULE_NAME . '/includes/tenant_select', ['name' => 'metadata[seeding_tenant]', 'value' => $seeding_tenant_slug]); ?>
                                        <small
                                            class="text text-warning"><?= _l('perfex_saas_tenant_seeding_source_package_note'); ?></small>
                                    </div>
                                    <div class="tw-mt-4 tw-mb-4">
                                        <hr />
                                    </div>
                                    <!-- Instance autoremoval -->
                                    <?php $selected = $package->metadata->auto_remove_inactive_instance ?? 'no'; ?>
                                    <?= render_select('metadata[auto_remove_inactive_instance]', $yes_no_options, ['key', ['label']], _l('perfex_saas_auto_remove_inactive_instance'), $selected); ?>

                                    <div class="auto_remove_inactive_instance deps">
                                        <?= render_input('metadata[auto_remove_inactive_instance_days]', _l('perfex_saas_auto_remove_inactive_instance_days') . ' ' . perfex_saas_form_label_hint('perfex_saas_auto_remove_inactive_instance_days_hint'), $package->metadata->auto_remove_inactive_instance_days_hint ?? 30, 'number', ['min' => PERFEX_SAAS_MINIMUM_AUTO_INSTANCE_REMOVE_GRACE_PERIOD]); ?>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <button type="submit" data-loading-text="<?= _l('perfex_saas_saving...'); ?>"
                                        data-form="#packages_form" class="btn btn-primary mtop15
                                    mbot15"><?= _l('perfex_saas_submit'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End advance info -->

                </div>
                <!-- end row-->

                <?= form_close(); ?>

            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
"use strict";

$(document).ready(function() {
    saasPackageFormScript();
    saasFormRepeat();
});
</script>
</body>

</html>