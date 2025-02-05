var fnServerParams;
(function($) {
		"use strict";

		fnServerParams = {
    };

    appValidateForm($('#insurance-category-form'), {
			name: 'required',
    	},insurance_category_form_handler);

    init_insurance_categories_table();
    
    $('.add-new-insurance-category').on('click', function(){
      $('#insurance-category-modal').find('button[type="submit"]').prop('disabled', false);

      $('input[name="name"]').val('');
      tinyMCE.activeEditor.setContent('');
      $('textarea[name="description"]').val('');
      $('input[name="id"]').val('');
      $('#insurance-category-modal').modal('show');
    });
})(jQuery);

function init_insurance_categories_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-insurance-categories')) {
    $('.table-insurance-categories').DataTable().destroy();
  }
  initDataTable('.table-insurance-categories', admin_url + 'fleet/insurance_categories_table', false, false, fnServerParams);
}


function edit_insurance_category(id) {
  "use strict";
    $('#insurance-category-modal').find('button[type="submit"]').prop('disabled', false);

  requestGetJSON(admin_url + 'fleet/get_data_insurance_category/'+id).done(function(response) {
      $('#insurance-category-modal').modal('show');

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

function insurance_category_form_handler(form) {
    "use strict";
    $('#insurance-category-modal').find('button[type="submit"]').prop('disabled', true);
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

	 		    init_insurance_categories_table();
        }else{
          alert_float('danger', response.message);
        }
        $('#insurance-category-modal').modal('hide');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    return false;
}