<script>
	$( ".inventory" ).click(function() {

  	"use strict";

  		var notification_recipients_id_str = '<?php echo get_warehouse_option('inventory_cronjob_notification_recipients'); ?>';

	    if(typeof(notification_recipients_id_str) == "string" && notification_recipients_id_str != ''){
	        $('#settings-form select[name="inventory_cronjob_notification_recipients[]"]').val( (notification_recipients_id_str).split(',')).change();
	    }

        init_selectpicker();
        init_datepicker();


  });


</script>