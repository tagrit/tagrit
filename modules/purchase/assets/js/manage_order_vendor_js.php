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

function update_delivery_date(el){
  var order_id = $(el).data('order_id');
  $('#update_delivery_date_modal input').val();
  $('#order_additional').html('<input type="hidden" name="order_id" value="'+order_id+'">');
  $('#update_delivery_date_modal').modal('show');
}
</script>