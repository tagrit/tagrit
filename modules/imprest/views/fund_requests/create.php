<?php init_head(); ?>
<style>
    #fund-request-form {
        max-width: 800px;
        margin: auto;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 10px;
        background-color: #f9f9f9;
    }

    .category-section {
        margin-bottom: 20px;
    }

    .category {
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 15px;
        background-color: #fff;
    }

    .trainer-input {
        margin-top: 10px;
    }

    .trainer-button {
        margin-top: 10px;
    }

    .category h3 {
        margin: 0 0 10px 0;
        font-size: 18px;
        font-weight: bold;
    }

    .add-subcategory {
        display: block;
        margin: 10px 0;
        padding: 5px 10px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .add-subcategory:hover {
        background-color: #0056b3;
    }

    .subcategory-list {
        margin-top: 10px;
    }

    .subcategory-item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        gap: 10px;
    }

    .subcategory-item select,
    .subcategory-item input {
        padding: 5px;
        flex: 1;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .remove-button {
        padding: 5px 10px;
        background-color: #f44336;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 0px;
    }

    .remove-button:hover {
        background-color: #d32f2f;
    }

    .summary-section table {
        width: 100%;
        border-collapse: collapse;
    }

    .summary-section table th,
    .summary-section table td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: left;
    }

    .all_total {
        width: 100%;
        font-size: 16px;
        font-weight: bold;
        margin: 20px 20px 20px 0px;
        padding: 15px 0px 15px 10px;
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 5px;
        text-align: left;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .all_total strong {
        color: #333;
    }

    .all_total span {
        font-weight: bold;
        margin: 0 5px;
    }

    .revenue {
        color: #0066cc; /* Blue for revenue */
    }

    .expenses {
        color: #ff9800; /* Orange for expenses */
    }

    .net-profit {
        color: #4caf50; /* Green for profit */
    }

    .net-profit.negative {
        color: #f44336; /* Red for loss */
    }


    #submit_btn {
        margin-top: 20px;
        padding: 10px 15px;
        border: none;
        background-color: #007bff;
        color: white;
        border-radius: 5px;
        cursor: pointer;
    }

    button:hover {
        background-color: #0056b3;
    }

    .category-section {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }

    .category-section label {
        font-size: 16px;
        font-weight: bold;
        margin-right: 15px; /* Add spacing between the label and the dropdown */
        white-space: nowrap; /* Prevent label from wrapping */
    }

    .category-section select {
        flex: 1; /* Allow the dropdown to grow and take up remaining space */
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #f9f9f9;
        font-size: 16px;
        color: #333;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3E%3Cpath fill='%23666' d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 16px 16px;
    }

    .category-section select:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }

    .fund-request-heading {
        font-size: 24px; /* Larger font size for better emphasis */
        font-weight: bold; /* Make it bold for clarity */
        color: #007bff; /* Use a visually appealing color */
        text-align: center; /* Center-align the heading */
        text-transform: uppercase; /* Make the text uppercase for importance */
        margin-bottom: 20px; /* Add spacing below the heading */
        border-bottom: 3px solid #007bff; /* Add an underline effect */
        display: inline-block; /* To keep underline closer to the text */
        padding-bottom: 5px; /* Add padding between the text and underline */
    }

    .subcategory-item {
        width: 100%;
        margin-bottom: 15px;
        padding: 15px;
        border: 1px solid #ccc;
        border-radius: 8px;
        background-color: #f9f9f9;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        margin-bottom: 10px;
    }

    label {
        font-size: 14px;
        font-weight: bold;
        margin-bottom: 5px;
    }

    input, select {
        width: 100%;
        padding: 10px;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    .form-group-container {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 20px;
    }

    .form-group {
        flex: 1 1 calc(33.33% - 16px); /* Each item takes up a third of the row, minus the gap */
        min-width: 200px; /* Ensure reasonable width for smaller screens */
    }

    .remove-button {
        display: block;
        margin: 10px 0 0 auto;
        padding: 5px 10px;
        background-color: #d9534f;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .remove-button:hover {
        background-color: #c9302c;
    }

    /* Container Styling */
    .custom-container {
        max-width: 800px;
        margin: auto;
        background-color: #f8f9fa;
        border-radius: 10px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        padding: 20px;
        font-family: Arial, sans-serif;
    }

    /* Heading */
    .custom-heading {
        text-align: center;
        margin-bottom: 20px;
        font-size: 24px;
        font-weight: bold;
        color: #333;
    }

    /* Row Styling */
    .custom-row {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 20px;
    }

    /* Column Styling */
    .custom-col {
        flex: 1 1 calc(50% - 20px); /* 50% width with gap adjustment */
        display: flex;
        flex-direction: column;
    }

    .custom-col-full {
        flex: 1 1 100%; /* Full width */
    }

    /* Label Styling */
    .custom-label {
        margin-bottom: 5px;
        font-size: 14px;
        font-weight: bold;
        color: #333;
    }

    /* Input Styling */
    .custom-input {
        padding: 10px;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 5px;
        width: 100%;
    }


    /* Button Styling */
    .custom-button {
        padding: 10px 20px;
        font-size: 16px;
        font-weight: bold;
        color: #fff;
        background-color: #007bff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .custom-button:hover {
        background-color: #0056b3;
    }

    .error-message {
        color: red;
    }

    .input-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        width: 100%;
    }

    .trainer-input {
        flex: 0 0 90%; /* 80% of the width */
        margin-top: 10px; /* Add margin on top of each input */
        padding: 8px;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .cancel-trainer-btn {
        padding-top: 5px;
        margin-top: 10px;
        background-color: transparent;
        border: 0px;
        color: red;
    }

    .cancel-trainer-btn:hover {
        background-color: transparent; /* Transparent on hover */
    }

    .alert-container {
        position: fixed;
        top: 50%;
        left: 60%;
        transform: translate(-50%, -50%);
        background-color: #ffcccc;
        padding: 20px 40px;
        border: 1px solid #ff6b6b;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        text-align: center;
        z-index: 1000;
    }

    .alert-container h2 {
        color: #d9534f;
        margin-bottom: 15px;
    }

    .alert-container p {
        color: #555;
        font-size: 16px;
    }

    .alert-container .close-btn {
        margin-top: 10px;
        padding: 10px 20px;
        background-color: #d9534f;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
    }

    .alert-container .close-btn:hover {
        background-color: #c9302c;
    }

    .notification {
        background-color: #ffe6e6; /* Light red background */
        color: #cc0000; /* Red text */
        border: 1px solid #cc0000; /* Red border */
        border-radius: 8px;
        padding: 15px;
        margin: 20px 0;
        font-size: 16px;
        line-height: 1.5;
        text-align: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .notification strong {
        font-weight: bold;
    }


</style>

<div id="wrapper">
    <div class="content">
        <?php if (!empty($totalAmountRequested) && ($totalAmountRequested - $totalAmountCleared) > intval($max_unreconciled_amount)): ?>
            <div class="notification">
                <p>
                    <?php
                    $description = 'The unreconciled amount is <strong>' . number_format($totalAmountRequested - $totalAmountCleared) .
                        '</strong>, which exceeds the threshold of <strong>' . number_format($max_unreconciled_amount) .
                        '</strong>. Please reconcile the unreconciled funds before requesting more funds.';
                    echo $description;
                    ?>
                </p>
            </div>
        <?php else: ?>
        <div id="fund-request-form">
            <h3 class="fund-request-heading">Fund Request</h3>

            <?php echo form_open('admin/imprest/fund_requests/store', [
                'id' => 'store-fund-request-form',
                'enctype' => 'multipart/form-data'
            ]); ?>

            <div class="category-section">
                <label for="events">Event:</label>
                <select class="form-control selectpicker" data-live-search="true" name="event_id" id="events" required>
                    <?php if (!empty($events)): ?>
                        <?php foreach ($events as $index => $event): ?>
                            <option value="<?= htmlspecialchars($event->id) ?>" <?= $index === 0 ? 'selected' : '' ?>>
                                <?= strlen($event->name) > 90 ? htmlspecialchars(substr($event->name, 0, 27)) . '...' : htmlspecialchars($event->name) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled selected>No events available</option>
                    <?php endif; ?>
                </select>
            </div>


            <?php if (!empty($mandatory_fields)): ?>
                <hr style="border-color:grey;">
                <div class="custom-container">
                    <div class="custom-row">
                        <?php if (in_array('venue', $mandatory_fields ?? [])): ?>
                            <div class="custom-col">
                                <label for="venue" class="custom-label">Venue</label>
                                <input type="text" id="venue" name="venue" class="custom-input"
                                       placeholder="Enter Venue"
                                       required>
                            </div>
                        <?php endif; ?>

                        <?php if (in_array('organization', $mandatory_fields ?? [])): ?>
                            <div class="custom-col">
                                <label for="organization" class="custom-label">Organization</label>
                                <input type="text" id="organization" name="organization" class="custom-input"
                                       placeholder="Enter Organization"
                                       required>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (in_array('dates', $mandatory_fields ?? [])): ?>
                        <div class="custom-row">
                            <div class="custom-col">
                                <label for="start_date" class="custom-label">Start Date</label>
                                <input type="date" id="start_date" name="start_date" class="custom-input"
                                       required>
                            </div>
                            <div class="custom-col">
                                <label for="end_date" class="custom-label">End Date</label>
                                <input type="date" id="end_date" name="end_date" class="custom-input"
                                       required>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="custom-row">
                        <?php if (in_array('delegates_details', $mandatory_fields ?? [])): ?>
                            <div class="custom-col">
                                <label for="no_of_delegates" class="custom-label">Number of Delegates</label>
                                <input type="number" id="no_of_delegates" name="no_of_delegates" class="custom-input"
                                       placeholder="Enter number" required>
                            </div>
                            <div class="custom-col">
                                <label for="charges_per_delegate" class="custom-label">Charges Per Delegate</label>
                                <input type="number" id="charges_per_delegate" name="charges_per_delegate"
                                       class="custom-input"
                                       placeholder="Enter number" required>
                            </div>

                        <?php endif; ?>


                        <?php if (in_array('trainers', $mandatory_fields ?? [])): ?>
                            <div class="custom-col">
                                <label for="trainer" class="custom-label">Trainers</label>
                                <div id="trainer-container">
                                    <input type="text" id="trainer1" name="trainers[]" class="custom-input"
                                           placeholder="Enter Trainer" required>
                                </div>
                                <button style="margin-top:10px;" type="button" id="add-trainer" class="custom-button">
                                    Add
                                    Trainer
                                </button>
                            </div>
                        <?php endif; ?>

                        <?php if (in_array('facilitator', $mandatory_fields ?? [])): ?>
                            <div class="custom-col">
                                <label for="facilitator" class="custom-label">Facilitator</label>
                                <input type="text" id="facilitator" name="facilitator"
                                       value="<?php echo $facilitator; ?>" class="custom-input" readonly required>
                            </div>
                        <?php endif; ?>

                        <?php if (in_array('setup', $mandatory_fields ?? [])): ?>
                            <div class="custom-col">
                                <label for="setup" class="control-label">Setup</label>
                                <select id="setup" name="setup" class="form-control selectpicker"
                                        data-live-search="true" required>
                                    <option value="">Select Setup</option>
                                    <option value="Physical">Physical</option>
                                    <option value="Virtual">Virtual</option>
                                </select>
                            </div>
                        <?php endif; ?>

                        <?php if (in_array('type', $mandatory_fields ?? [])): ?>
                            <div class="custom-col">
                                <label for="type" class="control-label">Type</label>
                                <select id="type" name="type" class="form-control selectpicker"
                                        data-live-search="true" required>
                                    <option value="">Select Type</option>
                                    <option value="Local">Local</option>
                                    <option value="International">International</option>
                                </select>
                            </div>
                        <?php endif; ?>

                        <?php if (in_array('revenue', $mandatory_fields ?? [])): ?>
                            <div class="custom-col">
                                <label for="revenue" class="custom-label">Revenue</label>
                                <input type="number" id="revenue" name="revenue" class="custom-input"
                                       placeholder="Enter revenue" required>
                            </div>
                        <?php endif; ?>

                    </div>
                    <div class="custom-row">
                        <?php if (in_array('delegates_details', $mandatory_fields ?? [])): ?>
                            <div class="col-12">
                                <label class="custom-label">Delegates Details</label>
                                <div id="delegates-container">
                                    <div class="row align-items-center delegate-entry">
                                        <div class="col-md-3">
                                            <input type="text" name="delegates[0][first_name]" class="custom-input"
                                                   placeholder="First Name" required>
                                        </div>
                                        <div style="margin-left:-20px;" class="col-md-3">
                                            <input type="text" name="delegates[0][last_name]" class="custom-input"
                                                   placeholder="Last Name" required>
                                        </div>
                                        <div style="margin-left:-20px;" class="col-md-3">
                                            <input type="email" name="delegates[0][email]" class="custom-input"
                                                   placeholder="Email" required>
                                        </div>
                                        <div style="margin-left:-20px;" class="col-md-3">
                                            <input type="text" name="delegates[0][phone]" class="custom-input"
                                                   placeholder="Phone" required>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" id="add-delegate" class="custom-button" style="margin-top: 10px;">
                                    Add
                                    Delegate
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <hr style="border-color:grey;">
            <div class="category-section">
                <label for="categories">Expense Category:</label>
                <select id="categories" onchange="addCategory()">
                    <option value="" disabled selected>Select a category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category->id) ?>"
                                data-name="<?= htmlspecialchars($category->name) ?>"
                        ><?= htmlspecialchars($category->name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="category-container">
                <!-- Categories with subcategories will dynamically appear here -->
            </div>

            <div class="summary-section">
                <h4>Summary</h4>
                <table>
                    <thead>
                    <tr>
                        <th>Category</th>
                        <th>Subcategories</th>
                        <th>Total Amount</th>
                    </tr>
                    </thead>
                    <tbody id="summary-table">
                    <!-- Summaries will dynamically appear here -->
                    </tbody>
                </table>
                <div class="all_total">
                    <?php if (in_array('revenue', $mandatory_fields ?? [])): ?>
                        <strong>Total Revenue:</strong> KES <span id="total-revenue" class="revenue">0.00</span>
                        <strong>|</strong>
                    <?php endif; ?>

                    <strong>Total Expenses:</strong> KES <span id="total-amount" class="expenses">0.00</span>

                    <?php if (in_array('revenue', $mandatory_fields ?? [])): ?>
                        <strong>|</strong>
                        <strong>Net Profit:</strong> KES <span id="net-profit" class="net-profit">0.00</span>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" id="submit_btn">Submit Request</button>

            <?php echo form_close(); ?>

        </div>
    </div>
    <?php endif; ?>


</div>
</div>
<?php init_tail(); ?>
<script>

    document.getElementById('add-trainer').addEventListener('click', function () {
        const container = document.getElementById('trainer-container');
        const currentInputs = container.getElementsByTagName('input');

        // Limit the number of inputs to 5
        if (currentInputs.length < 5) {
            const inputWrapper = document.createElement('div');
            inputWrapper.classList.add('input-wrapper');

            const newInput = document.createElement('input');
            newInput.type = 'text';
            newInput.name = 'trainers[]';
            newInput.className = 'custom-input';
            newInput.className = 'trainer-input';
            newInput.placeholder = 'Enter trainer';
            container.appendChild(newInput);

            const cancelBtn = document.createElement('button');
            cancelBtn.type = 'button';
            cancelBtn.className = 'cancel-trainer-btn';
            cancelBtn.innerHTML = "<i class='fa fa-trash'></i>";

            // Add input and cancel button to the wrapper
            inputWrapper.appendChild(newInput);
            inputWrapper.appendChild(cancelBtn);

            // Add the new wrapper to the container
            container.appendChild(inputWrapper);

            // Add event listener to remove the input when cancel is clicked
            cancelBtn.addEventListener('click', function () {
                inputWrapper.remove();
            });
        }
    });


    const form = document.getElementById("store-fund-request-form");
    const submitButton = form.querySelector('button[type="submit"]');

    form.addEventListener("submit", (event) => {
        if (submitButton.disabled) {
            event.preventDefault();
            alert("Please fill out all required fields.");
        }
    });

    let categories = {};
    let totalAmount = 0;

    // Predefined subcategories for each category
    const subcategories = <?php echo json_encode($subcategories); ?>;
    const allCategories = <?php echo json_encode($categories); ?>;


    function addCategory() {

        const category = document.getElementById('categories').value;
        const categoryName = document.getElementById('categories').options[document.getElementById('categories').selectedIndex].getAttribute('data-name');


        if (!categories[category]) {
            categories[category] = {
                'categoryName': categoryName
            };

            const container = document.getElementById('category-container');
            const categoryDiv = document.createElement('div');
            categoryDiv.className = 'category';
            categoryDiv.id = `category-${category}`; // Assign an ID to identify the card
            categoryDiv.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h3>${categoryName}</h3>
                    <button class="remove-button" onclick="removeCategory('${category}')"><i class="fa fa-trash"></i></button>
                </div>
                <button class="add-subcategory" onclick="addSubcategory('${category}')">+ Add Subcategory</button>
                <div class="subcategory-list" id="subcategory-${category}">
                    <!-- Subcategories will be added here -->
            </div>
            `;
            container.appendChild(categoryDiv);

            updateSummary();
            }
    }

    function removeCategory(category) {
        // Remove the category div
        const categoryDiv = document.getElementById(`category-${category}`);
        if (categoryDiv) {
            categoryDiv.remove();
        }

        // Remove the category from the categories object
        delete categories[category];

        // Update the summary table and total amount
        updateSummary();
    }

    function addSubcategory(category) {
        const subcategoryList = document.getElementById(`subcategory-${category}`);

        const subcategoryDiv = document.createElement('div');
        subcategoryDiv.className = 'subcategory-item';

        if (category === '3') {
            subcategoryDiv.innerHTML = `
               <div class="form-group-container">
                <div class="form-group">
                    <label for="hotel-name">Hotel Name:</label>
                    <input type="text" name="conferencing_hotel_name" placeholder="Enter Hotel Name" class="hotel-name" required>
                </div>
                <div class="form-group">
                    <label for="num-persons">Number of Persons:</label>
                    <input type="number" name="conferencing_num_persons" placeholder="Enter Number of Persons" class="conferencing-num-persons" oninput="calculateConferencingAmount(this.closest('.subcategory-item'),'${category}')" required>
                </div>
                <div class="form-group">
                    <label for="charges-per-person">Charges Per Person:</label>
                    <input type="number" name="conferencing_charges_per_person" placeholder="Enter Charges per Person" class="conferencing-charges-per-person" oninput="calculateConferencingAmount(this.closest('.subcategory-item'),'${category}')" required>
                </div>
                <div class="form-group">
                    <label for="num-days">Number of Days:</label>
                    <input type="number" name="conferencing_num_days" placeholder="Enter Number of Days" class="conferencing-num-days" oninput="calculateConferencingAmount(this.closest('.subcategory-item'),'${category}')" required>
                </div>
                <div class="form-group">
                    <label for="amount">Total Amount:</label>
                    <input type="number"  name="amounts[]" placeholder="Total Amount" class="subcategory-amount" readonly required>
                </div>
              <input type="hidden" name="subcategories[]" value="Conferencing Amount">
            </div>
            <button style="margin-top:-5px;" class="remove-button" onclick="removeSubcategory(this, '${category}')">Remove</button>
            `;
        } else if (category === '4') {
            subcategoryDiv.innerHTML = `
               <div class="form-group-container">
                <div class="form-group">
                    <label for="hotel-name">Hotel Name:</label>
                    <input type="text" name="accommodation_hotel_name" placeholder="Enter Hotel Name" class="accommodation-hotel-name" required>
                </div>
                <div class="form-group">
                    <label for="num-persons">Number of Persons:</label>
                    <input type="number" name="accommodation_num_persons" placeholder="Enter Number of Persons" class="accommodation-num-persons" oninput="calculateAccomodationAmount(this.closest('.subcategory-item'),'${category}')" required>
                </div>
                <div class="form-group">
                    <label for="charges-per-person">Charges Per Person(Bed & Breakfast):</label>
                    <input type="number" name="accommodation_charges_per_person" placeholder="Enter Charges per Person" class="accommodation-charges-per-person" oninput="calculateAccomodationAmount(this.closest('.subcategory-item'),'${category}')" required>
                </div>
                <div class="form-group">
                    <label for="charges-per-person">Charges Per Person (Dinner):</label>
                    <input type="number" name="accommodation_dinner_per_person" placeholder="Enter Dinner Amount per Person" class="accommodation-dinner-per-person" oninput="calculateAccomodationAmount(this.closest('.subcategory-item'),'${category}')" required>
                </div>
                <div class="form-group">
                    <label for="num-days">Number of Days:</label>
                    <input type="number" name="accommodation_num_days" placeholder="Enter Number of Days" class="accommodation-num-days" oninput="calculateAccomodationAmount(this.closest('.subcategory-item'),'${category}')" required>
                </div>
                <div class="form-group">
                    <label for="amount">Total Amount:</label>
                    <input type="number"  name="amounts[]" placeholder="Total Amount" class="subcategory-amount" readonly required>
                </div>
              <input type="hidden" name="subcategories[]" value="Accommodation Amount">
            </div>
            <button style="margin-top:-5px;" class="remove-button" onclick="removeSubcategory(this, '${category}')">Remove</button>
            `;
        } else if (category === '6') {
            subcategoryDiv.innerHTML = `
               <select style="margin-top:24px;" name="subcategories[]" class="subcategory-name">
                   ${subcategories[category]
                .map(subcat => `<option value="${subcat}">${subcat}</option>`)
                .join("")}
               </select>
                <div style="width:150px;">
                    <label for="hotel-name">Number of Delegates:</label>
                    <input type="text" name="number_of_delegates[]" placeholder="Enter Number of Delegates" oninput="calculateClientGifts(this.closest('.subcategory-item'),'${category}')" class="number-of-delegates" required>
                </div>
               <div style="width:150px;">
                    <label for="hotel-name">Amount Per Gift:</label>
                    <input type="text" name="amount_per_gift[]" placeholder="Enter Amount Per Gift" oninput="calculateClientGifts(this.closest('.subcategory-item'),'${category}')" class="amount-per-gift" required>
                </div>
               <input style="margin-top:24px;" type="number" name="amounts[]" readonly placeholder="Amount" class="subcategory-amount" oninput="updateCategoryTotal('${category}')" required>
               <button style="margin-top:24px;" class="remove-button" onclick="removeSubcategory(this, '${category}')">Remove</button>
               `;
        } else if (category === '7') {
            subcategoryDiv.innerHTML = `
               <div class="form-group-container">
                <div class="form-group">
                    <label for="speaker-name">Speaker Name:</label>
                    <input type="text" name="speaker_name" placeholder="Enter Speaker Name" class="speaker-name" required>
                </div>
                <div class="form-group">
                    <label for="speaker-rate-per-day">Rate Per Day:</label>
                    <input type="number" name="speaker_rate_per_day" placeholder="Enter Rate Per Day" class="speaker-rate-per-day" oninput="calculateSpeakerAmount(this.closest('.subcategory-item'),'${category}')" required>
                </div>
                <div class="form-group">
                    <label for="num-days">Number of Days:</label>
                    <input type="number" name="speaker_num_days" placeholder="Enter Number of Days" class="speaker-num-days" oninput="calculateSpeakerAmount(this.closest('.subcategory-item'),'${category}')" required>
                </div>
                <div class="form-group">
                    <label for="amount">Total Amount:</label>
                    <input type="number"  name="amounts[]" placeholder="Total Amount" class="subcategory-amount" readonly required>
                </div>
              <input type="hidden" name="subcategories[]" value="Speaker Costs">
            </div>
            <button style="margin-top:-5px;" class="remove-button" onclick="removeSubcategory(this, '${category}')">Remove</button>
            `;
        } else {
            subcategoryDiv.innerHTML = `
            <select name="subcategories[]" class="subcategory-name">
                ${subcategories[category]
                .map(subcat => `<option value="${subcat}">${subcat}</option>`)
                .join("")}
            </select>
            <input type="number" name="amounts[]" placeholder="Amount" class="subcategory-amount" oninput="updateCategoryTotal('${category}')" required>
            <button style="margin-top:-5px;" class="remove-button" onclick="removeSubcategory(this, '${category}')">Remove</button>
            `;
        }

        subcategoryList.appendChild(subcategoryDiv);
    }

    function calculateConferencingAmount(subcategoryItem, category) {
        // Get inputs within the specific subcategory-item
        const numPersons = subcategoryItem.querySelector('.conferencing-num-persons')?.value || 0;
        const chargesPerPerson = subcategoryItem.querySelector('.conferencing-charges-per-person')?.value || 0;
        const numDays = subcategoryItem.querySelector('.conferencing-num-days')?.value || 0;

        // Calculate the total amount
        const totalAmount = numPersons * chargesPerPerson * numDays;

        // Update the total amount field
        const amountField = subcategoryItem.querySelector('.subcategory-amount');
        if (amountField) {
            amountField.value = totalAmount.toFixed(2); // Ensure 2 decimal places for consistency
        }

        updateCategoryTotal(category);
    }

    function calculateSpeakerAmount(subcategoryItem, category) {

        // Get inputs within the specific subcategory-item
        const ratePerDay = subcategoryItem.querySelector('.speaker-rate-per-day')?.value || 0;
        const numDays = subcategoryItem.querySelector('.speaker-num-days')?.value || 0;

        // Calculate the total amount
        const totalAmount = ratePerDay * numDays;

        // Update the total amount field
        const amountField = subcategoryItem.querySelector('.subcategory-amount');
        if (amountField) {
            amountField.value = totalAmount.toFixed(2); // Ensure 2 decimal places for consistency
        }

        updateCategoryTotal(category);
    }


    function calculateAccomodationAmount(subcategoryItem, category) {
        // Get inputs within the specific subcategory-item
        const numPersons = subcategoryItem.querySelector('.accommodation-num-persons')?.value || 0;
        let chargesPerPerson = subcategoryItem.querySelector('.accommodation-charges-per-person')?.value || 0;
        const numDays = subcategoryItem.querySelector('.accommodation-num-days')?.value || 0;
        const dinnerPerPerson = subcategoryItem.querySelector('.accommodation-dinner-per-person')?.value || 0;
        chargesPerPerson = parseInt(chargesPerPerson) + parseInt(dinnerPerPerson);

        // Calculate the total amount
        const totalAmount = chargesPerPerson * numPersons * numDays;

        // Update the total amount field
        const amountField = subcategoryItem.querySelector('.subcategory-amount');
        if (amountField) {
            amountField.value = totalAmount.toFixed(2); // Ensure 2 decimal places for consistency
        }

        updateCategoryTotal(category);
    }


    function calculateClientGifts(subcategoryItem, category) {

        const numPersons = subcategoryItem.querySelector('.number-of-delegates')?.value || 0;
        const amount_per_gift = subcategoryItem.querySelector('.amount-per-gift')?.value || 0;

        const totalAmount = parseInt(numPersons) * parseInt(amount_per_gift);

        // Update the total amount field
        const amountField = subcategoryItem.querySelector('.subcategory-amount');
        if (amountField) {
            amountField.value = totalAmount.toFixed(2); // Ensure 2 decimal places for consistency
        }

        updateCategoryTotal(category);

    }


    function removeSubcategory(element, category) {
        const parent = element.parentNode;
        parent.remove();
        updateCategoryTotal(category);
    }

    function updateCategoryTotal(category) {

        const subcategoryList = document.querySelectorAll(`#subcategory-${category} .subcategory-item`);
        let categoryTotal = 0;

        subcategoryList.forEach(item => {
            const amount = parseFloat(item.querySelector('.subcategory-amount').value) || 0;
            categoryTotal += amount;
        });

        categories[category] = categoryTotal;
        updateSummary();
    }

    function getCategoryNameById(id) {
        const category = allCategories.find(category => category.id === id);
        return category ? category.name : "Unknown Category";
    }

    function updateSummary() {
        const summaryTable = document.getElementById('summary-table');
        summaryTable.innerHTML = '';

        totalAmount = 0;

        for (const [category, total] of Object.entries(categories)) {
            const categoryName = getCategoryNameById(category); // Get category name from ID
            const row = summaryTable.insertRow();
            row.innerHTML = `
                <td>${categoryName}</td>
                <td>${total > 0 ? total : '-'}</td>
                <td>KES ${total.toFixed(2)}</td>
            `;
            totalAmount += total;
        }

        document.getElementById('total-amount').textContent = totalAmount.toFixed(2);
        const revenueInput = parseFloat(document.getElementById('revenue').value) || 0;
        const netProfit = revenueInput - totalAmount;
        document.getElementById('net-profit').textContent = netProfit.toFixed(2);
    }


    document.getElementById('no_of_delegates').addEventListener('input', calculateRevenue);
    document.getElementById('charges_per_delegate').addEventListener('input', calculateRevenue);

    function calculateRevenue() {
        const noOfDelegates = parseFloat(document.getElementById('no_of_delegates').value) || 0;
        const chargesPerDelegate = parseFloat(document.getElementById('charges_per_delegate').value) || 0;
        const revenue = noOfDelegates * chargesPerDelegate;

        document.getElementById('revenue').value = revenue.toFixed(2); // Set revenue with 2 decimal points
        document.getElementById('total-revenue').textContent = revenue.toFixed(2); // Set revenue with 2 decimal points

        const totalAmount = parseFloat(document.getElementById('total-amount').textContent) || 0;
        const netProfit = revenue - totalAmount;
        document.getElementById('net-profit').textContent = netProfit.toFixed(2);

    }

    let delegateIndex = 1;

    document.getElementById("add-delegate").addEventListener("click", function () {
        let delegateContainer = document.getElementById("delegates-container");

        let delegateEntry = document.createElement("div");
        delegateEntry.classList.add("row", "align-items-center", "delegate-entry");
        delegateEntry.innerHTML = `
            <div class="mtop4 col-md-3">
                <input type="text" name="delegates[${delegateIndex}][first_name]" class="custom-input" placeholder="First Name" required>
            </div>
            <div style="margin-left:-20px;" class="mtop4 col-md-3">
                <input type="text" name="delegates[${delegateIndex}][last_name]" class="custom-input" placeholder="Last Name" required>
            </div>
            <div style="margin-left:-20px;" class="mtop4 col-md-3">
                <input type="email" name="delegates[${delegateIndex}][email]" class="custom-input" placeholder="Email" required>
            </div>
            <div style="margin-left:-20px;" class="mtop4 col-md-3">
                <input type="text" name="delegates[${delegateIndex}][phone]" class="custom-input" placeholder="Phone" required>
            </div>
            <div style="margin-left:-20px;" class="mtop4 col-md-1 text-center">
                <button style="color:red; padding:8px; background-color: transparent;" type="button" class="remove-delegate custom-button">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        `;

        delegateContainer.appendChild(delegateEntry);
        delegateIndex++;

        delegateEntry.querySelector(".remove-delegate").addEventListener("click", function () {
            delegateEntry.remove();
        });
    });

    document.querySelectorAll(".remove-delegate").forEach(button => {
        button.addEventListener("click", function () {
            this.closest(".delegate-entry").remove();
        });
    });
</script>
