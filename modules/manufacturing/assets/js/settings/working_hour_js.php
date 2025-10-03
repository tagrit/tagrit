
<script>

    "use strict";

	var InvoiceServerParams={};

	var working_hour_table = $('.table-working_hour_table');
	initDataTable(working_hour_table, admin_url+'manufacturing/working_hour_table',[0],[0], InvoiceServerParams, [0 ,'desc']);

	$('#date_add').on('change', function() {
	    working_hour_table.DataTable().ajax.reload().columns.adjust().responsive.recalc();
	});


	var hidden_columns = [0];
	$('.table-working_hour_table').DataTable().columns(hidden_columns).visible(false, false);

	
</script>
