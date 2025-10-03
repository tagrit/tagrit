(function($) {
"use strict";
	var table_contracts = $('.table-table_contracts');
	var Params = {
        "vendor": "[name='vendor[]']",
        "department": "[name='department[]']",
        "project": "[name='project[]']",
    };

	initDataTable(table_contracts, admin_url+'purchase/table_contracts',[0], [0], Params);

	 $.each(Params, function(i, obj) {
        $('select' + obj).on('change', function() {  
            table_contracts.DataTable().ajax.reload()
                .columns.adjust()
                .responsive.recalc();
        });
    });
})(jQuery);