(function($) {
	"use strict"; 
	var table_invoice = $('.table-table_pur_invoices');
	var Params = {
		"from_date": 'input[name="from_date"]',
        "to_date": 'input[name="to_date"]',
        "contract": "[name='contract[]']",
        "pur_orders": "[name='pur_orders[]']",
        "vendors": "[name='vendor_ft[]']"
    };

	initDataTable(table_invoice, admin_url+'purchase/table_pur_invoices',[], [], Params);
	$.each(Params, function(i, obj) {
        $('select' + obj).on('change', function() {  
            table_invoice.DataTable().ajax.reload()
                .columns.adjust()
                .responsive.recalc();
        });
    });

    $('input[name="from_date"]').on('change', function() {
        table_invoice.DataTable().ajax.reload()
                .columns.adjust()
                .responsive.recalc();
    });
    $('input[name="to_date"]').on('change', function() {
        table_invoice.DataTable().ajax.reload()
                .columns.adjust()
                .responsive.recalc();
    });
})(jQuery);