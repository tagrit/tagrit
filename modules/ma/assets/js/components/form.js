var fnServerParams;
(function($) {
		"use strict";

		fnServerParams = {
      "category": '[name="category"]',
    };

    $('select[name="category"]').on('change', function() {
      init_form_table();
    });
    
    init_form_table();

})(jQuery);

function init_form_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-form')) {
    $('.table-form').DataTable().destroy();
  }
  initDataTable('.table-form', admin_url + 'ma/form_table', false, false, fnServerParams);
}