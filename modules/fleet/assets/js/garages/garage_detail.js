var maintenancesParams = {
  'garage_id': '[name="garage_id"]'
};
	var fnServerParams ={
		'garage_id': '[name="garage_id"]'
	};
(function(){
	"use strict";

    init_fuel_table();
    init_maintenances_table();

    $('.add-new-team').on('click', function(){
    	$('#maintenance-team-modal').find('button[type="submit"]').prop('disabled', false);
      	$('#maintenance-team-modal').modal('show');
    });

})(jQuery);


function init_fuel_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-maintenance-team')) {
    $('.table-maintenance-team').DataTable().destroy();
  }
  initDataTable('.table-maintenance-team', admin_url + 'fleet/maintenance_team_table', [0], [0], fnServerParams, [1, 'desc']);
}

function init_maintenances_table() {
    "use strict";

    if ($.fn.DataTable.isDataTable('.table-maintenances')) {
        $('.table-maintenances').DataTable().destroy();
    }

    initDataTable('.table-maintenances', admin_url + 'fleet/maintenances_table', '', '', maintenancesParams, [0, 'desc']);
}