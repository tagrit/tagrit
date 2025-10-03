<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-3">
                <h4 class="tw-font-semibold tw-mt-0 tw-text-neutral-800">
                    Settings
                </h4>
                <ul class="nav navbar-pills navbar-pills-flat nav-pills nav-stacked">
                    <li class="settings-group-impress-settings">
                        <a href="<?php echo admin_url('imprest/settings/main?group=reconciliation'); ?>">
                            <i class="fa fa-dashboard menu-icon"></i>
                            Fund Reconciliation
                        </a>
                    </li>
                    <li class="settings-group-impress-settings">
                        <a href="<?php echo admin_url('imprest/settings/main?group=email_notifications'); ?>">
                            <i class="fa fa-envelope menu-icon"></i>
                            Email Notifications
                        </a>
                    </li>
                    <li class="settings-group-impress-settings">
                        <a href="<?php echo admin_url('imprest/settings/main?group=events'); ?>">
                            <i class="fa fa-calendar menu-icon"></i>
                            Events
                        </a>
                    </li>
                    <li class="settings-group-impress-settings">
                        <a href="<?php echo admin_url('imprest/settings/main?group=custom_fields'); ?>">
                            <i class="fa fa-gear menu-icon"></i>
                            Custom Fields
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
