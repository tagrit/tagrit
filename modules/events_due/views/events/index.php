<?php init_head(); ?>
<style>
    .custom-button {
        display: inline-flex;
        align-items: center;
        padding: 0.625rem 1.25rem; /* Equivalent to px-5 py-2.5 */
        margin-top: 0.4rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: black;
        background-color: transparent;
        border-radius: 0.375rem; /* Equivalent to rounded-lg */
        transition: background-color 0.2s, box-shadow 0.2s;
    }

    /* Hide action buttons by default */
    .action-buttons {
        display: none;
    }

    /* Show action buttons when hovering over the row */
    .data-row:hover .action-buttons {
        display: block;
        cursor: pointer;
    }
</style>
<div id="wrapper">
    <div class="content">
        <div style="background-color:transparent;" class="panel_s">
            <div style="background-color:transparent;" class="panel-body">
                <div class="row">
                    <div style="padding:15px; margin-bottom:10px;">
                        <a style="text-decoration: none; margin-left:-10px; border: 2px solid black;"
                           class="custom-button"
                           href="<?php echo admin_url('events_due/registrations/create') ?>">
                            <i class="fa fa-user-plus" aria-hidden="true"></i>
                            <span style="margin-left: 10px;">Register For Event</span>
                        </a>
                    </div>
                    <?php if (!empty($events)) : ?>
                        <table class="table dt-table" id="events-table">
                            <thead>
                            <tr>
                                <th><?php echo _l('Event'); ?></th>
                                <th><?php echo _l('Date'); ?></th>
                                <th><?php echo _l('Location'); ?></th>
                                <th><?php echo _l('Unique Code'); ?></th>
                                <th><?php echo _l('Action'); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($events as $event) : ?>
                                <tr>
                                    <td>
                                        <div class="d-flex flex-column justify-content-center">
                                            <p style="font-weight: bold; font-size: 14px;">
                                                <?php echo $event->event_name; ?>
                                            </p>
                                        </div>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <p class="text-secondary mb-0">
                                            <?php
                                            $start_date = date('jS M Y', strtotime($event->start_date));
                                            $end_date = date('jS M Y', strtotime($event->end_date));
                                            echo $start_date . ' - ' . $end_date;
                                            ?>
                                        </p>
                                    </td>
                                    <td>
                                        <?php
                                        echo $event->location . ' - ' . $event->venue;
                                        ?>
                                    </td>
                                    <td class="copy-cell">
                                        <span class="copy-text"><?php echo $event->event_unique_code; ?></span>
                                        <button class="copy-btn" onclick="copyToClipboard(this)" title="Copy">
                                            <i class="fa fa-copy"></i>
                                        </button>
                                    </td>
                                    <td>
                                        <div style="display: inline-flex; gap: 10px; align-items: center;">

                                            <?php echo form_open('admin/events_due/events/view', [
                                                'id' => 'eventForm_' . $event->event_id,
                                                'method' => 'POST',
                                                'style' => 'display:inline;' // <-- key to keeping the form inline
                                            ]); ?>

                                            <input type="hidden" name="event_id"
                                                   value="<?php echo $event->event_id; ?>">
                                            <input type="hidden" name="location"
                                                   value="<?php echo $event->location; ?>">
                                            <input type="hidden" name="venue" value="<?php echo $event->venue; ?>">
                                            <input type="hidden" name="start_date"
                                                   value="<?php echo $event->start_date; ?>">
                                            <input type="hidden" name="end_date"
                                                   value="<?php echo $event->end_date; ?>">

                                            <button
                                                    onfocus="this.style.outline='none'"
                                                    style="border:0; background-color:transparent; box-shadow:none; outline:none; padding: 0;"
                                                    class="btn btn-info" type="submit">
                                                <i style="margin-bottom:5px; color:blue;" class="fa fa-eye"></i>
                                            </button>

                                            <?php echo form_close(); ?>

                                            <button
                                                    onfocus="this.style.outline='none'"
                                                    style="border:0; background-color:transparent; box-shadow:none; outline:none; padding: 0;"
                                                    data-toggle="modal"
                                                    data-target="#editEventModal"
                                                    data-event-id="<?php echo $event->event_id; ?>"
                                                    data-event-name="<?php echo $event->event_name; ?>"
                                                    data-location="<?php echo $event->location; ?>"
                                                    data-venue="<?php echo $event->venue; ?>"
                                                    data-start-date="<?php echo $event->start_date; ?>"
                                                    data-end-date="<?php echo $event->end_date; ?>"
                                                    class="btn btn-info open-edit-event-modal">
                                                <i style="color:orange;" class="fa fa-pencil"></i>
                                            </button>

                                            <button
                                                    onfocus="this.style.outline='none'"
                                                    style="border:0; background-color:transparent; box-shadow:none; outline:none; padding: 0;"
                                                    data-toggle="modal"
                                                    data-target="#attendanceSheetModal"
                                                    data-event-id="<?php echo $event->event_id; ?>"
                                                    data-location="<?php echo $event->location; ?>"
                                                    data-venue="<?php echo $event->venue; ?>"
                                                    data-start-date="<?php echo $event->start_date; ?>"
                                                    data-end-date="<?php echo $event->end_date; ?>"
                                                    class="btn btn-info open-attendance-modal">
                                                <i style="color:brown;" class="fa fa-paperclip"></i>
                                            </button>
                                        </div>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else : ?>
                        <p class="text-center">
                            No events available.
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="attendanceSheetModal" tabindex="-1" role="dialog"
     aria-labelledby="attendanceSheetModalLabel"
     aria-hidden="true">
    <?php echo form_open('admin/events_due/events/upload_attendance_sheet', [
        'id' => 'upload-attendance-sheet',
        'enctype' => 'multipart/form-data'
    ]); ?>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <button type="button" class="close d-flex align-items-center" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Hidden Inputs for Event Data -->
                <input type="hidden" name="event_id" id="event_id">
                <input type="hidden" name="location" id="location">
                <input type="hidden" name="venue" id="venue">
                <input type="hidden" name="startDate" id="startDate">
                <input type="hidden" name="endDate" id="endDate">

                <div class="form-group">
                    <label for="attendance_sheet">Attendance Sheet</label>
                    <input type="file" name="attendance_sheet" id="attendance_sheet"
                           style="width: 100%; padding: 8px; font-size: 16px; border: 1px solid #ccc; border-radius: 5px;"
                           required>
                </div>
            </div>
            <div style="margin-top:-20px;" class="modal-footer">
                <button type="submit" class="btn btn-primary">Upload</button>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<div class="modal fade" id="editEventModal" tabindex="-1" role="dialog"
     aria-labelledby="attendanceSheetModalLabel"
     aria-hidden="true">
    <?php echo form_open('admin/events_due/events/edit', [
        'id' => 'upload-attendance-sheet',
        'enctype' => 'multipart/form-data'
    ]); ?>
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <button type="button" class="close d-flex align-items-center" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <!-- Hidden Inputs for Event Data -->
                <input type="hidden" name="event_id" id="editEventId">
                <input type="hidden" name="location" id="editLocation">
                <input type="hidden" name="venue" id="editVenue">
                <input type="hidden" name="startDate" id="editStartDate">
                <input type="hidden" name="endDate" id="editEndDate">

                <!-- Client Details -->
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="edit_event_id" class="control-label">Event</label>
                        <select id="edit_event_id" name="edit_event_id" class="form-control selectpicker"
                                data-live-search="true" required>
                            <?php if (!empty($events)): ?>
                                <?php foreach ($events as $index => $event): ?>
                                    <option value="<?= htmlspecialchars($event->event_id) ?>"
                                        <?= set_value('event_id') == $event->event_id ? 'selected' : '' ?>>
                                        <?= strlen($event->event_name) > 90 ? htmlspecialchars(substr($event->event_name, 0, 27)) . '...' : htmlspecialchars($event->event_name) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled selected>No events available
                                </option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <!-- Client Details -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="edit_start_date" class="control-label">Start Date*</label>
                        <input type="date" id="edit_start_date" name="edit_start_date"
                               class="form-control"
                               required value="<?= set_value('edit_start_date'); ?>">
                        <?php echo form_error('edit_start_date', '<div class="error-message">', '</div>'); ?>
                    </div>
                </div>
                <!-- Client Details -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="edit_end_date" class="control-label">End Date*</label>
                        <input type="date" id="edit_end_date" name="edit_end_date"
                               class="form-control"
                               value="<?= set_value('edit_end_date'); ?>"
                               required>
                        <?php echo form_error('edit_end_date', '<div class="error-message">', '</div>'); ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="edit_location_id" class="control-label">Location*</label>
                        <select id="edit_location_id" name="edit_location_id"
                                class="form-control selectpicker"
                                data-live-search="true" required>
                            <option value="">Select Location</option>
                        </select>
                        <?php echo form_error('edit_location_id', '<div class="error-message">', '</div>'); ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="edit_venue_id" class="control-label">Venue*</label>
                        <select id="edit_venue_id" name="edit_venue_id" class="form-control selectpicker"
                                data-live-search="true" required>
                            <option value="">Select Venue</option>
                        </select>
                        <?php echo form_error('edit_venue', '<div class="error-message">', '</div>'); ?>
                    </div>
                </div>
            </div>
            <div style="margin-top:-20px; margin-right:15px;" class="modal-footer">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<?php init_tail(); ?>
