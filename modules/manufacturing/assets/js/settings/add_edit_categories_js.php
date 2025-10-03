<script>
  function new_category(){
    "use strict";

    $('#measure_category').modal('show');
    $('.edit-title').addClass('hide');
    $('.add-title').removeClass('hide');
    $('#categories_id').html('');

    $('#measure_category input[name="category_name"]').val('');
  }

  function edit_category(invoker,id){
      "use strict";
      
      $('#measure_category').modal('show');
      $('.edit-title').removeClass('hide');
      $('.add-title').addClass('hide');

      $('#categories_id').html('');
      $('#categories_id').append(hidden_input('id',id));

      $('#measure_category input[name="category_name"]').val($(invoker).data('category_name'));
   
       
  }
</script>