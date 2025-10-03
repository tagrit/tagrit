<script type="text/javascript">
	var fnServerParams;
	(function($) {
		"use strict";

		appValidateForm($('#booking-form'), {
			userid: 'required',
			subject: 'required',
      delivery_date: 'required',
      phone: 'required',
      receipt_address: 'required',
			delivery_address: 'required',
    },fuel_form_handler);

		fnServerParams = {
      "status": '[name="status"]',
      "from_date": '[name="from_date"]',
      "to_date": '[name="to_date"]',
    };

		$('.add-new-booking').on('click', function(){
    $('#booking-modal').find('button[type="submit"]').prop('disabled', false);
      $('#booking-modal').modal('show');
      $('#booking-modal input[name="id"]').val('');
      $('#booking-modal select[name="userid"]').val('').change();
      $('#booking-modal input[name="subject"]').val('');
      $('#booking-modal input[name="delivery_date"]').val('');
      $('#booking-modal input[name="amount"]').val('');
      $('#booking-modal input[name="phone"]').val('');
      $('#booking-modal textarea[name="receipt_address"]').val('');
      $('#booking-modal textarea[name="delivery_address"]').val('');
      $('#booking-modal textarea[name="note"]').val('');
      $('#booking-modal textarea[name="admin_note"]').val('');
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
  initDataTable('.table-fuel', admin_url + 'fleet/bookings_table', [0], [0], fnServerParams, [1, 'desc']);
  $('.dataTables_filter').addClass('hide');
}

function fuel_form_handler(form) {
    "use strict";
    $('#booking-modal').find('button[type="submit"]').prop('disabled', true);

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
        $('#booking-modal').modal('hide');
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

// fuel bulk actions action
function bulk_action(event) {
  "use strict";
    if (confirm_delete()) {
        var ids = [],
            data = {};
            data.mass_delete = $('#mass_delete').prop('checked');

        var rows = $($('#fuel_bulk_actions').attr('data-table')).find('tbody tr');

        $.each(rows, function() {
            var checkbox = $($(this).find('td').eq(0)).find('input');
            if (checkbox.prop('checked') === true) {
                ids.push(checkbox.val());
            }
        });
        data.ids = ids;
        $(event).addClass('disabled');
        setTimeout(function() {
            $.post(admin_url + 'fleet/fuel_bulk_action', data).done(function() {
                window.location.reload();
            });
        }, 200);
    }
}
</script>

