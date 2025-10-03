
var Input_debit_total = $('#bill-debit-account').children().length;
(function($) {
  "use strict"; 
  $("body").on('click', '.new_template', function() {
    console.log('asdasd');
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
    
    new_template.find('label[for="part[0]"]').attr('for', 'part[' + Input_debit_total + ']');
    new_template.find('select[name="part[0]"]').attr('name', 'part[' + Input_debit_total + ']');
    new_template.find('select[id="part[0]"]').attr('id', 'part[' + Input_debit_total + ']').selectpicker('refresh');

    new_template.find('input[id="qty[0]"]').attr('name', 'qty['+Input_debit_total+']').val('');
    new_template.find('input[id="qty[0]"]').attr('id', 'qty['+Input_debit_total+']').val('');

    new_template.find('button[name="add_template"] i').removeClass('fa-plus').addClass('fa-minus');
    new_template.find('button[name="add_template"]').removeClass('new_template').addClass('remove_template').removeClass('btn-success').addClass('btn-danger');

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
      
    Input_debit_total++;
  });

  $("body").on('click', '.remove_template', function() {
      $(this).parents('.template_children').remove();
  });

  appValidateForm($('#work-order-form'),{
    vehicle_id:'required',
    vendor_id:'required',
    issue_date:'required',
    total:'required',
    status:'required',
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