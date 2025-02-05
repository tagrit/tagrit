<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php load_courier_styles(); ?>
<?php
// Include Flatpickr CSS
echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">';

// Include Select2 CSS
echo '<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />';

?>

<style>
    .select2-container .select2-selection--single {
        background-color: #f9fafb;
        border: 1px solid #d1d5db;
        color: #111827;
        font-size: 0.875rem;
        border-radius: 0.375rem;
        padding: 5px 10px 10px 10px;
        width: 100%;
        height: 40px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .select2-selection__arrow {
        transform: translateY(30%);
    }

    .hidden {
        display: none;
    }

    #sync-loader {
        margin-left: 10px;
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

    /* Switch container */
    .switch {
        position: relative;
        display: inline-block;
        width: 40px;
        height: 20px;
        margin-top: 10px;
    }

    /* Hide the default checkbox */
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    /* The slider */
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: 0.4s;
        border-radius: 34px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 2px;
        bottom: 2px;
        background-color: white;
        transition: 0.4s;
        border-radius: 50%;
    }

    /* Toggle the background when checked */
    input:checked + .slider {
        background-color: #4caf50;
    }

    /* Move the slider */
    input:checked + .slider:before {
        transform: translateX(20px);
    }
</style>

<div id="wrapper">
    <div class="content">
        <div class="row">

            <?php

            // Retrieve `show_company_section` from the session and set a default if not present
            $show_company_section = $this->session->userdata('show_company_section') ?? false;

            ?>

            <div class="col-md-3">
                <h4 class="tw-font-semibold tw-mt-0 tw-text-neutral-800">
                    Agents
                </h4>
                <ul class="nav navbar-pills navbar-pills-flat nav-pills nav-stacked">
                    <li class="settings-group-create-shipments">
                        <a href="<?php echo admin_url('courier/agents/main?group=create_agent'); ?>">
                            <i class="fa fa-plus-square menu-icon"></i>
                            Create Agent
                        </a>
                    </li>
                    <li class="settings-group-list-of-shipments">
                        <a href="<?php echo admin_url('courier/agents/main?group=list_agents'); ?>">
                            <i class="fa fa-list menu-icon"></i>
                            List of Agents
                        </a>
                    </li>
                    <li class="settings-group-list-of-shipments">
                        <a href="#" id="sync-permissions-btn">
                            <i class="fa fa-refresh menu-icon"></i>
                            <span id="sync-text">Sync Permissions</span>
                            <span id="sync-loader" class="hidden"><i class="fa fa-spinner fa-spin"></i></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="col-md-9">
                <?php echo $group_content ?? " "; ?>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>

