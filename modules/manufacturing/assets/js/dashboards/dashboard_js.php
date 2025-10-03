<script>
	'use strict';

	report_by_manufacturing_order('report_by_manufacturing_order', '', '');
	report_by_work_order('report_by_work_order', '', '');

	function report_by_manufacturing_order(id, value, title_c){
		'use strict';

		var mo_measures = $('select[name="mo_measures"]').val(); 
		var months_report = $('select[name="mo_months-report"]').val(); 
		var report_from = $('input[name="mo_report-from"]').val();
		var report_to = $('input[name="mo_report-to"]').val();

		requestGetJSON('manufacturing/report_by_manufacturing_order?mo_measures='+mo_measures+'&months_report='+months_report+'&report_from='+report_from+'&report_to='+report_to).done(function (response) {

       //get data for hightchart
       
       Highcharts.setOptions({
       	chart: {
       		style: {
       			fontFamily: 'inherit !important',
       			fill: 'black'
       		}
       	},
       	colors: [ '#119EFA','#15f34f','#ef370dc7','#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263','#6AF9C4','#50B432','#0d91efc7','#ED561B']
       });
       Highcharts.chart(id, {
       	chart: {
       		type: 'column'
       	},
       	title: {
       		text: '<?php echo _l('manufacturing_order'); ?>'
       	},
       	credits: {
       		enabled: false
       	},
       	xAxis: {
       		categories: response.categories,
       		crosshair: true
       	},
       	yAxis: {
       		min: 0,
       		title: {
       			text: '<?php echo _l('product_qty'); ?>'
       		}
       	},
       	tooltip: {
       		headerFormat: '<span class="font-size-10">{point.key}</span><table>',
       		pointFormat: '<tr><td class="padding-0" style="color:{series.color}">{series.name}: </td>' +
       		'<td class="padding-0"><b>{point.y:.1f}</b> <?php echo _l('product_qty'); ?></td></tr>',
       		footerFormat: '</table>',
       		shared: true,
       		useHTML: true
       	},
       	plotOptions: {
       		column: {
       			pointPadding: 0.2,
       			borderWidth: 0
       		}
       	},
       	series: [{
       		name: '<?php echo _l('draft'); ?>',
       		data: response.draft 

       	}, {
       		name: '<?php echo _l('planned'); ?>',
       		data: response.planned

       	},{
       		name: '<?php echo _l('cancelled'); ?>',
       		data: response.cancelled

       	},{
       		name: '<?php echo _l('confirmed'); ?>',
       		data: response.confirmed

       	},{
       		name: '<?php echo _l('done'); ?>',
       		data: response.done

       	}, {
       		name: '<?php echo _l('in_progress'); ?>',
       		data: response.in_progress

       	}]
       });
       

   });
	}


	function report_by_work_order(id, value, title_c){
		'use strict';

		var wo_measures = $('select[name="wo_measures"]').val(); 
		var months_report = $('select[name="wo_months-report"]').val(); 
		var report_from = $('input[name="wo_report-from"]').val();
		var report_to = $('input[name="wo_report-to"]').val();

		requestGetJSON('manufacturing/report_by_work_order?wo_measures='+wo_measures+'&months_report='+months_report+'&report_from='+report_from+'&report_to='+report_to).done(function (response) {

       //get data for hightchart
       
       Highcharts.setOptions({
       	chart: {
       		style: {
       			fontFamily: 'inherit !important',
       			fill: 'black'
       		}
       	},
       	colors: [ '#119EFA','#15f34f','#ef370dc7','#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263','#6AF9C4','#50B432','#0d91efc7','#ED561B']
       });
       Highcharts.chart(id, {
       	chart: {
       		type: 'column'
       	},
       	title: {
       		text: '<?php echo _l('mrp_work_orders'); ?>'
       	},
       	credits: {
       		enabled: false
       	},
       	xAxis: {
       		categories: response.categories,
       		crosshair: true
       	},
       	yAxis: {
       		min: 0,
       		title: {
       			text: ''
       		}
       	},
       	tooltip: {
       		headerFormat: '<span class="font-size-10">{point.key}</span><table>',
       		pointFormat: '<tr><td class="padding-0" style="color:{series.color}">{series.name}: </td>' +
       		'<td class="padding-0"><b>{point.y:.1f}</b></td></tr>',
       		footerFormat: '</table>',
       		shared: true,
       		useHTML: true
       	},
       	plotOptions: {
       		column: {
       			pointPadding: 0.2,
       			borderWidth: 0
       		}
       	},
       	series: [{
       		name: '<?php echo _l('manufacturing_order'); ?>',
       		data: response.mo_data 

       	}]
       });
       

   });
	}

	//manufacturing order
	var mo_report_from = $('input[name="mo_report-from"]');
	var mo_report_to = $('input[name="mo_report-to"]');
	var mo_date_range = $('#mo_date-range');

	$('select[name="mo_months-report"]').on('change', function() {
		'use strict';

		var val = $(this).val();
		mo_report_to.attr('disabled', true);
		mo_report_to.val('');
		mo_report_from.val('');
		if (val == 'custom') {
			mo_date_range.addClass('fadeIn').removeClass('hide');
			return;
		} else {
			if (!mo_date_range.hasClass('hide')) {
				mo_date_range.removeClass('fadeIn').addClass('hide');
			}
		}
		mo_gen_reports();
	});

	mo_report_from.on('change', function() {
		'use strict';

		var val = $(this).val();
		var report_to_val = mo_report_to.val();
		if (val != '') {
			mo_report_to.attr('disabled', false);
			if (report_to_val != '') {
				mo_gen_reports();
			}
		} else {
			mo_report_to.attr('disabled', true);
		}
	});

	mo_report_to.on('change', function() {
		'use strict';

		var val = $(this).val();
		if (val != '') {
			mo_gen_reports();
		}
	});

	$('select[name="mo_measures"]').on('change', function() {
		'use strict';

		var val = $(this).val();
		if (val != '') {
			mo_gen_reports();
		}
	});

	//work order
	var wo_report_from = $('input[name="wo_report-from"]');
	var wo_report_to = $('input[name="wo_report-to"]');
	var wo_date_range = $('#wo_date-range');

	$('select[name="wo_months-report"]').on('change', function() {
		'use strict';

		var val = $(this).val();
		wo_report_to.attr('disabled', true);
		wo_report_to.val('');
		wo_report_from.val('');
		if (val == 'custom') {
			wo_date_range.addClass('fadeIn').removeClass('hide');
			return;
		} else {
			if (!wo_date_range.hasClass('hide')) {
				wo_date_range.removeClass('fadeIn').addClass('hide');
			}
		}
		wo_gen_reports();
	});

	wo_report_from.on('change', function() {
		'use strict';

		var val = $(this).val();
		var report_to_val = wo_report_to.val();
		if (val != '') {
			wo_report_to.attr('disabled', false);
			if (report_to_val != '') {
				wo_gen_reports();
			}
		} else {
			wo_report_to.attr('disabled', true);
		}
	});

	wo_report_to.on('change', function() {
		'use strict';

		var val = $(this).val();
		if (val != '') {
			wo_gen_reports();
		}
	});

	$('select[name="wo_measures"]').on('change', function() {
		'use strict';

		var val = $(this).val();
		if (val != '') {
			wo_gen_reports();
		}
	});



	function mo_gen_reports() {
		'use strict';
		report_by_manufacturing_order('report_by_manufacturing_order', '', '');
	}

	function wo_gen_reports() {
		'use strict';
		report_by_work_order('report_by_work_order', '', '');

	}

</script>