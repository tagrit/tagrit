
<script>

	"use strict";

	var InvoiceServerParams={};
	var routing_table = $('.table-routing_table');
	initDataTable(routing_table, admin_url+'manufacturing/routing_table',[0],[0], InvoiceServerParams, [0 ,'desc']);

	$('#date_add').on('change', function() {
		routing_table.DataTable().ajax.reload().columns.adjust().responsive.recalc();
	});

	var hidden_columns = [0];
	$('.table-routing_table').DataTable().columns(hidden_columns).visible(false, false);


	/**
	 * add routing
	 * @param {[type]} staff_id 
	 * @param {[type]} role_id  
	 * @param {[type]} add_new  
	 */
	function add_routing(staff_id, role_id, add_new) {
	"use strict";

	  $("#modal_wrapper").load("<?php echo admin_url('manufacturing/manufacturing/routing_modal'); ?>", {
	       slug: 'add',
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