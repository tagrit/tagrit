var fnServerParams = {
    	"from_date": '[name="from_date"]',
        "to_date": '[name="to_date"]',
    	"status": '[name="status"]',
	};
var id, type, amount;
	
(function($) {
	"use strict";
	init_banking_table();

	$('input[name="from_date"]').on('change', function() {
		init_banking_table();
	});

	$('input[name="to_date"]').on('change', function() {
		init_banking_table();
	});

    $('select[name="status"]').on('change', function() {
        init_banking_table();
    });

	appValidateForm($('#convert-form'), {
	      
	},convert_form_handler);

    $('input[name="mass_convert"]').on('change', function() {
        if($('#mass_convert').is(':checked') == true){
          $('#mass_delete_convert').prop( "checked", false );
          $('#mass_delete').prop( "checked", false );
        }
      });

      $('input[name="mass_delete_convert"]').on('change', function() {
        if($('#mass_delete_convert').is(':checked') == true){
          $('#mass_convert').prop( "checked", false );
          $('#mass_delete').prop( "checked", false );
        }
      });

      $('input[name="mass_delete"]').on('change', function() {
        if($('#mass_delete').is(':checked') == true){
          $('#mass_convert').prop( "checked", false );
          $('#mass_delete_convert').prop( "checked", false );
        }
      });
})(jQuery);

function init_banking_table() {
"use strict";

 if ($.fn.DataTable.isDataTable('.table-banking')) {
   $('.table-banking').DataTable().destroy();
 }
 initDataTable('.table-banking', admin_url + 'accounting/banking_table', [0], [0], fnServerParams, [1, 'desc']);
}


function convert(invoker){
    "use strict";
    $('#convert-modal').find('button[id="btn_account_history"]').prop('disabled', false);

    id = $(invoker).data('id');
    type = $(invoker).data('type');
    amount = $(invoker).data('amount');

    $('input[name="id"]').val(id);
    $('input[name="type"]').val(type);
    $('input[name="amount"]').val(amount);

    requestGet('accounting/get_data_convert/' + id + '/' + type).done(function(response) {
        response = JSON.parse(response);

        $('#div_info').html(response.html);
        console.log(response);
        if(response.debit != 0){
            $('select[name="deposit_to"]').val(response.debit).change();
        }

        if(response.credit != 0){
            $('select[name="payment_account"]').val(response.credit).change();
        }
    });

  $('#convert-modal').modal('show');
}

function delete_convert(id,type) {
  "use strict";
    if (confirm("Are you sure?")) {
      var url = admin_url + 'accounting/delete_convert/'+id+'/'+type;

      requestGet(url).done(function(response){
          response = JSON.parse(response);
          if (response.success === true || response.success == 'true') { 
            alert_float('success', response.message); 
            init_banking_table();
          }else{
            alert_float('danger', response.message); 
          }
      });
    }
    return false;
}

function convert_form_handler(form) {
    "use strict";
    $('#convert-modal').find('button[id="btn_account_history"]').prop('disabled', true);

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
            init_banking_table();
        }else{
          alert_float('danger', response.message);
        }
        $('#convert-modal').modal('hide');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    return false;
}

// banking bulk actions action
function bulk_action(event) {
  "use strict";
    if (confirm_delete()) {
        var ids = [],
            data = {};
            data.type = 'banking';
            data.mass_convert = $('#mass_convert').prop('checked');
            data.mass_delete = $('#mass_delete').prop('checked');
            data.mass_delete_convert = $('#mass_delete_convert').prop('checked');

        var rows = $($('#banking_bulk_actions').attr('data-table')).find('tbody tr');

        $.each(rows, function() {
            var checkbox = $($(this).find('td').eq(0)).find('input');
            if (checkbox.prop('checked') === true) {
                ids.push(checkbox.val());
            }
        });
        data.ids = ids;
        $(event).addClass('disabled');
        setTimeout(function() {
            $.post(admin_url + 'accounting/transaction_bulk_action', data).done(function() {
                window.location.reload();
            });
        }, 200);
    }
}