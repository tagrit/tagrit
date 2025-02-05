var fnServerParams;
(function($) {
		"use strict";

		fnServerParams = {
    };

    appValidateForm($('#account-type-detail-form'), {
			account_type_id: 'required',
			name: 'required',
    	},account_type_detail_form_handler);

    init_account_type_details_table();
    
    $('.add-new-account-type-detail').on('click', function(){
      $('#account-type-detail-modal').find('button[type="submit"]').prop('disabled', false);

      $('input[name="name"]').val('');

      tinyMCE.activeEditor.setContent('');
      $('textarea[name="note"]').val('');
      $('input[name="id"]').val('');
      $('#account-type-detail-modal').modal('show');
    });
})(jQuery);

function init_account_type_details_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-account-type-details')) {
    $('.table-account-type-details').DataTable().destroy();
  }
  initDataTable('.table-account-type-details', admin_url + 'accounting/account_type_details_table', false, false, fnServerParams);
}


function edit_account_type_detail(id) {
  "use strict";
    $('#account-type-detail-modal').find('button[type="submit"]').prop('disabled', false);

  requestGetJSON(admin_url + 'accounting/get_data_account_type_detail/'+id).done(function(response) {
      $('#account-type-detail-modal').modal('show');

      $('select[name="account_type_id"]').val(response.account_type_id).change();
      $('select[name="statement_of_cash_flows"]').val(response.statement_of_cash_flows).change();
      
      $('input[name="name"]').val(response.name);
      $('input[name="id"]').val(id);

      if(response.note != null){
          	tinyMCE.activeEditor.setContent(response.note);
      }else{
          	tinyMCE.activeEditor.setContent('');
      }
      $('textarea[name="note"]').val(response.note);
  });
}

function account_type_detail_form_handler(form) {
    "use strict";
    $('#account-type-detail-modal').find('button[type="submit"]').prop('disabled', true);
    tinyMCE.triggerSave();

    var formURL = form.action;
    var formData = new FormData($(form)[0]);

    $.ajax({
        type: $(form).attr('method'),
        data: formData,
        mimeType: $(form).attr('enctype'),
        contentType: false,
        cache: false,
        processData: false,
        url: formURL
    }).done(function(response) {
        response = JSON.parse(response);
        if (response.success === true || response.success == 'true' || $.isNumeric(response.success)) {
          	alert_float('success', response.message);

	 		    init_account_type_details_table();
        }else{
          alert_float('danger', response.message);
        }
        $('#account-type-detail-modal').modal('hide');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    return false;
}