<!-- Scripts should be at the end of the body -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {


        $('.setting-switch').change(function () {

            const $checkbox = $(this); // Store reference to the checkbox
            const agentId = $checkbox.data('agent-id');
            const status = $checkbox.is(':checked') ? 1 : 0;


            $.ajax({
                url: '<?= admin_url("courier/agents/update_status"); ?>',
                type: 'POST',
                data: {
                    agent_id: agentId,
                    status: status
                },
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        if (status == '1') {
                            alert_float('success', 'Agent activated successfully');
                        }
                        if (status == '0') {
                            alert_float('success', 'Agent deactivated successfully');
                        }
                    } else {
                        alert('Failed to update status');
                        $checkbox.prop('checked', !status); // Revert switch if update fails
                    }
                },
                error: function (xhr, status, error) {

                    // Display more detailed error message
                    let errorMessage = 'Error while changing agent status';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message; // If the server returns a detailed message
                    } else if (xhr.status) {
                        errorMessage += ` (Status: ${xhr.status} - ${xhr.statusText})`; // Append HTTP status
                    }

                    console.log(errorMessage)
                    alert_float('danger', 'Error while changing agent status');
                    $checkbox.prop('checked', !status); // Revert switch on error
                }
            });
        });


        $('#sync-permissions-btn').on('click', function (e) {
            e.preventDefault();
            $('#sync-loader').removeClass('hidden');

            const $button = $(this);
            const $syncText = $('#sync-text');
            const originalText = $syncText.text();

            $button.addClass('disabled')
            $syncText.text('Syncing...');

            $.ajax({
                url: '<?php echo admin_url("courier/agents/sync_role_permissions"); ?>',
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function (response) {
                    $('#sync-loader').addClass('hidden');
                    alert_float('success', response.message);
                    $button.removeClass('disabled')
                    $syncText.text(originalText); // Restore original button text

                },
                error: function (xhr, status, error) {
                    $('#sync-loader').addClass('hidden');
                    alert_float('danger', 'An error occurred while syncing permissions: ' + error);
                    $button.removeClass('disabled')
                    $syncText.text(originalText); // Restore original button text

                }
            });
        });


        //individual
        $('#country_id').select2({});
        $('#state_id').select2({});

        //company
        $('#company_country_id').select2({});
        $('#company_state_id').select2({});

        const showCompanySection = <?php echo $show_company_section ? 'true' : 'false'; ?>;

        // Elements for sections
        const senderSection = document.getElementById('individualContent');
        const companySection = document.getElementById('companyContent');

        // Function to toggle sections based on selection
        window.toggleAgentType = function (type = null) {
            // If `type` is null, retrieve the value from the element with id 'type'
            type = type || document.getElementById('type').value;

            // Toggle display based on `type`
            senderSection.style.display = type === 'individual' ? 'block' : 'none';
            companySection.style.display = type === 'company' ? 'block' : 'none';
            generateAgentNumber();

        };


        // Initialize toggle based on showCompanySection value
        if (showCompanySection) {
            toggleAgentType('company');
        } else {
            toggleAgentType('individual');
        }

        generateAgentNumber();

        function generateAgentNumber() {
            const agentType = document.getElementById('type').value;
            if (agentType === 'company') {
                const country_id = document.getElementById('company_country_id').value;
                setTimeout(function () {
                    populateAgentNumber(country_id, 1);
                }, 100);

            } else {
                const country_id = document.getElementById('country_id').value;
                setTimeout(function () {
                    populateAgentNumber(country_id, 1);
                }, 100);
            }
        }


        window.populateAgentNumber = function (country_id, state_id) {
            const agentNumber = document.getElementById("unique_number");
            const companyAgentNumber = document.getElementById("company_unique_number");

            $.ajax({
                url: '<?php echo admin_url("courier/agents/agent_number"); ?>',
                method: 'POST',
                data: {
                    country_id: country_id,
                    state_id: state_id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        const agentType = document.getElementById('type').value;
                        if (agentType === 'company') {
                            companyAgentNumber.value = response.new_agent_number;
                        } else {
                            agentNumber.value = response.new_agent_number;
                        }
                    } else {
                        console.error('Error generating agent number');
                    }
                },
                error: function (error) {
                    console.error('AJAX request failed', error);

                    // Check for HTTP status and display a more informative message
                    let errorMessage = 'Ajax request failed';
                    if (error.responseJSON && error.responseJSON.message) {
                        errorMessage += `: ${error.responseJSON.message}`;
                    } else if (error.status) {
                        errorMessage += ` (Status: ${error.status} - ${error.statusText})`;
                    }
                    alert_float('danger', 'Ajax request failed');
                }
            });
        };


        const state = $('#state_id');
        const companyState = $('#company_state_id');
        getStates(1, state)
        getStates(1, companyState)


        function getStates(countryId, state) {
            if (countryId) {
                $.ajax({
                    url: '<?php echo admin_url("courier/states"); ?>',
                    type: "POST",
                    data: {country_id: countryId},
                    dataType: "json",
                    success: function (data) {
                        state.empty();
                        $.each(data.states, function (key, value) {
                            state.append('<option value="' + value.id + '">' + value.name + '</option>');
                        });

                    },
                    error: function (error) {
                        alert_float('danger', 'Error retrieving states');
                        console.log(error.responseText)
                    }
                });
            } else {
                state.empty();
                state.append('<option value="">Select State</option>');
            }
        }

        $('#country_id').change(function () {
            const countryId = $(this).val();
            getStates(countryId, state)
            populateAgentNumber(countryId, 1);

        });

        $('#company_country_id').change(function () {
            const countryId = $(this).val();
            getStates(countryId, companyState);
            populateAgentNumber(countryId, 1);
        });

        $('#state_id').change(function () {
            const countryId = $('#country_id').val();
            const stateId = $(this).val();
            populateAgentNumber(countryId, stateId);
        });

        $('#company_state_id').change(function () {
            const countryId = $('#company_country_id').val();
            const stateId = $(this).val();
            populateAgentNumber(countryId, stateId);
        });


    })
</script>
