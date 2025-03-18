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
                           href="<?php echo admin_url('events_due/events/create') ?>">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                            <span style="margin-left: 10px;">Create Event</span>
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
                                        <a style="color:white;" href="#" class="btn btn-info">
                                            <i class="fa fa-eye"></i> View
                                        </a>
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
<?php init_tail(); ?>
