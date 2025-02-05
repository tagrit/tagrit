<script>
var pur_order_id = '<?php echo pur_html_entity_decode($estimate->id); ?>';
(function($) {
  "use strict"; 
   var data_send_mail = {};
  <?php if(isset($send_mail_approve)){ 
    ?>
    data_send_mail = <?php echo json_encode($send_mail_approve); ?>;
    data_send_mail.rel_id = <?php echo pur_html_entity_decode($estimate->id); ?>;
    data_send_mail.rel_type = 'pur_order';
    data_send_mail.addedfrom = <?php echo pur_html_entity_decode($estimate->addedfrom); ?>;
    $.post(admin_url+'purchase/send_mail', data_send_mail).done(function(response){
    });
  <?php } ?>

  init_items_sortable(true);
   init_btn_with_tooltips();
   init_datepicker();
   init_selectpicker();
   init_form_reminder();
   init_tabs_scrollable();
   $("input[data-type='currency']").on({
    keyup: function() {        
      formatCurrency($(this));
    },
    blur: function() { 
      formatCurrency($(this), "blur");
    }
});

  get_contract_comments();
})(jQuery);

 function send_po(id) {
  "use strict"; 
  $('#additional_po').html('');
  $('#additional_po').append(hidden_input('po_id',id));
  $('#send_po').modal('show');
 }

function add_payment(id){
  "use strict"; 
   appValidateForm($('#purorder-add_payment-form'),{amount:'required', date:'required'});
   $('#payment_record_pur').modal('show');
   $('.edit-title').addClass('hide');
   $('#additional').html('');
}

function add_payment_with_inv(id){
  "use strict"; 
  appValidateForm($('#purorder-add_payment_with_inv-form'),{pur_invoice:'required', amount:'required', date:'required'});
  $('#payment_record_pur_with_inv').modal('show');
  $('#inv_additional').html('');
}


function pur_inv_payment_change(el){
  "use strict"; 
  var invoice = $(el).val();
  if(invoice != '' ){
    $.post(admin_url+'purchase/pur_inv_payment_change/'+invoice).done(function(reponse){
      reponse = JSON.parse(reponse);
      $('#payment_record_pur_with_inv input[name="amount"]').val(reponse.amount);
      $('#payment_record_pur_with_inv input[name="amount"]').attr('max', reponse.amount);
    });
  }else{
    $('#payment_record_pur_with_inv input[name="amount"]').val(0);
    $('#payment_record_pur_with_inv input[name="amount"]').attr('max', 0);

    alert_float('warning', '<?php echo _l('please_select_purchase_invoice'); ?>');
  }
}

   
function change_status_pur_order(invoker,id){
  "use strict"; 
   $.post(admin_url+'purchase/change_status_pur_order/'+invoker.value+'/'+id).done(function(reponse){
    reponse = JSON.parse(reponse);
    window.location.href = admin_url + 'purchase/purchase_order/'+id;
    alert_float('success',reponse.result);
  });
}

//preview purchase order attachment
function preview_purorder_btn(invoker){
  "use strict"; 
    var id = $(invoker).attr('id');
    var rel_id = $(invoker).attr('rel_id');
    view_purorder_file(id, rel_id);
}

function view_purorder_file(id, rel_id) {
  "use strict"; 
      $('#purorder_file_data').empty();
      $("#purorder_file_data").load(admin_url + 'purchase/file_purorder/' + id + '/' + rel_id, function(response, status, xhr) {
          if (status == "error") {
              alert_float('danger', xhr.statusText);
          }
      });
}
function close_modal_preview(){
  "use strict"; 
 $('._project_file').modal('hide');
}

function delete_purorder_attachment(id) {
  "use strict"; 
    if (confirm_delete()) {
        requestGet('purchase/delete_purorder_attachment/' + id).done(function(success) {
            if (success == 1) {
                $("#purorder_pv_file").find('[data-attachment-id="' + id + '"]').remove();
            }
        }).fail(function(error) {
            alert_float('danger', error.responseText);
        });
    }
  }

  
function formatNumber(n) {
  "use strict"; 
  // format number 1000000 to 1,234,567
  return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
}
function formatCurrency(input, blur) {
  "use strict"; 
  var input_val = input.val();
  if (input_val === "") { return; }
  var original_len = input_val.length;
  var caret_pos = input.prop("selectionStart");
  if (input_val.indexOf(".") >= 0) {
    var decimal_pos = input_val.indexOf(".");
    var left_side = input_val.substring(0, decimal_pos);
    var right_side = input_val.substring(decimal_pos);
    left_side = formatNumber(left_side);
    right_side = formatNumber(right_side);
    right_side = right_side.substring(0, 2);
    input_val = left_side + "." + right_side;

  } else {
    input_val = formatNumber(input_val);
    input_val = input_val;

  }
  input.val(input_val);
  var updated_len = input_val.length;
  caret_pos = updated_len - original_len + caret_pos;
  input[0].setSelectionRange(caret_pos, caret_pos);
}

