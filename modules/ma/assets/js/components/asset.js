
Dropzone.options.expenseForm = false;
var expenseDropzone;
var is_edit = $('input[name="is_edit"]').val();

(function($) {
  "use strict";

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
    name:'required',
    category:'required',
  },expenseSubmitHandler);
})(jQuery);

    
 function expenseSubmitHandler(form){
    "use strict";
  if(is_edit === true || is_edit === 'true'){
    $.post(form.action, $(form).serialize()).done(function(response) {
        response = JSON.parse(response);
       
        if(response.message){
          alert_float('warning',response.message);
        }

        window.location.reload();
      });
  }else{
    if(typeof(expenseDropzone) !== 'undefined' || is_edit === true || is_edit === 'true'){
      if (expenseDropzone.getQueuedFiles().length > 0 || is_edit === true || is_edit === 'true') {
        $.post(form.action, $(form).serialize()).done(function(response) {
          response = JSON.parse(response);
          if (response.asset_id) {
            expenseDropzone.options.url = admin_url + 'ma/add_asset_attachment/' + response.asset_id;
            expenseDropzone.processQueue();
          } else {
            if(response.message){
              alert_float('warning',response.message);
            }

            if(response.url){
              window.location.assign(response.url);
            }
          }
        });
      } else {
        alert_float('warning','Please upload files');
      }
    } else {
      alert_float('warning','Please upload files');
    }
  }
  return false;
}

