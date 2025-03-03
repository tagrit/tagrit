<script>
	var product_tabs;
	var data_color = <?php echo json_encode($data_color); ?>;

	(function($) {
		"use strict";  


		<?php if(isset($product_tab_details)){ ?>
			var dataObject_pu = <?php echo json_encode($product_tab_details) ; ?>;
		<?php }else{?>
			var dataObject_pu = [];
		<?php } ?>

		var hotElement1 = document.getElementById('product_tab_hs');

		product_tabs = new Handsontable(hotElement1, {
			licenseKey: 'non-commercial-and-evaluation',

			contextMenu: true,
			manualRowMove: true,
			manualColumnMove: true,
			stretchH: 'all',
			autoWrapRow: true,
			rowHeights: 30,
			defaultRowHeight: 100,
			minRows: 10,
			maxRows: 40,
			width: '100%',

			rowHeaders: true,
			cells: function(row, col, prop) {
				var cellProperties = {};
				if (col > 2) {
					cellProperties.renderer = firstRowRenderer; 
				}
				return cellProperties;
			},
			colHeaders: true,
			autoColumnSize: {
				samplingRatio: 23
			},

			filters: true,
			manualRowResize: true,
			manualColumnResize: true,
			allowInsertRow: true,
			allowRemoveRow: true,
			columnHeaderHeight: 40,

			rowHeights: 30,
			rowHeaderWidth: [44],
			minSpareRows: 1,
			hiddenColumns: {
				columns: [0],
				indicators: true
			},

			columns: [
			{
				type: 'text',
				data: 'id',
			},
			{
				type: 'text',
				data: 'product_id',
				renderer: customDropdownRenderer,
				editor: "chosen",
				chosenOptions: {
					data: <?php echo json_encode($product_for_hansometable); ?>
				},
			},
			{
				type: 'text',
				data: 'unit_id',
				renderer: customDropdownRenderer,
				editor: "chosen",
				chosenOptions: {
					data: <?php echo json_encode($unit_for_hansometable); ?>
				},
			},
			
			{
				data: 'qty_to_consume',
				type: 'numeric',
				numericFormat: {
					pattern: '0,0.00',
				},
			},
			{
				data: 'qty_reserved',
				type: 'numeric',
				numericFormat: {
					pattern: '0,0.00',
				},
			},

			{
				data: 'qty_done',
				type: 'numeric',
				numericFormat: {
					pattern: '0,0.00',
				},
			},

			
			],

			colHeaders: [

			'<?php echo _l('id'); ?>',
			'<?php echo _l('product_label'); ?>',
			'<?php echo _l('unit_id'); ?>',
			'<?php echo _l('qty_to_consume'); ?>',
			'<?php echo _l('qty_reserved'); ?>',
			'<?php echo _l('qty_done'); ?>',

			],

			data: dataObject_pu,
		});


	})(jQuery);

	function firstRowRenderer(instance, td, row, col, prop, value, cellProperties) {
		
		"use strict";
		Handsontable.renderers.TextRenderer.apply(this, arguments);
		td.style.background = '#fff';
		if(data_color[row] != undefined){
			td.style.color = data_color[row];
			td.className = 'htRight';

		}
	}

	function customDropdownRenderer(instance, td, row, col, prop, value, cellProperties) {
		"use strict";
		var selectedId;
		var optionsList = cellProperties.chosenOptions.data;

		if(typeof optionsList === "undefined" || typeof optionsList.length === "undefined" || !optionsList.length) {
			Handsontable.cellTypes.text.renderer(instance, td, row, col, prop, value, cellProperties);
			return td;
		}

		var values = (value + "").split("|");
		value = [];
		for (var index = 0; index < optionsList.length; index++) {

			if (values.indexOf(optionsList[index].id + "") > -1) {
				selectedId = optionsList[index].id;
				value.push(optionsList[index].label);
			}
		}
		value = value.join(", ");

		Handsontable.cellTypes.text.renderer(instance, td, row, col, prop, value, cellProperties);
		return td;
	}

	
	$('.mark_as_todo').on('click', function() {
		"use strict";

		var id = $("input[name='id']").val();
		$.get(admin_url + 'manufacturing/mo_mark_as_todo/' + id+'/mark_as_todo', function (response) {
			if(response.status == 'warning'){
				alert_float(response.status, response.message, 5000);
				setTimeout(function(){ location.reload(); }, 5000);
			}else{
				alert_float(response.status, response.message);
				location.reload();
			}
		}, 'json');

	});

	$('.mark_check_availability').on('click', function() {
		"use strict";

		var id = $("input[name='id']").val();
		$.get(admin_url + 'manufacturing/mo_mark_as_todo/' + id+'/check_availability', function (response) {
			if(response.status == 'warning'){
				alert_float(response.status, response.message, 5000);
				setTimeout(function(){ location.reload(); }, 5000);
			}else{
				alert_float(response.status, response.message);
				location.reload();
			}
		}, 'json');

	});

	$('.mark_as_done').on('click', function() {

		"use strict";

		$('#show_detail').modal('show');

	});


	$('.btn_mark_as_done').on('click', function() {
		"use strict";


		var id = $("input[name='id']").val();
		var quantity = $("input[name='change_product_qty']").val();

		if(quantity != undefined && quantity != ''){

			$('.mark_as_done').attr( "disabled", "disabled" );
			$('.btn_mark_as_done').attr( "disabled", "disabled" );
			$.get(admin_url + 'manufacturing/mo_mark_as_done/' + id + '/' + quantity, function (response) {
				alert_float(response.status, response.message);

				location.reload();
			}, 'json');
		}else{
			alert_float('warning', '<?php echo _l('please_enter_quantity_produced'); ?>');
		}

	});

	$('.mark_as_planned').on('click', function() {
		"use strict";
		var id = $("input[name='id']").val();
		$.get(admin_url + 'manufacturing/mo_mark_as_planned/' + id, function (response) {
			alert_float(response.status, response.message);

			location.reload();
		}, 'json');

	});

	$('.mark_as_unreserved').on('click', function() {
		"use strict";

		$('.mark_as_unreserved').attr( "disabled", "disabled" );

		var id = $("input[name='id']").val();
		$.get(admin_url + 'manufacturing/mo_mark_as_unreserved/' + id, function (response) {
			alert_float(response.status, response.message);

			location.reload();
		}, 'json');

	});
	
	$('.mark_as_cancel').on('click', function() {
		"use strict";

		$('.mark_as_cancel').attr( "disabled", "disabled" );

		var id = $("input[name='id']").val();
		$.get(admin_url + 'manufacturing/mo_mark_as_cancel/' + id, function (response) {
			alert_float(response.status, response.message);

			location.reload();
		}, 'json');

	});
	
	$('.mo_create_purchase_request').on('click', function() {
		"use strict";

		$('.mo_create_purchase_request').attr( "disabled", "disabled" );

		var id = $("input[name='id']").val();
		$.get(admin_url + 'manufacturing/mo_create_purchase_request/' + id, function (response) {
			alert_float(response.status, response.message);

			location.reload();
		}, 'json');

	});

	var BomChangeLogParams={
		"manufacturing_order_id": "[name='manufacturing_order_id']",
	};
	var bom_change_log_table = $('.table-bom_change_log_table');
	initDataTable(bom_change_log_table, admin_url+'manufacturing/bom_change_log_table',[0],[0], BomChangeLogParams, [0 ,'desc']);
	//hide first column
	var hidden_columns = [0];
	$('.table-bom_change_log_table').DataTable().columns(hidden_columns).visible(false, false);
	

</script>