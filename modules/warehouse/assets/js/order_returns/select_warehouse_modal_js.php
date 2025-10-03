<script>
	(function($) {
		"use strict";  

		init_selectpicker();
		$('.selectpicker').selectpicker('refresh');
		appValidateForm($('#select_warehouse_modal'), {
			warehouse_id: 'required',
		});

	})(jQuery);
</script>