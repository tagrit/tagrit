<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="tw-flex tw-justify-between">
                    <?php if (staff_can('create', 'perfex_saas_packages') && !empty($packages)) { ?>
                    <div class="tw-mb-2 sm:tw-mb-4">
                        <a href="<?php echo admin_url(PERFEX_SAAS_ROUTE_NAME . '/packages/create'); ?>"
                            class="btn btn-primary">
                            <i class="fa-regular fa-plus tw-mr-1"></i>
                            <?php echo _l('perfex_saas_new_package'); ?>
                        </a>
                    </div>
                    <?php } ?>
                </div>

                <div class="panel_s">
                    <div class="panel-body panel-table-full">
                        <?php if (empty($packages)) : ?>
                        <?php if (staff_can('create', 'perfex_saas_packages')) { ?>
                        <div class="text-center">
                            <div class="tw-mb-4"><?= _l('perfex_saas_create_your_first_package'); ?></div>
                            <a href="<?php echo admin_url(PERFEX_SAAS_ROUTE_NAME . '/packages/create'); ?>"
                                class="btn btn-primary">
                                <i class="fa-regular fa-plus tw-mr-1"></i>
                                <?php echo _l('perfex_saas_new_package'); ?>
                            </a>
                        </div>
                        <?php } ?>
                        <?php endif ?>
                        <?php $this->view('packages/list'); ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


<?php init_tail(); ?>
<?php get_instance()->load->view(PERFEX_SAAS_MODULE_NAME . '/includes/add_user_to_package_modal'); ?>
</body>

</html>