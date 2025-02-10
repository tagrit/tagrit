<script type="text/javascript">
	var commodity_type_value, data;
  var Input_total = $('#journal-entry-rows .template_children').length;
  var timer = null;

(function($) {
	"use strict";

  acc_init_currency();

	appValidateForm($('#journal-entry-form'), {
		journal_date: 'required',
		number: 'required',
    });


  $("body").on('click', '.new_template', function() {
    var new_template = $('#journal-entry-rows').find('.template_children').eq(0).clone().appendTo('#journal-entry-rows');

    for(var i = 0; i <= new_template.find('#template-item').length ; i++){
        if(i > 0){
          new_template.find('#template-item').eq(i).remove();
        }
        new_template.find('#template-item').eq(1).remove();
    }

    new_template.find('.template').attr('value', Input_total);
    new_template.find('button[role="combobox"]').remove();
    new_template.find('select').selectpicker('refresh');
    // start expense
    
    new_template.find('label[for="account[0]"]').attr('for', 'account[' + Input_total + ']');
    new_template.find('select[name="account[0]"]').attr('name', 'account[' + Input_total + ']');
    new_template.find('select[id="account[0]"]').attr('id', 'account[' + Input_total + ']').selectpicker('refresh');

    new_template.find('input[id="debit_amount[0]"]').attr('name', 'debit_amount['+Input_total+']').val('');
    new_template.find('input[id="debit_amount[0]"]').attr('data-index', Input_total);
    new_template.find('input[id="debit_amount[0]"]').attr('id', 'debit_amount['+Input_total+']');

    new_template.find('input[id="credit_amount[0]"]').attr('name', 'credit_amount['+Input_total+']').val('');
    new_template.find('input[id="credit_amount[0]"]').attr('data-index', Input_total);
    new_template.find('input[id="credit_amount[0]"]').attr('id', 'credit_amount['+Input_total+']');

    new_template.find('input[id="description_detail[0]"]').attr('name', 'description_detail['+Input_total+']').val('');
    new_template.find('input[id="description_detail[0]"]').attr('id', 'description_detail['+Input_total+']');

    new_template.find('button[name="add_template"] i').removeClass('fa-plus').addClass('fa-minus');
    new_template.find('button[name="add_template"]').removeClass('new_template').addClass('remove_template').removeClass('btn-success').addClass('btn-danger');

    $("input[data-type='currency']").on({
      keyup: function() {
        formatCurrency($(this));
        clearTimeout(timer); 
        timer = setTimeout(calculate_amount_total, 1000);
      },
      blur: function() {
        formatCurrency($(this), "blur");
      }
    });

    Input_total++;
  });

  $("body").on('click', '.remove_template', function() {
      $(this).parents('.template_children').remove();
      calculate_amount_total();
  });

  $("body").on('change', 'input[name^="debit_amount"]', function() {
      if($(this).val() != ''){
        var i = $(this).data('index');
        $('input[name="credit_amount['+i+']"]').val('');
      }
  });

  $("body").on('change', 'input[name^="credit_amount"]', function() {
      if($(this).val() != ''){
        var i = $(this).data('index');
        $('input[name="debit_amount['+i+']"]').val('');
      }
  });

  $('.journal-entry-form-submiter').on('click', function() {
    var debit_amount = 0;
    var checked = 0;
    $('input[name^="debit_amount"]').each(function() {
      if($(this).val() != ''){
        debit_amount += parseFloat(unFormatNumber($(this).val()));
      }else{
        var i = $(this).data('index');
        var v = $('input[name="credit_amount['+i+']"]').val();
        if(v == ''){
          alert('<?php echo _l('journal_entry_invalid_value'); ?>');
          checked = 1;
          return false;
        }
      }
    });

    var credit_amount = 0;
    $('input[name^="credit_amount"]').each(function() {
      if($(this).val() != ''){
        credit_amount += parseFloat(unFormatNumber($(this).val()));
      }
    });
      
    if(debit_amount != credit_amount){
      alert('<?php echo _l('please_balance_debits_and_credits'); ?>');
      checked = 1;
    }else if (debit_amount == 0) {
      alert('<?php echo _l('amount_must_be_greater_than_0'); ?>');
      checked = 1;
    }

    if(checked == 0){
      $('input[name="amount"]').val(debit_amount);
      $('#journal-entry-form').submit();
    }
    
    return false;
	});

  $("input[data-type='currency']").on({
    keyup: function() {
      formatCurrency($(this));
      clearTimeout(timer); 
        timer = setTimeout(calculate_amount_total, 1000);
    },
    blur: function() {
      formatCurrency($(this), "blur");
    }
  });
})(jQuery);

function calculate_amount_total (){
  var debit_amount = 0;
  $('input[name^="debit_amount"]').each(function() {
    if($(this).val() != ''){
      debit_amount += parseFloat(unFormatNumber($(this).val()));
    }
  });

  var credit_amount = 0;
  $('input[name^="credit_amount"]').each(function() {
    if($(this).val() != ''){
      credit_amount += parseFloat(unFormatNumber($(this).val()));
    }
  });

  $('input[name="amount"]').val(debit_amount);  
  $('.total_debit').html(format_money(debit_amount));
  $('.total_credit').html(format_money(credit_amount));
}

function calculate_amount_total2(){
  "use strict";
  var journal_entry = JSON.parse(JSON.stringify(commodity_type_value.getData()));
  var total_debit = 0, total_credit = 0;
  $.each(journal_entry, function(index, value) {
    if(value[1] != ''){
      total_debit += parseFloat(value[1]);
    }
    if(value[2] != ''){
      total_credit += parseFloat(value[2]);
    }
  });

  $('.total_debit').html(format_money(total_debit));
  $('.total_credit').html(format_money(total_credit));
}

// Set the currency for accounting
function acc_init_currency() {
  "use strict";
  
  var selectedCurrencyId = <?php echo new_html_entity_decode($currency->id); ?>;

  requestGetJSON('misc/get_currency/' + selectedCurrencyId)
      .done(function(currency) {
          // Used for formatting money
          accounting.settings.currency.decimal = currency.decimal_separator;
          accounting.settings.currency.thousand = currency.thousand_separator;
          accounting.settings.currency.symbol = currency.symbol;
          accounting.settings.currency.format = currency.placement == 'after' ? '%v %s' : '%s%v';
      });
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