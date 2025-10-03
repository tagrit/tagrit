<script>
  var _print_a_check = 0;
  var _print_later = 0;
  var _print_multiple_check = 0;
  var fnServerParams = [];
  (function($) {
   "use strict";

   $('input[name^="pay_bill_amount_paid"]').on('change', function(){
    var total = 0;
    var rows = $('input[name^="pay_bill_amount_paid"]');
      $.each(rows, function() {
        if($(this).val() != ''){
            total += parseFloat(unFormatNumber($(this).val()));
        }
      });

      $('input[name="amount"]').val(format_money(total, true));

    });

  $('select[name="bill_items[]"]').on('change', function(){
    var data = {};
    data.bill_items = $('select[name="bill_items[]"]').val();

    $.post(admin_url + 'accounting/pay_bill_items_change', data).done(function(response){
      response = JSON.parse(response);

      $('#pay-bill-items').html(response.html);

      $('#pay-bill-items .col-md-5.col-md-offset-7').remove();

      $("input[data-type='currency']").on({
          keyup: function() {
            formatCurrency($(this));
          },
          blur: function() {
            formatCurrency($(this), "blur");
          }
        });

      var total = 0;
        var rows = $('input[name^="pay_bill_amount_paid"]');
          $.each(rows, function() {
            if($(this).val() != ''){
                total += parseFloat(unFormatNumber($(this).val()));
            }
          });

          $('input[name="amount"]').val(format_money(total, true));

      $('input[name^="pay_bill_amount_paid"]').on('change', function(){
        var total = 0;
        var rows = $('input[name^="pay_bill_amount_paid"]');
          $.each(rows, function() {
            if($(this).val() != ''){
                total += parseFloat(unFormatNumber($(this).val()));
            }
          });

          $('input[name="amount"]').val(format_money(total, true));
        });

    });
  });

    $('li.sub-menu-item-accounting_checks').addClass('active');
 
    appValidateForm($('#check-form'), {
      },check_form_handler);

    window.onbeforeunload = null;

   $('select[name="bank_account"]').on('change', function(){
    var bank_account = $(this).val();
    if(bank_account != '' && bank_account != null && bank_account != undefined){
     requestGetJSON(admin_url + 'accounting/get_bank_account_data/'+bank_account).done(function(response) { 
      $('#routing_number_span').html(response.routing_number);
      $('#account_number_span').html(response.account_number);
      $('input[name="bank_account_balance"]').val(format_money(response.balance));
      $('.check-card .bank-name').html(response.bank_name_html);

      <?php if(!isset($is_edit)){ ?>
        $('input[name="check_number"]').val(response.check_number);
        check_number_change();
      <?php } ?>

      // if(response.check_debit == false || response.account_number == 'false'){
      //   $('.check-submit').attr('disabled', true);
      //   $('.no_mapping_setup').removeClass('hide');
      // }else{
      //   $('.check-submit').attr('disabled', false);
      //   $('.no_mapping_setup').addClass('hide');
      // }
    });
   }else{
    $('input[name="bank_account_balance"]').val(format_money(0));
   }
 });

   $('input[name="amount"]').on('change', function(){
    var amount = $(this).val();
    if(amount != '' && amount != null && amount != undefined){
     requestGetJSON(admin_url + 'accounting/get_number_to_text?amount='+amount).done(function(response) { 
      $('.money_text').html(response.text);
    });
   }
 });

   fnServerParams = {
    "vendor": '[name="rel_id"]',
    "bill": '[name="bill"]',
    "bill_ids": '[name="bill_ids"]',
  };

  $('select[name="rel_id"]').on('change', function() {
    init_bill_payment_information_table();

    var vendor_id = $(this).val();
    if(vendor_id != '' && vendor_id != null && vendor_id != undefined){
     requestGetJSON(admin_url + 'accounting/get_vendor_address/'+vendor_id).done(function(response) { 
      $('#vendor-address').html(response.html);
    });
   }else{
     $('#vendor-address').html('');
   }
  });



  if($('input[name="bank_account_id"]').val() != ''){
    $('select[name="bank_account_check"]').val($('input[name="bank_account_id"]').val());
    $('select[name="bank_account_check"]').change();
  }

  init_items_sortable(true);
  init_btn_with_tooltips();
  init_datepicker();
  init_selectpicker();
  init_form_reminder();
  init_tabs_scrollable();
  init_bill_payment_information_table();
  $('input[name="amount"]').change();
  $('select[name="bank_account"]').change();
  
  <?php if(isset($check)){ ?>
    var amount = '<?php echo new_html_entity_decode($check->amount); ?>';
    if(amount != '' && amount != null && amount != undefined){
     requestGetJSON(admin_url + 'accounting/get_number_to_text?amount='+amount).done(function(response) { 
      $('.money_text').html(response.text);
    });
   }

   var bank_account = '<?php echo new_html_entity_decode($check->bank_account); ?>';
   if(bank_account != '' && bank_account != null && bank_account != undefined){
     requestGetJSON(admin_url + 'accounting/get_bank_account_data/'+bank_account).done(function(response) { 
      $('#routing_number_span').html(response.routing_number);
      $('#account_number_span').html(response.account_number);
      $('input[name="bank_account_balance"]').val(format_money(response.balance));
    });
   }else{
    $('input[name="bank_account_balance"]').val(format_money(0));
   }

   $('#div_print_later_btn').html('<a href="'+admin_url +'accounting/check/'+<?php echo new_html_entity_decode($check->id); ?>+'" class="btn btn-default mright5 mtop5" data-toggle="tooltip" data-title="<?php echo _l('back_check_note'); ?>"><?php echo _l('acc_back'); ?></a><a href="#" class="btn btn-default mright5 mtop5" onclick="print_a_check('+<?php echo new_html_entity_decode($check->id); ?>+'); return false;" data-toggle="tooltip" data-title="<?php echo _l('save_print_now_note'); ?>"><?php echo _l('save_print_now'); ?></a><a href="#" class="btn btn-default mright5 mtop5" onclick="print_later('+<?php echo new_html_entity_decode($check->id); ?>+','+bank_account+'); return false;" data-toggle="tooltip" data-title="<?php echo _l('save_print_later_note'); ?>"><?php echo _l('save_print_later'); ?></a><a href="#" class="btn btn-default mtop5" onclick="print_multiple_check('+<?php echo new_html_entity_decode($check->id); ?>+','+bank_account+'); return false;" data-toggle="tooltip" data-title="<?php echo _l('print_multiple_saved_checks_note'); ?>"><?php echo _l('print_multiple_saved_checks'); ?></a>');
  <?php if($check->issue != 3){ ?>
   $('#div_check_btn_left').html('<a href="#" class="btn btn-default mright5 mbot5" onclick="void_check('+<?php echo new_html_entity_decode($check->id); ?>+'); return false;" data-toggle="tooltip" data-title="<?php echo _l('void_check'); ?>"><?php echo _l('void_check'); ?></a>');
  <?php }else{ ?>
   $('#div_check_btn_left').html('');
  <?php } ?>

    if (get_url_param('print_later')) {
      requestGetJSON(admin_url + 'accounting/print_later/<?php echo new_html_entity_decode($check->id); ?>/'+ bank_account).done(function (response) {
          if(response.success != false){
              alert_float('success', response.message); 
          }
      });

      var uri = window.location.toString();

      if(uri.indexOf("?")> 0){
        var clean_uri = uri.substring(uri, uri.indexOf("?"));
        window.history.replaceState({}, document.title, clean_uri);
      }
    }

    if (get_url_param('print_check')) {
      var html_success = '<iframe id="content_print" class="w100" name="content_print"></iframe>';
      var ids = [];
      var data = {};
      ids.push(<?php echo new_html_entity_decode($check->id); ?>);
      data.ids = ids;

      $.post(admin_url + 'accounting/print_a_check', data).done(function(response){ 
        response = JSON.parse(response); 
        if(navigator.userAgent.indexOf("Firefox") != -1 ){
            var mywindow = window.open('', 'Print check');
            mywindow.document.write(response.html);

            mywindow.document.close();
            mywindow.focus()
            mywindow.print();
            mywindow.close();
        }else{
            $('.content_cart').html(html_success);
            $("#content_print").contents().find('body').html(response.html);
            $("#content_print").contents().find('body').attr('style','text-align: center');
            $("#content_print").get(0).contentWindow.print();
        }
      });

      var uri = window.location.toString();

      if(uri.indexOf("?")> 0){
        var clean_uri = uri.substring(uri, uri.indexOf("?"));
        window.history.replaceState({}, document.title, clean_uri);
      }
    }

    if (get_url_param('print_multiple_check')) {
      requestGetJSON(admin_url + 'accounting/print_later/<?php echo new_html_entity_decode($check->id); ?>/'+ bank_account).done(function (response) {
          if(response.success != false){
              alert_float('success', response.message); 
          }
      });

      var uri = window.location.toString();

      if(uri.indexOf("?")> 0){
        var clean_uri = uri.substring(uri, uri.indexOf("?"));
        window.history.replaceState({}, document.title, clean_uri);
      }

      print_form();
    }
 <?php } ?>

 $('.check-submit').on('click', function(){
  var bank_account = $('select[name="bank_account"]').val();
  var rel_id = $('select[name="rel_id"]').val();
  var date = $('input[name="date"]').val();
  var amount = $('input[name="amount"]').val();
  if($('input[name="id"]').val() == ''){
    if(!bank_account.trim()){
      alert_float('warning', '<?php echo _l('please_choose_a_bank_account'); ?>');
      return false;
    }
  }
  if(!date.trim()){
    alert_float('warning', '<?php echo _l('date_field_is_required'); ?>');
    return false;
  }
  if(!rel_id.trim()){
    alert_float('warning', '<?php echo _l('pay_to_the_order_of_field_is_required'); ?>');
    return false;
  }
  if(!amount.trim()){
    alert_float('warning', '<?php echo _l('amount_field_is_required'); ?>');
    return false;
  }
 
  $('#check-form').submit();
});

  $("body").on('click', '.table-bill-payment-information .checkbox', function() {
        setTimeout(function() {
          calculate_check_total();
        }, 200);
  }); 

 $("input[data-type='currency']").on({
      keyup: function() {
        formatCurrency($(this));
      },
      blur: function() {
        formatCurrency($(this), "blur");
      }
    });

 SignaturePad.prototype.toDataURLAndRemoveBlanks = function() {
   var canvas = this._ctx.canvas;
       // First duplicate the canvas to not alter the original
       var croppedCanvas = document.createElement('canvas'),
       croppedCtx = croppedCanvas.getContext('2d');

       croppedCanvas.width = canvas.width;
       croppedCanvas.height = canvas.height;
       croppedCtx.drawImage(canvas, 0, 0);

       // Next do the actual cropping
       var w = croppedCanvas.width,
       h = croppedCanvas.height,
       pix = {
         x: [],
         y: []
       },
       imageData = croppedCtx.getImageData(0, 0, croppedCanvas.width, croppedCanvas.height),
       x, y, index;

       for (y = 0; y < h; y++) {
         for (x = 0; x < w; x++) {
           index = (y * w + x) * 4;
           if (imageData.data[index + 3] > 0) {
             pix.x.push(x);
             pix.y.push(y);

           }
         }
       }
       pix.x.sort(function(a, b) {
         return a - b
       });
       pix.y.sort(function(a, b) {
         return a - b
       });
       var n = pix.x.length - 1;

       w = pix.x[n] - pix.x[0];
       h = pix.y[n] - pix.y[0];
       var cut = croppedCtx.getImageData(pix.x[0], pix.y[0], w, h);

       croppedCanvas.width = w;
       croppedCanvas.height = h;
       croppedCtx.putImageData(cut, 0, 0);

       return croppedCanvas.toDataURL();
     };


     function signaturePadChanged() {

       var input = document.getElementById('signatureInput');
       var $signatureLabel = $('#signatureLabel');
       $signatureLabel.removeClass('text-danger');

       if (signaturePad.isEmpty()) {
         $signatureLabel.addClass('text-danger');
         input.value = '';
         return false;
       }

       $('#signatureInput-error').remove();
       var partBase64 = signaturePad.toDataURLAndRemoveBlanks();
       partBase64 = partBase64.split(',')[1];
       input.value = partBase64;
     }

     var canvas = document.getElementById("signature");
     var signaturePad = new SignaturePad(canvas, {
      maxWidth: 2,
      onEnd:function(){
        signaturePadChanged();
      }
    });

     $('#identityConfirmationForm').submit(function() {
       signaturePadChanged();
     });

     $('input[name="include_company_name_address"]').on('change', function(){
      var value = $(this).is(':checked');
      if(!value){
        $('.check-card .address').addClass('hide');
      }
      else{
        $('.check-card .address').removeClass('hide');    
      }
    });

    $('input[name="include_routing_account_numbers"]').on('change', function(){
      var value = $(this).is(':checked');
      if(!value){
        $('.check-card #routing_number_span').addClass('hide');
        $('.check-card #account_number_span').addClass('hide');
        $('.hide_routing_account_numbers').addClass('hide');
      }
      else{
        $('.check-card #routing_number_span').removeClass('hide');    
        $('.check-card #account_number_span').removeClass('hide');  
        $('.hide_routing_account_numbers').removeClass('hide');

      }
    });


    $('input[name="include_check_number"]').on('change', function(){
      var value = $(this).is(':checked');
      if(!value){
        $('.check-card .check_number_label').addClass('hide');
        $('.check-card .check_number_label_bottom').addClass('hide');
        $('.hide_check_number').addClass('hide');

      }
      else{
        $('.check-card .check_number_label').removeClass('hide');    
        $('.check-card .check_number_label_bottom').removeClass('hide'); 
        $('.hide_check_number').removeClass('hide');
           
      }
    });

    $('input[name="include_bank_name"]').on('change', function(){
      var value = $(this).is(':checked');
      if(!value){
        $('.check-card .bank-name').addClass('hide');
      }
      else{
        $('.check-card .bank-name').removeClass('hide');    
      }
    });

     $('#check_number').on('keyup', function(){
      check_number_change();
     });



   })(jQuery);

  function check_number_change() {
    "use strict";
      var value = $('input[name="check_number"]').val();
      var max_number = $('input[name="max_check_number"]').val();
      if(value.trim()){
        value = pad_left(value, max_number);
        $('.check_number_label').text(value);
        $('#check_number_span').html(value);
        $('.check_number_label_bottom').text('#'+value);
      }
      else{
       var default_value = $('input[name="number"]').val();
        default_value = pad_left(default_value, max_number);
        $('#check_number_span').html(default_value);
       $('.check_number_label').text(default_value);
       $('.check_number_label_bottom').text('#'+default_value);
      }
  }


   function init_bill_payment_information_table() {
    "use strict";
    var vendor = 0;
    var check_id = 0;
    <?php if(isset($check)){ ?>
     vendor = '<?php echo new_html_entity_decode($check->rel_id); ?>';
     check_id = '<?php echo new_html_entity_decode($check->id); ?>';
   <?php } ?>
   if ($.fn.DataTable.isDataTable('.table-bill-payment-information')) {
     $('.table-bill-payment-information').DataTable().destroy();
   }
   var _table = initDataTable('.table-bill-payment-information', admin_url + 'accounting/bill_payment_information_table/'+vendor+'/'+check_id, false, [0], fnServerParams, [1, 'desc']);
 }

 function sign_action() {
  "use strict";
  $('#add_signature').modal('show');
}

