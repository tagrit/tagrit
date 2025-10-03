<script>


	appValidateForm($("body").find('#add_update_manufacturing_order'), {
		'product_id': 'required',
		'product_qty': 'required',
		'unit_id': 'required',
		'bom_id': 'required',
		'manufacturing_order_code': 'required',
		'finished_products_warehouse_id': 'required',
		'date_plan_from': 'required',
		'date_deadline': 'required',
	});  

	var product_tabs;

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


//get related data for manufacturing order
$('select[name="product_id"]').on('change', function() {
	"use strict";

	var product_id =$(this).val();

	$.get(admin_url + 'manufacturing/get_data_create_manufacturing_order/' + product_id, function (response) {
		$("select[name='bom_id']").html('');
		$("select[name='bom_id']").append(response.bill_of_material_option);

		$("input[name='routing_id_view']").val(response.routing_name);
		$("input[name='routing_id']").val(response.routing_id);
		$("select[name='unit_id']").val(response.unit_id).selectpicker('refresh');

		product_tabs.updateSettings({
			data: response.component_arr,
			maxRows: response.component_row,
		});
		

		init_selectpicker();
		$(".selectpicker").selectpicker('refresh');


	}, 'json');

});

$('select[name="bom_id"]').on('change', function() {
	"use strict";

	var bill_of_material_id =$(this).val();
	var product_id = $('select[name="product_id"]').val();
	var product_qty = $('input[name="product_qty"]').val();

	$.get(admin_url + 'manufacturing/get_bill_of_material_detail/' + bill_of_material_id+'/'+product_id+'/'+product_qty, function (response) {

		product_tabs.updateSettings({
			data: response.component_arr,
			maxRows: response.component_row,
		});

		$("input[name='routing_id_view']").val(response.routing_name);
		$("input[name='routing_id']").val(response.routing_id);


		init_selectpicker();
		$(".selectpicker").selectpicker('refresh');

	}, 'json');
});

$('input[name="product_qty"]').on('change', function() {
	"use strict";

	var product_qty =$(this).val();
	var product_id = $('select[name="product_id"]').val();
	var bill_of_material_id = $('select[name="bom_id"]').val();

	$.get(admin_url + 'manufacturing/get_bill_of_material_detail/' + bill_of_material_id+'/'+product_id+'/'+product_qty, function (response) {

		product_tabs.updateSettings({
			data: response.component_arr,
			maxRows: response.component_row,
		});

		$("input[name='routing_id_view']").val(response.routing_name);
		$("input[name='routing_id']").val(response.routing_id);


		init_selectpicker();
		$(".selectpicker").selectpicker('refresh');

	}, 'json');
});



$('.add_manufacturing_order').on('click', function() {
	'use strict';

	var valid_working_hour = $('#working_hour_hs').find('.htInvalid').html();

		$('input[name="product_tab_hs"]').val(JSON.stringify(product_tabs.getData()));   
		$('#add_update_manufacturing_order').submit(); 

});


</script>