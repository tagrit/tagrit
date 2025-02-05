var fnServerParams;
(function($) {
		"use strict";

    init_driver_documents_table();

})(jQuery);

function init_driver_documents_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-driver-documents')) {
    $('.table-driver-documents').DataTable().destroy();
  }
  initDataTable('.table-driver-documents', admin_url + 'fleet/driver_documents_table', [0], [0], fnServerParams, [1, 'desc']);
}
