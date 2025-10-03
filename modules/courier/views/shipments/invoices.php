<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php load_courier_styles(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php echo form_open($this->uri->uri_string(), ['id' => 'create-pickup-form']); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div style="margin-bottom:20px;"  class="flex-container">
                            <a style="text-decoration:  none; border:2px solid black;" class="custom-button"
                               href="<?php echo admin_url('courier/shipments/main'); ?>">
                                <i class="fa fa-arrow-left" aria-hidden="true"></i>
                                <span style="margin-left:10px;">Shipment Dashboard</span>
                            </a>
                            <a style="text-decoration:  none; border:2px solid black;" class="custom-button"
                               href="<?php echo admin_url('invoices'); ?>">
                                All Invoices
                            </a>
                        </div>

                        <!-- Shipment -->
                        <table class="table dt-table" id="example">
                            <thead class="table-head">

                            <tr>
                                <th>Invoice #</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($invoices as $invoice): ?>
                            <tr>
                                <td><?php echo $invoice->number; ?></td>
                                <td><?php echo $invoice->total; ?></td>
                                <td><?php echo _d($invoice->date); ?></td>
                                <td><?php echo $invoice->customer; ?></td>
                                <td><?php echo _d($invoice->duedate); ?></td>
                                <td><?php echo format_invoice_status($invoice->status); ?></td>
                                <td class="align-middle">
                                    <div class="d-flex flex-row justify-content-center">
                                        <a href="<?php echo admin_url('invoices/invoice/' . $invoice->id); ?>"
                                           class="text-primary font-weight-bold text-xs mx-2"
                                           data-bs-toggle="tooltip" title="View client">
                                            <i class="fa fa-eye" aria-hidden="true"></i> View
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <!-- Add more rows as needed -->
                            </tbody>
                            <tfoot class="table-footer">
                            <tr>
                                <th>Invoice #</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div
    </div>
    <?php init_tail(); ?>


