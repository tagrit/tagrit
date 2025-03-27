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
                    <div class="col-md-10">
                        <h4 class="no-margin"></h4>
                    </div>
                    <div class="col-md-2">
                        <div style="margin-bottom:10px;" class="btn-group  pull-right">
                            <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                               aria-haspopup="true" aria-expanded="false"><i
                                        class="fa fa-print"></i><?php if (is_mobile()) {
                                    echo ' PDF';
                                } ?> <span class="caret"></span></a>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a href="<?php echo admin_url('events_due/reports/export_filtered_report'); ?>">
                                        <?php echo _l('export_to_excel'); ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <hr class="hr-panel-heading"/>

                </div>

                <div class="clearfix mtop10">

                </div>

                <div class="row">
                    <button type="button" class="btn btn-info clear-filters apply-filter far-right">
                        <i class="fa fa-trash-alt"></i> Clear
                    </button>
                </div>


                <!-- Filter Form -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status"><?php echo _l('Status'); ?></label>
                            <select id="status" name="status"
                                    data-live-search="true"
                                    class="form-control selectpicker"
                                    data-none-selected-text="<?php echo _l('Dropdown Non Selected Text'); ?>">
                                <option value="Pending">Pending</option>
                                <option value="Confirmed">Confirmed</option>
                                <option value="Canceled">Canceled</option>
                            </select>
                            <?php echo form_error('setup', '<div class="error-message">', '</div>'); ?>

                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="start-date" class="control-label">Start Date</label>
                            <div class="input-group date">
                                <input type="text" class="form-control datepicker" id="start-date"
                                       name="start-date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar calendar-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="end-date" class="control-label">End Date</label>
                            <div class="input-group date">
                                <input type="text" class="form-control datepicker" id="end-date"
                                       name="end-date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar calendar-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="organization"><?php echo _l('Organization'); ?></label>
                            <select id="organization" name="organization"
                                    data-live-search="true"
                                    class="form-control selectpicker"
                                    data-none-selected-text="<?php echo _l('Dropdown Non Selected Text'); ?>">
                                <option value="" disabled selected>Select Organization</option>
                                <?php if (!empty($organizations)): ?>
                                    <?php foreach ($organizations as $org): ?>
                                        <option value="<?= htmlspecialchars($org->organization) ?>">
                                            <?= htmlspecialchars($org->organization) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>No organizations found</option>
                                <?php endif; ?>
                            </select>
                            <?php echo form_error('setup', '<div class="error-message">', '</div>'); ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="query" class="control-label">Search</label>
                            <div style="border-radius:7px; box-shadow: none;" class="input-group date">
                                <input style="border-radius:7px; box-shadow: none;" type="text" class="form-control"
                                       id="query"
                                       name="query">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="clearfix mtop20"></div>

                <!-- DataTable -->
                <table class="table dt-table table-events" id="reports-table">
                    <thead>
                    <tr>
                        <th><?php echo _l('Event'); ?></th>
                        <th><?php echo _l('Client'); ?></th>
                        <th><?php echo _l('Organization'); ?></th>
                        <th><?php echo _l('start Date'); ?></th>
                        <th><?php echo _l('End Date'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($registrations as $registration): ?>
                        <tr>
                            <td>
                                <div class="d-flex flex-column justify-content-center">
                                    <p style="font-weight: bold; font-size: 14px;">
                                        <?php echo $registration->event_name; ?>
                                    </p>
                                    <p style="color:#007BFF; font-weight: bold;" class="text-secondary mb-0">
                                        <?php echo $registration->location . ' - ' . $registration->venue; ?>
                                    </p>
                                </div>
                            </td>
                            <td>
                                <p class="text-secondary mb-0">
                                    <?php echo $registration->client_first_name . ' ' . $registration->client_last_name; ?>
                                </p>
                                <p class="text-secondary mb-0">
                                    <?php echo $registration->client_phone; ?>
                                </p>
                                <p class="text-secondary mb-0">
                                    <?php echo $registration->client_email; ?>
                                </p>
                            </td>
                            <td>
                                <?php echo $registration->organization; ?>
                            </td>
                            <td>
                                <?php echo $registration->start_date; ?>
                            </td>
                            <td>
                                <?php echo $registration->end_date; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    const baseUrl = "<?= base_url(); ?>";
</script>
<?php init_tail(); ?>

