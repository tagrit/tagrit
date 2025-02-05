var fnServerParams;
(function($) {
		"use strict";

		fnServerParams = {
    };

    appValidateForm($('#criteria-form'), {
			account_type_id: 'required',
			name: 'required',
    	},criteria_form_handler);

    init_criterias_table();
    
    $('.add-new-criteria').on('click', function(){
      $('#criteria-modal').find('button[type="submit"]').prop('disabled', false);

      $('input[name="name"]').val('');
      tinyMCE.activeEditor.setContent('');
      $('textarea[name="description"]').val('');
      $('input[name="id"]').val('');
      $('#criteria-modal').modal('show');
    });
})(jQuery);

function init_criterias_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-criterias')) {
    $('.table-criterias').DataTable().destroy();
  }
  initDataTable('.table-criterias', admin_url + 'fleet/criterias_table', false, false, fnServerParams);
}


function edit_criteria(id) {
  "use strict";
    $('#criteria-modal').find('button[type="submit"]').prop('disabled', false);

  requestGetJSON(admin_url + 'fleet/get_data_criteria/'+id).done(function(response) {
      $('#criteria-modal').modal('show');

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

function criteria_form_handler(form) {
    "use strict";
    $('#criteria-modal').find('button[type="submit"]').prop('disabled', true);
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

	 		    init_criterias_table();
        }else{
          alert_float('danger', response.message);
        }
        $('#criteria-modal').modal('hide');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    return false;
}