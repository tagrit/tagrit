<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="section-header">
            <h1>Events</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                <div class="breadcrumb-item">Events</div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <div class="tw-mb-8 tw-flex tw-items-center tw-justify-between">
                            <button onclick="openEventDrawer()" class="btn btn-primary pull-right">
                                <i class="fa fa-plus"></i> <?php echo _l('New Event'); ?>
                            </button>
                        </div>
                        <hr class="hr-panel-separator"/>

                        <table class="table dt-table table-events" id="events-table">
                            <thead>
                            <tr>
                                <th><?php echo _l('Event Name'); ?></th>
                                <th><?php echo _l('Setup'); ?></th>
                                <th><?php echo _l('Division'); ?></th>
                                <th><?php echo _l('Start Date'); ?></th>
                                <th><?php echo _l('End Date'); ?></th>
                                <th><?php echo _l('Venue'); ?></th>
                                <th><?php echo _l('Name of Delegate'); ?></th>
                                <th><?php echo _l('Email'); ?></th>
                                <th><?php echo _l('Phone'); ?></th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Drawer Overlay -->
<div class="drawer-overlay" onclick="closeEventDrawer()"
     style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999; transition: opacity 0.3s;"></div>

<!-- Event Drawer -->
<div class="drawer" id="eventDrawer"
     style="position:fixed; right:-80%; top:0; width:80%; height:100%; background:white; z-index:1000; box-shadow:-2px 0 5px rgba(0,0,0,0.1); transition: right 0.3s;">
    <div class="drawer-header"
         style="padding: 20px; display: flex; justify-content: space-between; align-items: center;">
        <h3 class="drawer-title" id="drawerTitle"><?php echo _l('New Event'); ?></h3>
        <button type="button" class="drawer-close" onclick="closeEventDrawer()"
                style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">
            <i class="fa fa-times"></i>
        </button>
    </div>

    <div class="drawer-body" style="padding: 20px; padding-right:40px; overflow-y: auto; height: calc(100% - 100px);">
        <?php echo form_open('admin/events_due/events/store', [
            'id' => 'create-event-name-form',
            'enctype' => 'multipart/form-data'
        ]); ?>

        <div class="form-group">
            <div class="row align-items-end">
                <div class="col-md-11">
                    <label for="event_name_id"><?php echo _l('Event Name'); ?></label>
                    <select id="event_name_id" name="event_name_id"
                            data-live-search="true"
                            class="form-control selectpicker"
                            data-none-selected-text="<?php echo _l('Dropdown Non Selected Text'); ?>">
                        <?php foreach ($event_names as $event_name): ?>
                            <option value="<?= htmlspecialchars($event_name->id); ?>"><?= htmlspecialchars($event_name->event_name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="margin-top:26px;" class="col-md-1 d-flex justify-content-end">
                    <button type="button" class="btn btn-primary btn-sm"
                            data-toggle="modal" data-target="#addEventName">
                        +
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="location_id"><?php echo _l('Event Location'); ?></label>
                    <select id="location_id" name="location_id"
                            data-live-search="true"
                            class="form-control selectpicker"
                            data-none-selected-text="<?php echo _l('Dropdown Non Selected Text'); ?>">
                        <?php foreach ($event_locations as $event_location): ?>
                            <option value="<?= htmlspecialchars($event_location->id); ?>"><?= htmlspecialchars($event_location->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php echo form_error('location_id', '<div class="error-message">', '</div>'); ?>

                </div>
            </div>
            <div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="venue_id"><?php echo _l('Event Venue'); ?></label>
                        <select id="venue_id" name="venue_id"
                                data-live-search="true"
                                class="form-control selectpicker"
                                data-none-selected-text="<?php echo _l('Dropdown Non Selected Text'); ?>">
                            <?php foreach ($event_venues as $event_venue): ?>
                                <option value="<?= htmlspecialchars($event_venue->id); ?>"><?= htmlspecialchars($event_venue->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php echo form_error('venue_id', '<div class="error-message">', '</div>'); ?>

                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="event_type"><?php echo _l('Event Type'); ?></label>
                    <select id="event_type" name="event_type"
                            data-live-search="true"
                            class="form-control selectpicker"
                            data-none-selected-text="<?php echo _l('Dropdown Non Selected Text'); ?>">
                        <option value="Local">Local</option>
                        <option value="International">International</option>
                    </select>
                    <?php echo form_error('event_type', '<div class="error-message">', '</div>'); ?>

                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="setup"><?php echo _l('Setup'); ?></label>
                    <select id="setup" name="setup"
                            data-live-search="true"
                            class="form-control selectpicker"
                            data-none-selected-text="<?php echo _l('Dropdown Non Selected Text'); ?>">
                        <option value="Physical">Physical</option>
                        <option value="Virtual">Virtual</option>
                    </select>
                    <?php echo form_error('setup', '<div class="error-message">', '</div>'); ?>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="start_date" class="required"><?php echo _l('Start Date'); ?></label>
                    <input type="date" id="start_date" name="start_date" class="form-control" required>
                </div>
                <?php echo form_error('start_date', '<div class="error-message">', '</div>'); ?>

            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="end_date" class="required"><?php echo _l('End Date'); ?></label>
                    <input type="date" id="end_date" name="end_date" class="form-control" required>
                </div>
                <?php echo form_error('end_date', '<div class="error-message">', '</div>'); ?>

            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="division"><?php echo _l('Division'); ?></label>
                    <input type="text" id="division" name="division" class="form-control">
                </div>
                <?php echo form_error('division', '<div class="error-message">', '</div>'); ?>

            </div>
        </div>
        <button type="submit" style="margin-top: 20px;" class="btn btn-primary btn-block">Submit Event</button>
        <?php echo form_close(); ?>
    </div>
</div>

<div class="modal fade" id="addEventName" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Add Even Name</h4>
            </div>
            <?php echo form_open('admin/events_due/events/store_event_name', [
                'id' => 'create-event-name-form',
                'enctype' => 'multipart/form-data'
            ]); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <label for="subcategory_name" class="form-label">Event Name</label>
                        <input type="text" id="event_name" name="event_name" class="form-control"
                               placeholder="Enter event name" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo _l('Add Event Name'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
    // Initialize selectpicker
    $(document).ready(function () {
        $('.selectpicker').selectpicker();
    });

    const showEventModal = <?php echo isset($show_event_modal) && $show_event_modal ? 'true' : 'false'; ?>;
    if (showEventModal) {
        openEventDrawer();
    }

    // Open drawer
    function openEventDrawer() {
        document.querySelector('.drawer-overlay').style.display = 'block';
        document.querySelector('.drawer-overlay').style.opacity = '1';
        document.getElementById('eventDrawer').style.right = '0';
    }

    // Close drawer
    function closeEventDrawer() {
        document.querySelector('.drawer-overlay').style.opacity = '0';
        setTimeout(() => {
            document.querySelector('.drawer-overlay').style.display = 'none';
        }, 300);
        document.getElementById('eventDrawer').style.right = '-80%';
        document.getElementById('event-form').reset();
        $('.selectpicker').selectpicker('refresh');
    }

    // Save event
    function saveEvent() {
        const form = document.getElementById('event-form');
        if (form.checkValidity()) {
            const formData = new FormData(form);
            const event = {};
            formData.forEach((value, key) => {
                event[key] = value;
            });

            const table = document.getElementById('events-table').getElementsByTagName('tbody')[0];
            const newRow = table.insertRow();
            Object.values(event).forEach((text, index) => {
                const cell = newRow.insertCell(index);
                cell.textContent = text;
            });

            closeEventDrawer();
        } else {
            form.reportValidity();
        }
    }
</script>