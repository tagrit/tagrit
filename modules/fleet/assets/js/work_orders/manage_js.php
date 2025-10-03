<script type="text/javascript">
	var fnServerParams;
	(function($) {
		"use strict";

		appValidateForm($('#work_order-form'), {
			vehicle_id: 'required',
			subject: 'required',
      work_order_type: 'required',
      work_order_time: 'required',
      driver_id: 'required',
    },work_order_form_handler);

		fnServerParams = {
      "status": '[name="__status"]',
      "from_date": '[name="from_date"]',
      "to_date": '[name="to_date"]',
    };

		$('.add-new-work-order').on('click', function(){
    $('#work-order-modal').find('button[type="submit"]').prop('disabled', false);
      $('#work-order-modal').modal('show');

      $('#work-order-modal input[name="id"]').val('');
      $('#work-order-modal select[name="vehicle_id"]').val('').change();
      $('#work-order-modal select[name="driver_id"]').val('').change();
      $('#work-order-modal select[name="work_order_type"]').val('').change();
      $('#work-order-modal input[name="work_order_time"]').val('');
      $('#work-order-modal input[name="subject"]').val('');
      $('#work-order-modal textarea[name="description"]').val('');
    });

    $('select[name="_work_order_type"]').on('change', function() {
      init_work_order_table();
    });

    $('input[name="from_date"]').on('change', function() {
      init_work_order_table();
    });

    $('input[name="to_date"]').on('change', function() {
      init_work_order_table();
    });

    init_work_order_table();

	$("input[data-type='currency']").on({
      keyup: function() {
        formatCurrency($(this));
      },
      blur: function() {
        formatCurrency($(this), "blur");
      }
  });
})(jQuery);

function init_work_order_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-work-order')) {
    $('.table-work-order').DataTable().destroy();
  }
  initDataTable('.table-work-order', admin_url + 'fleet/work_orders_table', [0], [0], fnServerParams, [1, 'desc']);
}

function work_order_form_handler(form) {
    "use strict";
    $('#work-order-modal').find('button[type="submit"]').prop('disabled', true);

    var formURL = form.action;
    var formData = new FormData($(form)[0]);

    $.ajax({
        type: $(form).attr('method'),
        data: formData,
        mimeType: $(form).attr('enctype'),
        contentType: false,
        cache: false,
        processData: false,
        url: formURL
    }).done(function(response) {
        response = JSON.parse(response);
        if (response.success === true || response.success == 'true' || $.isNumeric(response.success)) {
          alert_float('success', response.message);
	 		    init_work_order_table();
        }else {
          alert_float('danger', response.message);
        }
        $('#work-order-modal').modal('hide');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    return false;
}


function edit_work_order(id) {
  "use strict";
    $('#work-order-modal').find('button[type="submit"]').prop('disabled', false);

  requestGetJSON(admin_url + 'fleet/get_data_work_order/'+id).done(function(response) {
      $('#work-order-modal').modal('show');

      $('#work-order-modal input[name="id"]').val(id);
      $('#work-order-modal select[name="vehicle_id"]').val(response.vehicle_id).change();
      $('#work-order-modal select[name="driver_id"]').val(response.driver_id).change();
      $('#work-order-modal select[name="work_order_type"]').val(response.work_order_type).change();
      $('#work-order-modal input[name="work_order_time"]').val(response.work_order_time);
      $('#work-order-modal input[name="subject"]').val(response.subject);
      $('#work-order-modal textarea[name="description"]').val(response.description);

  });
}

function formatNumber(n) {
  "use strict";
  // format number 1000000 to 1,234,567
  return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
}
function formatCurrency(input, blur) {
  "use strict";
  // appends $ to value, validates decimal side
  // and puts cursor back in right position.

  // get input value
  var input_val = input.val();

  // don't validate empty input
  if (input_val === "") { return; }

  // original length
  var original_len = input_val.length;

  // initial caret position
  var caret_pos = input.prop("selectionStart");

  // check for decimal
  if (input_val.indexOf(".") >= 0) {

    // get position of first decimal
    // this prevents multiple decimals from
    // being entered
    var decimal_pos = input_val.indexOf(".");

    // split number by decimal point
    var left_side = input_val.substring(0, decimal_pos);
    var right_side = input_val.substring(decimal_pos);

    // add commas to left side of number
    left_side = formatNumber(left_side);

    // validate right side
    right_side = formatNumber(right_side);

    // Limit decimal to only 2 digits
    right_side = right_side.substring(0, 2);

    // join number by .
    input_val = left_side + "." + right_side;

  } else {
    // no decimal entered
    // add commas to number
    // remove all non-digits
    input_val = formatNumber(input_val);
    input_val = input_val;

  }

  // send updated string to input
  input.val(input_val);

  // put caret back in the right position
  var updated_len = input_val.length;
  caret_pos = updated_len - original_len + caret_pos;
  input[0].setSelectionRange(caret_pos, caret_pos);
}

</script>

