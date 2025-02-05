var bankRegisterParams = {
 "from_date": '[name="from_date"]',
 "to_date": '[name="to_date"]',
 "bank_account": '[name="bank_account"]',
 "status": '[name="status"]',
};

var id, type, amount, transaction_banking_id ;

(function($) {
	"use strict";
	init_banking_table();

	$('input[name="from_date"], input[name="to_date"], select[name="status"]').on('change', function() {
		init_banking_table();
	});


  $('select[name="bank_account"]').on('change', function() {
    init_banking_table();
    var bank_id = $(this).val();
    requestGet('accounting/check_plaid_connect/' + bank_id).done(function(response) {
      response = JSON.parse(response);
      if(response === true || response === 'true'){
        $('#update_bank_transactions').removeAttr('disabled');
        $('#update_bank_transactions').attr('href', admin_url+'accounting/plaid_bank_new_transactions?id='+bank_id);
      }else{
        $('#update_bank_transactions').attr('disabled', true);
      }
    });
  });
})(jQuery);

function init_banking_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-banking-registers')) {
   $('.table-banking-registers').DataTable().destroy();
 }
 initDataTable('.table-banking-registers', admin_url + 'accounting/banking_register_table', [], [], bankRegisterParams, [0, 'desc']);
}