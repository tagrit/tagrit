var fnServerParams;
(function($) {
		"use strict";

		fnServerParams = {
      "category": '[name="category"]',
    };
    init_point_actions_table();

    $('select[name="category"]').on('change', function() {
      init_point_actions_table();
    });

})(jQuery);

function init_point_actions_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-point-actions')) {
    $('.table-point-actions').DataTable().destroy();
  }
  initDataTable('.table-point-actions', admin_url + 'ma/point_actions_table', false, false, fnServerParams);
}