(function($) {
	"use strict";

  if($('input[name="resume"]').val() == 1){
    appValidateForm($('#reconcile-account-form'),{});
  }else{
	  appValidateForm($('#reconcile-account-form'),{ending_balance:'required', ending_date:'required'});
  }


  function get_info_reconcile() {
    requestGet('accounting/get_info_reconcile_bank_account/' + $('select[name="account"]').val()).done(function(response) {
          response = JSON.parse(response);
          if(response.resume_reconciling == true || response.resume_reconciling == 'true'){
            appValidateForm($('#reconcile-account-form'),{});
            if(response.approval_reconciling == true || response.approval_reconciling == 'true'){
              $('.btnApproval').removeClass('hide');
              $('.btnResume').addClass('hide');
            }else{
              $('.btnApproval').addClass('hide');
              $('.btnResume').removeClass('hide');
            }

            $('#divResume').removeClass('hide');

            
            $('#divInfo').addClass('hide');
            $('input[name="resume"]').val(1);

            $('.edit_reconcile').removeClass('hide');
          }else{
            appValidateForm($('#reconcile-account-form'),{ending_balance:'required', ending_date:'required'});

            $('input[name="resume"]').val(0);
            $('input[name="beginning_balance"]').val(response.beginning_balance);
            formatCurrency($('input[name="beginning_balance"]'));
            caculate_ending_balance();
            $('input[name="ending_date"]').val('');

            $('input[name="expense_date"]').val('');
            $('input[name="income_date"]').val('');
            $('input[name="service_charge"]').val('');
            $('input[name="interest_earned"]').val('');

            $('input[name="debits_for_period"]').val(response.edit_debits_for_period);
            $('input[name="credits_for_period"]').val(response.edit_credits_for_period);
            $('input[name="ending_balance"]').val(response.edit_ending_balance);
            $('input[name="ending_date"]').val(response.edit_ending_date);
            $('input[name="reconcile_id"]').val(response.edit_reconcile_id);


            $('#divResume').addClass('hide');
            $('#divInfo').removeClass('hide');

            $('.hide_start_reconciling').removeClass('hide');
            $('.edit_reconcile').addClass('hide');
          }
            $('.update_reconcile').addClass('hide');

          if(response.hide_restored == true || response.hide_restored == 'true' ){
            $('.hide_restored').addClass('hide');
          } else{
            if(response.closing_date == false || response.closing_date == 'false'){
              $('.hide_restored').removeClass('hide');
            }
          }
      });
  }

	$('select[name="account"]').on('change', function() {
      get_info_reconcile();
  });

  $('.hide_restored').on('click', function(){

    requestGet('accounting/reconcile_bank_account_restored/' + $('select[name="account"]').val()).done(function(response) {
          response = JSON.parse(response);
          if(response.success == true || response.success == 'true'){
            
            if(response.hide_restored == true || response.hide_restored == 'true' ){
              $('.hide_restored').addClass('hide');
            } else{
              $('.hide_restored').removeClass('hide');
            }

            $('input[name="resume"]').val(0);
            $('.divInfo').removeClass('hide');
            $('.divResume').addClass('hide');

            alert_float('success', response.message);
          }else{
            alert_float('warning', response.message);
          }


      });
  });


	$("input[data-type='currency']").on({
      keyup: function() {
        formatCurrency($(this));
      },
      blur: function() {
        formatCurrency($(this), "blur");
      }
  	});

  $('input[name="debits_for_period"]').on('keyup', function(){
    caculate_ending_balance();
  });

  $('input[name="credits_for_period"]').on('keyup', function(){
    caculate_ending_balance();
  });

  $('.edit_reconcile').on('click', function(){
    requestGet('accounting/get_info_reconcile_bank_account/' + $('select[name="account"]').val()).done(function(response) {
          response = JSON.parse(response);
          if(response.resume_reconciling == true || response.resume_reconciling == 'true'){
            appValidateForm($('#reconcile-account-form'),{ending_balance:'required', ending_date:'required'});

            // $('input[name="resume"]').val(0);
            $('input[name="beginning_balance"]').val(response.edit_beginning_balance);
            formatCurrency($('input[name="beginning_balance"]'));
            caculate_ending_balance();
            $('input[name="debits_for_period"]').val(response.edit_debits_for_period);
            $('input[name="credits_for_period"]').val(response.edit_credits_for_period);
            $('input[name="ending_balance"]').val(response.edit_ending_balance);
            $('input[name="ending_date"]').val(response.edit_ending_date);

            $('input[name="expense_date"]').val('');
            $('input[name="income_date"]').val('');
            $('input[name="income_date"]').val('');
            $('input[name="service_charge"]').val('');
            $('input[name="interest_earned"]').val('');
            $('input[name="reconcile_id"]').val(response.edit_reconcile_id);

            // $('#divResume').addClass('hide');
            $('#divInfo').removeClass('hide');
            $('input[name="resume"]').val(1);
          }

          $('.hide_start_reconciling').addClass('hide');
          $('.edit_reconcile').addClass('hide');
          $('.update_reconcile').removeClass('hide');
         
      });
  });

  $('.update_reconcile').on('click', function(){

    var data = {};
        data.account = $('select[name="account"]').val();
        data.beginning_balance = $('input[name="beginning_balance"]').val();
        data.debits_for_period = $('input[name="debits_for_period"]').val();
        data.credits_for_period = $('input[name="credits_for_period"]').val();
        data.ending_balance = $('input[name="ending_balance"]').val();
        data.ending_date = $('input[name="ending_date"]').val();
        data.reconcile_id = $('input[name="reconcile_id"]').val();

    $.get(admin_url+'accounting/update_bank_reconcile', data, function (response) {
          // response = JSON.parse(response);
          if(response.success == true || response.success == 'true'){
            alert_float('success', response.message);
          }else{
            alert_float('warning', response.message);
          }
          $('.edit_reconcile').removeClass('hide');
          $('.update_reconcile').addClass('hide');

      }, 'json');
  });

  
})(jQuery);

