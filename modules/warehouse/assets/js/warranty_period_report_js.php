<script>
	(function($) {
		"use strict";

		var ProposalServerParams = {
			"commodity_filter": "select[name='commodity_filter[]']",
			"customer_name_filter": "select[name='customer_name_filter[]']",
			"to_date_filter": "input[name='to_date_filter']",
			"status_filter": "select[name='status_filter[]']",
		};

		var table_warranty_period = $('table.table-table_warranty_period');
		var _table_api = initDataTable(table_warranty_period, admin_url+'warehouse/table_warranty_period', [0], [0], ProposalServerParams, [8, 'asc']);

		$('input[name="to_date_filter"], select[name="commodity_filter[]"], select[name="customer_name_filter[]"], select[name="status_filter[]"]').on('change', function() {
			table_warranty_period.DataTable().ajax.reload();
		});

	// Maybe items ajax search
	init_ajax_search('items','#commodity_filter.ajax-search',undefined,admin_url+'warehouse/wh_commodity_code_search_all');


})(jQuery);

</script>