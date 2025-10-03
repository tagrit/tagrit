var fnServerParams;
(function($) {
		"use strict";

		fnServerParams = {
    };

    appValidateForm($('#income-statement-modification-form'), {
			account: 'required',
      name: 'required',
			type: 'required',
    	},income_statement_modification_form_handler);

    init_income_statement_modifications_table();
    
    $('.add-new-income-statement-modification').on('click', function(){
      $('#income-statement-modification-modal').find('button[type="submit"]').prop('disabled', false);
      $('.fomula-list').html('');

      $('input[name="name"]').val('');
      $('input[name="id"]').val('');
      $('#income-statement-modification-modal').modal('show');
    });

    var addMoreLadderInputKey = $('.fomula-list').length;
    $("body").on('click', '.new_fomula', function() {
      if ($(this).hasClass('disabled')) { return false; }

      addMoreLadderInputKey++;
      var newItem = $('.fomula-template').clone().appendTo('.fomula-list');

      newItem.removeClass('hide').removeClass('fomula-template').addClass('fomula-item');
      newItem.find('button[role="combobox"]').remove();
      newItem.find('select').selectpicker('refresh');

      newItem.find('label[for="fomula[0]"]').attr('for', 'fomula[' + addMoreLadderInputKey + ']');
      newItem.find('select[name="fomula[0]"]').attr('name', 'fomula[' + addMoreLadderInputKey + ']');
      newItem.find('select[id="fomula[0]"]').attr('id', 'fomula[' + addMoreLadderInputKey + ']').selectpicker('refresh');

      newItem.find('label[for="account_fomula[0]"]').attr('for', 'account_fomula[' + addMoreLadderInputKey + ']');
      newItem.find('select[name="account_fomula[0]"]').attr('name', 'account_fomula[' + addMoreLadderInputKey + ']');
      newItem.find('select[id="account_fomula[0]"]').attr('id', 'account_fomula[' + addMoreLadderInputKey + ']').selectpicker('refresh');
        
    });

    $("body").on('click', '.remove_fomula', function() {
      $(this).parents('.fomula-item').remove();
  });

})(jQuery);

function init_income_statement_modifications_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-income-statement-modifications')) {
    $('.table-income-statement-modifications').DataTable().destroy();
  }
  initDataTable('.table-income-statement-modifications', admin_url + 'accounting/income_statement_modifications_table', false, false, fnServerParams);
}


function edit_income_statement_modification(id) {
  "use strict";
    $('#income-statement-modification-modal').find('button[type="submit"]').prop('disabled', false);

  requestGetJSON(admin_url + 'accounting/get_data_income_statement_modification/'+id).done(function(response) {
      $('#income-statement-modification-modal').modal('show');
      $('.fomula-list').html('');

      console.log(response.options);

      if(response.options){
      var addMoreLadderInputKey = $('.fomula-list').length;
      for (const [key, fomula] of Object.entries(response.options.fomula)) {
        console.log(key, fomula);
        addMoreLadderInputKey++;
          var newItem = $('.fomula-template').clone().appendTo('.fomula-list');

          newItem.removeClass('hide').removeClass('fomula-template').addClass('fomula-item');
          newItem.find('button[role="combobox"]').remove();
          newItem.find('select').selectpicker('refresh');

          newItem.find('label[for="fomula[0]"]').attr('for', 'fomula[' + addMoreLadderInputKey + ']');
          newItem.find('select[name="fomula[0]"]').attr('name', 'fomula[' + addMoreLadderInputKey + ']');
          newItem.find('select[id="fomula[0]"]').attr('id', 'fomula[' + addMoreLadderInputKey + ']').selectpicker('refresh').val(fomula).change();

          newItem.find('label[for="account_fomula[0]"]').attr('for', 'account_fomula[' + addMoreLadderInputKey + ']');
          newItem.find('select[name="account_fomula[0]"]').attr('name', 'account_fomula[' + addMoreLadderInputKey + ']');
          newItem.find('select[id="account_fomula[0]"]').attr('id', 'account_fomula[' + addMoreLadderInputKey + ']').selectpicker('refresh').val(response.options.account_fomula[key]).change();

      }
      }

      $('select[name="account"]').val(response.account).change();
      $('select[name="type"]').val(response.type).change();
      $('input[name="name"]').val(response.name);
      $('input[name="id"]').val(id);
  });
}

function income_statement_modification_form_handler(form) {
    "use strict";
    // $('#income-statement-modification-modal').find('button[type="submit"]').prop('disabled', true);
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

	 		    init_income_statement_modifications_table();
        }else{
          alert_float('danger', response.message);
        }
        $('#income-statement-modification-modal').modal('hide');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    return false;
}