function signature_clear(){
  "use strict";
  var canvas = document.getElementById("signature");
  var signaturePad = new SignaturePad(canvas, {
    maxWidth: 2,
    onEnd:function(){

    }
  });
  signaturePad.clear();
  $('input[name="signature"]').val('');
}




function import_signature_modal(checkId){
  "use strict";

  $('#add_signature').modal('hide');
  $('#import_signature_modal').modal('show');
}



$('input[name="checkbox_signature"]').on('change', function(){
  var value = $(this).is(':checked');
  if(!value){
    $('input[name="checkbox_signature"]').prop('checked', true);
  }
  else{
    $('input[name="checkbox_signature_available"]').prop('checked', false);
    $('.div_signature').removeClass('hide');    
    $('.div_signature_available').addClass('hide');
  }
});

$('input[name="checkbox_signature_available"]').on('change', function(){
  var value = $(this).is(':checked');
  if(!value){
    $('input[name="checkbox_signature_available"]').prop('checked', true);
  }
  else{
    $('input[name="checkbox_signature"]').prop('checked', false);
    $('.div_signature').addClass('hide');    
    $('.div_signature_available').removeClass('hide');
  }
});

function pad_left(str, max) {
  "use strict";
  str = str.toString();
  return str.length < max ? pad_left("0" + str, max) : str;
}


