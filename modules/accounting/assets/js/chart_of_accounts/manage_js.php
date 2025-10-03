<script type="text/javascript">
	var list_account_type_details, fnServerParams;
	(function($) {
		"use strict";

		appValidateForm($('#account-form'), {
			account_type_id: 'required',
			account_detail_type_id: 'required',
			name: 'required',
    	},account_form_handler);

		fnServerParams = {
      "ft_type": '[name="ft_type"]',
      "ft_detail_type": '[name="ft_detail_type"]',
      "ft_parent_account": '[name="ft_parent_account"]',
      "ft_account": '[name="ft_account"]',
      "ft_active": '[name="ft_active"]',
    };
    $('select[name="ft_type"]').on('change', function() {
      init_account_table();
    });
    $('select[name="ft_active"]').on('change', function() {
      init_account_table();
    });
    $('select[name="ft_detail_type"]').on('change', function() {
      init_account_table();
    });

    $('select[name="ft_parent_account"]').on('change', function() {
      init_account_table();
    });
    
    $('select[name="ft_account"]').on('change', function() {
      init_account_table();
    });

	 	list_account_type_details = <?php echo json_encode($detail_types); ?>;

		  $('.add-new-account').on('click', function(){
          if($('select[name="account_type_id"]').val() <= 10 && $('select[name="account_type_id"]').val() != 1 && $('select[name="account_type_id"]').val() != 6){
            $('#div_balance').removeClass('hide');
          }else{
            $('#div_balance').addClass('hide');
          }

          if($('select[name="account_type_id"]').val() == 3 && $('select[name="account_detail_type_id"]').val() == 14){
            $('#div_bank_info').removeClass('hide');
          }else{
            $('#div_bank_info').addClass('hide');
          }

          $('#account-modal').find('button[type="submit"]').prop('disabled', false);

          $('select[name="parent_account"]').val('').change();

          $('input[name="name"]').val('');
          $('input[name="balance"]').val('');
          $('input[name="balance_as_of"]').val('');

          tinyMCE.activeEditor.setContent('');
          $('textarea[name="description"]').val('');
          $('input[name="id"]').val('');
	        $('#account-modal').modal('show');
	    });

    var html = '';
      var note = 0;
        $.each(list_account_type_details, function( index, value ) {
          if(value.account_type_id == $('select[name="account_type_id"]').val()){
            if(note == 0){
              $('#detail_type_note').val(value.note);
              note = 1;
            }
            html += '<option value="'+value.id+'">'+value.name+'</option>';
          }
      });

      $('select[name="account_detail_type_id"]').html(html);
      $('select[name="account_detail_type_id"]').selectpicker('refresh');

      $.each(list_account_type_details, function( index, value ) {
          if(value.id == $('select[name="account_detail_type_id"]').val()){
            $('.detail_type_note').html(value.note);
          }
      });

	 	init_account_table();

		$('select[name="account_type_id"]').on('change', function() {

      if($(this).val() <= 10 && $(this).val() != 1 && $(this).val() != 6 && $('input[name="id"]').val() == ''){
        $('#div_balance').removeClass('hide');
      }else{
        $('#div_balance').addClass('hide');
      }

      

			var html = '';
			var note = 0;
		  	$.each(list_account_type_details, function( index, value ) {
		  		if(value.account_type_id == $('select[name="account_type_id"]').val()){
		  			if(note == 0){
			  			$('#detail_type_note').val(value.note);
			  			note = 1;
		  			}
			  		html += '<option value="'+value.id+'">'+value.name+'</option>';
		  		}
			});

			$('select[name="account_detail_type_id"]').html(html);
			$('select[name="account_detail_type_id"]').selectpicker('refresh');

      $.each(list_account_type_details, function( index, value ) {
          if(value.id == $('select[name="account_detail_type_id"]').val()){
            $('.detail_type_note').html(value.note);
          }
      });

      if($('select[name="account_type_id"]').val() == 3 && $('select[name="account_detail_type_id"]').val() == 14){
        $('#div_bank_info').removeClass('hide');
      }else{
        $('#div_bank_info').addClass('hide');
      }
		});

	  	$('select[name="account_detail_type_id"]').on('change', function() {
        if($('select[name="account_type_id"]').val() == 3 && $('select[name="account_detail_type_id"]').val() == 14){
          $('#div_bank_info').removeClass('hide');
        }else{
          $('#div_bank_info').addClass('hide');
        }

	  		$.each(list_account_type_details, function( index, value ) {
		  		if(value.id == $('select[name="account_detail_type_id"]').val()){
			  		$('.detail_type_note').html(value.note);
		  		}
			});
	 	});

	$("input[data-type='currency']").on({
      keyup: function() {
        formatCurrency($(this));
      },
      blur: function() {
        formatCurrency($(this), "blur");
      }
  });

  $('input[name="mass_activate"]').on('change', function() {
    if($('#mass_activate').is(':checked') == true){
      $('#mass_delete').prop( "checked", false );
      $('#mass_deactivate').prop( "checked", false );
    }
  });

  $('input[name="mass_deactivate"]').on('change', function() {
    if($('#mass_deactivate').is(':checked') == true){
      $('#mass_delete').prop( "checked", false );
      $('#mass_activate').prop( "checked", false );
    }
  });

  $('input[name="mass_delete"]').on('change', function() {
    if($('#mass_delete').is(':checked') == true){
      $('#mass_activate').prop( "checked", false );
      $('#mass_deactivate').prop( "checked", false );
    }
  });

})(jQuery);

