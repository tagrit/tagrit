$(document).ready(function () {


    function fetchFilteredData() {
        let status = $('#status').val();
        let startDate = $('#start-date').val();
        let endDate = $('#end-date').val();
        let organization = $('#organization').val();

        $.ajax({
            url: baseUrl + "admin/events_due/reports/fetch_filtered_data", // Use the global baseUrl variable
            type: "POST",
            data: {
                status: status,
                start_date: startDate,
                end_date: endDate,
                organization: organization
            },
            success: function (response) {
                $('#reports-table tbody').html(response);
            },
            error: function (xhr, status, error) {
                console.error("Error fetching data:", error);
            }
        });
    }

    // Listen to filter changes
    $('#status, #start-date, #end-date, #organization').on('change', function () {
        fetchFilteredData();
    });

    // Clear filters
    $('.clear-filters').on('click', function () {
        $('#status').val('').trigger('change');
        $('#start-date').val('').trigger('change');
        $('#end-date').val('').trigger('change');
        $('#organization').val('').trigger('change');
        fetchFilteredData();
    });
});
