<?php init_head(); ?>
<style>

    /* Tab styles */
    .tabs-container {
        margin-top: -15px;
        width: 100%;
        background: white;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        overflow: hidden;
    }

    .tabs-header {
        display: flex;
        justify-content: space-around;
        background: #007bff;
        color: white;
        border-bottom: 2px solid #0056b3;
    }

    .tab {
        max-height: 50px;
        font-weight: bold;
        padding: 10px 5px;
        flex: 3;
        text-align: center;
        cursor: pointer;
        transition: background 0.3s;
    }

    .tab:hover {
        background: #0056b3;
    }

    .tab.active {
        background: #0056b3;
        font-weight: bold;
        padding: 10px 5px;
    }

    .tab-content {
        padding: 10px;
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    /* Table Styling */
    .table {
        width: 100%;
        margin-top: 20px;
        border-collapse: collapse;
    }

    .table th, .table td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: left;
    }

    .table th {
        background-color: #f8f9fa;
    }

</style>

<div id="wrapper">
    <div class="content">
        <div class="row">

            <?php if (!is_admin() || !staff_can('approve_fund_requests', 'impress-fund-requests')): ?>
                <div id="fund-request-btn" style="
             position: fixed;
             bottom: 20px;
             right: 20px;
             z-index: 1000;">
                    <a href="<?php echo admin_url('imprest/fund_requests/create'); ?>" class="btn btn-success btn-lg"
                       title="Request Fund">
                        Request Fund <i class="fa fa-hand-holding"></i>
                    </a>
                </div>
            <?php endif; ?>

            <?php

            $pending_approval_count = 0;
            $rejected_count = 0;
            $rejected_reconciliation = 0;
            $approved_count = 0;
            $waiting_reconciliation_count = 0;
            $reconciled_ongoing_count = 0;
            $cleared_count = 0;

            foreach ($fund_requests as $fund_request) {

                if ($fund_request->status === 'pending_approval') {
                    $pending_approval_count++;
                }

                if ($fund_request->status === 'rejected') {
                    $rejected_count++;
                }

                if ($fund_request->status === 'approved') {
                    $approved_count++;
                }

                if ($fund_request->status === 'pending_reconciliation') {
                    $waiting_reconciliation_count++;
                }

                if ($fund_request->status === 'reconciliation_rejected') {
                    $rejected_reconciliation++;
                }

                if ($fund_request->status === 'reconciliation_ongoing') {
                    $reconciled_ongoing_count++;
                }


                if ($fund_request->status === 'cleared') {
                    $cleared_count++;
                }

            }
            ?>
            <div class="tabs-container">
                <div class="tabs-header">
                    <div style="border-right:2px solid white;" class="tab active" onclick="showTab('fundRequests')">
                        <p style="font-size:12px;"> Awaiting Review <span
                                    style="font-weight:bold; color:orange;"><?php echo $pending_approval_count ?></span>
                        </p>
                    </div>
                    <div style="border-right:2px solid white;" class="tab" onclick="showTab('rejectedFundRequests')">
                        <p style="font-size:12px;">Rejected <span
                                    style="font-weight:bold; color:orange;"><?php echo $rejected_count ?></span>
                        </p>
                    </div>
                    <div style="border-right:2px solid white;" class="tab" onclick="showTab('approvedRequests')">
                        <p style="font-size:12px;">Approved <span
                                    style="font-weight:bold; color:orange;"><?php echo $approved_count ?></span>
                        </p>
                    </div>
                    <div style="border-right:2px solid white;" class="tab" onclick="showTab('pendingReconciliations')">
                        <p style="font-size:12px;">Awaiting <br>Reconciliation <span
                                    style="font-weight:bold; color:orange;"><?php echo $waiting_reconciliation_count ?></span>
                        </p>
                    </div>
                    <div style="padding-bottom:5px; border-right:2px solid white;" class="tab"
                         onclick="showTab('rejectedReceipts')">
                        <p style="font-size:12px;">Rejected<br>Reconciliations<span
                                    style="font-weight:bold; color:orange;"> <?php echo $rejected_reconciliation ?></span>
                        </p>
                    </div>
                    <div style="border-right:2px solid white;" class="tab" onclick="showTab('reconciliationOngoing')">
                        <p style="font-size:12px;">Reconciliation <br>Ongoing <span
                                    style="font-weight:bold; color:orange;"><?php echo $reconciled_ongoing_count ?></span>
                        </p>
                    </div>
                    <div class="tab" onclick="showTab('cleared')">
                        <p style="font-size:12px;">Cleared <span
                                    style="font-weight:bold; color:orange;"><?php echo $cleared_count ?></span></p>
                    </div>
                </div>
            </div>

            <!-- Tab Content for Fund Requests -->
            <div class="tab-content active" id="fundRequests">
                <?php

                $filtered_requests = array_filter($fund_requests, function ($request) {
                    return $request->status === 'pending_approval';
                });

                if (!empty($filtered_requests)):
                    ?>

                    <table class="table dt-table" id="awaitingReviewsFundRequestsTable">
                        <thead class="table-head">
                        <tr>
                            <th>Reference No</th>
                            <th>Event</th>
                            <th>Funds Requested</th>
                            <th>Requested By</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($fund_requests as $fund_request): ?>
                            <?php if ($fund_request->status === 'pending_approval'): ?>
                                <tr>
                                    <td style="font-weight: bold; color: #007bff; font-size: 13px;">
                                        <?php echo $fund_request->reference_no; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column justify-content-center">
                                            <p style="font-weight: bold; font-size: 14px;">
                                                <?php echo $fund_request->event_name; ?>
                                            </p>
                                            <?php if (!empty($fund_request->organization)): ?>
                                                <p style="font-style: italic; font-size: 14px;">
                                                    <?php echo $fund_request->organization; ?>
                                                </p>
                                            <?php endif; ?>
                                            <?php if (!empty($fund_request->start_date) && $fund_request->start_date !== "0000-00-00" && !empty($fund_request->end_date) && $fund_request->end_date !== "0000-00-00"): ?>
                                                <p class="text-secondary mb-0">
                                                    <?php
                                                    $start_date = date('jS M Y', strtotime($fund_request->start_date));
                                                    $end_date = date('jS M Y', strtotime($fund_request->end_date));
                                                    echo $start_date . ' - ' . $end_date;
                                                    ?>
                                                </p>
                                            <?php endif; ?>

                                            <?php if (!empty($fund_request->venue)): ?>
                                                <p style="color:#007BFF; font-weight: bold;"
                                                   class="text-secondary mb-0">
                                                    <?php echo $fund_request->venue; ?>
                                                </p>
                                            <?php endif; ?>

                                        </div>

                                    </td>
                                    <td>KES <?php
                                        echo number_format($fund_request->total_requested, 2);
                                        ?> <br>
                                        <?php if ($fund_request->additional_fund_request == "1"): ?>
                                            <span class="badge bg-danger ml-2">Additional Funds</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo $fund_request->requested_by; ?>
                                    </td>
                                    <td>
                                        <?php if ($fund_request->status === 'pending_approval'): ?>
                                            <span class="badge bg-warning">Pending Approval</span>
                                        <?php elseif ($fund_request->status === 'rejected'): ?>
                                            <span class="badge bg-danger">Rejected</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (staff_can('approve_fund_requests', 'impress-fund-requests')): ?>
                                            <a style="color:white;"
                                               href="<?php echo admin_url('imprest/fund_requests/view/' . $fund_request->fund_request_id); ?>"
                                               class="btn btn-info">
                                                <i class="fa fa-eye"></i> Review
                                            </a>
                                        <?php else: ?>
                                            <a style="color:white;"
                                               href="<?php echo admin_url('imprest/fund_requests/view/' . $fund_request->fund_request_id); ?>"
                                               class="btn btn-info">
                                                <i class="fa fa-eye"></i> View
                                            </a>
                                        <?php endif ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p style="text-align: center; font-weight: bold; font-size:15px;">
                        No fund requests found.
                    </p>
                <?php endif; ?>
            </div>

            <div class="tab-content" id="rejectedFundRequests">

                <?php
                $filtered_requests = array_filter($fund_requests, function ($request) {
                    return $request->status === 'rejected';
                });

                if (!empty($filtered_requests)):
                    ?>
                    <table class="table dt-table" id="rejectedFundRequestsTable">
                        <thead class="table-head">
                        <tr>
                            <th>Reference No</th>
                            <th>Event</th>
                            <th>Funds Requested</th>
                            <th>Requested By</th>
                            <th>Status</th>
                            <th>Comments</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($fund_requests as $fund_request): ?>
                            <?php if ($fund_request->status === 'rejected'): ?>
                                <tr>
                                    <td style="font-weight: bold; color: #007bff; font-size: 13px;">
                                        <?php echo $fund_request->reference_no; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column justify-content-center">
                                            <p style="font-weight: bold; font-size: 14px;">
                                                <?php echo $fund_request->event_name; ?>
                                            </p>
                                            <?php if (!empty($fund_request->organization)): ?>
                                                <p style="font-style: italic; font-size: 14px;">
                                                    <?php echo $fund_request->organization; ?>
                                                </p>
                                            <?php endif; ?>
                                            <?php if (!empty($fund_request->start_date) && $fund_request->start_date !== "0000-00-00" && !empty($fund_request->end_date) && $fund_request->end_date !== "0000-00-00"): ?>
                                                <p class="text-secondary mb-0">
                                                    <?php
                                                    $start_date = date('jS M Y', strtotime($fund_request->start_date));
                                                    $end_date = date('jS M Y', strtotime($fund_request->end_date));
                                                    echo $start_date . ' - ' . $end_date;
                                                    ?>
                                                </p>
                                            <?php endif; ?>

                                            <?php if (!empty($fund_request->venue)): ?>
                                                <p style="color:#007BFF; font-weight: bold;"
                                                   class="text-secondary mb-0">
                                                    <?php echo $fund_request->venue; ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>

                                    </td>
                                    <td>KES <?php echo number_format($fund_request->total_requested, 2); ?>
                                        <br>
                                        <?php if ($fund_request->additional_fund_request == "1"): ?>
                                            <span style="color:white;"
                                                  class="badge bg-danger ml-2">Additional Funds</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo $fund_request->requested_by; ?>
                                    </td>
                                    <td>
                                        <?php if ($fund_request->status === 'rejected'): ?>
                                            <span class="badge bg-danger">Rejected</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo $fund_request->rejection_reason; ?>
                                    </td>
                                    <td>
                                        <a style="color:white;"
                                           href="<?php echo admin_url('imprest/fund_requests/view/' . $fund_request->fund_request_id); ?>"
                                           class="btn btn-info">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p style="text-align: center; font-weight: bold; font-size:15px;">
                        No fund requests found.
                    </p>
                <?php endif; ?>
            </div>

            <div style="padding-top:20px;" class="tab-content" id="approvedRequests">
                <?php
                $filtered_requests = array_filter($fund_requests, function ($request) {
                    return $request->status === 'approved';
                });

                if (!empty($filtered_requests)):
                    ?>
                    <table class="table dt-table" id="approvedFundRequestsTable">
                        <thead class="table-head">
                        <tr>
                            <th>Reference No</th>
                            <th>Event</th>
                            <th>Funds Approved</th>
                            <th>Requested By</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($fund_requests as $fund_request): ?>

                            <?php if ($fund_request->status === 'approved'): ?>
                                <tr>
                                    <td style="font-weight: bold; color: #007bff; font-size: 13px;">
                                        <?php echo $fund_request->reference_no; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column justify-content-center">
                                            <p style="font-weight: bold; font-size: 14px;">
                                                <?php echo $fund_request->event_name; ?>
                                            </p>
                                            <?php if (!empty($fund_request->organization)): ?>
                                                <p style="font-style: italic; font-size: 14px;">
                                                    <?php echo $fund_request->organization; ?>
                                                </p>
                                            <?php endif; ?>
                                            <?php if (!empty($fund_request->start_date) && $fund_request->start_date !== "0000-00-00" && !empty($fund_request->end_date) && $fund_request->end_date !== "0000-00-00"): ?>
                                                <p class="text-secondary mb-0">
                                                    <?php
                                                    $start_date = date('jS M Y', strtotime($fund_request->start_date));
                                                    $end_date = date('jS M Y', strtotime($fund_request->end_date));
                                                    echo $start_date . ' - ' . $end_date;
                                                    ?>
                                                </p>
                                            <?php endif; ?>

                                            <?php if (!empty($fund_request->venue)): ?>
                                                <p style="color:#007BFF; font-weight: bold;"
                                                   class="text-secondary mb-0">
                                                    <?php echo $fund_request->venue; ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        KES <?php echo number_format($fund_request->total_requested, 2); ?>
                                    </td>
                                    <td>
                                        <?php echo $fund_request->requested_by; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">Approved</span>
                                    </td>
                                    <td class="align-left">
                                        <div class="d-flex flex-row justify-content-center">
                                            <?php if (!staff_can('approve_fund_requests', 'impress-fund-requests')): ?>
                                                <a style="color:white; margin-bottom:5px;"
                                                   href="<?php echo admin_url('imprest/fund_reconciliations/edit/' . $fund_request->fund_request_id) ?>"
                                                   class="btn btn-success">
                                                    <i class="fa fa-paperclip" aria-hidden="true"></i> Attach Receipts
                                                </a>
                                                <?php if ($fund_request->additional_fund_request == '0'): ?>
                                                    <a style="color:white;"
                                                       class="btn  btn-primary request-additional-fund"
                                                       id="<?php echo $fund_request->fund_request_id; ?>"
                                                       event_name="<?php echo $fund_request->event_name; ?>"
                                                       data-toggle="modal"
                                                       data-target="#request_additional_funds">
                                                        <i class="fa fa-plus"></i> Additional Funds
                                                    </a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <a style="color:white;"
                                                   href="<?php echo admin_url('imprest/fund_requests/view/' . $fund_request->fund_request_id); ?>"
                                                   class="btn btn-info">
                                                    <i class="fa fa-eye"></i> View
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p style="text-align: center; font-weight: bold; font-size:15px;">
                        No fund requests found.
                    </p>
                <?php endif; ?>
            </div>

            <div style="padding-top:20px;" class="tab-content" id="rejectedReceipts">
                <?php
                $filtered_requests = array_filter($fund_requests, function ($request) {
                    return $request->status === 'reconciliation_rejected';
                });

                if (!empty($filtered_requests)):
                    ?>
                    <table class="table dt-table" id="rejectedReconciliationFundrequestsTable">
                        <thead class="table-head">
                        <tr>
                            <th>Reference No</th>
                            <th>Event</th>
                            <th>Funds Requested</th>
                            <th>Requested By</th>
                            <th>Status</th>
                            <th>Reason</th>
                            <?php if (!staff_can('clear_fund_reconciliations', 'impress-fund-reconciliations')): ?>
                                <th>Action</th>
                            <?php endif; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($fund_requests as $fund_request): ?>

                            <?php if ($fund_request->status === 'reconciliation_rejected'): ?>
                                <tr>
                                    <td style="font-weight: bold; color: #007bff; font-size: 13px;">
                                        <?php echo $fund_request->reference_no; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column justify-content-center">
                                            <p style="font-weight: bold; font-size: 14px;">
                                                <?php echo $fund_request->event_name; ?>
                                            </p>
                                            <?php if (!empty($fund_request->organization)): ?>
                                                <p style="font-style: italic; font-size: 14px;">
                                                    <?php echo $fund_request->organization; ?>
                                                </p>
                                            <?php endif; ?>
                                            <?php if (!empty($fund_request->start_date) && $fund_request->start_date !== "0000-00-00" && !empty($fund_request->end_date) && $fund_request->end_date !== "0000-00-00"): ?>
                                                <p class="text-secondary mb-0">
                                                    <?php
                                                    $start_date = date('jS M Y', strtotime($fund_request->start_date));
                                                    $end_date = date('jS M Y', strtotime($fund_request->end_date));
                                                    echo $start_date . ' - ' . $end_date;
                                                    ?>
                                                </p>
                                            <?php endif; ?>

                                            <?php if (!empty($fund_request->venue)): ?>
                                                <p style="color:#007BFF; font-weight: bold;"
                                                   class="text-secondary mb-0">
                                                    <?php echo $fund_request->venue; ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        KES <?php echo number_format($fund_request->total_requested, 2); ?>
                                    </td>
                                    <td>
                                        <?php echo $fund_request->requested_by; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger">Rejected</span>
                                    </td>
                                    <td>
                                        <?php echo $fund_request->rejection_reason; ?>
                                    </td>
                                    <?php if (!staff_can('clear_fund_reconciliations', 'impress-fund-reconciliations')): ?>
                                        <td>
                                            <a style="color:white;"
                                               href="<?php echo admin_url('imprest/fund_reconciliations/edit/' . $fund_request->fund_request_id); ?>"
                                               class="btn btn-info">
                                                <i class="fa fa-info-circle"></i>
                                                Resolve
                                            </a>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p style="text-align: center; font-weight: bold; font-size:15px;">
                        No fund requests found.
                    </p>
                <?php endif; ?>
            </div>

            <div style="padding-top:20px;" class="tab-content" id="pendingReconciliations">

                <?php
                $filtered_requests = array_filter($fund_requests, function ($request) {
                    return $request->status === 'pending_reconciliation';
                });

                if (!empty($filtered_requests)):
                    ?>
                    <table class="table dt-table" id="pendingReconciliationFundrequestsTable">
                        <thead class="table-head">
                        <tr>
                            <th>Reference No</th>
                            <th>Event</th>
                            <th>Funds Requested</th>
                            <th>Requested By</th>
                            <th>Status</th>
                            <?php if (staff_can('clear_fund_reconciliations', 'impress-fund-reconciliations')): ?>
                                <th>Action</th>
                            <?php endif; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($fund_requests as $fund_request): ?>
                            <?php if ($fund_request->status === 'pending_reconciliation'): ?>
                                <tr>
                                    <td style="font-weight: bold; color: #007bff; font-size: 13px;">
                                        <?php echo $fund_request->reference_no; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column justify-content-center">
                                            <p style="font-weight: bold; font-size: 14px;">
                                                <?php echo $fund_request->event_name; ?>
                                            </p>
                                            <?php if (!empty($fund_request->organization)): ?>
                                                <p style="font-style: italic; font-size: 14px;">
                                                    <?php echo $fund_request->organization; ?>
                                                </p>
                                            <?php endif; ?>
                                            <?php if (!empty($fund_request->start_date) && $fund_request->start_date !== "0000-00-00" && !empty($fund_request->end_date) && $fund_request->end_date !== "0000-00-00"): ?>
                                                <p class="text-secondary mb-0">
                                                    <?php
                                                    $start_date = date('jS M Y', strtotime($fund_request->start_date));
                                                    $end_date = date('jS M Y', strtotime($fund_request->end_date));
                                                    echo $start_date . ' - ' . $end_date;
                                                    ?>
                                                </p>
                                            <?php endif; ?>

                                            <?php if (!empty($fund_request->venue)): ?>
                                                <p style="color:#007BFF; font-weight: bold;"
                                                   class="text-secondary mb-0">
                                                    <?php echo $fund_request->venue; ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        KES <?php echo number_format($fund_request->total_requested, 2); ?>
                                    </td>
                                    <td>
                                        <?php echo $fund_request->requested_by; ?>
                                    </td>
                                    <td>
                                        <?php if ($fund_request->status === 'pending_reconciliation'): ?>
                                            <span class="badge bg-warning">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <?php if (staff_can('clear_fund_reconciliations', 'impress-fund-reconciliations')): ?>
                                        <td>
                                            <a style="color:white;"
                                               href="<?php echo admin_url('imprest/fund_reconciliations/view/' . $fund_request->fund_request_id); ?>"
                                               class="btn btn-info">
                                                <i class="fa fa-sync"></i>
                                                Start Reconciliation
                                            </a>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p style="text-align: center; font-weight: bold; font-size:15px;">
                        No fund requests found.
                    </p>
                <?php endif; ?>
            </div>

            <div class="tab-content" id="reconciliationOngoing">
                <?php
                $filtered_requests = array_filter($fund_requests, function ($request) {
                    return $request->status === 'reconciliation_ongoing';
                });

                if (!empty($filtered_requests)):
                    ?>
                    <table class="table dt-table" id="reconciliationOngoingFundrequestsTable">
                        <thead class="table-head">
                        <tr>
                            <th>Reference No</th>
                            <th>Event</th>
                            <th>Funds Requested</th>
                            <th>Requested By</th>
                            <th>Status</th>
                            <?php if (staff_can('clear_fund_reconciliations', 'impress-fund-reconciliations')): ?>
                                <th>Action</th>
                            <?php endif; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($fund_requests as $fund_request): ?>

                            <?php if ($fund_request->status === 'reconciliation_ongoing'): ?>
                                <tr>
                                    <td style="font-weight: bold; color: #007bff; font-size: 13px;">
                                        <?php echo $fund_request->reference_no; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column justify-content-center">
                                            <p style="font-weight: bold; font-size: 14px;">
                                                <?php echo $fund_request->event_name; ?>
                                            </p>
                                            <?php if (!empty($fund_request->organization)): ?>
                                                <p style="font-style: italic; font-size: 14px;">
                                                    <?php echo $fund_request->organization; ?>
                                                </p>
                                            <?php endif; ?>
                                            <?php if (!empty($fund_request->start_date) && $fund_request->start_date !== "0000-00-00" && !empty($fund_request->end_date) && $fund_request->end_date !== "0000-00-00"): ?>
                                                <p class="text-secondary mb-0">
                                                    <?php
                                                    $start_date = date('jS M Y', strtotime($fund_request->start_date));
                                                    $end_date = date('jS M Y', strtotime($fund_request->end_date));
                                                    echo $start_date . ' - ' . $end_date;
                                                    ?>
                                                </p>
                                            <?php endif; ?>

                                            <?php if (!empty($fund_request->venue)): ?>
                                                <p style="color:#007BFF; font-weight: bold;"
                                                   class="text-secondary mb-0">
                                                    <?php echo $fund_request->venue; ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        KES <?php echo number_format($fund_request->total_requested, 2); ?>
                                    </td>
                                    <td>
                                        <?php echo $fund_request->requested_by; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">Reconciliation Ongoing</span>
                                    </td>
                                    <?php if (staff_can('clear_fund_reconciliations', 'impress-fund-reconciliations')) : ?>
                                        <td>
                                            <a style="color:white;"
                                               href="<?php echo admin_url('imprest/fund_reconciliations/view/' . $fund_request->fund_request_id); ?>"
                                               class="btn btn-info">
                                                <i class="fa fa-eye"></i> Resume Reconciliation
                                            </a>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p style="text-align: center; font-weight: bold; font-size:15px;">
                        No fund requests found.
                    </p>
                <?php endif; ?>
            </div>

            <div class="tab-content" id="cleared">
                <?php
                $filtered_requests = array_filter($fund_requests, function ($request) {
                    return $request->status === 'cleared';
                });

                if (!empty($filtered_requests)):
                    ?>
                    <table class="table dt-table" id="clearedFundrequestsTable">
                        <thead class="table-head">
                        <tr>
                            <th>Reference No</th>
                            <th>Event</th>
                            <th>Funds Requested</th>
                            <th>Requested By</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($fund_requests as $fund_request): ?>

                            <?php if ($fund_request->status === 'cleared'): ?>
                                <tr>
                                    <td style="font-weight: bold; color: #007bff; font-size: 13px;">
                                        <?php echo $fund_request->reference_no; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column justify-content-center">
                                            <p style="font-weight: bold; font-size: 14px;">
                                                <?php echo $fund_request->event_name; ?>
                                            </p>
                                            <?php if (!empty($fund_request->organization)): ?>
                                                <p style="font-style: italic; font-size: 14px;">
                                                    <?php echo $fund_request->organization; ?>
                                                </p>
                                            <?php endif; ?>
                                            <?php if (!empty($fund_request->start_date) && $fund_request->start_date !== "0000-00-00" && !empty($fund_request->end_date) && $fund_request->end_date !== "0000-00-00"): ?>
                                                <p class="text-secondary mb-0">
                                                    <?php
                                                    $start_date = date('jS M Y', strtotime($fund_request->start_date));
                                                    $end_date = date('jS M Y', strtotime($fund_request->end_date));
                                                    echo $start_date . ' - ' . $end_date;
                                                    ?>
                                                </p>
                                            <?php endif; ?>

                                            <?php if (!empty($fund_request->venue)): ?>
                                                <p style="color:#007BFF; font-weight: bold;"
                                                   class="text-secondary mb-0">
                                                    <?php echo $fund_request->venue; ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        KES <?php echo number_format($fund_request->total_requested, 2); ?>
                                    </td>
                                    <td>
                                        <?php echo $fund_request->requested_by; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">Cleared</span>
                                    </td>
                                    <td>
                                        <a style="color:white;"
                                           href="<?php echo admin_url('imprest/fund_reconciliations/cleared_view/' . $fund_request->fund_request_id); ?>"
                                           class="btn btn-info">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p style="text-align: center; font-weight: bold; font-size:15px;">
                        No fund requests found.
                    </p>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="request_additional_funds" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Request Additional Funds</h4>
            </div>
            <?php echo form_open('admin/imprest/fund_requests/request_additional_funds', [
                'id' => 'request-additional-funds-form',
                'enctype' => 'multipart/form-data'
            ]); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <label for="event_id" class="form-label">Event Name</label>
                        <input type="text" id="event_name" name="event_name" class="form-control"
                               placeholder="Enter Event name" required readonly>
                        <input type="hidden" id="fund_request_id" name="fund_request_id" value="">
                    </div>
                    <div style="margin-top: 20px;" class="col-md-12">

                        <label for="event_id" class="form-label">Amount</label>
                        <input type="text" id="amount" name="amount" class="form-control"
                               placeholder="Enter Amount" required>
                    </div>
                    <div style="margin-top: 20px;" class="col-md-12">
                        <label for="reason" class="form-label">Reason</label>
                        <textarea
                                id="reason"
                                name="reason"
                                rows="6"
                                class="form-control"
                                placeholder="Enter your reason for requesting additional funds"
                                required
                                style="width: 100%; font-size: 14px; line-height: 1.5; border: 1px solid #ddd; border-radius: 5px; padding: 10px;"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo _l('Submit Request'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>


<?php init_tail(); ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab'); // Get the 'tab' parameter from the URL

        if (activeTab) {
            const tabs = document.querySelectorAll('.tab');
            const tabContents = document.querySelectorAll('.tab-content');

            // Remove active class from all tabs and contents
            tabs.forEach(tab => tab.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));

            // Add active class to the target tab and content
            const targetTab = document.querySelector(`.tab[onclick="showTab('${activeTab}')"]`);
            const targetContent = document.getElementById(activeTab);

            if (targetTab && targetContent) {
                targetTab.classList.add('active');
                targetContent.classList.add('active');
            }
        }

        function showTab(tabId) {
            const tabs = document.querySelectorAll('.tab');
            const tabContents = document.querySelectorAll('.tab-content');

            // Remove active class from all tabs and contents
            tabs.forEach(tab => tab.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));

            // Add active class to the clicked tab and content
            document.querySelector(`.tab[onclick="showTab('${tabId}')"]`).classList.add('active');
            document.getElementById(tabId).classList.add('active');
        }


        const modal = document.getElementById('request_additional_funds');
        const fundRequestId = modal.querySelector('#fund_request_id');
        const eventName = modal.querySelector('#event_name');

        document.querySelectorAll('.request-additional-fund').forEach(button => {
            button.addEventListener('click', function () {
                fundRequestId.value = this.getAttribute('id');
                eventName.value = this.getAttribute('event_name');
            });
        });
    });

    function showTab(tabId) {
        // Hide all tabs
        const tabs = document.querySelectorAll('.tab-content');
        tabs.forEach(tab => tab.classList.remove('active'));

        // Remove active class from all tabs in the header
        const tabLinks = document.querySelectorAll('.tab');
        tabLinks.forEach(tab => tab.classList.remove('active'));

        // Show the selected tab
        document.getElementById(tabId).classList.add('active');

        // Add active class to the clicked tab
        const activeTab = document.querySelector(`[onclick="showTab('${tabId}')"]`);
        activeTab.classList.add('active');
    }
</script>
