<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php load_courier_styles(); ?>

<?php
echo '<!-- Include Flatpickr CSS -->';
echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">';

// Include Select2 CSS
echo '<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />';

?>
<style>
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

<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php echo form_open('admin/courier/pickups/store', ['id' => 'create-pickup-form']); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div style="margin-bottom:25px">
                            <a style="text-decoration:  none; border:2px solid black;" class="custom-button"
                               href="<?php echo admin_url('courier/pickups/main'); ?>">
                                <i class="fa fa-arrow-left" aria-hidden="true"></i>
                                <span style="margin-left:10px;">Pickup Dashboard</span>
                            </a>
                        </div>

                        <div class="flex-container">
                            <h4 class="font-bold m-0">Create Pickup</h4>
                            <a style="text-decoration:  none; border:2px solid black;" class="custom-button"
                               href="<?php echo admin_url('courier/pickups/index'); ?>">
                                View Pickups
                            </a>
                        </div>
                        <hr class="hr-panel-heading"/>

                        <section class="custom-section">
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
                                            <label for="pickup_start_time" class="custom-label">Pickup Start Time:</label>
                                            <input type="text" id="pickup_start_time" name="pickup_start_time"
                                                   class="custom-input pickup_time" placeholder="Choose Start Time" required>
                                            <?php echo form_error('pickup_start_time', '<div class="error-message">', '</div>'); ?>
                                        </div>
                                        <div class="custom-form-group col-md-3 col-sm-12">
                                            <label for="pickup_end_time" class="custom-label">Pickup End Time:</label>
                                            <input type="text" id="pickup_end_time" name="pickup_end_time"
                                                   class="custom-input pickup_time" placeholder="Choose End Time" required>
                                            <?php echo form_error('pickup_end_time', '<div class="error-message">', '</div>'); ?>
                                        </div>
                                    </div>
                                    <div style="padding-left:15px; padding-right:15px;" class="row">
                                        <div class="col-md-6 col-sm-12">
                                            <div class="custom-form-group">
                                                <label for="country_id" class="custom-label">Country</label>
                                                <?php echo form_dropdown('country_id', array_column($countries, 'short_name', 'country_id'), set_value('country_id'), ['id' => 'country_id', 'class' => 'custom-select']); ?>
                                                <?php echo form_error('country_id', '<div class="error-message">', '</div>'); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-12">
                                            <div class="custom-form-group">
                                                <label for="state_id" class="custom-label">State</label>
                                                <select name="state_id" id="state_id"
                                                        class="custom-select">
                                                    <option value="">Select State</option>
                                                </select>
                                                <?php echo form_error('state_id', '<div class="error-message">', '</div>'); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="padding-left:15px; padding-right:15px;" class="row">
                                        <div class="col-md-6 col-sm-12">
                                            <div class="custom-form-group">
                                                <label for="address" class="custom-label">Address</label>
                                                <textarea id="address" name="address"
                                                          class="custom-textarea"
                                                          rows="3"
                                                          placeholder="Enter your address here..."><?php echo set_value('address'); ?></textarea>
                                                <?php echo form_error('address', '<div class="error-message">', '</div>'); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-12">
                                            <div class="custom-form-group">
                                                <div id="address_type" style="display: flex; gap: 20px;">
                                                    <label>
                                                        <input type="radio" name="address_type"
                                                               value="zip_code" checked>
                                                        Zipcode
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="address_type"
                                                               value="postal_code"> Postal
                                                        Address
                                                    </label>
                                                </div>
                                                <?php echo form_input(['id' => 'zipcode', 'name' => 'zipcode', 'type' => 'text', 'class' => 'custom-input', 'placeholder' => 'Zipcode', 'value' => set_value('zipcode')]); ?>
                                                <?php echo form_error('zipcode', '<div class="error-message">', '</div>'); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="custom-form-group">
                                            <label for="vehicle_type" class="custom-label">Vehicle Type</label>
                                            <select name="vehicle_type" id="vehicle_type"
                                                    class="custom-select">
                                                <option value="truck">Truck</option>
                                                <option value="van">Van</option>
                                                <option value="motorcycle">Motorcycle</option>
                                                <option value="other">Other</option>
                                            </select>
                                            <?php echo form_error('vehicle_type', '<div class="error-message">', '</div>'); ?>
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
                                            echo form_dropdown('driver_id', $drivers_dropdown, set_value('driver_id'), ['id' => 'driver_id', 'class' => 'custom-select', 'style' => 'width: 100%;']);
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
                                            <label for="contact_first_name" class="custom-label">First Name</label>
                                            <input name="contact_first_name" id="contact_first_name" type="text"
                                                   class="custom-input"
                                                   value="<?php echo set_value('contact_first_name'); ?>"
                                                   placeholder="First Name">
                                            <?php echo form_error('contact_first_name', '<div class="error-message">', '</div>'); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="custom-form-group">
                                            <label for="contact_last_name" class="custom-label">Last Name</label>
                                            <input name="contact_last_name" id="contact_last_name" type="text"
                                                   class="custom-input"
                                                   value="<?php echo set_value('contact_last_name'); ?>"
                                                   placeholder="Last Name">
                                            <?php echo form_error('contact_last_name', '<div class="error-message">', '</div>'); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="custom-form-group">
                                            <label for="contact_phone_number" class="custom-label">Phone</label>
                                            <input name="contact_phone_number" id="contact_phone_number" type="text"
                                                   class="custom-input"
                                                   value="<?php echo set_value('contact_phone_number'); ?>"
                                                   placeholder="Phone Number">
                                            <?php echo form_error('contact_phone_number', '<div class="error-message">', '</div>'); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="custom-form-group">
                                            <label for="email" class="custom-label">Email</label>
                                            <input name="contact_email" id="email" type="text"
                                                   class="custom-input"
                                                   value="<?php echo set_value('contact_email'); ?>"
                                                   placeholder="Email">
                                            <?php echo form_error('contact_email', '<div class="error-message">', '</div>'); ?>
                                        </div>
                                    </div>

                                </div>
                                <!--End of Recipient Information-->
                            </div>
                        </section>
                        <button type="submit" class="custom-button">Add pickup</button>
                    </div>
                    </section>
                </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div
</div>
<?php init_tail(); ?>

<!-- Scripts should be at the end of the body -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<script>
    $(document).ready(function () {

        $('#country_id').select2({});
        $('#state_id').select2({});

        $('#driver_id').select2({});

        const state = $('#state_id');
        getStates(1, state)

        function getStates(countryId, state) {
            if (countryId) {
                $.ajax({
                    url: '<?php echo admin_url("courier/states"); ?>',
                    type: "POST",
                    data: {country_id: countryId},
                    dataType: "json",
                    success: function (data) {
                        state.empty();
                        $.each(data.states, function (key, value) {
                            $('#state_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    },
                    error: function () {
                        alert('Error retrieving states');
                    }
                });
            } else {
                state.empty();
                state.append('<option value="">Select State</option>');
            }
        }

        $('#country_id').change(function () {
            const countryId = $(this).val();
            getStates(countryId, state);
        });

        flatpickr("#pickup_date", {
            dateFormat: "Y-m-d",
            minDate: "today", // Prevent selection of past dates
            disable: [
                function(date) {
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

