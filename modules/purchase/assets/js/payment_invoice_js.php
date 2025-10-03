<script>


var data_send_mail = {};
  <?php if(isset($send_mail_approve)){ 
    ?>
    data_send_mail = <?php echo json_encode($send_mail_approve); ?>;
    data_send_mail.rel_id = <?php echo pur_html_entity_decode($payment_invoice->id); ?>;
    data_send_mail.rel_type = 'payment_request';
    data_send_mail.addedfrom = <?php echo pur_html_entity_decode($payment_invoice->requester); ?>;
    data_send_mail.csrf_token_name = $('input[id="csrf_token_name"]').val();
    $.post(admin_url+'purchase/send_mail', data_send_mail).done(function(response){
    });
  <?php } ?>
function send_request_approve(id){
  "use strict"; 
    var data = {};
    data.rel_id = <?php echo pur_html_entity_decode($payment_invoice->id); ?>;
    data.rel_type = 'payment_request';
    data.addedfrom = <?php echo pur_html_entity_decode($payment_invoice->requester); ?>;
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
        //signaturePadChanged();
      }
    });
    signaturePad.clear();
    //signaturePadChanged();
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
function change_request_approval_status(id, status, sign_code = false){
    var data = {};
    data.rel_id = id;
    data.rel_type = 'payment_request';
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
</script> 