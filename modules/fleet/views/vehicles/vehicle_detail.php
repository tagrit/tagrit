<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper" class="customer_profile">
    <div class="content">
        <div class="row">
            <div class="col-md-3">
                <?php if (isset($vehicle) && (!has_permission('customers', '', 'view') && is_customer_admin($vehicle->staffid))) {?>
                <div class="alert alert-info">
                    <?php echo _l('customer_admin_login_as_client_message', get_staff_full_name(get_staff_user_id())); ?>
                </div>
                <?php } ?>

                <?php if (isset($vehicle)) { ?>
                <h4 class="tw-text-lg tw-font-semibold tw-text-neutral-800 tw-mt-0">
                    <div class="tw-space-x-3 tw-flex tw-items-center">
                        <span class="tw-truncate">
                            #<?php echo new_html_entity_decode($vehicle->id . ' ' . $title); ?>
                        </span>
                    </div>
                </h4>
                <?php } ?>
            </div>
            <div class="clearfix"></div>

            <?php if (isset($vehicle)) { ?>
            <div class="col-md-3">
                <?php $this->load->view('vehicles/tabs'); ?>
            </div>
            <?php } ?>

            <div class="tw-mt-12 sm:tw-mt-0 <?php echo isset($vehicle) ? 'col-md-9' : 'col-md-8 col-md-offset-2'; ?>">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (isset($vehicle)) { ?>
                        <?php echo form_hidden('isedit'); ?>
                        <?php echo form_hidden('id', $vehicle->id); ?>
                        <div class="clearfix"></div>
                        <?php } ?>
                        <div>
                            <div class="tab-content">
                                <?php $this->load->view((isset($tab) ? $tabs['view'] : 'vehicles/groups/general')); ?>
                            </div>
                        </div>
                    </div>
                    <?php if ($group == 'profile') { ?>
                    <div class="panel-footer text-right tw-space-x-1" id="profile-save-section">
                        <?php if (!isset($vehicle)) { ?>
                        <button class="btn btn-default save-and-add-contact customer-form-submiter">
                            <?php echo _l('save_customer_and_add_contact'); ?>
                        </button>
                        <?php } ?>
                        <button class="btn btn-primary only-save customer-form-submiter">
                            <?php echo _l('submit'); ?>
                        </button>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>

    </div>
</div>
<?php init_tail(); ?>

<?php require 'modules/fleet/assets/js/vehicles/vehicle_js.php';?>

</body>
</html>
