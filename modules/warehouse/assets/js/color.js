  function new_color(){
    "use strict";

    $('#color').modal('show');
    $('.edit-title').addClass('hide');
    $('.add-title').removeClass('hide');
    $('#color_id_t').html('');

    $('#colors_setting input[name="color_code"]').val('');
    $('#colors_setting input[name="color_name"]').val('');
    $('#colors_setting input[name="color_hex"]').val('');
    $('#colors_setting textarea[name="note"]').val('');
       
  }

  function edit_color(invoker,id){
      "use strict";
      
      $('#color').modal('show');
      $('.edit-title').removeClass('hide');
      $('.add-title').addClass('hide');

      $('#color_id_t').html('');
      $('#color_id_t').append(hidden_input('id',id));

      $('#colors_setting input[name="color_code"]').val($(invoker).data('color_code'));
      $('#colors_setting input[name="color_name"]').val($(invoker).data('color_name'));
      $('#colors_setting input[name="color_hex"]').val($(invoker).data('color_hex'));
      $('#colors_setting textarea[name="note"]').val($(invoker).data('note'));

      $('#colors_setting input[name="order"]').val($(invoker).data('order'));
      if($(invoker).data('display') == 1){
        $('#colors_setting input[name="display"]').prop("checked", true);
      }else{
        $('#colors_setting input[name="display"]').prop("checked", false);

      }
   
       
  }