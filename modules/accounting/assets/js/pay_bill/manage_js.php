<script type="text/javascript">
	"use strict";

	var InvoiceServerParams = {
		"from_date": '[name="from_date"]',
		"to_date": '[name="to_date"]',
		"vendor_id": "select[name='vendor_id[]']",
	};
	var table_manage_packing_list = $('.table-table_paybill');
	initDataTable(table_manage_packing_list, admin_url+'accounting/table_paybill',[],[], InvoiceServerParams, [0 ,'desc']);

	$('.table-table_paybill').DataTable().columns([0]).visible(false, false);

	$('input[name="to_date"]').on('change', function() {
      table_manage_packing_list.DataTable().ajax.reload();
    });
	$('input[name="from_date"]').on('change', function() {
		table_manage_packing_list.DataTable().ajax.reload();
	});

    $(' select[name="type"], select[name="vendor_id[]"]').on('change', function() {
        table_manage_packing_list.DataTable().ajax.reload();
    });
    
</script>