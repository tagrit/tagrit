(function($) {
  "use strict";

  	var _project_id = $('input[name="_project_id"]').val();
	initDataTable('.table-table_pur_request', admin_url+'purchase/table_project_pur_request/'+_project_id);

})(jQuery);
