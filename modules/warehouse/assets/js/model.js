  (function($){
"use strict";
  
  appValidateForm($('#models_setting'), {
     name: 'required',
     brand_id: 'required',
   }); 

})(jQuery);

  function new_model(){
    "use strict";

    $('#model').modal('show');
    $('.edit-title').addClass('hide');
    $('.add-title').removeClass('hide');
    $('#model_id_t').html('');

    $('#models_setting input[name="name"]').val('');
    $('#models_setting select[name="brand_id"]').val('').change();
       
  }

  function edit_model(invoker,id){
      "use strict";
      
      $('#model').modal('show');
      $('.edit-title').removeClass('hide');
      $('.add-title').addClass('hide');

      $('#model_id_t').html('');
      $('#model_id_t').append(hidden_input('id',id));

      $('#models_setting input[name="name"]').val($(invoker).data('name'));
      $('#models_setting select[name="brand_id"]').val($(invoker).data('brand_id')).change();
   
       
  }