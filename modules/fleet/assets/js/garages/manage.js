(function(){
	"use strict";
	var fnServerParams = {
		"id": "[name='id']",
		"maintenance_type": "[name='maintenance_type_filter']",
		"from_date": "[name='from_date_filter']",
		"to_date": "[name='to_date_filter']"
	}
	initDataTable('.table-garages', admin_url + 'fleet/garages_table', '', '', fnServerParams, [1, 'desc']);
	$('select[name="maintenance_type_filter"], input[name="from_date_filter"], input[name="to_date_filter"]').change(function(){
		$('.table-garages').DataTable().ajax.reload();
	});
	appValidateForm($('#garages-form'), {
		'name': 'required',
	})

	$("input[data-type='currency']").on({
		keyup: function() {        
			formatCurrency($(this));
		},
		blur: function() { 
			formatCurrency($(this), "blur");
		}
	});

	$(document).on("click","#mass_select_all",function() {
		var favorite = [];
		if($(this).is(':checked')){
			$('.individual').prop('checked', true);
			$.each($(".individual"), function(){ 
				favorite.push($(this).data('id'));
			});
		}else{
			$('.individual').prop('checked', false);
			favorite = [];
		}

		$("input[name='check']").val(favorite);
	});

})(jQuery);

/**
 * add asset
 */
 function add(){
 	"use strict";
 	$('#add_new_garages').modal('show');
 	$('#add_new_garages .add-title').removeClass('hide');
 	$('#add_new_garages .edit-title').addClass('hide');
 	$('#add_new_garages input[name="id"]').val('');
 	$('#add_new_garages input[type="text"]').val('');
 	$('#add_new_garages select').val('').change();
 	$('#add_new_garages textarea').val('');
 	$('#add_new_garages input[type="checkbox"]').prop('checked', false);
 }

/**
 * edit
 */
 function edit(id){
 	"use strict";
 	$('#add_new_garages').modal('show');
 	$('#add_new_garages .add-title').addClass('hide');
 	$('#add_new_garages .edit-title').removeClass('hide');
 	$('#add_new_garages input[name="id"]').val(id);
 	var requestURL = admin_url+'fleet/get_data_garages/' + (typeof(id) != 'undefined' ? id : '');
 	requestGetJSON(requestURL).done(function(response) {
 		$('input[name="name"]').val(response.name);
 		$('select[name="country"]').val(response.country).change();
 		$('input[name="state"]').val(response.state);
 		$('input[name="zip"]').val(response.zip);
 		$('input[name="city"]').val(response.city);
 		$('textarea[name="address"]').val(response.address);
 		$('textarea[name="notes"]').val(response.notes);

 	}).fail(function(data) {
 		alert_float('danger', 'Error');
 	});
 }

 function bulk_delete(){
 	"use strict";
 	var print_id = $('input[name="check"]').val();
 	if(print_id != ''){
 		if(confirm($('input[name="are_you_sure_you_want_to_delete_these_items"]').val()) == true){
 			window.location.href = admin_url+"fixed_equipment/delete_all_maintenance/"+encodeURIComponent(print_id);
 		}
 	}
 	else{
 		alert_float('danger', $('input[name="please_select_at_least_one_item_from_the_list"]').val());
 	}
 }

 function checked_add(el){
 	"use strict";
 	var id = $(el).data("id");
 	var id_product = $(el).data("product");
 	if ($(".individual").length == $(".individual:checked").length) {
 		$("#mass_select_all").attr("checked", "checked");
 		var value = $("input[name='check']").val();
 		if(value != ''){
 			value = value + ',' + id;
 		}else{
 			value = id;
 		}
 	} else {
 		$("#mass_select_all").removeAttr("checked");
 		var value = $("input[name='check']").val();
 		var arr_val = value.split(',');
 		if(arr_val.length > 0){
 			$.each( arr_val, function( key, value ) {
 				if(value == id){
 					arr_val.splice(key, 1);
 					value = arr_val.toString();
 					$("input[name='check']").val(value);
 				}
 			});
 		}
 	}
 	if($(el).is(':checked')){
 		var value = $("input[name='check']").val();
 		if(value != ''){
 			value = value + ',' + id;
 		}else{
 			value = id;
 		}
 		$("input[name='check']").val(value);
 	}else{
 		var value = $("input[name='check']").val();
 		var arr_val = value.split(',');
 		if(arr_val.length > 0){
 			$.each( arr_val, function( key, value ) {
 				if(value == id){
 					arr_val.splice(key, 1);
 					value = arr_val.toString();
 					$("input[name='check']").val(value);
 				}
 			});
 		}
 	}
 }
