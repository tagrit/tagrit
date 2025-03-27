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
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="events">Event:</label>
                                            <div class="input-group">
                                                <select class="form-control selectpicker no-border-right"
                                                        data-live-search="true" name="event_id" id="events" required>
                                                    <?php if (!empty($events)): ?>
                                                        <?php foreach ($events as $index => $event): ?>
                                                            <option value="<?= htmlspecialchars($event->id) ?>"
                                                                <?= set_value('event_id') == $event->id ? 'selected' : '' ?>>
                                                                <?= strlen($event->name) > 90 ? htmlspecialchars(substr($event->name, 0, 27)) . '...' : htmlspecialchars($event->name) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <option value="" disabled selected>No events available</option>
                                                    <?php endif; ?>
                                                </select>
                                                <div class="input-group-append">
                                                    <button style="margin-top:0px;" type="button"
                                                            class="btn btn-default no-border-left"
                                                            data-toggle="modal" data-target="#newEventModal">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <?php echo form_error('event_id', '<div class="error-message">', '</div>'); ?>
                                        </div>
                                    </div>

                                    <!-- Client Details -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="organization" class="control-label">Organization *</label>
                                            <input type="text" id="organization" required name="organization"
                                                   class="form-control" value="<?= set_value('organization'); ?>">
                                            <?php echo form_error('organization', '<div class="error-message">', '</div>'); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Client Details -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="location_id" class="control-label">Location*</label>
                                            <select id="location_id" name="location_id"
                                                    class="form-control selectpicker"
                                                    data-live-search="true" required>
                                                <option value="">Select Location</option>
                                            </select>
                                            <?php echo form_error('location_id', '<div class="error-message">', '</div>'); ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="venue_id" class="control-label">Venue*</label>
                                            <select id="venue_id" name="venue_id" class="form-control selectpicker"
                                                    data-live-search="true" required>
                                                <option value="">Select Venue</option>
                                            </select>
                                            <?php echo form_error('venue_id', '<div class="error-message">', '</div>'); ?>
                                        </div>
                                    </div>


                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="start_date" class="control-label">Start Date*</label>
                                            <input type="date" id="start_date" name="start_date" class="form-control"
                                                   required value="<?= set_value('start_date'); ?>">
                                            <?php echo form_error('start_date', '<div class="error-message">', '</div>'); ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="end_date" class="control-label">End Date*</label>
                                            <input type="date" id="end_date" name="end_date" class="form-control"
                                                   value="<?= set_value('end_date'); ?>"
                                                   required>
                                            <?php echo form_error('end_date', '<div class="error-message">', '</div>'); ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="no_of_delegates" class="control-label">Number of
                                                Delegates*</label>
                                            <input type="number" id="no_of_delegates" name="no_of_delegates"
                                                   class="form-control"
                                                   value="<?= set_value('no_of_delegates'); ?>"
                                                   required>
                                            <?php echo form_error('no_of_delegates', '<div class="error-message">', '</div>'); ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="charges_per_delegate" class="control-label">Charges Per
                                                Delegates*</label>
                                            <input type="number" id="charges_per_delegate" name="charges_per_delegate"
                                                   class="form-control"
                                                   value="<?= set_value('charges_per_delegate'); ?>"
                                                   required>
                                            <?php echo form_error('charges_per_delegate', '<div class="error-message">', '</div>'); ?>
                                        </div>
                                    </div>


                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="setup" class="control-label">Setup*</label>
                                            <select id="setup" name="setup" class="form-control selectpicker"
                                                    data-live-search="true" required>
                                                <option value="">Select Setup</option>
                                                <option value="Physical" <?= set_value('setup') == 'Physical' ? 'selected' : '' ?>>
                                                    Physical
                                                </option>
                                                <option value="Virtual" <?= set_value('setup') == 'Virtual' ? 'selected' : '' ?>>
                                                    Virtual
                                                </option>
                                            </select>
                                            <?php echo form_error('setup', '<div class="error-message">', '</div>'); ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="setup" class="control-label">Type*</label>
                                            <select id="type" name="type" class="form-control selectpicker"
                                                    data-live-search="true" required>
                                                <option value="">Select Type</option>
                                                <option value="Local" <?= set_value('type') == 'Local' ? 'selected' : '' ?>>
                                                    Local
                                                </option>
                                                <option value="International" <?= set_value('type') == 'International' ? 'selected' : '' ?>>
                                                    International
                                                </option>
                                            </select>
                                            <?php echo form_error('type', '<div class="error-message">', '</div>'); ?>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="division" class="control-label">Division</label>
                                            <input type="text" id="division" name="division" class="form-control"
                                                   placeholder="Enter division" value="<?= set_value('division'); ?>"
                                                   required>
                                            <?php echo form_error('division', '<div class="error-message">', '</div>'); ?>

                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="revenue" class="control-label">Revenue</label>
                                            <input type="number" id="revenue" name="revenue" class="form-control"
                                                   placeholder="Enter revenue" value="<?= set_value('revenue'); ?>"
                                                   required>
                                            <?php echo form_error('revenue', '<div class="error-message">', '</div>'); ?>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="custom-label">Delegates Details <span
                                                    style="font-size:11px; color:red;">(*The first delegate will be used as the primary contact)</span></label>
                                        <div id="delegates-container">
                                            <?php if (!empty($old_input['delegates'])): ?>
                                                <?php foreach ($old_input['delegates'] as $key => $delegate): ?>
                                                    <div class="row align-items-center delegate-entry <?= $key != 0 ? 'mtop4' : '' ?>">
                                                        <div class="col-md-3">
                                                            <input type="text"
                                                                   name="delegates[<?php echo $key; ?>][first_name]"
                                                                   class="form-control"
                                                                   placeholder="First Name" required
                                                                   value="<?php echo $delegate['first_name']; ?>">
                                                            <small class="text-danger"><?php echo form_error("delegates[$key][first_name]"); ?></small>
                                                        </div>
                                                        <div style="margin-left:-20px;" class="col-md-3">
                                                            <input type="text"
                                                                   name="delegates[<?php echo $key; ?>][last_name]"
                                                                   class="form-control"
                                                                   placeholder="Last Name" required
                                                                   value="<?php echo $delegate['last_name']; ?>">
                                                            <small class="text-danger"><?php echo form_error("delegates[$key][last_name]"); ?></small>
                                                        </div>
                                                        <div style="margin-left:-20px;" class="col-md-3">
                                                            <input type="email"
                                                                   name="delegates[<?php echo $key; ?>][email]"
                                                                   class="form-control"
                                                                   placeholder="Email" required
                                                                   value="<?php echo $delegate['email']; ?>">
                                                            <small class="text-danger"><?php echo form_error("delegates[$key][email]"); ?></small>
                                                        </div>
                                                        <div style="margin-left:-20px;" class="col-md-3">
                                                            <input type="text"
                                                                   name="delegates[<?php echo $key; ?>][phone]"
                                                                   class="form-control"
                                                                   placeholder="Phone" required
                                                                   value="<?php echo $delegate['phone']; ?>">
                                                            <small class="text-danger"><?php echo form_error("delegates[$key][phone]"); ?></small>
                                                        </div>
                                                        <?php if ($key != 0): ?>
                                                            <div class="mtop4 text-center">
                                                                <button style="margin-left: -20px; border: 0px; color: red; background-color: transparent;"
                                                                        type="button" class="remove-delegate">
                                                                    <i class="fas fa-trash-alt"
                                                                       style="font-size: 1.5rem;"></i>
                                                                </button>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <div class="row align-items-center delegate-entry">
                                                    <div class="col-md-3">
                                                        <input type="text" name="delegates[0][first_name]"
                                                               class="form-control"
                                                               placeholder="First Name" required>
                                                    </div>
                                                    <div style="margin-left:-20px;" class="col-md-3">
                                                        <input type="text" name="delegates[0][last_name]"
                                                               class="form-control"
                                                               placeholder="Last Name" required>
                                                    </div>
                                                    <div style="margin-left:-20px;" class="col-md-3">
                                                        <input type="email" name="delegates[0][email]"
                                                               class="form-control"
                                                               placeholder="Email" required>
                                                    </div>
                                                    <div style="margin-left:-20px;" class="col-md-3">
                                                        <input type="text" name="delegates[0][phone]"
                                                               class="form-control"
                                                               placeholder="Phone" required>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <button id="add-delegate" type="button"
                                                class="mtop10 btn btn-info d-flex align-items-center justify-content-center"
                                                style="width:210px; border-radius: 5px; font-weight: bold;">
                                            </i> Add Delegate
                                        </button>
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

<div class="modal fade" id="newEventModal" tabindex="-1" role="dialog" aria-labelledby="newEventModalLabel"
     aria-hidden="true">
    <?php echo form_open('admin/events_due/events/store', [
        'id' => 'create-new-event-form',
        'enctype' => 'multipart/form-data'
    ]); ?>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between px-3">
                <h6 class="modal-title mb-0" id="newEventModalLabel">Add New Event</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="eventName">Event Name</label>
                    <input type="text" class="form-control" id="eventName" name="event_name" required>
                </div>
            </div>
            <div style="margin-top:-20px;" class="modal-footer">
                <button type="submit" class="btn btn-primary">Save Event</button>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<?php init_tail(); ?>
<script>
    $(document).ready(function () {

        function fetchLocations() {
            $.ajax({
                url: '<?= base_url('admin/events_due/locations') ?>',
                type: 'POST',
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

        }

        fetchLocations();

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

    });
</script>
