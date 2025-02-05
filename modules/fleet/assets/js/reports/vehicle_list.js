var fnServerParams;
(function($) {
  "use strict";

    fnServerParams = {
      "is_report": '[name="is_report"]',
      "type": '[name="type"]',
    };
    
  init_email_log_table();
})(jQuery);

function init_email_log_table() {
"use strict";

 if ($.fn.DataTable.isDataTable('.table-email-logs')) {
   $('.table-email-logs').DataTable().destroy();
 }
 initDataTable('.table-email-logs', admin_url + 'fleet/vehicles_table', false, false, fnServerParams, [3, 'desc']);
}