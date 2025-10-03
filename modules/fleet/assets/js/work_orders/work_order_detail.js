
function work_order_status_mark_as(status, work_order_id) {
	"use strict"; 
	
	var url = 'fleet/work_order_status_mark_as/' + status + '/' + work_order_id;
	$("body").append('<div class="dt-loader"></div>');

	requestGetJSON(url).done(function (response) {
		$("body").find('.dt-loader').remove();
		if (response.success === true || response.success == 'true') {
			alert_float('success','Status changed');
    		setTimeout(function(){location.reload();},1500);
		}
	});
}


function create_expense(id) {
    "use strict";
    if (confirm("Are you sure?")) {
	    $.post(admin_url + 'fleet/create_expense_by_work_order/' + id).done(function(response) {
	        response = JSON.parse(response);
	        if (response.message != '') {
	            alert_float('success', response.message);
	            $('#expense-number').text(response.expense_number);
	        } else {
	            alert_float('danger');
	        }
	        $('#btn-create-expense').addClass('hide');
	    });
	}
}