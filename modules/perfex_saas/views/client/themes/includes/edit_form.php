<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- edit form -->
<div class="edit-form tw-w-full text-left <?= $theme_name == 'single' ? 'panel panel-body' : 'tw-mt-4'; ?>"
    style="display:none">
    <?= form_open(base_url('clients/companies/edit/' . $company->slug), ['id' => 'company_edit_form']); ?>
    <?= render_input('name', 'perfex_saas_company_name', $company->name, 'text', [], [], "text-left tw-mb-4 $centered_size", $input_class); ?>

    <!-- disabled modules selection -->
    <?php $selected = (!empty($company->metadata->disabled_modules) ? (array)$company->metadata->disabled_modules : []); ?>
    <?php
    $modules = perfex_saas_tenant_modules($company, false, true);
    $_modules = $CI->perfex_saas_model->modules();
    ?>
    <?php $label = _l('perfex_saas_disabled_modules') . perfex_saas_form_label_hint('perfex_saas_disabled_modules_hint'); ?>
    <div class="form-group text-left tw-mb-4 col-md-12 open-ticket-department-group tw-block">
        <label for="disabled_modules"><?= $label; ?></label>

        <!-- Set this input to ensure the value can be cleared i.e emptied without selecting the blank option -->
        <input name="disabled_modules[]" value="" type="hidden" />
        <select data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" name="disabled_modules[]"
            id="disabled_modules" class="form-control selectpicker" multiple>
            <option value=""></option>
            <?php foreach ($modules as $module) { ?>
            <option value="<?= $module; ?>" <?= in_array($module, $selected) ? 'selected' : ''; ?>>
                <?= $_modules[$module]['custom_name'] ?? $module; ?>
            </option>
            <?php } ?>
        </select>
        <?php echo form_error('disabled_modules'); ?>
    </div>

    <div class="text-center">
        <button type="button" class="btn btn-default mtop15 mbot15 close-btn"><?= _l('perfex_saas_cancel'); ?></button>
        <button type="submit" data-loading-text="<?= _l('perfex_saas_saving...'); ?>" data-form="#company_edit_form"
            class="btn btn-primary mtop15 mbot15"><?= _l('perfex_saas_submit'); ?></button>
    </div>
    <?= form_close(); ?>
</div>
<!-- end edit form -->