<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
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
</style>

<div id="wrapper">
    <div class="content">
        <div class="custom-container">
            <h4 class="custom-heading">Add Event</h4>
            <?php echo form_open('admin/imprest/events/store', [
                'id' => 'create-event-form',
                'enctype' => 'multipart/form-data'
            ]); ?>
            <div class="custom-row">
                <div class="custom-col">
                    <label for="name" class="custom-label">Name</label>
                    <input type="text" id="name" name="name" class="custom-input" placeholder="Enter Name"
                           value="<?php echo set_value('name'); ?>">
                    <?php echo form_error('name', '<span class="error-message">', '</span>'); ?>
                </div>
                <div class="custom-col-full text-left">
                    <button type="submit" class="custom-button">Create Event</button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
