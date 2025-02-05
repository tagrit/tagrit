var fnServerParams = {
		"member_filter": '[name="member_filter"]',
    	"hash_share": '[name="hash_share"]',
    	"from_date": '[name="from_date"]',
    	"to_date": '[name="to_date"]',
	};
(function($) {
	"use strict";

	init_download_management_table();


	$('select[name="member_filter"]').on('change', function() {
		init_download_management_table();
	});
	$('select[name="hash_share"]').on('change', function() {
		init_download_management_table();
	});

	$('input[name="from_date"]').on('change', function() {
		init_download_management_table();
	});

	$('input[name="to_date"]').on('change', function() {
		init_download_management_table();
	});

})(jQuery);

function init_download_management_table() {
"use strict";

 if ($.fn.DataTable.isDataTable('.table-download-management')) {
   $('.table-download-management').DataTable().destroy();
 }
 initDataTable('.table-download-management', admin_url + 'file_sharing/download_management_table', false, false, fnServerParams, [3, 'desc']);
}
