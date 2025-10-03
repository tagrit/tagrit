<script>

(function($) {
"use strict";  


})(jQuery);  

function confirm_order(el){
  var order_id = $(el).data('order_id');
  var r = confirm("<?php echo _l('confirm_order_string_confirm'); ?>");
  if(r == true && order_id != ''){ 
    $.post(site_url + 'purchase/vendors_portal/confirm_order/'+ order_id).done(function (response) { 
      response = JSON.parse(response);
      if(response.success == true || response.success == 'true'){
        alert_float('success', '<?php echo _l('confirm_order_successful'); ?>');
      }

      window.location.reload();
    });
  }
}

function update_delivery_status(el){
  var status = $(el).val();
  var order_id = '<?php echo pur_html_entity_decode($pur_order->id); ?>';
  $.post(site_url + 'purchase/vendors_portal/update_delivery_status/'+ order_id+'/'+status).done(function (response) { 
    response = JSON.parse(response);
    if(response.success == true || response.success == 'true'){
      if(response.success == true || response.success == 'true'){
        alert_float('success', '<?php echo _l('update_delivery_status_successfully'); ?>');
      }

      window.location.reload();
    }
  });
}

function update_delivery_date(el){
  var data = {};
  data.date = $(el).val();
  var order_id = '<?php echo pur_html_entity_decode($pur_order->id); ?>';
  $.post(site_url + 'purchase/vendors_portal/update_delivery_date/'+ order_id, data).done(function (response) {
    response = JSON.parse(response);
    if(response.success == true || response.success == 'true'){
      alert_float('success', '<?php echo _l('update_delivery_date_successfully'); ?>');
    }
  });
}


</script>