<script>
	
	init_selectpicker();
	$(".selectpicker").selectpicker('refresh');

	appValidateForm($("body").find('#add_bill_of_material'), {
		'product_id': 'required',
		'routing_id': 'required',
		'bom_code': 'required',
	});  


	$('input[name="bom_type"]').on('click', function() {
	"use strict";

		var bom_type =$(this).val();
		if(bom_type == 'manufacture_this_product'){
			$('.kit_hide').addClass('hide');
		}else if(bom_type == 'kit'){
			$('.kit_hide').removeClass('hide');

		}
	});

	$('select[name="product_id"]').on('change', function() {
	"use strict";
		
		var product_id =$(this).val();

		$.get(admin_url + 'manufacturing/get_product_variants/' + product_id, function (response) {
			$("select[name='product_variant_id']").html('');

			$("select[name='product_variant_id']").append(response.product_variants);
			$("select[name='product_variant_id']").selectpicker('refresh');

			$("select[name='unit_id']").val(response.unit_id).selectpicker('refresh');


				
		}, 'json');

	});


</script>