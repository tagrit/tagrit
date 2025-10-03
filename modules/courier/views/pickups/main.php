<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php load_courier_styles(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-3">
                <h4 class="tw-font-semibold tw-mt-0 tw-text-neutral-800">
                    Pickups
                </h4>
                <ul class="nav navbar-pills navbar-pills-flat nav-pills nav-stacked">
                    <?php if ($user_role !== 'Fleet: Driver'): ?>
                    <li class="settings-group-create-pickups">
                        <a href="<?php echo admin_url('courier/pickups/main?group=dashboard'); ?>">
                            <i class="fa fa-dashboard menu-icon"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="settings-group-create-shipments">
                        <a href="<?php echo admin_url('courier/pickups/create'); ?>">
                            <i class="fa fa-plus-square menu-icon"></i>
                            Create Pickup
                        </a>
                    </li>
                    <?php endif;?>
                    <li class="settings-group-list-of-shipments">
                        <a href="<?php echo admin_url('courier/pickups'); ?>">
                            <i class="fa fa-list menu-icon"></i>
                            List of Pickups
                        </a>
                    </li>
                </ul>
            </div>
            <div class="col-md-9">
                <?php echo $group_content ?? " "; ?>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
