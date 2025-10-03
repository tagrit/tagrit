
  appValidateForm($('#brands_setting'), {
     name: 'required',
   }); 

  function new_brand(){
    "use strict";

    $('#brand').modal('show');
    $('.edit-title').addClass('hide');
    $('.add-title').removeClass('hide');
    $('#brand_id_t').html('');

    $('#brands_setting input[name="name"]').val('');
       
  }

  function edit_brand(invoker,id){
      "use strict";
      
      $('#brand').modal('show');
      $('.edit-title').removeClass('hide');
      $('.add-title').addClass('hide');

      $('#brand_id_t').html('');
      $('#brand_id_t').append(hidden_input('id',id));

      $('#brands_setting input[name="name"]').val($(invoker).data('name'));
   
       
  }