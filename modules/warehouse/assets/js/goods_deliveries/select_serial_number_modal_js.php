<script>
	
	init_selectpicker();
	$(".selectpicker").selectpicker('refresh');

	$('.btn_submit_multiple_serial_number').on('click', function() {
		'use strict';

		var formdata = $('#serial_number_modal').serializeArray();
		after_wh_add_item_to_table('undefined', 'undefined', formdata);
		$("body").find('#serialNumberModal').modal('hide');

	});
</script>