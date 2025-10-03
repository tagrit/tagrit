<script>

	"use strict";

	appValidateForm($('#select_warehouse_modal'), {
			warehouse_id: 'required',
		});

	var InvoiceServerParams = {
		"from_date": "input[name='from_date']",
		"to_date": "input[name='to_date']",
		"staff_id": "select[name='staff_id[]']",
		"delivery_id": "select[name='delivery_id[]']",
		"status": "select[name='status_ft']",
		"vendors": "select[name='vendor_ft[]']",
	};


	var table_manage_order_return = $('.table-table_manage_order_return');
	initDataTable(table_manage_order_return, admin_url+'purchase/table_manage_order_return',[],[], InvoiceServerParams, [0 ,'desc']);

	$('.order_return_sm').DataTable().columns([0]).visible(false, false);

	$('input[name="from_date"], input[name="to_date"], select[name="vendor_ft[]"], select[name="status_ft"], select[name="rel_type_filter[]"]').on('change', function() {
		table_manage_order_return.DataTable().ajax.reload();
	});


	init_order_return();
	function init_order_return(id) {
		"use strict";
		load_small_table_item_proposal(id, '#order_return_sm_view', 'delivery_id', 'purchase/view_order_return', '.order_return_sm');
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
		$('#filter_div').removeClass('hide');
	} else {
		$('#filter_div').addClass('hide');
		tablewrap.addClass('col-md-5').removeClass('col-md-12');
		$('.toggle-small-view').find('i').removeClass('fa fa-angle-double-left').addClass('fa fa-angle-double-right');
	}
	var _table = $(table).DataTable();
	// Show hide hidden columns
	//_table.columns(hidden_columns).visible(_visible, false);
	_table.columns.adjust();
	$(main_data).toggleClass('hide');
	$(window).trigger('resize');
	
}

function open_warehouse_modal(iv, order_return_id) {
    "use strict";

      $("#warehouse_modal_wrapper").load("<?php echo admin_url('purchase/purchase/open_warehouse_modal'); ?>", {
        order_return_id:order_return_id,
      }, function() {

        $("body").find('#warehouse_modal').modal({ show: true, backdrop: 'static' });

      });
  }

</script>