function copy_public_link(pur_order){
  "use strict";
  var link = $('#link_public').val();
  if(link != ''){
    var copyText = document.getElementById("link_public");
    copyText.select();
    copyText.setSelectionRange(0, 99999)
    document.execCommand("copy");
    alert_float('success','Copied!');

  }else{
    $.post(admin_url+'purchase/copy_public_link/'+pur_order).done(function(reponse){
      reponse = JSON.parse(reponse);
      if(reponse.copylink != ''){
        $('#link_public').val(reponse.copylink);
        
      }

      if($('#link_public').val() != ''){
          var copyText = document.getElementById("link_public");
          copyText.select();
          copyText.setSelectionRange(0, 99999)
          document.execCommand("copy");
          alert_float('success','Created!');
        }
    });
  }
}

function send_request_approve(id){
  "use strict";
    var data = {};
    data.rel_id = <?php echo pur_html_entity_decode($estimate->id); ?>;
    data.rel_type = 'pur_order';
    data.addedfrom = <?php echo pur_html_entity_decode($estimate->addedfrom); ?>;
  $("body").append('<div class="dt-loader"></div>');
    $.post(admin_url + 'purchase/send_request_approve', data).done(function(response){
        response = JSON.parse(response);
        $("body").find('.dt-loader').remove();
        if (response.success === true || response.success == 'true') {
            alert_float('success', response.message);
            window.location.reload();
        }else{
          alert_float('warning', response.message);
            window.location.reload();
        }
    });
}
$(function(){
  "use strict";
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
});

function signature_clear(){
"use strict";
var canvas = document.getElementById("signature");
var signaturePad = new SignaturePad(canvas, {
  maxWidth: 2,
  onEnd:function(){

  }
});
signaturePad.clear();

}
function sign_request(id){
  "use strict";
    change_request_approval_status(id,2, true);
}
function approve_request(id){
  "use strict";
  change_request_approval_status(id,2);
}
function deny_request(id){
  "use strict";
    change_request_approval_status(id,3);
}
function change_request_approval_status(id, status, sign_code){
  "use strict";
    var data = {};
    data.rel_id = id;
    data.rel_type = 'pur_order';
    data.approve = status;
    if(sign_code == true){
      data.signature = $('input[name="signature"]').val();
    }else{
      data.note = $('textarea[name="reason"]').val();
    }
    $.post(admin_url + 'purchase/approve_request/' + id, data).done(function(response){
        response = JSON.parse(response); 
        if (response.success === true || response.success == 'true') {
            alert_float('success', response.message);
            window.location.reload();
        }
    });
}
function accept_action() {
  "use strict";
  $('#add_action').modal('show');
}

function convert_to_purchase_inv(pur_order){
  "use strict";
  $.post(admin_url + 'purchase/convert_po_payment/' + pur_order).done(function(response){
      response = JSON.parse(response);
      if(response.success == true){
        alert_float('success', response.mess);
      }else{
        alert_float('warning', response.mess);
      }  
  });
}

