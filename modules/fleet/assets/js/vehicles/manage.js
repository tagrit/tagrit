var fnServerParams;
(function($) {
		"use strict";

		fnServerParams = {
      "vehicle_type_id": '[name="vehicle_type_id"]',
      "vehicle_group_id": '[name="vehicle_group_id"]',
      "status": '[name="status"]',
    };

    init_vehicles_table();
    $('select[name="vehicle_type_id"]').on('change', function() {
      init_vehicles_table();
    });

    $('select[name="vehicle_group_id"]').on('change', function() {
      init_vehicles_table();
    });

    $('select[name="status"]').on('change', function() {
      init_vehicles_table();
    });

})(jQuery);

function init_vehicles_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-vehicles')) {
    $('.table-vehicles').DataTable().destroy();
  }
  initDataTable('.table-vehicles', admin_url + 'fleet/vehicles_table', [0], [0], fnServerParams, [1, 'desc']);
}