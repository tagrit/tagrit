var fnServerParams;
  (function($) {
    "use strict";

    appValidateForm($('#time-card-form'), {
      start_time: 'required',
      end_time: 'required',
    },fuel_form_handler);

    fnServerParams = {
      "logbook_id": '[name="logbook_id"]',
      "status": '[name="status"]',
      "from_date": '[name="from_date"]',
      "to_date": '[name="to_date"]',
    };

    $('.add-new-time-card').on('click', function(){
      $('#time-card-modal').find('button[type="submit"]').prop('disabled', false);
      $('#time-card-modal').modal('show');
      $('#time-card-modal input[name="id"]').val('');
      $('#time-card-modal input[name="start_time"]').val('');
      $('#time-card-modal input[name="end_time"]').val('');
      $('#time-card-modal textarea[name="notes"]').val('');
    });

    init_time_card_table();

  $("input[data-type='currency']").on({
      keyup: function() {
        formatCurrency($(this));
      },
      blur: function() {
        formatCurrency($(this), "blur");
      }
  });
})(jQuery);

function logbook_status_mark_as(status, logbook_id) {
	"use strict"; 
	
	var url = 'fleet/logbook_status_mark_as/' + status + '/' + logbook_id;
	$("body").append('<div class="dt-loader"></div>');

	requestGetJSON(url).done(function (response) {
		$("body").find('.dt-loader').remove();
		if (response.success === true || response.success == 'true') {
			alert_float('success','Status changed');
    		setTimeout(function(){location.reload();},1500);
		}
	});
}


function init_time_card_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-time-card')) {
    $('.table-time-card').DataTable().destroy();
  }
  initDataTable('.table-time-card', admin_url + 'fleet/time_card_table', [0], [0], fnServerParams, [1, 'desc']);
}

function fuel_form_handler(form) {
    "use strict";
    $('#time-card-modal').find('button[type="submit"]').prop('disabled', true);

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
          init_time_card_table();
        }else {
          alert_float('danger', response.message);
        }
        $('#time-card-modal').modal('hide');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    return false;
}

function edit_time_card(id) {
  "use strict";
    $('#time-card-modal').find('button[type="submit"]').prop('disabled', false);

  requestGetJSON(admin_url + 'fleet/get_data_time_card/'+id).done(function(response) {
      $('#time-card-modal').modal('show');

      $('input[name="id"]').val(id);
      $('input[name="start_time"]').val(response.start_time);
      $('input[name="end_time"]').val(response.end_time);
      $('textarea[name="notes"]').val(response.notes);

  });
}