<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php load_courier_styles(); ?>
<?php echo '<script src="https://cdn.jsdelivr.net/npm/signature_pad"></script>'; ?>

<!-- Load Custom CSS Files -->
<?php echo '<link rel="stylesheet" href="' . base_url('modules/courier/assets/waybill.css') . '">'; ?>
<?php echo '<link rel="stylesheet" href="' . base_url('modules/courier/assets/progress.css') . '">'; ?>

<script>
    function printWaybill() {
        // Create a new iframe for printing
        const printFrame = document.createElement('iframe');
        printFrame.style.position = 'absolute';
        printFrame.style.top = '-1000px';
        document.body.appendChild(printFrame);

        const printDocument = printFrame.contentDocument || printFrame.contentWindow.document;
        const printContents = document.getElementById('waybill-section').innerHTML;

        // Inline CSS with padding for waybill container
        const cssStyles = `<style>
    @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap');

    body {
        font-family: 'Open Sans', Arial, sans-serif;
    }

    .waybill-container {
        position: relative;
        max-width: 800px;
        margin: auto;
        background: white;
        padding: 20px 30px;
        border: 2px solid #333;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        z-index: 2;
    }
    .watermark {
        background-size: contain;
        opacity: 0.1; /* Adjust opacity for print */
        pointer-events: none; /* Ensure watermark does not interfere with user interactions */
        position: absolute; /* Positioning to cover the container */
        top: 0;
        left: 0;
        width: 100%;
        height: 80%;
        z-index: -1; /* Ensure watermark is behind content */
    }

    @media print {
        .watermark{
                    display: block; /* Ensure watermark is visible in print */
        }
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 5px;
        border-bottom: 1px solid black;
        position: relative;
        z-index: 2;
    }

    .header img {
        max-width: 150px;
        height: auto;
    }

    .header .waybill-number {
        font-size: 14px;
        font-weight: bold;
        text-align: center;
    }

    .header .date {
        font-size: 16px;
        font-weight: bold;
    }

    .title {
        text-align: center;
        margin-top: 10px;
        font-size: 18px;
        font-weight: bold;
        text-transform: uppercase;
    }

    .info-table {
        width: 100%;
        margin-top: 10px;
        border-collapse: collapse;
    }

    .company-title {
        font-size: 10px;
    }

    .info-table th,
    .info-table td {
        padding: 2px;
        border: 1px solid #333;
        text-align: left;
        font-size: 10px;
    }

    .info-table th {
        background-color: #f0f0f0;
        font-weight: bold;
    }

    .no-border {
        border: none;
    }

    .shipping-section {
        display: flex;
        flex-direction: column;
        margin-top: 20px;
    }

    .shipping-info {
        width: 100%;
    }

    .shipping-level {
        width: 35%;
        margin-left: 20px;
        padding: 15px;
        background-color: #f9f9f9;
        border-radius: 5px;
    }

    .shipping-level h3 {
        margin-bottom: 10px;
        font-size: 16px;
        font-weight: bold;
        text-align: center;
    }

    .shipping-level .checkbox-group {
        display: flex;
        flex-direction: column;
    }

    .shipping-level .checkbox-group div {
        margin-bottom: 10px;
    }

    .shipping-level .checkbox {
        margin-bottom: 10px;
    }

    .shipping-level input {
        margin-right: 10px;
    }

    .international-options {
        margin-top: 10px;
        display: flex;
        flex-direction: column;
    }

    .international-options .checkbox {
        margin-bottom: 10px;
    }

    .international-options .checkbox-sub {
        margin-left: 20px;
    }

    .footer {
        text-align: center;
        margin-top: 20px;
        font-size: 14px;
        position: relative;
        z-index: 2;
    }

    .company-section {
        margin-top: 20px;
    }

    .company-section h3 {
        margin-bottom: 10px;
        font-size: 16px;
        font-weight: bold;
        text-align: center;
    }

    .terms {
        margin-top: 10px;
        font-size: 12px;
        line-height: 1.5;
        border-top: 2px solid #333;
        padding-top: 10px;
    }

    .terms h4 {
        font-weight: bold;
        margin: 0; /* Remove margin */
        padding: 0 5px; /* Add left padding only */
    }

    .terms .content {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap; /* Ensure content wraps in case of overflow */
        margin: 0; /* Remove margin */
        padding: 0; /* Remove padding */
    }

    .terms .column {
        width: 48%; /* Two columns side by side */
        margin: 0; /* Remove margin */
        padding: 0 5px; /* Add left and right padding */
    }

    .terms .column p {
        margin: 0; /* Remove margin between paragraphs */
        padding: 0; /* Remove padding */
    }
</style>
`;

        // Write the contents to the iframe's document
        printDocument.open();
        printDocument.write(`
            <html>
            <head>
                <title>Print Waybill</title>
                ${cssStyles}
            </head>
            <body>
                ${printContents}
            </body>
            </html>
        `);
        printDocument.close();

        // Wait for the content to load before printing
        printFrame.onload = function () {
            printFrame.contentWindow.focus();
            printFrame.contentWindow.print();

            // Remove the iframe after printing
            document.body.removeChild(printFrame);
        };
    }
