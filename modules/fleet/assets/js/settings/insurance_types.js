var fnServerParams;
(function($) {
		"use strict";

		fnServerParams = {
    };

    appValidateForm($('#insurance-type-form'), {
			account_type_id: 'required',
			name: 'required',
    	},insurance_type_form_handler);

    init_insurance_types_table();
    
    $('.add-new-insurance-type').on('click', function(){
      $('#insurance-type-modal').find('button[type="submit"]').prop('disabled', false);

      $('input[name="name"]').val('');
      tinyMCE.activeEditor.setContent('');
      $('textarea[name="description"]').val('');
      $('input[name="id"]').val('');
      $('#insurance-type-modal').modal('show');
    });
})(jQuery);

function init_insurance_types_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-insurance-types')) {
    $('.table-insurance-types').DataTable().destroy();
  }
  initDataTable('.table-insurance-types', admin_url + 'fleet/insurance_types_table', false, false, fnServerParams);
}


function edit_insurance_type(id) {
  "use strict";
    $('#insurance-type-modal').find('button[type="submit"]').prop('disabled', false);

  requestGetJSON(admin_url + 'fleet/get_data_insurance_type/'+id).done(function(response) {
      $('#insurance-type-modal').modal('show');

      $('input[name="name"]').val(response.name);
      $('input[name="id"]').val(id);

      if(response.description != null){
          	tinyMCE.activeEditor.setContent(response.description);
      }else{
          	tinyMCE.activeEditor.setContent('');
      }
      $('textarea[name="description"]').val(response.description);
  });
}

function insurance_type_form_handler(form) {
    "use strict";
    $('#insurance-type-modal').find('button[type="submit"]').prop('disabled', true);
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

	 		    init_insurance_types_table();
        }else{
          alert_float('danger', response.message);
        }
        $('#insurance-type-modal').modal('hide');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    return false;
}