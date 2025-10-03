<?php init_head(); ?>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f9;
        color: #333;
        line-height: 1.6;
    }

    .container {
        max-width: 1200px;
        margin: 20px auto;
        padding: 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
        word-wrap: break-word; /* Ensure words wrap */
        word-break: break-word; /* Break long words */
        max-width: 100px; /* Set a reasonable maximum width */
    }

    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f4f4f9;
        color: #333;
        font-weight: bold;
    }

    .receipt-btn {
        padding: 5px 10px;
        font-size: 14px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        text-align: center;
        text-decoration: none;
    }

    .receipt-btn:hover {
        background-color: #0056b3;
        color: white;
    }

    .total-amount {
        font-weight: bold;
        font-size: 13px;
        text-align: right;
        margin-top: 20px;
        color: green;
    }

    .approve-btn,
    .deny-btn {
        padding: 10px 20px;
        font-size: 16px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        color: white;
    }

    .approve-btn {
        background-color: #28a745;
    }

    .deny-btn {
        background-color: #dc3545;
    }

    .approve-btn:hover {
        background-color: #218838;
    }

    .deny-btn:hover {
        background-color: #c82333;
    }

    .label-td {
        font-weight: bold;
        color: black;
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

        <?php echo form_open('admin/imprest/fund_reconciliations/clear/' . $fund_request_details['id'], [
            'id' => 'reconcile-fund-request-form',
            'enctype' => 'multipart/form-data'
        ]); ?>
        <div style="background-color:transparent;" class="panel_s">
            <div style="display: flex; padding: 20px; justify-content: space-between;">
                <div style="max-width: 50%;" class="event-summary">
                    <h4 style="font-weight: bold;">Event Summary</h4>
                    <div class="event-row"><span style="font-weight: bold;">Event:</span>
                        <strong><?= htmlspecialchars($event_details['event_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                    </div>
                    <?php if (!empty($event_details['start_date']) && $event_details['start_date'] !== "0000-00-00" && !empty($event_details['end_date']) && $event_details['end_date'] !== "0000-00-00"): ?>
                        <div class="event-row"><span style="font-weight: bold;">Date:</span>
                            <strong><?= $event_details['start_date'] ?>
                                - <?= $event_details['end_date'] ?></strong>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($event_details['venue'])): ?>
                        <div class="event-row"><span style="font-weight: bold;">Venue:</span>
                            <strong><?= htmlspecialchars($event_details['venue'], ENT_QUOTES, 'UTF-8') ?></strong></div>
                    <?php endif; ?>

                    <?php if (!empty($event_details['organization'])): ?>
                        <div class="event-row"><span style="font-weight: bold;">Organization:</span>
                            <strong><?= htmlspecialchars($event_details['organization'], ENT_QUOTES, 'UTF-8') ?></strong>
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
                        <div class="event-row"><span style="font-weight: bold;">Facilitator:</span>
                            <strong><?= $event_details['facilitator'] ?></strong>
                        </div>
                    <?php endif; ?>

                    <div class="event-row">
                        <span style="font-weight: bold;">Trainers:</span>
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
                        <div class="event-row"><span>Delegates:</span>
                            <strong><?= htmlspecialchars($event_details['delegates'], ENT_QUOTES, 'UTF-8') ?></strong>
                        </div>
                    <?php endif; ?>

                    <p style="margin-top:20px; font-size:15px;"><span style="font-weight: bold;">Requested By :</span>
                        <?= $fund_request_details['requested_by']; ?>
                    </p>
                </div>
                <div class="action-buttons">
                    <button type="submit" class="approve-btn">Mark as Cleared</button>
                    <button class="deny-btn"
                            data-toggle="modal"
                            data-target="#reject_fund_reconciliation"
                            type="button">Reject
                    </button>
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
                                        <th>Amount Approved (KES)</th>
                                        <th>Receipt</th>
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

                                            <td><?= $subcategory['amount_requested'] ?></td>
                                            <!-- Receipt link -->
                                            <td>
                                                <?php if (!empty($subcategory['receipt_url'])): ?>
                                                    <a style="color:white; text-decoration: none;"
                                                       href="<?php echo base_url($subcategory['receipt_url']); ?>"
                                                       target="_blank"
                                                       class="receipt-btn">View Receipt</a>
                                                    <?php if ($subcategory['cleared'] == 1): ?>
                                                        <span class="badge bg-success">cleared</span>
                                                    <?php elseif ($subcategory['cleared'] == 2): ?>
                                                        <span class="badge bg-success">Rejected</span>
                                                    <?php else: ?>
                                                        <button data-action="reject"
                                                                data-fundrequestitem-id="<?php echo $subcategory['item_id'] ?>"
                                                                class="clear-btn btn btn-sm btn-danger">
                                                            Reject
                                                        </button>
                                                        <button data-action="clear"
                                                                data-fundrequestitem-id="<?php echo $subcategory['item_id'] ?>"
                                                                class="clear-btn btn btn-sm btn-warning">
                                                            Clear
                                                        </button>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="no-receipt">No Receipt</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div style="display:flex; justify-content: right">
                        <table style="width: 50%;" class="total-amount-table">
                            <tr>
                                <td class="label-td">Total Expense Approved:</td>
                                <td class="amount">KES <?= number_format($total_amount_requested, 2) ?></td>
                            </tr>
                            <tr>
                                <td class="label-td">Total Expense Reconciled:</td>
                                <td class="amount">KES <?= number_format($total_amount_cleared, 2) ?></td>
                            </tr>
                            <tr>
                                <td class="label-td">Amount Pending Reconciliation:</td>
                                <td class="amount">
                                    KES <?= number_format($total_amount_requested - $total_amount_cleared, 2) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<div class="modal fade" id="reject_fund_reconciliation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Fund Reconciliation</h4>
            </div>
            <?php echo form_open('admin/imprest/fund_reconciliations/reject/' . $fund_request_details['id'], [
                'id' => 'reject-fund-request-form',
                'enctype' => 'multipart/form-data'
            ]); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <textarea
                                id="rejection_reason"
                                name="rejection_reason"
                                rows="6"
                                class="form-control"
                                placeholder="Enter your reason for not reconciling"
                                required
                                style="width: 100%; font-size: 14px; line-height: 1.5; border: 1px solid #ddd; border-radius: 5px; padding: 10px;"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo _l('close'); ?>
                </button>
                <button type="submit" class="btn btn-primary">
                    <?php echo _l('Reject Reconciliation'); ?>
                </button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    // Listen for form submission
    document.getElementById('reconcile-fund-request-form').addEventListener('submit', function () {
        document.getElementById('spanLoader').style.display = 'block';
    });

    // Prevent form submission when "Clear" button is clicked
    document.querySelectorAll('.clear-btn').forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();

            // Get the subcategory_id from the clicked button
            const fund_request_item_id = this.getAttribute('data-fundrequestitem-id');
            const action = this.getAttribute('data-action');

            if (!fund_request_item_id) {
                console.error('No fund_request_item_id found on the clicked button.');
                return;
            }

            let originalText = this.innerHTML; // Store original button text
            this.innerHTML = "Processing..."; // Change text to Processing...
            this.disabled = true; // Disable button

            // Clear specific item for a fund request
            $.ajax({
                url: '<?php echo base_url("imprest/fund_reconciliations/clear_item"); ?>',
                type: "POST",
                data: {
                    fund_request_item_id: fund_request_item_id,
                    item_action: action,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Ensure your meta tag exists
                },
                dataType: "json",
                success: function (data) {
                    console.log(data);
                    if (data.success) {
                        alert_float('success', data.message);

                        setTimeout(function () {
                            button.disabled = false;
                            location.reload();
                        }, 3000);

                    } else {
                        alert_float('danger', 'Failed to clear the item. Please try again.');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    console.error('Response Text:', xhr.responseText);
                }
            });
        });
    });


</script>
