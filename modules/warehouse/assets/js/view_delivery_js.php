<script>

var signaturePad;
var croppedCtx;

   (function($) {
    "use strict";

  var data_send_mail = {};
  <?php if(isset($send_mail_approve)){ 
    ?>
    data_send_mail = <?php echo json_encode($send_mail_approve); ?>;
    data_send_mail.rel_id = <?php echo new_html_entity_decode($goods_delivery->id); ?>;
    data_send_mail.rel_type = '2';
   
    data_send_mail.addedfrom = <?php echo new_html_entity_decode($goods_delivery->addedfrom); ?>;

    $.get(admin_url+'warehouse/send_mail', data_send_mail).done(function(response){
      response = JSON.parse(response);

    }).fail(function(error) {

    });
  <?php } ?>


   SignaturePad.prototype.toDataURLAndRemoveBlanks = function() {
     var canvas = this._ctx.canvas;
       // First duplicate the canvas to not alter the original
       var croppedCanvas = document.createElement('canvas');
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

    var canvas = document.getElementById("signature");
      signaturePad = new SignaturePad(canvas, {
        maxWidth: 2,
        onEnd:function(){
          signaturePadChanged();
        }
      });

    $('#identityConfirmationForm').submit(function() {
       signaturePadChanged();
     });


    $('select[name="customer_name"]').on('change',function(){

      var data_select = {};
        data_select.userid = $('select[name="customer_name"]').val();

          $.post(admin_url + 'warehouse/get_primary_contact',data_select).done(function(response){
             response = JSON.parse(response);
             $('input[name="email"]').val(response.primary_email);
           });

    });


})(jQuery);

     function signaturePadChanged() {
      "use strict";

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
  
  function sign_request(id){
    "use strict";
    var signature_val = $('input[name="signature"]').val();
    if(signature_val.length > 0){
      change_request_approval_status(id,1, true);
      $('.sign_request_class').prop('disabled', true);
      $('.sign_request_class').html('<?php echo _l('wait_text'); ?>');
      $('.clear').prop('disabled', true);
    }else{
      alert_float('warning', '<?php echo _l('please_sign_the_form'); ?>');
      $('.sign_request_class').prop('disabled', false);
      $('.clear').prop('disabled', false);
    }
  }

  function approve_request(id){
    "use strict";
    change_request_approval_status(id,1);
  }
  function deny_request(id){
    "use strict";
      change_request_approval_status(id,-1);
  }

  function change_request_approval_status(id, status, sign_code){
    "use strict";
   
      var data = {};
      data.rel_id = id;
      data.rel_type = '2';
     
      data.approve = status;

      if(status == 1){
        $.post(admin_url + 'warehouse/check_approval_sign', data).done(function(response){
          response = JSON.parse(response); 
            if (response.success === true || response.success == 'true') {

                      if(sign_code == true){
                data.signature = $('input[name="signature"]').val();
              }else{
                data.note = $('textarea[name="reason"]').val();
              }
              $.post(admin_url + 'warehouse/approve_request/' + id, data).done(function(response){
                  response = JSON.parse(response); 
                  if (response.success === true || response.success == 'true') {
                      alert_float('success', response.message);
                      window.location.reload();
                  }
              });
            }else{
              //check approval false
              alert_float('success', response.message);
            }

        });

      }else{
        if(sign_code == true){
          data.signature = $('input[name="signature"]').val();
        }else{
          data.note = $('textarea[name="reason"]').val();
        }
        $.post(admin_url + 'warehouse/approve_request/' + id, data).done(function(response){
            response = JSON.parse(response); 
            if (response.success === true || response.success == 'true') {
                alert_float('success', response.message);
                window.location.reload();
            }
        });
      }
      
  }

  function send_request_approve(id){
    "use strict";

    var data = {};
    data.rel_id = <?php echo new_html_entity_decode($goods_delivery->id); ?>;
    data.rel_type = '2';
    
    data.addedfrom = <?php echo new_html_entity_decode($goods_delivery->addedfrom); ?>;
    $('.send_request_approve_class').prop('disabled', true);
    $('.send_request_approve_class').html('<?php echo _l('wait_text'); ?>');

  $("body").append('<div class="dt-loader"></div>');
    $.post(admin_url + 'warehouse/send_request_approve', data).done(function(response){
        response = JSON.parse(response);
        $("body").find('.dt-loader').remove();
        if (response.success === true || response.success == 'true') {
            alert_float('success', response.message);
            window.location.reload();
        }else{
          alert_float('warning', response.message);
          $('.send_request_approve_class').prop('disabled', false);
          $('.send_request_approve_class').html('<?php echo _l('send_request_approve'); ?>');

        }
    });
}

function accept_action() {
  "use strict";
      $('#add_action').modal('show');
}

 function get_goods_delivery_ajax(id, invoice_id) {
  "use strict"; 
    var data={};
    data.id =  id;
     $.post(admin_url + 'warehouse/get_delivery_ajax', data).done(function(response){
          response = JSON.parse(response);

          $("select[name='customer_name']").html('');
          $("select[name='customer_name']").append(response.options);
          $('input[name="email"]').val(response.primary_email);

          $("select[name='customer_name']").selectpicker('refresh');
      });

  $('#additional_goods_delivery').html('');
  $('#additional_goods_delivery').append(hidden_input('goods_delivery',id));
  
  $('#goods_delivery_invoice_id').html('');
  $('#goods_delivery_invoice_id').append(hidden_input('invoice_id',invoice_id));

  $('#send_goods_delivery').modal('show');
 }

  // Manually add goods delivery activity
    $("#wh_enter_activity").on('click', function() {
      "use strict"; 

        var message = $('#wh_activity_textarea').val();
        var goods_delivery_id = $('input[name="_attachment_sale_id"]').val();

        if (message === '') { return; }
        $.post(admin_url + 'warehouse/wh_add_activity', {
            goods_delivery_id: goods_delivery_id,
            activity: message,
            rel_type: 'delivery',
        }).done(function(response) {
            response = JSON.parse(response);
            if(response.status == true){
              alert_float('success', response.message);
              init_goods_delivery(goods_delivery_id)
            }else{
              alert_float('danger', response.message);
            }
        }).fail(function(data) {
            alert_float('danger', data.message);
        });
    });

function delete_wh_activitylog(wrapper, id) {
      "use strict"; 

    if (confirm_delete()) {
        requestGetJSON('warehouse/delete_activitylog/' + id).done(function(response) {
            if (response.success === true || response.success == 'true') { $(wrapper).parents('.feed-item').remove(); }
        }).fail(function(data) {
            alert_float('danger', data.responseText);
        });
    }
}




</script>

