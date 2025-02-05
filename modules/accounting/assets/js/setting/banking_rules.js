var fnServerParams;
(function($) {
		"use strict";

		fnServerParams = {
    };
    init_banking_rules_table();
    
})(jQuery);

function init_banking_rules_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-banking-rules')) {
    $('.table-banking-rules').DataTable().destroy();
  }
  initDataTable('.table-banking-rules', admin_url + 'accounting/banking_rules_table', false, false, fnServerParams);
}
