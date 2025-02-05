var fnServerParams = {};
var id, type, amount;

(function($) {
	"use strict";

	fnServerParams = {
      "invoice": '[name="invoice"]',
      "payment_mode": '[name="payment_mode"]',
      "status": '[name="status"]',
      "from_date": '[name="from_date"]',
      "to_date": '[name="to_date"]',
    };
    
  acc_init_currency();
	appValidateForm($('#convert-form'), {
	      
	      },convert_form_handler);

	$('select[name="invoice"]').on('change', function() {
	    init_expenses_table();
	});

	$('select[name="payment_mode"]').on('change', function() {
	    init_expenses_table();
	});

	$('select[name="status"]').on('change', function() {
	    init_expenses_table();
	});

	$('input[name="from_date"]').on('change', function() {
		init_expenses_table();
	});

	$('input[name="to_date"]').on('change', function() {
		init_expenses_table();
	});

  $('input[name="mass_convert"]').on('change', function() {
    if($('#mass_convert').is(':checked') == true){
      $('#mass_delete_convert').prop( "checked", false );
    }
  });

  $('input[name="mass_delete_convert"]').on('change', function() {
    if($('#mass_delete_convert').is(':checked') == true){
      $('#mass_convert').prop( "checked", false );
    }
  });
  init_expenses_table();
  
  $("body").on('click', '.edit_conversion_rate_action', function() {
      $('input[name="exchange_rate"]').val($('input[name="edit_exchange_rate"]').val());

      $('.amount_after_convert').html(format_money(($('input[name="exchange_rate"]').val() * $('input[name="expense_amount"]').val())));
      $('.currency_converter_label').html('1 '+$('input[name="currency_from"]').val() +' = '+$('input[name="edit_exchange_rate"]').val()+' '+ $('input[name="currency_to"]').val());
  });
})(jQuery);

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
            init_expenses_table();
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
            init_expenses_table();
        }else{
          alert_float('danger', response.message);
        }
        $('#convert-modal').modal('hide');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    return false;
}

function init_expenses_table() {
"use strict";

 if ($.fn.DataTable.isDataTable('.table-expenses')) {
   $('.table-expenses').DataTable().destroy();
 }
 initDataTable('.table-expenses', admin_url + 'accounting/expenses_table', [0], [0], fnServerParams, [1, 'desc']);
}

// expense bulk actions action
function bulk_action(event) {
  "use strict";
    if (confirm_delete()) {
        var ids = [],
            data = {};
            data.type = $('input[name="bulk_actions_type"]').val();
            data.mass_convert = $('#mass_convert').prop('checked');
            data.mass_delete_convert = $('#mass_delete_convert').prop('checked');

        if($('input[name="bulk_actions_type"]').val() == 'payment'){
          var rows = $($('#sales_bulk_actions').attr('data-table')).find('tbody tr');
        }else if($('input[name="bulk_actions_type"]').val() == 'invoice'){
          var rows = $($('#sales_invoice_bulk_actions').attr('data-table')).find('tbody tr');
        }else if($('input[name="bulk_actions_type"]').val() == 'expense'){
          var rows = $($('#expenses_bulk_actions').attr('data-table')).find('tbody tr');
        }

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

// Set the currency for accounting
function acc_init_currency() {
  "use strict";
  var selectedCurrencyId = $('input[name="currency_id"]').val();
  requestGetJSON('misc/get_currency/' + selectedCurrencyId)
      .done(function(currency) {
          // Used for formatting money
          accounting.settings.currency.decimal = currency.decimal_separator;
          accounting.settings.currency.thousand = currency.thousand_separator;
          accounting.settings.currency.symbol = currency.symbol;
          accounting.settings.currency.format = currency.placement == 'after' ? '%v %s' : '%s%v';
      });
}