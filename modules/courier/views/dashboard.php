<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php load_courier_styles(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div>
                            <h4 class="font-bold m-0"><i class="fa fa-dashboard menu-icon"></i><span style="margin-left:4px;">DashBoard</span></h4>
                        </div>
                        <hr class="hr-panel-heading"/>

                        <!-- Record Shipment -->
                        <section class="custom-section">
                            <div class="custom-container">
                                <div class="custom-form-grid">
                                    <div  class="card-container row">
                                        <div style="margin-right:20px;" class="card col-xs-6 col-sm-3 bg-blue">
                                            <div class="icon"><i style="color:blueviolet" class="fa fa-globe" aria-hidden="true"></i></div>
                                            <div class="label">Shipments</div>
                                            <div class="count"><?=$shipment_counts?></div>
                                        </div>
                                        <div style="margin-right:20px;" class="card col-xs-6 col-sm-3 bg-green">
                                            <div class="icon"><i  style="color:orange" class="fa fa-truck" aria-hidden="true"></i></div>
                                            <div class="label">Pickups</div>
                                            <div class="count"><?=$pickup_counts?></div>
                                        </div>
                                        <div style="margin-right:20px;" class="card col-xs-6 col-sm-3 bg-orange">
                                            <div class="icon"><i  style="color:green" class="fa fa-building" aria-hidden="true"></i></div>
                                            <div class="label">Courier Companies</div>
                                            <div class="count"><?=$courier_company_counts?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div
    </div>
</div>
<?php init_tail(); ?>
