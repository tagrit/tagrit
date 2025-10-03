<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Add use to package modal -->
<div class="modal animated fadeIn" id="add-package-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo _l('perfex_saas_add_user_to_package_modal_title'); ?></h4>
            </div>
            <div class="modal-body tw-m-0">


                <!--- add user to package form -->
                <?= form_open(admin_url(PERFEX_SAAS_ROUTE_NAME . '/packages/add_user_to_package'), ['id' => 'add_user_to_package_form']); ?>
                <!-- contact selection -->
                <div class="form-group">
                    <label for="packageid" class="control-label"><?php echo _l('perfex_saas_package'); ?></label>
                    <select id="packageid" name="packageid" class="form-control">
                        <?php foreach (get_instance()->perfex_saas_model->packages() as $package) : ?>
                            <option value="<?= $package->id; ?>"><?= $package->name; ?>
                                (<?= app_format_money($package->price, get_base_currency()); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group select-placeholder">
                    <label for="clientid" class="control-label"><?php echo _l('perfex_saas_invoice_select_customer'); ?></label>
                    <select id="clientid" name="clientid" data-live-search="true" data-width="100%" class="ajax-search" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                    </select>
                </div>
                <div class="form-group">
                    <button class="btn btn-primary"><?= _l('perfex_saas_submit'); ?></button>
                </div>
                <?= form_close(); ?>
                <!-- add user to package form end -->
            </div>
        </div>
    </div>
</div>

<script>
    "use strict";

    window.addEventListener('load', function() {
        appValidateForm($("#add_user_to_package_form"), {
            clientid: "required",
            packageid: "required",
        });

        let modalButton = `
            <button type="button" class="btn btn-danger tw-ml-2" data-target="#add-package-modal" data-toggle="modal">
                <span class="btn-with-tooltip" data-toggle="tooltip" data-title="<?php echo _l('perfex_saas_add_user_to_package_btn'); ?>"
                    data-placement="bottom">
                    <i class="fa-regular fa-plus"></i>
                    <?php echo _l('perfex_saas_add_user_to_package_btn'); ?>
                </span>
            </button>`;

        $(modalButton).insertAfter(".new-invoice-list");
    })
</script>