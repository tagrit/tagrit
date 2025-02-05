  
   appValidateForm($('#custom_fields_setting'), {
     custom_fields_id: 'required',
     'warehouse_id[]': 'required',
   }); 

  function new_custom_fields_warehouse(){
    "use strict";

    $('#custom_fields').modal('show');
    $('.edit-title').addClass('hide');
    $('.add-title').removeClass('hide');
    $('#custom_fields_id_t').html('');

    $('#custom_fields_setting select[name="custom_fields_id"]').val('').change();
    $('#custom_fields_setting select[name="warehouse_id"]').val('').change();

       
  }

  function edit_custom_fields_warehouse(invoker,id){
      "use strict";
      
      $('#custom_fields').modal('show');
      $('.edit-title').removeClass('hide');
      $('.add-title').addClass('hide');

      $('#custom_fields_id_t').html('');
      $('#custom_fields_id_t').append(hidden_input('id',id));

      $('#custom_fields_setting select[name="custom_fields_id"]').val($(invoker).data('custom_fields_id')).change();

      var warehouse_id_str = $(invoker).data('warehouse_id');
        if(typeof(warehouse_id_str) == "string"){
            $('#custom_fields_setting select[name="warehouse_id[]"]').val( ($(invoker).data('warehouse_id')).split(',')).change();
        }else{
           $('#custom_fields_setting select[name="warehouse_id[]"]').val($(invoker).data('warehouse_id')).change();

        }

       
  }

  $('.warehouse_custom_fields_submit').on('click', function() {

      var custom_fields_id = $('select[name="custom_fields_id"]').val();
      var id = $('input[name="id"]').val();

      var datacheck = {};
      datacheck.custom_fields_id = custom_fields_id;
      datacheck.id = id;

        $.post(admin_url + 'warehouse/check_warehouse_custom_fields', datacheck).done(function(responsec){
          responsec = JSON.parse(responsec);

          if(responsec.success){
            $('#custom_fields_setting').submit(); 

          }else{
            alert_float('danger', responsec.message);
          }

        });


  });
