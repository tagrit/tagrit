<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <h4 class="pull-left"><?= _l('perfex_saas_api_documentation'); ?></h4>
                            <a href="<?php echo admin_url('settings?group=' . PERFEX_SAAS_MODULE_WHITELABEL_NAME . '&tab=api'); ?>"
                                class="btn btn-default pull-right"><i class="fa fa-cog"></i>
                                <?php echo _l('perfex_saas_settings'); ?></a>
                        </div>
                        <div class="clearfix"></div>
                        <hr />
                        <div class="clearfix"></div>
                        <?php $apiDocLink = base_url(PERFEX_SAAS_ROUTE_NAME . '/api/docs'); ?>
                        <p class="mtop20">
                            (OAS UI): <a href="<?= $apiDocLink; ?>" target="_blank"><?= $apiDocLink; ?></a>
                        </p>
                        <p class="mtop20">
                            (OAS JSON): <a href="<?= $apiDocLink; ?>/json" target="_blank"><?= $apiDocLink; ?>/json</a>
                        </p>
                        <p class="mtop20">
                            (OAS YAML): <a href="<?= $apiDocLink; ?>/yaml" target="_blank"><?= $apiDocLink; ?>/yaml</a>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <h4 class="pull-left"><?= _l('perfex_saas_api_user'); ?></h4>
                            <a href="<?php echo admin_url(PERFEX_SAAS_ROUTE_NAME . '/api/create_user') ?>"
                                class="btn btn-primary pull-right"><i class="fa fa-plus"></i>
                                <?php echo _l('add_new'); ?></a>
                        </div>
                        <div class="clearfix"></div>
                        <hr />
                        <div class="clearfix"></div>
                        <?php $this->load->view('api/users/list', ['api_users' => $api_users]); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="tw-mt-4 tw-mb-4">
            <hr />
        </div>
    </div>
</div>

<?php init_tail(); ?>