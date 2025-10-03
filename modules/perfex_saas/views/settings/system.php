<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">

        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel">
                    <div class="panel-body">
                        <div class="input-group">
                            <input type="text" placeholder="<?= _l('perfex_saas_purchase_code'); ?>" class="form-control" id="purchase-code" data-value="<?= $purchase_code; ?>" data-action="<?= admin_url(PERFEX_SAAS_ROUTE_NAME . '/system/save_purchase_code'); ?>" value="<?= $purchase_code; ?>" readonly>
                            <span class="input-group-btn">
                                <button class="btn btn-primary purchase_code_update" id="edit-button" data-toggle="tooltip" data-title="<?= _l('edit'); ?>"><i class="fa fa-pen"></i></button>
                                <button class="btn btn-success purchase_code_update" style="display: none;" id="save-button" data-toggle="tooltip" data-title="<?= _l('save'); ?>"><i class="fa fa-save"></i></button>
                            </span>
                        </div>

                        <?php if (!empty($remote_modules->{PERFEX_SAAS_MODULE_NAME})) :
                            $installed_version = $saas_module['headers']['version'];
                            $latest_version = $remote_modules->{PERFEX_SAAS_MODULE_NAME}->version;
                        ?>
                            <div class="text-center tw-mt-5">
                                <div class="tw-flex tw-justify-between">
                                    <div class="tw-text-lg <?= $installed_version == $latest_version ? 'text-success' : 'text-danger'; ?>">
                                        <?= _l('your_version'); ?>:
                                        v<?= $saas_module['headers']['version']; ?>
                                    </div>
                                    <div class="tw-text-xl text-success">
                                        <?= _l('latest_version'); ?>:
                                        v<?= $latest_version; ?>
                                    </div>
                                </div>
                                <div class="tw-text-2xl tw-w-full tw-mt-4">

                                    <a onclick="javascript: return confirm('<?= perfex_saas_ecape_js_attr(_l('perfex_saas_backup_warning')); ?>');" href="<?= admin_url(PERFEX_SAAS_ROUTE_NAME . '/system/get_module/' . PERFEX_SAAS_MODULE_NAME); ?>" class="btn btn-<?= $installed_version != $latest_version ? 'danger' : 'primary'; ?> btn-block"><?= $installed_version != $latest_version ? _l('update_now') : _l('download'); ?></a>

                                </div>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>


        <?php if (!empty($remote_modules)) : ?>

            <div class="tw-mt-4 tw-mb-4">
                <hr />
            </div>


            <div class="panel">
                <div class="panel-body">

                    <h3 class="tw-mt-0"><?= _l('perfex_saas_extensions'); ?></h3>
                    <p><?= _l('perfex_saas_extensions_hint'); ?></p>

                    <?php $this->load->view('authentication/includes/alerts'); ?>

                    <?php if (empty($remote_modules)) : ?>
                        <p class="text-center"><?= _l('perfex_saas_empty_data'); ?></p>
                    <?php endif; ?>

                    <div class="tw-grid tw-gap-3 tw-grid-cols-1 sm:tw-grid-cols-2 md:tw-grid-cols-3 tw-mt-4">
                        <?php foreach ($remote_modules as $module_name => $module) :
                            if ($module_name === PERFEX_SAAS_MODULE_NAME) continue;
                            $installed_module = $this->app_modules->get($module_name);
                            $installed_version = $installed_module['installed_version'] ?? '-';
                        ?>
                            <div class="panel_s tw-p-4 tw-py-2 tw-bg-neutral-100">
                                <div class="panel_body tw-flex tw-flex-col tw-items-center tw-justify-center text-center tw-gap-3 tw-h-full tw-relative">
                                    <div class="tw-text-2xl">
                                        <?= $module->name; ?>
                                        <?php if (!empty($module->tag)) : ?>
                                            <span class="badge bg-success tw-text-xs tw-px-1 tw-absolute tw-right-0" <?php if (!empty($module->tag_hint)) : ?> data-toggle="tooltip" data-title="<?= $module->tag_hint; ?>" <?php endif; ?>>
                                                <?= $module->tag; ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <p><?= $module->description ?? ''; ?></p>

                                    <div class="tw-text-lg">
                                        <?= _l('your_version'); ?>:
                                        <?= empty($installed_version) ? '-' : $installed_version; ?>
                                    </div>
                                    <div class="tw-text-xl">
                                        <?= _l('latest_version'); ?>: <?= $module->version; ?>
                                    </div>
                                    <div class="tw-text-xl tw-w-full tw-mt-4 tw-grid tw-gap-3 tw-grid-cols-<?= $installed_module ? '2' : '1'; ?>">

                                        <a onclick="javascript: return confirm('<?= perfex_saas_ecape_js_attr(_l('perfex_saas_backup_warning')); ?>');" href="<?= !empty($module->link) ? $module->link : admin_url(PERFEX_SAAS_ROUTE_NAME . '/system/get_module/' . $module_name); ?>" class="btn btn-<?= $installed_module && $installed_version != $module->version ? 'danger' : 'primary'; ?> btn-block"><?= $installed_module && $installed_version != $module->version ? _l('update_now') : _l('download'); ?></a>

                                        <?php if ($installed_module) : ?>
                                            <?php if ($installed_module['activated']) : ?>
                                                <a href="<?= admin_url(PERFEX_SAAS_ROUTE_NAME . '/system/deactivate/' . $module_name); ?>" class="btn btn-danger"><?= _l('module_deactivate'); ?></a>
                                            <?php else : ?>
                                                <a href="<?= admin_url(PERFEX_SAAS_ROUTE_NAME . '/system/activate/' . $module_name); ?>" class="btn btn-success"><?= _l('module_activate'); ?></a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>


