var fnServerParams1;
var difference = 0;

(function($) {
    "use strict";
    $('li.menu-item-accounting_banking').addClass('active');
    $('li.sub-menu-item-accounting_reconcile_bank_account').addClass('active');

    $('button[type=submit]').click(function() {
        $(window).unbind('beforeunload');
    });
    // Init accountacy currency symbol
    fnServerParams1 = {
        "account": '[name="account"]',
        "reconcile": '[name="reconcile"]',
    };

  appValidateForm($('#adjustment-form'), {
      adjustment_date: 'required',
      closing_date_2: 'required',
      manager_2: 'required',
      date_reconciled_2: 'required',
      }, adjustment_form_handler);

  appValidateForm($('#edit-reconcile-form'),{ending_balance:'required', ending_date:'required'});


    init_transactions_table();
    init_posted_bank_table();

  $("body").on('change', '#mass_select_all_a', function() {
        var to, rows, checked;
        to = $(this).data('to-table');

        rows = $('.table-' + to).find('tbody tr');
        checked = $(this).prop('checked');
        $.each(rows, function() {
            $($(this).find('td').eq(0)).find('input').prop('checked', checked);
        });
    });

    $("body").on('click', '#btn-finish', function() {
        $(window).unbind('beforeunload');
        $('input[name="closing_date"]').val($('input[name="closing_date_2"]').val());
        $('input[name="manager"]').val($('select[name="manager_2"]').val());
        $('input[name="date_reconciled"]').val($('input[name="date_reconciled_2"]').val());

        $('#reconcile-account-form').submit();
    });

    $("body").on('click', '#btn-make-adjusting-entry-submit', function() {
        var payee = $('#make-adjusting-entry-modal [name=make_adjusting_payee]').val();
        var date = $('#make-adjusting-entry-modal [name=make_adjusting_date]').val();
        var adjust_type = $('#make-adjusting-entry-modal [name=make_adjusting_type]:checked').val()
        var transaction_payee = $('.adjust_transaction_params [name=adjust_transaction_payee]').val();
        var transaction_date = $('.adjust_transaction_params [name=adjust_transaction_date]').val();

        if(adjust_type == 'add_transaction'){
            if(date == transaction_date){
                make_adjusting_entry_save();
            }else{
                $('#make-adjusting-entry-modal').modal('hide');
                $('#adjust-transaction-confirm-message').html('Date is not the same. Are you sure you want to do this?');
                $('#adjust-transaction-confirm-modal').modal('show');
            }
        }else{
            $('#make-adjusting-entry-modal').modal('hide');
            $('#adjust-transaction-confirm-message').html('Once the transaction is updated, it cannot be undone. Are you sure you want to do this?');
            $('#adjust-transaction-confirm-modal').modal('show');
        }
    });

    $("body").on('click', '#btn-complete-reconcile-submit', function() {
        //$('#complete-reconcile-modal').modal('hide');
        //$('#adjustment-modal').modal('show');
        $('#reconcile-account-form').submit();
    });

    $("body").on('change', 'input[name=make_adjusting_type]', function() {
        var value = $(this).val();
        if(value == 'update_transaction'){
            $('.div-add-transaction').addClass('hide');
            $('.div-update-transaction').removeClass('hide');
        }else{
            $('.div-add-transaction').removeClass('hide');
            $('.div-update-transaction').addClass('hide');
        }
    });

    $("body").on('change', 'select[name="make_adjusting_transaction"]', function() {
        $.get(admin_url + 'accounting/make_adjusting_transaction_change/'+$(this).val(), function(response) {
            response = JSON.parse(response);
            $('input[name=make_adjusting_withdrawal]').val(response.withdrawal);
            $('input[name=make_adjusting_deposit]').val(response.deposit);
        });
    });

})(jQuery);


function init_transactions_table(){
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-reconcile-transactions')) {
     $('.table-reconcile-transactions').DataTable().destroy();
  }
  var _table = initDataTable('.table-reconcile-transactions', admin_url + 'accounting/reconcile_transactions_table', false, [0], fnServerParams1, [0, 'asc']);

  _table.on( 'draw', function () {
      $('.table-reconcile-transactions tfoot').remove();
      var transactions_withdrawal_total = $('input[name=banking_register_withdrawals]').val();
      var transactions_deposit_total = $('input[name=banking_register_deposits]').val();
      var tfoot = '<tfoot><tr><th></th><th></th><th></th><th class="padding-10"><strong>'+format_money(transactions_withdrawal_total)+'</strong></th><th class="padding-10"><strong>'+format_money(transactions_deposit_total)+'</strong></th><th></th></tr></tfoot>';
      
      $('.table-reconcile-transactions').append(tfoot);
  });
}

