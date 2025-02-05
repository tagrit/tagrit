<script>

function confirm_order(el){
	"use strict";  

	var status = 'confirm';
	var order_id = $(el).data('order_id');

	change_order_return_status(status, order_id);
}

function reject_order(el){
	"use strict";  
	
	var status = 'canceled';
	var order_id = $(el).data('order_id');

	change_order_return_status(status, order_id);
}

function  change_order_return_status(status, order) {
	"use strict";
	$.post(site_url + 'purchase/vendors_portal/update_order_return_status/'+ order+'/'+status).done(function (response) { 
		response = JSON.parse(response);

		if(status == 'confirm'){
			alert_float('success', "<?php echo _l('confirm_order_successfully'); ?>");
		}else if(status == 'canceled'){
			alert_float('success',"<?php echo _l('reject_order_successfully'); ?>");
		}

		window.location.reload();
	});
}
</script>