(function(){
  "use strict";
	$("input[data-type='currency']").on({
      keyup: function() {
        formatCurrency($(this));
      },
      blur: function() {
        formatCurrency($(this), "blur");
      }
  });

})(jQuery);
function change_status(data){
  "use strict";
  $.post(admin_url+'fleet/admin_change_status',data).done(function(response){
   response = JSON.parse(response);
   if(response.success == true) {
    alert_float('success','Status changed');
    setTimeout(function(){location.reload();},1500);
  }

});
}

function create_invoice(id) {
    "use strict";
    if (confirm("Are you sure?")) {
      $.post(admin_url + 'fleet/create_invoice_by_booking/' + id).done(function(response) {
          response = JSON.parse(response);
          if (response.message != '') {
              alert_float('success', response.message);
              $('#invoice-number').text(response.invoice_number);
          } else {
              alert_float('danger');
          }
          $('#btn-create-invoice').addClass('hide');
      });
    }
}

function booking_status_mark_as(status, booking_id) {
	"use strict"; 
	
	var url = 'fleet/booking_status_mark_as/' + status + '/' + booking_id;
	$("body").append('<div class="dt-loader"></div>');

	requestGetJSON(url).done(function (response) {
		$("body").find('.dt-loader').remove();
		if (response.success === true || response.success == 'true') {
			alert_float('success','Status changed');
    		setTimeout(function(){location.reload();},1500);
		}
	});
}

function update_info(id) {
    "use strict";
    $('#info-modal').modal('show');
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