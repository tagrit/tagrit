var fnServerParams = {};
var id, type, amount;

(function($) {
	"use strict";

	fnServerParams = {
      "status": '[name="status"]',
      "from_date": '[name="from_date"]',
      "to_date": '[name="to_date"]',
    };
    
	appValidateForm($('#convert-form'), {
	      
	      },convert_form_handler);

	$('select[name="status"]').on('change', function() {
	    init_purchase_order_table();
	});

	$('input[name="from_date"]').on('change', function() {
		init_purchase_order_table();
	});

	$('input[name="to_date"]').on('change', function() {
		init_purchase_order_table();
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
  init_purchase_order_table();
  
  $("body").on('click', '.edit_conversion_rate_action', function() {
      $('input[name="exchange_rate"]').val($('input[name="edit_exchange_rate"]').val());

      $('.amount_after_convert').html(format_money(($('input[name="exchange_rate"]').val() * $('input[name="convert_amount"]').val())));
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

        if(type == 'purchase_order'){
            init_selectpicker();
            $('#payment_account_insurance').selectpicker('refresh');
            $('#deposit_to_insurance').selectpicker('refresh');

            $('#payment_account_tax_paye').selectpicker('refresh');
            $('#deposit_to_tax_paye').selectpicker('refresh');

            $('#payment_account_net_pay').selectpicker('refresh');
            $('#deposit_to_net_pay').selectpicker('refresh');
        }else if(type == 'purchase_invoice'){
            init_selectpicker();
            $('#payment_account_insurance').selectpicker('refresh');
            $('#deposit_to_insurance').selectpicker('refresh');

            $('#payment_account_tax_paye').selectpicker('refresh');
            $('#deposit_to_tax_paye').selectpicker('refresh');

            $('#payment_account_net_pay').selectpicker('refresh');
            $('#deposit_to_net_pay').selectpicker('refresh');
        }else if(type == 'purchase_order_return'){
            init_selectpicker();
        }else{
          if(response.debit != 0){
            $('select[name="deposit_to"]').val(response.debit).change();
          }

          if(response.credit != 0){
            $('select[name="payment_account"]').val(response.credit).change();
          }
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
            init_purchase_order_table();
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
            init_purchase_order_table();
        }else{
          alert_float('danger', response.message);
        }
        $('#convert-modal').modal('hide');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    return false;
}

function init_purchase_order_table() {
"use strict";
  if ($.fn.DataTable.isDataTable('.table-purchase-order')) {
    $('.table-purchase-order').DataTable().destroy();
  }
  initDataTable('.table-purchase-order', admin_url + 'accounting/purchase_order_table', [0], [0], fnServerParams, [1, 'desc']);

  if ($.fn.DataTable.isDataTable('.table-purchase-invoice')) {
    $('.table-purchase-invoice').DataTable().destroy();
  }
  initDataTable('.table-purchase-invoice', admin_url + 'accounting/purchase_invoice_table', [0], [0], fnServerParams, [1, 'desc']);

  if ($.fn.DataTable.isDataTable('.table-purchase-payment')) {
    $('.table-purchase-payment').DataTable().destroy();
  }
  initDataTable('.table-purchase-payment', admin_url + 'accounting/purchase_payment_table', [0], [0], fnServerParams, [1, 'desc']);

  if ($.fn.DataTable.isDataTable('.table-purchase-order-return')) {
    $('.table-purchase-order-return').DataTable().destroy();
  }
  initDataTable('.table-purchase-order-return', admin_url + 'accounting/purchase_return_order_table', [0], [0], fnServerParams, [1, 'desc']);

  if ($.fn.DataTable.isDataTable('.table-purchase-refund')) {
    $('.table-purchase-refund').DataTable().destroy();
  }
  initDataTable('.table-purchase-refund', admin_url + 'accounting/purchase_refund_table', [0], [0], fnServerParams, [1, 'desc']);
}

// purchase_order bulk actions action
function bulk_action(event) {
  "use strict";
    if (confirm_delete()) {
        var ids = [],
            data = {};
            data.type = $('input[name="bulk_actions_type"]').val();
            data.mass_convert = $('#mass_convert').prop('checked');
            data.mass_delete_convert = $('#mass_delete_convert').prop('checked');

        if($('input[name="bulk_actions_type"]').val() == 'purchase_order'){
          var rows = $($('#purchase_order_bulk_actions').attr('data-table')).find('tbody tr');
        }else if($('input[name="bulk_actions_type"]').val() == 'purchase_invoice'){
          var rows = $($('#purchase_invoice_bulk_actions').attr('data-table')).find('tbody tr');
        }else if($('input[name="bulk_actions_type"]').val() == 'purchase_payment'){
          var rows = $($('#purchase_payment_bulk_actions').attr('data-table')).find('tbody tr');
        }else if($('input[name="bulk_actions_type"]').val() == 'purchase_order_return'){
          var rows = $($('#purchase_order_return_bulk_actions').attr('data-table')).find('tbody tr');
        }else if($('input[name="bulk_actions_type"]').val() == 'purchase_refund'){
          var rows = $($('#purchase_refund_bulk_actions').attr('data-table')).find('tbody tr');
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