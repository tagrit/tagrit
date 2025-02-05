<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $is_admin_and_not_impersonating_client = is_admin() && !is_client_logged_in(); ?>

<div class="row">
    <div class="col-md-12">
        <h4 class="company-view-heading tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700 tw-flex tw-items-center tw-space-x-2">
            <?= _l('perfex_saas_company_view_heading'); ?>
        </h4>
        <div class="panel_s">
            <div class="panel-body">

                <?php if ($is_admin_and_not_impersonating_client && !empty($company->metadata->pending_custom_domain)) : ?>
                    <div class="alert alert-warning text-center">
                        <div>
                            <?= _l('perfex_saas_pending_domain_request', [$company->name, $company->metadata->pending_custom_domain]); ?>
                        </div>
                        <div class="tw-mt-4"><strong><?= _l('perfex_saas_pending_domain_request_hint'); ?></strong></div>
                        <div class="tw-text-2xl tw-mt-4">
                            <strong><?= $company->metadata->pending_custom_domain; ?></strong>
                        </div>
                        <div class="tw-flex tw-mt-4 tw-w-full">
                            <?php echo form_open(admin_url(PERFEX_SAAS_ROUTE_NAME . '/companies/custom_domain'), ['id' => 'custom_domain', 'class' => 'tw-w-full']); ?>
                            <?= form_hidden('id', $company->id); ?>
                            <textarea name="extra_note" class="form-control" cols="2" rows="2" placeholder="<?= _l('perfex_saas_extra_email_note'); ?>"></textarea>
                            <div class="text-left tw-flex tw-justify-between tw-w-full">
                                <input name="cancel" type="submit" data-loading-text="<?= _l('perfex_saas_saving...'); ?>" data-form="#custom_domain_form" class="btn btn-danger mtop15 mbot15" value="<?= _l('perfex_saas_cancel'); ?>" onclick="return confirm('<?= perfex_saas_ecape_js_attr(_l('perfex_saas_confirm_action', _l('perfex_saas_cancel'))); ?>');" />
                                <input name="approve" type="submit" data-loading-text="<?= _l('perfex_saas_saving...'); ?>" data-form="#custom_domain_form" class="btn btn-success mtop15 mbot15" value="<?= _l('perfex_saas_approve'); ?>" onclick="return confirm('<?= perfex_saas_ecape_js_attr(_l('perfex_saas_confirm_action', _l('perfex_saas_approve'))); ?>');" />
                            </div>
                            <?php echo form_close(); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="company-status text-center tw-mb-4">
                    <span class="badge badge-success <?= $company->status == 'active' ? 'bg-success' : 'bg-danger'; ?>">
                        <?= perfex_saas_company_status_label($company); ?>
                        <?= perfex_saas_company_status_can_deploy($company) ? '<i class="fa fa-spin fa-spinner"></i>' : ''; ?>
                    </span>
                </div>

                <div class="form-group">
                    <label for="slug"><?= _l('perfex_saas_company_name'); ?></label>
                    <p class="form-control-static"><?= $company->name; ?></p>
                </div>
                <div class="form-group">
                    <label for="slug"><?= _l('perfex_saas_slug'); ?></label>
                    <p class="form-control-static"><?= $company->slug; ?></p>
                </div>
                <div class="form-group">
                    <label for="slug"><?= _l('perfex_saas_custom_domain'); ?></label>
                    <p class="form-control-static"><?= $company->custom_domain ?? '-'; ?></p>
                </div>
                <div class="form-group">
                    <label for="company-name"><?= _l('perfex_saas_company_accessible_links'); ?></label>
                    <p class="form-control-static tw-flex tw-flex-col">
                        <?php foreach (perfex_saas_tenant_base_url($company, '', 'all') as $key => $value) : if (!$value) continue; ?>
                            <em><?= _l('perfex_saas_url_scheme_' . $key); ?>:</em>
                            <span class="tw-mb-2 tw-flex tw-flex-col">
                                <a href="<?= $value; ?>" target="_blank" data-toggle="tooltip" data-title="<?= _l('perfex_saas_customer_link'); ?>"><?= $value; ?></a>
                                <a href="<?= $value . 'admin'; ?>" target="_blank" data-toggle="tooltip" data-title="<?= _l('perfex_saas_admin_link'); ?>"><?= $value . 'admin'; ?></a>
                            </span>
                        <?php endforeach; ?>
                    </p>
                </div>

                <div class="form-group">
                    <label for="company-name"><?= _l('perfex_saas_date_created'); ?></label>
                    <p class="form-control-static"><?= time_ago($company->created_at); ?></p>
                </div>

                <?php if ($is_admin_and_not_impersonating_client) : ?>
                    <div class="form-group">
                        <label for="perfex_saas_data_location"><?= _l('perfex_saas_data_location'); ?></label>
                        <p class="form-control-static">
                            <?php
                            $dsn = $company->dsn;
                            if (!empty($dsn)) {
                                $dsn = perfex_saas_parse_dsn($dsn);
                                $dsn_string = $dsn['host'] . ':<b>' . $dsn['dbname'] . '</b>';
                            } else {
                                $dsn_string = '-';
                            }
                            echo $dsn_string;
                            ?>
                            <button class="btn btn-xs btn-primary btn-circle rounded tw-ml-2" data-toggle="modal" data-target="#dsn_modal"><i class="fa fa-edit" title="<?= _l('perfex_saas_update_datacenter'); ?>" data-toggle="tooltip"></i></button>
                        </p>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?php if ($is_admin_and_not_impersonating_client) : ?>
    <div class="modal fade" id="dsn_modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?= _l('perfex_saas_update_datacenter'); ?></h4>
                </div>
                <div class="modal-body">

                    <div class="alert alert-warning">
                        <?= _l('perfex_saas_update_datacenter_warning'); ?>
                    </div>
                    <?php echo form_hidden('company_id', $company->id); ?>
                    <?php
                    $_dsn = $dsn;
                    if (empty($_dsn['user']) && !empty($_dsn['host'])) {
                        $_dsn['user'] = perfex_saas_master_dsn()['user'];
                    }
                    require('partials/db_pool.php');
                    ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="button" class="btn btn-primary update-datacenter" onclick="saasUpdateDatacenter()"><?= _l('perfex_saas_update_datacenter'); ?></button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <script>
        function saasUpdateDatacenter() {
            const button = $("button.update-datacenter");
            button.addClass("disabled");

            let modal = $("#dsn_modal");

            let data = {};
            modal
                .find("input, select")
                .each(function() {
                    let thisInput = $(this);
                    data[thisInput.attr("name")] = thisInput.val();
                });

            // Send AJAX request to test the database connection
            try {
                $.post(admin_url + SAAS_MODULE_NAME + "/companies/update_dsn", data)
                    .done(function(response) {
                        response = JSON.parse(response);
                        if (response.status) {
                            alert_float(response.status, response.message);
                        }
                        if (response.status == 'success') {
                            $("button[data-dismiss='modal']").click();
                            setTimeout(() => {
                                window.location.reload();
                            }, 3000);
                        }
                    }).always(function() {
                        button.removeClass("disabled");
                    });
            } catch (error) {
                console.trace(error)
            }
        }
    </script>
<?php endif; ?>