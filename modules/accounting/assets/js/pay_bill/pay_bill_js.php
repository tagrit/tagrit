<script>
var account_change = 1;
var customer_currency = '';
Dropzone.options.expenseForm = false;
var expenseDropzone;

var selectCurrency = $('select[name="currency"]');
<?php if(isset($customer_currency)){ ?>
 var customer_currency = '<?php echo $customer_currency; ?>';
<?php } ?>
(function($) {
	"use strict";
	$('.menu-item-accounting_expenses ').addClass('active');
	$('.menu-item-accounting_expenses ul').addClass('in');
	$('.sub-menu-item-accounting_bills').addClass('active');

	// $('select[name="account_debit"]').on('change', function(){
	// 	if(account_change == 1){
	// 		var data = {};
	// 		data.vendor = $('input[name="vendor"]').val();
	// 		data.account_id = $(this).val();

	// 		var account_credit = $('select[name="account_credit"]').val();

	// 		$.post(admin_url + 'accounting/account_debit_change', data).done(function(response){
	// 			response = JSON.parse(response);

	// 			$('select[name="account_credit"]').html(response.account_credit).selectpicker('refresh');
	// 			account_change = 0;
	// 			$('select[name="account_credit"]').val(account_credit).change();
	// 		});
	// 	}else{
	// 		account_change = 1;
	// 	}
	// });

	// $('select[name="account_credit"]').on('change', function(){
	// 	if(account_change == 1){
	// 	   var data = {};
	// 		data.vendor = $('select[name="vendor"]').val();
	// 		data.account_id = $(this).val();
	// 		var account_debit = $('select[name="account_debit"]').val();

	// 		$.post(admin_url + 'accounting/account_credit_change', data).done(function(response){
	// 			response = JSON.parse(response);

	// 			$('select[name="account_debit"]').html(response.account_debit).selectpicker('refresh');
	// 			account_change = 0;
	// 			$('select[name="account_debit"]').val(account_debit).change();
	// 		});
	// 	}else{
	// 		account_change = 1;
	// 	}
	// });

	$('body').on('change','#project_id', function(){
		var project_id = $(this).val();
		if(project_id != '') {
			if (customer_currency != 0) {
				selectCurrency.val(customer_currency);
				selectCurrency.selectpicker('refresh');
			} else {
				set_base_currency();
			}
		} else {
			do_billable_checkbox();
		}
	});

	$("input[data-type='currency']").on({
      keyup: function() {
        formatCurrency($(this));
      },
      blur: function() {
        formatCurrency($(this), "blur");
      }
    });

	$('input[name^="pay_bill_amount_paid"]').on('change', function(){
		var total = 0;
		var rows = $('input[name^="pay_bill_amount_paid"]');
	    $.each(rows, function() {
	    	if($(this).val() != ''){
	        	total += parseFloat(unFormatNumber($(this).val()));
	    	}
	    });

  		$('input[name="amount"]').val(parseFloat(total));
	    $('#pay-bill-total').html(format_money(total));
    });

	$('select[name="bill_items[]"]').on('change', function(){
		var data = {};
		data.bill_items = $('select[name="bill_items[]"]').val();

		$.post(admin_url + 'accounting/pay_bill_items_change', data).done(function(response){
			response = JSON.parse(response);

			$('#pay-bill-items').html(response.html);

			$("input[data-type='currency']").on({
		      keyup: function() {
		        formatCurrency($(this));
		      },
		      blur: function() {
		        formatCurrency($(this), "blur");
		      }
		    });

			$('input[name^="pay_bill_amount_paid"]').on('change', function(){
				var total = 0;
				var rows = $('input[name^="pay_bill_amount_paid"]');
			    $.each(rows, function() {
			    	if($(this).val() != ''){
			        	total += parseFloat(unFormatNumber($(this).val()));
			    	}
			    });

          		$('input[name="amount"]').val(parseFloat(total));
			    $('#pay-bill-total').html(format_money(total));
		    });

		});
	});

	

	 if($('#dropzoneDragArea').length > 0){
		expenseDropzone = new Dropzone("#expense-form", appCreateDropzoneOptions({
		  autoProcessQueue: false,
		  clickable: '#dropzoneDragArea',
		  previewsContainer: '.dropzone-previews',
		  addRemoveLinks: true,
		  maxFiles: 1,
		  success:function(file,response){
		   response = JSON.parse(response);
		   if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
			 window.location.assign(response.url);
		   }
		 },
	   }));
	 }

	 appValidateForm($('#expense-form'),{
	  account_debit:'required',
	  account_credit:'required',
	  date:'required',
	  amount:'required',
	  vendor:'required',
	},expenseSubmitHandler);

	 <?php if(!isset($pay_bill)) { ?>
		$('select[name="tax"], select[name="tax2"]').on('change', function () {

			delay(function(){
				var $amount = $('#amount'),
				taxDropdown1 = $('select[name="tax"]'),
				taxDropdown2 = $('select[name="tax2"]'),
				taxPercent1 = parseFloat(taxDropdown1.find('option[value="'+taxDropdown1.val()+'"]').attr('data-percent')),
				taxPercent2 = parseFloat(taxDropdown2.find('option[value="'+taxDropdown2.val()+'"]').attr('data-percent')),
				total = $amount.val();

				if(total == 0 || total == '') {
					return;
				}

				if($amount.attr('data-original-amount')) {
				  total = $amount.attr('data-original-amount');
				}

				total = parseFloat(total);

				if(taxDropdown1.val() || taxDropdown2.val()) {

					$('#tax_subtract').removeClass('hide');

					var totalTaxPercentExclude = taxPercent1;
					if(taxDropdown2.val()){
					  totalTaxPercentExclude += taxPercent2;
					}

					var totalExclude = accounting.toFixed(total - exclude_tax_from_amount(totalTaxPercentExclude, total), app.options.decimal_places);
					$('#tax_subtract_total').html(accounting.toFixed(totalExclude, app.options.decimal_places));
				} else {
				   $('#tax_subtract').addClass('hide');
				}
				if($('#tax1_included').prop('checked') == true) {
					subtract_tax_amount_from_expense_total();
				}
			  }, 200);
		});

		$('#amount').on('blur', function(){
		  $(this).removeAttr('data-original-amount');
		  if($(this).val() == '' || $(this).val() == '') {
			  $('#tax1_included').prop('checked', false);
			  $('#tax_subtract').addClass('hide');
		  } else {
			var tax1 = $('select[name="tax"]').val();
			var tax2 = $('select[name="tax2"]').val();
			if(tax1 || tax2) {
				setTimeout(function(){
					$('select[name="tax2"]').trigger('change');
				}, 100);
			}
		  }
		})

		$('#tax1_included').on('change', function() {

		  var $amount = $('#amount'),
		  total = parseFloat($amount.val());

		  // da pokazuva total za 2 taxes  Subtract TAX total (136.36) from expense amount
		  if(total == 0) {
			  return;
		  }

		  if($(this).prop('checked') == false) {
			  $amount.val($amount.attr('data-original-amount'));
			  return;
		  }

		  subtract_tax_amount_from_expense_total();
		});
	  <?php } ?>

	$('body').on('change','input[type=radio][name=payment_method]', function(){
		if (this.value == 'check') {
			$('#div_check').removeClass('hide');
			$('#div_credit_card').addClass('hide');
		}
		else if (this.value == 'credit_card') {
			$('#div_check').addClass('hide');
			$('#div_credit_card').removeClass('hide');
		}	else if (this.value == 'electronic_payment') {
			$('#div_check').addClass('hide');
			$('#div_credit_card').removeClass('hide');
		}
	});

	$('a').click(function() {
		$(window).unbind('beforeunload');
	});


	$("body").on('change', '#mass_select_all', function () {
		var to, rows, checked;
		to = $(this).data('to-table');

		rows = $('.' + to).find('tbody tr');
		checked = $(this).prop('checked');
		$.each(rows, function () {
			$($(this).find('td').eq(0)).find('input').prop('checked', checked);
		});

		caculate_amount_check();
	});

	$("body").on('click', '.list-bills .checkbox', function() {
		caculate_amount_check();
	}); 
})(jQuery);

	function subtract_tax_amount_from_expense_total(){
		"use strict";
		 var $amount = $('#amount'),
		 total = parseFloat($amount.val()),
		 taxDropdown1 = $('select[name="tax"]'),
		 taxDropdown2 = $('select[name="tax2"]'),
		 taxRate1 = parseFloat(taxDropdown1.find('option[value="'+taxDropdown1.val()+'"]').attr('data-percent')),
		 taxRate2 = parseFloat(taxDropdown2.find('option[value="'+taxDropdown2.val()+'"]').attr('data-percent'));

		 var totalTaxPercentExclude = taxRate1;
		 if(taxRate2) {
		  totalTaxPercentExclude+= taxRate2;
		}

		if($amount.attr('data-original-amount')) {
		  total = parseFloat($amount.attr('data-original-amount'));
		}

		$amount.val(exclude_tax_from_amount(totalTaxPercentExclude, total));

		if($amount.attr('data-original-amount') == undefined) {
		  $amount.attr('data-original-amount', total);
		}
	}

	
	function expenseSubmitHandler(form){
	"use strict";

		var rows = $('.table.list-bills').find('tbody tr');
    var ids = [];
    $.each(rows, function() {
        var checkbox = $($(this).find('td').eq(0)).find('input');
        if (checkbox.prop('checked') === true) {
            ids.push(checkbox.val());
        }
    });

    if(ids.length == 0){
        alert_float('warning', '<?php echo _l('acc_select_bill'); ?>'); 
    }else{

		  selectCurrency.prop('disabled',false);

		  $('select[name="tax2"]').prop('disabled',false);
		  $('input[name="billable"]').prop('disabled',false);
		  $('input[name="date"]').prop('disabled',false);

		  $.post(form.action, $(form).serialize()).done(function(response) {
			response = JSON.parse(response);
			if (response.paybillid) {
				if(typeof(expenseDropzone) !== 'undefined'){
					if (expenseDropzone.getQueuedFiles().length > 0) {
						expenseDropzone.options.url = admin_url + 'accounting/add_pay_bill_attachment/' + response.paybillid;
						expenseDropzone.processQueue();
					} else {
						window.location.assign(response.url);
					}
				} else {
				  	window.location.assign(response.url);
				}
		  	} else {
				if(response.message){
					alert_float('warning',response.message);
				}

				if(response.url){
					window.location.assign(response.url);
				}
		  	}
		});
		  return false;
		}
	}

   function set_base_currency(){
	"use strict";
	selectCurrency.val(selectCurrency.data('base'));
	selectCurrency.selectpicker('refresh');
   }

   function caculate_amount_check(el){
	"use strict";
				var rows = $('input[name^="pay_bill_amount_paid"]');
	    if(rows.length > 0){
				var total = 0;
			    $.each(rows, function() {
			    	if($(this).val() != ''){
			        	total += parseFloat(unFormatNumber($(this).val()));
			    	}
			    });

		  		$('input[name="amount"]').val(parseFloat(total));
			    $('#pay-bill-total').html(format_money(total));
	    }else{
				var total_amount = 0;

				var rows = $('.list-bills').find('tbody tr');
				$.each(rows, function() {
					var checkbox = $($(this).find('td').eq(0)).find('input');
					if (checkbox.is(":checked") == true) {
						total_amount = total_amount + parseFloat(checkbox.data('amount'));
					}
				});
			   
				$('input[name="amount"]').val(total_amount);
	    }
   }

   function create_check(){
	  "use strict";
	  var rows = $('.list-bills').find('tbody tr');
	  var ids = [];

	  $.each(rows, function() {
		  var checkbox = $($(this).find('td').eq(0)).find('input');
		  if (checkbox.prop('checked') === true) {
			  ids.push(checkbox.val());
		  }
	  });

	  if(ids.length == 0){
		  alert_float('warning', '<?php echo _l('acc_select_bill'); ?>'); 
	  }else{
		  $('input[name="bill_ids"]').val(ids.toString());
		  $('#check_bill-form').submit();  
	  }
   }


function formatNumber(n) {
  "use strict";
  // format number 1000000 to 1,234,567
  return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
}

function unFormatNumber(n) {
  "use strict";
  // format number 1,000,000 to 1000000  
  return n.replace(/([,])+/g, "");
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

  var input_max = parseFloat(input.attr('max-amount'));
  var _input_val = parseFloat(unFormatNumber(input_val));

  if(_input_val > input_max){
  	input.parents('.form-group').find('p[id="'+input.attr('id')+'-error"]').remove();
  	input.parents('.form-group').addClass('has-error').append('<p id="'+input.attr('id')+'-error" class="text-danger" style="">Please enter a value less than or equal to '+input_max+'.</p>');
  }else{
  	input.parents('.form-group').removeClass('has-error');
  	input.parents('.form-group').find('p[id="'+input.attr('id')+'-error"]').remove();
  }
}

</script>