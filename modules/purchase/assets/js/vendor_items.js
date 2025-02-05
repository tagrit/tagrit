(function($) {
  "use strict";

  group_it_change();
})(jQuery);

  function group_it_change() {
    "use strict";
    var group = $('select[name="group_item"]').val();
    if(group != ''){
      requestGet(admin_url + 'purchase/group_it_change/'+group).done(function(response){
        response = JSON.parse(response);
        if(response.html != ''){
          $('select[id="items"]').html('');
          $('select[id="items"]').append(response.html);
          $('select[id="items"]').selectpicker('refresh');
        }else{
          init_ajax_search('items','#items.ajax-search',undefined,admin_url+'purchase/pur_commodity_code_search_vendor_item/purchase_price/can_be_purchased/'+group);
          $('select[id="items"]').html('');
          $('select[id="items"]').selectpicker('refresh');
        }
      });
    }else{
      init_ajax_search('items','#items.ajax-search',undefined,admin_url+'purchase/pur_commodity_code_search');
      requestGet(admin_url + 'purchase/group_it_change/'+group).done(function(response){
        response = JSON.parse(response);
        if(response.html != ''){
          $('select[id="items"]').html('');
          $('select[id="items"]').append(response.html);
          $('select[id="items"]').selectpicker('refresh');
        }else{
          $('select[id="items"]').html('');
          $('select[id="items"]').selectpicker('refresh');
        }
      });
    }
  }