function calculate_check_total(){
    "use strict";
    var total_amount = 0;
    var rows = $('.table-bill-payment-information').find('tbody tr');
    $.each(rows, function() {
        var checkbox = $($(this).find('td').eq(0)).find('input');
        if (checkbox.is(":checked") == true) {
            total_amount = total_amount + parseFloat(checkbox.data('amount'));
        }
    });
    $('input[name="amount"]').val(total_amount.toFixed(2));
    formatCurrency($('input[name="amount"]'));
    $('input[name="amount"]').change();
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


function check_form_handler(form) {
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
            alert_float('success', response.message);

            if(_print_a_check != 0){
              var html_success = '<iframe id="content_print" class="w100" name="content_print"></iframe>';
              var ids = [];
              var data = {};
              ids.push(_print_a_check);
              data.ids = ids;

              $.post(admin_url + 'accounting/print_a_check', data).done(function(response){ 
                response = JSON.parse(response); 
                  if(navigator.userAgent.indexOf("Firefox") != -1 ){
                      var mywindow = window.open('', 'Print checks');
                      mywindow.document.write(response.html);

                      mywindow.document.close();
                      mywindow.focus()
                      mywindow.print();
                      mywindow.close();
                  }else{
                      $('.content_cart').html(html_success);
                      $("#content_print").contents().find('body').html(response.html);
                      $("#content_print").contents().find('body').attr('style','text-align: center');
                      $("#content_print").get(0).contentWindow.print();
                  }
              });
              _print_a_check = 0;
            }else if(_print_multiple_check != 0){
              print_form();
              _print_multiple_check = 0;
            }

            if(response.href != ''){
              window.location.href = response.href;
            }
        }else{
          if(response.book_closed){
            alert_float('warning', response.message);
          }else{
            alert_float('danger', response.message);
          }
        }
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.message));
    });

    return false;
}

