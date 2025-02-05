<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php load_courier_styles(); ?>
<?php

?>

<?php
// Include Flatpickr CSS
echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">';

// Include Select2 CSS
echo '<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />';

?>

<style>
    .select2-container .select2-selection--single {
        background-color: #f9fafb;
        border: 1px solid #d1d5db;
        color: #111827;
        font-size: 0.875rem;
        border-radius: 0.375rem;
        padding: 5px 10px 10px 10px;
        width: 100%;
        height: 35px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .select2-selection__arrow {
        transform: translateY(30%);
    }

    /* Hide action buttons by default */
    .action-buttons {
        display: none;
    }

    /* Show action buttons when hovering over the row */
    .data-row:hover .action-buttons {
        display: block;
        cursor: pointer;
    }
</style>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <?php
                $type = $this->session->userdata('type');
                $mode = $this->session->userdata('mode');
                $mode_type = $this->session->userdata('mode_type');


                if ($type == 'domestic') {
                    $mode = null;
                    $mode_type = null;
                }

                ?>
                <div style="background-color:transparent;" class="panel_s">
                    <div style="padding:15px; margin-bottom:10px;">
                        <a style="text-decoration: none; border: 2px solid black;" class="custom-button"
                           href="<?php echo !empty($mode) ? admin_url('courier/shipments/create?type=' . $type . '&mode=' . $mode . '&mode_type=' . $mode_type) : admin_url('courier/shipments/create?type=' . $type); ?>">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                            <span style="margin-left: 10px;">Create Shipment</span>
                        </a>
                    </div>

                    <div class="panel-body">
                        <div style="margin-bottom:20px; display: flex; justify-content: flex-end;">
                            <a style="text-decoration: none; border: 2px solid black; margin-left: 10px;"
                               class="custom-button"
                               href="<?php echo admin_url('courier/shipments/main?type=international'); ?>">
                                <i class="fa fa-arrow-left" aria-hidden="true"></i>
                                <span style="margin-left: 10px;">Shipment Dashboard</span>
                            </a>
                            <a style="text-decoration: none; border: 2px solid black; margin-left: 10px;"
                               class="custom-button"
                               href="#" data-toggle="modal" data-target="#generateManifestModal">
                                <span style="margin-left: 10px;">Generate Manifest</span>
                            </a>
                        </div>
                        <?php echo form_open('admin/courier/shipments/filter_shipments', ['id' => 'filter-shipments-form']); ?>

                        <!-- Hidden inputs to carry URL parameters -->
                        <input type="hidden" id="type" name="type" value="<?php echo $type ?>">
                        <input type="hidden" id="mode" name="mode" value="<?php echo $mode ?>">
                        <input type="hidden" id="mode_type" name="mode_type" value="<?php echo $mode_type ?>">

                        <div class="row mb-3" style="margin-bottom: 10px;">
                            <!-- Date Range Filter -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filterDateRange">Filter By Date</label>
                                    <input type="text" class="form-control" id="filterDateRange"
                                           value="<?= !empty($this->session->userdata('filterDateRange')) ? $this->session->userdata('filterDateRange') : '' ?>"
                                           name="filterDateRange"
                                           placeholder="Select date range">
                                </div>
                            </div>

                            <!-- Status Filter -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status_id">Filter By Status</label>
                                    <select class="form-control" id="status_id" name="status_id">
                                        <option value="0" <?= $this->session->userdata('status_id') == '0' ? 'selected' : '' ?>>
                                            All
                                        </option>

                                        <option value="1" <?= $this->session->userdata('status_id') == '1' ? 'selected' : '' ?>>
                                            Created
                                        </option>
                                        <option value="2" <?= $this->session->userdata('status_id') == '2' ? 'selected' : '' ?>>
                                            Picked Up
                                        </option>
                                        <option value="3" <?= $this->session->userdata('status_id') == '3' ? 'selected' : '' ?>>
                                            Received
                                        </option>
                                        <option value="4" <?= $this->session->userdata('status_id') == '4' ? 'selected' : '' ?>>
                                            Dispatched
                                        </option>
                                        <option value="5" <?= $this->session->userdata('status_id') == '5' ? 'selected' : '' ?>>
                                            In Transit
                                        </option>
                                        <option value="6" <?= $this->session->userdata('status_id') == '6' ? 'selected' : '' ?>>
                                            Out for Delivery
                                        </option>
                                        <option value="7" <?= $this->session->userdata('status_id') == '7' ? 'selected' : '' ?>>
                                            Delivered
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="staff_id">Filter By Agent</label>
                                    <select class="form-control" id="staff_id" name="staff_id">
                                        <option value="0" <?= $this->session->userdata('staff_id') == '0' ? 'selected' : '' ?>>
                                            All
                                        </option>
                                        <?php if (!empty($agents)): ?>
                                            <?php foreach ($agents as $agent): ?>
                                                <option value="<?php echo htmlspecialchars($agent->staff_id); ?>" <?= $this->session->userdata('staff_id') == $agent->staff_id ? 'selected' : '' ?>>
                                                    <?php echo htmlspecialchars($agent->firstname . ' ' . $agent->lastname); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="">No Agents Available</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>


                            <!-- Clear Filter Button -->
                            <div style="margin-top: 22px;" class="col-md-3 mt-4 d-flex">
                                <button type="submit" class="btn btn-secondary"
                                        id="filter">Filter
                                </button>
                                <button type="button" class="btn btn-secondary" id="clearFilters">Clear Filter
                                </button>
                            </div>
                        </div>
                        <?php echo form_close(); ?>

                        <?php if ($no_shipments): ?>
                            <div class="text-center text-danger">
                                <p>No shipment were Found</p>
                            </div>
                        <?php else: ?>
                            <?php if (!empty($shipment_details)): ?>
                                <table class="table dt-table" data-order-col="5" data-order-type="desc"
                                       id="shipmentTable">
                                    <thead class="table-head">
                                    <tr>
                                        <th>Tracking ID</th>
                                        <th>Sender</th>
                                        <th>Recipient</th>
                                        <th>Mode</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($shipment_details as $shipment_detail): ?>
                                        <tr class="data-row">
                                            <td><?php echo $shipment_detail['shipment']->tracking_id; ?></td>
                                            <td>
                                                <?php if ($shipment_detail['sender_type'] === 'individual'): ?>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <p style="font-weight: bold; font-size: 14px;">
                                                            <?php echo $shipment_detail['sender']->first_name . ' ' . $shipment_detail['sender']->last_name; ?>
                                                        </p>
                                                        <p class="text-secondary mb-0"><?php echo $shipment_detail['sender']->email; ?></p>
                                                        <p class="text-secondary mb-0"><?php echo $shipment_detail['sender']->phone_number; ?></p>
                                                        <p class="text-secondary mb-0"><?php echo $shipment_detail['sender']->address . ' ' . $shipment_detail['sender']->zipcode; ?></p>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <p style="font-weight: bold; font-size: 14px;">
                                                            <?php echo $shipment_detail['sender']->contact_person_name; ?>
                                                        </p>
                                                        <p class="text-secondary mb-0"><?php echo $shipment_detail['sender']->contact_person_email; ?></p>
                                                        <p class="text-secondary mb-0"><?php echo $shipment_detail['sender']->contact_person_phone_number; ?></p>
                                                        <p class="text-secondary mb-0"><?php echo $shipment_detail['sender']->contact_address . ' ' . $shipment_detail['sender']->contact_zipcode; ?></p>
                                                    </div>
                                                <?php endif; ?>
                                                <!-- Action buttons container -->
                                                <div class="action-buttons">
                                                    <p>
                                                        <!-- View Form -->
                                                        <!--                                                    <form action="-->
                                                        <?php //echo admin_url('courier/shipments/view'); ?><!--" method="post" style="display:inline;">-->
                                                        <!--                                                        <input type="hidden" name="shipment_id" value="-->
                                                        <?php //echo $shipment_detail['shipment']->id; ?><!--">-->
                                                        <!--                                                        <button type="submit" style="color:blue; background:none; border:none; padding:0; cursor:pointer;">-->
                                                        <!--                                                            View-->
                                                        <!--                                                        </button>-->
                                                        <!--                                                    </form>-->
                                                        <!--                                                    |-->
                                                        <!-- Edit Form -->
                                                        <!--                                                    <form action="-->
                                                        <?php //echo admin_url('courier/shipments/edit'); ?><!--" method="post" style="display:inline;">-->
                                                        <!--                                                        <input type="hidden" name="shipment_id" value="-->
                                                        <?php //echo $shipment_detail['shipment']->id; ?><!--">-->
                                                        <!--                                                        <button type="submit" style="color:blue; background:none; border:none; padding:0; cursor:pointer;">-->
                                                        <!--                                                            Edit-->
                                                        <!--                                                        </button>-->
                                                        <!--                                                    </form>-->
                                                        <!--                                                    |-->
                                                        <!-- Delete Form -->
                                                        <?php if (is_admin()): ?>
                                                    <form action="<?php echo admin_url('courier/shipments/delete'); ?>"
                                                          method="post" style="display:inline;"
                                                          onsubmit="return confirm('Are you sure you want to delete this shipment?');">
                                                        <input type="hidden"
                                                               name="<?php echo $this->security->get_csrf_token_name(); ?>"
                                                               value="<?php echo $this->security->get_csrf_hash(); ?>">
                                                        <input type="hidden" name="shipment_id"
                                                               value="<?php echo $shipment_detail['shipment']->id; ?>">
                                                        <button type="submit"
                                                                style="color:red; background:none; border:none; padding:0; cursor:pointer;">
                                                            Delete
                                                        </button>
                                                    </form>
                                                    <?php endif; ?>
                                                    </p>
                                                </div>

                                            </td>
                                            <td>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <?php if ($shipment_detail['recipient_type'] === 'individual'): ?>
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <p style="font-weight: bold; font-size: 14px;">
                                                                <?php echo $shipment_detail['recipient']->first_name . ' ' . $shipment_detail['recipient']->last_name; ?>
                                                            </p>
                                                            <p class="text-secondary mb-0"><?php echo $shipment_detail['recipient']->email; ?></p>
                                                            <p class="text-secondary mb-0"><?php echo $shipment_detail['recipient']->phone_number; ?></p>
                                                            <p class="text-secondary mb-0"><?php echo $shipment_detail['recipient']->address . ' ' . $shipment_detail['recipient']->zipcode; ?></p>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <p style="font-weight: bold; font-size: 14px;">
                                                                <?php echo $shipment_detail['recipient']->recipient_contact_person_name; ?>
                                                            </p>
                                                            <p class="text-secondary mb-0"><?php echo $shipment_detail['recipient']->recipient_contact_person_email; ?></p>
                                                            <p class="text-secondary mb-0"><?php echo $shipment_detail['recipient']->recipient_contact_person_phone_number; ?></p>
                                                            <p class="text-secondary mb-0"><?php echo $shipment_detail['recipient']->recipient_contact_address . ' ' . $shipment_detail['recipient']->recipient_contact_zipcode; ?></p>
                                                        </div>
                                                    <?php endif; ?>


                                            </td>
                                            <td><?php echo htmlspecialchars($shipment_detail['shipment']->shipping_mode); ?></td>
                                            <td>
                                                <?php
                                                $status_name = htmlspecialchars($shipment_detail['shipment']->status_name);
                                                $status_description = htmlspecialchars($shipment_detail['shipment']->status_description);
                                                switch ($status_name) {
                                                    case 'created':
                                                        $badge_class = 'bg-primary';
                                                        break;
                                                    case 'picked_up':
                                                        $badge_class = 'bg-info';
                                                        break;
                                                    case 'in_transit':
                                                    case 'dispatched':
                                                        $badge_class = 'bg-warning';
                                                        break;
                                                    case 'delivered':
                                                    case 'received':
                                                    case 'arrived_destination':
                                                    case 'out_for_delivery':
                                                        $badge_class = 'bg-success';
                                                        break;
                                                    default:
                                                        $badge_class = 'bg-secondary';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge badge-pill <?php echo $badge_class; ?>">
                                                    <?php echo $status_description; ?>
                                                </span>
                                            </td>

                                            <td>
                                                <?php echo date('d-m-Y, g:i A', strtotime($shipment_detail['shipment']->created_at)); ?>
                                            </td>
                                            <td class="align-middle">
                                                <div class="d-flex flex-row justify-content-center">
                                                    <?php
                                                    // Get the current URL query parameters
                                                    $type = $this->input->get('type');
                                                    $mode = $this->input->get('mode');
                                                    $mode_type = $this->input->get('mode_type');

                                                    // Store the parameters in the session
                                                    $this->session->set_userdata('type', $type);
                                                    $this->session->set_userdata('mode', $mode);
                                                    $this->session->set_userdata('mode_type', $mode_type);
                                                    ?>

                                                    <?php if (!empty($shipment_detail['shipment']->commercial_invoice_url)): ?>
                                                        <a style="margin-right: 5px; font-size:9px;"
                                                           href="<?php echo base_url($shipment_detail['shipment']->commercial_invoice_url); ?>"
                                                           class="btn btn-warning btn-sm font-weight-bold text-xs mx-2"
                                                           data-bs-toggle="tooltip" title="View Commercial invoice"
                                                           target="_blank">
                                                            <!-- This attribute opens the link in a new tab -->
                                                            <i class="fa fa-money-bill" aria-hidden="true"></i>
                                                            Commercial Invoice
                                                        </a><br>
                                                    <?php else: ?>
                                                        <a style="margin-right: 5px; font-size:9px;"
                                                           href="<?php echo admin_url('courier/shipments/commercial_invoice/' . $shipment_detail['shipment']->id); ?>"
                                                           class="btn btn-warning btn-sm font-weight-bold text-xs mx-2"
                                                           data-bs-toggle="tooltip" title="View Commercial invoice">
                                                            <i class="fa fa-money-bill" aria-hidden="true"></i>
                                                            Commercial
                                                            Invoice
                                                        </a><br>
                                                    <?php endif; ?>


                                                    <a style="font-size:9px; margin-top:5px;"
                                                       href="<?php echo admin_url('courier/shipments/waybill/' . $shipment_detail['shipment']->id); ?>"
                                                       class="btn btn-info btn-sm font-weight-bold text-xs mx-2"
                                                       data-bs-toggle="tooltip" title="View waybill">
                                                        <i class="fa fa-file" aria-hidden="true"></i> Waybill
                                                    </a>
                                                    <a style="font-size:9px; margin-top:5px;"
                                                       href="<?php echo admin_url('invoices/invoice/' . $shipment_detail['shipment']->invoice_id); ?>"
                                                       class="btn btn-success btn-sm font-weight-bold text-xs mx-2"
                                                       data-bs-toggle="tooltip" title="View waybill">
                                                        <i class="fa fa-money" aria-hidden="true"></i> Invoice
                                                    </a>
                                                    <?php if (!empty($shipment_detail['shipment']->pickup_id)): ?>
                                                        <a style="font-size:9px; margin-right:6px; margin-top:5px;"
                                                           href="<?php echo admin_url('courier/pickups/view/' . $shipment_detail['shipment']->pickup_id); ?>"
                                                           class="btn btn-info btn-sm font-weight-bold text-xs mx-2"
                                                           title="View Pickup">
                                                            <i class="fa fa-truck" aria-hidden="true"></i> Pick up
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                    <tfoot class="table-footer">
                                    <tr>
                                        <th>Tracking ID</th>
                                        <th>Sender</th>
                                        <th>Recipient</th>
                                        <th>Mode</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            <?php else: ?>
                                <!-- Show a message when there's no data -->
                                <div class="text-center text-danger">
                                    <p>No available shipments</p>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
    <?php init_tail(); ?>
</div>

<div class="modal fade" id="generateManifestModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <?php echo form_open('admin/courier/shipments/generate_manifest/', ['id' => 'generate-manifest-form']); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Generate Manifest</h4>
            </div>
            <div class="modal-body">
                <?php if ($this->session->flashdata('form_errors')): ?>
                    <div class="alert alert-danger">
                        <?php echo $this->session->flashdata('form_errors'); ?>
                    </div>
                <?php endif; ?>
                <div style="padding-left:20px; padding-right:20px;" class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="dateRange">Select Date Range</label>
                            <input type="text" class="form-control" id="dateRange" name="dateRange"
                                   placeholder="Select date range" required>
                            <?php if (form_error('dateRange')): ?>
                                <div class="error"><?= form_error('company_name') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="country_id">Recipient Country</label>
                            <?php
                            // Add "Select All" as the first option with an empty string as the value
                            $country_options = ['' => 'All'] + array_column($countries, 'short_name', 'country_id');

                            echo form_dropdown(
                                'country_id',
                                $country_options,
                                set_value('country_id'),
                                [
                                    'id' => 'country_id',
                                    'class' => 'custom-select',
                                    'style' => 'width:100%',
                                ]
                            );
                            ?>
                        </div>
                    </div>
                </div>
                <div style="border-bottom:0px; margin-top:20px; margin-bottom:-20px; padding-bottom:0px; border-left:0px; border-right:0px; border-radius:0px;"
                     class="row section-container">
                    <div class="section-label">Destination Office</div>
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="custom-form-group">
                                <label for="company_name" class="custom-label">Company Name</label>
                                <?php echo form_input(['id' => 'company_name', 'name' => 'company_name', 'type' => 'text', 'class' => 'custom-input', 'placeholder' => 'Company Name', 'value' => set_value('company_name')]); ?>
                                <?php if ($this->session->flashdata('company_name')): ?>
                                    <div class="text-danger"><?php echo $this->session->flashdata('company_name_error'); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="custom-form-group">
                                <label for="location" class="custom-label">Location</label>
                                <?php echo form_input(['id' => 'location', 'name' => 'location', 'type' => 'text', 'class' => 'custom-input', 'placeholder' => 'Location', 'value' => set_value('location')]); ?>
                                <?php if ($this->session->flashdata('location')): ?>
                                    <div class="text-danger"><?php echo $this->session->flashdata('location_error'); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="custom-form-group">
                                <label for="address" class="custom-label">Street Address</label>
                                <?php echo form_input(['id' => 'street_address', 'name' => 'street_address', 'type' => 'text', 'class' => 'custom-input', 'placeholder' => 'Address', 'value' => set_value('street_address')]); ?>
                                <?php if ($this->session->flashdata('street_address')): ?>
                                    <div class="text-danger"><?php echo $this->session->flashdata('street_address_error'); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="custom-form-group">
                                <label for="landmark" class="custom-label">LandMark</label>
                                <?php echo form_input(['id' => 'landmark', 'name' => 'landmark', 'type' => 'text', 'class' => 'custom-input', 'placeholder' => 'landmark', 'value' => set_value('landmark')]); ?>
                                <?php if ($this->session->flashdata('landmark')): ?>
                                    <div class="text-danger"><?php echo $this->session->flashdata('landmark_error'); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="custom-form-group">
                                <label for="contact_phone" class="custom-label">Phone Number</label>
                                <?php echo form_input(['id' => 'phone_number', 'name' => 'phone_number', 'type' => 'text', 'class' => 'custom-input', 'placeholder' => 'phone_number', 'value' => set_value('phone_number')]); ?>
                                <?php if ($this->session->flashdata('phone_number')): ?>
                                    <div class="text-danger"><?php echo $this->session->flashdata('phone_number_error'); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hidden inputs to carry URL parameters -->
                <input type="hidden" id="shipment_type" name="shipment_type" value="">
                <input type="hidden" id="shipment_mode" name="shipment_mode" value="">
                <input type="hidden" id="shipment_mode_type" name="shipment_mode_type" value="">
                <input type="hidden" name="form_submitted" value="1">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal">close</button>
                <button type="submit" class="btn btn-primary"
                        id="generateManifestBtn">Generate Manifest</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts should be at the end of the body -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Add necessary scripts for the modal -->
<script>
    $(document).ready(function () {

        <?php if ($this->session->flashdata('show_modal')): ?>
        $('#generateManifestModal').modal('show');
        <?php endif; ?>

        $('#status_id').select2({});

        $('#staff_id').select2({});

        $('#country_id').select2({
            dropdownParent: $('#generateManifestModal') // Replace #yourModalID with the ID of your modal
        });


        // Array of dates to be disabled (already used dates)
        const disabledDates = [];

        flatpickr("#dateRange", {
            mode: "range",
            dateFormat: "Y-m-d",
            disable: disabledDates,
        });

        flatpickr("#filterDateRange", {
            mode: "range",
            dateFormat: "Y-m-d",
            disable: disabledDates,
        });

        // Extract query parameters from the current URL
        const urlParams = new URLSearchParams(window.location.search);

        document.getElementById('shipment_type').value = urlParams.get('type');
        document.getElementById('shipment_mode').value = urlParams.get('mode');
        document.getElementById('shipment_mode_type').value = urlParams.get('mode_type');

        document.getElementById('clearFilters').addEventListener('click', function () {
            // Clear the input fields
            document.getElementById('filterDateRange').value = '';
            document.getElementById('status_id').selectedIndex = 0;

            // Get CSRF token
            const csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>'; // CSRF Token name
            const csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>'; // CSRF hash

            // AJAX request to clear the filters
            $.ajax({
                url: '<?php echo admin_url("courier/shipments/clear_filters"); ?>',
                type: "POST",
                data: {
                    [csrfName]: csrfHash, // Send the CSRF token with the request
                },
                dataType: "json",
                success: function (data) {
                    console.log('Clear filter response:', data);
                    window.location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    // Log detailed error information
                    console.error('Error clearing filters:', jqXHR.responseText);
                    console.error('Text Status:', textStatus);
                    console.error('Error Thrown:', errorThrown);
                    alert('Error clearing filters. Check the console for more details.');
                }
            });
        });


    });
</script>
