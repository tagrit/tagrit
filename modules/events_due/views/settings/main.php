<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
    <div id="wrapper">
        <div class="content">
            <div class="section-header">
                <h1>Settings</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item">Settings</div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <ul class="nav navbar-pills navbar-pills-flat nav-pills nav-stacked">
                        <li class="settings-group-impress-settings">
                            <a href="<?php echo admin_url('events_due/settings/main?group=import_events_registrations'); ?>">
                                <i class="fa fa-share menu-icon"></i>
                                Import Data
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