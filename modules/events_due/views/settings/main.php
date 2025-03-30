<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-3">
                <ul class="nav navbar-pills navbar-pills-flat nav-pills nav-stacked">
                    <li class="settings-group-impress-settings">
                        <a href="<?php echo admin_url('events_due/settings/main?group=import_events_registrations'); ?>">
                            <i class="fa fa-share menu-icon"></i>
                            Client Records
                        </a>
                    </li>
                    <li class="settings-group-impress-settings">
                        <a href="<?php echo admin_url('events_due/settings/main?group=set_reminder_period'); ?>">
                            <i class="fa fa-bell menu-icon"></i> <!-- Bell icon for reminders -->
                            Reminder
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
</div>
<?php init_tail(); ?>