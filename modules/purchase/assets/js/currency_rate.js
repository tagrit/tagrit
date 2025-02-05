var fnServerParams, _fnServerParams;
(function(){
	"use strict";
	fnServerParams = {
      "from_currency": '[name="from_currency"]',
      "to_currency": '[name="to_currency"]',
	};
	
	_fnServerParams = {
      "from_currency": '[name="from_currency_logs"]',
      "to_currency": '[name="to_currency_logs"]',
      "date": '[name="date"]',
	};

	appValidateForm($('#form_currency_rates'), {
		'name': 'required',
		'type': 'required'
	});

		$('select[name="from_currency"]').on('change', function() {
	    init_currency_rates_table();
	  });
		$('select[name="to_currency"]').on('change', function() {
	    init_currency_rates_table();
	  });

		$('select[name="from_currency_logs"]').on('change', function() {
	    init_currency_rate_logs_table();
	  });
		$('select[name="to_currency_logs"]').on('change', function() {
	    init_currency_rate_logs_table();
	  });

	  $('input[name="date"]').on('change', function() {
	    init_currency_rate_logs_table();
	  });

	init_currency_rates_table();
	init_currency_rate_logs_table();
})(jQuery);

function init_currency_rates_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-currency_rates')) {
     $('.table-currency_rates').DataTable().destroy();
  }
  initDataTable('.table-currency_rates', admin_url + 'purchase/currency_rate_table', [0], [0], fnServerParams, [0, 'desc']);
}

function init_currency_rate_logs_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-currency-rate-logs')) {
     $('.table-currency-rate-logs').DataTable().destroy();
  }
  initDataTable('.table-currency-rate-logs', admin_url + 'purchase/currency_rate_logs_table', [0], [0], _fnServerParams, [0, 'desc']);
}

function get_currency_rate(id){
	"use strict";
	var requestURL = admin_url+'purchase/get_currency_rate_online/' + id;
	requestGetJSON(requestURL).done(function(response) {
		$('input[name="to_currency_rate"]').val(response.value);
	}).fail(function(data) {
		alert_float('danger', 'Error');
	});
}

function get_all_currency_rate(id){
	"use strict";
	var requestURL = admin_url+'purchase/get_all_currency_rate';
	requestGetJSON(requestURL).done(function(response) {
		
	}).fail(function(data) {
		alert_float('danger', 'Error');
	});
}


function edit_currency_rate_modal(id) {
		"use strict";

		$("#modal_wrapper").load(admin_url + 'purchase/currency_rate_modal', {
			id: id,
		}, function() {
			$("body").find('#currencyRateModal').modal({ show: true, backdrop: 'static' });
		});
	}
