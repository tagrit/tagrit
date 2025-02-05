<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php load_courier_styles(); ?>

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
        .watermark {
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
                               href="<?php echo !empty($mode) ? admin_url('courier/shipments?type=' . $type . '&mode=' . $mode . '&mode_type=' . $mode_type) : admin_url('courier/shipments?type=' . $type); ?>">
                                <i class="fa fa-arrow-left" aria-hidden="true"></i>
                                <span style="margin-left:10px;">Back</span>
                            </a>

                            <div>
                                <a style="text-decoration: none; border:2px solid black;" class="custom-button"
                                   href="javascript:void(0);" onclick="printWaybill();">
                                    <i style="margin-right:4px;" class="fa fa-print" aria-hidden="true"></i>
                                    Print Commercial Invoice
                                </a>

                            </div>
                        </div>


                        <div style="margin-top:60px;" id="waybill-section" class="waybill-container">
                            <div class="header">
                                <img style="visibility: hidden;" height="60" width="60"
                                     src=#"
                                     alt="Company Logo">
                                <h3 style="font-weight:bold;">COMMERCIAL INVOICE</h3>
                                <div style="visibility: hidden;" class="date"><?php echo $current_date ?></div>
                            </div>

                            <table class="info-table">
                                <tr>
                                    <th>SENDER</th>
                                    <th>RECEIVER</th>
                                </tr>
                                <tr style="font-weight:bold;">
                                    <td>
                                        <?php
                                        if ($shipment_details['sender_type'] === 'individual') {
                                            echo strtoupper($shipment_details['sender']->first_name . ' ' . $shipment_details['sender']->last_name);
                                        } else {
                                            echo strtoupper($shipment_details['sender']->company_name . ' ' . $shipment_details['sender']->contact_person_name);
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($shipment_details['recipient_type'] === 'individual') {
                                            echo strtoupper($shipment_details['recipient']->first_name . ' ' . $shipment_details['recipient']->last_name);
                                        } else {
                                            echo strtoupper($shipment_details['recipient']->recipient_company_name . ' ' . $shipment_details['recipient']->recipient_contact_person_name);
                                        }
                                        ?>
                                    </td>
                                </tr>

                                <tr>
                                    <?php if ($shipment_details['sender_type'] === 'individual'): ?>
                                        <td><?php echo $shipment_details['sender']->address . ',' . str_replace('_', ' ', $shipment_details['sender']->address_type) . ' ' . $shipment_details['sender']->zipcode; ?></td>
                                    <?php else: ?>
                                        <td><?php echo $shipment_details['sender']->contact_address . ',' . str_replace('_', ' ', $shipment_details['sender']->contact_address_type) . ' ' . $shipment_details['sender']->contact_zipcode; ?></td>
                                    <?php endif; ?>
                                    <?php if ($shipment_details['recipient_type'] === 'individual'): ?>
                                        <td><?php echo $shipment_details['recipient']->address . ',' . str_replace('_', ' ', $shipment_details['recipient']->address_type) . ' ' . $shipment_details['recipient']->zipcode; ?></td>
                                    <?php else: ?>
                                        <td><?php echo $shipment_details['recipient']->recipient_contact_address . ',' . str_replace('_', ' ', $shipment_details['recipient']->recipient_contact_address_type) . ' ' . $shipment_details['recipient']->recipient_contact_zipcode; ?></td>
                                    <?php endif; ?>
                                </tr>
                                <tr>
                                    <?php if ($shipment_details['sender_type'] === 'individual'): ?>
                                        <td
                                        ">TEL : <?php echo $shipment_details['sender']->phone_number; ?></td>
                                    <?php else: ?>
                                        <td
                                        ">TEL : <?php echo $shipment_details['sender']->contact_person_phone_number; ?></td>
                                    <?php endif; ?>
                                    <?php if ($shipment_details['recipient_type'] === 'individual'): ?>
                                        <td
                                        <td>TEL : <?php echo $shipment_details['recipient']->phone_number; ?></td>
                                    <?php else: ?>
                                        <td
                                        <td>TEL : <?php echo $shipment_details['recipient']->recipient_contact_person_phone_number; ?></td>
                                    <?php endif; ?>
                                </tr>
                                <tr style="font-weight:bold;">
                                    <?php if (!empty($shipment_details['sender_country'])): ?>
                                        <td>ORIGIN COUNTRY
                                            : <?php echo strtoupper($shipment_details['sender_country']->short_name); ?></td>
                                    <?php endif; ?>

                                    <?php if (!empty($shipment_details['recipient_country'])): ?>
                                        <td>DESTINATION COUNTRY
                                            : <?php echo strtoupper($shipment_details['recipient_country']->short_name); ?></td>
                                    <?php endif; ?>
                                </tr>
                            </table>

                            <table style="margin-top:30px;" class="info-table no-border">
                                <?php
                                $counter = 1;
                                $total = 0;

                                ?>
                                <tr>
                                    <th>#</th>
                                    <th>QUANTITY</th>
                                    <th>DESCRIPTION</th>
                                    <th>AMOUNT</th>
                                </tr>
                                <?php foreach ($shipment_details['commercial_details'] as $commercial_detail): ?>
                                    <tr>
                                        <td><?php echo $counter ?></td>
                                        <td><?php echo $commercial_detail->quantity ?></td>
                                        <td><?php echo $commercial_detail->description ?></td>
                                        <td><?php echo $commercial_detail->declared_value ?></td>
                                    </tr>
                                    <?php $counter++; ?>
                                    <?php $total += $commercial_detail->declared_value; ?>
                                <?php endforeach; ?>
                                <tr>
                                    <td></td>
                                    <td style="font-weight:bold;"> TOTAL</td>
                                    <td></td>
                                    <td><?php echo $total ?></td>
                                </tr>
                            </table>

                            <div style="border-top:0px; margin-top:10px;" class="terms">
                                <h4 style="margin-top:5px; text-decoration:underline; margin-bottom:5px;">
                                    DECLARATION</h4>
                                <div class="content">
                                    <p style="font-size:16px; padding-left:8px;">I declare that this invoice shows the
                                        actual value/price of the goods described and that all particulars are true and
                                        correct
                                        and that the goods are of no commercial value, the value used is only for
                                        commercial
                                        purposes.</p>
                                </div>
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

