(function($) {
	"use strict";  
	var table_vendor = $('.table-vendors');

    var VendorsServerParams = {};
       $.each($('._hidden_inputs._filters input'),function(){
          VendorsServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
      });
       VendorsServerParams['exclude_inactive'] = '[name="exclude_inactive"]:checked';

	var tAPI = initDataTable('.table-vendors', admin_url+'purchase/table_vendor',[0], [0],VendorsServerParams,  [1, 'desc']);
	$('input[name="exclude_inactive"]').on('change',function(){
           tAPI.ajax.reload();
       });

})(jQuery);

function staff_bulk_actions(){
	"use strict";
	$('#table_vendors_list_bulk_actions').modal('show');
}

function purchase_delete_bulk_action(event) {
	"use strict";

	if (confirm_delete()) {
		var mass_delete = $('#mass_delete').prop('checked');

		if(mass_delete == true){
			var ids = [];
			var data = {};

			data.mass_delete = true;
			data.rel_type = 'vendors';

			var rows = $('.table-vendors').find('tbody tr');
			$.each(rows, function() {
				var checkbox = $($(this).find('td').eq(0)).find('input');
				if (checkbox.prop('checked') === true) {
					ids.push(checkbox.val());
				}
			});

			data.ids = ids;
			$(event).addClass('disabled');
			setTimeout(function() {
				$.post(admin_url + 'purchase/purchase_delete_bulk_action', data).done(function() {
					window.location.reload();
				}).fail(function(data) {
					$('#table_vendors_list_bulk_actions').modal('hide');
					alert_float('danger', data.responseText);
				});
			}, 200);
		}else{
			window.location.reload();
		}

	}
}