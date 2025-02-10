var fnServerParams;
(function($) {
		"use strict";

		fnServerParams = {
    };
    init_email_sending_limit_table();
})(jQuery);

function init_email_sending_limit_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-email_sending_limit')) {
    $('.table-email_sending_limit').DataTable().destroy();
  }

  var table_email_limit = $("table.table-email_sending_limit");

  if(table_email_limit.length > 0){
    var _table_email_limit_api = initDataTable('.table-email_sending_limit', admin_url + 'ma/email_sending_limit_table', false, false, fnServerParams);

    _table_email_limit_api.on("draw", function () {
      init_progress_bars();
    });
  }
}