
<script>

	"use strict";

	var InvoiceServerParams={
		"routing_id": "[name='routing_id']",
	};
	var operation_table = $('.table-operation_table');
	initDataTable(operation_table, admin_url+'manufacturing/operation_table',[0],[0], InvoiceServerParams, [1 ,'asc']);

	$('#date_add').on('change', function() {
		operation_table.DataTable().ajax.reload().columns.adjust().responsive.recalc();
	});

	var hidden_columns = [0];
	$('.table-operation_table').DataTable().columns(hidden_columns).visible(false, false);



	function add_operation(routing_id, operation_id, type) {
	"use strict";

	  $("#modal_wrapper").load("<?php echo admin_url('manufacturing/manufacturing/operation_modal'); ?>", {
	       routing_id: routing_id,
	       operation_id: operation_id,
	       type: type
	  }, function() {

	       $("body").find('#appointmentModal').modal({ show: true, backdrop: 'static' });
	  });

	  init_selectpicker();
	  $(".selectpicker").selectpicker('refresh');

	}


</script>