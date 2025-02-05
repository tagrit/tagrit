<script type="text/javascript">
var historiesParams = {
    "id": "[name='partid']",
    "type": "[name='type']",
};

(function($) {
	"use strict";

    $("input[data-type='currency']").on({
        keyup: function() {        
            formatCurrency($(this));
        },
        blur: function() { 
            formatCurrency($(this), "blur");
        }
    });

	appValidateForm($('.part-form'), {
        name: 'required',
        part_type_id: 'required',
        status: 'required',
    });

    $('.part-form-submiter').on('click', function() {
        var form = $('.part-form');
        if (form.valid()) {
            form.find('.additional').html('');
            form.submit();
        }
    });

    init_part_histories_table();
})(jQuery);

function init_part_histories_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-part-histories')) {
    $('.table-part-histories').DataTable().destroy();
  }
  initDataTable('.table-part-histories', admin_url + 'fleet/part_histories_table', [0], [0], historiesParams, [1, 'desc']);
}

/**
 * format Number
 */
 function formatNumber(n) {
    "use strict";
    return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
 }

/**
 * format Currency
 */
 function formatCurrency(input, blur) {
    "use strict";
    var input_val = input.val();
    if (input_val === "") { return; }
    var original_len = input_val.length;
    var caret_pos = input.prop("selectionStart");
    if (input_val.indexOf(".") >= 0) {
        var decimal_pos = input_val.indexOf(".");
        var left_side = input_val.substring(0, decimal_pos);
        var right_side = input_val.substring(decimal_pos);
        left_side = formatNumber(left_side);

        right_side = formatNumber(right_side);
        right_side = right_side.substring(0, 2);
        input_val = left_side + "." + right_side;

    } else {
        input_val = formatNumber(input_val);
        input_val = input_val;
    }
    input.val(input_val);
    var updated_len = input_val.length;
    caret_pos = updated_len - original_len + caret_pos;
    input[0].setSelectionRange(caret_pos, caret_pos);
 }

</script>