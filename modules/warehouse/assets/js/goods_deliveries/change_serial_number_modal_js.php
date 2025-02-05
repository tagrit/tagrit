<script>
	
	init_selectpicker();
	$(".selectpicker").selectpicker('refresh');

	function wh_submit_serial_number(name_commodity_name, name_serial_number) {
		console.log('name_commodity_name', name_commodity_name);
		console.log('name_serial_number', name_serial_number);

		var old_commodity_name = $('textarea[name="'+name_commodity_name+'"]').val();
		var old_serial_number = $('input[name="'+name_serial_number+'"]').val();
		var new_serial_number = $('select[name="change_serial_number"]').val();

		var new_commodity_name = old_commodity_name.replace(old_serial_number, new_serial_number);

		$('textarea[name="'+name_commodity_name+'"]').val(new_commodity_name);
		$('textarea[name="'+name_commodity_name+'"]').text(new_commodity_name);
		$('input[name="'+name_serial_number+'"]').val(new_serial_number);

		$("body").find('#changeSerialNumberModal').modal('hide');
	}

</script>