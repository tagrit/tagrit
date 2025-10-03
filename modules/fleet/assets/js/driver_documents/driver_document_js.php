<script>
Dropzone.options.expenseForm = false;
var expenseDropzone;

(function($) {
  "use strict";


  if($('#dropzoneDragArea').length > 0){
      expenseDropzone = new Dropzone("#expense-form", appCreateDropzoneOptions({
        autoProcessQueue: false,
        clickable: '#dropzoneDragArea',
        previewsContainer: '.dropzone-previews',
        addRemoveLinks: true,
        maxFiles: 10,
        success:function(file,response){
         response = JSON.parse(response);
         if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
           window.location.assign(response.url);
         }
       },
     }));
  }

  appValidateForm($('#expense-form'),{
    subject:'required',
  },driverDocumentSubmitHandler);

})(jQuery);

    
 function driverDocumentSubmitHandler(form){
  $.post(form.action, $(form).serialize()).done(function(response) {
    response = JSON.parse(response);
    if (response.id) {
      if(typeof(expenseDropzone) !== 'undefined'){
        if (expenseDropzone.getQueuedFiles().length > 0) {
          expenseDropzone.options.url = admin_url + 'fleet/add_driver_document_attachment/' + response.id+'?driver_id='+response.driver_id+'&vehicle_id='+response.vehicle_id;
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

</script>
