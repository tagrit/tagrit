var fnServerParams;
(function($) {
		"use strict";

		fnServerParams = {
      "from_date": '[name="from_date"]',
      "to_date": '[name="to_date"]',
    };

    $('input[name="from_date"]').on('change', function() {
    init_expenses_table();
    });

    $('input[name="to_date"]').on('change', function() {
      init_expenses_table();
    });
   
    init_expenses_table();
    
})(jQuery);

function init_expenses_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-fleet-expenses')) {
    $('.table-fleet-expenses').DataTable().destroy();
  }
  initDataTable('.table-fleet-expenses', admin_url + 'fleet/expenses_table', false, false, fnServerParams);
}