function edit_account(id) {
  "use strict";
    $('#account-modal').find('button[type="submit"]').prop('disabled', false);

  requestGetJSON(admin_url + 'accounting/get_data_account/'+id).done(function(response) {
      $('#account-modal').modal('show');

      $('select[name="account_type_id"]').val(response.account_type_id).change();
      $('select[name="account_detail_type_id"]').val(response.account_detail_type_id).change();
      if(response.parent_account != 0){
        $('select[name="parent_account"]').val(response.parent_account).change();
      }else{
        $('select[name="parent_account"]').val('').change();
      }
      $('input[name="number"]').val(response.number);
      $('input[name="name"]').val(response.name);
      $('input[name="id"]').val(id);
      $('input[name="balance"]').val(response.balance);
      $('input[name="balance_as_of"]').val(response.balance_as_of);
      $('input[name="bank_name"]').val(response.bank_name);
      $('input[name="bank_account"]').val(response.bank_account);
      $('input[name="bank_routing"]').val(response.bank_routing);
      $('input[name="address_line_1"]').val(response.address_line_1);

      if(response.description != null){
          tinyMCE.activeEditor.setContent(response.description);
      }else{
            tinyMCE.activeEditor.setContent('');
      }
      $('textarea[name="description"]').val(response.description);
      if(response.balance > 0){
        $('input[name="update_balance"]').val(0);
        $('#div_balance').addClass('hide');
      }else{
        $('input[name="update_balance"]').val(1);
        $('#div_balance').removeClass('hide');
      }
  });
}

function account_form_handler(form) {
    "use strict";
    $('#account-modal').find('button[type="submit"]').prop('disabled', true);
    tinyMCE.triggerSave();
    
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

	 		    init_account_table();
        }else{
          alert_float('danger', response.message);
        }
        $('#account-modal').modal('hide');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    return false;
}

function formatNumber(n) {
  "use strict";
  // format number 1000000 to 1,234,567
  return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
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
    var minus = input_val.substring(0, 1);
    if(minus != '-'){
      minus = '';
    }

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
    input_val = minus+left_side + "." + right_side;

  } else {
    // no decimal entered
    // add commas to number
    // remove all non-digits
    var minus = input_val.substring(0, 1);
    if(minus != '-'){
      minus = '';
    }
    input_val = formatNumber(input_val);
    input_val = minus+input_val;

  }

  // send updated string to input
  input.val(input_val);

  // put caret back in the right position
  var updated_len = input_val.length;
  caret_pos = updated_len - original_len + caret_pos;
  //input[0].setSelectionRange(caret_pos, caret_pos);
}

function init_account_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-accounts')) {
    $('.table-accounts').DataTable().destroy();
  }
  initDataTable('.table-accounts', admin_url + 'accounting/accounts_table', [0], [0,1,2,5,6,7,8], fnServerParams, []);
  $('.dataTables_filter').addClass('hide');
}

// journal entry bulk actions action
function bulk_action(event) {
  "use strict";
    if (confirm_delete()) {
        var ids = [],
            data = {};
            data.mass_delete = $('#mass_delete').prop('checked');
            data.mass_activate = $('#mass_activate').prop('checked');
            data.mass_deactivate = $('#mass_deactivate').prop('checked');

        var rows = $($('#accounts_bulk_actions').attr('data-table')).find('tbody tr');

        $.each(rows, function() {
            var checkbox = $($(this).find('td').eq(0)).find('input');
            if (checkbox.prop('checked') === true) {
                ids.push(checkbox.val());
            }
        });
        data.ids = ids;
        
        $(event).addClass('disabled');
        setTimeout(function() {
            $.post(admin_url + 'accounting/accounts_bulk_action', data).done(function() {
                window.location.reload();
            });
        }, 200);
    }
}
</script>
