
<script>

    "use strict";

	var InvoiceServerParams={};

	var unit_of_measure_table = $('.table-unit_of_measure_table');
	initDataTable(unit_of_measure_table, admin_url+'manufacturing/unit_of_measure_table',[0],[0], InvoiceServerParams, [0 ,'desc']);

	$('#date_add').on('change', function() {
	    unit_of_measure_table.DataTable().ajax.reload().columns.adjust().responsive.recalc();
	});


	var hidden_columns = [0];
	$('.table-unit_of_measure_table').DataTable().columns(hidden_columns).visible(false, false);


	function add_edit_unit_measure(unit_id, type) {
		"use strict";

		$("#modal_wrapper").load("<?php echo admin_url('manufacturing/manufacturing/unit_of_measure_modal'); ?>", {
			unit_id: unit_id,
			type: type
		}, function() {

			$("body").find('#appointmentModal').modal({ show: true, backdrop: 'static' });
		});

		init_selectpicker();
		$(".selectpicker").selectpicker('refresh');

	}
</script>
