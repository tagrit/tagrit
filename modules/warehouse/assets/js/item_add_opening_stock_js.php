<script>
	
	var working_hours;

	$(document).ready(function() {
		setTimeout(function(){
			"use strict";  
			$('.work_instruction').click();

		}, 1);
	});

	(function($) {
		"use strict";  

		appValidateForm($("body").find('#add_update_working_hour'), {
			'working_hour_name': 'required',
			'hours_per_day': 'required',
		});    


		<?php if(isset($opening_stock_data)){ ?>
			var dataObject_pu = <?php echo json_encode($opening_stock_data) ; ?>;
		<?php }else{?>
			var dataObject_pu = [];
		<?php } ?>

		setTimeout(function(){

			var hotElement1 = document.getElementById('item_add_opening_stock_hs');

			working_hours = new Handsontable(hotElement1, {
				licenseKey: 'non-commercial-and-evaluation',


				contextMenu: true,
				manualRowMove: true,
				manualColumnMove: true,
				stretchH: 'none',
				autoWrapRow: true,
				rowHeights: 30,
				defaultRowHeight: 100,
				minRows: <?php echo new_html_entity_decode($min_row); ?>,
				width: '100%',
				height: '350px',
				licenseKey: 'non-commercial-and-evaluation',
				rowHeaders: true,
				autoColumncommodity_group: {
					samplingRatio: 23
				},
				

				filters: true,
				manualRowRecommodity_group: true,
				manualColumnRecommodity_group: true,
				allowInsertRow: true,
				allowRemoveRow: true,
				columnHeaderHeight: 40,
				minSpareRows: 1,
				colWidths: [40, 300, 220,150, 160, 170],
				rowHeights: 30,
				
				rowHeaderWidth: [44],
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
					data: 'commodity_id',
					renderer: customDropdownRenderer,
					editor: "chosen",
					chosenOptions: {
						data: <?php echo json_encode($commodity_code_name); ?>
					},
				},
				{
					type: 'text',
					data: 'warehouse_id',
					renderer: customDropdownRenderer,
					editor: "chosen",
					chosenOptions: {
						data: <?php echo json_encode($units_warehouse_name); ?>
					}

				},

				{
					type: 'text',
					data: 'lot_number',
				},

				{
					type: 'date',
					data: 'expiry_date',
					dateFormat: 'YYYY-MM-DD',
					correctFormat: true,
					defaultDate: "<?php echo _d(date('Y-m-d')) ?>"
				},
				{
					data: 'inventory_number',
					type: 'numeric',
					numericFormat: {
						pattern: '0,0.00',
					},
				},

				],

				colHeaders: [

				"<?php echo _l('id'); ?>",
				"<?php echo _l('commodity_name'); ?>",
				"<?php echo _l('warehouse_name'); ?>",
				"<?php echo _l('lot_number'); ?>",
				"<?php echo _l('expiry_date'); ?>",
				"<?php echo _l('inventory_number'); ?>",

				],

				data: dataObject_pu,
			});
		},300);

	})(jQuery);


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

	$('.btn_add_opening_stock').on('click', function() {
		'use strict';

		var valid_add_opening_stock = $('#item_add_opening_stock_hs').find('.htInvalid').html();

		if(valid_add_opening_stock){
			alert_float('danger', "<?php echo _l('data_must_number') ; ?>");
		}else{
			$('.btn_add_opening_stock').attr( "disabled", "disabled" );
			$('input[name="item_add_opening_stock_hs"]').val(JSON.stringify(working_hours.getData()));   
			$('#add_opening_stock').submit(); 

		}
	});

</script>