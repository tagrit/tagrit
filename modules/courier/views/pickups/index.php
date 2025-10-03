<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php load_courier_styles(); ?>
<?php echo '<script src="https://cdn.jsdelivr.net/npm/signature_pad"></script>'; ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div style="margin-bottom:20px;" class="flex-container">
                            <a style="text-decoration: none; border:2px solid black;" class="custom-button"
                               href="<?php echo admin_url('courier/pickups/main'); ?>">
                                <i class="fa fa-arrow-left" aria-hidden="true"></i>
                                <span style="margin-left:10px;">Pickup Dashboard</span>
                            </a>
                            <a style="text-decoration: none; border:2px solid black;" class="custom-button"
                               href="<?php echo admin_url('courier/pickups/create'); ?>">
                                Create Pickup
                            </a>
                        </div>

                        <!-- Check if there is data -->
                        <?php if (!empty($pickups)): ?>
                            <table class="table dt-table" id="example">
                                <thead class="table-head">
                                <tr>
                                    <th>Pickup Time</th>
                                    <th>Vehicle Type</th>
                                    <th>Contact Person</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th>Pickup Signature</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($pickups as $pickup): ?>
                                    <tr>
                                        <td>
                                            <p><?php echo htmlspecialchars($pickup->pickup_date); ?></p>
                                            <p>FROM : <?php echo htmlspecialchars($pickup->pickup_start_time); ?></p>
                                            <p>TO : <?php echo htmlspecialchars($pickup->pickup_end_time); ?></p>
                                        </td>
                                        <td><?php echo htmlspecialchars($pickup->vehicle_type); ?></td>
                                        <td>
                                            <div class="d-flex flex-column justify-content-center">
                                                <p style="font-weight: bold; font-size:14px;"><?php echo htmlspecialchars($pickup->contact_first_name) . ' ' . htmlspecialchars($pickup->contact_last_name); ?></p>
                                                <p class="text-secondary mb-0"><?php echo htmlspecialchars($pickup->contact_email); ?></p>
                                                <p class="text-secondary mb-0"><?php echo htmlspecialchars($pickup->contact_phone_number); ?></p>
                                            </div>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($pickup->address) . ',' . $pickup->pickup_zip; ?>
                                        </td>
                                        <td>
                                            <?php if ($pickup->status === 'picked_up'): ?>
                                                <span class="badge badge-pill bg-info"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $pickup->status))); ?></span>
                                            <?php else: ?>
                                                <span class="badge badge-pill bg-success"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $pickup->status))); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($pickup->signature_url)): ?>
                                                <img
                                                        width="60"
                                                        height="60"
                                                        src="<?php echo base_url('modules/courier/' . $pickup->signature_url) ?>"
                                                        alt="signature">
                                            <?php else: ?>
                                                N/A
                                            <?php endif; ?>
                                        </td>
                                        <td class="align-left">
                                            <div class="d-flex flex-row justify-content-center">
                                                <a style="margin-right:6px;" href="#"
                                                   data-toggle="modal"
                                                   data-target="#update_status"
                                                   data-id="<?php echo $pickup->id; ?>"
                                                   data-status="<?php echo htmlspecialchars($pickup->status); ?>"
                                                   class="update-status-btn btn btn-warning btn-sm font-weight-bold text-xs mx-2"
                                                   data-bs-toggle="tooltip" title="Update Status">
                                                    <i class="fa fa-pencil" aria-hidden="true"></i> Update Status
                                                </a>

                                                <a style="margin-right:6px; margin-top:5px;"
                                                   href="<?php echo admin_url('courier/pickups/view/' . $pickup->id); ?>"
                                                   data-id="<?php echo $pickup->id; ?>"
                                                   class="btn btn-info btn-sm font-weight-bold text-xs mx-2"
                                                   title="View Pickup">
                                                    <i class="fa fa-eye" aria-hidden="true"></i> View
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                                <tfoot class="table-footer">
                                <tr>
                                    <th>Pickup Time</th>
                                    <th>Vehicle Type</th>
                                    <th>Contact Person</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th>Pickup Signature</th>
                                    <th>Action</th>
                                </tr>
                                </tfoot>
                            </table>
                        <?php else: ?>
                            <!-- Show a message when there's no data -->
                            <div class="text-center text-danger">
                                <p>No available pickups.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="update_status" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php echo form_open('admin/courier/pickups/update_status/', ['id' => 'update-pickup-form']); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Update Status</h4>
                <input type="hidden" value="" name="pickup_id">
                <input type="hidden" value="" name="signature">
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <select id="status" name="status"
                                class="custom-select">
                            <option value="picked_up"> Picked Up</option>
                            <option value="delivered"> Delivered</option>
                        </select>
                    </div>
                    <div id="signatureCanvas" style="display:none;" class="col-md-12">
                        <canvas height="150" id="signature"
                                style="margin-top:10px;  border: 1px solid #ddd;"></canvas>
                        <br>
                        <button style="margin-top:10px;" id="clear-signature" class="btn-info btn ">Clear Signature
                        </button>
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
<?php init_tail(); ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        $('.update-status-btn').on('click', function () {

            let pickupId = $(this).data('id');
            let currentStatus = $(this).data('status');

            $('input[name="pickup_id"]').val(pickupId);

            // Get the status select element
            let statusSelect = $('#status');

            // Clear all options first
            statusSelect.empty();

            // Check current status and set available options
            if ((currentStatus === 'picked_up') || (currentStatus === 'delivered')) {
                statusSelect.append(new Option('Delivered', 'delivered'));
            } else {
                // Add more status options if needed, based on other statuses
                statusSelect.append(new Option('Picked Up', 'picked_up'));
                statusSelect.append(new Option('Delivered', 'delivered'));
            }

            toggleSignatureCanvas();

        });

        // Reference to the status select and signature canvas container
        let statusSelect = document.getElementById('status');
        let signatureCanvas = document.getElementById('signatureCanvas');

        // Function to toggle the display of the signature canvas based on the selected status
        window.toggleSignatureCanvas = function () {
            if (statusSelect.value === 'picked_up') {
                signatureCanvas.style.display = 'block';
            } else {
                signatureCanvas.style.display = 'none';
            }
        }


        // Event listener for changes in the select dropdown
        statusSelect.addEventListener('change', toggleSignatureCanvas);

        let canvas = document.getElementById("signature");
        const signaturePad = new SignaturePad(canvas);

        $('#clear-signature').on('click', function (event) {
            event.preventDefault()
            signaturePad.clear();
        });

        document.getElementById('update-pickup-form').addEventListener('submit', function (e) {
            canvas = document.getElementById('signature');
            document.querySelector('input[name="signature"]').value = canvas.toDataURL('image/png');
        });

    })
</script>

