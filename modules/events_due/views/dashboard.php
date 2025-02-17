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
                        <h4>14</h4>
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
                        <h4>11</h4>
                        <div class="card-description">Users</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div style="padding:15px;" class="card p-4">
                    <div style="margin-bottom:20px;" class="text-right">
                        <button class="btn btn-primary">View More</button>
                    </div>
                    <table class="table dt-table table-events" id="events-table">
                        <thead>
                        <tr>
                            <th><?php echo _l('Event Name'); ?></th>
                            <th><?php echo _l('Start Date'); ?></th>
                            <th><?php echo _l('End Date'); ?></th>
                            <th><?php echo _l('Venue'); ?></th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row gx-4 mb-4">
            <div class="col-md-6 col-sm-12">
                <div style="padding:15px;" class="card p-4 text-center">
                    <canvas id="attendanceChart" style="max-width: 100%; height: 250px;"></canvas>
                </div>
            </div>
            <div class="col-md-6 col-sm-12">
                <div style="padding:15px;" class="card p-4 text-center">
                    <canvas id="demographicsChart" style="max-width: 100%; height: 250px;"></canvas>
                </div>
            </div>
        </div>

        <div class="row gx-4 ">
            <div class="col-md-6 col-sm-12">
                <div style="padding:15px;" class="card p-4 text-center">
                    <canvas id="registrationsChart" style="max-width: 100%; height: 250px;"></canvas>
                </div>
            </div>
            <div class="col-md-6 col-sm-12">
                <div style="padding:15px;" class="card p-4 text-center">
                    <canvas id="popularEventsChart" style="max-width: 100%; height: 250px;"></canvas>
                </div>
            </div>
        </div>

    </div>
</div>
<?php init_tail(); ?>

<!-- External JavaScript for Charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        new Chart(document.getElementById("registrationsChart"), {
            type: "line",
            data: {
                labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
                datasets: [{
                    label: "Registrations",
                    data: [10, 25, 40, 30, 50, 70],
                    borderColor: "#36A2EB",
                    backgroundColor: "rgba(54, 162, 235, 0.2)",
                    fill: true
                }]
            }
        });

        new Chart(document.getElementById("attendanceChart"), {
            type: "doughnut",
            data: {
                labels: ["Attended", "Missed"],
                datasets: [{
                    data: [80, 20],
                    backgroundColor: ["#4CAF50", "#FF6384"]
                }]
            }
        });

        new Chart(document.getElementById("popularEventsChart"), {
            type: "bar",
            data: {
                labels: ["Tech Conference", "Music Festival", "Business Expo", "Sports Event", "Workshop"],
                datasets: [{
                    label: "Registrations",
                    data: [150, 200, 120, 90, 170],
                    backgroundColor: "#FFCE56"
                }]
            }
        });

        new Chart(document.getElementById("demographicsChart"), {
            type: "pie",
            data: {
                labels: ["Students", "Professionals", "Entrepreneurs", "Others"],
                datasets: [{
                    data: [40, 30, 20, 10],
                    backgroundColor: ["#36A2EB", "#FF6384", "#4CAF50", "#FFCE56"]
                }]
            }
        });
    });
</script>
