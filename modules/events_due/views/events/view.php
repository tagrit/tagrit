<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div style="background-color:transparent; padding-left:10px; padding-top:20px;" class="panel_s">
            <a href="<?= admin_url('events_due/events/index'); ?>"
               style="margin-top:10px; background: white; color: #007bff; padding: 10px 15px; font-size: 14px;
                        font-weight: bold; border-radius: 5px; text-decoration: none;
                        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);">
                <i class="fa fa-arrow-left" aria-hidden="true"></i>
                Back
            </a>

            <div style="background-color:transparent;" class="panel-body">

                <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px; background: linear-gradient(45deg, #007bff, #0056b3); border-radius: 8px;">
                    <h2 class="event-title" id="event-name"
                        style="max-width:70%; font-size: 17px; font-weight: bold; color: white; text-transform: uppercase;
                        margin: 0; padding: 12px 20px; border-radius: 8px;
                        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);">
                        <?= htmlspecialchars($event_data['event_name']); ?>
                    </h2>

                    <?php if (!empty($event_data['attendance_sheet_url'])): ?>
                        <a href="<?= htmlspecialchars($event_data['attendance_sheet_url']); ?>" target="_blank"
                           style="background: white; color: #007bff; padding: 10px 15px; font-size: 14px;
                        font-weight: bold; border-radius: 5px; text-decoration: none;
                        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);">
                            Attendance Sheet
                        </a>
                    <?php endif; ?>
                </div>


                <div class="event-info">
                    <div class="event-column">
                        <p><strong>Start Date:</strong> <span
                                    id="start-date"><?= htmlspecialchars($event_data['start_date']); ?></span></p>
                        <p><strong>End Date:</strong> <span
                                    id="end-date"><?= htmlspecialchars($event_data['end_date']); ?></span></p>
                        <p><strong>Setup:</strong> <span
                                    id="setup"><?= $event_data['setup'] ?? ''; ?></span></p>
                        <p><strong>Division:</strong> <span
                                    id="division"><?= htmlspecialchars($event_data['division']); ?></span></p>
                        <p><strong>Type:</strong> <span id="type"><?= $event_data['type'] ?? ''; ?></span>
                        </p>
                    </div>

                    <div class="event-column">
                        <p><strong>Revenue:</strong> <span
                                    id="revenue"><?= htmlspecialchars($event_data['total_revenue']); ?></span></p>
                        <p><strong>Location:</strong> <span
                                    id="location"><?= htmlspecialchars($event_data['location']); ?></span></p>
                        <p><strong>Venue:</strong> <span
                                    id="venue"><?= htmlspecialchars($event_data['venue']); ?></span></p>
                        <p><strong>Trainers:</strong> <span id="trainers">
                            <?php
                            $trainers = unserialize($event_data['trainers']);
                            if (is_array($trainers) && !empty($trainers)) {
                                echo implode(', ', array_map('htmlspecialchars', $trainers));
                            } else {
                                echo 'No trainers available';
                            }
                            ?>
                        </span></p>
                    </div>
                </div>

                <p style="margin-top: 15px; font-size: 14px; font-weight: bold;
                     color: white; margin-bottom:15px;  background: linear-gradient(45deg, #007bff, #0056b3);
                     padding: 10px 15px; border-radius: 6px; display: inline-block;
                     box-shadow: 0px 3px 5px rgba(0, 0, 0, 0.1); text-transform: uppercase;">
                    <i class="fa fa-users" aria-hidden="true"></i> Delegates
                </p>

                <?php if (!empty($event_data['clients'])): ?>
                    <table class="table dt-table" id="delegates-table">
                        <thead>
                        <tr>
                            <th><?= _l('Name'); ?></th>
                            <th><?= _l('Email'); ?></th>
                            <th><?= _l('Phone Number'); ?></th>
                            <th><?= _l('Organization'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($event_data['clients'] as $delegate): ?>
                            <tr>
                                <td>
                                    <div class="d-flex flex-column justify-content-center">
                                        <p style="font-weight: bold; font-size: 14px;">
                                            <?= htmlspecialchars($delegate['first_name'] . ' ' . $delegate['last_name']); ?>
                                        </p>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($delegate['email']); ?></td>
                                <td><?= htmlspecialchars($delegate['phone']); ?></td>
                                <td><?= htmlspecialchars($delegate['organization']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No Delegates</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
