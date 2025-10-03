<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php

?>
<script>
$(function() {

    "use strict"; 

    appValidateForm($('.vendor-form'), 
    {
        company: 'required', 
        vendor_code: {
               required: true,
               remote: {
                url: site_url + "admin/accounting/vendor_code_exists",
                type: 'post',
                data: {
                    vendor_code: function() {
                        return $('input[name="vendor_code"]').val();
                    },
                    userid: function() {
                        return $('input[name="userid"]').val();
                    }
                }
            }
        }
    }, vendorSubmitHandler);

    $('.menu-item-accounting_expenses ').addClass('active');
    $('.menu-item-accounting_expenses ul').addClass('in');
    $('.sub-menu-item-accounting_vendors').addClass('active');

	$('.billing-same-as-customer').on('click', function(e) {
        e.preventDefault();
        $('textarea[name="billing_street"]').val($('textarea[name="address"]').val());
        $('input[name="billing_city"]').val($('input[name="city"]').val());
        $('input[name="billing_state"]').val($('input[name="state"]').val());
        $('input[name="billing_zip"]').val($('input[name="zip"]').val());
        $('select[name="billing_country"]').selectpicker('val', $('select[name="country"]').selectpicker('val'));
    });

    $('.customer-copy-billing-address').on('click', function(e) {
        e.preventDefault();
        $('textarea[name="shipping_street"]').val($('textarea[name="billing_street"]').val());
        $('input[name="shipping_city"]').val($('input[name="billing_city"]').val());
        $('input[name="shipping_state"]').val($('input[name="billing_state"]').val());
        $('input[name="shipping_zip"]').val($('input[name="billing_zip"]').val());
        $('select[name="shipping_country"]').selectpicker('val', $('select[name="billing_country"]').selectpicker('val'));
    });

    $('.customer-form-submiter').on('click', function() {
        var form = $('.vendor-form');
        if (form.valid()) {
            form.find('.additional').html('');
            form.submit();
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

    $("input[data-type='phonenumber']").on({
      keyup: function() {
        formatPhoneNumber($(this));
      },
      blur: function() {
        formatPhoneNumber($(this));
      }
    });

    $("input[data-type='phonenumber']").keyup();
});


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

function formatPhoneNumber(input) {
  var input_val = input.val();

  var match = input_val.replace(/\D+/g, '');
    
  var part1 = match.length > 2 ? match.substring(0,3) : match;
  var part2 = match.length > 3 ? '-' + match.substring(3, 6) : '';
  var part3 = match.length > 6 ? '-' + match.substring(6, 10) : '';  

  input.val(part1+''+part2+''+part3);
}

function vendorSubmitHandler(form){
  "use strict";

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
          if(response.url != ''){
            window.location.href = response.url;
          }
        }else{
          alert_float('danger', response.message);
        }
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });
  }

</script>