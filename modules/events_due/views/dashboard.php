<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="section-header">
            <h1>Dashboard </h1>
        </div>
        <div class="row">

            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="card card-hero">
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
                <div class="card card-hero">
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
        <div class="row">
            <div class="col-lg-8 col-xl-9">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel_s">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-lg-8">
                                        <h2 class="section-title">Upcoming Event</h2>
                                    </div>
                                    <div class="col-lg-4 text-right mt-2">
                                        <a href="#">
                                            <button class="btn btn-sm btn-primary">View all</button>
                                        </a>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table dt-table" id="eventsTable">
                                        <thead>
                                            <tr>
                                                <th>Image</th>
                                                <th>Event</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><img class="table-img" src="https://eventrights.saasmonks.in/images/upload/641aa475ed2cd.jpg"></td>
                                                <td style="width:390px">
                                                    <h6>ABA 404:Effective Writing,Communication & Personal Etiquette Essentials For Administrative Assistance</h6>
                                                    <p>Mombasa</p>
                                                </td>
                                                <td>2023-03-22</td>
                                            </tr>
                                            <tr>
                                                <td><img class="table-img" src="https://eventrights.saasmonks.in/images/upload/63e5ed9879e3b.jpg"></td>
                                                <td style="width:390px">
                                                    <h6>Effective Writing,Communication & Personal Etiquete Essentials For Administrative Assistance</h6>
                                                    <p>Machakos</p>
                                                </td>
                                                <td>2023-06-24</td>
                                            </tr>
                                            <tr>
                                                <td><img class="table-img" src="https://eventrights.saasmonks.in/images/upload/65963a0aa7484.jpg"></td>
                                                <td style="width:390px">
                                                    <h6>ABA 403:Modern Business Management Skills,Leadership & Administrative Effectiveness</h6>
                                                    <p>Mombasa</p>
                                                </td>
                                                <td>2023-10-18</td>
                                            </tr>
                                            <tr>
                                                <td><img class="table-img" src="https://eventrights.saasmonks.in/images/upload/659638830a0af.jpg"></td>
                                                <td style="width:390px">
                                                    <h6>Intergrated Audit,Data Mining & Analytics Forum-Unveiling The future Of Data Driven Governance</h6>
                                                    <p>Mombasa</p>
                                                </td>
                                                <td>2024-01-04</td>
                                            </tr>
                                            <tr>
                                                <td><img class="table-img" src="https://eventrights.saasmonks.in/images/upload/63e5eebbab2f9.jpg"></td>
                                                <td style="width:390px">
                                                    <h6>ABA 407A:Electronic Records Management & Digitization Training</h6>
                                                    <p>Online Event</p>
                                                </td>
                                                <td>2024-02-10</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-xl-3">
                <div class="card">
                    <div class="card-body calender-event">
                        
                        <input type="hidden" id="home_calender" class="flatpickr-input" readonly="readonly">
                        <!-- Removed static Flatpickr markup -->
                        <h5 class="text-dark mb-4 mt-2">February Event</h5>
                        <div class="home-upcoming-event">
                            <div class="row">
                                <div class="col-12 text-center">
                                    <div class="empty-data">
                                        <div class="card-icon shadow-primary">
                                            <i class="fas fa-search"></i>
                                        </div>
                                        <h6 class="mt-3">No events found </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
   
   document.addEventListener('DOMContentLoaded', function() {
        // Initialize Flatpickr with today's date as the default date
        flatpickr('#home_calender', {
            dateFormat: "Y-m-d",
            inline: true,
            defaultDate: new Date(), // Set to today's date
        });
    });
</script>
<?php init_tail(); ?>