<script>
var Input_debit_total = $('#bill-debit-account').children().length;
var Input_credit_total = $('#bill-credit-account').children().length;
var customer_currency = '';
var max_amount = '';
var limit = '';
Dropzone.options.expenseForm = false;
var expenseDropzone;
var timer = null;

(function($) {
  "use strict";
  $('.menu-item-accounting_expenses ').addClass('active');
  $('.menu-item-accounting_expenses ul').addClass('in');
  $('.sub-menu-item-accounting_bills').addClass('active');

  var input = document.getElementById('debit_amount[0]');

  input.addEventListener('change', debit_account_change);


  $("body").on('click', '.new_debit_template', function() {
    var new_template = $('#bill-debit-account').find('.template_children').eq(0).clone().appendTo('#bill-debit-account');

    for(var i = 0; i <= new_template.find('#template-item').length ; i++){
        if(i > 0){
          new_template.find('#template-item').eq(i).remove();
        }
        new_template.find('#template-item').eq(1).remove();
    }

    new_template.find('.template').attr('value', Input_debit_total);
    new_template.find('button[role="combobox"]').remove();
    new_template.find('select').selectpicker('refresh');
    // start expense
    
    new_template.find('label[for="debit_account[0]"]').attr('for', 'debit_account[' + Input_debit_total + ']');
    new_template.find('select[name="debit_account[0]"]').attr('name', 'debit_account[' + Input_debit_total + ']');
    new_template.find('select[id="debit_account[0]"]').attr('id', 'debit_account[' + Input_debit_total + ']').selectpicker('refresh');

    new_template.find('input[id="debit_amount[0]"]').attr('name', 'debit_amount['+Input_debit_total+']').val('');
    new_template.find('input[id="debit_amount[0]"]').attr('id', 'debit_amount['+Input_debit_total+']').val('');

    new_template.find('button[name="add_template"] i').removeClass('fa-plus').addClass('fa-minus');
    new_template.find('button[name="add_template"]').removeClass('new_debit_template').addClass('remove_debit_template').removeClass('btn-success').addClass('btn-danger');

    $("input[data-type='currency']").on({
      keyup: function() {
        formatCurrency($(this));
        clearTimeout(timer); 
        timer = setTimeout(debit_account_change, 1000);
      },
      blur: function() {
        formatCurrency($(this), "blur");
      }
    });

    var input = document.getElementById('debit_amount['+Input_debit_total+']');
    input.addEventListener('change', debit_account_change);
      
  $('input[id="debit_amount[0]"]')

    Input_debit_total++;
  });

  $("body").on('click', '.new_credit_template', function() {
    var new_template = $('#bill-credit-account').find('.template_children').eq(0).clone().appendTo('#bill-credit-account');

    for(var i = 0; i <= new_template.find('#template-item').length ; i++){
        if(i > 0){
          new_template.find('#template-item').eq(i).remove();
        }
        new_template.find('#template-item').eq(1).remove();
    }

    new_template.find('.template').attr('value', Input_credit_total);
    new_template.find('button[role="combobox"]').remove();
    new_template.find('select').selectpicker('refresh');
    // start expense
    
    new_template.find('label[for="credit_account[0]"]').attr('for', 'credit_account[' + Input_credit_total + ']');
    new_template.find('select[name="credit_account[0]"]').attr('name', 'credit_account[' + Input_credit_total + ']');
    new_template.find('select[id="credit_account[0]"]').attr('id', 'credit_account[' + Input_credit_total + ']').selectpicker('refresh');

    new_template.find('input[id="credit_amount[0]"]').attr('name', 'credit_amount['+Input_credit_total+']').val('');
    new_template.find('input[id="credit_amount[0]"]').attr('id', 'credit_amount['+Input_credit_total+']').val('');

    new_template.find('button[name="add_template"] i').removeClass('fa-plus').addClass('fa-minus');
    new_template.find('button[name="add_template"]').removeClass('new_credit_template').addClass('remove_template').removeClass('btn-success').addClass('btn-danger');

    $("input[data-type='currency']").on({
      keyup: function() {
        formatCurrency($(this));
      },
      blur: function() {
        formatCurrency($(this), "blur");
      }
    });
    Input_credit_total++;
  });
  $("body").on('click', '.remove_debit_template', function() {
      $(this).parents('.template_children').remove();
      debit_account_change();
  });

  $("body").on('click', '.remove_template', function() {
      $(this).parents('.template_children').remove();
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
    vendor:'required',
    date:'required',
  },expenseSubmitHandler);

  $("input[data-type='currency']").on({
      keyup: function() {
        formatCurrency($(this));
        clearTimeout(timer); 
        timer = setTimeout(debit_account_change, 1000);
      },
      blur: function() {
        formatCurrency($(this), "blur");
      }
    });
})(jQuery);

function subtract_tax_amount_from_expense_total(){
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
  var debit_amount = 0;
  $('input[name^="debit_amount"]').each(function() {
      debit_amount += parseFloat(unFormatNumber($(this).val()));
  });

  var credit_amount = 0;
  $('input[name^="credit_amount"]').each(function() {
      credit_amount += parseFloat(unFormatNumber($(this).val()));
  });
    
  if(debit_amount != credit_amount){
    alert('<?php echo _l('please_balance_debits_and_credits'); ?>');
    return false;
  }else if(max_amount !== '' && debit_amount > max_amount){
    alert('<?php echo _l('maximum_amount'); ?>: '+format_money(max_amount));
    return false;
  }

  $('select[name="tax2"]').prop('disabled',false);
  $('input[name="billable"]').prop('disabled',false);
  $('input[name="date"]').prop('disabled',false);

  $.post(form.action, $(form).serialize()).done(function(response) {
    response = JSON.parse(response);
    if (response.billid) {
      if(typeof(expenseDropzone) !== 'undefined'){
        if (expenseDropzone.getQueuedFiles().length > 0) {
          expenseDropzone.options.url = admin_url + 'accounting/add_bill_attachment/' + response.billid;
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

function debit_account_change (){
  var debit_account = $("select[name^='debit_account']")
              .map(function(){return $(this).val();}).get();
  var debit_amount = $("input[name^='debit_amount']")
              .map(function(){return $(this).val();}).get();

  var data = {};
  data.account = debit_account;
  data.amount = debit_amount;

  var debit_amount = 0;
  $('input[name^="debit_amount"]').each(function() {
      debit_amount += parseFloat(unFormatNumber($(this).val()));
  });

  var credit_amount = 0;
  $('input[name^="credit_amount"]').each(function() {
      credit_amount += parseFloat(unFormatNumber($(this).val()));
  });
  $('input[name="amount"]').val(debit_amount);  
  $('#bill-total').html(format_money(debit_amount));
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
}

</script>
