<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="section-header">
            <h1>Reports</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                <div class="breadcrumb-item">Reports</div>
            </div>
        </div>
        <div class="panel_s">
            <div class="panel-body">
                <!-- Date Filter -->
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="no-margin">Sort Results</h4>
                        <hr class="hr-panel-heading" />
                    </div>
                </div>

                <div class="clearfix mtop20"></div>

                <!-- Filter Form -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date-range" class="control-label">Duration</label>
                            <div class="input-group date">
                                <input type="text" class="form-control datepicker" id="date-range" name="date-range">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar calendar-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-info apply-filter mtop25">Apply</button>
                    </div>
                </div>

                <div class="clearfix mtop20"></div>

                <!-- DataTable -->
                <table class="table dt-table table-events" id="reports-table">
                            <thead>
                                <tr>
                                    <th><?php echo _l('ID'); ?></th>
                                    <th><?php echo _l('Participant Name'); ?></th>
                                    <th><?php echo _l('Email'); ?></th>
                                    <th><?php echo _l('Phone'); ?></th>
                                    <th><?php echo _l('Event'); ?></th>
                                    <th><?php echo _l('Venue'); ?></th>
                                    <th><?php echo _l('Registration Date'); ?></th>

                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>

            </div>
        </div>


    </div>
</div>
<?php init_tail(); ?>