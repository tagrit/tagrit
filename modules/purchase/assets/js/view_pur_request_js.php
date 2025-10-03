<script>
(function($) {
"use strict"; 
var data_send_mail = {};
  <?php if(isset($send_mail_approve)){ 
    ?>
    data_send_mail = <?php echo json_encode($send_mail_approve); ?>;
    data_send_mail.rel_id = <?php echo pur_html_entity_decode($pur_request->id); ?>;
    data_send_mail.rel_type = 'pur_request';
    data_send_mail.addedfrom = <?php echo pur_html_entity_decode($pur_request->requester); ?>;
    data_send_mail.csrf_token_name = $('input[id="csrf_token_name"]').val();
    $.post(admin_url+'purchase/send_mail', data_send_mail).done(function(response){
    });
  <?php } ?>



})(jQuery); 


function change_status_pur_request(invoker,id){
  "use strict"; 
  $.post(admin_url+'purchase/change_status_pur_request/'+invoker.value+'/'+id).done(function(reponse){
    reponse = JSON.parse(reponse);
    window.location.href = admin_url + 'purchase/view_pur_request/'+id;
    alert_float('success',reponse.result);
  });
}

function send_request_approve(id){
  "use strict"; 
    var data = {};
    data.rel_id = <?php echo pur_html_entity_decode($pur_request->id); ?>;
    data.rel_type = 'pur_request';
    data.addedfrom = <?php echo pur_html_entity_decode($pur_request->requester); ?>;
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
    data.rel_type = 'pur_request';
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
    $.post(admin_url+'purchase/copy_public_link_pur_request/'+pur_order).done(function(reponse){
      reponse = JSON.parse(reponse);
      if(reponse.copylink != ''){
        $('#link_public').val(reponse.copylink);
        
      }

      if($('#link_public').val() == reponse.copylink){
          var copyText = document.getElementById("link_public");
          copyText.select();
          copyText.setSelectionRange(0, 99999)
          document.execCommand("copy");
          alert_float('success','Created!');
        }
    });
  }
}  

// Mark task status
function purchase_request_mark_as(status, pur_request_id) {
    var url = 'purchase/change_status_pur_request/' + status + '/' + pur_request_id;

    $("body").append('<div class="dt-loader"></div>');
    requestGetJSON(url).done(function (response) {
        $("body").find('.dt-loader').remove();
        if (response.result != '') {
            alert_float('success',response.result);
            window.location.reload();
        }
    });
}

if(typeof(commonTaskPopoverMenuOptions) == 'undefined') {
      var commonTaskPopoverMenuOptions = {
         html: true,
         placement: 'bottom',
         trigger: 'click',
         template: '<div class="popover"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"></div></div></div>',
      };
   }

if(typeof(taskPopoverMenus) == 'undefined'){
    var taskPopoverMenus = [{
          selector: '.task-menu-options',
          title: "<?php echo _l('actions'); ?>",
       },
       {
          selector: '.task-menu-status',
          title: "<?php echo _l('ticket_single_change_status'); ?>",
       },
       {
          selector: '.task-menu-priority',
          title: "<?php echo _l('task_single_priority'); ?>",
       },
       {
          selector: '.task-menu-milestones',
          title: "<?php echo _l('task_milestone'); ?>",
       },
    ];
 }

 for (var i = 0; i < taskPopoverMenus.length; i++) {
    $(taskPopoverMenus[i].selector + ' .trigger').popover($.extend({}, commonTaskPopoverMenuOptions, {
       title: taskPopoverMenus[i].title,
       content: $('body').find(taskPopoverMenus[i].selector + ' .content-menu').html()
    }));
 }


function preview_purrequest_btn(invoker){
  "use strict"; 
    var id = $(invoker).attr('id');
    var rel_id = $(invoker).attr('rel_id');
    view_purrequest_file(id, rel_id);
}

function view_purrequest_file(id, rel_id) {
  "use strict"; 
      $('#purrequest_file_data').empty();
      $("#purrequest_file_data").load(admin_url + 'purchase/file_purrequest/' + id + '/' + rel_id, function(response, status, xhr) {
          if (status == "error") {
              alert_float('danger', xhr.statusText);
          }
      });
}
function close_modal_preview(){
  "use strict"; 
 $('._project_file').modal('hide');
}

function delete_purrequest_attachment(id) {
  "use strict"; 
    if (confirm_delete()) {
        requestGetJSON('purchase/delete_purrequest_attachment/' + id).done(function(response) {
            if (response.success == true || response.success == 'true') {
                $("#purrequest_pv_file").find('[data-attachment-id="' + id + '"]').remove();
            }
        }).fail(function(error) {
            alert_float('danger', error.responseText);
        });
    }
  }
</script>