
<script>

	$(function() {

		"use strict";
		initDataTable('.table-warehouse-permission', admin_url + 'warehouse/warehouse_permission_table');
	});

	function warehouse_permissions_update(staff_id, role_id, add_new) {
	"use strict";

	  $("#modal_wrapper").load("<?php echo admin_url('warehouse/warehouse/permission_modal'); ?>", {
	       slug: 'update',
	       staff_id: staff_id,
	       role_id: role_id,
	       add_new: add_new
	  }, function() {
	       if ($('.modal-backdrop.fade').hasClass('in')) {
	            $('.modal-backdrop.fade').remove();
	       }
	       if ($('#appointmentModal').is(':hidden')) {
	            $('#appointmentModal').modal({
	                 show: true
	            });
	       }
	  });

	  init_selectpicker();
	  $(".selectpicker").selectpicker('refresh');
	}

</script>
