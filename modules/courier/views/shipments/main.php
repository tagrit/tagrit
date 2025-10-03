<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php load_courier_styles(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-3">
                <h4 class="tw-font-semibold tw-mt-0 tw-text-neutral-800">
                    Shipments
                </h4>
                <ul class="nav navbar-pills navbar-pills-flat nav-pills nav-stacked">
                    <li class="settings-group-create-shipments">
                    <li class="settings-group-create-pickups">
                        <a href="<?php echo admin_url('courier/shipments/main?group=dashboard'); ?>">
                            <i class="fa fa-dashboard menu-icon"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="#create-shipments-submenu" data-toggle="collapse" aria-expanded="false"
                           class="dropdown-toggle">
                            <i class="fa fa-plus-square menu-icon"></i>
                            <span>Create Shipments</span>
                            <i style="margin-left:30px;" class="fa fa-caret-down"></i> <!-- Caret moved to the right -->
                        </a>
                    </li>
                    <ul class="collapse list-unstyled" id="create-shipments-submenu">
                        <li style="padding-left:30px;">
                            <a href="<?php echo admin_url('courier/shipments/create?type=domestic'); ?>">
                                <i class="fa fa-home"></i> Domestic
                            </a>
                        </li>
                        <li style="padding-left:30px; padding-top:10px;">
                            <a href="#create-international-shipments-submenu" data-toggle="collapse"
                               aria-expanded="false">
                                <i class="fa fa-globe"></i> <span>International</span>
                                <i style="margin-left:30px;" class="fa fa-caret-down"></i>
                                <!-- Caret moved to the right -->
                            </a>
                        </li>
                        <ul style="margin-top: 10px;" class="collapse list-unstyled"
                            id="create-international-shipments-submenu">
                            <li style="padding-left:50px;">
                                <a href="<?php echo admin_url('courier/shipments/create?type=international&mode=road&mode_type=none'); ?>">
                                    <i class="fa fa-truck"></i> Road
                                </a>
                            </li>
                            <li style="padding-left:50px; padding-top:10px;">
                                <a href="#create-sea-international-shipments-submenu" data-toggle="collapse"
                                   aria-expanded="false">
                                    <i class="fa fa-ship"></i> Sea
                                    <i style="margin-left:30px;" class="fa fa-caret-down"></i>
                                </a>
                            </li>
                            <ul style="margin-top: 10px;" class="collapse list-unstyled"
                                id="create-sea-international-shipments-submenu">
                                <li style="padding:6px; padding-left:70px;">
                                    <a href="<?php echo admin_url('courier/shipments/create?type=international&mode=sea&mode_type=fcl'); ?>">
                                        FCL
                                    </a>
                                </li>
                                <li style="padding:6px; padding-left:70px;">
                                    <a href="<?php echo admin_url('courier/shipments/create?type=international&mode=sea&mode_type=lcl'); ?>">
                                        LCL
                                    </a>
                                </li>
                                <li style="padding:6px; padding-left:70px;">
                                    <a href="<?php echo admin_url('courier/shipments/create?type=international&mode=sea&mode_type=sea_consolidation'); ?>">
                                        Consolidation
                                    </a>
                                </li>
                            </ul>
                            <li style="padding-left:50px; padding-top:10px;">
                                <a href="#create-air-international-shipments-submenu" data-toggle="collapse"
                                   aria-expanded="false">
                                    <i class="fa fa-plane"></i> <span>Air</span>
                                    <i style="margin-left:30px;" class="fa fa-caret-down"></i>
                                    <!-- Caret moved to the right -->
                                </a>
                            </li>
                            <ul style="margin-top: 10px;" class="collapse list-unstyled"
                                id="create-air-international-shipments-submenu">
                                <li style="padding:6px; padding-left:70px;">
                                    <a href="<?php echo admin_url('courier/shipments/create?type=international&mode=air&mode_type=air_freight'); ?>">
                                        Air Freight
                                    </a>
                                </li>
                                <li style="padding:6px; padding-left:70px;">
                                    <a href="<?php echo admin_url('courier/shipments/create?type=international&mode=air&mode_type=air_consolidation'); ?>">
                                        Air Consolidation
                                    </a>
                                </li>
                            </ul>
                            <li style="margin-top:10px; padding-left:50px;">
                                <a href="<?php echo admin_url('courier/shipments/create?type=international&mode=courier&mode_type=none'); ?>">
                                    <i class="fa fa-road"></i> Courier
                                </a>
                            </li>
                        </ul>
                    </ul>
                    </li>
                    <li>
                        <a href="#list-shipments-submenu" data-toggle="collapse" aria-expanded="false"
                           class="dropdown-toggle ">
                            <i class="fa fa-list menu-icon"></i>
                            <span>List of Shipments</span>
                            <i style="margin-left:30px;" class="fa fa-caret-down"></i>
                        </a>
                    </li>
                    <ul class="collapse list-unstyled" id="list-shipments-submenu">
                        <li style="padding-left:30px;">
                            <a href="<?php echo admin_url('courier/shipments?type=domestic'); ?>">
                                <i class="fa fa-home"></i> Domestic
                            </a>
                        </li>
                        <li style="padding-left:30px; padding-top:10px;">
                            <a href="#list-international-shipments-submenu" data-toggle="collapse"
                               aria-expanded="false">
                                <i class="fa fa-globe"></i> <span>International</span>
                                <i style="margin-left:30px;" class="fa fa-caret-down"></i>
                                <!-- Caret moved to the right -->
                            </a>
                        </li>
                        <ul style="margin-top: 10px;" class="collapse list-unstyled"
                            id="list-international-shipments-submenu">
                            <li style="padding-left:50px;">
                                <a href="<?php echo admin_url('courier/shipments?type=international&mode=road&mode_type=none'); ?>">
                                    <i class="fa fa-truck"></i> Road
                                </a>
                            </li>
                            <li style="padding-left:50px; padding-top:10px;">
                                <a href="#list-sea-international-shipments-submenu" data-toggle="collapse"
                                   aria-expanded="false">
                                    <i class="fa fa-ship"></i> Sea
                                    <i style="margin-left:30px;" class="fa fa-caret-down"></i>
                                </a>
                            </li>
                            <ul style="margin-top: 10px;" class="collapse list-unstyled"
                                id="list-sea-international-shipments-submenu">
                                <li style="padding:6px; padding-left:70px;">
                                    <a href="<?php echo admin_url('courier/shipments?type=international&mode=sea&mode_type=fcl'); ?>">
                                        FCL
                                    </a>
                                </li>
                                <li style="padding:6px; padding-left:70px;">
                                    <a href="<?php echo admin_url('courier/shipments?type=international&mode=sea&mode_type=lcl'); ?>">
                                        LCL
                                    </a>
                                </li>
                                <li style="padding:6px; padding-left:70px;">
                                    <a href="<?php echo admin_url('courier/shipments?type=international&mode=sea&mode_type=sea_consolidation'); ?>">
                                        Consolidation
                                    </a>
                                </li>
                            </ul>
                            <li style="padding-left:50px; padding-top:10px;">
                                <a href="#list-air-international-shipments-submenu" data-toggle="collapse"
                                   aria-expanded="false">
                                    <i class="fa fa-plane"></i> <span>Air</span>
                                    <i style="margin-left:30px;" class="fa fa-caret-down"></i>
                                    <!-- Caret moved to the right -->
                                </a>
                            </li>
                            <ul style="margin-top: 10px;" class="collapse list-unstyled"
                                id="list-air-international-shipments-submenu">
                                <li style="padding:6px; padding-left:70px;">
                                    <a href="<?php echo admin_url('courier/shipments?type=international&mode=air&mode_type=air_freight'); ?>">
                                        Air Freight
                                    </a>
                                </li>
                                <li style="padding:6px; padding-left:70px;">
                                    <a href="<?php echo admin_url('courier/shipments?type=international&mode=air&mode_type=air_consolidation'); ?>">
                                        Air Consolidation
                                    </a>
                                </li>
                            </ul>
                            <li style="margin-top:10px; padding-left:50px;">
                                <a href="<?php echo admin_url('courier/shipments?type=international&mode=courier&mode_type=none'); ?>">
                                    <i class="fa fa-road"></i> Courier
                                </a>
                            </li>
                        </ul>
                    </ul>
                    <li class="settings-group-list-of-shipments">
                        <a href="<?php echo admin_url('courier/shipments/list_invoices'); ?>">
                            <i class="fa fa-dollar menu-icon"></i>
                            Invoices
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