</script>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php echo form_open($this->uri->uri_string(), ['id' => 'create-pickup-form']); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div style="margin-bottom:30px;" class="flex-container">
                            <a style="text-decoration: none; border:2px solid black;" class="custom-button"
                                <?php

                                $url = 'courier/shipments?type=';

                                // Set session data
                                $type = $this->session->userdata('type');

                                $url = $url . $type;

                                if ($this->session->userdata('mode') !== null) {
                                    $mode = $this->session->userdata('mode');
                                    $mode_type = $this->session->userdata('mode_type');
                                    $url = $url . '&mode=' . $mode . '&mode_type=' . $mode_type;
                                }


                                ?>
                               href="<?php echo admin_url($url); ?>">
                                <i class="fa fa-arrow-left" aria-hidden="true"></i>
                                <span style="margin-left:10px;">Back</span>
                            </a>

                            <div>
                                <a style="text-decoration: none; border:2px solid black;" class="custom-button"
                                   href="javascript:void(0);" onclick="printWaybill();">
                                    <i style="margin-right:4px;" class="fa fa-print" aria-hidden="true"></i>
                                    Print Waybill
                                </a>

                                <a style="border:2px solid black;" href="#" data-toggle="modal"
                                   data-target="#update_status" class="text-dark    custom-button">
                                    <i style="margin-right:4px;" class="fa-pen"></i>
                                    Update Status
                                </a>
                            </div>
                        </div>

                        <div style="margin-top:20px;" class="stepper-wrapper">
                            <?php
                            $displayCounter = 1; // Initialize a counter for display purposes
                            foreach ($statuses as $status):
                                // Check if we should skip the pickup step
                                if ($status->id != 2 || !empty($shipment_details['shipment']->pickup_id)):
                                    // Adjust the display counter based on the pickup condition
                                    $displayId = (!empty($shipment_details['shipment']->pickup_id) || $status->id != 2) ? $displayCounter : $displayCounter - 1;
                                    ?>
                                    <div class="stepper-item <?= ($status->id <= $shipment_details['shipment']->status_id) ? 'completed' : ''; ?> <?= ($status->id == $shipment_details['shipment']->status_id) ? 'active' : ''; ?>">
                                        <div class="step-counter"><?= $displayId; ?></div>
                                        <div class="step-name"><?= $status->description; ?></div>
                                    </div>
                                    <?php
                                    $displayCounter++; // Increment the display counter for the next step
                                endif;
                            endforeach;
                            ?>
                        </div>

                        <div style="margin-top:60px;" id="waybill-section" class="waybill-container">
                            <img class="watermark"
                                 src="<?php echo base_url('modules/courier/assets/go_shipping_logo.png') ?>"
                                 alt="Watermark">

                            <div style="display:flex; justify-content:right; width:100%; margin-top:15px; margin-bottom:5px;"
                                 class="barcode">
                                <img src="<?php echo $barcode; ?>" alt="Barcode">
                            </div>

                            <div class="header">
                                <img height="60" width="60"
                                     src="<?php echo base_url('modules/courier/assets/go_shipping_logo.png') ?>"
                                     alt="Company Logo">
                                <div class="waybill-number">Waybill
                                    Number: <?php echo $shipment_details['shipment']->tracking_id; ?></div>
                                <div class="date"><?php echo $current_date ?></div>
                            </div>

                            <table class="info-table">
                                <tr>
                                    <?php
                                    $is_sender_individual = $shipment_details['sender_type'] === 'individual';
                                    $is_recipient_individual = $shipment_details['recipient_type'] === 'individual';

                                    // Sender Information
                                    $sender_label = $is_sender_individual ? 'Sender Name' : 'Sender';
                                    $sender_name = $is_sender_individual
                                        ? $shipment_details['sender']->first_name . ' ' . $shipment_details['sender']->last_name
                                        : 'Company: ' . $shipment_details['sender']->company_name . ' (' . $shipment_details['sender']->contact_person_name . ')';

                                    // Recipient Information
                                    $recipient_label = $is_recipient_individual ? 'Receiver Name' : 'Receiver';
                                    $recipient_name = $is_recipient_individual
                                        ? $shipment_details['recipient']->first_name . ' ' . $shipment_details['recipient']->last_name
                                        : 'Company: ' . $shipment_details['recipient']->recipient_company_name . ' (' . $shipment_details['recipient']->recipient_contact_person_name . ')';
                                    ?>

                                    <th><?php echo $sender_label; ?></th>
                                    <td><?php echo $sender_name; ?></td>

                                    <th><?php echo $recipient_label; ?></th>
                                    <td><?php echo $recipient_name; ?></td>
                                </tr>

                                <tr>
                                    <?php
                                    $is_sender_individual = $shipment_details['sender_type'] === 'individual';
                                    $is_recipient_individual = $shipment_details['recipient_type'] === 'individual';

                                    // Sender Information
                                    $sender_address_label = $is_sender_individual ? 'Sender Address' : 'Contact person Address';
                                    $sender_address = $is_sender_individual
                                        ? (!empty($shipment_details['sender_country'])
                                            ? $shipment_details['sender']->address . ', ' . str_replace('_', ' ', $shipment_details['sender']->address_type) . ' ' . $shipment_details['sender']->zipcode . ', ' . $shipment_details['sender_country']->short_name
                                            : $shipment_details['sender']->address . ', ' . str_replace('_', ' ', $shipment_details['sender']->address_type) . ' ' . $shipment_details['sender']->zipcode)
                                        : (!empty($shipment_details['sender_country'])
                                            ? $shipment_details['sender']->contact_address . ', ' . str_replace('_', ' ', $shipment_details['sender']->contact_address_type) . ' ' . $shipment_details['sender']->contact_zipcode . ', ' . $shipment_details['sender_country']->short_name
                                            : $shipment_details['sender']->contact_address . ', ' . str_replace('_', ' ', $shipment_details['sender']->contact_address_type) . ' ' . $shipment_details['sender']->contact_zipcode);

                                    // Recipient Information


                                    $recipient_address_label = $is_recipient_individual ? 'Recipient Address' : 'Contact person Address';
                                    $recipient_address = $is_recipient_individual
                                        ? (!empty($shipment_details['recipient_country'])
                                            ? $shipment_details['recipient']->address . ', ' . str_replace('_', ' ', $shipment_details['recipient']->address_type) . ' ' . $shipment_details['recipient']->zipcode . ', ' . $shipment_details['recipient_country']->short_name
                                            : $shipment_details['recipient']->address . ', ' . str_replace('_', ' ', $shipment_details['recipient']->address_type) . ' ' . $shipment_details['recipient']->zipcode)
                                        : (!empty($shipment_details['recipient_country'])
                                            ? $shipment_details['recipient']->recipient_contact_address . ', ' . str_replace('_', ' ', $shipment_details['recipient']->recipient_contact_address_type) . ' ' . $shipment_details['recipient']->recipient_contact_zipcode . ', ' . $shipment_details['recipient_country']->short_name
                                            : $shipment_details['recipient']->recipient_contact_address . ', ' . str_replace('_', ' ', $shipment_details['recipient']->recipient_contact_address_type) . ' ' . $shipment_details['recipient']->recipient_contact_zipcode);

                                    ?>
                                    <th><?php echo $sender_address_label; ?></th>
                                    <td><?php echo $sender_address; ?></td>

                                    <th><?php echo $recipient_address_label; ?></th>
                                    <td><?php echo $recipient_address; ?></td>
                                </tr>
                                <tr>
                                    <?php
                                    $is_sender_individual = $shipment_details['sender_type'] === 'individual';
                                    $is_recipient_individual = $shipment_details['recipient_type'] === 'individual';

                                    $sender_phone_label = $is_sender_individual ? 'Sender Number' : 'Contact Person Number';
                                    $sender_phone = $is_sender_individual ? $shipment_details['sender']->phone_number
                                        : $shipment_details['sender']->contact_person_phone_number;

                                    $receiver_phone_label = $is_recipient_individual ? 'Receiver Number' : 'Contact Person Number';
                                    $receiver_phone = $is_recipient_individual ? $shipment_details['recipient']->phone_number
                                        : $shipment_details['recipient']->recipient_contact_person_phone_number;

                                    ?>

                                    <th><?php echo $sender_phone_label; ?></th>
                                    <td><?php echo $sender_phone; ?></td>

                                    <th><?php echo $receiver_phone_label; ?></th>
                                    <td><?php echo $receiver_phone; ?></td>

                                </tr>
                                <tr>
                                    <th>Tracking Number</th>
                                    <td
                                            colspan="3"><?php echo $shipment_details['shipment']->tracking_id; ?></td>
                                </tr>
                                <tr>
                                    <th>Shipping Level</th>
                                    <td><?php echo strtoupper($shipment_details['shipment']->shipping_category); ?></td>
                                    <th>Shipping Mode</th>
                                    <td><?php echo $shipment_details['shipment']->shipping_mode; ?></td>
                                </tr>
                            </table>
                            <div class="shipping-section">
                                <div class="shipping-info">
                                    <table class="info-table">
                                        <tr>
                                            <th>
                                                <?php echo !empty($shipment_details['shipment']->company_type) ? $shipment_details['shipment']->company_type : 'Courier Company'; ?>
                                            </th>
                                            <td>GO SHIPPING</td>
                                        </tr>
                                        <tr style="border-left:none; border-right:0px; ">
                                            <td colspan="2" class="no-border">
                                                <h4 style="font-weight:bold; margin-bottom:-10px;"
                                                    class="package-title">Package Details</h4>
                                                <?php if ($shipment_details['shipment']->fcl_shipment == 1): ?>
                                                    <table class="info-table no-border">
                                                        <?php $counter = 1; ?>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Quantity (cm)</th>
                                                            <th>Description (cm)</th>
                                                            <th>FCL Option</th>
                                                        </tr>
                                                        <?php foreach ($shipment_details['packages'] as $package): ?>
                                                            <tr>
                                                                <td><?php echo $counter ?></td>
                                                                <td><?php echo $package->quantity ?></td>
                                                                <td><?php echo $package->description ?></td>
                                                                <td><?php echo $package->fcl_option ?></td>
                                                            </tr>
                                                            <?php $counter++; ?>
                                                        <?php endforeach; ?>
                                                    </table>
                                                <?php else: ?>
                                                    <table class="info-table no-border">
                                                        <?php $counter = 1; ?>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Quantity</th>
                                                            <th>Length (cm)</th>
                                                            <th>Width (cm)</th>
                                                            <th>Height (cm)</th>
                                                            <th>Volumetric Weight (kg)</th>
                                                            <th>Gross Weight (kg)</th>
                                                            <th>Chargeable Weight (kg)</th>
                                                        </tr>
                                                        <?php foreach ($shipment_details['packages'] as $package): ?>
                                                            <tr>
                                                                <td><?php echo $counter ?></td>
                                                                <td><?php echo $package->quantity ?></td>
                                                                <td><?php echo $package->length ?></td>
                                                                <td><?php echo $package->width ?></td>
                                                                <td><?php echo $package->height ?></td>
                                                                <td><?php echo $package->weight_volume ?></td>
                                                                <td><?php echo $package->weight ?></td>
                                                                <td><?php echo $package->chargeable_weight ?></td>
                                                            </tr>
                                                            <?php $counter++; ?>
                                                        <?php endforeach; ?>
                                                    </table>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Shipping Notes</th>
                                            <td>Handle with care, Fragile items</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <link rel="stylesheet" href="path/to/waybill.css">

                            <div class="terms">
                                <h4 style="margin-bottom:5px;">Terms and Conditions</h4>
                                <div class="content">
                                    <div class="column">
                                        <p><strong>1. General Conditions:</strong> Use of our services implies
                                            acceptance of these terms and applicable laws.</p>
                                        <p><strong>2. Delivery Times:</strong> We estimate delivery times but do not
                                            guarantee specific dates. Delays may occur.</p>
                                        <p><strong>3. Package Restrictions:</strong> Ensure package contents comply
                                            with
                                            laws. Some items may be restricted or prohibited.</p>
                                        <p><strong>4. Shipping Charges:</strong> Charges are based on weight,
                                            dimensions, and destination. Additional fees may apply.</p>
                                        <p><strong>5. Claims and Liability:</strong> We are not liable for issues
                                            after
                                            delivery. Claims must be reported within a specified period.</p>
                                    </div>
                                    <div class="column">
                                        <p><strong>6. Customs and Duties:</strong> You are responsible for customs
                                            fees
                                            and taxes for international shipments.</p>
                                        <p><strong>7. Insurance:</strong> Optional insurance covers package value up
                                            to
                                            a limit. Refer to our policy for details.</p>
                                        <p><strong>8. Address Accuracy:</strong> Ensure correct address details to
                                            avoid
                                            delays or issues.</p>
                                        <p><strong>9. Changes to Terms:</strong> Terms may be updated. Review
                                            regularly
                                            for any changes.</p>
                                        <p><strong>10. Contact Information:</strong> For questions or concerns,
                                            contact
                                            our customer service at [contact information].</p>
                                    </div>
                                </div>
                                <p>Thank you for using our services. We strive to provide reliable delivery
                                    solutions.</p>
                            </div>
                            <div class="footer">
                                &copy; 2024 GO SHIPPING. All rights reserved.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
    <?php init_tail(); ?>
