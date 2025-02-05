<script>

	"use strict";

	var InvoiceServerParams = {
		"from_date": "input[name='from_date']",
		"to_date": "input[name='to_date']",
		"staff_id": "select[name='staff_id[]']",
		"delivery_id": "select[name='delivery_id[]']",
		"status_id": "select[name='status_id[]']",
	};


	var table_manage_packing_list = $('.table-table_manage_packing_list');
	initDataTable(table_manage_packing_list, admin_url+'warehouse/table_manage_packing_list',[],[], InvoiceServerParams, [0 ,'desc']);

	$('.packing_list_sm').DataTable().columns([0]).visible(false, false);

	$('input[name="from_date"], input[name="to_date"], select[name="staff_id[]"], select[name="delivery_id[]"], select[name="status_id"]').on('change', function() {
		table_manage_packing_list.DataTable().ajax.reload();
	});


	init_packing_list();
	function init_packing_list(id) {
		"use strict";
		load_small_table_item_proposal(id, '#packing_list_sm_view', 'delivery_id', 'warehouse/view_packing_list', '.packing_list_sm');
	}
	var hidden_columns = [3,4,5];

	function load_small_table_item_proposal(pr_id, selector, input_name, url, table) {
		"use strict";

		var _tmpID = $('input[name="' + input_name + '"]').val();
	// Check if id passed from url, hash is prioritized becuase is last
	if (_tmpID !== '' && !window.location.hash) {
		pr_id = _tmpID;
		// Clear the current id value in case user click on the left sidebar credit_note_ids
		$('input[name="' + input_name + '"]').val('');
	} else {
		// check first if hash exists and not id is passed, becuase id is prioritized
		if (window.location.hash && !pr_id) {
			pr_id = window.location.hash.substring(1); //Puts hash in variable, and removes the # character
		}
	}
	if (typeof(pr_id) == 'undefined' || pr_id === '') { return; }
	if (!$("body").hasClass('small-table')) { toggle_small_view_proposal(table, selector); }
	$('input[name="' + input_name + '"]').val(pr_id);
	do_hash_helper(pr_id);
	$(selector).load(admin_url + url + '/' + pr_id);
	if (is_mobile()) {
		$('html, body').animate({
			scrollTop: $(selector).offset().top + 150
		}, 600);
	}
}

function toggle_small_view_proposal(table, main_data) {
	"use strict";

	$("body").toggleClass('small-table');
	var tablewrap = $('#small-table');
	if (tablewrap.length === 0) { return; }
	var _visible = false;
	if (tablewrap.hasClass('col-md-5')) {
		tablewrap.removeClass('col-md-5').addClass('col-md-12');
		_visible = true;
		$('.toggle-small-view').find('i').removeClass('fa fa-angle-double-right').addClass('fa fa-angle-double-left');
	} else {
		tablewrap.addClass('col-md-5').removeClass('col-md-12');
		$('.toggle-small-view').find('i').removeClass('fa fa-angle-double-left').addClass('fa fa-angle-double-right');
	}
	var _table = $(table).DataTable();
	// Show hide hidden columns
	_table.columns(hidden_columns).visible(_visible, false);
	_table.columns.adjust();
	$(main_data).toggleClass('hide');
	$(window).trigger('resize');
	
}

</script>