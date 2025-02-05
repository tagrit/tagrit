<script>
	var hidden_columns = [2,6];
	var sub_group_value ='';

	(function($) {
		"use strict";

		var ProposalServerParams = {
			"warehouse_ft": "[name='warehouse_filter[]']",
			"commodity_ft": "[name='commodity_filter[]']",
			"show_serial_number_status_filter": "[name='show_serial_number_status_filter[]']",
		};

		var table_serial_number = $('table.table-table_serial_number');
		var _table_api = initDataTable(table_serial_number, admin_url+'warehouse/table_serial_number', [0], [0], ProposalServerParams,  [1, 'desc']);
		$.each(ProposalServerParams, function(i, obj) {
			$('select' + obj).on('change', function() {  
				table_serial_number.DataTable().ajax.reload();
			});
		});

		init_selectpicker();
	})(jQuery); 

	function show_serial_number_detail_modal(inventory_serial_numbers) {
		"use strict";

		$("#modal_wrapper").load("<?php echo admin_url('warehouse/warehouse/show_serial_number_detail_modal'); ?>", {
			slug: 'add',
			inventory_serial_numbers:inventory_serial_numbers,
		}, function() {

			$("body").find('#appointmentModal').modal({ show: true, backdrop: 'static' });

		});

		init_selectpicker();
		$(".selectpicker").selectpicker('refresh');
	}


	// Maybe items ajax search
	init_ajax_search('items','#commodity_filter.ajax-search',undefined,admin_url+'warehouse/wh_commodity_code_search_all');
</script>