function init_posted_bank_table(){
   "use strict";

  if ($.fn.DataTable.isDataTable('.table-reconcile-posted-bank')) {
     $('.table-reconcile-posted-bank').DataTable().destroy();
  }
  var _table = initDataTable('.table-reconcile-posted-bank', admin_url + 'accounting/reconcile_posted_bank_table', false, [0], fnServerParams1, [0, 'asc']);

  _table.on( 'draw', function () {
      $('.table-reconcile-posted-bank tfoot').remove();
      var posted_withdrawal_total = $('input[name=posted_bank_withdrawals]').val();
      var posted_deposit_total = $('input[name=posted_bank_deposits]').val();
      var tfoot = '<tfoot><tr><th></th><th></th><th></th><th class="padding-10"><strong>'+format_money(posted_withdrawal_total)+'</strong></th><th class="padding-10"><strong>'+format_money(posted_deposit_total)+'</strong></th><th></th></tr></tfoot>';
      
      $('.table-reconcile-posted-bank').append(tfoot);
  });
}

function edit_info(){
  "use strict";
    $('#edit-info-modal').modal('show'); 
}

function save_for_later(){
  "use strict";
    $('input[name="finish"]').val(0);
    $('#reconcile-account-form').submit();
}

function finish_now(){
  "use strict";
    $('input[name="finish"]').val(1);
    
    if(difference == 0){
        $('#finish_difference').addClass('hide');
        $('#finish_difference_header').addClass('hide');
        $('#finish_now_header').removeClass('hide');
        $('#btn-add-adjustment-and-finish').addClass('hide');
        $('#btn-finish').removeClass('hide');
    }else{
        $('#finish_difference').removeClass('hide');
        $('#finish_difference_header').removeClass('hide');
        $('#finish_now_header').addClass('hide');
        $('#btn-add-adjustment-and-finish').removeClass('hide');
        $('#btn-finish').addClass('hide');
    }

    $('#adjustment-modal').modal('show');
}


function adjustment_form_handler(form) {
    "use strict";
    $('#adjustment-modal').find('button[type="submit"]').prop('disabled', true);

    $('input[name="adjustment_amount"]').val(difference);
    $('input[name="finish"]').val(1);
    $('input[name="closing_date"]').val($('input[name="closing_date_2"]').val());
    $('input[name="manager"]').val($('select[name="manager_2"]').val());
    $('input[name="date_reconciled"]').val($('input[name="date_reconciled_2"]').val());

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
        if (response.success === 'close_the_book') {
            alert_float('warning', response.message);
            $('#adjustment-modal').find('button[type="submit"]').prop('disabled', false);
        }else if (response.success === true || response.success == 'true' || $.isNumeric(response.success)) {
            $('#reconcile-account-form').submit();
        }else{
            alert_float('danger', response.message);
        }
        $('#adjustment-modal').modal('hide');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.message));
    });

    return false;
}

function match_transactions(){
  "use strict";
  var reconcile_id = $('#reconcile-account-form input[name="reconcile"]').val();
  var account_id = $('#reconcile-account-form input[name="account"]').val();
  requestGetJSON('accounting/match_transactions/' + reconcile_id + '/'+account_id).done(function(response) { 
    if(response.success === 1 || response.success === '1'){
        alert_float('success', response.message);
        
        init_posted_bank_table();
        init_transactions_table();
    }else{
        alert_float('danger', response.message);

        init_posted_bank_table();
        init_transactions_table();
    }

  });
}

function unmatch_transactions(){
  "use strict";
  var reconcile_id = $('#reconcile-account-form input[name="reconcile"]').val();
  var account_id = $('#reconcile-account-form input[name="account"]').val();

  requestGetJSON('accounting/unmatch_transactions/' + reconcile_id+'/'+account_id).done(function(response) { 
    
    if(response.success === true || response.success === 'true'){
        alert_float('success', response.message);
        $('.approval_btn').addClass('hide');
        $('.finish_btn').removeClass('hide');
        
        init_posted_bank_table();
        init_transactions_table();
    }else{
        alert_float('danger', response.message);
        
        init_posted_bank_table();
        init_transactions_table();
    }

  });
}

