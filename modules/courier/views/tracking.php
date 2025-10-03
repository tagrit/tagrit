<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $this->security->get_csrf_hash(); ?>">

    <title>Shipment Tracking</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #f8f9fd, #eef1f6);
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            text-align: center;
        }

        .container h1 {
            font-size: 28px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
        }

        .tracking-input {
            position: relative;
            margin-bottom: 30px;
        }

        .tracking-input input[type="text"] {
            width: 90%;
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 30px;
            font-size: 16px;
            outline: none;
            transition: all 0.3s ease;
        }

        .tracking-input input[type="text"]:focus {
            border-color: #007bff;
        }

        .tracking-input button {
            position: absolute;
            top: 50%;
            right: 30px;
            transform: translateY(-50%);
            padding: 13px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .tracking-input button:hover {
            background-color: #0056b3;
        }

        .status-box {
            background: #f1f3f7;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .status-box .status-icon {
            font-size: 30px;
            color: #007bff;
            margin-right: 15px;
        }

        .status-box .status-details {
            text-align: left;
            flex-grow: 1;
        }

        .status-box .status-details h3 {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .status-box .status-details p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }

        .status-box .status-time {
            font-size: 14px;
            color: #666;
        }

        .status-timeline {
            position: relative;
            margin-top: 40px;
        }

        .status-timeline::before {
            content: '';
            position: absolute;
            width: 2px;
            background: #ddd;
            top: 0;
            bottom: 0;
            left: 20px;
            margin: auto;
        }

        .timeline-item {
            position: relative;
            padding-left: 50px;
            margin-bottom: 30px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            width: 12px;
            height: 12px;
            background: #007bff;
            border-radius: 50%;
            left: 14px;
            top: 0;
            bottom: 0;
            margin: auto;
        }

        .timeline-item h4 {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
            text-align: left;
        }

        .timeline-item p {
            margin: 0;
            color: #666;
            font-size: 14px;
            text-align: left;
        }

        .timeline-item .time {
            font-size: 12px;
            color: #999;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #999;
        }

        .footer a {
            color: #007bff;
            text-decoration: none;
        }

        .shipment-data {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .shipment-data h2 {
            text-align: left;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: bold;
        }

        .data-row {
            display: flex;
            justify-content: space-between;
        }

        .data-column {
            width: 45%;
        }

        .data-column h4 {
            margin-bottom: 10px;
            font-size: 15px;
            color: #007bff;
            text-align: left;
        }

        .data-column p {
            margin-bottom: 8px;
            font-size: 14px;
            text-align: left;

        }

        /* Shipment Stops Styling */
        .shipment-stops {
            padding-left: 10px;
            padding-bottom: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-top: 10px;
        }

        .shipment-stops h4 {
            font-size: 15px;
            font-weight: bold;
            color: #333;
        }

        .stops-container {
            display: block;
            margin-top: -20px;
        }

        .stop-item {
            display: flex;
            align-items: center;
        }


        .stop-header h5 {
            font-size: 14px;
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
        }

        .stop-header p {
            font-size: 14px;
            color: #777;
        }

        .stop-arrow {
            font-size: 10px;
            color: #999;
            margin: 0 10px;
        }

        .stop-description {
            margin-top: 10px;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>

<body>

<div class="container">
    <h1>Track Your Shipment</h1>
    <div class="tracking-input">
        <input type="text" id="tracking_number" placeholder="Enter your tracking number"/>
        <button style="width:120px; font-weight:bold; font-size:14px;" id="get_shipment_btn">Track</button>
    </div>

    <!-- Placeholder for Logo -->
    <div class="logo-placeholder" id="logo-placeholder">
        <img height="100" width="100" src="<?php echo base_url('modules/courier/assets/go_shipping_logo.png') ?>"
             alt="Logo"/>
        <p id="default_message">Tracking information will be displayed here.</p>
    </div>

    <div class="status-box" id="status-box" style="display: none;">
        <div class="status-icon">
            <i class="fas fa-shipping-fast"></i>
        </div>
        <div class="status-details">
            <h3 id="current_status"></h3>
            <p id="current_status_description"></p>
        </div>
        <!-- Shipment Data Section -->
        <div class="shipment-data" id="receiver_data" style="display: none;">
            <div class="data-row">
                <div style="width: 100%;" class="data-column">
                    <h4>Delivered To : </h4>
                    <p><strong>First Name : </strong><span id="delivered_to_first_name"></span></p>
                    <p><strong>Last Name : </strong><span id="delivered_to_last_name"></span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Shipment Data Section -->
    <div class="shipment-data" id="shipment_data" style="display: none;">
        <h4>Shipment Information</h4>
        <div class="data-row">
            <div class="data-column">
                <h4>Sender Information</h4>
                <p><strong>Name : </strong><span id="sender_name"> </span></p>
                <p><strong>Email : </strong><span id="sender_email"></span></p>
                <p><strong>Phone : </strong><span id="sender_phone"></span></p>
                <p><strong>Address : </strong><span id="sender_address"></span></p>
            </div>
            <div class="data-column">
                <h4>Recipient Information</h4>
                <p><strong>Name : </strong><span id="recipient_name"></span></p>
                <p><strong>Email : </strong><span id="recipient_email"></span></p>
                <p><strong>Phone : </strong><span id="recipient_phone"></span></p>
                <p><strong>Address : </strong><span id="recipient_address"></span></p>
            </div>
        </div>
    </div>

    <div class="status-timeline" id="status-timeline" style="display: none;">
        <div class="timeline-item" id="status-1">
            <h4>Shipment Initiated</h4>
            <p>Your package was initiated.</p>
            <p class="time" id="time-1">Aug 15, 2024, 10:00 AM</p>
        </div>
        <div class="timeline-item" id="status-2">
            <h4>Shipment Picked up</h4>
            <p>Your package was picked up from the pickup location.</p>
            <p class="time" id="time-2">Aug 15, 2024, 10:00 AM</p>
        </div>
        <div class="timeline-item" id="status-3">
            <h4>Shipment Received</h4>
            <p>Your package was received at the warehouse.</p>
            <p class="time" id="time-3">Aug 15, 2024, 10:00 AM</p>
        </div>
        <div class="timeline-item" id="status-4">
            <h4>Shipped</h4>
            <p>Your package has been shipped.</p>
            <p class="time" id="time-4">Aug 16, 2024, 02:00 PM</p>
        </div>
        <div class="timeline-item" id="status-5">
            <h4>Shipment In Transit</h4>
            <p>Your package is on the way to the designated location.</p>
            <p class="time" id="time-5">Aug 16, 2024, 02:00 PM</p>

            <!-- Shipment Stops Section -->
            <div style="display:none;" class="shipment-stops" id="shipment-stops">
                <h4 style="margin-bottom:30px;">Transit Points</h4>
                <div class="stops-container">
                    <div class="stop-item">
                        <div class="stop-header">
                            <h5>Departure</h5>
                            <p id="departure_point_1">Departure Point 1</p>
                        </div>
                        <div class="stop-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                        <div class="stop-header">
                            <h5>Destination</h5>
                            <p id="destination_point_1">Destination Point 1</p>
                        </div>
                    </div>
                    <div class="stop-description">
                        <p id="description_1">Description for Stop 1</p>
                    </div>
                    <!-- Add more stop items as needed -->
                </div>
            </div>

        </div>
        <div class="timeline-item" id="status-6">
            <h4>Shipment Arrived at Destination</h4>
            <p>Your package is has arrived at destination.</p>
            <p class="time" id="time-6">Aug 19, 2024, 09:00 AM</p>
        </div>
        <div class="timeline-item" id="status-7">
            <h4>Shipment Out for Delivery</h4>
            <p>Your package is out for delivery.</p>
            <p class="time" id="time-6">Aug 19, 2024, 09:00 AM</p>
        </div>
        <div class="timeline-item" id="status-8">
            <h4>Shipment Delivered</h4>
            <p>Your package has been delivered to the destination.</p>
            <p class="time" id="time-7">Aug 20, 2024, 01:00 PM</p>
        </div>
    </div>

    <div class="footer">
        <p>&copy; 2024 Go Shipping. <a href="#">Terms of Service</a> | <a href="#">Privacy Policy</a></p>
    </div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {

        function updateTimeline(currentStatusId, shipmentHistory) {
            // Hide all timeline items initially
            $('.timeline-item').hide();

            // Initialize the latest available date
            let latestDate = null;

            // Iterate over the status IDs
            for (let i = 1; i <= currentStatusId; i++) {
                // Initialize a flag to check if the status is found
                let statusFound = false;

                // Loop through the shipment history to find the matching status
                for (let j = 0; j < shipmentHistory.length; j++) {
                    if (parseInt(shipmentHistory[j].status_id) === i) {
                        let date = new Date(shipmentHistory[j].changed_at);

                        // Update the latest date with the current status date
                        latestDate = date;

                        let options = {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric',
                            hour: 'numeric',
                            minute: 'numeric',
                            hour12: true
                        };
                        let formattedDate = date.toLocaleString('en-US', options);

                        // Update the time in the corresponding status timeline item
                        $(`#time-${i}`).text(formattedDate);
                        statusFound = true; // Mark the status as found
                        break;
                    }
                }

                // If the status is not found, use the latest available date
                if (!statusFound && latestDate) {
                    let formattedDate = latestDate.toLocaleString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                        hour: 'numeric',
                        minute: 'numeric',
                        hour12: true
                    });

                    // Set the latest available date for the current status
                    $(`#time-${i}`).text(formattedDate);
                }

                // Show the current status timeline item
                $(`#status-${i}`).show();
            }
        }


        function populateShipmentStops(shipmentHistory) {
            // Get the container for shipment stops
            const stopsContainer = document.querySelector('.stops-container');
            stopsContainer.innerHTML = ''; // Clear any existing stops

            // Loop through the shipment history and create HTML elements
            shipmentHistory.forEach((stop, index) => {
                // Create the stop item container
                const stopItem = document.createElement('div');
                stopItem.style.marginTop = '20px';

                stopItem.classList.add('stop-item');

                // Create departure point element
                const departureHeader = document.createElement('div');
                departureHeader.classList.add('stop-header');
                const departureTitle = document.createElement('h5');
                departureTitle.textContent = 'Departure';
                departureTitle.style.display = 'none';
                departureTitle.style.marginBottom = '20px';
                const departurePoint = document.createElement('p');
                departurePoint.id = `departure_point_${index + 1}`;
                departurePoint.textContent = stop.departure_point;
                departureHeader.appendChild(departureTitle);
                departureHeader.appendChild(departurePoint);

                // Create destination point element
                const destinationHeader = document.createElement('div');
                destinationHeader.classList.add('stop-header');
                const destinationTitle = document.createElement('h5');
                destinationTitle.textContent = 'Destination';
                destinationTitle.style.display = 'none';
                destinationTitle.style.marginBottom = '20px';
                const destinationPoint = document.createElement('p');
                destinationPoint.id = `destination_point_${index + 1}`;
                destinationPoint.textContent = stop.destination_point;
                destinationHeader.appendChild(destinationTitle);
                destinationHeader.appendChild(destinationPoint);

                // Create arrow between departure and destination
                const stopArrow = document.createElement('div');
                stopArrow.classList.add('stop-arrow');
                const arrowIcon = document.createElement('i');
                arrowIcon.classList.add('fas', 'fa-arrow-right');
                stopArrow.appendChild(arrowIcon);

                // Append departure, arrow, and destination to the stop item
                stopItem.appendChild(departureHeader);
                stopItem.appendChild(stopArrow);
                stopItem.appendChild(destinationHeader);

                // Create the description element
                const stopDescription = document.createElement('div');
                stopDescription.classList.add('stop-description');
                const descriptionText = document.createElement('p');
                descriptionText.id = `description_${index + 1}`;
                descriptionText.textContent = stop.description;
                stopDescription.appendChild(descriptionText);

                // Append the stop item and description to the stops container
                stopsContainer.appendChild(stopItem);
                stopsContainer.appendChild(stopDescription);
            });
        }

        // Trigger the track shipment when 'the tracking button' is pressed on the tracking number input
        const shipmentBtn = $('#get_shipment_btn');
        shipmentBtn.click(function () {
            const trackingNumber = $('#tracking_number').val();
            trackShipment(trackingNumber);
        });

        // Trigger the track shipment when 'Enter' is pressed on the tracking number input
        $('#tracking_number').keypress(function (e) {
            if (e.which === 13) {  // 13 is the Enter key code
                const trackingNumber = $(this).val();
                trackShipment(trackingNumber);
            }
        });

        function trackShipment(trackingNumber) {
            $.ajax({
                url: '<?php echo base_url("courier/tracking/shipment_info"); ?>',
                type: "POST",
                data: {tracking_number: trackingNumber},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                success: function (data) {
                    if (data.status === 'success') {

                        console.log(data.data.shipment_details);

                        const statuses = data.data.statuses;
                        const sender = data.data.shipment_details.sender;
                        const sender_type = data.data.shipment_details.sender_type;
                        const recipient_type = data.data.shipment_details.recipient_type;
                        const recipient = data.data.shipment_details.recipient;
                        const delivery_details = data.data.shipment_details.delivery_details;

                        //set sender information
                        if (sender_type === 'individual') {
                            $('#sender_name').text(`${sender.first_name} ${sender.last_name}`);
                            $('#sender_email').text(`${sender.email}`);
                            $('#sender_phone').text(`${sender.phone_number}`);
                            $('#sender_address').text(`${sender.address} , ${sender.zipcode}`);
                        } else {
                            $('#sender_name').text(`${sender.contact_person_name}`);
                            $('#sender_email').text(`${sender.contact_person_email}`);
                            $('#sender_phone').text(`${sender.contact_person_phone_number}`);
                            $('#sender_address').text(`${sender.contact_address} , ${sender.contact_zipcode}`);
                        }


                        //set recipient information
                        if (recipient_type === 'individual') {
                            //set recipient information
                            $('#recipient_name').text(`${recipient.first_name} ${recipient.last_name}`);
                            $('#recipient_email').text(`${recipient.email}`);
                            $('#recipient_phone').text(`${recipient.phone_number}`);
                            $('#recipient_address').text(`${recipient.address} , ${recipient.zipcode}`);

                        } else {
                            $('#recipient_name').text(`${recipient.recipient_contact_person_name}`);
                            $('#recipient_email').text(`${recipient.recipient_contact_person_email}`);
                            $('#recipient_phone').text(`${recipient.recipient_contact_person_phone_number}`);
                            $('#recipient_address').text(`${recipient.recipient_contact_address} , ${recipient.recipient_contact_zipcode}`);
                        }


                        // Hide the logo placeholder
                        $('#logo-placeholder').hide();

                        // Show the status box and timeline
                        $('#status-box').show();
                        $('#status-timeline').show();
                        $('#shipment_data').show();

                        statuses.forEach(status => {
                            if (status.id === data.data.shipment_details.shipment.status_id) {
                                $('#current_status').text(status.description);
                            }
                        });

                        let description;
                        let iconClass;

                        switch (data.data.shipment_details.shipment.status_id) {
                            case '1':
                                description = "Your shipment has been created.";
                                iconClass = "fas fa-box";
                                break;
                            case '2':
                                description = "Your shipment has been picked up.";
                                iconClass = "fas fa-truck-pickup";
                                break;
                            case '3':
                                description = "Your shipment has been received.";
                                iconClass = "fas fa-inbox";
                                break;
                            case '4':
                                description = "Your shipment has been dispatched.";
                                iconClass = "fas fa-truck-loading";
                                break;
                            case '5':
                                description = "Your shipment is in transit.";
                                iconClass = "fas fa-shipping-fast";
                                break;
                            case '6':
                                description = "Your shipment is arrived at destination.";
                                iconClass = "fa-arrow-circle-o-down";
                                break;
                            case '7':
                                description = "Your shipment is out for delivery.";
                                iconClass = "fas fa-truck";
                                break;
                            case '8':
                                description = "Your shipment has been delivered.";
                                iconClass = "fas fa-check-circle";
                                break;
                            default:
                                description = "Status unknown.";
                                iconClass = "fas fa-question-circle";
                        }

                        // Update the status icon and description
                        $('#current_status_description').text(description);
                        $('.status-icon i').removeClass().addClass(`fas ${iconClass}`);
                        updateTimeline(data.data.shipment_details.shipment.status_id, data.data.shipment_details.shipment_history);

                        if (data.data.shipment_details.shipment.status_id >= '5') {
                            if (data.data.shipment_details.shipment_stops.length !== 0) {
                                $('.shipment-stops').show();
                                populateShipmentStops(data.data.shipment_details.shipment_stops);
                            }
                        }

                        if (data.data.shipment_details.shipment.status_id == '8') {
                            $('#receiver_data').show();
                            $('#delivered_to_first_name').text(delivery_details.first_name);
                            $('#delivered_to_last_name').text(delivery_details.last_name);
                        }


                    } else {
                        console.log('Error:', data.message);
                        // Hide the logo placeholder
                        $('#logo-placeholder').show();

                        // Show the status box and timeline
                        $('#status-box').hide();
                        $('#status-timeline').hide();
                        $('#shipment_data').hide();

                        $('#default_message').text("There was error, please try again later !")
                    }
                },
                error: function (xhr, status, error) {
                    console.log('AJAX Error:', status, error);
                    console.log('Response Text:', xhr.responseText);
                }
            });
        }

    });
</script>

</body>

</html>
