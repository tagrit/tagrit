<script>
	init_selectpicker();
	$(".selectpicker").selectpicker('refresh');

	(function($) {
		"use strict";  

		appValidateForm($("body").find('#add_edit_unit_of_measure'), {
			'unit_name': 'required',
			'category_id': 'required',
			'unit_measure_type': 'required',
		});  
	})(jQuery);

	$('select[name="unit_measure_type"]').on('change', function() {
	 	"use strict";  

		
		var type =$(this).val();
		if(type == 'bigger'){
			$('.smaller_ratio_hide').addClass('hide');
			$('.bigger_ratio_hide').removeClass('hide');
		}else if(type == 'smaller'){
			$('.bigger_ratio_hide').addClass('hide');
			$('.smaller_ratio_hide').removeClass('hide');

		}else{
			$('.smaller_ratio_hide').addClass('hide');
			$('.bigger_ratio_hide').addClass('hide');
		}
	});
</script>