<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="section-header">
            <h1>Dashboard</h1>
        </div>

        <!-- Cards Section -->
        <div class="row mb-4">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="card card-hero p-3">
                    <div class="card-header">
                        <div class="card-icon relative">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <h4><?php echo $events_count; ?></h4>
                        <div class="card-description absolute">Events</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="card card-hero p-3">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4><?php echo $delegates_count; ?></h4>
                        <div class="card-description">Delegates</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div style="padding:15px;" class="card p-4">
                    <div style="margin-bottom:20px;" class="text-right">
                        <a href="<?php echo admin_url('events_due/events/index'); ?>"
                           style="margin-bottom:20px;" class="text-right">
                            <button class="btn btn-primary">View More</button>
                        </a>
                    </div>
                    <?php if (!empty($latest_events)) : ?>
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
                            <?php foreach ($latest_events as $event) : ?>
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
        <div class="row gx-4 ">
            <div class="col-md-12 col-sm-12">
                <div style="padding:15px;" class="card p-4 text-center">
                    <canvas id="registrationsChart" style="max-width: 100%; height: 250px;"></canvas>
                </div>
            </div>
            <div class="col-md-12 col-sm-12">
                <div style="padding:15px;" class="card p-4 text-center">
                    <canvas id="divisionEventsChart" style="max-width: 100%; height: 250px;"></canvas>
                </div>
            </div>
            <div class="row gx-4 mb-4">
                <div class="col-md-12 col-sm-12">
                    <div style="padding:15px;" class="card p-4 text-center">
                        <canvas id="divisionRevenueChart" style="max-width: 100%; height: 250px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
<!--        <div class="row gx-4 mb-4">-->
<!--            <div class="col-md-6 col-sm-12">-->
<!--                <div style="padding:15px;" class="card p-4 text-center">-->
<!--                    <canvas id="attendanceChart" style="max-width: 100%; height: 250px;"></canvas>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
    </div>
</div>
<?php init_tail(); ?>

<!-- External JavaScript for Charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {

        const client_per_month_data = <?php echo $clients_per_month; ?>;

        new Chart(document.getElementById("registrationsChart"), {
            type: "line",
            data: {
                labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                datasets: [{
                    label: "Registrations",
                    data: client_per_month_data,
                    borderColor: "#36A2EB",
                    backgroundColor: "rgba(54, 162, 235, 0.2)",
                    fill: true
                }]
            }
        });

        // new Chart(document.getElementById("attendanceChart"), {
        //     type: "doughnut",
        //     data: {
        //         labels: ["Attended", "Missed"],
        //         datasets: [{
        //             data: [80, 20],
        //             backgroundColor: ["#4CAF50", "#FF6384"]
        //         }]
        //     }
        // });

        new Chart(document.getElementById("divisionEventsChart"), {
            type: "bar",
            data: {
                labels: <?= json_encode($events_per_division['labels']) ?>,
                datasets: [{
                    label: "Events Per Division",
                    data: <?= json_encode($events_per_division['counts']) ?>,
                    backgroundColor: "#FFCE56"
                }]
            }
        });

        new Chart(document.getElementById("divisionRevenueChart"), {
            type: "bar",
            data: {
                labels: <?= json_encode($revenue_per_division['labels']) ?>,
                datasets: [{
                    label: "Revenue Per Division",
                    data: <?= json_encode($revenue_per_division['revenues']) ?>,
                    backgroundColor: "#36A2EB"
                }]
            }
        });

    });
</script>
