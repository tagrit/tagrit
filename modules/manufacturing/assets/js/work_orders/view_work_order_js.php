<script>
	$(document).ready(function() {
		setTimeout(function(){
		"use strict";  
			
			
		$('.work_instruction').click();

		  }, 1);
	});

	var time_trackings;
		(function($) {
		"use strict";  


		<?php if(isset($time_tracking_details)){ ?>
			var dataObject_pu = <?php echo json_encode($time_tracking_details) ; ?>;
		<?php }else{?>
			var dataObject_pu = [];
		<?php } ?>

		var hotElement1 = document.getElementById('time_tracking_hs');

		time_trackings = new Handsontable(hotElement1, {
			licenseKey: 'non-commercial-and-evaluation',

			contextMenu: true,
			manualRowMove: true,
			manualColumnMove: true,
			stretchH: 'all',
			autoWrapRow: true,
			rowHeights: 30,
			defaultRowHeight: 100,
			minRows: 10,
			maxRows: <?php echo new_html_entity_decode($rows); ?>,
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
			// colWidths:  [20, 20, 20,20],
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
				data: 'from_date',
				type: 'text',
				
			},
			{
				data: 'to_date',
				type: 'text',
			},
			{
				data: 'duration',
				type: 'numeric',
				numericFormat: {
					pattern: '0,0.00',
				},
			},
			{
				data: 'full_name',
				type: 'text',
			},

			],

			colHeaders: [

			'<?php echo _l('id'); ?>',
			'<?php echo _l('start_date'); ?>',
			'<?php echo _l('end_date'); ?>',
			'<?php echo _l('duration'); ?>',
			'<?php echo _l('staff_name'); ?>',
			],

			data: dataObject_pu,
		});


	})(jQuery);



	$('.mark_start_working').on('click', function() {
		"use strict";

		var work_order_id = $("input[name='work_order_id']").val();
		var manufacturing_order = $("input[name='manufacturing_order']").val();

		$.get(admin_url + 'manufacturing/mo_mark_as_start_working/' + work_order_id+'/'+manufacturing_order, function (response) {
			alert_float(response.status, response.message);

			location.reload();
		}, 'json');

	});

	$('.mark_pause').on('click', function() {
		"use strict";

		var work_order_id = $("input[name='work_order_id']").val();

		$.get(admin_url + 'manufacturing/mo_mark_as_mark_pause/' + work_order_id, function (response) {
			alert_float(response.status, response.message);

			location.reload();
		}, 'json');

	});

	$('.mark_done').on('click', function() {
		"use strict";

		var work_order_id = $("input[name='work_order_id']").val();
		var manufacturing_order_id = $("input[name='manufacturing_order']").val();

		$.get(admin_url + 'manufacturing/mo_mark_as_mark_done/' + work_order_id+'/'+ manufacturing_order_id, function (response) {
			alert_float(response.status, response.message);

			location.reload();
		}, 'json');

	});
	

</script>