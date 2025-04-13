<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .custom-container {
        max-width: 800px;
        margin: auto;
        background-color: #f8f9fa;
        border-radius: 10px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        padding: 20px;
        font-family: Arial, sans-serif;
    }

    .custom-heading {
        text-align: center;
        margin-bottom: 20px;
        font-size: 24px;
        font-weight: bold;
        color: #333;
    }

    .custom-row {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 20px;
    }

    .custom-col {
        flex: 1 1 calc(50% - 20px);
        display: flex;
        flex-direction: column;
    }

    .custom-col-full {
        flex: 1 1 100%;
    }

    .custom-label {
        margin-bottom: 5px;
        font-size: 14px;
        font-weight: bold;
        color: #333;
    }

    .custom-input {
        padding: 10px;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 5px;
        width: 100%;
    }

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
        font-size: 12px;
    }
</style>

<div id="wrapper">
    <div class="content">
        <div class="custom-container">
            <h4 class="custom-heading">Edit Event</h4>

            <?php echo form_open('admin/imprest/events/update', [
                'id' => 'update-event-form',
                'enctype' => 'multipart/form-data'
            ]); ?>

            <input type="hidden" name="event_id" value="<?= isset($event['id']) ? $event['id'] : ''; ?>">

            <div class="custom-row">
                <div class="custom-col">
                    <label for="name" class="custom-label">Name</label>
                    <input type="text" id="name" class="custom-input" placeholder="Enter Name" name="name"
                           value="<?= set_value('name', isset($event['name']) ? $event['name'] : ''); ?>">
                    <?php echo form_error('name', '<span class="error-message">', '</span>'); ?>
                </div>
                <!--                <div class="custom-col">-->
                <!--                    <label for="venue" class="custom-label">Venue</label>-->
                <!--                    <input type="text" id="venue" class="custom-input" placeholder="Enter venue" name="venue"-->
                <!--                           value="-->
                <? //= set_value('venue', isset($event['venue']) ? $event['venue'] : ''); ?><!--">-->
                <!--                    --><?php //echo form_error('venue', '<span class="error-message">', '</span>'); ?>
                <!--                </div>-->
                <!--                <div class="custom-col">-->
                <!--                    <label for="start_date" class="custom-label">Start Date</label>-->
                <!--                    <input type="date" id="start_date" class="custom-input" name="start_date"-->
                <!--                           value="-->
                <? //= set_value('start_date', isset($event['start_date']) ? $event['start_date'] : ''); ?><!--">-->
                <!--                    --><?php //echo form_error('start_date', '<span class="error-message">', '</span>'); ?>
                <!--                </div>-->
                <!--                <div class="custom-col">-->
                <!--                    <label for="end_date" class="custom-label">End Date</label>-->
                <!--                    <input type="date" id="end_date" class="custom-input" name="end_date"-->
                <!--                           value="-->
                <? //= set_value('end_date', isset($event['end_date']) ? $event['end_date'] : ''); ?><!--">-->
                <!--                    --><?php //echo form_error('end_date', '<span class="error-message">', '</span>'); ?>
                <!--                </div>-->
            </div>

            <div class="custom-row">
                <!--                <div class="custom-col">-->
                <!--                    <label for="no_of_delegates" class="custom-label">Number of Delegates</label>-->
                <!--                    <input type="number" id="no_of_delegates" class="custom-input" placeholder="Enter number"-->
                <!--                           name="no_of_delegates"-->
                <!--                           value="-->
                <? //= set_value('no_of_delegates', isset($event['no_of_delegates']) ? $event['no_of_delegates'] : ''); ?><!--">-->
                <!--                    --><?php //echo form_error('no_of_delegates', '<span class="error-message">', '</span>'); ?>
                <!--                </div>-->
                <!--                <div class="custom-col">-->
                <!--                    <label for="charges_per_delegate" class="custom-label">Charges Per Delegate</label>-->
                <!--                    <input type="number" id="charges_per_delegate" name="charges_per_delegate" class="custom-input"-->
                <!--                           placeholder="Enter number"-->
                <!--                           value="-->
                <? //= set_value('charges_per_delegate', isset($event['charges_per_delegate']) ? $event['charges_per_delegate'] : ''); ?><!--">-->
                <!--                    --><?php //echo form_error('charges_per_delegate', '<span class="error-message">', '</span>'); ?>
                <!--                </div>-->
                <!--                <div class="custom-col">-->
                <!--                    <label for="division" class="custom-label">Division</label>-->
                <!--                    <input type="text" id="division" class="custom-input" placeholder="Enter division" name="division"-->
                <!--                           value="-->
                <? //= set_value('division', isset($event['division']) ? $event['division'] : ''); ?><!--">-->
                <!--                    --><?php //echo form_error('division', '<span class="error-message">', '</span>'); ?>
                <!--                </div>-->
                <!--                <div class="custom-col">-->
                <!--                    <label for="facilitator" class="custom-label">Facilitator</label>-->
                <!--                    <input type="text" id="facilitator" class="custom-input" placeholder="Enter facilitator"-->
                <!--                           name="facilitator" value="-->
                <? //= set_value('facilitator', isset($event['facilitator']) ? $event['facilitator'] : ''); ?><!--">-->
                <!--                    --><?php //echo form_error('facilitator', '<span class="error-message">', '</span>'); ?>
                <!--                </div>-->
                <!--                <div class="custom-col">-->
                <!--                    <label for="revenue" class="custom-label">Revenue</label>-->
                <!--                    <input type="number" id="revenue" class="custom-input" placeholder="Enter revenue" name="revenue"-->
                <!--                           value="-->
                <? //= set_value('revenue', isset($event['revenue']) ? $event['revenue'] : ''); ?><!--">-->
                <!--                    --><?php //echo form_error('revenue', '<span class="error-message">', '</span>'); ?>
                <!--                </div>-->
                <div class="custom-col-full text-left">
                    <button type="submit" class="custom-button">Update Event</button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
    $(function () {
        $('#no_of_delegates, #charges_per_delegate').on('input', function () {
            let delegates = $('#no_of_delegates').val();
            let charges = $('#charges_per_delegate').val();
            let totalRevenue = delegates * charges;
            $('#revenue').val(totalRevenue);
        });
    });
</script>
