
<script>

	"use strict";

	var InvoiceServerParams={};
	var work_center_table = $('.table-work_center_table');
	initDataTable(work_center_table, admin_url+'manufacturing/work_center_table',[0],[0], InvoiceServerParams, [0 ,'desc']);

	$('#date_add').on('change', function() {
		work_center_table.DataTable().ajax.reload().columns.adjust().responsive.recalc();
	});


	var hidden_columns = [0];
	$('.table-work_center_table').DataTable().columns(hidden_columns).visible(false, false);

</script>