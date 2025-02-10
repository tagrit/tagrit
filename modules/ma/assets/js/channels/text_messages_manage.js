var fnServerParams;
(function($) {
		"use strict";

		fnServerParams = {
    };
    init_text_messages_table();

})(jQuery);

function init_text_messages_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-text-messages')) {
    $('.table-text-messages').DataTable().destroy();
  }
  initDataTable('.table-text-messages', admin_url + 'ma/text_messages_table', false, false, fnServerParams);
}