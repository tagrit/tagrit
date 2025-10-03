<script>
	function delete_wh_activitylog(wrapper, id) {
		"use strict"; 

		if (confirm_delete()) {
			requestGetJSON('warehouse/delete_activitylog/' + id).done(function(response) {
				if (response.success === true || response.success == 'true') {
					$(wrapper).parents('.feed-item').remove();
					alert_float('success', '<?php echo  _l('wh_shipment_log_deleted') ?>');
				}
			}).fail(function(data) {
				alert_float('danger', data.responseText);
			});
		}
	}

	function wh_activity_log_modal(slug, id, shipment_id, cart_id) {
		"use strict";
		var data={};
		data.slug = slug;
		data.shipment_id = shipment_id;
		data.id = id;
		data.cart_id = cart_id;

		$.get(site_url+'warehouse/shipment_activity_log_modal',data , function (response) {

			$("#modal_wrapper").html(response.data);
			$("body").find('#add_activity_log').modal({ show: true, backdrop: 'static' });
			init_datepicker();
			
		}, 'json');
	}
</script>