function caculate_ending_balance() {
  "use strict";
  var credits = $('input[name="credits_for_period"]').val();
  var debits = $('input[name="debits_for_period"]').val();
  var beginning_balance = $('input[name="beginning_balance"]').val();
  
  if(debits == '' || debits == undefined){
    debits = '0';
  }

  if(credits == '' || credits == undefined){
    credits = '0';
  }

  if(beginning_balance == '' || beginning_balance == undefined){
    beginning_balance = '0';
  }

  var balance = removeCommas(debits) - removeCommas(credits);
  var ending_balance = removeCommas(beginning_balance) - balance;
  $('input[name="ending_balance"]').val(numberWithCommas(ending_balance.toFixed(2)));
}

function numberWithCommas(x) {
  "use strict";
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
function removeCommas(str) {
  "use strict";
  return(str.replace(/,/g,''));
}

function formatNumber(n) {
  "use strict";
  // format number 1000000 to 1,234,567
  return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
}
function formatCurrency(input, blur) {
  "use strict";
  // appends $ to value, validates decimal side
  // and puts cursor back in right position.

  // get input value
  var input_val = input.val();

  // don't validate empty input
  if (input_val === "") { return; }

  // original length
  var original_len = input_val.length;

  // initial caret position
  var caret_pos = input.prop("selectionStart");

  // check for decimal
  if (input_val.indexOf(".") >= 0) {

    // get position of first decimal
    // this prevents multiple decimals from
    // being entered
    var decimal_pos = input_val.indexOf(".");
    var minus = input_val.substring(0, 1);
    if(minus != '-'){
      minus = '';
    }

    // split number by decimal point
    var left_side = input_val.substring(0, decimal_pos);
    var right_side = input_val.substring(decimal_pos);
    // add commas to left side of number
    left_side = formatNumber(left_side);

    // validate right side
    right_side = formatNumber(right_side);

    // Limit decimal to only 2 digits
    right_side = right_side.substring(0, 2);

    // join number by .
    input_val = minus+left_side + "." + right_side;

  } else {
    // no decimal entered
    // add commas to number
    // remove all non-digits
    var minus = input_val.substring(0, 1);
    if(minus != '-'){
      minus = '';
    }
    input_val = formatNumber(input_val);
    input_val = minus+input_val;

  }

  // send updated string to input
  input.val(input_val);

  // put caret back in the right position
  var updated_len = input_val.length;
  caret_pos = updated_len - original_len + caret_pos;
  //input[0].setSelectionRange(caret_pos, caret_pos);
}