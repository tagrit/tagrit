<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<!-- Tailwind CSS CDN -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

<div id="wrapper" class="min-h-screen bg-gray-100 p-6">

    <?php

    $pending_approval_count = 0;
    $rejected_count = 0;
    $approved_count = 0;
    $waiting_reconciliation_count = 0;
    $reconciled_ongoing_count = 0;
    $reconciliation_rejected_count = 0;
    $cleared_count = 0;

    foreach ($fund_requests as $fund_request) {
        if ($fund_request->status === 'pending_approval' || $fund_request->status === 'rejected') {
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

        if ($fund_request->status === 'reconciliation_ongoing') {
            $reconciled_ongoing_count++;
        }

        if ($fund_request->status === 'reconciliation_rejected') {
            $reconciliation_rejected_count++;
        }


        if ($fund_request->status === 'cleared') {
            $cleared_count++;
        }

    }
    ?>

    <!-- Additional Sections Based on Module Overview -->
    <div class="container mx-auto py-5 mb-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Budget Overview for Staff -->
            <div class="bg-white p-6 rounded-lg shadow-md staff-only">
                <h3 class="text-2xl font-semibold text-gray-700 mb-4">Fund Requests</h3>
                <div class="grid grid-cols-1 sm:grid-cols-4 lg:grid-cols-3 gap-6">
                    <a href="<?php echo admin_url('imprest/fund_requests?tab=fundRequests'); ?>" class="block">
                        <div class="bg-blue-500 text-white p-6 rounded-lg shadow-md hover:bg-blue-600 transition">
                            <h3 class="text-xl font-medium">Waiting Approval</h3>
                            <p class="text-3xl font-bold mt-2"><?php echo $pending_approval_count; ?></p>
                        </div>
                    </a>

                    <a href="<?php echo admin_url('imprest/fund_requests?tab=approvedRequests'); ?>" class="block">
                        <div style="height: 150px;"
                             class="bg-green-500 text-white p-6 rounded-lg shadow-md hover:bg-green-600 transition">
                            <h3 class="text-xl font-medium">Approved</h3>
                            <p class="text-3xl font-bold mt-2"><?php echo $approved_count; ?></p>
                        </div>
                    </a>

                    <a href="<?php echo admin_url('imprest/fund_requests?tab=rejectedFundRequests'); ?>" class="block">
                        <div style="height: 150px;"
                             class="bg-yellow-500 text-white p-6 rounded-lg shadow-md hover:bg-yellow-600 transition">
                            <h3 class="text-xl font-medium">Rejected</h3>
                            <p class="text-3xl font-bold mt-2"><?php echo $rejected_count; ?></p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Reconciliation Status for Admin -->
            <div class="bg-white p-6 rounded-lg shadow-md admin-only">
                <h3 class="text-2xl font-semibold text-gray-700 mb-4">Fund Reconciliation</h3>
                <div class="grid grid-cols-1 sm:grid-cols-4 lg:grid-cols-3 gap-6">
                    <a href="<?php echo admin_url('imprest/fund_requests?tab=pendingReconciliations'); ?>"
                       class="block">
                        <div class="bg-blue-500 text-white p-6 rounded-lg shadow-md hover:bg-blue-600 transition">
                            <h3 class="text-xl font-medium">Waiting Reconciliation</h3>
                            <p class="text-3xl font-bold mt-2"><?php echo $waiting_reconciliation_count; ?></p>
                        </div>
                    </a>

                    <a href="<?php echo admin_url('imprest/fund_requests?tab=reconciliationOngoing'); ?>"
                       class="block">
                        <div class="bg-red-500 text-white p-6 rounded-lg shadow-md hover:bg-red-600 transition">
                            <h3 class="text-xl font-medium">Reconciliation Ongoing</h3>
                            <p class="text-3xl font-bold mt-2"><?php echo $reconciled_ongoing_count; ?></p>
                        </div>
                    </a>

                    <a href="<?php echo admin_url('imprest/fund_requests?tab=rejectedReceipts'); ?>" class="block">
                        <div class="bg-yellow-500 text-white p-6 rounded-lg shadow-md hover:bg-yellow-600 transition">
                            <h3 class="text-xl font-medium">Rejected Reconciliations</h3>
                            <p class="text-3xl font-bold mt-2"><?php echo $reconciliation_rejected_count; ?></p>
                        </div>
                    </a>

                    <a href="<?php echo admin_url('imprest/fund_requests?tab=cleared'); ?>" class="block">
                        <div style="height: 150px;"
                             class="bg-green-500 text-white p-6 rounded-lg shadow-md hover:bg-green-600 transition">
                            <h3 class="text-xl font-medium">Cleared</h3>
                            <p class="text-3xl font-bold mt-2"><?php echo $cleared_count; ?></p>
                        </div>
                    </a>

                </div>
            </div>
        </div>
    </div>

    <!-- Graphs Section -->
    <div class="container mx-auto mb-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-2xl font-semibold text-gray-700 mb-4">Category Expenses</h3>
                <canvas id="ticketTypesChart"></canvas>
            </div>
            <div style="max-height:200px;"
                 class="bg-white p-6 rounded-lg shadow-lg transform hover:scale-105 transition-all duration-300 ease-in-out">
                <?php if (!empty($totalAmountRequested)): ?>
                    <!-- Balance Section -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center space-x-2">
                                <span class="text-xl font-medium text-gray-600">Balance:</span>
                                <span class="text-3xl font-bold text-green-500">KES</span>
                                <span class="text-3xl font-bold text-green-500" id="balance-amount">
                    <?= number_format($totalAmountRequested - $totalAmountCleared, 2) ?>
                </span>
                            </div>
                        </div>
                    </div>
                    <!-- Additional Details Section -->
                    <div class="mt-6 space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">Total Expenses Approved:</span>
                            <span class="font-bold text-gray-700" id="total-expenses">
                KES <?= number_format($totalAmountRequested, 2) ?>
            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">Total Reconciled:</span>
                            <span class="font-bold text-gray-700" id="total-reconciled">
                KES <?= number_format($totalAmountCleared, 2) ?>
            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">Pending Reconciliation:</span>
                            <span class="font-bold text-gray-700 text-red-500" id="pending-reconciliation">
                KES <?= number_format($totalAmountRequested - $totalAmountCleared, 2) ?>
            </span>
                        </div>
                    </div>
                <?php else: ?>
                    <p style="text-align: center;">No Data...</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- External JavaScript for Charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

    // Pass PHP variables to JavaScript
    const labels = <?php echo json_encode($labels); ?>;
    const values = <?php echo json_encode($values); ?>;

    // Ticket Types Chart
    const ticketTypesCtx = document.getElementById('ticketTypesChart').getContext('2d');
    new Chart(ticketTypesCtx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'],
                hoverOffset: 4
            }]
        }
    });
</script>

<?php init_tail(); ?>
