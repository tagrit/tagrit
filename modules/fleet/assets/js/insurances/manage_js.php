<script type="text/javascript">
	var fnServerParams;
	(function($) {
		"use strict";

		appValidateForm($('#insurance-form'), {
			vehicle_id: 'required',
			name: 'required',
      start_date: 'required',
      end_date: 'required',
      amount: 'required',
      insurance_company_id: 'required',
      insurance_status_id: 'required',
    },fuel_form_handler);

		fnServerParams = {
      "status": '[name="status"]',
      "from_date": '[name="from_date"]',
      "to_date": '[name="to_date"]',
    };

		$('.add-new-insurance').on('click', function(){
    $('#insurance-modal').find('button[type="submit"]').prop('disabled', false);
      $('#insurance-modal').modal('show');
      $('#insurance-modal input[name="id"]').val('');
      $('#insurance-modal select[name="insurance_category_id"]').val('').change();
      $('#insurance-modal select[name="insurance_type_id"]').val('').change();
      $('#insurance-modal select[name="insurance_company_id"]').val('').change();
      $('#insurance-modal select[name="insurance_status_id"]').val('').change();
      $('#insurance-modal select[name="vehicle_id"]').val('').change();

      $('#insurance-modal input[name="name"]').val('');
      $('#insurance-modal input[name="start_date"]').val('');
      $('#insurance-modal input[name="end_date"]').val('');
      $('#insurance-modal input[name="amount"]').val('');
      $('#insurance-modal textarea[name="description"]').val('');
    });

    $('select[name="status"]').on('change', function() {
      init_fuel_table();
    });

    $('input[name="from_date"]').on('change', function() {
      init_fuel_table();
    });

    $('input[name="to_date"]').on('change', function() {
      init_fuel_table();
    });

    init_fuel_table();

	$("input[data-type='currency']").on({
      keyup: function() {
        formatCurrency($(this));
      },
      blur: function() {
        formatCurrency($(this), "blur");
      }
  });
})(jQuery);

function init_fuel_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-fuel')) {
    $('.table-fuel').DataTable().destroy();
  }
  initDataTable('.table-fuel', admin_url + 'fleet/insurances_table', [0], [0], fnServerParams, [1, 'desc']);
  $('.dataTables_filter').addClass('hide');
}

function fuel_form_handler(form) {
    "use strict";
    $('#insurance-modal').find('button[type="submit"]').prop('disabled', true);

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
	 		    init_fuel_table();
        }else {
          alert_float('danger', response.message);
        }
        $('#insurance-modal').modal('hide');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    return false;
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

function edit_insurance(id) {
  "use strict";
    $('#insurance-modal').find('button[type="submit"]').prop('disabled', false);

  requestGetJSON(admin_url + 'fleet/get_data_insurance/'+id).done(function(response) {
      $('#insurance-modal').modal('show');

      $('input[name="id"]').val(id);
      $('select[name="vehicle_id"]').val(response.vehicle_id).change();
      $('select[name="insurance_category_id"]').val(response.insurance_category_id).change();
      $('select[name="insurance_type_id"]').val(response.insurance_type_id).change();
      $('select[name="insurance_company_id"]').val(response.insurance_company_id).change();
      $('select[name="insurance_status_id"]').val(response.insurance_status_id).change();
      $('input[name="name"]').val(response.name);
      $('input[name="amount"]').val(response.amount);
      $('input[name="start_date"]').val(response.start_date);
      $('input[name="end_date"]').val(response.end_date);
      $('textarea[name="description"]').val(response.description);

  });
}
</script>

