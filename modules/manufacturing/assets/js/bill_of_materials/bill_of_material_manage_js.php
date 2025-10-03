
<script>

	"use strict";

	var InvoiceServerParams={
		"products_filter": "[name='products_filter[]']",
		"bom_type_filter": "[name='bom_type_filter[]']",
		"routing_filter": "[name='routing_filter[]']",
	};

	var bill_of_material_table = $('.table-bill_of_material_table');
	initDataTable(bill_of_material_table, admin_url+'manufacturing/bill_of_material_table',[0],[0], InvoiceServerParams, [0,'desc']);

	$.each(InvoiceServerParams, function(i, obj) {
		$('select' + obj).on('change', function() {  
			bill_of_material_table.DataTable().ajax.reload()
			.columns.adjust()
			.responsive.recalc();
		});
	});

	var hidden_columns = [1];
	$('.table-bill_of_material_table').DataTable().columns(hidden_columns).visible(false, false);


	/**
	 * add routing
	 * @param {[type]} staff_id 
	 * @param {[type]} role_id  
	 * @param {[type]} add_new  
	 */
	function add_bill_of_material() {
	"use strict";

	  $("#modal_wrapper").load("<?php echo admin_url('manufacturing/manufacturing/bill_of_material_modal'); ?>", {
	       slug: 'add',
	  }, function() {

	  	$("body").find('#appointmentModal').modal({ show: true, backdrop: 'static' });

	  });

	  init_selectpicker();
	  $(".selectpicker").selectpicker('refresh');
	}

	function staff_bulk_actions(){
		"use strict";
		$('#bill_of_material_table_bulk_actions').modal('show');
	}


	// Leads bulk action
	function bom_delete_bulk_action(event) {
		"use strict";

		if (confirm_delete()) {
			var mass_delete = $('#mass_delete').prop('checked');

			if(mass_delete == true){
				var ids = [];
				var data = {};

				data.mass_delete = true;
				data.rel_type = 'bill_of_material';

				var rows = $('#table-bill_of_material_table').find('tbody tr');
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
						$('#bill_of_material_table_bulk_actions').modal('hide');
						alert_float('danger', data.responseText);
					});
				}, 200);
			}else{
				window.location.reload();
			}

		}
	}


</script>