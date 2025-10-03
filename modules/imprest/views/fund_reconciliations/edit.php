<?php init_head(); ?>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f9f9fc;
        color: #333;
        line-height: 1.6;
    }

    .container {
        max-width: 1000px;
        margin: 20px auto;
        padding: 20px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .event-summary {
        background-color: #f1f5fa;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .event-summary h2 {
        font-size: 20px;
        color: #444;
        text-align: center;
        margin-bottom: 15px;
    }

    .event-row {
        display: flex;
        justify-content: left;
        margin-bottom: 10px;
    }

    .event-label {
        font-weight: 600;
        color: #444;
    }

    .event-value {
        color: #666;
        text-align: right;
    }

    .action-buttons {
        text-align: center;
        margin-top: 20px;
    }

    .approve-btn {
        background-color: #4caf50;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .save-btn {
        background-color: #36184f;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .approve-btn:hover {
        background-color: #45a049;
    }

    .fund-requests {
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        background-color: #fff;
        margin-top: 20px;
    }

    .section-title {
        font-size: 22px;
        margin-bottom: 15px;
        color: #333;
        border-bottom: 2px solid #4caf50;
        padding-bottom: 8px;
    }

    .fund-category {
        margin-top: 20px;
    }

    .fund-category h3 {
        font-size: 18px;
        color: #333;
        margin-bottom: 10px;
    }

    .subcategory table {
        width: 100%;
        border-collapse: collapse;
    }

    .subcategory table th, .subcategory table td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: left;
        max-width: 100px;
    }

    .subcategory table th {
        background-color: #f0f0f5;
        font-weight: 600;
        color: #333;
    }

    .file-input-container {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .custom-file-input {
        background-color: #eef2f7;
        border: 2px dashed #ddd;
        padding: 10px;
        border-radius: 8px;
        text-align: center;
        color: #555;
        cursor: pointer;
        font-weight: bold;
        transition: background-color 0.3s ease;
    }

    .custom-file-input:hover {
        background-color: #e0e5ec;
    }

    .total {
        font-size: 20px;
        font-weight: bold;
        text-align: right;
        margin-top: 20px;
        color: #333;
    }

    .custom-file-input {
        background-color: #eef2f7;
        border: 2px dashed #ddd;
        padding: 10px;
        border-radius: 8px;
        text-align: center;
        color: #555;
        cursor: pointer;
        font-weight: bold;
        transition: background-color 0.3s ease;
    }

    .custom-file-input:hover {
        background-color: #e0e5ec;
    }


    .total-amount {
        font-weight: bold;
        font-size: 18px;
        text-align: right;
        margin-top: 20px;
        color: green;
    }

    .file-input-container {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        width: 100%;
        max-width: 300px;
        margin-top: 10px;
    }

    .custom-file-input {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px;
        font-size: 14px;
        font-weight: bold;
        color: #555;
        background-color: #f9f9f9;
        border: 2px dashed #ddd;
        border-radius: 10px;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.3s ease, background-color 0.3s ease;
        width: 100%;
    }

    .custom-file-input:hover {
        border-color: #333;
        background-color: #eef2f7;
    }

    .file-input {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .file-label {
        margin-top: 10px;
        font-size: 12px;
        color: #666;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        max-width: 100%;
    }

    .loader {
        width: 48px;
        height: 48px;
        border: 3px solid #FFF;
        border-radius: 50%;
        display: inline-block;
        position: relative;
        box-sizing: border-box;
        animation: rotation 1s linear infinite;
    }

    .loader::after {
        content: '';
        box-sizing: border-box;
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        width: 56px;
        height: 56px;
        border-radius: 50%;
        border: 3px solid transparent;
        border-bottom-color: #FF3D00;
    }

    @keyframes rotation {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
</style>
<div id="wrapper">
    <div class="content">
         <span id="spanLoader" style="display:none; position: fixed; top: 50%; left: 50%; z-index: 9999;"
               class="loader"></span>

        <div style="margin-bottom:20px;">
            <a href="<?php echo admin_url('imprest/fund_requests'); ?>" class="btn btn-primary">
                <i style="margin-right:10px;" class="fa fa-arrow-left"></i>Back
            </a>
        </div>

        <?php echo form_open('admin/imprest/fund_reconciliations/request/' . $fund_request_details['id'], [
            'id' => 'submit-for-reconciliation-form',
            'enctype' => 'multipart/form-data'
        ]); ?>

        <!-- Hidden input to store the flag value -->
        <input type="hidden" name="flag" id="flag" value="">

        <div style="background-color:transparent;" class="panel_s">
            <div style="display: flex; padding: 20px; justify-content: space-between;">
                <div style="max-width:50%;" class="event-summary">
                    <h4 style="font-weight: bold;">Event Summary</h4>
                    <div class="event-row">
                        <span style="padding-right:3px; font-weight: bold;">Event: </span>
                        <strong> <?= $event_details['event_name'] ?></strong>
                    </div>
                    <?php if (!empty($event_details['start_date']) && $event_details['start_date'] !== "0000-00-00" && !empty($event_details['end_date']) && $event_details['end_date'] !== "0000-00-00"): ?>
                        <div class="event-row">
                            <span style="padding-right:3px; font-weight: bold;">Date: </span>
                            <strong> <?= $event_details['start_date'] ?>
                                - <?= $event_details['end_date'] ?></strong>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($event_details['venue'])): ?>
                        <div class="event-row">
                            <span style="padding-right:3px; font-weight: bold;">Venue: </span>
                            <strong> <?= $event_details['venue'] ?></strong>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($event_details['organization'])): ?>
                        <div class="event-row">
                            <span style="padding-right:3px; font-weight: bold;">Organization: </span>
                            <strong> <?= $event_details['organization'] ?></strong>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($event_details['revenue'])): ?>
                        <div style="display: flex; align-items: center; gap: 15px;">
                               <span style="color: green; font-weight: bold;">
                                Revenue: <?= number_format($event_details['revenue'], 2) ?>
                               </span>
                            <span style="color: purple; font-weight: bold;">
                                Expense: <?= number_format($total_amount_requested, 2) ?>
                               </span>
                            <span style="color: <?= ($event_details['revenue'] - $total_amount_requested) >= 0 ? 'blue' : 'red'; ?>; font-weight: bold;">
                               Net: <?= number_format($event_details['revenue'] - $total_amount_requested, 2) ?>
                             </span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($event_details['facilitator'])): ?>
                        <div class="event-row">
                            <span style="padding-right:3px; font-weight: bold;">Facilitator: </span>
                            <strong> <?= $event_details['facilitator'] ?></strong>
                        </div>
                    <?php endif; ?>

                    <div class="event-row">
                        <span style="padding-right:3px; font-weight: bold;">Trainers:</span>
                        <strong>
                            <?php
                            $trainers = unserialize($event_details['trainers']);
                            if (is_array($trainers) && !empty($trainers)) {
                                echo implode(', ', $trainers);
                            } else {
                                echo 'No trainers available';
                            }
                            ?>
                        </strong>
                    </div>

                    <?php if (!empty($event_details['delegates'])): ?>
                        <div class="event-row">
                            <span style="padding-right:3px; font-weight: bold;">Delegates: </span>
                            <strong> <?= $event_details['delegates'] ?></strong>
                        </div>
                    <?php endif; ?>

                    <p style="margin-top:20px; font-size:15px;"><span style="font-weight: bold;">Requested By :</span>
                        <?= $fund_request_details['requested_by']; ?>
                    </p>
                </div>
                <div class="action-buttons">
                    <?php if ($fund_request_details['status'] === 'reconciliation_rejected'): ?>
                        <!-- Mark as Resolved Button -->
                        <button type="submit" name="flag" value="solve-reconciliation" class="save-btn">
                            <i style="margin-right:10px;" class="fa fa-check"></i>Mark as Resolved
                        </button>
                    <?php elseif ($fund_request_details['status'] !== 'approved'): ?>
                        <!-- Save Receipts Button -->
                        <button type="submit" name="flag" value="save-receipt" class="save-btn">
                            <i style="margin-right:10px;" class="fa fa-file"></i>Save Receipts
                        </button>
                    <?php else: ?>
                        <!-- Save Receipts Button -->
                        <button type="submit" name="flag" value="save-receipt" class="save-btn">
                            <i style="margin-right:10px;" class="fa fa-file"></i>Save Receipts
                        </button>
                        <!-- Submit for Reconciliation Button -->
                        <button type="submit" name="flag" value="reconcile" class="approve-btn">
                            <i style="margin-right:10px;" class="fa fa-check"></i>Submit for Reconciliation
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="panel-body">
                <div class="fund-requests">
                    <!-- Categories -->

                    <?php foreach ($categories as $categoryName => $subcategories): ?>
                        <div style="margin-top:20px;" class="fund-category">
                            <h4 style="font-weight: bold;"><?= $categoryName ?></h4>
                            <div class="subcategory">
                                <table>
                                    <thead>
                                    <tr>
                                        <th>Subcategory</th>
                                        <th>Amount (KES)</th>
                                        <th>Attach Receipt</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <?php foreach ($subcategories as $subcategory): ?>
                                        <tr>
                                            <!-- Subcategory name -->
                                            <td>
                                                <?php if ($categoryName === 'Hotel Conferencing'): ?>
                                                    <div>
                                                        <h4 style="font-weight: bold; ">Details:</h4>
                                                        <div class="event-row"><span>Hotel Name:</span>
                                                            <strong><?= $conferencing_details['hotel_name'] ?></strong>
                                                        </div>
                                                        <div class="event-row"><span>Number of People:</span>
                                                            <strong><?= $conferencing_details['number_of_persons'] ?></strong>
                                                        </div>
                                                        <div class="event-row"><span>Charges Per Person:</span>
                                                            <strong><?= $conferencing_details['amount_per_person'] ?></strong>
                                                        </div>
                                                        <div class="event-row"><span>Number of Days:</span>
                                                            <strong><?= $conferencing_details['number_of_days'] ?></strong>
                                                        </div>
                                                    </div>
                                                <?php elseif ($categoryName === 'Hotel Accommodation'): ?>
                                                    <div>
                                                        <h4 style="font-weight: bold; ">Details:</h4>
                                                        <div class="event-row"><span>Hotel Name:</span>
                                                            <strong><?= $accommodation_details['hotel_name'] ?></strong>
                                                        </div>
                                                        <div class="event-row"><span>Number of People:</span>
                                                            <strong><?= $accommodation_details['number_of_persons'] ?></strong>
                                                        </div>
                                                        <div class="event-row"><span>Charges Per Person:</span>
                                                            <strong><?= $accommodation_details['amount_per_person'] ?></strong>
                                                        </div>
                                                        <div class="event-row"><span>Dinner Per Person:</span>
                                                            <strong><?= $accommodation_details['dinner'] ?></strong>
                                                        </div>
                                                        <div class="event-row"><span>Number of Days:</span>
                                                            <strong><?= $accommodation_details['number_of_nights'] ?></strong>
                                                        </div>
                                                    </div>
                                                <?php elseif ($categoryName === 'Additional Funds'): ?>
                                                    <div>
                                                        <h4 style="font-weight: bold; ">Reason:</h4>
                                                        <div class="event-row">
                                                            <strong><?= $additional_funds['reason'] ?></strong>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <?= $subcategory['subcategory_name'] ?>
                                                <?php endif; ?>
                                            </td>
                                            <!-- Amount field -->
                                            <td>
                                                <input type="number" style="border:0px;"
                                                       value="<?= $subcategory['amount_requested'] ?>" required
                                                       readonly>
                                            </td>
                                            <!-- File upload input for attaching receipt -->
                                            <td>
                                                <?php if (!empty($subcategory['receipt_url'])
                                                    && $subcategory['receipt_url'] !== 'resolve'
                                                    && $subcategory['receipt_url'] !== 'no_receipt.jpg'
                                                    && $subcategory['cleared'] !== '3'
                                                ): ?>
                                                    <p> Receipt Submitted</p>
                                                <?php elseif (!empty($subcategory['receipt_url'])
                                                    && ($subcategory['receipt_url'] === 'resolve' || $subcategory['cleared'] === '3')): ?>
                                                    <div class="file-input-container">
                                                        <p>Reupload the Correct Receipt</p>
                                                        <label class="custom-file-input">
                                                            <input type="file"
                                                                   name="<?= $subcategory['item_id'] ?>"
                                                                   class="file-input"/>
                                                            Choose File
                                                        </label>
                                                        <span class="file-label">No file chosen</span>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="file-input-container">
                                                        <label class="custom-file-input">
                                                            <input type="file"
                                                                   name="<?= $subcategory['item_id'] ?>"
                                                                   class="file-input"/>
                                                            Choose File
                                                        </label>
                                                        <span class="file-label">No file chosen</span>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="total-amount">Total Amount To Reconcile:
                        KES <?= number_format($total_amount_requested, 2) ?></div>
                </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<?php init_tail(); ?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const fileInputs = document.querySelectorAll(".file-input");

        fileInputs.forEach((input) => {
            input.addEventListener("change", (e) => {
                const fileName = e.target.files[0] ? e.target.files[0].name : "No file chosen";
                const label = input.closest(".file-input-container")?.querySelector(".file-label");
                if (label) {
                    label.textContent = fileName;
                }
            });
        });

        // Listen for form submission
        document.getElementById('submit-for-reconciliation-form').addEventListener('submit', function () {
            document.getElementById('spanLoader').style.display = 'block';
        });
    });
</script>

