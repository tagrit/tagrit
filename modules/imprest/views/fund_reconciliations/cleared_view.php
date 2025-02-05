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
</style>

<div id="wrapper">
    <div class="content">

        <div style="margin-bottom:20px;">
            <a href="<?php echo admin_url('imprest/fund_requests'); ?>" class="btn btn-primary">
                <i style="margin-right:10px;" class="fa fa-arrow-left"></i>Back
            </a>
        </div>

        <?php echo form_open('admin/imprest/fund_reconciliations/reconcile/' . $fund_request_details['id'], [
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
                            <strong><?= htmlspecialchars($event_details['venue'], ENT_QUOTES, 'UTF-8') ?></strong>
                        </div>
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
                            <strong><?= htmlspecialchars($event_details['facilitator'], ENT_QUOTES, 'UTF-8') ?></strong>
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
                            <strong><?= htmlspecialchars($event_details['delegates'], ENT_QUOTES, 'UTF-8') ?></strong>
                        </div>
                    <?php endif; ?>

                    <p style="margin-top:20px; font-size:15px;"><span style="font-weight: bold;">Requested By :</span>
                        <?= $fund_request_details['requested_by']; ?>
                    </p>
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
                                            <td><?= $subcategory['subcategory_name'] ?></td>
                                            <td><?= $subcategory['amount_requested'] ?></td>
                                            <!-- Receipt link -->
                                            <td>
                                                <?php if (!empty($subcategory['receipt_url'])): ?>
                                                    <a style="color:white; text-decoration: none;"
                                                       href="<?php echo base_url($subcategory['receipt_url']); ?>"
                                                       target="_blank"
                                                       class="receipt-btn">View Receipt</a>
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

                    <div class="total-amount">Total Amount Requested:
                        KES <?= number_format($total_amount_requested, 2) ?></div>
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
                <button type="submit" class="btn btn-primary">
                    <?php echo _l('Reject Fund Request'); ?>
                </button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<?php init_tail(); ?>
