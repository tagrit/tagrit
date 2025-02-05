var fnServerParams = {};
var id, type, amount;

(function($) {
	"use strict";

	fnServerParams = {
      "product": '[name="product"]',
      "routing": '[name="routing"]',
      "status": '[name="status"]',
    };
    
	appValidateForm($('#convert-form'), {
	      
	      },convert_form_handler);

  $('select[name="product"]').on('change', function() {
      init_manufacturing_order_table();
  });

	$('select[name="routing"]').on('change', function() {
	    init_manufacturing_order_table();
	});

  $('select[name="status"]').on('change', function() {
      init_manufacturing_order_table();
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
  init_manufacturing_order_table();
  
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

        init_selectpicker();
        $('#payment_account_insurance').selectpicker('refresh');
        $('#deposit_to_insurance').selectpicker('refresh');

        $('#payment_account_tax_paye').selectpicker('refresh');
        $('#deposit_to_tax_paye').selectpicker('refresh');

        $('#payment_account_net_pay').selectpicker('refresh');
        $('#deposit_to_net_pay').selectpicker('refresh');
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
            init_manufacturing_order_table();
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
            init_manufacturing_order_table();
        }else{
          alert_float('danger', response.message);
        }
        $('#convert-modal').modal('hide');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    return false;
}

function init_manufacturing_order_table() {
"use strict";
  if ($.fn.DataTable.isDataTable('.table-manufacturing-order')) {
    $('.table-manufacturing-order').DataTable().destroy();
  }
  initDataTable('.table-manufacturing-order', admin_url + 'accounting/manufacturing_order_table', [0], [0], fnServerParams, [1, 'desc']);
}

// stock_import bulk actions action
function bulk_action(event) {
  "use strict";
    if (confirm_delete()) {
        var ids = [],
            data = {};
            data.type = $('input[name="bulk_actions_type"]').val();
            data.mass_convert = $('#mass_convert').prop('checked');
            data.mass_delete_convert = $('#mass_delete_convert').prop('checked');

        if($('input[name="bulk_actions_type"]').val() == 'manufacturing_order'){
          var rows = $($('#manufacturing_order_bulk_actions').attr('data-table')).find('tbody tr');
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