<script>

    function copyToClipboard(btn) {
        const text = btn.closest('.copy-cell').querySelector('.copy-text').textContent.trim();
        navigator.clipboard.writeText(text).then(() => {
            const original = btn.innerHTML;
            btn.innerHTML = '<i class="fa fa-check" style="color:green;"></i>';
            setTimeout(() => {
                btn.innerHTML = original;
            }, 1500);
        });
    }


    $(document).ready(function () {
        $('.open-attendance-modal').on('click', function () {
            let eventId = $(this).data('event-id');
            let location = $(this).data('location');
            let venue = $(this).data('venue');
            let startDate = $(this).data('start-date');
            let endDate = $(this).data('end-date');

            $('#event_id').val(eventId);
            $('#location').val(location);
            $('#venue').val(venue);
            $('#startDate').val(startDate);
            $('#endDate').val(endDate);
        });


        $(document).on('click', '.open-edit-event-modal', function () {

            const eventName = $(this).data('event-name').trim().toLowerCase();
            const location = $(this).data('location');
            const venue = $(this).data('venue');
            const startDate = $(this).data('start-date');
            const endDate = $(this).data('end-date');
            const eventId = $(this).data('event-id');

            $('#event_id').val(); // for hidden input
            $('#edit_start_date').val(startDate);
            $('#edit_end_date').val(endDate);

            $('#editEventId').val(eventId);
            $('#editLocation').val(location);
            $('#editVenue').val(venue);
            $('#editStartDate').val(startDate);
            $('#editEndDate').val(endDate);


            // ✅ Select event by name
            const $eventSelect = $('#edit_event_id');
            let matched = false;

            $eventSelect.find('option').each(function () {
                const optionText = $(this).text().trim().toLowerCase();
                if (eventName && optionText === eventName.trim().toLowerCase()) {
                    $(this).prop('selected', true);
                    matched = true;
                } else {
                    $(this).prop('selected', false);
                }
            });

            if (!matched) {
                console.warn('Event name not found in dropdown:', eventName);
            }

            $eventSelect.selectpicker('refresh');

            // Fetch location and venues
            fetchLocations(location, function (selectedLocationId) {
                if (selectedLocationId) {
                    fetchVenues(selectedLocationId, venue);
                }
            });
        });

        function fetchLocations(location = null, callback = null) {
            $.ajax({
                url: '<?= base_url('admin/events_due/locations') ?>',
                type: 'POST',
                dataType: 'json',
                success: function (response) {
                    console.log('Response:', response);

                    const $select = $('#edit_location_id');
                    $select.empty().append('<option value="">Select Location</option>');

                    let selectedLocationId = null;

                    if (response.success) {
                        $.each(response.data, function (key, value) {
                            const isMatch = location && value.name.toLowerCase() === location.toLowerCase();
                            const selected = isMatch ? 'selected' : '';
                            if (isMatch) {
                                selectedLocationId = value.id;
                            }
                            $select.append('<option value="' + value.id + '" ' + selected + '>' + value.name + '</option>');
                        });
                    } else {
                        console.error('Error:', response.error);
                        alert('Error: ' + response.error);
                    }

                    $select.selectpicker('refresh');

                    // 🔁 Invoke callback if provided
                    if (typeof callback === 'function') {
                        callback(selectedLocationId);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', status, error, xhr);
                    alert('Failed to fetch locations. Please try again.');
                }
            });
        }

        function fetchVenues(locationId, venue = null) {
            if (locationId) {
                $.ajax({
                    url: '<?= base_url('admin/events_due/venues') ?>',
                    type: 'POST',
                    data: {location_id: locationId},
                    dataType: 'json',
                    success: function (response) {

                        const $select = $('#edit_venue_id');
                        $select.empty().append('<option value="">Select Venue</option>');

                        if (response.success) {
                            $.each(response.data, function (key, value) {
                                const isMatch = venue && value.name.toLowerCase() === venue.toLowerCase();
                                const selected = isMatch ? 'selected' : '';
                                $select.append('<option value="' + value.id + '" ' + selected + '>' + value.name + '</option>');
                            });
                        } else {
                            console.error('Error:', response.error);
                            alert('Error: ' + response.error);
                        }

                        $select.selectpicker('refresh');
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error:', status, error, xhr);
                        alert('Failed to fetch venues. Please try again.');
                    }
                });
            } else {
                const $select = $('#edit_venue_id');
                $select.empty().append('<option value="">Select Venue</option>');
                $select.selectpicker('refresh');
            }
        }

        fetchLocations();


        // When location is selected, fetch venues
        $('#edit_location_id').change(function () {
            const locationId = $(this).val();

            if (locationId) {
                $.ajax({
                    url: '<?= base_url('admin/events_due/venues') ?>',
                    type: 'POST',
                    data: {location_id: locationId},
                    dataType: 'json',
                    success: function (response) {

                        $('#edit_venue_id').empty().append('<option value="">Select Venue</option>');

                        if (response.success) {
                            $.each(response.data, function (key, value) {
                                $('#edit_venue_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                            });
                        } else {
                            console.error('Error:', response.error);
                            alert('Error: ' + response.error);
                            document.getElementById('circleLoader').style.display = 'none';
                        }

                        $('#edit_venue_id').selectpicker('refresh');
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error:', status, error, xhr);
                        alert('Failed to fetch locations. Please try again.');
                        document.getElementById('circleLoader').style.display = 'none';
                    }
                });
            } else {
                $('#edit_venue_id').empty().append('<option value="">Select Location</option>');
                $('#edit_venue_id').selectpicker('refresh');
            }
        });

        $('#events-table').DataTable({
            ordering: false
        });

    });
</script>