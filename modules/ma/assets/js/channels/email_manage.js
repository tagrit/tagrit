var fnServerParams;
(function($) {
		"use strict";

		fnServerParams = {
      "category": '[name="category"]',
    };

    $('select[name="category"]').on('change', function() {
      init_email_table();
    });
    
    init_email_table();

})(jQuery);

function init_email_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-emails')) {
    $('.table-emails').DataTable().destroy();
  }
  initDataTable('.table-emails', admin_url + 'ma/email_table', false, false, fnServerParams);
}