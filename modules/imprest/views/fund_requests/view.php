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

    td input[type="number"] {
        width: 100%;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 5px;
        box-sizing: border-box;
        font-size: 14px;
        font-family: Arial, sans-serif;
        color: #333;
        background-color: #fff;
        transition: all 0.3s ease;
    }

    td input[type="text"]:focus {
        border-color: #28a745;
        box-shadow: 0 0 5px rgba(40, 167, 69, 0.5);
        outline: none;
    }

    .total-amount {
        font-weight: bold;
        font-size: 18px;
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

        <?php echo form_open('admin/imprest/fund_requests/approve/' . $fund_request_details['id'], [
            'id' => 'approve-fund-request-form',
            'enctype' => 'multipart/form-data'
        ]); ?>
        <div style="background-color:transparent;" class="panel_s">
            <div style="display: flex; padding: 20px; justify-content: space-between;">
                <div style="max-width: 50%;" class="event-summary">
                    <h4 style="font-weight: bold; ">Event Summary</h4>
                    <div class="event-row"><span style="font-weight: bold;">Event:</span>
                        <strong><?= $event_details['event_name'] ?></strong>
                    </div>
                    <?php if (!empty($event_details['start_date']) && $event_details['start_date'] !== "0000-00-00" && !empty($event_details['end_date']) && $event_details['end_date'] !== "0000-00-00"): ?>
                        <div class="event-row"><span style="font-weight: bold;">Date:</span>
                            <strong><?= $event_details['start_date'] ?>
                                - <?= $event_details['end_date'] ?></strong></div>
                    <?php endif; ?>

                    <?php if (!empty($event_details['venue'])): ?>
                        <div class="event-row"><span style="font-weight: bold;">Venue:</span>
                            <strong><?= $event_details['venue'] ?></strong></div>
                    <?php endif; ?>

                    <?php if (!empty($event_details['organization'])): ?>
                        <div class="event-row"><span style="font-weight: bold;">Organization:</span>
                            <strong><?= $event_details['organization'] ?></strong></div>
                    <?php endif; ?>

                    <?php if (!empty($event_details['facilitator'])): ?>
                        <div class="event-row"><span style="font-weight: bold;">Facilitator:</span>
                            <strong><?= $event_details['facilitator'] ?></strong>
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
                        <div class="event-row"><span style="font-weight: bold;">Delegates:</span>
                            <strong><?= $event_details['delegates'] ?></strong>
                        </div>
                    <?php endif; ?>

                    <p style="margin-top:20px; font-size:15px;"><span style="font-weight: bold;">Requested By :</span>
                        <?= $fund_request_details['requested_by']; ?>
                    </p>
                </div>
                <div class="action-buttons">
                    <?php if (staff_can('approve_fund_requests', 'impress-fund-requests') && $fund_request_details['status'] !== 'rejected' && ($fund_request_details['status'] !== 'approved')): ?>
                        <button type="submit" class="approve-btn">
                            <i style="margin-right:10px;" class="fa fa-check"></i>Approve
                        </button>
                        <button class="deny-btn"
                                data-toggle="modal"
                                data-target="#reject_fund_request"
                                type="button">
                            <i style="margin-right:10px;" class="fa fa-times"></i> Reject
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
                                        <th>Amount ($)</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($subcategories as $index => $subcategory): ?>
                                        <tr>
                                            <!-- Subcategory name as a hidden input -->
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
                                                <?php elseif ($categoryName === 'Speaker Costs'): ?>
                                                    <div>
                                                        <h4 style="font-weight: bold; ">Details:</h4>
                                                        <div class="event-row"><span>Speaker Name:</span>
                                                            <strong><?= $speaker_details['speaker_name'] ?></strong>
                                                        </div>
                                                        <div class="event-row"><span>Rate Per Day:</span>
                                                            <strong><?= $speaker_details['rate_per_day'] ?></strong>
                                                        </div>
                                                        <div class="event-row"><span>Number of Days:</span>
                                                            <strong><?= $speaker_details['number_of_days'] ?></strong>
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
                                                <input type="number"
                                                       name="<?= $index . '-' . $subcategory['subcategory_id'] ?>"
                                                       value="<?= $subcategory['amount_requested'] ?>" required
                                                    <?php echo !staff_can('approve_fund_requests', 'impress-fund-requests') || ($fund_request_details['status'] === 'rejected') || ($fund_request_details['status'] === 'approved') ? 'readonly' : ''; ?>
                                                >
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="total-amount">Total Amount Requested:
                        KES <?php echo $total_amount_requested ?></div>
                </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<div class="modal fade" id="reject_fund_request" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Fund Request Rejection</h4>
            </div>
            <?php echo form_open('admin/imprest/fund_requests/reject/' . $fund_request_details['id'], [
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
                                placeholder="Enter your reason for rejecting this request"
                                required
                                style="width: 100%; font-size: 14px; line-height: 1.5; border: 1px solid #ddd; border-radius: 5px; padding: 10px;"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo _l('close'); ?>
                </button>
                <button id="reject_btn" type="submit" class="btn btn-primary">
                    <?php echo _l('Reject Fund Request'); ?>
                </button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Select the deny button
        const denyButton = document.querySelector('.deny-btn');

        if (denyButton) {
            // Add a click event listener
            denyButton.addEventListener('click', function (event) {
                    // Prevent any default action, including form submission
                    event.preventDefault();
                    event.stopPropagation();

                    $('#reject_fund_request').modal('show'); // Use Bootstrap's modal show method
                }
            );
        }

        // Listen for form submission
        document.getElementById('approve-fund-request-form').addEventListener('submit', function () {
            document.getElementById('spanLoader').style.display = 'block';
        });


        document.getElementById('reject_fund_request').addEventListener('submit', function () {
            const submitButton = document.getElementById('reject_btn');
            submitButton.disabled = true;
            submitButton.innerHTML = 'Processing...';
        });

    })
</script>

