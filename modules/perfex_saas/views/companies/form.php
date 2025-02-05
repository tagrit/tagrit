<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php $select_attr = ['multiple' => 'true', 'allow_blank' => 'true', 'class' => 'selectpicker display-block', 'data-actions-box' => 'true', 'data-width' => '100%']; ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-7">
                <h4
                    class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700 tw-flex tw-items-center tw-space-x-2">
                    <span>
                        <?php echo isset($company) ? $company->name : _l('perfex_saas_new_company'); ?>
                    </span>
                </h4>
                <div class="panel_s">
                    <div class="panel-body">

                        <?php echo validation_errors('<div class="alert alert-danger text-center">', '</div>'); ?>
                        <?php $this->load->view('authentication/includes/alerts'); ?>

                        <?php echo form_open_multipart($this->uri->uri_string(), ['id' => 'companies_form']); ?>

                        <?php if (isset($company)) echo form_hidden('id', $company->id); ?>

                        <?php $value = (isset($company) ? $company->name : ''); ?>
                        <?php echo render_input('name', 'name', $value); ?>

                        <?php if (!isset($company))
                            echo render_input('slug', _l('perfex_saas_create_company_slug') . perfex_saas_form_label_hint('perfex_saas_create_company_slug_hint', perfex_saas_get_saas_default_host()), $value, 'text', ['maxlength' => PERFEX_SAAS_MAX_SLUG_LENGTH]);
                        ?>

                        <?php $value = isset($company) ? $company->custom_domain : ''; ?>
                        <?= render_input('custom_domain', _l('perfex_saas_custom_domain') . perfex_saas_form_label_hint('perfex_saas_custom_domain_hint'), $value, 'text', [], [], "text-left tw-mb-4"); ?>


                        <!-- contact selection -->
                        <div class="form-group select-placeholder">
                            <label for="clientid"
                                class="control-label"><?php echo _l('perfex_saas_invoice_select_customer'); ?></label>
                            <select id="clientid" name="clientid" data-live-search="true" data-width="100%"
                                class="ajax-search<?php if (isset($company) && empty($company->clientid)) {
                                                                                                                                    echo ' customer-removed';
                                                                                                                                } ?>"
                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                <?php $selected = (isset($company) ? $company->clientid : '');
                                if ($selected == '') {
                                    $selected = (isset($customer_id) ? $customer_id : '');
                                }
                                if ($selected != '') {
                                    $rel_data = get_relation_data('customer', $selected);
                                    $rel_val  = get_relation_values($rel_data, 'customer');
                                    echo '<option value="' . $rel_val['id'] . '" selected>' . $rel_val['name'] . '</option>';
                                } ?>
                            </select>
                        </div>

                        <?php $modules = $this->perfex_saas_model->modules(); ?>


                        <!-- admin assigned modules selection -->
                        <div class="tw-mt-8 tw-mb-8">
                            <?php $selected = (isset($company->metadata->admin_approved_modules) ? $company->metadata->admin_approved_modules : []); ?>
                            <?php $label = _l('perfex_saas_admin_approved_modules') . perfex_saas_form_label_hint("perfex_saas_admin_approved_modules_hint"); ?>

                            <?php echo perfex_saas_render_select('metadata[admin_approved_modules][]', $modules, ['system_name', ['custom_name']], $label, $selected, $select_attr); ?>
                        </div>


                        <!-- admin disabled modules selection -->
                        <div class="tw-mt-8 tw-mb-8">
                            <?php $selected = (isset($company->metadata->admin_disabled_modules) ? $company->metadata->admin_disabled_modules : []); ?>
                            <?php $label = _l('perfex_saas_admin_disabled_modules') . perfex_saas_form_label_hint('perfex_saas_admin_disabled_modules_hint'); ?>
                            <?php echo perfex_saas_render_select('metadata[admin_disabled_modules][]', $modules, ['system_name', ['custom_name']], $label, $selected, $select_attr); ?>
                        </div>

                        <!-- disabled default modules -->
                        <div class="tw-mt-8 tw-mb-8">
                            <?php $selected = $company->metadata->admin_disabled_default_modules ?? ''; ?>
                            <?php $default_modules = $this->perfex_saas_model->default_modules(); ?>
                            <?php $label =  _l('perfex_saas_disabled_default_modules') . perfex_saas_form_label_hint('perfex_saas_disabled_default_modules_hint'); ?>
                            <?= perfex_saas_render_select('metadata[admin_disabled_default_modules][]', $default_modules, ['system_name', ['custom_name']], $label, $selected, $select_attr); ?>
                        </div>

                        <!-- status selection -->
                        <?php if (isset($company) && $company->status != PERFEX_SAAS_STATUS_PENDING) : ?>
                        <div class="tw-mt-8 tw-mb-8">
                            <?php $selected = (isset($company) ? $company->status : ''); ?>
                            <?php $company_status_list = $this->perfex_saas_model->company_status_list(); ?>
                            <?php echo render_select('status', $company_status_list, ['key', ['label']], 'perfex_saas_company_status', $selected, [], [], '', '', false); ?>
                        </div>
                        <?php endif ?>


                        <?php if (!isset($company) || (empty($company->dsn) && !empty($company->status) && $company->status == PERFEX_SAAS_STATUS_PENDING)) : ?>
                        <!-- DB scheme -->
                        <?php $value = (isset($company->db_scheme) ? $company->db_scheme : (isset($company) && empty($company->dsn) ? 'shard' : '')); ?>
                        <?php $db_schemes = $this->perfex_saas_model->db_schemes_alt(); ?>
                        <?php echo render_select('db_scheme', $db_schemes, ['key', ['label']], 'perfex_saas_db_scheme', $value, [], [], '', '', false); ?>

                        <!-- DB pools -->
                        <div
                            class="form-group tw-mt-8 tw-mb-8 db_pools <?= in_array($value, ['shard', 'single_pool']) ? '' : 'hidden'; ?>">
                            <?php require('partials/db_pool.php'); ?>
                        </div>
                        <?php endif ?>


                        <div class="text-right">
                            <button type="submit" data-loading-text="<?= _l('perfex_saas_saving...'); ?>"
                                data-form="#companies_form"
                                class="btn btn-primary mtop15 mbot15"><?php echo _l('perfex_saas_submit'); ?></button>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
            <?php if (!empty($company)) : ?>
            <div class="col-md-5">
                <?= $this->load->view("companies/view", ['company' => $company]); ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
"use strict";

<?php if (isset($company)) : ?>
const perfex_saas_company_id = "<?= $company->id ?>";
<?php endif ?>
$(document).ready(function() {
    saasCompanyFormScript();
});
</script>
</body>

</html>