function complete_reconcile(){
  "use strict";
    $.post(admin_url + 'accounting/get_transaction_uncleared', {
        reconcile_id: $('input[name=reconcile_id]').val(),
    }, function(response) {
        response = JSON.parse(response);

        if(response.status == 1){
            $('#transaction-uncleared-tbody').html(response.html);
            $('#transaction-uncleared-modal').modal('show');
        }else{
            $.post(admin_url + 'accounting/check_complete_reconcile', {
                reconcile_id: $('input[name=reconcile_id]').val(),
            }, function(res) {
                res = JSON.parse(res);

                if(res.leave_uncleared == 1){
                    $('#finish_uncleared_transactions').html(res.html);
                    $('#finish_uncleared_transactions').removeClass('hide');
                    $('#finish_closing_date').addClass('hide');
                    $('#complete_reconcile_transactions').html(res.html);
                    $('#complete_reconcile_transactions').removeClass('hide');
                }else{

                    $('#complete_reconcile_transactions').addClass('hide');
                    $('#finish_uncleared_transactions').addClass('hide');
                    $('#finish_closing_date').removeClass('hide')
                }
                $('#transaction-uncleared-modal').modal('hide');
                $('#complete-reconcile-modal').modal('show');
            });
        }
    });

}

function make_adjusting_entry(transaction_bank_id){
    "use strict";

    $.post(admin_url + 'accounting/get_make_adjusting_entry', {
        transaction_bank_id: transaction_bank_id,
        reconcile_id: $('input[name=reconcile_id]').val(),
    }, function(response) {
        response = JSON.parse(response);
        $('#transaction-uncleared-modal').modal('hide');
        $('#make-adjusting-entry-modal [name=transaction_bank_id]').val(transaction_bank_id);

        $('#make-adjusting-entry-modal input[name=make_adjusting_date]').val(response.date_value);
        $('#make-adjusting-entry-modal input[name=make_adjusting_withdrawal]').val(response.tran_withdrawal);
        $('#make-adjusting-entry-modal input[name=make_adjusting_deposit]').val(response.tran_deposit);
        $('select[name="make_adjusting_transaction"]').html(response.tran_html);
        $('select[name="make_adjusting_transaction"]').selectpicker('refresh');

        $('#make-adjusting-entry-date').html(response.date);
        $('#make-adjusting-entry-amount').html(response.amount);
        $('#make-adjusting-entry-transaction').html(response.date + ' ' + response.payee + ' ' + response.amount);
        $('#make-adjusting-entry-modal').modal('show');

        $('.adjust_transaction_params input[name=adjust_transaction_date]').val(response.date_value);
        $('.adjust_transaction_params input[name=adjust_transaction_payee]').val(response.payee);
    });
}


function leave_it_uncleared(invoker){
    "use strict";
    $.post(admin_url + 'accounting/leave_it_uncleared', {
        transaction_bank_id:   $(invoker).data('id'),
    }, function(response) {
        response = JSON.parse(response);

        if(response.success){
            alert_float('success', response.message);
            init_transactions_table();
            init_posted_bank_table();

        }
        $(invoker).closest('tr').remove();

        complete_reconcile();
    });
}


function cancel_confirm_modal(){
    "use strict";

    $('#adjust-transaction-confirm-modal').modal('hide');
    $('#make-adjusting-entry-modal').modal('show');
}


function make_adjusting_entry_save(){

    $.post(admin_url + 'accounting/make_adjusting_entry_save', {
        transaction_bank_id: $('#make-adjusting-entry-modal [name=transaction_bank_id]').val(),
        account: $('#make-adjusting-entry-modal [name=make_adjusting_account]').val(),
        payee: $('#make-adjusting-entry-modal [name=make_adjusting_payee]').val(),
        type: $('#make-adjusting-entry-modal [name=make_adjusting_type]:checked').val(),
        transaction: $('#make-adjusting-entry-modal [name=make_adjusting_transaction]').val(),
        date: $('#make-adjusting-entry-modal [name=make_adjusting_date]').val(),
        withdrawal: $('#make-adjusting-entry-modal [name=make_adjusting_withdrawal]').val(),
        deposit: $('#make-adjusting-entry-modal [name=make_adjusting_deposit]').val(),
        reconcile: $('#reconcile-account-form input[name="reconcile"]').val(),
    }, function(response) {
        response = JSON.parse(response);
        if(response.success){
            alert_float('success', response.message);
            init_transactions_table();
            init_posted_bank_table();
        }

        $('#adjust-transaction-confirm-modal').modal('hide');
        $('#make-adjusting-entry-modal').modal('hide');
        complete_reconcile();
    });
}