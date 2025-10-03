<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php load_courier_styles(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-3">
                <h4 class="tw-font-semibold tw-mt-0 tw-text-neutral-800">
                    Settings
                </h4>
                <ul class="nav navbar-pills navbar-pills-flat nav-pills nav-stacked">
                    <li class="settings-group-create-shipments">
                        <a href="<?php echo admin_url('courier/settings/main?group=customization'); ?>">
                            <i class="fa fa-dashboard menu-icon"></i>
                            Customization
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
