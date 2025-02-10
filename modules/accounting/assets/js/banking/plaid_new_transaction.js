
var fnServerParams = {
     "bank_account": '[name="bank_account"]',
     "from_date": '[name="fliter_from_date"]',
     "to_date": '[name="fliter_to_date"]',
     "status": '[name="status"]',
    };

var bankId; 

(function($) {
"use strict";
    init_banking_table();

    $('input[name="fliter_from_date"], input[name="fliter_to_date"], select[name="status"]').on('change', function() {
        init_banking_table();
    });
    
    bankId = $('select[name=bank_account]').val();

    $('select[name=bank_account]').on('change', function() {

        var bank_id = this.value;
        let here = new URL(window.location.origin + window.location.pathname);
        here.searchParams.append('group', 'banking_feeds');
        here.searchParams.append('id', bank_id);

        window.location.href = here

    });

    $("body").on('change', 'select[name="match_transaction_transaction"]', function() {
        $.get(admin_url + 'accounting/make_adjusting_transaction_change/'+$(this).val(), function(response) {
            response = JSON.parse(response);
            $('input[name=match_transaction_withdrawal]').val(response.withdrawal);
            $('input[name=match_transaction_deposit]').val(response.deposit);
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
 initDataTable('.table-banking', admin_url + 'accounting/posted_bank_transactions_table', [], [], fnServerParams, [0, 'desc']);
}




(async function() {

const fetchLinkToken = async () => {

    const response = await fetch(admin_url + 'accounting/create_plaid_token');

    const responseJSON = await response.json();

    return responseJSON.link_token;
};



const configs = {

    // 1. Pass a new link_token to Link.

    token: await fetchLinkToken(),

    onSuccess: async function(public_token, metadata) {

    // 2a. Send the public_token to your app server.

    // The onSuccess function is called when the user has successfully

    // authenticated and selected an account to use.

    const successMsg = await fetch(admin_url+'accounting/update_plaid_bank_accounts?public_token='+ public_token + '&bankId='+ bankId, {

    });

    const successJSON = await successMsg.json();

    if(successJSON.error == ''){
        window.location.reload();
    }else{
        window.location.reload();
    }
    setTimeout(function() {
        location.reload();
    }, 2000);
    },

    onExit: async function(err, metadata) {

    // 2b. Gracefully handle the invalid link token error. A link token

    // can become invalidated if it expires, has already been used

    // for a link session, or is associated with too many invalid logins.

    if (err != null && err.error_code === 'INVALID_LINK_TOKEN') {

        linkHandler.destroy();

        linkHandler = Plaid.create({

        ...configs,

        token: await fetchLinkToken(),

        });

    }

    if (err != null) {

    // Handle any other types of errors.

    }

    alert_float('danger','Connection failed, please check your settings: Setting -> Plald environment');


    // metadata contains information about the institution that the

    // user selected and the most recent API request IDs.

    // Storing this information can be helpful for support.

    },

};

var linkHandler = Plaid.create(configs);

    document.getElementById('linkButton').onclick = function() {

    linkHandler.open();

};

})();



//submit form on click

function submitForm(){

  var from_date = $('#from_date').val();

  var bank_id = $('#bank_account').find(":selected").val();

  if(from_date == ''){
    alert_float('warning', 'Please choose a date!');

    return false;
  }

  $('#import_button').prop('disabled',true);



  $.ajax({

       url: admin_url + 'accounting/update_plaid_transaction',

       type: 'POST',

       data: {bank_id: bank_id, from_date : from_date},

       error: function() {

          alert('Something is wrong');

       },

       success: function(response) {

           window.location.reload();

       }

    });      

}

function updatePlaidStatus(){

    var bank_id = $('#bank_account').find(":selected").val();

      $('#delete_button').prop('disabled',true);

     $.ajax({

       url: admin_url + 'accounting/update_plaid_status',

       type: 'POST',

       data: {bank_id: bank_id},

       error: function() {

          alert('Something is wrong');

       },

       success: function(response) {

            window.location.reload();

       }

    });

}

function match_transaction(transaction_bank_id){
    "use strict";

    $.post(admin_url + 'accounting/get_make_adjusting_entry', {
        transaction_bank_id: transaction_bank_id,
        bank_id: bankId,
    }, function(response) {
        response = JSON.parse(response);
        $('#transaction-uncleared-modal').modal('hide');
        $('#match-transaction-modal [name=transaction_bank_id]').val(transaction_bank_id);
        $('#match-transaction-modal input[name=match_transaction_date]').val(response.date_value);
        $('select[name="match_transaction_transaction"]').html(response.tran_html);
        $('select[name="match_transaction_transaction"]').selectpicker('refresh').change();
        $('#match-transaction-date').html(response.date);
        $('#match-transaction-modal').modal('show');
    });
}


function add_transaction(transaction_bank_id){
    "use strict";

    $.post(admin_url + 'accounting/get_make_adjusting_entry', {
        transaction_bank_id: transaction_bank_id,
        reconcile_id: $('input[name=reconcile_id]').val(),
    }, function(response) {
        response = JSON.parse(response);
        $('#transaction-uncleared-modal').modal('hide');
        $('#add-transaction-modal [name=transaction_bank_id]').val(transaction_bank_id);

        if(response.payment != ''){
            $('#add-transaction-modal .add-transaction-vendor').removeClass('hide');
            $('#add-transaction-modal .add-transaction-customer').addClass('hide');
            $('#add-transaction-modal select[name=add_transaction_customer]').val('').change();
            $('#add-transaction-modal select[name=add_transaction_account]').val(80).change();
        }

        if(response.deposit != ''){
            $('#add-transaction-modal .add-transaction-customer').removeClass('hide');
            $('#add-transaction-modal .add-transaction-vendor').addClass('hide');
            $('#add-transaction-modal select[name=add_transaction_vendor]').val('').change();
            $('#add-transaction-modal select[name=add_transaction_account]').val(81).change();
        }

        $('#add-transaction-date').html(response.date);
        $('#add-transaction-payment').html(response.payment);
        $('#add-transaction-deposit').html(response.deposit);
        $('#add-transaction-modal').modal('show');

        $('.adjust_transaction_params input[name=adjust_transaction_date]').val(response.date_value);
        $('.adjust_transaction_params input[name=adjust_transaction_payee]').val(response.payee);
    });
}


function add_transaction_save(){

    $.post(admin_url + 'accounting/add_transaction_save', {
        transaction_bank_id: $('#add-transaction-modal [name=transaction_bank_id]').val(),
        account: $('#add-transaction-modal [name=add_transaction_account]').val(),
        vendor: $('#add-transaction-modal [name=add_transaction_vendor]').val(),
        customer: $('#add-transaction-modal [name=add_transaction_customer]').val(),
    }, function(response) {
        response = JSON.parse(response);
        if(response.success){
            alert_float('success', response.message);
            init_banking_table();
        }

        $('#add-transaction-modal').modal('hide');
    });
}

function ignore_transaction(_transaction_bank_id){
    "use strict";
    $.post(admin_url + 'accounting/leave_it_uncleared', {
        transaction_bank_id:   _transaction_bank_id,
    }, function(response) {
        response = JSON.parse(response);

        if(response.success){
            alert_float('success', response.message);
            init_banking_table();
        }
    });
}



function match_transaction_save(){

    $.post(admin_url + 'accounting/match_transaction_save', {
        transaction_bank_id: $('#match-transaction-modal [name=transaction_bank_id]').val(),
        withdrawal: $('#match-transaction-modal [name=match_transaction_withdrawal]').val(),
        deposit: $('#match-transaction-modal [name=match_transaction_deposit]').val(),
        date: $('#match-transaction-modal [name=match_transaction_date]').val(),
        transaction: $('#match-transaction-modal [name=match_transaction_transaction]').val(),
    }, function(response) {
        response = JSON.parse(response);
        if(response.success){
            alert_float('success', response.message);
            init_banking_table();
        }

        $('#match-transaction-modal').modal('hide');
    });
}


function unmatch_transaction(transaction_bank_id){
  "use strict";
  var reconcile_id = $('#reconcile-account-form input[name="reconcile"]').val();
  var account_id = $('#reconcile-account-form input[name="account"]').val();

  requestGetJSON('accounting/unmatch_transaction/' + transaction_bank_id).done(function(response) { 
    
    if(response.success === true || response.success === 'true'){
        alert_float('success', response.message);
        
        init_banking_table();
    }else{
        alert_float('danger', response.message);
        
        init_banking_table();
    }

  });
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
