
<script>

	"use strict";

	var InvoiceServerParams={
		"products_filter": "[name='products_filter[]']",
		"routing_filter": "[name='routing_filter[]']",
		"status_filter": "[name='status_filter[]']",
	};
	var manufacturing_order_table = $('.table-manufacturing_order_table');
	initDataTable(manufacturing_order_table, admin_url+'manufacturing/manufacturing_order_table',[0],[0], InvoiceServerParams, [0 ,'desc']);

	$.each(InvoiceServerParams, function(i, obj) {
		$('select' + obj).on('change', function() {  
			manufacturing_order_table.DataTable().ajax.reload()
			.columns.adjust()
			.responsive.recalc();
		});
	});

	var hidden_columns = [1];
	$('.table-manufacturing_order_table').DataTable().columns(hidden_columns).visible(false, false);

	function staff_bulk_actions(){
		"use strict";
		$('#manufacturing_order_table_bulk_actions').modal('show');
	}


	// Leads bulk action
	function mo_delete_bulk_action(event) {
		"use strict";

		if (confirm_delete()) {
			var mass_delete = $('#mass_delete').prop('checked');

			if(mass_delete == true){
				var ids = [];
				var data = {};

				data.mass_delete = true;
				data.rel_type = 'manufacturing_order';

				var rows = $('#table-manufacturing_order_table').find('tbody tr');
				$.each(rows, function() {
					var checkbox = $($(this).find('td').eq(0)).find('input');
					if (checkbox.prop('checked') === true) {
						ids.push(checkbox.val());
					}
				});

				data.ids = ids;
				$(event).addClass('disabled');
				setTimeout(function() {
					$.post(admin_url + 'manufacturing/mrp_product_delete_bulk_action', data).done(function() {
						window.location.reload();
					}).fail(function(data) {
						$('#manufacturing_order_table_bulk_actions').modal('hide');
						alert_float('danger', data.responseText);
					});
				}, 200);
			}else{
				window.location.reload();
			}

		}
	}

</script>