function print_later(billid, account) {
    "use strict";

    $('#check-form').submit();

    requestGetJSON(admin_url + 'accounting/print_later/' + billid+'/'+ account).done(function (response) {
    });
}

function print_a_check(id){
  "use strict";
  _print_a_check = id;

  $('#check-form').submit();
}

function print_multiple_check(billid, account){
  "use strict";
  _print_multiple_check = 1;

  print_later(billid, account);
}

function sign_check(id){
  "use strict";
  if(id != 0){
    var data = {};
    data.signature = $('input[name="signature"]').val();
    data.checkbox_signature = $('input[name="checkbox_signature"]').is(':checked');
    data.checkbox_signature_available = $('input[name="checkbox_signature_available"]').is(':checked');
    data.signature_available_id = $('[name="signature_available_id"]:checked').val();

    $.post(admin_url + 'accounting/sign_check_ajax/' + id, data).done(function(response){
      response = JSON.parse(response); 
      if (response.success === true || response.success == 'true') {
        alert_float('success', response.message);
        window.location.href = admin_url + 'accounting/checks/'+id;
      }
    });
  }else{
    $("#add_signature").modal("hide");
    if($('input[name="signature"]').val() != '' && $('input[name="checkbox_signature"]').is(':checked') == true){
      var image = '<img src="data:image/png;base64,'+$('input[name="signature"]').val()+'" class="img_style">';
      $('.check-sign').html(image);
      $('.check-sign').append('<a href="#" onclick="sign_action();" class="btn btn-success pull-right mbot5"><?php echo _l('e_signature_sign'); ?></a>');
      alert_float('success', "<?php echo _l('signed'); ?>");
    }else if($('input[name="checkbox_signature_available"]').is(':checked') == true){
      var image = '<img src="'+$('.img-signature-available-'+$('[name="signature_available_id"]:checked').val()).attr('src')+'" class="img_style">';
      $('.check-sign').html(image);
      $('.check-sign').append('<a href="#" onclick="sign_action();" class="btn btn-success pull-right mbot5"><?php echo _l('e_signature_sign'); ?></a>');
      alert_float('success', "<?php echo _l('signed'); ?>");
    }else{
      $('.check-sign').html('<a href="#" onclick="sign_action();" class="btn btn-success pull-right mbot5"><?php echo _l('e_signature_sign'); ?></a>');
    }

  }
}

