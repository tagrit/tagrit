var fnServerParams;
(function($) {
		"use strict";

		fnServerParams = {
    };

    appValidateForm($('#insurance-status-form'), {
			name: 'required',
    	},insurance_status_form_handler);

    init_insurance_status_table();
    
    $('.add-new-insurance-status').on('click', function(){
      $('#insurance-status-modal').find('button[type="submit"]').prop('disabled', false);

      $('input[name="name"]').val('');
      tinyMCE.activeEditor.setContent('');
      $('textarea[name="description"]').val('');
      $('input[name="id"]').val('');
      $('#insurance-status-modal').modal('show');
    });
})(jQuery);

function init_insurance_status_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-insurance-status')) {
    $('.table-insurance-status').DataTable().destroy();
  }
  initDataTable('.table-insurance-status', admin_url + 'fleet/insurance_status_table', false, false, fnServerParams);
}


function edit_insurance_status(id) {
  "use strict";
    $('#insurance-status-modal').find('button[type="submit"]').prop('disabled', false);

  requestGetJSON(admin_url + 'fleet/get_data_insurance_status/'+id).done(function(response) {
      $('#insurance-status-modal').modal('show');

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

function insurance_status_form_handler(form) {
    "use strict";
    $('#insurance-status-modal').find('button[type="submit"]').prop('disabled', true);
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

	 		    init_insurance_status_table();
        }else{
          alert_float('danger', response.message);
        }
        $('#insurance-status-modal').modal('hide');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    return false;
}