<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php load_courier_styles(); ?>

<?php
// Include Flatpickr CSS
echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">';

// Include Select2 CSS
echo '<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />';

?>


<style>

    /* Custom Styles for Phone Input with Country Code */
    .phone-input-group {
        display: flex;
        align-items: center; /* Vertically center the inputs */
    }

    .country-code {
        width: 60px; /* Set a fixed width for the country code input */
        text-align: center; /* Center the text inside */
        border: 1px solid #ccc;
        border-radius: 4px;
        border-top-right-radius: 0 !important; /* Rounded corners on the left */
        border-bottom-right-radius: 0 !important; /* Rounded corners on the left */
        padding: 10px; /* Padding for better appearance */
        font-size: 14px; /* Adjust font size */
        background-color: #f9f9f9; /* Background color */
        cursor: none; /* Cursor style for read-only input */
    }


    .phone {
        background-color: #f9fafb; /* Equivalent to bg-gray-50 */
        border: 1px solid #d1d5db;
        border-left: 0px !important;
        color: #111827; /* Equivalent to text-gray-900 */
        font-size: 0.875rem;
        border-radius: 0.375rem; /* Equivalent to rounded-lg */
        border-top-left-radius: 0 !important; /* Remove top-left border radius */
        border-bottom-left-radius: 0 !important; /* Remove top-left border radius */
        padding: 0.625rem; /* Equivalent to p-2.5 */
        width: 100%;
        transition: border-color 0.2s, box-shadow 0.2s;
    }


    .error-message {
        color: red; /* Red color for error messages */
        font-size: 12px; /* Smaller font size for error messages */
        margin-top: 5px; /* Spacing above the error message */
    }


    /* Switch styling */
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 30px;
    }

    /* Hide default checkbox */
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    /* Slider */
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        border-radius: 15px;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 2px;
        bottom: 2px;
        background-color: white;
        border-radius: 50%;
        transition: .4s;
    }

    /* Checked state */
    input:checked + .slider {
        background-color: #007bff;
    }

    input:checked + .slider:before {
        transform: translateX(30px);
    }

    /* Checkbox Styles */
    .custom-checkbox {
        display: flex;
        align-items: center;
        cursor: pointer;
    }

    .custom-checkbox input[type="checkbox"] {
        width: 20px;
        height: 20px;
        margin-right: 10px;
        margin-left: 10px;
        margin-bottom: 5px;
    }

    .checkbox-text {
        font-weight: bold;
        color: #333;
    }

    .select2-container .select2-selection--single {
        background-color: #f9fafb;
        border: 1px solid #d1d5db;
        color: #111827;
        font-size: 0.875rem;
        border-radius: 0.375rem;
        padding: 5px 10px 10px 10px;
        width: 100%;
        height: 40px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .select2-selection__arrow {
        transform: translateY(30%);
    }

</style>
<!-- Assume the $mode_type is set in a script tag in your HTML -->
<script>
    // Set this based on your PHP code or context
    const modeType = '<?php echo $mode_type; ?>'; // Adjust according to how you get the value

    document.addEventListener('DOMContentLoaded', function () {

        const pickupCheckbox = document.getElementById('addPickupCheckbox');
        const pickupSection = document.getElementById('optionalPickupSection');

        //toggle commercial invoice view
        const toggleSwitch = document.getElementById('toggleSwitch');
        const attachInvoice = document.getElementById('invoice_attachment');
        const inputInvoice = document.getElementById('invoice_input');

        const showPickupSection = <?php echo isset($show_pickup_section) && $show_pickup_section ? 'true' : 'false'; ?>;
        const showCommercialAttachmentSection = <?php echo isset($show_commercial_value_attachment_section) && $show_commercial_value_attachment_section ? 'true' : 'false'; ?>;


        if (showPickupSection) {
            pickupSection.style.display = 'block';
            pickupCheckbox.checked = true; // Ensure the checkbox is checked
        }

        if (showCommercialAttachmentSection) {
            attachInvoice.style.display = 'block';
            inputInvoice.style.display = 'none';
            toggleSwitch.checked = true;
        } else {
            attachInvoice.style.display = 'none';
            inputInvoice.style.display = 'block';
            toggleSwitch.checked = false;
        }

        toggleSwitch.addEventListener('change', () => {
            if (toggleSwitch.checked) {
                attachInvoice.style.display = 'block';
                inputInvoice.style.display = 'none';
            } else {
                attachInvoice.style.display = 'none';
                inputInvoice.style.display = 'block';
            }
        });


        if (showPickupSection) {
            pickupSection.style.display = 'block';
            pickupCheckbox.checked = true; // Ensure the checkbox is checked
        }

        pickupCheckbox.addEventListener('change', function () {
            if (this.checked) {
                pickupSection.style.display = 'block';
            } else {
                pickupSection.style.display = 'none';
            }
        });

        function attachRemoveEvent(button) {
            button.addEventListener('click', function () {
                this.closest('tr').remove();
            });
        }

        // Attach event to initial remove package buttons
        const packageRemoveButtons = document.getElementsByClassName('remove-package');
        for (let i = 0; i < packageRemoveButtons.length; i++) {
            attachRemoveEvent(packageRemoveButtons[i]);
        }
        // Attach event to initial remove fcl-package buttons
        const fclPackageRemoveButtons = document.getElementsByClassName('remove-fcl-package');
        for (let i = 0; i < fclPackageRemoveButtons.length; i++) {
            attachRemoveEvent(fclPackageRemoveButtons[i]);
        }

        // Attach event to initial remove fcl-package buttons
        const CommercialItemsRemoveButtons = document.getElementsByClassName('remove-commercial-item');
        for (let i = 0; i < CommercialItemsRemoveButtons.length; i++) {
            attachRemoveEvent(CommercialItemsRemoveButtons[i]);
        }

        // Add new row functionality for FCL package
        window.addFCLPackage = function () {
            const packageTable = document.getElementById('fclPackageTable').getElementsByTagName('tbody')[0];
            const newRow = packageTable.insertRow();

            newRow.innerHTML = `
            <td><input name="amount[]" class="form-control" type="number" step="any"></td>
            <td><textarea name="package_description[]" class="custom-textarea" rows="3"></textarea></td>
            <td><select class="custom-select" name="fcl_options[]" id="">
                <option value="20'DV">20'DV</option>
                <option value="40'DV">40'DV</option>
                <option value="20'HC">20'HC</option>
                <option value="40'HC">40'HC</option>
                <option value="20'RF">20'RF</option>
                <option value="40'RF">40'RF</option>
                <option value="20'FR">20'FR</option>
                <option value="40'FR">40'FR</option>
                <option value="RoRo">RoRo</option>
            </select></td>
            <td><button type="button" class="btn btn-danger remove-fcl-package"><i class="fa fa-trash"></i></button></td>
        `;

            attachRemoveEvent(newRow.getElementsByClassName('remove-fcl-package')[0]);
        }

        // Add new row functionality for Commercial Items
        window.addCommercialItem = function () {
            const commercialItemsTable = document.getElementById('commercialItemsTable').getElementsByTagName('tbody')[0];
            const newRow = commercialItemsTable.insertRow();

            newRow.innerHTML = `
            <td><input name="commodity_quantity[]" class="form-control amount" type="number" step="any"></td>
            <td><textarea name="commodity_description[]" class="custom-textarea" rows="3"></textarea></td>
            <td><input name="declared_value[]" class="form-control chargeable-weight" type="number" step="any"></td>
            <td><button type="button" class="btn btn-danger remove-commercial-item"><i class="fa fa-trash"></i></button></td>
        `;

            attachRemoveEvent(newRow.getElementsByClassName('remove-commercial-item')[0]);
        }


        // Add new row functionality for normal package
        window.addNormalPackage = function () {
            const packageTable = document.getElementById('packageTable').getElementsByTagName('tbody')[0];
            const newRow = packageTable.insertRow();

            newRow.innerHTML = `
            <td><input name="amount[]" class="form-control amount" type="number" step="any"></td>
            <td><textarea name="package_description[]" class="custom-textarea" rows="3"></textarea></td>
            <td><input name="weight[]" class="form-control weight" type="number" step="any"></td>
            <td><input name="length[]" class="form-control length" type="number" step="any"></td>
            <td><input name="width[]" class="form-control width" type="number" step="any"></td>
            <td><input name="height[]" class="form-control height" type="number" step="any"></td>
            <td><input name="weight_vol[]" class="form-control weight-vol" type="number" step="any"></td>
            <td><input name="chargeable_weight[]" class="form-control chargeable-weight" type="number" step="any"></td>
            <td><button type="button" class="btn btn-danger remove-package"><i class="fa fa-trash"></i></button></td>
        `;

            attachRemoveEvent(newRow.getElementsByClassName('remove-package')[0]);
            attachVolumetricWeightCalculation(newRow);
        }

        function attachVolumetricWeightCalculation(row) {
            const lengthInput = row.querySelector('.length');
            const quantityInput = row.querySelector('.amount');
            const widthInput = row.querySelector('.width');
            const heightInput = row.querySelector('.height');
            const weightVolInput = row.querySelector('.weight-vol');
            const chargeableWeightInput = row.querySelector('.chargeable-weight');
            const weightInput = row.querySelector('.weight');

            function calculateVolumetricWeight() {
                const length = parseFloat(lengthInput.value) || 0;
                const amount = parseFloat(quantityInput.value) || 0;
                const width = parseFloat(widthInput.value) || 0;
                const height = parseFloat(heightInput.value) || 0;
                const actualWeight = parseFloat(weightInput.value) || 0;

                // Determine chargeable weight based on modeType
                let chargeableWeight = 0;
                switch (modeType) {
                    case 'lcl':
                        const seaVolumetricWeight = (length * width * height) / parseInt(<?php echo $dimensional_factor[3]->value; ?>);
                        chargeableWeight = Math.max(actualWeight, seaVolumetricWeight);
                        weightVolInput.value = seaVolumetricWeight.toFixed(2);
                        chargeableWeightInput.value = chargeableWeight.toFixed(2) * amount;
                        break;
                    case 'air_consolidation':
                        const airConsolidationVolumetricWeight = (length * width * height) / parseInt(<?php echo $dimensional_factor[1]->value; ?>);
                        weightVolInput.value = airConsolidationVolumetricWeight.toFixed(2);
                        chargeableWeightInput.value = actualWeight.toFixed(2) * amount;
                        break;
                    case 'air_freight':
                        const airFreightVolumetricWeight = (length * width * height) / parseInt(<?php echo $dimensional_factor[2]->value; ?>);
                        chargeableWeight = Math.max(actualWeight, airFreightVolumetricWeight);
                        weightVolInput.value = airFreightVolumetricWeight.toFixed(2);
                        chargeableWeightInput.value = chargeableWeight.toFixed(2) * amount;
                        break;
                    case 'sea_consolidation':
                        chargeableWeight = (length * width * height) / 1000000;
                        weightVolInput.value = chargeableWeight.toFixed(2);
                        chargeableWeightInput.value = chargeableWeight.toFixed(2) * amount;
                        break;
                    default:
                        const volumetricWeight = (length * width * height) / parseInt(<?php echo $dimensional_factor[0]->value; ?>);
                        chargeableWeight = Math.max(actualWeight, volumetricWeight);
                        weightVolInput.value = volumetricWeight.toFixed(2);
                        chargeableWeightInput.value = chargeableWeight.toFixed(2) * amount;
                }

            }

            // Attach the event listeners
            lengthInput.addEventListener('input', calculateVolumetricWeight);
            widthInput.addEventListener('input', calculateVolumetricWeight);
            heightInput.addEventListener('input', calculateVolumetricWeight);
            weightInput.addEventListener('input', calculateVolumetricWeight);
            quantityInput.addEventListener('input', calculateVolumetricWeight);
        }

        // Attach calculation to the initial row
        const initialRow = document.querySelector('#packageTable tbody tr');
        if (initialRow) {
            attachVolumetricWeightCalculation(initialRow);
        }

    });
</script>
<div id="wrapper">
    <div class="content">

        <div class="row">
            <?php echo form_open('admin/courier/shipments/store', [
                'id' => 'create-shipment-form',
                'enctype' => 'multipart/form-data'
            ]); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div style="width:100%;" class="panel-body">
                        <div style="margin-bottom:25px">
                            <a style="text-decoration: none; border:2px solid black;" class="custom-button"
                               href="<?php echo admin_url('courier/shipments/main'); ?>">
                                <i class="fa fa-arrow-left" aria-hidden="true"></i>
                                <span style="margin-left:10px;">Shipments Dashboard</span>
                            </a>
                        </div>

                        <div class="flex-container">
                            <h4 class="font-bold m-0">Create Shipment <span
                                        style="font-weight:bold">(<?php echo htmlspecialchars(!empty($mode) ? $mode_type == 'none' ? ucfirst($type) . ',' . ucfirst($mode) : ucfirst($type) . ',' . ucfirst($mode) . ' - ' . ucfirst(str_replace('_', ' ', $mode_type)) : ucfirst($type)) ?>)</span>
                            </h4>
                            <a style="text-decoration: none; border:2px solid black;" class="custom-button"
                               href="<?php echo !empty($mode) ? admin_url('courier/shipments?type=' . $type . '&mode=' . $mode . '&mode_type=' . $mode_type) : admin_url('courier/shipments?type=' . $type); ?>">
                                View Shipments
                            </a>
                            <?php if ($type === 'international'): ?>
                                <input type="hidden" name="type" value="international">
                            <?php else: ?>
                                <input type="hidden" name="type" value="domestic">
                            <?php endif; ?>

                            <input type="hidden" name="mode" value="<?php echo $mode; ?>">

                            <input type="hidden" name="mode_type" value="<?php echo $mode_type; ?>">

                        </div>

                        <hr class="hr-panel-heading"/>

                        <!-- Record Shipment -->
                        <section class="custom-section">


                            <div class="custom-container">
                                <!-- Radio buttons to toggle between Individual and Company -->

                                <div class="custom-form-group"
                                     style="margin-bottom: 20px; padding: 20px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); background-color: #f9f9f9;">
                                    <label for="type_selection" class="custom-label"
                                           style="display: block; font-weight: bold; font-size: 18px;">Sender
                                        Type</label>
                                    <div id="type_selection"
                                         style="display: flex; margin-top:-18px; gap: 20px; align-items: center;">
                                        <label style="display: flex; align-items: center; font-size: 16px; cursor: pointer;">
                                            <input type="radio" name="sender_type" value="individual" checked
                                                   style="margin-right: 8px; accent-color: #007bff;">
                                            Individual
                                        </label>
                                        <label style="display: flex; align-items: center; font-size: 16px; cursor: pointer;">
                                            <input type="radio" name="sender_type" value="company"
                                                   style="margin-right: 8px; accent-color: #007bff;">
                                            Company
                                        </label>

                                        <label style="padding-left:40px; text-align:right; margin-top:-5px; align-items: center; font-size: 15px; cursor: pointer;">
                                            <label for="type_selection" class="custom-label"
                                                   style="display: block; font-weight: bold; margin-top:-12px; margin-bottom: 5px; font-size: 18px;">Shipping
                                                Currency
                                            </label>
                                            <?php echo form_dropdown('currency_id', array_column($currencies, 'name', 'id'), set_value('id'), ['id' => 'currency_id', 'class' => 'custom-select']); ?>
                                            <?php echo form_error('currency_id', '<div class="error-message">', '</div>'); ?>
                                        </label>
                                    </div>
                                </div>


                                <!-- Company Section (Hidden by default) -->
                                <div id="company_section" class="custom-form-grid"
                                     style="display: none; margin-top:70px;">
                                    <div class="row section-container">
                                        <div class="section-label">Company</div>
                                        <div style="padding-left:15px; padding-right:15px;" class="row">
                                            <div class="col-md-6 col-sm-12">
                                                <div class="custom-form-group">
                                                    <label for="company_name" class="custom-label">Company Name</label>
                                                    <?php echo form_input(['id' => 'company_name', 'name' => 'company_name', 'type' => 'text', 'class' => 'custom-input', 'placeholder' => 'Company Name', 'value' => set_value('company_name')]); ?>
                                                    <?php echo form_error('company_name', '<div class="error-message">', '</div>'); ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12">
                                                <div class="custom-form-group">
                                                    <label for="contact_name" class="custom-label">Contact Name</label>
                                                    <?php echo form_input(['id' => 'contact_name', 'name' => 'contact_name', 'type' => 'text', 'class' => 'custom-input', 'placeholder' => 'Contact Name', 'value' => set_value('contact_name')]); ?>
                                                    <?php echo form_error('contact_name', '<div class="error-message">', '</div>'); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($type === 'international'): ?>
                                            <div style="padding-left:15px; padding-right:15px;" class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="custom-form-group">
                                                        <label for="contact_country_id"
                                                               class="custom-label">Country</label>
                                                        <?php
                                                        // Check if $contact_country_id is set, otherwise default to form validation data
                                                        $selected_contact_country_id = isset($user_country->country_id) ? $user_country->country_id : set_value('contact_country_id');

                                                        echo form_dropdown(
                                                            'contact_country_id',
                                                            array_column($countries, 'short_name', 'country_id'),
                                                            $selected_contact_country_id,
                                                            [
                                                                'id' => 'contact_country_id',
                                                                'class' => 'custom-select',
                                                                'style' => 'width:100%;'
                                                            ]
                                                        );
                                                        ?>
                                                        <?php echo form_error('contact_country_id', '<div class="error-message">', '</div>'); ?>
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-sm-12">
                                                    <div class="custom-form-group">
                                                        <label for="sender_state_id"
                                                               class="custom-label">State</label>
                                                        <select style="width:100%;" name="contact_state_id"
                                                                id="contact_state_id"
                                                                class="custom-select">
                                                            <option value="" selected>Select State</option>
                                                        </select>
                                                        <?php echo form_error('contact_state_id', '<div class="error-message">', '</div>'); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <div style="padding-left:15px; padding-right:15px;" class="row">
                                            <div class="col-md-6 col-sm-12">
                                                <div class="custom-form-group">
                                                    <?php
                                                    $contact_country_code = isset($user_country->calling_code) ? '+' . $user_country->calling_code : set_value('contact_country_code');
                                                    ?>
                                                    <label for="contact_phone" class="custom-label">Contact
                                                        Phone</label>
                                                    <div class="phone-input-group">
                                                        <!-- Country Code Input -->
                                                        <input style="border-right:transparent;" type="text"
                                                               id="contact_country_code" name="contact_country_code"
                                                               class="country-code"
                                                               value="<?php echo $contact_country_code; ?>" readonly>
                                                        <?php echo form_input(['id' => 'contact_phone', 'name' => 'contact_phone', 'type' => 'text', 'class' => 'phone', 'placeholder' => 'Phone Number', 'value' => set_value('contact_phone')]); ?>
                                                    </div>
                                                    <!-- Hidden Field for Combined Number -->
                                                    <input type="hidden" id="contact_full_phone_number"
                                                           name="contact_full_phone_number" value="">
                                                    <?php echo form_error('contact_phone', '<div class="error-message">', '</div>'); ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12">
                                                <div class="custom-form-group">
                                                    <label for="contact_email" class="custom-label">Contact
                                                        Email</label>
                                                    <?php echo form_input(['id' => 'contact_email', 'name' => 'contact_email', 'type' => 'email', 'class' => 'custom-input', 'placeholder' => 'Email', 'value' => set_value('contact_email')]); ?>
                                                    <?php echo form_error('contact_email', '<div class="error-message">', '</div>'); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div style="padding-left:15px; padding-right:15px;" class="row">
                                            <div class="col-md-6 col-sm-12">
                                                <div class="custom-form-group">
                                                    <label for="contact_address" class="custom-label">Address</label>
                                                    <textarea id="contact_address" name="contact_address"
                                                              class="custom-textarea"
                                                              rows="3"
                                                              placeholder="Enter your address here..."><?php echo set_value('contact_address'); ?></textarea>
                                                    <?php echo form_error('contact_address', '<div class="error-message">', '</div>'); ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12">
                                                <div class="custom-form-group">
                                                    <div id="address_type" style="display: flex; gap: 20px;">
                                                        <label>
                                                            <input type="radio" name="contact_address_type"
                                                                   value="zip_code" checked>
                                                            Zip code
                                                        </label>
                                                        <label>
                                                            <input type="radio" name="contact_address_type"
                                                                   value="postal_code"> Postal code
                                                        </label>
                                                    </div>
                                                    <?php echo form_input(['id' => 'contact_zipcode', 'name' => 'contact_zipcode', 'type' => 'text', 'class' => 'custom-input', 'placeholder' => 'Zipcode', 'value' => set_value('contact_zipcode')]); ?>
                                                    <?php echo form_error('contact_zipcode', '<div class="error-message">', '</div>'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>


                        <!-- Sender Shipment -->
                        <section id="sender_section" style="display: block;" class="custom-section">
                            <div class="custom-container">
                                <div class="custom-form-grid">
                                    <div style="margin-top:-10px;" class="row section-container">
                                        <div class="section-label">Sender</div>
                                        <div class="col-md-6 col-sm-12">
                                            <div class="custom-form-group">
                                                <label for="sender_first_name" class="custom-label">First Name</label>
                                                <?php echo form_input(['id' => 'sender_first_name', 'name' => 'sender_first_name', 'type' => 'text', 'class' => 'custom-input', 'placeholder' => 'First Name', 'value' => set_value('sender_first_name')]); ?>
                                                <?php echo form_error('sender_first_name', '<div class="error-message">', '</div>'); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-12">
                                            <div class="custom-form-group">
                                                <label for="sender_last_name" class="custom-label">Last Name</label>
                                                <?php echo form_input(['id' => 'sender_last_name', 'name' => 'sender_last_name', 'type' => 'text', 'class' => 'custom-input', 'placeholder' => 'Last Name', 'value' => set_value('sender_last_name')]); ?>
                                                <?php echo form_error('sender_last_name', '<div class="error-message">', '</div>'); ?>
                                            </div>
                                        </div>
                                        <?php if ($type === 'international'): ?>
                                            <div style="padding-left:15px; padding-right:15px;" class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="custom-form-group">
                                                        <label for="sender_country_id"
                                                               class="custom-label">Country</label>
                                                        <?php

                                                        $selected_sender_country_id = isset($user_country->country_id) ? $user_country->country_id : set_value('sender_country_id');

                                                        echo form_dropdown(
                                                            'sender_country_id',
                                                            array_column($countries, 'short_name', 'country_id'),
                                                            $selected_sender_country_id,
                                                            [
                                                                'id' => 'sender_country_id',
                                                                'class' => 'custom-select'
                                                            ]
                                                        );
                                                        ?>
                                                        <?php echo form_error('sender_country_id', '<div class="error-message">', '</div>'); ?>
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-sm-12">
                                                    <div class="custom-form-group">
                                                        <label for="sender_state_id"
                                                               class="custom-label">State</label>
                                                        <select name="sender_state_id" id="sender_state_id"
                                                                class="custom-select">
                                                            <option value="" selected>Select State</option>
                                                        </select>
                                                        <?php echo form_error('sender_state_id', '<div class="error-message">', '</div>'); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <div class="col-md-6 col-sm-12">
                                            <div class="custom-form-group">
                                                <?php
                                                $sender_country_code = isset($user_country->calling_code) ? '+' . $user_country->calling_code : set_value('sender_country_code');
                                                ?>
                                                <label for="sender_phone_number" class="custom-label">Phone</label>
                                                <div class="phone-input-group">
                                                    <!-- Country Code Input -->
                                                    <input style="border-right:transparent;" type="text"
                                                           id="sender_country_code" name="sender_country_code"
                                                           class="country-code"
                                                           value="<?php echo $sender_country_code; ?>" readonly>
                                                    <?php echo form_input(['id' => 'sender_phone_number', 'name' => 'sender_phone_number', 'type' => 'text', 'class' => 'phone', 'placeholder' => 'Phone Number', 'value' => set_value('sender_phone_number')]); ?>
                                                </div>
                                                <!-- Hidden Field for Combined Number -->
                                                <input type="hidden" id="sender_full_phone_number"
                                                       name="sender_full_phone_number" value="">
                                                <?php echo form_error('sender_phone_number', '<div class="error-message">', '</div>'); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-12">
                                            <div class="custom-form-group">
                                                <label for="sender_email" class="custom-label">Email</label>
                                                <?php echo form_input(['id' => 'sender_email', 'name' => 'sender_email', 'type' => 'email', 'class' => 'custom-input', 'placeholder' => 'Email', 'value' => set_value('sender_email')]); ?>
                                                <?php echo form_error('sender_email', '<div class="error-message">', '</div>'); ?>
                                            </div>
                                        </div>
                                        <div style="padding-left:15px; padding-right:15px;" class="row">
                                            <div class="col-md-6 col-sm-12">
                                                <div class="custom-form-group">
                                                    <label for="sender_address" class="custom-label">Address</label>
                                                    <textarea id="sender_address" name="sender_address"
                                                              class="custom-textarea"
                                                              rows="3"
                                                              autocomplete="off"
                                                              placeholder="Enter your address here..."><?php echo set_value('sender_address'); ?></textarea>
                                                    <?php echo form_error('sender_address', '<div class="error-message">', '</div>'); ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12">
                                                <div class="custom-form-group">
                                                    <div id="address_type" style="display: flex; gap: 20px;">
                                                        <label>
                                                            <input type="radio" name="sender_address_type"
                                                                   value="zip_code" checked>
                                                            Zip code
                                                        </label>
                                                        <label>
                                                            <input type="radio" name="sender_address_type"
                                                                   value="postal_code"> Postal code
                                                        </label>
                                                    </div>
                                                    <?php echo form_input(['id' => 'sender_zipcode', 'name' => 'sender_zipcode', 'type' => 'text', 'class' => 'custom-input', 'placeholder' => 'Zipcode', 'value' => set_value('recipient_zipcode'), 'autocomplete' => 'off']); ?>
                                                    <?php echo form_error('sender_zipcode', '<div class="error-message">', '</div>'); ?>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </section>

                        <section id class="custom-section">
                            <div class="custom-container">
                                <div class="custom-form-group"
                                     style="margin-bottom: 20px; padding: 20px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); background-color: #f9f9f9;">
                                    <label for="type_selection" class="custom-label"
                                           style="display: block; font-weight: bold; font-size: 18px;">Recipient
                                        Type</label>
                                    <div id="type_selection"
                                         style="display: flex; gap: 20px; align-items: center;">
                                        <label style="display: flex; align-items: center; font-size: 16px; cursor: pointer;">
                                            <input type="radio" name="recipient_type" value="individual" checked
                                                   style="margin-right: 8px; accent-color: #007bff;">
                                            Individual
                                        </label>
                                        <label style="display: flex; align-items: center; font-size: 16px; cursor: pointer;">
                                            <input type="radio" name="recipient_type" value="company"
                                                   style="margin-right: 8px; accent-color: #007bff;">
                                            Company
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Recipient Information -->
                        <section id="recipient_section" class="custom-section">
                            <div class="custom-container">
                                <div class="custom-form-grid">
                                    <div style="margin-top:-10px;" class="row section-container">
                                        <div class="section-label">Recipient</div>
                                        <div class="col-md-6 col-sm-12">
                                            <div class="custom-form-group">
                                                <label for="recipient_first_name" class="custom-label">First
                                                    Name</label>
                                                <?php echo form_input(['id' => 'recipient_first_name', 'name' => 'recipient_first_name', 'type' => 'text', 'class' => 'custom-input', 'placeholder' => 'First Name', 'value' => set_value('recipient_first_name')]); ?>
                                                <?php echo form_error('recipient_first_name', '<div class="error-message">', '</div>'); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-12">
                                            <div class="custom-form-group">
                                                <label for="recipient_last_name" class="custom-label">Last Name</label>
                                                <?php echo form_input(['id' => 'recipient_last_name', 'name' => 'recipient_last_name', 'type' => 'text', 'class' => 'custom-input', 'placeholder' => 'Last Name', 'value' => set_value('recipient_last_name')]); ?>
                                                <?php echo form_error('recipient_last_name', '<div class="error-message">', '</div>'); ?>
                                            </div>
                                        </div>
                                        <?php if ($type === 'international'): ?>
                                            <div style="padding-left:15px; padding-right:15px;" class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="custom-form-group">
                                                        <label for="recipient_country_id"
                                                               class="custom-label">Country</label>
                                                        <?php
                                                        // Check if $recipient_country_id is set, otherwise default to form validation data
                                                        $selected_recipient_country_id = isset($user_country->country_id) ? $user_country->country_id : set_value('recipient_country_id');

                                                        echo form_dropdown(
                                                            'recipient_country_id',
                                                            array_column($recipient_countries, 'short_name', 'country_id'),
                                                            $selected_recipient_country_id,
                                                            [
                                                                'id' => 'recipient_country_id',
                                                                'class' => 'custom-select'
                                                            ]
                                                        );
                                                        ?>
                                                        <?php echo form_error('recipient_country_id', '<div class="error-message">', '</div>'); ?>
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-sm-12">
                                                    <div class="custom-form-group">
                                                        <label for="recipient_state_id"
                                                               class="custom-label">State</label>
                                                        <select name="recipient_state_id" id="recipient_state_id"
                                                                class="custom-select">
                                                            <option value="" selected>Select State</option>
                                                        </select>
                                                        <?php echo form_error('recipient_state_id', '<div class="error-message">', '</div>'); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <div class="col-md-6 col-sm-12">
                                            <div class="custom-form-group">
                                                <?php
                                                $recipient_country_code = isset($user_country->calling_code) ? '+' . $user_country->calling_code : set_value('recipient_country_code');
                                                ?>
                                                <label for="recipient_phone_number" class="custom-label">Phone</label>
                                                <div class="phone-input-group">
                                                    <!-- Country Code Input -->
                                                    <input style="border-right:transparent;" type="text"
                                                           id="recipient_country_code" name="recipient_country_code"
                                                           class="country-code"
                                                           value="<?php echo $recipient_country_code ?>" readonly>

                                                    <!-- Phone Number Input -->
                                                    <?php echo form_input([
                                                        'id' => 'recipient_phone_number',
                                                        'name' => 'recipient_phone_number',
                                                        'type' => 'text',
                                                        'class' => 'phone',
                                                        'placeholder' => 'Phone Number',
                                                        'value' => set_value('recipient_phone_number')
                                                    ]); ?>
                                                </div>
                                                <!-- Hidden Field for Combined Number -->
                                                <input type="hidden" id="recipient_full_phone_number"
                                                       name="recipient_full_phone_number" value="">
                                                <?php echo form_error('recipient_phone_number', '<div class="error-message">', '</div>'); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-12">
                                            <div class="custom-form-group">
                                                <label for="recipient_email" class="custom-label">Email</label>
                                                <?php echo form_input(['id' => 'recipient_email', 'name' => 'recipient_email', 'type' => 'email', 'class' => 'custom-input', 'placeholder' => 'Email', 'value' => set_value('recipient_email')]); ?>
                                                <?php echo form_error('recipient_email', '<div class="error-message">', '</div>'); ?>
                                            </div>
                                        </div>
                                        <div style="padding-left:15px; padding-right:15px;" class="row">
                                            <div class="col-md-6 col-sm-12">
                                                <div class="custom-form-group">
                                                    <label for="recipient_address" class="custom-label">Address</label>
                                                    <textarea id="recipient_address" name="recipient_address"
                                                              class="custom-textarea"
                                                              rows="3"
                                                              autocomplete="off"
                                                              placeholder="Enter your address here..."><?php echo set_value('recipient_address'); ?></textarea>
                                                    <?php echo form_error('recipient_address', '<div class="error-message">', '</div>'); ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12">
                                                <div class="custom-form-group">
                                                    <div id="address_type" style="display: flex; gap: 20px;">
                                                        <label>
                                                            <input type="radio" name="recipient_address_type"
                                                                   value="zip_code" checked>
                                                            Zip code
                                                        </label>
                                                        <label>
                                                            <input type="radio" name="recipient_address_type"
                                                                   value="postal_code"> Postal code
                                                        </label>
                                                    </div>
                                                    <?php echo form_input(['id' => 'recipient_zipcode', 'name' => 'recipient_zipcode', 'type' => 'text', 'class' => 'custom-input', 'placeholder' => 'Zipcode', 'value' => set_value('recipient_zipcode'), 'autocomplete' => 'off']); ?>
                                                    <?php echo form_error('recipient_zipcode', '<div class="error-message">', '</div>'); ?>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Recipient Company Section (Hidden by default) -->
                        <section style="display: none; margin-top:-20px;" id="recipient_company_section" class="custom-section">
                            <div class="custom-container">
                                <div id="company_section" class="custom-form-grid">
                                    <div class="row section-container">
                                        <div class="section-label">Company</div>
                                        <div style="padding-left:15px; padding-right:15px;" class="row">
                                            <div class="col-md-6 col-sm-12">
                                                <div class="custom-form-group">
                                                    <label for="recipient_company_name" class="custom-label">Company Name</label>
                                                    <?php echo form_input(['id' => 'recipient_company_name', 'name' => 'recipient_company_name', 'type' => 'text', 'class' => 'custom-input', 'placeholder' => 'Company Name', 'value' => set_value('recipient_company_name')]); ?>
                                                    <?php echo form_error('recipient_company_name', '<div class="error-message">', '</div>'); ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12">
                                                <div class="custom-form-group">
                                                    <label for="recipient_contact_name" class="custom-label">Contact Name</label>
                                                    <?php echo form_input(['id' => 'recipient_contact_name', 'name' => 'recipient_contact_name', 'type' => 'text', 'class' => 'custom-input', 'placeholder' => 'Contact Name', 'value' => set_value('recipient_contact_name')]); ?>
                                                    <?php echo form_error('recipient_contact_name', '<div class="error-message">', '</div>'); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($type === 'international'): ?>
                                            <div style="padding-left:15px; padding-right:15px;" class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="custom-form-group">
                                                        <label for="recipient_contact_country_id"
                                                               class="custom-label">Country</label>
                                                        <?php
                                                        // Check if $contact_country_id is set, otherwise default to form validation data
                                                        $selected_contact_country_id = isset($user_country->country_id) ? $user_country->country_id : set_value('recipient_contact_country_id');

                                                        echo form_dropdown(
                                                            'recipient_contact_country_id',
                                                            array_column($countries, 'short_name', 'country_id'),
                                                            $selected_contact_country_id,
                                                            [
                                                                'id' => 'recipient_contact_country_id',
                                                                'class' => 'custom-select',
                                                                'style' => 'width:100%;'
                                                            ]
                                                        );
                                                        ?>
                                                        <?php echo form_error('recipient_contact_country_id', '<div class="error-message">', '</div>'); ?>
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-sm-12">
                                                    <div class="custom-form-group">
                                                        <label for="recipient_contact_state_id"
                                                               class="custom-label">State</label>
                                                        <select style="width:100%;" name="recipient_contact_state_id"
                                                                id="recipient_contact_state_id"
                                                                class="custom-select">
                                                            <option value="" selected>Select State</option>
                                                        </select>
                                                        <?php echo form_error('recipient_contact_state_id', '<div class="error-message">', '</div>'); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <div style="padding-left:15px; padding-right:15px;" class="row">
                                            <div class="col-md-6 col-sm-12">
                                                <div class="custom-form-group">
                                                    <?php
                                                    $recipient_contact_country_code = isset($user_country->calling_code) ? '+' . $user_country->calling_code : set_value('recipient_contact_country_code');
                                                    ?>
                                                    <label for="contact_phone" class="custom-label">Contact
                                                        Phone</label>
                                                    <div class="phone-input-group">
                                                        <!-- Country Code Input -->
                                                        <input style="border-right:transparent;" type="text"
                                                               id="recipient_contact_country_code" name="recipient_contact_country_code"
                                                               class="country-code"
                                                               value="<?php echo $recipient_contact_country_code; ?>" readonly>
                                                        <?php echo form_input(['id' => 'recipient_contact_phone', 'name' => 'recipient_contact_phone', 'type' => 'text', 'class' => 'phone', 'placeholder' => 'Phone Number', 'value' => set_value('recipient_contact_phone')]); ?>
                                                    </div>
                                                    <!-- Hidden Field for Combined Number -->
                                                    <input type="hidden" id="recipient_contact_full_phone_number"
                                                           name="recipient_contact_full_phone_number" value="">
                                                    <?php echo form_error('recipient_contact_phone', '<div class="error-message">', '</div>'); ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12">
                                                <div class="custom-form-group">
                                                    <label for="recipient_contact_email" class="custom-label">Contact
                                                        Email</label>
                                                    <?php echo form_input(['id' => 'recipient_contact_email', 'name' => 'recipient_contact_email', 'type' => 'email', 'class' => 'custom-input', 'placeholder' => 'Email', 'value' => set_value('recipient_contact_email')]); ?>
                                                    <?php echo form_error('recipient_contact_email', '<div class="error-message">', '</div>'); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div style="padding-left:15px; padding-right:15px;" class="row">
                                            <div class="col-md-6 col-sm-12">
                                                <div class="custom-form-group">
                                                    <label for="recipient_contact_address" class="custom-label">Address</label>
                                                    <textarea id="recipient_contact_address" name="recipient_contact_address"
                                                              class="custom-textarea"
                                                              rows="3"
                                                              placeholder="Enter your address here..."><?php echo set_value('recipient_contact_address'); ?></textarea>
                                                    <?php echo form_error('recipient_contact_address', '<div class="error-message">', '</div>'); ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12">
                                                <div class="custom-form-group">
                                                    <div id="recipient_contact_address_type" style="display: flex; gap: 20px;">
                                                        <label>
                                                            <input type="radio" name="recipient_contact_address_type"
                                                                   value="zip_code" checked>
                                                            Zip code
                                                        </label>
                                                        <label>
                                                            <input type="radio" name="recipient_contact_address_type"
                                                                   value="postal_code"> Postal code
                                                        </label>
                                                    </div>
                                                    <?php echo form_input(['id' => 'recipient_contact_zipcode', 'name' => 'recipient_contact_zipcode', 'type' => 'text', 'class' => 'custom-input', 'placeholder' => 'Zipcode', 'value' => set_value('recipient_contact_zipcode')]); ?>
                                                    <?php echo form_error('recipient_contact_zipcode', '<div class="error-message">', '</div>'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Shipping  Information -->
                        <section style="margin-top:-40px;" class="custom-section">
                            <div class="custom-container">
                                <div class="custom-form-grid">
                                    <div class="row section-container">
                                        <div class="section-label">Shipment Information</div>

                                        <div style="padding-left:15px; padding-right:15px;" class="row">
                                            <?php if ($type !== 'international'): ?>
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="custom-form-group">
                                                        <label for="shipping_mode" class="custom-label">Shipping
                                                            Mode</label>
                                                        <select id="shipping_mode" name="shipping_mode"
                                                                class="custom-select">
                                                            <option value="<?php echo strtoupper('air'); ?>">
                                                                Air
                                                            </option>
                                                            <option value="<?php echo strtoupper('road'); ?>">
                                                                Road Transport
                                                            </option>
                                                        </select>
                                                        <?php echo form_error('shipping_mode', '<div class="error-message">', '</div>'); ?>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="col-md-6 col-sm-12">
                                                    <div class="custom-form-group">
                                                        <label for="shipping_mode" class="custom-label">Shipping
                                                            Mode</label>

                                                        <input type="text" readonly class="custom-input"
                                                               name="shipping_mode"
                                                               value="<?php echo strtoupper($mode . ' (' . str_replace('_', ' ', $mode_type) . ')') ?>">
                                                        <?php echo form_error('shipping_mode', '<div class="error-message">', '</div>'); ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <div class="col-md-6 col-sm-12">
                                                <div class="custom-form-group">
                                                    <label for="courier_company_id" class="custom-label">
                                                        <?php
                                                        switch ($mode ?? 'default') {
                                                            case 'road':
                                                                $company_type = 'Logistic Company';
                                                                break;
                                                            case 'sea':
                                                                $company_type = 'Shipping Company';
                                                                break;
                                                            case 'air':
                                                                $company_type = 'Freight Company';
                                                                break;
                                                            case 'courier':
                                                                $company_type = 'Courier Company';
                                                                break;
                                                            default:
                                                                $company_type = 'Logistic Company';
                                                                break;
                                                        }
                                                        echo $company_type;
                                                        ?>
                                                    </label>
                                                    <input type="hidden" name="company_type"
                                                           value="<?php echo $company_type; ?>"/>
                                                    <select id="courier_company_id" name="courier_company_id"
                                                            class="custom-select">
                                                        <?php if ($mode != 'courier'): ?>
                                                            <option value="1" <?php echo set_select('courier_company_id', '1'); ?>>
                                                                Go Shipping
                                                            </option>
                                                        <?php else: ?>
                                                            <option value="1" <?php echo set_select('courier_company_id', '1'); ?>>
                                                                Go Shipping
                                                            </option>
                                                            <option value="2" <?php echo set_select('courier_company_id', '2'); ?>>
                                                                FedEx
                                                            </option>
                                                            <option value="3" <?php echo set_select('courier_company_id', '3'); ?>>
                                                                DHL
                                                            </option>
                                                            <option value="4" <?php echo set_select('courier_company_id', '5'); ?>>
                                                                Other
                                                            </option>
                                                        <?php endif; ?>
                                                    </select>
                                                    <?php echo form_error('courier_company_id', '<div class="error-message">', '</div>'); ?>
                                                </div>
                                            </div>
                                        </div>


                                        <?php if ($type === 'international'): ?>
                                            <div class="col-md-6 col-sm-12">
                                                <div class="custom-form-group">
                                                    <label for="export_import"
                                                           class="custom-label">Export/Import</label>
                                                    <select id="export_import" name="export_import"
                                                            class="custom-select">
                                                        <option value="export" <?php echo set_select('export_import', 'export'); ?>>
                                                            Export
                                                        </option>
                                                        <option value="import" <?php echo set_select('export_import', 'import'); ?>>
                                                            Import
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <?php if ($mode_type === 'fcl'): ?>
                            <!-- Package Information -->
                            <section class="custom-section">
                                <div class="custom-container">
                                    <div class="custom-form-grid">
                                        <div style="margin-top:-10px;" class="row section-container">
                                            <div class="section-label">Package</div>
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped"
                                                           id="fclPackageTable">
                                                        <thead>
                                                        <tr>
                                                            <th>Quantity</th>
                                                            <th>Package Description</th>
                                                            <th>FCL Option</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                        // Retrieve previously set values for amounts, descriptions, and FCL options
                                                        $amounts = set_value('amount', []);
                                                        $descriptions = set_value('package_description', []);
                                                        $fclOptions = set_value('fcl_options', []);

                                                        // Ensure there is at least one row; otherwise, initialize with one empty row
                                                        $itemCount = max(1, count($amounts)); // At least one row

                                                        // Iterate through each FCL package item
                                                        for ($i = 0; $i < $itemCount; $i++): ?>
                                                            <tr>
                                                                <td>
                                                                    <?php echo form_input([
                                                                        'name' => "amount[$i]", // Change to indexed name
                                                                        'class' => 'amount form-control',
                                                                        'type' => 'number',
                                                                        'step' => 'any',
                                                                        'value' => isset($amounts[$i]) ? $amounts[$i] : ''
                                                                    ]); ?>
                                                                    <?php echo form_error("amount[$i]", '<div class="text-danger">', '</div>'); ?>
                                                                </td>
                                                                <td>
                                                                    <textarea
                                                                            name="package_description[<?php echo $i; ?>]"
                                                                            class="custom-textarea"
                                                                            rows="3"><?php echo isset($descriptions[$i]) ? $descriptions[$i] : ''; ?></textarea>
                                                                    <?php echo form_error("package_description[$i]", '<div class="text-danger">', '</div>'); ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo form_dropdown("fcl_options[$i]", [
                                                                        "20'DV" => "20'DV",
                                                                        "40'DV" => "40'DV",
                                                                        "20'HC" => "20'HC",
                                                                        "40'HC" => "40'HC",
                                                                        "20'RF" => "20'RF",
                                                                        "40'RF" => "40'RF",
                                                                        "20'FR" => "20'FR",
                                                                        "40'FR" => "40'FR",
                                                                        "RoRo" => "RoRo"
                                                                    ], isset($fclOptions[$i]) ? $fclOptions[$i] : '', ['class' => 'custom-select']); ?>
                                                                    <?php echo form_error("fcl_options[$i]", '<div class="text-danger">', '</div>'); ?>
                                                                </td>
                                                                <td>
                                                                    <?php if ($i === 0): ?>
                                                                        <button type="button" class="btn btn-primary"
                                                                                onclick="addFCLPackage()">
                                                                            <i class="fa fa-plus"></i>
                                                                        </button>
                                                                    <?php else: ?>
                                                                        <button type="button"
                                                                                class="btn btn-danger remove-fcl-package">
                                                                            <i class="fa fa-trash"></i>
                                                                        </button>
                                                                    <?php endif; ?>
                                                                </td>
                                                            </tr>
                                                        <?php endfor; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div style="margin-left:0px; margin-top:20px; margin-bottom:15px;"
                                                 class="col-lg-6 col-sm-12 col-md-6">
                                                <label style="font-weight:bold;" for="packaging_charges">Packaging
                                                    Charges</label>
                                                <?php echo form_input([
                                                    'name' => 'packaging_charges',
                                                    'id' => 'packaging_charges',
                                                    'class' => 'form-control',
                                                    'type' => 'number',
                                                    'step' => 'any',
                                                    'value' => set_value('packaging_charges')
                                                ]); ?>
                                                <?php echo form_error('packaging_charges', '<div class="text-danger">', '</div>'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        <?php endif; ?>


                        <?php if ($mode_type !== 'fcl'): ?>
                            <!-- Package Information -->
                            <section style="margin-left:0px;" class="custom-section">
                                <div class="custom-container">
                                    <div class="custom-form-grid">
                                        <div style="padding-bottom:10px; padding-right:2px; padding-left:2px; margin-top:-40px;"
                                             class="row section-container">
                                            <div class="section-label">Package</div>
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped" id="packageTable">
                                                        <thead>
                                                        <tr>
                                                            <th>Quantity</th>
                                                            <th>Package Description</th>
                                                            <th>Gross Weight(kgs)</th>
                                                            <th>Length(cm)</th>
                                                            <th>Width(cm)</th>
                                                            <th>Height(cm)</th>
                                                            <th>Volumetric Weight(kgs)</th>
                                                            <th>Chargeable Weight</th>
                                                            <th>Action</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php for ($i = 0; $i < (count(set_value('amount', [])) ?: 1); $i++): ?>
                                                            <tr>
                                                                <td>
                                                                    <?php echo form_input([
                                                                        'name' => 'amount[' . $i . ']',
                                                                        'class' => 'amount form-control',
                                                                        'type' => 'number',
                                                                        'step' => 'any',
                                                                        'value' => set_value('amount[' . $i . ']', '')
                                                                    ]); ?>
                                                                    <?php echo form_error('amount[' . $i . ']', '<div class="text-danger">', '</div>'); ?>
                                                                </td>
                                                                <td>
                                                                    <textarea
                                                                            name="package_description[<?php echo $i; ?>]"
                                                                            class="custom-textarea"
                                                                            rows="3"><?php echo set_value('package_description[' . $i . ']', ''); ?></textarea>
                                                                    <?php echo form_error('package_description[' . $i . ']', '<div class="text-danger">', '</div>'); ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo form_input([
                                                                        'name' => 'weight[' . $i . ']',
                                                                        'class' => 'weight form-control',
                                                                        'type' => 'number',
                                                                        'step' => 'any',
                                                                        'value' => set_value('weight[' . $i . ']', '')
                                                                    ]); ?>
                                                                    <?php echo form_error('weight[' . $i . ']', '<div class="text-danger">', '</div>'); ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo form_input([
                                                                        'name' => 'length[' . $i . ']',
                                                                        'class' => 'length form-control',
                                                                        'type' => 'number',
                                                                        'step' => 'any',
                                                                        'value' => set_value('length[' . $i . ']', '')
                                                                    ]); ?>
                                                                    <?php echo form_error('length[' . $i . ']', '<div class="text-danger">', '</div>'); ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo form_input([
                                                                        'name' => 'width[' . $i . ']',
                                                                        'class' => 'width form-control',
                                                                        'type' => 'number',
                                                                        'step' => 'any',
                                                                        'value' => set_value('width[' . $i . ']', '')
                                                                    ]); ?>
                                                                    <?php echo form_error('width[' . $i . ']', '<div class="text-danger">', '</div>'); ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo form_input([
                                                                        'name' => 'height[' . $i . ']',
                                                                        'class' => 'height form-control',
                                                                        'type' => 'number',
                                                                        'step' => 'any',
                                                                        'value' => set_value('height[' . $i . ']', '')
                                                                    ]); ?>
                                                                    <?php echo form_error('height[' . $i . ']', '<div class="text-danger">', '</div>'); ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo form_input([
                                                                        'name' => 'weight_vol[' . $i . ']',
                                                                        'class' => 'weight-vol form-control',
                                                                        'type' => 'number',
                                                                        'step' => 'any',
                                                                        'value' => set_value('weight_vol[' . $i . ']', '')
                                                                    ]); ?>
                                                                    <?php echo form_error('weight_vol[' . $i . ']', '<div class="text-danger">', '</div>'); ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo form_input([
                                                                        'name' => 'chargeable_weight[' . $i . ']',
                                                                        'class' => 'chargeable-weight form-control',
                                                                        'type' => 'number',
                                                                        'step' => 'any',
                                                                        'value' => set_value('chargeable_weight[' . $i . ']', '')
                                                                    ]); ?>
                                                                    <?php echo form_error('chargeable_weight[' . $i . ']', '<div class="text-danger">', '</div>'); ?>
                                                                </td>
                                                                <td>
                                                                    <?php if ($i === 0): ?>
                                                                        <button type="button" class="btn btn-primary"
                                                                                onclick="addNormalPackage()">
                                                                            <i class="fa fa-plus"></i>
                                                                        </button>
                                                                    <?php else: ?>
                                                                        <button type="button"
                                                                                class="btn btn-danger remove-package">
                                                                            <i class="fa fa-trash"></i>
                                                                        </button>
                                                                    <?php endif; ?>
                                                                </td>
                                                            </tr>
                                                        <?php endfor; ?>
                                                        </tbody>
                                                    </table>

                                                </div>
                                                <div style="margin-left:-10px; margin-top:20px; margin-bottom:15px;"
                                                     class="col-lg-6 col-sm-12 col-md-6">
                                                    <label style="font-weight:bold;" for="packaging_charges">Packaging
                                                        Charges</label>
                                                    <?php echo form_input([
                                                        'name' => 'packaging_charges',
                                                        'id' => 'packaging_charges',
                                                        'class' => 'amount form-control',
                                                        'type' => 'number',
                                                        'step' => 'any',
                                                        'value' => set_value('packaging_charges')
                                                    ]); ?>
                                                    <?php echo form_error('packaging_charges', '<div class="text-danger">', '</div>'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        <?php endif; ?>

                        <!-- Commercial Value Information -->
                        <section style="margin-left:0px;" class="custom-section">
                            <div class="custom-container">
                                <div class="custom-form-grid">
                                    <div style="padding-bottom:10px; padding-right:2px; padding-left:2px; margin-top:-40px;"
                                         class="row section-container">
                                        <div class="section-label">Commercial Invoice Information (<span
                                                    class="text-danger">This information will be used to generate commercial Invoice*</span>)
                                        </div>
                                        <div class="custom-form-group"
                                             style="margin: 10px; margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); background-color: #f9f9f9;">
                                            <div id="type_selection" style="display: flex; align-items: center;">
                                                <label class="switch"
                                                       style="display: flex; align-items: center; cursor: pointer;">
                                                    <input type="checkbox" name="hasCommercialInvoiceAttachment"
                                                           id="toggleSwitch">
                                                    <span class="slider"></span>
                                                </label>
                                                <span id="toggleLabel"
                                                      style="margin-left: 15px; font-weight:bold; font-size: 13px;">Attach Commercial Invoice</span>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div style="display: none; margin-top:10px; padding-bottom:10px;"
                                                 id="invoice_attachment">
                                                <label style="font-weight:bold; font-size:14px;"
                                                       for="commercial_invoice_file">Attach
                                                    Commercial Invoice</label>
                                                <input class="custom-input" type="file" name="commercial_invoice_file"
                                                       id="commercial_invoice_file">
                                                <?php echo form_error('commercial_invoice_file', '<div class="error-message">', '</div>'); ?>

                                            </div>
                                            <div style="display: block;" id="invoice_input" class="table-responsive">
                                                <table class="table table-bordered table-striped"
                                                       id="commercialItemsTable">
                                                    <thead>
                                                    <tr>
                                                        <th>Quantity</th>
                                                        <th>Item Description</th>
                                                        <th>Declared Value</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    // Retrieve previously set values for quantity, description, and declared value
                                                    $quantities = set_value('commodity_quantity', []);
                                                    $descriptions = set_value('commodity_description', []);
                                                    $declaredValues = set_value('declared_value', []);

                                                    // Check if there are existing items; if not, initialize with one empty row
                                                    $itemCount = max(1, count($quantities)); // Ensure at least one row

                                                    for ($i = 0; $i < $itemCount; $i++): ?>
                                                        <tr>
                                                            <td>
                                                                <?php echo form_input([
                                                                    'name' => "commodity_quantity[$i]", // Change to indexed name
                                                                    'class' => 'amount form-control',
                                                                    'type' => 'number',
                                                                    'step' => 'any',
                                                                    'value' => isset($quantities[$i]) ? $quantities[$i] : ''
                                                                ]); ?>
                                                                <?php echo form_error("commodity_quantity[$i]", '<div class="text-danger">', '</div>'); ?>
                                                            </td>
                                                            <td>
                                                                <textarea
                                                                        name="commodity_description[<?php echo $i; ?>]"
                                                                        class="custom-textarea"
                                                                        rows="3"><?php echo isset($descriptions[$i]) ? $descriptions[$i] : ''; ?></textarea>
                                                                <?php echo form_error("commodity_description[$i]", '<div class="text-danger">', '</div>'); ?>
                                                            </td>
                                                            <td>
                                                                <?php echo form_input([
                                                                    'name' => "declared_value[$i]", // Change to indexed name
                                                                    'class' => 'declared-value form-control',
                                                                    'type' => 'number',
                                                                    'step' => 'any',
                                                                    'value' => isset($declaredValues[$i]) ? $declaredValues[$i] : ''
                                                                ]); ?>
                                                                <?php echo form_error("declared_value[$i]", '<div class="text-danger">', '</div>'); ?>
                                                            </td>
                                                            <td>
                                                                <?php if ($i === 0): ?>
                                                                    <button type="button" class="btn btn-primary"
                                                                            onclick="addCommercialItem()">
                                                                        <i class="fa fa-plus"></i>
                                                                    </button>
                                                                <?php else: ?>
                                                                    <button type="button"
                                                                            class="btn btn-danger remove-commercial-item">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endfor; ?>
                                                    </tbody>
                                                </table>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>


                        <div class="question-container">
                            <label class="custom-checkbox">
                                <span class="checkbox-text">Do you wish to add a pickup for this shipment ?</span>
                                <input type="checkbox" name="hasPickup" id="addPickupCheckbox">
                            </label>
                        </div>

                        <!-- Optional Pickup Section -->
                        <section style="display: none; margin-left: 18px; margin-right:18px;" id="optionalPickupSection"
                                 class="custom-section">
                            <div class="custom-form-grid">
                                <!--Pickup Information-->
                                <div class=" section-container">
                                    <div class="section-label">Pickup Information</div>
                                    <div style="padding-left:15px; padding-right:15px; margin-top:10px;" class="row">
                                        <div class="custom-form-group col-md-6 col-sm-12">
                                            <label for="pickup_date" class="custom-label">Pickup Date:</label>
                                            <input type="text" id="pickup_date" name="pickup_date"
                                                   class="custom-input" placeholder="Choose Date" required>
                                            <?php echo form_error('pickup_date', '<div class="error-message">', '</div>'); ?>
                                        </div>
                                        <div class="custom-form-group col-md-3 col-sm-12">
                                            <label for="pickup_start_time" class="custom-label">Pickup Start
                                                Time:</label>
                                            <input type="text" id="pickup_start_time" name="pickup_start_time"
                                                   class="custom-input pickup_time" placeholder="Choose Start Time"
                                                   required>
                                            <?php echo form_error('pickup_start_time', '<div class="error-message">', '</div>'); ?>
                                        </div>
                                        <div class="custom-form-group col-md-3 col-sm-12">
                                            <label for="pickup_end_time" class="custom-label">Pickup End Time:</label>
                                            <input type="text" id="pickup_end_time" name="pickup_end_time"
                                                   class="custom-input pickup_time" placeholder="Choose End Time"
                                                   required>
                                            <?php echo form_error('pickup_end_time', '<div class="error-message">', '</div>'); ?>
                                        </div>
                                    </div>
                                    <div style="padding-left:15px; padding-right:15px;" class="row">
                                        <div class="col-md-6 col-sm-12">
                                            <div class="custom-form-group">
                                                <label for="pickup_country_id" class="custom-label">Country</label>
                                                <?php

                                                $selected_country_id = isset($user_country->country_id) ? $user_country->country_id : set_value('pickup_country_id');

                                                echo form_dropdown(
                                                    'pickup_country_id',
                                                    array_column($countries, 'short_name', 'country_id'),
                                                    $selected_country_id,
                                                    [
                                                        'id' => 'pickup_country_id',
                                                        'class' => 'custom-select',
                                                        'style' => 'width: 100%;'
                                                    ]
                                                );
                                                ?>
                                                <?php echo form_error('pickup_country_id', '<div class="error-message">', '</div>'); ?>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-12">
                                            <div class="custom-form-group">
                                                <label for="pickup_state_id" class="custom-label">State</label>
                                                <select style="width:100%;" name="pickup_state_id" id="pickup_state_id"
                                                        class="custom-select">
                                                    <option value="" selected>Select State</option>
                                                </select>
                                                <?php echo form_error('pickup_state_id', '<div class="error-message">', '</div>'); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="padding-left:15px; padding-right:15px;" class="row">
                                        <div class="col-md-6 col-sm-12">
                                            <div class="custom-form-group">
                                                <label for="pickup_address" class="custom-label">Address</label>
                                                <textarea id="pickup_address" name="pickup_address"
                                                          class="custom-textarea"
                                                          autocomplete="off"
                                                          rows="3"
                                                          placeholder="Enter your address here..."><?php echo set_value('pickup_address'); ?></textarea>
                                                <?php echo form_error('pickup_address', '<div class="error-message">', '</div>'); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-12">
                                            <div class="custom-form-group">
                                                <div id="address_type" style="display: flex; gap: 20px;">
                                                    <label>
                                                        <input type="radio" name="pickup_address_type"
                                                               value="zip_code" checked>
                                                        Zip code
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="pickup_address_type"
                                                               value="postal_code"> Postal code
                                                    </label>
                                                </div>
                                                <?php echo form_input(['id' => 'pickup_zipcode', 'name' => 'pickup_zipcode', 'type' => 'text', 'class' => 'custom-input', 'placeholder' => 'Zipcode', 'value' => set_value('zipcode'), 'autocomplete' => 'off']); ?>
                                                <?php echo form_error('pickup_zipcode', '<div class="error-message">', '</div>'); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="custom-form-group">
                                            <label for="pickup_vehicle_type" class="custom-label">Vehicle Type</label>
                                            <select name="pickup_vehicle_type" id="vehicle_type"
                                                    class="custom-select">
                                                <option value="truck">Truck</option>
                                                <option value="van">Van</option>
                                                <option value="motorcycle">Motorcycle</option>
                                                <option value="motorcycle">Other</option>
                                            </select>
                                            <?php echo form_error('pickup_vehicle_type', '<div class="error-message">', '</div>'); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="custom-form-group">
                                            <label for="driver_id" class="custom-label">Driver</label>
                                            <?php
                                            // Prepare the dropdown options by concatenating first name and last name
                                            $drivers_dropdown = array_column($drivers, 'firstname', 'staffid');
                                            foreach ($drivers as $driver) {
                                                $drivers_dropdown[$driver['staffid']] = $driver['firstname'] . ' ' . $driver['lastname'];
                                            }
                                            echo form_dropdown('pickup_driver_id', $drivers_dropdown, set_value('pickup_driver_id'), ['id' => 'driver_id', 'class' => 'custom-select', 'style' => 'width: 100%;']);
                                            ?>
                                            <?php echo form_error('driver_id', '<div class="error-message">', '</div>'); ?>
                                        </div>
                                    </div>
                                </div>
                                <!--End Of Pickup Information-->

                                <!--Recipient Information-->
                                <div class="row section-container">
                                    <div class="section-label">Contact Person</div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="custom-form-group">
                                            <label for="pickup_contact_first_name" class="custom-label">First
                                                Name</label>
                                            <input name="pickup_contact_first_name" id="pickup_contact_first_name"
                                                   type="text"
                                                   class="custom-input"
                                                   value="<?php echo set_value('pickup_contact_first_name'); ?>"
                                                   placeholder="First Name">
                                            <?php echo form_error('pickup_contact_first_name', '<div class="error-message">', '</div>'); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="custom-form-group">
                                            <label for="pickup_contact_last_name" class="custom-label">Last Name</label>
                                            <input name="pickup_contact_last_name" id="pickup_contact_last_name"
                                                   type="text"
                                                   class="custom-input"
                                                   value="<?php echo set_value('pickup_contact_last_name'); ?>"
                                                   placeholder="Last Name">
                                            <?php echo form_error('pickup_contact_last_name', '<div class="error-message">', '</div>'); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="custom-form-group">
                                            <?php
                                            $pickup_country_code = isset($user_country->calling_code) ? '+' . $user_country->calling_code : set_value('pickup_country_code');
                                            ?>
                                            <label for="pickup_contact_phone_number" class="custom-label">Phone</label>
                                            <div class="phone-input-group">
                                                <!-- Country Code Input -->
                                                <input style="border-right:transparent;" type="text"
                                                       id="pickup_country_code" name="pickup_country_code"
                                                       class="country-code"
                                                       value="<?php echo $pickup_country_code; ?>" readonly>
                                                <input name="pickup_contact_phone_number"
                                                       id="pickup_contact_phone_number"
                                                       type="text"
                                                       class="phone"
                                                       value="<?php echo set_value('pickup_contact_phone_number'); ?>"
                                                       placeholder="Phone Number">
                                            </div>
                                            <!-- Hidden Field for Combined Number -->
                                            <input type="hidden" id="pickup_full_phone_number"
                                                   name="pickup_full_phone_number" value="">
                                            <?php echo form_error('pickup_contact_phone_number', '<div class="error-message">', '</div>'); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="custom-form-group">
                                            <label for="pickup_contact_email" class="custom-label">Email</label>
                                            <input name="pickup_contact_email" id="pickup_contact_email" type="text"
                                                   class="custom-input"
                                                   value="<?php echo set_value('pickup_contact_email'); ?>"
                                                   placeholder="Email">
                                            <?php echo form_error('pickup_contact_email', '<div class="error-message">', '</div>'); ?>
                                        </div>
                                    </div>
                                </div>
                                <!--End of Recipient Information-->
                            </div>
                        </section>

                        <div class="col-md-12">
                            <button style="margin-left:20px; text-decoration: none; border:2px solid black;"
                                    type="submit"
                                    class="custom-button">Create Shipment
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>

<!-- Scripts should be at the end of the body -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        //intialize selct2 on countries and states...
        $('#contact_country_id').select2({});
        $('#contact_state_id').select2({});

        $('#recipient_country_id').select2({});
        $('#recipient_state_id').select2({});

        $('#recipient_contact_country_id').select2({});
        $('#recipient_contact_state_id').select2({});

        $('#sender_country_id').select2({});
        $('#sender_state_id').select2({});

        $('#pickup_country_id').select2({});
        $('#pickup_state_id').select2({});

        $('#driver_id').select2({});

        const companySection = document.getElementById('company_section');
        const senderSection = document.getElementById('sender_section');
        const recipientSection = document.getElementById('recipient_section');
        const recipientCompanySection = document.getElementById('recipient_company_section');

        const senderTypeRadios = document.querySelectorAll('input[name="sender_type"]');
        const recipientTypeRadios = document.querySelectorAll('input[name="recipient_type"]');


        const showCompanySection = <?php echo isset($show_company_section) && $show_company_section ? 'true' : 'false'; ?>;
        const showRecipientCompanySection = <?php echo isset($show_recipient_company_section) && $show_recipient_company_section ? 'true' : 'false'; ?>;

        if (showCompanySection) {
            senderSection.style.display = 'none';
            companySection.style.display = 'block';
            document.querySelector('input[name="sender_type"][value="company"]').checked = true;
        }

        if (showRecipientCompanySection) {
            recipientSection.style.display = 'none';
            recipientCompanySection.style.display = 'block';
            document.querySelector('input[name="recipient_type"][value="company"]').checked = true;
        }

        // Function to show/hide sections based on selected radio button
        function toggleSections() {
            if (document.querySelector('input[name="sender_type"]:checked').value === 'company') {
                console.log('company');
                companySection.style.display = 'block';
                senderSection.style.display = 'none';
            } else {
                console.log('no company');
                senderSection.style.display = 'block';
                companySection.style.display = 'none';
            }
        }

        // Function to show/hide sections based on selected radio button
        function toggleRecipientSections() {
            if (document.querySelector('input[name="recipient_type"]:checked').value === 'company') {
                recipientCompanySection.style.display = 'block';
                recipientSection.style.display = 'none';
            } else {
                console.log('no company');
                recipientSection.style.display = 'block';
                recipientCompanySection.style.display = 'none';
            }
        }

        // Initial check to set the correct section visibility on page load
        toggleSections();

        // Add event listeners to the radio buttons
        senderTypeRadios.forEach(radio => {
            radio.addEventListener('change', toggleSections);
        });

        // Add event listeners to the radio buttons
        recipientTypeRadios.forEach(radio => {
            radio.addEventListener('change', toggleRecipientSections);
        });

        // Get the state dropdown elements
        const recipientState = $('#recipient_state_id');
        const senderState = $('#sender_state_id');
        const pickupState = $('#pickup_state_id');
        const contactState = $('#contact_state_id');
        const recipientContactState = $('#recipient_contact_state_id');
        const recipientCountry = $('#recipient_country_id');
        const senderCountry = $('#sender_country_id');
        const pickupCountry = $('#pickup_country_id');
        const contactCountry = $('#contact_country_id');
        const recipientContactCountry = $('#recipient_contact_country_id');

        // Preselected values from the backend (PHP)
        const recipientCountryId = <?php echo $recipient_country_id ?? 'recipientCountry.val() '; ?>;
        const senderCountryId = <?php echo $sender_country_id ?? 'senderCountry.val()'; ?>;
        const pickupCountryId = <?php echo $pickup_country_id ?? 'pickupCountry.val()'; ?>;
        const contactCountryId = <?php echo $contact_country_id ?? 'contactCountry.val()'; ?>;
        const recipientContactCountryId = <?php echo $recipient_contact_country_id ?? 'recipientContactCountry.val()'; ?>;


        // Load states for pre-selected countries (if any)
        if (recipientCountryId) {
            const recipientStateId = "<?php echo set_value('recipient_state_id') ?: 'null'; ?>";
            getStates(recipientCountryId, 'recipient', recipientState, recipientStateId === 'null' ? null : recipientStateId);
        }

        if (recipientContactCountryId) {
            const recipientContactStateId = "<?php echo set_value('recipient_contact_state_id') ?: 'null'; ?>";
            getStates(recipientContactCountryId, 'recipient_contact', recipientContactState, recipientContactStateId === 'null' ? null : recipientContactStateId);
        }

        if (senderCountryId) {
            const senderStateId = "<?php echo set_value('sender_state_id') ?: 'null'; ?>";
            getStates(senderCountryId, 'sender', senderState, senderStateId === 'null' ? null : senderStateId);
        }
        if (pickupCountryId) {
            const pickupStateId = "<?php echo set_value('pickup_state_id') ?: 'null'; ?>";
            getStates(pickupCountryId, 'pickup', pickupState, pickupStateId === 'null' ? null : pickupStateId);
        }
        if (contactCountryId) {
            const contactStateId = "<?php echo set_value('contact_state_id') ?: 'null'; ?>";
            getStates(contactCountryId, 'contact', contactState, contactStateId === 'null' ? null : contactStateId);
        }

        function getStates(countryId, section, stateDropdown, stateId) {
            if (countryId) {
                $.ajax({
                    url: '<?php echo admin_url("courier/states"); ?>',
                    type: "POST",
                    data: {country_id: countryId},
                    dataType: "json",
                    success: function (data) {
                        // Log the received data to see if it’s as expected
                        console.log('Received states data:', data);

                        if (section === 'recipient') {
                            $('#recipient_country_code').val(`+${data.country_code}`);
                        }

                        if (section === 'recipient_contact') {
                            $('#recipient_contact_country_code').val(`+${data.country_code}`);
                        }

                        if (section === 'sender') {
                            $('#sender_country_code').val(`+${data.country_code}`);
                        }
                        if (section === 'contact') {
                            $('#contact_country_code').val(`+${data.country_code}`);
                        }
                        if (section === 'pickup') {
                            $('#pickup_country_code').val(`+${data.country_code}`);
                        }

                        // Clear current options
                        stateDropdown.empty();

                        // Populate dropdown with new data
                        $.each(data.states, function (key, value) {
                            var selected = (stateId && value.id == stateId) ? 'selected' : '';
                            stateDropdown.append('<option value="' + value.id + '" ' + selected + '>' + value.name + '</option>');
                        });
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        // Log detailed error information
                        console.error('Error retrieving states:', jqXHR.responseText);
                        console.error('Text Status:', textStatus);
                        console.error('Error Thrown:', errorThrown);
                        alert('Error retrieving states. Check the console for more details.');
                    }
                });
            } else {
                // Handle empty country case
                stateDropdown.empty();
                stateDropdown.append('<option value="">Select State</option>');
            }
        }


        // Attach change event listeners to the country dropdowns
        recipientCountry.change(function () {
            const countryId = $(this).val();
            getStates(countryId, 'recipient', recipientState, null);
        });

        recipientContactCountry.change(function () {
            const countryId = $(this).val();
            getStates(countryId, 'recipient_contact', recipientContactState, null);
        });

        senderCountry.change(function () {
            const countryId = $(this).val();
            getStates(countryId, 'sender', senderState, null);
        });

        pickupCountry.change(function () {
            const countryId = $(this).val();
            getStates(countryId, 'pickup', pickupState, null);
        });

        contactCountry.change(function () {
            const countryId = $(this).val();
            getStates(countryId, 'contact', contactState, null);
        });

        flatpickr("#pickup_date", {
            dateFormat: "Y-m-d",
            minDate: "today", // Prevent selection of past dates
            disable: [
                function (date) {
                    // Disable Saturday (6) and Sunday (0)
                    return (date.getDay() === 6 || date.getDay() === 0);
                }
            ],
            disableMobile: true // Forces desktop UI even on mobile devices
        });

        // Initialize Flatpickr for time range selection
        flatpickr(".pickup_time", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "h:i K",
            time_24hr: false, // Use 12-hour time format with AM/PM
            defaultHour: 9,  // Set the default hour to something other than 12
            defaultMinute: 0,  // Set the default minute
            minuteIncrement: 30,  // Allow increments every 30 minutes
            disableMobile: true // Forces desktop UI even on mobile devices
        });


    });
</script>
</body>
</html>
