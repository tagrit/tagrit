var fnServerParams;
(function($) {
		"use strict";

		fnServerParams = {
    };

    init_inspection_forms_table();
    
    $('.add-new-vehicle-type').on('click', function(){
      $('#vehicle-type-modal').find('button[type="submit"]').prop('disabled', false);

      $('input[name="name"]').val('');
      tinyMCE.activeEditor.setContent('');
      $('textarea[name="description"]').val('');
      $('input[name="id"]').val('');
      $('#vehicle-type-modal').modal('show');
    });
})(jQuery);

function init_inspection_forms_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-inspection-forms')) {
    $('.table-inspection-forms').DataTable().destroy();
  }
  initDataTable('.table-inspection-forms', admin_url + 'fleet/inspection_forms_table', false, false, fnServerParams);
}


function edit_vehicle_type(id) {
  "use strict";
    $('#vehicle-type-modal').find('button[type="submit"]').prop('disabled', false);

  requestGetJSON(admin_url + 'fleet/get_data_vehicle_type/'+id).done(function(response) {
      $('#vehicle-type-modal').modal('show');

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

function vehicle_type_form_handler(form) {
    "use strict";
    $('#vehicle-type-modal').find('button[type="submit"]').prop('disabled', true);
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

	 		    init_inspection_forms_table();
        }else{
          alert_float('danger', response.message);
        }
        $('#vehicle-type-modal').modal('hide');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    return false;
}