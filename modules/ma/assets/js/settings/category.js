var fnServerParams;
(function($) {
		"use strict";

		fnServerParams = {
    };
    init_category_table();

    $('.add-new-category').on('click', function(){

      $('#category-modal').find('button[type="submit"]').prop('disabled', false);
      $('#category-modal').modal('show');
      $('input[name="id"]').val('');
      $('select[name="type"]').val('segment').change();
      $('input[name="name"]').val('');
      $('input[name="color"]').val('');
      $('textarea[name="description"]').val('');
    });

    appValidateForm($('#category-form'), {
      name: 'required',
      type: 'required',
    },category_form_handler);
    
})(jQuery);

function init_category_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-category')) {
    $('.table-category').DataTable().destroy();
  }
  initDataTable('.table-category', admin_url + 'ma/category_table', false, false, fnServerParams);
}

function edit_category(id) {
  "use strict";
  $('#category-modal').find('button[type="submit"]').prop('disabled', false);

  requestGetJSON(admin_url + 'ma/get_data_category/'+id).done(function(response) {
      $('select[name="type"]').val(response.type).change();
      $('input[name="name"]').val(response.name);
      $('.colorpicker-input').colorpicker('setValue', response.color);
      $('input[name="id"]').val(id);
      $('textarea[name="description"]').val(response.description.replace(/(<|&lt;)br\s*\/*(>|&gt;)/g, " "));
      $('#category-modal').modal('show');

  });
}


function category_form_handler(form) {
    "use strict";
    $('#category-modal').find('button[type="submit"]').prop('disabled', true);

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
          init_category_table();
        }else {
          alert_float('danger', response.message);
        }
        $('#category-modal').modal('hide');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    return false;
}