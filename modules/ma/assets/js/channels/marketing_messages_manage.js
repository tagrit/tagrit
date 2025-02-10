var fnServerParams;
(function($) {
		"use strict";

		fnServerParams = {
    };
    init_marketing_messages_table();

})(jQuery);

function init_marketing_messages_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-marketing-messages')) {
    $('.table-marketing-messages').DataTable().destroy();
  }
  initDataTable('.table-marketing-messages', admin_url + 'ma/marketing_messages_table', false, false, fnServerParams);
}