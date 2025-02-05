<script type="text/javascript">
	
$('#auto_generate_lotnumber').on('change', function() {
	'use strict';

	var input_name_status = $('input[id="auto_generate_lotnumber"]').is(":checked");
	if(input_name_status == true){
		$('.option-show-lotnumber-setting').removeClass('hide');
	}else{
		$('.option-show-lotnumber-setting').addClass('hide');
	}
});
</script>