<?php init_tail(); ?>
<script>
    "use strict";
    document.addEventListener('DOMContentLoaded', function() {

        $(".purchase_code_update").on('click', function() {
            toggleEdit();
        });
    });

    const toggleEdit = () => {
        var purchaseCodeInput = document.querySelector("#purchase-code");
        var editButton = document.querySelector("#edit-button");
        var saveButton = document.querySelector("#save-button");

        if (purchaseCodeInput.readOnly) {
            // Activate editing
            purchaseCodeInput.readOnly = false;
            editButton.style.display = "none";
            saveButton.style.display = "inline-block";
            purchaseCodeInput.focus();
            purchaseCodeInput.setSelectionRange(
                purchaseCodeInput.value.length,
                purchaseCodeInput.value.length
            ); // Place cursor at the end
        } else {
            // Saving
            if (!purchaseCodeInput.value.length) {
                purchaseCodeInput.value = purchaseCodeInput.dataset.value;
            }

            if (!purchaseCodeInput.value.length) return;

            purchaseCodeInput.readOnly = true;

            saveButton.setAttribute("disabled", "disabled");

            // Send to server
            $.post(purchaseCodeInput.dataset.action, {
                    purchase_code: purchaseCodeInput.value,
                })
                .done((response) => {
                    response = JSON.parse(response);

                    $(".payouts .btn-dt-reload").click();
                    if (response.status === "success") $("[data-dismiss]").click();
                    saveButton.removeAttribute("disabled");

                    purchaseCodeInput.value = response.purchase_code;
                    alert_float(response.status, response.message);

                    saveButton.style.display = "none";
                    editButton.style.display = "inline-block";


                    if (!purchaseCodeInput.dataset?.value?.length || !response?.purchase_code?.length)
                        setTimeout(() => {
                            window.location.reload();
                        }, 3000);
                })
                .fail(function(error) {
                    alert_float("danger", error.responseText);
                    saveButton.removeAttribute("disabled");
                });
        }
    };
</script>
</body>

</html>