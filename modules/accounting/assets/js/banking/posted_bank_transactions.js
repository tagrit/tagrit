var posted_bank_transaction_params = {
 "from_date": '[name="from_date"]',
 "to_date": '[name="to_date"]',
 "bank_account": '[name="bank_account"]',
 "status": '[name="status"]',
};

var id, type, amount, transaction_banking_id ;

(function($) {
	"use strict";
	init_banking_table();

	$('input[name="from_date"], input[name="to_date"], select[name="status"]').on('change', function() {
		init_banking_table();
	});


  $('select[name="bank_account"]').on('change', function() {
    init_banking_table();
    var bank_id = $(this).val();
    requestGet('accounting/check_plaid_connect/' + bank_id).done(function(response) {
      response = JSON.parse(response);
      if(response === true || response === 'true'){
        $('#update_bank_transactions').removeAttr('disabled');
        $('#update_bank_transactions').attr('href', admin_url+'accounting/plaid_bank_new_transactions?id='+bank_id);
      }else{
        $('#update_bank_transactions').attr('disabled', true);
      }
    });
  });

  $("body").on('change', 'input[name=withdrawals]', function() {
    var value = $(this).val();
    if(value != '0' && value != '0.00'){
        $('input[name=deposits]').val(0);
    }
  });

  $("body").on('change', 'input[name=deposits]', function() {
    var value = $(this).val();
    if(value != '0' && value != '0.00'){
        $('input[name=withdrawals]').val(0);
    }
  });

  appValidateForm($('#edit-transaction-form'), {
      withdrawals: 'required',
      deposits: 'required',
      payee: 'required',
      date: 'required',
    },edit_transaction_form_handler);
})(jQuery);

function init_banking_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-banking')) {
   $('.table-banking').DataTable().destroy();
 }
 initDataTable('.table-banking', admin_url + 'accounting/posted_bank_transactions_table', [], [], posted_bank_transaction_params, [0, 'desc']);
}

function delete_transation(id) {
  "use strict";
    if (confirm("Are you sure?")) {
      var url = admin_url + 'accounting/delete_bank_transaction/'+id;

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

function edit_transaction(invoker){
    "use strict";
    $('#edit-transaction-modal').find('button[id="btn_account_history"]').prop('disabled', false);

    var id = $(invoker).data('id');
    var date = $(invoker).data('date');
    var payee = $(invoker).data('payee');
    var description = $(invoker).data('description');
    var withdrawals = $(invoker).data('withdrawals');
    var deposits = $(invoker).data('deposits');

    $('input[name="id"]').val(id);
    $('input[name="date"]').val(date);
    $('input[name="payee"]').val(payee);
    $('input[name="withdrawals"]').val(withdrawals);
    $('input[name="deposits"]').val(deposits);
    $('textarea[name="description"]').val(description);

    $('#edit-transaction-modal').modal('show');
}


function edit_transaction_form_handler(form) {
    "use strict";
    $('#edit-transaction-modal').find('button[type="submit"]').prop('disabled', true);

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
        }else {
          alert_float('danger', response.message);
        }
        $('#edit-transaction-modal').find('button[type="submit"]').prop('disabled', false);
        $('#edit-transaction-modal').modal('hide');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    return false;
}

function undo_banking_rule(id) {
  "use strict";
    if (confirm("Are you sure?")) {
      var url = admin_url + 'accounting/undo_banking_rule/'+id;

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