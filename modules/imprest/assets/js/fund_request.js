$(document).ready(function () {
    function recalculateTotal() {
        var total = 0;
        $("input.item-amount").each(function () {
            total += parseFloat($(this).val()) || 0;
        });
        $("#totalAmount").text(total.toFixed(2));
    }

    function addImprestItemRow() {
        var newRow = `
            <tr>
                <td>
                    <select class="form-control item-name">
                        <option value="">Select an item</option>
                        <!-- Additional options will be dynamically populated from the server -->
                    </select>
                </td>
                <td>
                    <input type="number" class="form-control item-amount" onchange="recalculateTotal()" />
                </td>
            </tr>`;
        $("#imprestItemsTable tbody").append(newRow);
    }

    // Event binding for adding new item rows
    $(document).on('click', '.add-item', function () {
        addImprestItemRow();
    });

    // Event binding for recalculating total when item amount changes
    $(document).on("change", "input.item-amount", function () {
        recalculateTotal();
    });

    window.submitFundRequest = function () {
        var formData = $("#fundRequestForm").serialize();
        console.log('Form data to submit:', formData);
        // Implement AJAX to submit form data
        $.ajax({
            url: admin_url + 'imprest_management/submit_fund_request',
            type: 'POST',
            data: formData,
            success: function (response) {
                alert('Fund Request Submitted Successfully');
                $('#fundRequestModal').modal('hide');
            },
            error: function (error) {
                alert('Error submitting fund request');
            }
        });
    };
});


// Progress bar for fund request
// Code to handle the progress navigation
jQuery(document).ready(function() {
    var back = jQuery(".prev");
    var next = jQuery(".next");
    var steps = jQuery(".step");

    next.bind("click", function() { 
        jQuery.each(steps, function(i) {
            if (!jQuery(steps[i]).hasClass('current') && !jQuery(steps[i]).hasClass('done')) {
                jQuery(steps[i]).addClass('current');
                jQuery(steps[i - 1]).removeClass('current').addClass('done');
                return false;
            }
        })        
    });

    back.bind("click", function() { 
        jQuery.each(steps, function(i) {
            if (jQuery(steps[i]).hasClass('done') && jQuery(steps[i + 1]).hasClass('current')) {
                jQuery(steps[i + 1]).removeClass('current');
                jQuery(steps[i]).removeClass('done').addClass('current');
                return false;
            }
        })        
    });
})
