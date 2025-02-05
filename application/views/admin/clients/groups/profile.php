<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php if (isset($client)) { ?>
<h4 class="customer-profile-group-heading">
    <?= _l('client_add_edit_profile'); ?>
</h4>
<?php } ?>

<div class="row">
    <?= form_open($this->uri->uri_string(), ['class' => 'client-form', 'autocomplete' => 'off']); ?>
    <div class="additional"></div>
    <div class="col-md-12">
        <div class="horizontal-scrollable-tabs panel-full-width-tabs">
            <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
            <div class="horizontal-tabs">
                <ul class="nav nav-tabs customer-profile-tabs nav-tabs-horizontal" role="tablist">
                    <li role="presentation"
                        class="<?= ! $this->input->get('tab') ? 'active' : ''; ?>">
                        <a href="#contact_info" aria-controls="contact_info" role="tab" data-toggle="tab">
                            <?= _l('customer_profile_details'); ?>
                        </a>
                    </li>
                    <?php
                  $customer_custom_fields = false;
if (total_rows(db_prefix() . 'customfields', ['fieldto' => 'customers', 'active' => 1]) > 0) {
    $customer_custom_fields = true; ?>
                    <li role="presentation"
                        class="<?= $this->input->get('tab') == 'custom_fields' ? 'active' : ''; ?>">
                        <a href="#custom_fields" aria-controls="custom_fields" role="tab" data-toggle="tab">
                            <?= hooks()->apply_filters('customer_profile_tab_custom_fields_text', _l('custom_fields')); ?>
                        </a>
                    </li>
                    <?php } ?>
                    <li role="presentation">
                        <a href="#billing_and_shipping" aria-controls="billing_and_shipping" role="tab"
                            data-toggle="tab">
                            <?= _l('billing_shipping'); ?>
                        </a>
                    </li>
                    <?php hooks()->do_action('after_customer_billing_and_shipping_tab', $client ?? false); ?>
                    <?php if (isset($client)) { ?>
                    <li role="presentation">
                        <a href="#customer_admins" aria-controls="customer_admins" role="tab" data-toggle="tab">
                            <?= _l('customer_admins'); ?>
                            <?php if (count($customer_admins) > 0) { ?>
                            <span
                                class="badge bg-default"><?= count($customer_admins) ?></span>
                            <?php } ?>
                        </a>
                    </li>
                    <?php hooks()->do_action('after_customer_admins_tab', $client); ?>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <div class="tab-content mtop15">
            <?php hooks()->do_action('after_custom_profile_tab_content', $client ?? false); ?>
            <?php if ($customer_custom_fields) { ?>
            <div role="tabpanel"
                class="tab-pane<?= $this->input->get('tab') == 'custom_fields' ? ' active' : ''; ?>"
                id="custom_fields">
                <div class="row">
                    <div class="col-md-8">
                        <?php $rel_id = (isset($client) ? $client->userid : false); ?>
                        <?= render_custom_fields('customers', $rel_id); ?>
                    </div>
                </div>
            </div>
            <?php } ?>
            <div role="tabpanel"
                class="tab-pane<?= ! $this->input->get('tab') ? ' active' : ''; ?>"
                id="contact_info">
                <div class="row">
                    <div class="col-md-12<?= isset($client) && (! is_empty_customer_company($client->userid) && total_rows(db_prefix() . 'contacts', ['userid' => $client->userid, 'is_primary' => 1]) > 0) ? '' : ' hide'; ?>"
                        id="client-show-primary-contact-wrapper">
                        <div class="checkbox checkbox-info mbot20 no-mtop">
                            <input type="checkbox" name="show_primary_contact"
                                <?= isset($client) && $client->show_primary_contact == 1 ? 'checked' : ''; ?>
                            value="1" id="show_primary_contact">
                            <label
                                for="show_primary_contact"><?= _l('show_primary_contact', _l('invoices') . ', ' . _l('estimates') . ', ' . _l('payments') . ', ' . _l('credit_notes')); ?></label>
                        </div>
                    </div>
                    <div
                        class="col-md-<?= ! isset($client) ? 12 : 8; ?>">
                        <?php hooks()->do_action('before_customer_profile_company_field', $client ?? null); ?>
                        <?php $value = (isset($client) ? $client->company : ''); ?>
                        <?php $attrs = (isset($client) ? [] : ['autofocus' => true]); ?>
                        <?= render_input('company', 'client_company', $value, 'text', $attrs); ?>
                        <div id="company_exists_info" class="hide"></div>
                        <?php hooks()->do_action('after_customer_profile_company_field', $client ?? null); ?>
                        <?php if (get_option('company_requires_vat_number_field') == 1) {
                            $value = (isset($client) ? $client->vat : '');
                            echo render_input('vat', 'client_vat_number', $value);
                        } ?>
                        <?php hooks()->do_action('before_customer_profile_phone_field', $client ?? null); ?>
                        <?php $value = (isset($client) ? $client->phonenumber : ''); ?>
                        <?= render_input('phonenumber', 'client_phonenumber', $value); ?>
                        <?php hooks()->do_action('after_customer_profile_company_phone', $client ?? null); ?>
                        <?php if ((isset($client) && empty($client->website)) || ! isset($client)) {
                            $value = (isset($client) ? $client->website : '');
                            echo render_input('website', 'client_website', $value);
                        } else { ?>
                        <div class="form-group">
                            <label
                                for="website"><?= _l('client_website'); ?></label>
                            <div class="input-group">
                                <input type="text" name="website" id="website"
                                    value="<?= e($client->website); ?>"
                                    class="form-control">
                                <span class="input-group-btn">
                                    <a href="<?= e(maybe_add_http($client->website)); ?>"
                                        class="btn btn-default" target="_blank" tabindex="-1">
                                        <i class="fa fa-globe"></i></a>
                                </span>

                            </div>
                        </div>
                        <?php }
                        $selected = [];
if (isset($customer_groups)) {
    foreach ($customer_groups as $group) {
        array_push($selected, $group['groupid']);
    }
}
if (is_admin() || get_option('staff_members_create_inline_customer_groups') == '1') {
    echo render_select_with_input_group('groups_in[]', $groups, ['id', 'name'], 'customer_groups', $selected, '<div class="input-group-btn"><a href="#" class="btn btn-default" data-toggle="modal" data-target="#customer_group_modal"><i class="fa fa-plus"></i></a></div>', ['multiple' => true, 'data-actions-box' => true], [], '', '', false);
} else {
    echo render_select('groups_in[]', $groups, ['id', 'name'], 'customer_groups', $selected, ['multiple' => true, 'data-actions-box' => true], [], '', '', false);
}
?>
                        <div class="row">
                            <div
                                class="col-md-<?= ! is_language_disabled() ? 6 : 12; ?>">
                                <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1"
                                    data-toggle="tooltip"
                                    data-title="<?= _l('customer_currency_change_notice'); ?>"></i>
                                <?php
$s_attrs  = ['data-none-selected-text' => _l('system_default_string')];
$selected = '';
if (isset($client) && client_have_transactions($client->userid)) {
    $s_attrs['disabled'] = true;
}

foreach ($currencies as $currency) {
    if (isset($client)) {
        if ($currency['id'] == $client->default_currency) {
            $selected = $currency['id'];
        }
    }
}
// Do not remove the currency field from the customer profile!
echo render_select('default_currency', $currencies, ['id', 'name', 'symbol'], 'invoice_add_edit_currency', $selected, $s_attrs);
?>
                            </div>
                            <?php if (! is_language_disabled()) { ?>
                            <div class="col-md-6">
                                <div class="form-group select-placeholder">
                                    <label for="default_language"
                                        class="control-label"><?= _l('localization_default_language'); ?>
                                    </label>
                                    <select name="default_language" id="default_language"
                                        class="form-control selectpicker"
                                        data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>">
                                        <option value="">
                                            <?= _l('system_default_string'); ?>
                                        </option>
                                        <?php foreach ($this->app->get_available_languages() as $availableLanguage) {
                                            $selected = '';
                                            if (isset($client)) {
                                                if ($client->default_language == $availableLanguage) {
                                                    $selected = 'selected';
                                                }
                                            } ?>
                                        <option
                                            value="<?= e($availableLanguage); ?>"
                                            <?= e($selected); ?>>
                                            <?= e(ucfirst($availableLanguage)); ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <?php } ?>
                        </div>

                        <hr />

                        <?php $value = (isset($client) ? $client->address : ''); ?>
                        <?= render_textarea('address', 'client_address', $value); ?>
                        <?php $value = (isset($client) ? $client->city : ''); ?>
                        <?= render_input('city', 'client_city', $value); ?>
                        <?php $value = (isset($client) ? $client->state : ''); ?>
                        <?= render_input('state', 'client_state', $value); ?>
                        <?php $value = (isset($client) ? $client->zip : ''); ?>
                        <?= render_input('zip', 'client_postal_code', $value); ?>
                        <?php $countries = get_all_countries();
$customer_default_country                = get_option('customer_default_country');
$selected                                = (isset($client) ? $client->country : $customer_default_country);
echo render_select('country', $countries, ['country_id', ['short_name']], 'clients_country', $selected, ['data-none-selected-text' => _l('dropdown_non_selected_tex')]);
?>
                    </div>
                </div>
            </div>
            <?php if (isset($client)) { ?>
            <div role="tabpanel" class="tab-pane" id="customer_admins">
                <?php if (staff_can('create', 'customers') || staff_can('edit', 'customers')) { ?>
                <a href="#" data-toggle="modal" data-target="#customer_admins_assign"
                    class="btn btn-primary mbot30"><?= _l('assign_admin'); ?></a>
                <?php } ?>
                <table class="table dt-table">
                    <thead>
                        <tr>
                            <th><?= _l('staff_member'); ?>
                            </th>
                            <th><?= _l('customer_admin_date_assigned'); ?>
                            </th>
                            <?php if (staff_can('create', 'customers') || staff_can('edit', 'customers')) { ?>
                            <th><?= _l('options'); ?>
                            </th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customer_admins as $c_admin) { ?>
                        <tr>
                            <td><a
                                    href="<?= admin_url('profile/' . $c_admin['staff_id']); ?>">
                                    <?= staff_profile_image($c_admin['staff_id'], [
                                        'staff-profile-image-small',
                                        'mright5',
                                    ]);
                            echo e(get_staff_full_name($c_admin['staff_id'])); ?></a>
                            </td>
                            <td
                                data-order="<?= e($c_admin['date_assigned']); ?>">
                                <?= e(_dt($c_admin['date_assigned'])); ?>
                            </td>
                            <?php if (staff_can('create', 'customers') || staff_can('edit', 'customers')) { ?>
                            <td>
                                <a href="<?= admin_url('clients/delete_customer_admin/' . $client->userid . '/' . $c_admin['staff_id']); ?>"
                                    class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 _delete">
                                    <i class="fa-regular fa-trash-can fa-lg"></i>
                                </a>
                            </td>
                            <?php } ?>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <?php } ?>
            <div role="tabpanel" class="tab-pane" id="billing_and_shipping">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <h4
                                    class="tw-font-semibold tw-text-base tw-text-neutral-700 tw-flex tw-justify-between tw-items-center tw-mt-0 tw-mb-6">
                                    <?= _l('billing_address'); ?>
                                    <a href="#"
                                        class="billing-same-as-customer tw-text-sm tw-text-neutral-500 hover:tw-text-neutral-700 active:tw-text-neutral-700">
                                        <?= _l('customer_billing_same_as_profile'); ?>
                                    </a>
                                </h4>

                                <?php $value = (isset($client) ? $client->billing_street : ''); ?>
                                <?= render_textarea('billing_street', 'billing_street', $value); ?>
                                <?php $value = (isset($client) ? $client->billing_city : ''); ?>
                                <?= render_input('billing_city', 'billing_city', $value); ?>
                                <?php $value = (isset($client) ? $client->billing_state : ''); ?>
                                <?= render_input('billing_state', 'billing_state', $value); ?>
                                <?php $value = (isset($client) ? $client->billing_zip : ''); ?>
                                <?= render_input('billing_zip', 'billing_zip', $value); ?>
                                <?php $selected = (isset($client) ? $client->billing_country : ''); ?>
                                <?= render_select('billing_country', $countries, ['country_id', ['short_name']], 'billing_country', $selected, ['data-none-selected-text' => _l('dropdown_non_selected_tex')]); ?>
                            </div>
                            <div class="col-md-6">
                                <h4
                                    class="tw-font-semibold tw-text-base tw-text-neutral-700 tw-flex tw-justify-between tw-items-center tw-mt-0 tw-mb-6">
                                    <span>
                                        <i class="fa-regular fa-circle-question tw-mr-1" data-toggle="tooltip"
                                            data-title="<?= _l('customer_shipping_address_notice'); ?>"></i>

                                        <?= _l('shipping_address'); ?>
                                    </span>
                                    <a href="#"
                                        class="customer-copy-billing-address tw-text-sm tw-text-neutral-500 hover:tw-text-neutral-700 active:tw-text-neutral-700">
                                        <?= _l('customer_billing_copy'); ?>
                                    </a>
                                </h4>

                                <?php $value = (isset($client) ? $client->shipping_street : ''); ?>
                                <?= render_textarea('shipping_street', 'shipping_street', $value); ?>
                                <?php $value = (isset($client) ? $client->shipping_city : ''); ?>
                                <?= render_input('shipping_city', 'shipping_city', $value); ?>
                                <?php $value = (isset($client) ? $client->shipping_state : ''); ?>
                                <?= render_input('shipping_state', 'shipping_state', $value); ?>
                                <?php $value = (isset($client) ? $client->shipping_zip : ''); ?>
                                <?= render_input('shipping_zip', 'shipping_zip', $value); ?>
                                <?php $selected = (isset($client) ? $client->shipping_country : ''); ?>
                                <?= render_select('shipping_country', $countries, ['country_id', ['short_name']], 'shipping_country', $selected, ['data-none-selected-text' => _l('dropdown_non_selected_tex')]); ?>
                            </div>
                            <?php if (isset($client)
                        && (total_rows(db_prefix() . 'invoices', ['clientid' => $client->userid]) > 0 || total_rows(db_prefix() . 'estimates', ['clientid' => $client->userid]) > 0 || total_rows(db_prefix() . 'creditnotes', ['clientid' => $client->userid]) > 0)) { ?>
                            <div class="col-md-12">
                                <div
                                    class="tw-bg-neutral-50 tw-py-3 tw-px-4 tw-rounded-lg tw-border tw-border-solid tw-border-neutral-200">
                                    <div class="checkbox checkbox-primary -tw-mb-0.5">
                                        <input type="checkbox" name="update_all_other_transactions"
                                            id="update_all_other_transactions">
                                        <label for="update_all_other_transactions">
                                            <?= _l('customer_update_address_info_on_invoices'); ?><br />
                                        </label>
                                    </div>
                                    <p class="tw-ml-7 tw-mb-0">
                                        <?= _l('customer_update_address_info_on_invoices_help'); ?>
                                    </p>
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" name="update_credit_notes" id="update_credit_notes">
                                        <label for="update_credit_notes">
                                            <?= _l('customer_profile_update_credit_notes'); ?>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?= form_close(); ?>
</div>
<?php if (isset($client)) { ?>
<?php if (staff_can('create', 'customers') || staff_can('edit', 'customers')) { ?>
<div class="modal fade" id="customer_admins_assign" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?= form_open(admin_url('clients/assign_admins/' . $client->userid)); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <?= _l('assign_admin'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <?php
               $selected = [];

    foreach ($customer_admins as $c_admin) {
        array_push($selected, $c_admin['staff_id']);
    }
    echo render_select('customer_admins[]', $staff, ['staffid', ['firstname', 'lastname']], '', $selected, ['multiple' => true], [], '', '', false); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                    data-dismiss="modal"><?= _l('close'); ?></button>
                <button type="submit"
                    class="btn btn-primary"><?= _l('submit'); ?></button>
            </div>
        </div>
        <!-- /.modal-content -->
        <?= form_close(); ?>
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<?php } ?>
<?php } ?>
<?php $this->load->view('admin/clients/client_group'); ?>