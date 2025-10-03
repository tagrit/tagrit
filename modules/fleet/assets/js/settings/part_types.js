var fnServerParams;
(function($) {
		"use strict";

		fnServerParams = {
    };

    appValidateForm($('#part-type-form'), {
			account_type_id: 'required',
			name: 'required',
    	},part_type_form_handler);

    init_part_types_table();
    
    $('.add-new-part-type').on('click', function(){
      $('#part-type-modal').find('button[type="submit"]').prop('disabled', false);

      $('input[name="name"]').val('');
      tinyMCE.activeEditor.setContent('');
      $('textarea[name="description"]').val('');
      $('input[name="id"]').val('');
      $('#part-type-modal').modal('show');
    });
})(jQuery);

function init_part_types_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-part-types')) {
    $('.table-part-types').DataTable().destroy();
  }
  initDataTable('.table-part-types', admin_url + 'fleet/part_types_table', false, false, fnServerParams);
}


function edit_part_type(id) {
  "use strict";
    $('#part-type-modal').find('button[type="submit"]').prop('disabled', false);

  requestGetJSON(admin_url + 'fleet/get_data_part_type/'+id).done(function(response) {
      $('#part-type-modal').modal('show');

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

function part_type_form_handler(form) {
    "use strict";
    $('#part-type-modal').find('button[type="submit"]').prop('disabled', true);
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

	 		    init_part_types_table();
        }else{
          alert_float('danger', response.message);
        }
        $('#part-type-modal').modal('hide');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    return false;
}