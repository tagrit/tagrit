
<script>

$(function () {
	"use strict";

	var InvoiceServerParams={
		"manufacturing_order_id": "[name='manufacturing_order_id']",
	};
	var mo_work_order_table = $('.table-mo_work_order_table');
	initDataTable(mo_work_order_table, admin_url+'manufacturing/mo_work_order_table',[0],[0], InvoiceServerParams, [0 ,'desc']);

	$('#date_add').on('change', function() {
		mo_work_order_table.DataTable().ajax.reload().columns.adjust().responsive.recalc();
	});

	var hidden_columns = [0];
	$('.table-mo_work_order_table').DataTable().columns(hidden_columns).visible(false, false);

	$.each(InvoiceServerParams, function(i, obj) {
		$('select' + obj).on('change', function() {  
			mo_work_order_table.DataTable().ajax.reload()
			.columns.adjust()
			.responsive.recalc();
		});
	});

	$('.toggle-articles-list').on('click', function () {
		var list_tab = $('#list_tab');
		if (list_tab.hasClass('active')) {
			list_tab.css('display', 'none').removeClass('active');
			$('.kan-ban-tab').css('display', 'block');
			fix_kanban_height(290, 360);
			mainWrapperHeightFix();
		} else {
			list_tab.css('display', 'block').addClass('active');
			$('.kan-ban-tab').css('display', 'none');
		}
	});

	$('.fc-quarter-day-button').on('click', function () {
		gantt.change_view_mode('Quarter Day');
		$('.fc-quarter-day-button').addClass('active');
		$('.fc-half-day-button').removeClass('active');
		$('.fc-day-button').removeClass('active');
		$('.fc-week-button').removeClass('active');
		$('.fc-month-button').removeClass('active');
		
	});

	$('.fc-half-day-button').on('click', function () {
		gantt.change_view_mode('Half Day');
		$('.fc-half-day-button').addClass('active');
		$('.fc-quarter-day-button').removeClass('active');
		$('.fc-day-button').removeClass('active');
		$('.fc-week-button').removeClass('active');
		$('.fc-month-button').removeClass('active');
	});

	$('.fc-day-button').on('click', function () {
		gantt.change_view_mode('Day');
		$('.fc-half-day-button').removeClass('active');
		$('.fc-quarter-day-button').removeClass('active');
		$('.fc-day-button').addClass('active');
		$('.fc-week-button').removeClass('active');
		$('.fc-month-button').removeClass('active');
	});

	$('.fc-week-button').on('click', function () {
		gantt.change_view_mode('Week');
		$('.fc-half-day-button').removeClass('active');
		$('.fc-quarter-day-button').removeClass('active');
		$('.fc-day-button').removeClass('active');
		$('.fc-week-button').addClass('active');
		$('.fc-month-button').removeClass('active');
	});

	$('.fc-month-button').on('click', function () {
		gantt.change_view_mode('Month');
		$('.fc-half-day-button').removeClass('active');
		$('.fc-quarter-day-button').removeClass('active');
		$('.fc-day-button').removeClass('active');
		$('.fc-week-button').removeClass('active');
		$('.fc-month-button').addClass('active');
	});
	



});

	function change_work_order_view() {
		'use strict';

		if($( ".article_change_icon" ).hasClass( "fa fa-th-list" )){
			$( ".article_change_icon" ).removeClass("fa fa-th-list");
			$( ".article_change_icon" ).addClass("fa fa-archive");
		}else{
			$( ".article_change_icon" ).removeClass("fa fa-archive");
			$( ".article_change_icon" ).addClass("fa fa-th-list");
		}

		var KB_Articles_ServerParams = {
			"group_id"    : "select[name='group[]']",
		};

		$.each($('._hidden_inputs._filters input'), function () {
			KB_Articles_ServerParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
		});
		$('._filter_data').toggleClass('hide');
		initDataTable('.table-articles', window.location.href, [0], [0], KB_Articles_ServerParams, [0, 'desc']);

		$('#group').on('change', function() {
			$('.table-articles').DataTable().ajax.reload().columns.adjust().responsive.recalc();
		});
	}

	work_orders = <?php echo new_html_entity_decode(json_encode($data_timeline)); ?>;
	gantt = new Gantt("#timeline", work_orders, {
		custom_popup_html: function(work_order) {
        // the work_order object will contain the updated
        // dates and progress value

        if(work_order.duration_expected != undefined){
        	duration_expected = '<p class="details_title"> Total time: '+work_order.duration_expected+'</p>';
        }else{
        	duration_expected = ''
        }

        if(work_order.real_duration != undefined){
        	real_duration = '<p class="details_title"> Estimate hour: '+work_order.real_duration+'</p>';
        }else{
        	real_duration = ''
        }

        if(work_order.quantity_produced != undefined){
        	quantity_produced = '<p class="details_title"> Estimate hour: '+work_order.quantity_produced+'</p>';
        }else{
        	quantity_produced = ''
        }

        return `
        <div class="details-container">
        <h5 class="details_title">  ${work_order.name}</h5>
        <hr>
        <p class="details_title"> Start date: ${work_order.start}</p>
        <p class="details_title"> End date: ${work_order.end}</p>
        ${duration_expected}
        ${real_duration}
        ${quantity_produced}
        </div>
        `;
    },
    
   
});

	gantt.change_view_mode('Quarter Day') 

</script>