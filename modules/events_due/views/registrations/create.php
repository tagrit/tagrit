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

                    <?php echo form_open('admin/events_due/registrations/store', [
                        'id' => 'register-for-event-form',
                        'enctype' => 'multipart/form-data'
                    ]); ?>

                    <!-- Form Container -->
                    <div class="col-md-12">
                        <div class="card mtop15">
                            <div class="card-body">
                                <div class="row">
                                    <!-- Client Details -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="first_name" class="control-label">First Name *</label>
                                            <input type="text" id="first_name" name="first_name"
                                                   class="form-control"
                                                   required>
                                        </div>
                                    </div>

                                    <!-- Client Details -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="last_name" class="control-label">Last Name *</label>
                                            <input type="text" id="last_name" name="last_name" class="form-control"
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
                                            <label for="phone_number" class="control-label">Phone Number *</label>
                                            <input type="text" id="phone_number" name="phone_number"
                                                   class="form-control"
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
                                            <label for="event_name_id"><?php echo _l('Event'); ?></label>
                                            <select id="event_name_id" name="event_name_id"
                                                    data-live-search="true"
                                                    class="form-control selectpicker"
                                                    data-none-selected-text="<?php echo _l('Dropdown Non Selected Text'); ?>">
                                                <?php foreach ($events as $event): ?>
                                                    <option value="<?= htmlspecialchars($event->event_name_id); ?>"><?= htmlspecialchars($event->event_name); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Location and Venue -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="location_id" class="control-label">Location *</label>
                                            <select id="location_id" name="location_id"
                                                    class="form-control selectpicker"
                                                    data-live-search="true" required>
                                                <option value="">Select Location</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="venue_id" class="control-label">Venue *</label>
                                            <select id="venue_id" name="venue_id" class="form-control selectpicker"
                                                    data-live-search="true" required>
                                                <option value="">Select Venue</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Duration -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="duration" class="control-label">Duration *</label>
                                            <select id="duration" name="duration" class="form-control selectpicker"
                                                    data-live-search="true" required>
                                                <option value="">Select Period</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="setup" class="control-label">Setup*</label>
                                            <select id="setup" name="setup" class="form-control selectpicker"
                                                    data-live-search="true" required>
                                                <option value="">Select Type</option>
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
                    <?php echo form_close(); ?>
                </div>

            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(document).ready(function () {

        // When event is selected, fetch locations
        $('#event_name_id').change(function () {
            const eventNameId = $(this).val();

            if (eventNameId) {
                $.ajax({
                    url: '<?= base_url('admin/events_due/locations') ?>',
                    type: 'POST',
                    data: {event_name_id: eventNameId},
                    dataType: 'json',
                    success: function (response) {
                        console.log('Response:', response);

                        $('#location_id').empty().append('<option value="">Select Location</option>');

                        if (response.success) {
                            $.each(response.data, function (key, value) {
                                $('#location_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                            });
                        } else {
                            console.error('Error:', response.error);
                            alert('Error: ' + response.error);
                        }

                        $('#location_id').selectpicker('refresh');
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error:', status, error, xhr);
                        alert('Failed to fetch locations. Please try again.');
                    }
                });
            } else {
                $('#location_id').empty().append('<option value="">Select Location</option>');
                $('#location_id').selectpicker('refresh');
            }
        });


        // When location is selected, fetch venues
        $('#location_id').change(function () {
            const locationId = $(this).val();

            if (locationId) {
                $.ajax({
                    url: '<?= base_url('admin/events_due/venues') ?>',
                    type: 'POST',
                    data: {location_id: locationId},
                    dataType: 'json',
                    success: function (response) {

                        $('#venue_id').empty().append('<option value="">Select Venue</option>');

                        if (response.success) {
                            $.each(response.data, function (key, value) {
                                $('#venue_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                            });
                        } else {
                            console.error('Error:', response.error);
                            alert('Error: ' + response.error);
                        }

                        $('#venue_id').selectpicker('refresh');
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error:', status, error, xhr);
                        alert('Failed to fetch locations. Please try again.');
                    }
                });
            } else {
                $('#venue_id').empty().append('<option value="">Select Location</option>');
                $('#venue_id').selectpicker('refresh');
            }
        });

        // when venue and location is set choose setup
        $('#venue_id').change(function () {

            const eventNameId = $('#event_name_id').val();
            const locationId = $('#location_id').val();
            const venueId = $(this).val();

            if (venueId) {
                $.ajax({
                    url: '<?= base_url('admin/events_due/setups') ?>',
                    type: 'POST',
                    data: {
                        event_name_id: eventNameId,
                        location_id: locationId,
                        venue_id: venueId,
                    },
                    dataType: 'json',
                    success: function (response) {
                        $('#setup').empty().append('<option value="">Select Setup</option>');
                        $.each(response.data, function (key, value) {
                            $('#setup').append('<option value="' + value.setup + '">' + value.setup + '</option>');
                        });
                        $('#setup').selectpicker('refresh');
                    }
                });
            }
        });

        // when venue and location is set choose setup
        $('#venue_id').change(function () {

            const eventNameId = $('#event_name_id').val();
            const locationId = $('#location_id').val();
            const venueId = $(this).val();

            if (venueId) {
                $.ajax({
                    url: '<?= base_url('admin/events_due/durations') ?>',
                    type: 'POST',
                    data: {
                        event_name_id: eventNameId,
                        location_id: locationId,
                        venue_id: venueId,
                    },
                    dataType: 'json',
                    success: function (response) {
                        $('#duration').empty().append('<option value="">Select Setup</option>');
                        $.each(response.data, function (key, value) {
                            $('#duration').append('<option value="' + value + '">' + value + '</option>');
                        });
                        $('#duration').selectpicker('refresh');
                    }
                });
            }
        });
    });
</script>
