$(document).ready(function () {

    function fetchFilteredData() {
        let status = $('#status').val();
        let startDate = $('#start-date').val();
        let endDate = $('#end-date').val();
        let organization = $('#organization').val();
        let query = $('#query').val();

        // Save filters to session via AJAX
        $.ajax({
            url: baseUrl + "admin/events_due/reports/save_filters",
            type: "POST",
            data: {
                status: status,
                start_date: startDate,
                end_date: endDate,
                organization: organization,
                query: query
            }
        });

        // Fetch the filtered data
        $.ajax({
            url: baseUrl + "admin/events_due/reports/fetch_filtered_data",
            type: "POST",
            data: {
                status: status,
                start_date: startDate,
                end_date: endDate,
                organization: organization,
                query: query
            },
            success: function (response) {
                $('#reports-table tbody').html(response);
            },
            error: function (xhr, status, error) {
                console.error("Error fetching data:", error);
            }
        });
    }

    function restoreFilters() {
        $.ajax({
            url: baseUrl + "admin/events_due/reports/get_filters",
            type: "GET",
            dataType: "json",
            success: function (data) {
                if (data) {
                    $('#status').val(data.status || '').trigger('change');
                    $('#start-date').val(data.start_date || '');
                    $('#end-date').val(data.end_date || '');
                    $('#organization').val(data.organization || '').trigger('change');
                    $('#query').val(data.query || '');

                    // Fetch filtered data if any filters are set
                    if (data.status || data.start_date || data.end_date || data.organization || data.query) {
                        fetchFilteredData();
                    }
                }
            }
        });
    }

    // Listen to filter changes
    $('#status, #start-date, #end-date, #organization').on('change', function () {
        fetchFilteredData();
    });

    // Listen to query input with debounce
    let debounceTimer;
    $('#query').on('keyup', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            fetchFilteredData();
        }, 500);
    });

    // Clear filters
    $('.clear-filters').on('click', function () {
        $('#status').val('').trigger('change');
        $('#start-date').val('');
        $('#end-date').val('');
        $('#organization').val('').trigger('change');
        $('#query').val('');

        // Clear session filters
        $.ajax({
            url: baseUrl + "admin/events_due/reports/clear_filters",
            type: "POST",
            success: function () {
                fetchFilteredData();
            }
        });
    });

    // Restore filters on page load
    restoreFilters();
});
