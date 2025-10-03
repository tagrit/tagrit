<script>
  var  input_group_addon_wh = $('#input-group-addon-wh'),
   _rel_type = $('#rel_type');

    _rel_type.on('change', function() {

      if($(this).val() != ''){
        input_group_addon_wh.removeClass('hide');
      } else {
        input_group_addon_wh.addClass('hide');
      }

    });

    $('body').on('change','#warehouse_id_f', function() {
      "use strict";

      var warehouse_id = $('select[name="warehouse_id_f"]').val();
      var brand_id = $('select[name="brand_id"]').val();
      var model_id = $('select[name="model_id"]').val();
      var series_id = $('select[name="series_id"]').val();

      var data ={};
          data.warehouse_id = warehouse_id;
          data.brand_id     = brand_id;
          data.model_id     = model_id;
          data.series_id    = series_id;
          data.status    = 'warehouse_id';


        $.post(admin_url + 'warehouse/get_item_proposal_value', data).done(function(response){
            response = JSON.parse(response); 

            $("select[name='model_id']").html('');
             $("select[name='model_id']").append(response.model_options);
             $("select[name='model_id']").selectpicker('refresh');

            $('select[name="series_id"]').html('');
            $('select[name="series_id"]').html(response.series_options);
             $("select[name='series_id']").selectpicker('refresh');
             
            $('select[name="item_select"]').html('');
            $('select[name="item_select"]').html(response.item_options);
             $("select[name='item_select']").selectpicker('refresh');

        });


    });

    $('body').on('change','#brand_id', function() {
      "use strict";

      var warehouse_id = $('select[name="warehouse_id_f"]').val();
      var brand_id = $('select[name="brand_id"]').val();
      var model_id = $('select[name="model_id"]').val();
      var series_id = $('select[name="series_id"]').val();

      var data ={};
          data.warehouse_id = warehouse_id;
          data.brand_id     = brand_id;
          data.model_id     = model_id;
          data.series_id    = series_id;
          data.status    = 'brand_id';



        $.post(admin_url + 'warehouse/get_item_proposal_value', data).done(function(response){
            response = JSON.parse(response); 
              $("select[name='model_id']").html('');
             $("select[name='model_id']").append(response.model_options);
             $("select[name='model_id']").selectpicker('refresh');

            $('select[name="series_id"]').html('');
            $('select[name="series_id"]').html(response.series_options);
             $("select[name='series_id']").selectpicker('refresh');

            $('select[name="item_select"]').html('');
            $('select[name="item_select"]').html(response.item_options);
             $("select[name='item_select']").selectpicker('refresh');

        });

     

    });

    $('body').on('change','#model_id', function() {
      "use strict";

      var warehouse_id = $('select[name="warehouse_id_f"]').val();
      var brand_id = $('select[name="brand_id"]').val();
      var model_id = $('select[name="model_id"]').val();
      var series_id = $('select[name="series_id"]').val();

      var data ={};
          data.warehouse_id = warehouse_id;
          data.brand_id     = brand_id;
          data.model_id     = model_id;
          data.series_id    = series_id;
          data.status    = 'model_id';



        $.post(admin_url + 'warehouse/get_item_proposal_value', data).done(function(response){
            response = JSON.parse(response); 

             $("select[name='model_id']").html('');
             $("select[name='model_id']").append(response.model_options);
             $("select[name='model_id']").selectpicker('refresh');

            $('select[name="series_id"]').html('');
            $('select[name="series_id"]').html(response.series_options);
             $("select[name='series_id']").selectpicker('refresh');
             
            $('select[name="item_select"]').html('');
            $('select[name="item_select"]').html(response.item_options);
             $("select[name='item_select']").selectpicker('refresh');

        });

      

    });

    $('body').on('change','#series_id', function() {
      "use strict";

      var warehouse_id = $('select[name="warehouse_id_f"]').val();
      var brand_id = $('select[name="brand_id"]').val();
      var model_id = $('select[name="model_id"]').val();
      var series_id = $('select[name="series_id"]').val();

      var data ={};
          data.warehouse_id = warehouse_id;
          data.brand_id     = brand_id;
          data.model_id     = model_id;
          data.series_id    = series_id;
          data.status    = 'series_id';
          


        $.post(admin_url + 'warehouse/get_item_proposal_value', data).done(function(response){
            response = JSON.parse(response); 
             $("select[name='model_id']").html('');
             $("select[name='model_id']").append(response.model_options);
             $("select[name='model_id']").selectpicker('refresh');

            $('select[name="series_id"]').html('');
            $('select[name="series_id"]').html(response.series_options);
             $("select[name='series_id']").selectpicker('refresh');
             
            $('select[name="item_select"]').html('');
            $('select[name="item_select"]').html(response.item_options);
             $("select[name='item_select']").selectpicker('refresh');

        });

      

    });



</script>