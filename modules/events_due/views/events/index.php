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
                                <th><?php echo _l('Start Date'); ?></th>
                                <th><?php echo _l('End Date'); ?></th>
                                <th><?php echo _l('Location'); ?></th>
                                <th><?php echo _l('Venue'); ?></th>
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
                                    <td><?php echo $event->start_date; ?></td>
                                    <td><?php echo $event->end_date; ?></td>
                                    <td><?php echo $event->location; ?></td>
                                    <td><?php echo $event->venue; ?></td>
                                    <td>
                                        <a style="color:white;"
                                           href="<?php echo admin_url('events_due/events/view/' . $event->event_id); ?>"
                                           class="btn btn-info">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                        <button style="color:white;"
                                                data-toggle="modal"
                                                data-target="#attendanceSheetModal"
                                                data-event-id="<?php echo $event->event_id; ?>"
                                                data-location="<?php echo $event->location; ?>"
                                                data-venue="<?php echo $event->venue; ?>"
                                                class="btn btn-info open-attendance-modal">
                                            <i class="fa fa-file"></i> Attendance sheet
                                        </button>
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

                <div class="form-group">
                    <label for="attendance_sheet">Attendance Sheet</label>
                    <input type="file"  name="attendance_sheet" id="attendance_sheet"
                           style="width: 100%; padding: 8px; font-size: 16px; border: 1px solid #ccc; border-radius: 5px;" required>
                </div>
            </div>
            <div style="margin-top:-20px;" class="modal-footer">
                <button type="submit" class="btn btn-primary">Upload</button>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?php init_tail(); ?>
<script>
    $(document).ready(function () {
        $('.open-attendance-modal').on('click', function () {
            let eventId = $(this).data('event-id');
            let location = $(this).data('location');
            let venue = $(this).data('venue');

            $('#event_id').val(eventId);
            $('#location').val(location);
            $('#venue').val(venue);
        });
    });
</script>