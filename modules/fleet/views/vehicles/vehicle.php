<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper" class="customer_profile">
    <div class="content">
        <div class="row">
            <div class="col-md-3">
                <?php if (isset($vehicle)) { ?>
                <h4 class="tw-text-lg tw-font-semibold tw-text-neutral-800 tw-mt-0">
                    <div class="tw-space-x-3 tw-flex tw-items-center">
                        <span class="tw-truncate">
                            #<?php echo new_html_entity_decode($vehicle->id . ' ' . $title); ?>
                        </span>
                        <?php if (has_permission('fleet_vehicle', '', 'delete') || is_admin()) { ?>
                        <div class="btn-group">
                            <a href="#" class="dropdown-toggle btn-link" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <?php if (has_permission('fleet_vehicle', '', 'delete')) { ?>
                                <li>
                                    <a href="<?php echo admin_url('fleet/delete_vehicle/' . $vehicle->id); ?>"
                                        class="text-danger delete-text _delete"><i class="fa fa-remove"></i>
                                        <?php echo _l('delete'); ?>
                                    </a>
                                </li>
                                <?php } ?>
                            </ul>
                        </div>
                        <?php } ?>
                    </div>
                </h4>
                <?php } ?>
            </div>
            <div class="clearfix"></div>

            <?php if (isset($vehicle)) { ?>
            <div class="col-md-3">
                <?php $this->load->view('tabs'); ?>
            </div>
            <?php } ?>

            <div class="tw-mt-12 sm:tw-mt-0 <?php echo isset($vehicle) ? 'col-md-9' : 'col-md-8 col-md-offset-2'; ?>">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (isset($vehicle)) { ?>
                        <?php echo form_hidden('isedit'); ?>
                        <?php echo form_hidden('vehicleid', $vehicle->id); ?>
                        <div class="clearfix"></div>
                        <?php } ?>
                        <div>
                            <div class="tab-content">
                                <?php $this->load->view((isset($tab) ? $tab['view'] : 'groups/details')); ?>
                            </div>
                        </div>
                    </div>
                    <?php if ($group == 'details' || $group == 'lifecycle' || $group == 'financial' || $group == 'specifications') { ?>
                    <div class="panel-footer text-right tw-space-x-1" id="profile-save-section">
                        <button class="btn btn-primary only-save vehicle-form-submiter">
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