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
                        <table class="table dt-table" id="example">
                            <thead class="table-head">
                            <tr>
                                <th>Event</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($events as $event) : ?>
                                <tr class="data-row">
                                    <td>
                                        <div class="d-flex flex-column justify-content-center">
                                            <p style="font-weight: bold; font-size: 14px;">
                                                <?php echo htmlspecialchars($event->name ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                            </p>
                                            <div class="action-buttons">
                                                <p>
                                                    <a href="<?php echo admin_url('imprest/events/edit/' . $event->id); ?>"
                                                       style="color:red; background:none; border:none; padding:0; cursor:pointer;">
                                                        Edit
                                                    </a>
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else : ?>
                        <p style="text-align: center; font-weight: bold; font-size:15px;">
                        <td colspan="5" class="text-center">No events available.</td>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
