  
  appValidateForm($('#series_setting'), {
     name: 'required',
     model_id: 'required',
   }); 

  function new_series(){
    "use strict";

    $('#series').modal('show');
    $('.edit-title').addClass('hide');
    $('.add-title').removeClass('hide');
    $('#series_id_t').html('');

    $('#series_setting input[name="name"]').val('');
    $('#series_setting select[name="model_id"]').val('').change();
       
  }

  function edit_series(invoker,id){
      "use strict";
      
      $('#series').modal('show');
      $('.edit-title').removeClass('hide');
      $('.add-title').addClass('hide');

      $('#series_id_t').html('');
      $('#series_id_t').append(hidden_input('id',id));

      $('#series_setting input[name="name"]').val($(invoker).data('name'));
      $('#series_setting select[name="model_id"]').val($(invoker).data('model_id')).change();
   
       
  }