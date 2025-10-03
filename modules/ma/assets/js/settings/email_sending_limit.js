var fnServerParams;
(function($) {
		"use strict";

		fnServerParams = {
    };
    $( document ).ready(function() {
    init_email_sending_limit_table();

    $('input[name="ma_second_sending_limit_choice"]').on('change', function() {
      if($(this).is(':checked') == true){
        $(this).parents('form').find('#div_second_sending_limit_choice').removeClass('hide');
      }else{
        $(this).parents('form').find('#div_second_sending_limit_choice').addClass('hide');
      }
    });

  $('.add_email_limit_config').on('click', function(){
      $('#email-limit-config-modal').modal('show');
    });

  appValidateForm($('#email-limit-config-form'), 
    {
      name: 'required',
    });
    });
})(jQuery);

function init_email_sending_limit_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-email_sending_limit')) {
    $('.table-email_sending_limit').DataTable().destroy();
  }

  var table_email_limit = $("table.table-email_sending_limit");

  if(table_email_limit.length > 0){
    var _table_email_limit_api = initDataTable('.table-email_sending_limit', admin_url + 'ma/email_sending_limit_table', false, false, fnServerParams);

    _table_email_limit_api.on("draw", function () {
      init_progress_bars();
    });
  }
}