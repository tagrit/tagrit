(function($) {
	"use strict";

  if($('input[name="resume"]').val() == 1){
    appValidateForm($('#reconcile-account-form'),{});
  }else{
	  appValidateForm($('#reconcile-account-form'),{ending_balance:'required', ending_date:'required'});
  }
	$('select[name="account"]').on('change', function() {
  		requestGet('accounting/get_info_reconcile/' + $('select[name="account"]').val()).done(function(response) {
	        response = JSON.parse(response);
	        if(response.resume_reconciling == true || response.resume_reconciling == 'true'){
				    appValidateForm($('#reconcile-account-form'),{});

	        	$('#divResume').removeClass('hide');
	        	$('#divInfo').addClass('hide');
	        	$('input[name="resume"]').val(1);
	        }else{
				    appValidateForm($('#reconcile-account-form'),{ending_balance:'required', ending_date:'required'});

	        	$('input[name="resume"]').val(0);
	        	$('input[name="beginning_balance"]').val(response.beginning_balance);
            formatCurrency($('input[name="beginning_balance"]'));
	        	$('input[name="ending_balance"]').val('');
	        	$('input[name="ending_date"]').val('');

	        	$('input[name="expense_date"]').val('');
	        	$('input[name="income_date"]').val('');
	        	$('input[name="service_charge"]').val('');
	        	$('input[name="interest_earned"]').val('');

	        	$('#divResume').addClass('hide');
	        	$('#divInfo').removeClass('hide');
	        }

          if(response.hide_restored == true || response.hide_restored == 'true' ){
            $('.hide_restored').addClass('hide');
          } else{
            if(response.closing_date == false || response.closing_date == 'false'){
              $('.hide_restored').removeClass('hide');
            }
          }
	    });
 	});

  $('.hide_restored').on('click', function(){

    requestGet('accounting/reconcile_restored/' + $('select[name="account"]').val()).done(function(response) {
          response = JSON.parse(response);
          if(response.success == true || response.success == 'true'){
            
            if(response.hide_restored == true || response.hide_restored == 'true' ){
              $('.hide_restored').addClass('hide');
            } else{
              if(response.closing_date == false || response.closing_date == 'false'){
                $('.hide_restored').removeClass('hide');
              }
            }
            $('select[name="account"]').change();
            
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
})(jQuery);


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
    input_val = left_side + "." + right_side;

  } else {
    // no decimal entered
    // add commas to number
    // remove all non-digits
    input_val = formatNumber(input_val);
    input_val = input_val;

  }

  // send updated string to input
  input.val(input_val);

  // put caret back in the right position
  var updated_len = input_val.length;
  caret_pos = updated_len - original_len + caret_pos;
  input[0].setSelectionRange(caret_pos, caret_pos);
}