function add_contract_comment() {
  "use strict";
    var comment = $('#comment').val();
    if (comment == '') {
       return;
    }
    var data = {};
    data.content = comment;
    data.rel_id = pur_order_id;
    data.rel_type = 'pur_order';
    $('body').append('<div class="dt-loader"></div>');
    $.post(admin_url + 'purchase/add_comment', data).done(function (response) {
       response = JSON.parse(response);
       $('body').find('.dt-loader').remove();
       if (response.success == true) {
          $('#comment').val('');
          get_contract_comments();
       }
    });
   }

   function get_contract_comments() {
    "use strict";
    if (typeof (pur_order_id) == 'undefined') {
       return;
    }
    requestGet('purchase/get_comments/' + pur_order_id+'/pur_order').done(function (response) {
       $('#contract-comments').html(response);
       var totalComments = $('[data-commentid]').length;
       var commentsIndicator = $('.comments-indicator');
       if(totalComments == 0) {
            commentsIndicator.addClass('hide');
       } else {
         commentsIndicator.removeClass('hide');
         commentsIndicator.text(totalComments);
       }
    });
   }

   function remove_contract_comment(commentid) {
    "use strict";
    if (confirm_delete()) {
       requestGetJSON('purchase/remove_comment/' + commentid).done(function (response) {
          if (response.success == true) {

            var totalComments = $('[data-commentid]').length;

             $('[data-commentid="' + commentid + '"]').remove();

             var commentsIndicator = $('.comments-indicator');
             if(totalComments-1 == 0) {
               commentsIndicator.addClass('hide');
            } else {
               commentsIndicator.removeClass('hide');
               commentsIndicator.text(totalComments-1);
            }
          }
       });
    }
   }

   function edit_contract_comment(id) {
    "use strict";
    var content = $('body').find('[data-contract-comment-edit-textarea="' + id + '"] textarea').val();
    if (content != '') {
       $.post(admin_url + 'purchase/edit_comment/' + id, {
          content: content
       }).done(function (response) {
          response = JSON.parse(response);
          if (response.success == true) {
             alert_float('success', response.message);
             $('body').find('[data-contract-comment="' + id + '"]').html(nl2br(content));
          }
       });
       toggle_contract_comment_edit(id);
    }
   }

   function toggle_contract_comment_edit(id) {
    "use strict";
       $('body').find('[data-contract-comment="' + id + '"]').toggleClass('hide');
       $('body').find('[data-contract-comment-edit-textarea="' + id + '"]').toggleClass('hide');
   }

   function routing_init_editor(selector, settings) {

        "use strict";

      tinymce.remove(selector);

    selector = typeof(selector) == 'undefined' ? '.tinymce' : selector;
    var _editor_selector_check = $(selector);

    if (_editor_selector_check.length === 0) { return; }

    $.each(_editor_selector_check, function() {
      if ($(this).hasClass('tinymce-manual')) {
        $(this).removeClass('tinymce');
      }
    });

    // Original settings
    var _settings = {
      branding: false,
      selector: selector,
      browser_spellcheck: true,
      height: 400,
      theme: 'modern',
      skin: 'perfex',
      language: app.tinymce_lang,
      relative_urls: false,
      inline_styles: true,
      verify_html: false,
      cleanup: false,
      autoresize_bottom_margin: 25,
      valid_elements: '+*[*]',
      valid_children: "+body[style], +style[type]",
      apply_source_formatting: false,
      remove_script_host: false,
      removed_menuitems: 'newdocument restoredraft',
      forced_root_block: false,
      autosave_restore_when_empty: false,
      fontsize_formats: '8pt 10pt 12pt 14pt 18pt 24pt 36pt',
      setup: function(ed) {
            // Default fontsize is 12
            ed.on('init', function() {
              this.getDoc().body.style.fontSize = '12pt';
            });
        },
        table_default_styles: {
            // Default all tables width 100%
            width: '100%',
        },
        plugins: [
        'advlist autoresize autosave lists link image print hr codesample',
        'visualblocks code fullscreen',
        'media save table contextmenu',
        'paste textcolor colorpicker'
        ],
        toolbar1: 'fontselect fontsizeselect | forecolor backcolor | bold italic | alignleft aligncenter alignright alignjustify | image link | bullist numlist | restoredraft',
        file_browser_callback: elFinderBrowser,
    };

    // Add the rtl to the settings if is true
    isRTL == 'true' ? _settings.directionality = 'rtl' : '';
    isRTL == 'true' ? _settings.plugins[0] += ' directionality' : '';

    // Possible settings passed to be overwrited or added
    if (typeof(settings) != 'undefined') {
      for (var key in settings) {
        if (key != 'append_plugins') {
          _settings[key] = settings[key];
        } else {
          _settings['plugins'].push(settings[key]);
        }
      }
    }

    // Init the editor
    var editor = tinymce.init(_settings);
    $(document).trigger('app.editor.initialized');

    return editor;
}


function refresh_order_value(po_id){
  "use strict";
  var r = confirm("<?php echo _l('refresh_order_value_confirm'); ?>");
  if(r == true && po_id != ''){
    $.post(admin_url + 'purchase/refresh_order_value/'+ po_id).done(function (response) { 
      response = JSON.parse(response);
      window.location.reload();
      if(response.success == true){
        alert_float('success', '<?php echo _l('refresh_successfully'); ?>');
      }else{
        if(response.success == false){
          alert_float('warning', '<?php echo _l('refresh_fail'); ?>');
        }else{
          alert_float('warning', '<?php echo _l('refresh_fail'); ?>' + ' '+ response.success);
        }
      }
    });
  }
}
</script>