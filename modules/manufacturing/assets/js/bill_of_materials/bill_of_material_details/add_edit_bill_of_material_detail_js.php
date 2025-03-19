<script>
	
	init_selectpicker();
	$(".selectpicker").selectpicker('refresh');

	appValidateForm($("body").find('#add_edit_bill_of_material_detail'), {
		'product_id': 'required',
		'product_qty': 'required',
		'unit_id': 'required',
		'display_order': 'required',
	});

	$('select[name="product_id"]').on('change', function() {
	"use strict";
		
		var product_id =$(this).val();

		$.get(admin_url + 'manufacturing/get_product_variants/' + product_id, function (response) {

			$("select[name='unit_id']").val(response.unit_id).selectpicker('refresh');
				
		}, 'json');

	});

</script>