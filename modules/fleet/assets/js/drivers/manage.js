var fnServerParams;
(function($) {
		"use strict";

		fnServerParams = {
      "status": '[name="status"]',
    };

    init_drivers_table();

    $('select[name="status"]').on('change', function() {
      init_drivers_table();
    });

    $('.add-new-drivers').on('click', function(){
      $('#driver-modal').find('button[type="submit"]').prop('disabled', false);

      $('#driver-modal').modal('show');
    });

})(jQuery);

function init_drivers_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-drivers')) {
    $('.table-drivers').DataTable().destroy();
  }
  initDataTable('.table-drivers', admin_url + 'fleet/drivers_table', [0], [0], fnServerParams, [1, 'desc']);
}