var fnServerParams;
(function($) {
		"use strict";

		fnServerParams = {
      "category": '[name="category"]',
    };

    $('select[name="category"]').on('change', function() {
      init_asset_table();
    });

    init_asset_table();

})(jQuery);

function init_asset_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-asset')) {
    $('.table-asset').DataTable().destroy();
  }
  initDataTable('.table-asset', admin_url + 'ma/asset_table', false, false, fnServerParams);
}