<script>
	
	var working_hours;
	var global_time_off;


	 (function($) {
	 	"use strict";  

	appValidateForm($("body").find('#add_update_working_hour'), {
		'working_hour_name': 'required',
		'hours_per_day': 'required',
	});    


	<?php if(isset($working_hour_details)){ ?>
		var dataObject_pu = <?php echo json_encode($working_hour_details) ; ?>;
	<?php }else{?>
		var dataObject_pu = <?php echo json_encode($working_hour_sample_data); ?>;
	<?php } ?>

	var hotElement1 = document.getElementById('working_hour_hs');

	working_hours = new Handsontable(hotElement1, {
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
			columns: [0,1],
			indicators: true
		},

		columns: [
		{
			type: 'text',
			data: 'id',
		},
		{
			type: 'text',
			data: 'working_hour_id',
		},
		{
			type: 'text',
			data: 'working_hour_name',
		},
		{
			type: 'text',
			data: 'day_of_week',
			renderer: customDropdownRenderer,
			editor: "chosen",
			chosenOptions: {
				data: <?php echo json_encode($day_of_week_types); ?>
			},
		},
		{
			type: 'text',
			data: 'day_period',
			renderer: customDropdownRenderer,
			editor: "chosen",
			chosenOptions: {
				data: <?php echo json_encode($day_period_type); ?>
			}

		},
		{
			data: 'work_from',
			type: 'time',
			timeFormat: 'H:mm',
			correctFormat: true
		},
		{
			data: 'work_to',
			type: 'time',
			timeFormat: 'H:mm',
			correctFormat: true
		},
		

		{
			type: 'date',
			data: 'starting_date',
			dateFormat: 'YYYY-MM-DD',
			correctFormat: true,
			defaultDate: "<?php echo _d(date('Y-m-d')) ?>"
		},
		{
			type: 'date',
			data: 'end_date',
			dateFormat: 'YYYY-MM-DD',
			correctFormat: true,
			defaultDate: "<?php echo _d(date('Y-m-d')) ?>"
		},

		],

		colHeaders: [

		'<?php echo _l('id'); ?>',
		'<?php echo _l('working_hour_id'); ?>',
		'<?php echo _l('working_hour_name'); ?>',
		'<?php echo _l('day_of_week'); ?>',
		'<?php echo _l('day_period'); ?>',
		'<?php echo _l('work_from'); ?>',
		'<?php echo _l('work_to'); ?>',
		'<?php echo _l('starting_date'); ?>',
		'<?php echo _l('end_date'); ?>',

		],

		data: dataObject_pu,
	});


	//global time off
	<?php if(isset($time_off)){ ?>
		var global_time_data = <?php echo json_encode($time_off) ; ?>;
	<?php }else{?>
		var global_time_data = [];
	<?php } ?>

	var hotElement2 = document.getElementById('global_time_off_hs');

	global_time_off = new Handsontable(hotElement2, {
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
			columns: [0,1],
			indicators: true
		},

		columns: [
		{
			type: 'text',
			data: 'id',
		},
		{
			type: 'text',
			data: 'working_hour_id',
		},
		{
			type: 'text',
			data: 'reason',
		},

		{
			type: 'date',
			data: 'starting_date',
			dateFormat: 'YYYY-MM-DD',
			correctFormat: true,
			defaultDate: "<?php echo _d(date('Y-m-d')) ?>"
		},
		{
			type: 'date',
			data: 'end_date',
			dateFormat: 'YYYY-MM-DD',
			correctFormat: true,
			defaultDate: "<?php echo _d(date('Y-m-d')) ?>"
		},

		],

		colHeaders: [

		'<?php echo _l('id'); ?>',
		'<?php echo _l('working_hour_id'); ?>',
		'<?php echo _l('working_time_reason'); ?>',
		'<?php echo _l('starting_date'); ?>',
		'<?php echo _l('end_date'); ?>',

		],

		data: global_time_data,
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

	$('.add_working_hours').on('click', function() {
		'use strict';

		var valid_working_hour = $('#working_hour_hs').find('.htInvalid').html();
		var valid_global_time_off = $('#global_time_off_hs').find('.htInvalid').html();

		if(valid_working_hour || valid_global_time_off){
			alert_float('danger', "<?php echo _l('data_must_number') ; ?>");
		}else{

			$('input[name="working_hour_hs"]').val(JSON.stringify(working_hours.getData()));   
			$('input[name="global_time_off_hs"]').val(JSON.stringify(global_time_off.getData()));   
			$('#add_update_working_hour').submit(); 

		}
	});
</script>