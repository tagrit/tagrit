<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div style="background-color:transparent;" class="panel_s">
            <div style="background-color:transparent;" class="panel-body">
                <h2 class="event-title text-center" id="event-name"
                    style="font-size: 17px; font-weight: bold; color: white; text-transform: uppercase;
                          background: linear-gradient(45deg, #007bff, #0056b3);
                          padding: 12px 20px; border-radius: 8px; display: inline-block;
                          box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);">
                    <?php echo htmlspecialchars($event[0]->event_name); ?>
                </h2>

                <div class="event-info">
                    <div class="event-column">
                        <p><strong>Start Date:</strong> <span id="start-date"><?php echo $event[0]->start_date ?></span>
                        </p>
                        <p><strong>End Date:</strong> <span id="end-date"><?php echo $event[0]->end_date ?></span></p>
                        <p><strong>Setup:</strong> <span id="setup"><?php echo $event[0]->setup ?></span></p>
                        <p><strong>Division:</strong> <span id="division"><?php echo $event[0]->division ?></span></p>
                        <p><strong>Type:</strong> <span id="type"><?php echo $event[0]->type ?></span></p>
                    </div>

                    <div class="event-column">
                        <p><strong>Revenue:</strong> <span id="revenue"><?php echo $event[0]->revenue ?></span></p>
                        <p><strong>Location:</strong> <span id="location"><?php echo $event[0]->location ?></span></p>
                        <p><strong>Venue:</strong> <span id="venue"><?php echo $event[0]->venue ?></span></p>
                        <p><strong>Organization:</strong> <span
                                    id="organization"><?php echo $event[0]->organization ?></span></p>
                        <p><strong>Trainers:</strong> <span id="trainers">
                                   <?php
                                   $trainers = unserialize($event[0]->trainers);
                                   if (is_array($trainers) && !empty($trainers)) {
                                       echo implode(', ', $trainers);
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
                <?php
                $delegates = !empty($event[0]->serialized_clients) ? unserialize($event[0]->serialized_clients, ['allowed_classes' => false]) : [];
                ?>
                <?php if (!empty($delegates)): ?>
                    <table class="table dt-table" id="delegates-table">
                        <thead>
                        <tr>
                            <th><?= _l('Name'); ?></th>
                            <th><?= _l('Email'); ?></th>
                            <th><?= _l('Phone Number'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($delegates as $delegate): ?>
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
