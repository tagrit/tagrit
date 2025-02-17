<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
    <div id="wrapper">
        <div class="content">
            <div class="section-header">
                <h1>Events</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item">Event Registrations</div>
                </div>
            </div>
            <div class="panel_s">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="no-margin" style="color: #34395e;font-weight: 600;">Event Registration Form</h4>
                            <hr class="hr-panel-heading"/>
                        </div>

                        <!-- Form Container -->
                        <div class="col-md-12">
                            <div class="card mtop15">
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Client Details -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="fullname" class="control-label">First Name *</label>
                                                <input type="text" id="fullname" name="fullname" class="form-control"
                                                       required>
                                            </div>
                                        </div>

                                        <!-- Client Details -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="fullname" class="control-label">Last Name *</label>
                                                <input type="text" id="fullname" name="fullname" class="form-control"
                                                       required>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email" class="control-label">Email Address *</label>
                                                <input type="email" id="email" name="email" class="form-control"
                                                       required>
                                            </div>
                                        </div>


                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email" class="control-label">Phone Number *</label>
                                                <input type="email" id="email" name="email" class="form-control"
                                                       required>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="organization" class="control-label">Organization *</label>
                                                <input type="text" id="organization" name="organization"
                                                       class="form-control" required>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="setup"><?php echo _l('Event'); ?></label>
                                                <select id="setup" name="setup"
                                                        data-live-search="true"
                                                        class="form-control selectpicker"
                                                        data-none-selected-text="<?php echo _l('Dropdown Non Selected Text'); ?>">
                                                    <option value="Physical">DSAC 101: DATA ANALYSIS TRAINING</option>
                                                    <option value="Virtual">DSAC 102: DATA ANALYSIS TRAINING</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Location and Venue -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="location" class="control-label">Location *</label>
                                                <select id="location" name="location" class="form-control selectpicker"
                                                        data-live-search="true" required>
                                                    <option value="">Select Location</option>
                                                    <option value="Diani">Diani</option>
                                                    <option value="Mombasa">Mombasa</option>
                                                    <option value="Machakos">Machakos</option>
                                                    <option value="Nakuru">Nakuru</option>
                                                    <option value="Naivasha">Naivasha</option>
                                                    <option value="Kisumu">Kisumu</option>
                                                    <option value="Thika">Thika</option>
                                                    <option value="Eldoret">Eldoret</option>
                                                    <option value="Dubai">Dubai</option>
                                                    <option value="Arusha">Arusha</option>
                                                    <option value="Malaysia">Malaysia</option>
                                                    <option value="Singapore">Singapore</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="venue" class="control-label">Venue *</label>
                                                <select id="venue" name="venue" class="form-control selectpicker"
                                                        data-live-search="true" required>
                                                    <option value="">Select Venue</option>
                                                    <!-- Dynamic venues will be populated via JavaScript -->
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Duration -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="duration" class="control-label">Duration *</label>
                                                <select id="duration" name="duration" class="form-control selectpicker"
                                                        data-live-search="true"
                                                        required>
                                                    <option value="">Select Duration</option>
                                                    <option value="5days">1 week - 5 Days (Monday to Friday)</option>
                                                    <option value="7days">1 week - 7 Days (Sunday to Saturday)</option>
                                                    <option value="10days">Two Weeks - 10 Days</option>
                                                    <option value="14days">Two Weeks - 14 Days</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="venue" class="control-label">Training Type *</label>
                                                <select id="venue" name="venue" class="form-control selectpicker"
                                                        data-live-search="true" required>
                                                    <option value="">Select Venue</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-md-12 mtop15">
                            <button type="submit" class="btn btn-primary btn-block">Submit Registration</button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                $(document).ready(function () {
                    // Venue data
                    const venues = {
                        'Mombasa': ['Sarova Hotel', 'Voyager Hotel'],
                        'Diani': ['Baobab Hotel'],
                        'Machakos': ['Seo Hotel', 'Maanzoni Lodge'],
                        'Naivasha': ['Blooming Suites Hotel', 'Eseriani Hotel'],
                        'Nakuru': ['Sarova Woodlands Hotel', 'Ole Ken Hotel'],
                        'Kisumu': ['Sarova Imperial'],
                        'Thika': ['The Luke Hotel'],
                        'Arusha': ['Mt. Meru Hotel'],
                        'Singapore': ['Ibis Bencoolen']
                    };

                    // Initialize selectpicker
                    $('.selectpicker').selectpicker();

                    // Update venues when location changes
                    $('#location').change(function () {
                        const location = $(this).val();
                        const venueSelect = $('#venue');
                        venueSelect.empty();
                        venueSelect.append('<option value="">Select Venue</option>');

                        if (venues[location]) {
                            venues[location].forEach(venue => {
                                venueSelect.append(`<option value="${venue}">${venue}</option>`);
                            });
                        }

                        venueSelect.selectpicker('refresh');
                    });

                    // Update cost when duration changes
                    $('#duration').change(function () {
                        const duration = $(this).val();
                        const cost = duration.includes('weeks') ? '159,850' : '99,850';
                        $('#cost_display').text(`Training Cost: KES ${cost} per person`);
                    });

                    // Form validation
                    $('form').on('submit', function (e) {
                        e.preventDefault();

                        // Add your form submission logic here
                        // You can use FormData or serialize the form data
                        const formData = new FormData(this);

                        // Example AJAX submission
                        /*
                        $.ajax({
                            url: admin_url + 'your_controller/save_training',
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                alert_float('success', 'Training registration submitted successfully');
                            },
                            error: function(xhr, status, error) {
                                alert_float('danger', 'Error submitting registration');
                            }
                        });
                        */
                    });
                });
            </script>


        </div>
    </div>
<?php init_tail(); ?>