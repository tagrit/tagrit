<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php load_courier_styles(); ?>
<?php echo '<script src="https://cdn.jsdelivr.net/npm/signature_pad"></script>'; ?>
<style>
    .container {
        width: 100%;
        background-color: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }


    .header {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f0f0;
        position: relative;
    }

    .header h1 {
        font-size: 28px;
        margin: 0;
        color: #333;
        text-align: center;
    }


    .header .status-badge {
        background-color: #007bff;
        color: white;
        padding: 5px 10px;
        font-size: 16px;
        font-weight: bold;
        margin-left: 20px;
    }

    .pickup-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
        flex-wrap: wrap;
    }

    .pickup-info .info-section {
        width: 48%;
    }

    .pickup-info h2 {
        font-size: 16px;
        margin-bottom: 15px;
        color: #007bff;
        padding-bottom: 5px;
        text-decoration: underline;
    }

    .pickup-info p {
        margin: 10px 0;
        font-size: 14px;
    }

    .pickup-info label {
        font-weight: bold;
        color: #555;
    }

    .pickup-info span {
        margin-left: 10px;
        color: #333;
    }

    .status-container {
        text-align: center;
        margin-top: 20px;
    }

    .status-container select {
        padding: 10px;
        font-size: 16px;
        border-radius: 5px;
        border: 1px solid #ccc;
        margin-bottom: 10px;
        width: 50%;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .header {
            justify-content: center;
        }

        .header .status-badge {
            margin-left: 20px; /* Keep the margin to maintain spacing */
        }

        .pickup-info .info-section {
            width: 100%;
            margin-bottom: 20px;
        }

        .status-container select {
            width: 100%;
        }
    }
</style>
<div id="wrapper">
    <div class="content">
        <div class="container">
            <div class="header">
                <h1>Pickup Information</h1>
                <span class="status-badge"><?php echo str_replace('_',' ',strtoupper($pickup['status'])); ?></span>
            </div>

            <input type="hidden" id="current_status"  value="<?php echo $pickup['status']; ?>">

            <div class="pickup-info">
                <div class="info-section">
                    <h2>Contact Information</h2>
                    <p>
                        <label>Name:</label><span><?php echo $pickup['contact_first_name'] . ' ' . $pickup['contact_last_name']; ?></span>
                    </p>
                    <p><label>Email:</label><span><?php echo $pickup['contact_email']; ?></span></p>
                    <p><label>Phone:</label><span><?php echo $pickup['contact_phone_number']; ?></span></p>
                </div>

                <div class="info-section">
                    <h2>Pickup Address</h2>
                    <p><label>Address:</label><span><?php echo $pickup['address']; ?></span></p>
                    <p><label><?php echo str_replace('_', ' ', $pickup['address_type']); ?>
                            :</label><span><?php echo $pickup['pickup_zip']; ?></span></p>
                    <p><label>Country:</label><span><?php echo $pickup['country_name']; ?></span></p>
                </div>
            </div>

            <div class="status-container">
                <?php echo form_open('admin/courier/pickups/update_status/', ['id' => 'update-status-pickup-form']); ?>
                <label for="status">Update Status:</label>
                <input type="hidden" value="<?php echo $pickup['id']; ?>" name="pickup_id">
                <input type="hidden" value="" name="signature">

                <select id="status" name="status">
                    <option value="picked_up">Picked Up</option>
                    <option value="delivered">Delivered</option>
                </select>
                <div style="margin-bottom:10px;" id="signatureCanvasP" class="col-md-12">
                    <canvas height="150" id="signature" style="margin-top:10px;  border: 1px solid #ddd;"></canvas>
                    <br>
                    <button style="margin-top:10px;" id="clear-signature" class="btn-info btn ">Clear Signature</button>
                </div>
                <button type="submit" class="custom-button" style="text-decoration: none; border: 2px solid black;">Update Status
                </button>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        window.toggleStatusView = function () {

            let currentStatus = $('#current_status').val();
            let statusSelect = $('#status');

            statusSelect.empty();

            // Check current status and set available options
            if ((currentStatus === 'picked_up') || (currentStatus === 'delivered')) {
                console.log(currentStatus);
                statusSelect.append(new Option('Delivered', 'delivered'));
            } else {
                // Add more status options if needed, based on other statuses
                statusSelect.append(new Option('Picked Up', 'picked_up'));
                statusSelect.append(new Option('Delivered', 'delivered'));
            }
        }

        toggleStatusView();


        // Reference to the status select and signature canvas container
        let statusSelect = document.getElementById('status');
        let signatureCanvas = document.getElementById('signatureCanvasP');

        // Function to toggle the display of the signature canvas based on the selected status
        window.toggleSignatureCanvas = function () {
            if (statusSelect.value === 'picked_up') {
                signatureCanvas.style.display = 'block';
            } else {
                signatureCanvas.style.display = 'none';
            }
        }
        toggleSignatureCanvas()

        statusSelect.addEventListener('change', toggleSignatureCanvas);


        let canvas = document.getElementById("signature");
        const signaturePad = new SignaturePad(canvas);

        $('#clear-signature').on('click', function (event) {
            event.preventDefault()
            signaturePad.clear();
        });

        document.getElementById('update-status-pickup-form').addEventListener('submit', function (e) {
            canvas = document.getElementById('signature');
            document.querySelector('input[name="signature"]').value = canvas.toDataURL('image/png');
        });


    })
</script>