function save_a_check() {
    "use strict";
    var bank_account = $('select[name="bank_account"]').val();
  var rel_id = $('select[name="rel_id"]').val();
  var date = $('input[name="date"]').val();
  var amount = $('input[name="amount"]').val();
  if($('input[name="id"]').val() == ''){
    if(!bank_account.trim()){
      alert_float('warning', '<?php echo _l('please_choose_a_bank_account'); ?>');
      return false;
    }
  }
  if(!date.trim()){
    alert_float('warning', '<?php echo _l('date_field_is_required'); ?>');
    return false;
  }
  if(!rel_id.trim()){
    alert_float('warning', '<?php echo _l('pay_to_the_order_of_field_is_required'); ?>');
    return false;
  }
  if(!amount.trim()){
    alert_float('warning', '<?php echo _l('amount_field_is_required'); ?>');
    return false;
  }
  
    $('#check-form').submit();
}

function save_and_print_later() {
    "use strict";
    var bank_account = $('select[name="bank_account"]').val();
  var rel_id = $('select[name="rel_id"]').val();
  var date = $('input[name="date"]').val();
  var amount = $('input[name="amount"]').val();
  if($('input[name="id"]').val() == ''){
    if(!bank_account.trim()){
      alert_float('warning', '<?php echo _l('please_choose_a_bank_account'); ?>');
      return false;
    }
  }
  if(!date.trim()){
    alert_float('warning', '<?php echo _l('date_field_is_required'); ?>');
    return false;
  }
  if(!rel_id.trim()){
    alert_float('warning', '<?php echo _l('pay_to_the_order_of_field_is_required'); ?>');
    return false;
  }
  if(!amount.trim()){
    alert_float('warning', '<?php echo _l('amount_field_is_required'); ?>');
    return false;
  }

    $('.additional').html(hidden_input('save_and_print_later', 'true'));
    $('#check-form').submit();
}

