<script>

    function setting_rule_sale_price(invoker){
"use strict";
        var input_value = $('input[id="warehouse_selling_price_rule_profif_ratio"]').val();

        if(input_value > 100){
          alert_float('warning', '<?php echo _l('_please_enter_from_0_100'); ?>');
          $('input[id="warehouse_selling_price_rule_profif_ratio"]').val('');
        }
        if(input_value < 0){
          alert_float('warning', '<?php echo _l('_please_enter_from_0_100'); ?>');
          $('input[id="warehouse_selling_price_rule_profif_ratio"]').val('');
        }

        var data = {};
            data.warehouse_selling_price_rule_profif_ratio = input_value;

        if((input_value >= 0) && ( input_value <= 100)){
          $.post(admin_url + 'warehouse/warehouse_selling_price_profif_ratio', data).done(function(response){
                response = JSON.parse(response); 
                if (response.success == true) {
                    alert_float('success', response.message);
                }else{
                    alert_float('warning', response.message);

                }
            });
        }

    }

    function setting_profit_rate(invoker) {
    "use strict";
     var data={};
        data.profit_rate_by_purchase_price_sale = invoker.value;

        $.post(admin_url + 'warehouse/warehouse_profit_rate_by_purchase_price_sale', data).done(function(response){
              response = JSON.parse(response); 
              if (response.success == true) {
                  alert_float('success', response.message);
              }else{
                  alert_float('warning', response.message);

              }
          });
    }



    function setting_rules_for_rounding_prices(invoker) {
"use strict";

      var input_value = $('input[id="'+invoker.name+'"]').val();
      var data={};
          data.type = invoker.name;
          data.input_value = input_value;

        if(input_value < 0){
          alert_float('warning', '<?php echo _l('_please_enter_from_0'); ?>');
          $('input[id="'+invoker.name+'"]').val('');
        }

        if((input_value >= 0) && ( input_value <= 100)){
          $.post(admin_url + 'warehouse/setting_rules_for_rounding_prices', data).done(function(response){
                response = JSON.parse(response); 
                if (response.success == true) {
                    if(invoker.name =='warehouse_the_fractional_part'){
                      $('input[id="warehouse_integer_part"]').val('0');
                    }else{
                      $('input[id="warehouse_the_fractional_part"]').val('0');

                    }
                    alert_float('success', response.message);
                }else{
                    alert_float('warning', response.message);

                }
            });
        }

    }

    function auto_create_change_setting(invoker){
    "use strict";
        var input_name = invoker.value;
        var input_name_status = $('input[id="'+invoker.value+'"]').is(":checked");
        
        var data = {};
            data.input_name = input_name;
            data.input_name_status = input_name_status;
            
        $.post(admin_url + 'warehouse/auto_create_goods_received_delivery_setting', data).done(function(response){
              response = JSON.parse(response); 
              if (response.success == true) {
                  alert_float('success', response.message);
              }else{
                  alert_float('warning', response.message);

              }
          });

    }

    function show_item_cf_on_pdf(invoker){
    "use strict";
        var input_name = invoker.value;
        var input_name_status = $('input[id="'+invoker.value+'"]').is(":checked");
        
        var data = {};
            data.input_name = input_name;
            data.input_name_status = input_name_status;
            
        $.post(admin_url + 'warehouse/show_item_cf_on_pdf', data).done(function(response){
              response = JSON.parse(response); 
              if (response.success == true) {
                  alert_float('success', response.message);
              }else{
                  alert_float('warning', response.message);

              }
          });

    }

    function goods_receipt_warehouse_change(invoker) {
      "use strict";

      var input_name = invoker.name;
      var input_name_status = invoker.value; //warehouse id
        
        var data = {};
            data.input_name = input_name;
            data.input_name_status = input_name_status;

        $.post(admin_url + 'warehouse/update_goods_receipt_warehouse', data).done(function(response){
              response = JSON.parse(response); 
              if (response.success == true) {
                  alert_float('success', response.message);
              }else{
                  alert_float('warning', response.message);

              }
          });

    }

    
  function update_unchecked_inventory_numbers(event){
    "use strict";
    if (confirm_delete()) {
        $(event).attr( "disabled", "disabled" );
        $('#update_unchecked_inventory_numbers').submit(); 
    }

  }

  function submit_policies_information(event){
    var myContent = tinymce.get("wh_return_policies_information").getContent();
    var data = {};
    data.myContent = myContent;

    $.get(admin_url+'warehouse/update_return_policies_information', data).done(function(response){
      response = JSON.parse(response);
      if(response.status == true || response.status == 'true'){
        alert_float('success', response.message);
      }

    }).fail(function(error) {

    });

  }

  function setting_fee_for_return_order(invoker) {
    "use strict";
    var data={};
    data.wh_fee_for_return_order =  $('input[id="wh_fee_for_return_order"]').val();

    $.post(admin_url + 'warehouse/warehouse_fee_for_return_order', data).done(function(response){
      response = JSON.parse(response); 
      if (response.success == true) {
        alert_float('success', response.message);
      }else{
        alert_float('warning', response.message);

      }
    });
  }

  function setting_wh_on_total_items(invoker) {
    "use strict";
    var data={};
    data.wh_on_total_items = $('input[id="wh_on_total_items"]').val();

    $.post(admin_url + 'warehouse/warehouse_wh_on_total_items', data).done(function(response){
      response = JSON.parse(response); 
      if (response.success == true) {
        alert_float('success', response.message);
      }else{
        alert_float('warning', response.message);

      }
    });
  }

    function setting_custom_measurements_name(invoker) {
    "use strict";
    var value = $(invoker).find('input').val();
    var name = $(invoker).find('input').data('name');
    var data={};
    data[name] = value;

    $.post(admin_url + 'warehouse/setting_custom_measurements_name', data).done(function(response){
      response = JSON.parse(response); 
      if (response.success == true) {
        alert_float('success', response.message);
      }else{
        alert_float('warning', response.message);

      }
    });
  }
  
  
</script>