var fnServerParams;
(function($) {
		"use strict";

		fnServerParams = {
    };
    init_point_triggers_table();

})(jQuery);

function init_point_triggers_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-point-triggers')) {
    $('.table-point-triggers').DataTable().destroy();
  }
  initDataTable('.table-point-triggers', admin_url + 'ma/point_triggers_table', false, false, fnServerParams);
}