function save_and_print_a_check(){
  "use strict";
  var bank_account = $('select[name="bank_account"]').val();
  var rel_id = $('select[name="rel_id"]').val();
  var date = $('input[name="date"]').val();
  var amount = $('input[name="amount"]').val();
  if($('input[name="id"]').val() == ''){
    if(!bank_account.trim()){
      alert_float('warning', '<?php echo _l('please_choose_a_bank_account'); ?>');
      return false;
    }
  }
  if(!date.trim()){
    alert_float('warning', '<?php echo _l('date_field_is_required'); ?>');
    return false;
  }
  if(!rel_id.trim()){
    alert_float('warning', '<?php echo _l('pay_to_the_order_of_field_is_required'); ?>');
    return false;
  }
  if(!amount.trim()){
    alert_float('warning', '<?php echo _l('amount_field_is_required'); ?>');
    return false;
  }

  $('.additional').html(hidden_input('save_and_print_a_check', 'true'));
  $('#check-form').submit();
}

function save_and_print_multiple_check(){
  "use strict";
  var bank_account = $('select[name="bank_account"]').val();
  var rel_id = $('select[name="rel_id"]').val();
  var date = $('input[name="date"]').val();
  var amount = $('input[name="amount"]').val();
  if($('input[name="id"]').val() == ''){
    if(!bank_account.trim()){
      alert_float('warning', '<?php echo _l('please_choose_a_bank_account'); ?>');
      return false;
    }
  }
  if(!date.trim()){
    alert_float('warning', '<?php echo _l('date_field_is_required'); ?>');
    return false;
  }
  if(!rel_id.trim()){
    alert_float('warning', '<?php echo _l('pay_to_the_order_of_field_is_required'); ?>');
    return false;
  }
  if(!amount.trim()){
    alert_float('warning', '<?php echo _l('amount_field_is_required'); ?>');
    return false;
  }

  $('.additional').html(hidden_input('save_and_print_multiple_check', 'true'));
  $('#check-form').submit();
}

function open_config(){
  "use strict";

  if($('#config_div').hasClass('hide')){
    $('#config_div').removeClass('hide');
    $('#i-angel').removeClass('fa-angle-left');
    $('#i-angel').addClass('fa-angle-down');

  }else{
    $('#config_div').addClass('hide');
    $('#i-angel').addClass('fa-angle-left');
    $('#i-angel').removeClass('fa-angle-down');
  }
}
</script>