</div>

<div class="modal fade" id="update_status" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <?php echo form_open('admin/courier/shipments/update_status/' . $shipment_details['shipment']->id, ['id' => 'update-shipment-status-form']); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Update Status</h4>
                <input type="hidden" name="shipment_id" value="<?php echo $shipment_details['shipment']->id; ?>">
                <input type="hidden" value="" name="signature">
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <select onchange="toggleShipmentStops(); toggleDeliveryDetails();" id="status_id"
                                name="status_id"
                                class="custom-select">
                            <?php foreach ($statuses as $status): ?>
                                <?php if ($status->id == 2) continue; ?>
                                <option <?= $status->id == $shipment_details['shipment']->status_id ? "selected" : ""; ?>
                                        value="<?php echo $status->id; ?>">
                                    <?php echo $status->description ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <div style="margin-top:20px;" id="delivery_details" class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped"
                                           id="deliveryTable">
                                        <thead>
                                        <tr>
                                            <th>Receiver First Name</th>
                                            <th>Receiver Last Name</th>
                                            <th>Signature</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>
                                                <?php echo form_input([
                                                    'name' => 'first_name',
                                                    'class' => 'form-control',
                                                    'type' => 'text',
                                                    'step' => 'any',
                                                    'value' => set_value('first_name')
                                                ]); ?>
                                                <?php echo form_error('first_name', '<div class="text-danger">', '</div>'); ?>
                                            </td>
                                            <td>
                                                <?php echo form_input([
                                                    'name' => 'last_name',
                                                    'class' => 'form-control',
                                                    'type' => 'text',
                                                    'step' => 'any',
                                                    'value' => set_value('last_name')
                                                ]); ?>
                                                <?php echo form_error('last_name', '<div class="text-danger">', '</div>'); ?>
                                            </td>
                                            <td>
                                                <div style="margin-bottom:10px;" id="signatureCanvasP"
                                                     class="col-md-12">
                                                    <canvas height="150" id="signature"
                                                            style="margin-top:10px;  border: 1px solid #ddd;"></canvas>
                                                    <br>
                                                    <button style="margin-top:10px;" id="clear-signature"
                                                            class="btn-info btn ">Clear Signature
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Shipment stops Information -->
                        <div style="margin-top:20px;" id="shipment_stops" class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped"
                                           id="shipmentStopsTable">
                                        <thead>
                                        <tr>
                                            <th>Departure Point</th>
                                            <th>Destination Point</th>
                                            <th>Description</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>
                                                <?php echo form_input([
                                                    'name' => 'departure_points[]',
                                                    'class' => 'form-control',
                                                    'type' => 'text',
                                                    'step' => 'any',
                                                    'value' => set_value('departure_points[]')
                                                ]); ?>
                                                <?php echo form_error('departure_points[]', '<div class="text-danger">', '</div>'); ?>
                                            </td>
                                            <td>
                                                <?php echo form_input([
                                                    'name' => 'destination_points[]',
                                                    'class' => 'form-control',
                                                    'type' => 'text',
                                                    'step' => 'any',
                                                    'value' => set_value('destination_points[]')
                                                ]); ?>
                                                <?php echo form_error('destination_points[]', '<div class="text-danger">', '</div>'); ?>
                                            </td>
                                            <td>
                                                                <textarea name="description[]"
                                                                          class="custom-textarea"
                                                                          rows="3"
                                                                ><?php echo set_value('description[]'); ?></textarea>
                                                <?php echo form_error('description[]', '<div class="text-danger">', '</div>'); ?>
                                            </td>

                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-primary" onclick="addShipmentStops()">
                                    Add Stops
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo _l('update status'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        window.toggleShipmentStops = function () {
            const statusId = document.getElementById('status_id');
            const shipmentStops = document.getElementById('shipment_stops');
            const deliveryDetails = document.getElementById('delivery_details');

            if (statusId.value === '5') {
                shipmentStops.style.display = 'block';
            } else {
                shipmentStops.style.display = 'none';
            }
        }


        window.toggleDeliveryDetails = function () {

            const statusId = document.getElementById('status_id');
            const deliveryDetails = document.getElementById('delivery_details');

            if (statusId.value === '8') {
                deliveryDetails.style.display = 'block';
            } else {
                deliveryDetails.style.display = 'none';
            }
        }

        toggleDeliveryDetails();
        toggleShipmentStops();

        function attachRemoveEvent(button) {
            button.addEventListener('click', function () {
                this.closest('tr').remove();
            });
        }

        // Attach event to initial remove buttons
        const packageRemoveButtons = document.getElementsByClassName('remove-shipment-stop');
        for (let i = 0; i < packageRemoveButtons.length; i++) {
            attachRemoveEvent(packageRemoveButtons[i]);
        }

        // Add new row functionality for FCL package
        window.addShipmentStops = function () {
            const packageTable = document.getElementById('shipmentStopsTable').getElementsByTagName('tbody')[0];
            const newRow = packageTable.insertRow();

            newRow.innerHTML = `
            <td><input name="departure_points[]" class="form-control" type="text"></td>
            <td><input name="destination_points[]" class="form-control" type="text"></td>
            <td><textarea name="description[]" class="custom-textarea" rows="3"></textarea></td>
            <td><button type="button" class="btn btn-danger remove-shipment-stop"><i class="fa fa-trash"></i></button></td>
        `;

            attachRemoveEvent(newRow.getElementsByClassName('remove-shipment-stop')[0]);
        }


        let canvas = document.getElementById("signature");
        const signaturePad = new SignaturePad(canvas);

        $('#clear-signature').on('click', function (event) {
            event.preventDefault()
            signaturePad.clear();
        });

        document.getElementById('update-shipment-status-form').addEventListener('submit', function (e) {
            canvas = document.getElementById('signature');
            document.querySelector('input[name="signature"]').value = canvas.toDataURL('image/png');
        });

    })
</script>
