<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php load_courier_styles(); ?>
<style>
    .container-wrapper {
        max-width: 100%;
        margin: 0 auto;
        padding: 10px;
        border: 2px solid #333;
        border-radius: 5px;
        overflow: hidden;
    }

    .header-container {
        display: flex;
        justify-content: start;
        align-items: center;
        margin-bottom: 10px;
        padding: 10px;
        background-color: #fff; /* Ensuring the header has a white background */
    }

    .logo {
        max-width: 150px;
    }

    .details-container {
        width: 70%;
        display: flex;
        justify-content: start;
        margin-left: 10px;
        margin-right: 10px;
    }

    .details-section h3 {
        margin: 5px 0;
        font-size: 16px;
        font-weight: bold;
    }

    .btn-print {
        margin-top: 10px;
        margin-bottom: 10px;
        padding: 5px 15px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 3px;
        cursor: pointer;
    }

    .btn-save {
        margin-top: 10px;
        margin-bottom: 10px;
        padding: 5px 15px;
        background-color: green;
        color: white;
        border: none;
        border-radius: 3px;
        cursor: pointer;
    }

    .btn-print:hover {
        background-color: #0056b3;
    }

    .excel-table-container {
        max-width: 100%;
        overflow-x: auto;
    }

    .excel-table {
        width: 100%;
        border-collapse: collapse;
        border: none; /* Remove border from the table itself */
        font-family: Arial, sans-serif;
        table-layout: fixed;
    }

    .excel-table th, .excel-table td {
        border: 1px solid #ddd;
        padding: 3px;
        text-align: left;
    }

    .excel-table th {
        background-color: #f4f4f4;
        font-weight: bold;
        white-space: pre-wrap;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .excel-table td[contenteditable="true"] {
        background-color: #fff;
        cursor: text;
        white-space: pre-wrap;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .excel-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .excel-table tr:hover {
        background-color: #f1f1f1;
    }

    .excel-table td:focus {
        outline: none;
        background-color: #eef;
    }
</style>

<div id="wrapper">
    <div style="padding-left:20px; padding-right:20px;" class="content">
        <button style="margin-bottom:20px;" class="btn-print" onclick="printTable()">Print Manifest</button>

        <button style="margin-bottom:20px;" class="btn-save" onclick="saveManifest()">Update Manifest</button>

        <div class="container-wrapper">
            <div class="title-container"
                 style="text-align:center; padding-top:10px; margin-bottom:-10px; background-color:white;">
                <h2>
                    DUBAI AIR
                    MANIFEST <?php echo strtoupper(date('F Y', strtotime($manifest_records[0]->created_at))); ?>
                </h2>
                <h4>
                    AWB <?php echo $manifest_number ?> FLIGHT <?php echo $manifest_records[0]->flight_number ?>
                    - <?php echo date('j/n/y', strtotime($manifest_records[0]->created_at)); ?>
                </h4>
            </div>
            <div class="header-container">
                <div class="logo">
                    <img width="200" height="200" class=""
                         src="<?php echo base_url('modules/courier/assets/go_shipping_transparent_logo.png') ?>"
                         alt="Watermark">
                </div>
                <div style="margin-left:80px;" class="details-container">
                    <div class="details-section">
                        <h3>GO SHIPPING CARGO LTD</h3>
                        <p><strong>Late Omar Ali Bin Haider Building</strong></p>
                        <p><strong>Second Floor, Suite 204</strong></p>
                        <p><strong>Near Sabkha Bus Station, Opp. Emirates Hotel</strong></p>
                        <p><strong>Tel: + 97142859988</strong></p>
                    </div>
                    <div style="margin-left:70px;" class="details-section">
                        <h3><?php echo strtoupper($manifest_records[0]->company_name); ?></h3>
                        <p><strong><?php echo ucfirst($manifest_records[0]->location); ?></strong></p>
                        <p><strong><?php echo ucfirst($manifest_records[0]->street_address); ?></strong></p>
                        <p><strong><?php echo ucfirst($manifest_records[0]->landmark); ?></strong></p>
                        <p><strong>Tel: <?php echo strtoupper($manifest_records[0]->phone_number); ?></strong></p>
                    </div>
                </div>
            </div>

            <div class="excel-table-container">
                <?php if (!empty($manifest_records)): ?>
                    <table class="excel-table" id="excel-table">
                        <thead>
                        <tr>
                            <th>DATE</th>
                            <th>SENDER</th>
                            <th>RCVR</th>
                            <th>PHONE</th>
                            <th>AWB#</th>
                            <th>DESC</th>
                            <th>PCS</th>
                            <th>KGS</th>
                            <th>RATE</th>
                            <th>USD</th>
                            <th>STAT</th>
                            <th>PACK</th>
                            <th>DEST</th>
                            <th>RMKS</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($manifest_records as $manifest_record): ?>
                            <tr>
                                <td contenteditable="true"><?php echo $manifest_record->date ?></td>
                                <td contenteditable="true"><?php echo $manifest_record->sender ?></td>
                                <td contenteditable="true"><?php echo $manifest_record->rcvr ?></td>
                                <td contenteditable="true"><?php echo $manifest_record->phone ?></td>
                                <td contenteditable="true"><?php echo $manifest_record->awb_number ?></td>
                                <td contenteditable="true"><?php echo $manifest_record->description ?></td>
                                <td contenteditable="true" class="pcs"><?php echo $manifest_record->pcs ?></td>
                                <td contenteditable="true" class="kgs"><?php echo $manifest_record->kgs ?></td>
                                <td contenteditable="true" class="rate"><?php echo $manifest_record->rate ?></td>
                                <td contenteditable="true" class="usd"><?php echo $manifest_record->usd ?></td>
                                <td contenteditable="true"><?php echo $manifest_record->status ?></td>
                                <td contenteditable="true"><?php echo $manifest_record->pack ?></td>
                                <td contenteditable="true"><?php echo $manifest_record->dest ?></td>
                                <td contenteditable="true"><?php echo $manifest_record->rmks ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr style="font-weight:bold; font-size:15px; background-color:grey;" class="table-row">
                            <td style="font-weight:bold; font-size:15px; background-color:#f4f4f4;"
                                contenteditable="true" class="editable-cell"></td>
                            <td style="font-weight:bold; font-size:15px; background-color:#f4f4f4; color:black;"
                                contenteditable="true" class="editable-cell"></td>
                            <td style="font-weight:bold; font-size:15px; background-color:#f4f4f4; color:black;"
                                contenteditable="true" class="editable-cell"></td>
                            <td style="font-weight:bold; font-size:15px; background-color:#f4f4f4; color:black;"
                                contenteditable="true" class="editable-cell"></td>
                            <td style="font-weight:bold; font-size:15px; background-color:#f4f4f4; color:black;"
                                contenteditable="true" class="total-cell">TOTAL
                            </td>
                            <td style="font-weight:bold; font-size:15px; background-color:#f4f4f4; color:black;"
                                contenteditable="true" class="editable-cell"></td>
                            <td style="font-weight:bold; font-size:15px; background-color:#f4f4f4; color:black;"
                                contenteditable="true" class="total-pcs">0
                            </td>
                            <td style="font-weight:bold; font-size:15px; background-color:#f4f4f4; color:black;"
                                contenteditable="true" class="total-kgs">0
                            </td>
                            <td style="font-weight:bold; font-size:15px; background-color:#f4f4f4; color:black;"
                                contenteditable="true" class="editable-cell"></td>
                            <td style="font-weight:bold; font-size:15px; background-color:#f4f4f4; color:black;"
                                contenteditable="true" class="total-usd">0
                            </td>
                            <td style="font-weight:bold; font-size:15px; background-color:#f4f4f4; color:black;"
                                contenteditable="true" class="editable-cell"></td>
                            <td style="font-weight:bold; font-size:15px; background-color:#f4f4f4; color:black;"
                                contenteditable="true" class="empty-cell"></td>
                            <td style="font-weight:bold; font-size:15px; background-color:#f4f4f4; color:black;"
                                contenteditable="true" class="editable-cell"></td>
                            <td style="font-weight:bold; font-size:15px; background-color:#f4f4f4; color:black;"
                                contenteditable="true" class="editable-cell"></td>
                        </tr>
                        </tbody>
                    </table>
                <?php else: ?>
                    <!-- Show a message when there's no data -->
                    <div class="text-center text-danger">
                        <p>No available shipments</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
    $(document).ready(function () {

        window.saveManifest = function () {
            // Initialize an array to hold manifest data
            const manifestData = [];

            // Loop through each row of the table except the last one
            $('#excel-table tbody tr').each(function (index) {
                // Exclude the last row (total row)
                if ($(this).index() !== $('#excel-table tbody tr').length - 1) {
                    const rowData = {
                        date: $(this).find('td:eq(0)').text().trim(),
                        sender: $(this).find('td:eq(1)').text().trim(),
                        rcvr: $(this).find('td:eq(2)').text().trim(),
                        phone: $(this).find('td:eq(3)').text().trim(),
                        awb_number: $(this).find('td:eq(4)').text().trim(),
                        description: $(this).find('td:eq(5)').text().trim(),
                        pcs: $(this).find('td.pcs').text().trim(),
                        kgs: $(this).find('td.kgs').text().trim(),
                        rate: $(this).find('td.rate').text().trim(),
                        usd: $(this).find('td.usd').text().trim(),
                        status: $(this).find('td:eq(11)').text().trim(), // Assuming status is in the 12th column
                        pack: $(this).find('td:eq(12)').text().trim(),
                        dest: $(this).find('td:eq(13)').text().trim(),
                        rmks: $(this).find('td:eq(14)').text().trim(),
                    };

                    // Add the row data to the manifestData array
                    manifestData.push(rowData);
                }
            });

            // Get CSRF token
            const csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>'; // CSRF Token name
            const csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>'; // CSRF hash

            // AJAX request to store the data
            $.ajax({
                url: '<?php echo admin_url("courier/manifests/store"); ?>',
                type: "POST",
                data: {
                    [csrfName]: csrfHash,
                    manifests: manifestData,
                    manifest_number: "<?php echo $manifest_number ?>",
                    flight_number: "<?php echo $manifest_records[0]->flight_number ?>"
                    destination_id: "<?php echo $manifest_records[0]->destination_id ?>"
                },
                dataType: "json",
                success: function (data) {
                    console.log('Store response:', data);
                    alert('Manifest Updated Successfully');
                    window.location.href = '<?php echo admin_url('courier/manifests') ?>'
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    // Log detailed error information
                    console.error('Error storing data:', jqXHR.responseText);
                    console.error('Text Status:', textStatus);
                    console.error('Error Thrown:', errorThrown);
                    alert('Error storing data. Check the console for more details.');
                }
            });
        }


        window.printTable = function () {
            const tableToPrint = document.getElementById('excel-table').outerHTML;
            const headerToPrint = document.querySelector('.header-container').outerHTML;
            const titleToPrint = document.querySelector('.title-container').outerHTML;
            const newWin = window.open("");
            newWin.document.write(`
        <html>
        <head>
        <title>Print Manifest</title>
        <style>
            body {
                font-family: 'Open Sans', Arial, sans-serif;
                padding-left: 20px;
                padding-right: 20px;
            }
            .container-wrapper {
                max-width: 100%;
                margin: 0 auto;
                padding: 10px;
                border: 2px solid #333;
                border-radius: 5px;
                overflow: hidden;
                font-size: 12px;
            }

            .header-container {
                display: flex;
                justify-content: start;
                align-items: center;
                margin-bottom: 10px;
                padding: 10px;
                background-color: #fff; /* Ensuring the header has a white background */
                border-bottom: 2px solid #333;
            }

            .details-container {
                width: 70%;
                display: flex;
                justify-content: start;
                margin-left: 10px;
                margin-right: 10px;
            }

            .details-section h3 {
                margin: 5px 0;
                font-size: 14px;
                font-weight: bold;
            }

            .excel-table {
                width: 100%;
                border-collapse: collapse;
                table-layout: fixed;
            }

            .excel-table th, .excel-table td {
                border: 1px solid #ddd;
                padding: 3px;
                text-align: left;
            }

            .excel-table th {
                background-color: #f4f4f4;
                font-weight: bold;
                white-space: pre-wrap;
                word-wrap: break-word;
                overflow-wrap: break-word;
            }

            .excel-table tr:nth-child(even) {
                background-color: #f9f9f9;
            }

            .excel-table tr:hover {
                background-color: #f1f1f1;
            }
        </style>
        </head>
        <body>
            <div class="container-wrapper">
                ${titleToPrint}
                ${headerToPrint}
                <div class="excel-table-container">
                    ${tableToPrint}
                </div>
            </div>
        </body>
        </html>
    `);
            newWin.document.close();
            newWin.print();
        }

        document.addEventListener("input", function (event) {
            if (event.target.closest("table")) {
                updateTotals();
            }
        });

        window.updateTotals = function () {
            let pcsTotal = 0;
            let kgsTotal = 0;
            let usdTotal = 0;

            document.querySelectorAll(".pcs").forEach(function (td) {
                pcsTotal += parseFloat(td.innerText) || 0;
            });

            document.querySelectorAll(".kgs").forEach(function (td) {
                kgsTotal += parseFloat(td.innerText) || 0;
            });

            document.querySelectorAll(".usd").forEach(function (td) {
                usdTotal += parseFloat(td.innerText) || 0;
            });

            document.querySelector(".total-pcs").innerText = pcsTotal;
            document.querySelector(".total-kgs").innerText = kgsTotal;
            document.querySelector(".total-usd").innerText = usdTotal;
        }

    })
</script>
