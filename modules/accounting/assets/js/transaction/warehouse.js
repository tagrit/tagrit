var fnServerParams = {};
var id, type, amount;

(function($) {
	"use strict";

	fnServerParams = {
      "status": '[name="status"]',
      "from_date": '[name="from_date"]',
      "to_date": '[name="to_date"]',
    };
    
	appValidateForm($('#convert-form'), {
	      
	      },convert_form_handler);

	$('select[name="status"]').on('change', function() {
	    init_warehouse_table();
	});

	$('input[name="from_date"]').on('change', function() {
		init_warehouse_table();
	});

	$('input[name="to_date"]').on('change', function() {
		init_warehouse_table();
	});

  $('input[name="mass_convert"]').on('change', function() {
    if($('#mass_convert').is(':checked') == true){
      $('#mass_delete_convert').prop( "checked", false );
    }
  });

  $('input[name="mass_delete_convert"]').on('change', function() {
    if($('#mass_delete_convert').is(':checked') == true){
      $('#mass_convert').prop( "checked", false );
    }
  });

  

  init_warehouse_table();
  
})(jQuery);

function convert(invoker){
    "use strict";
    $('#convert-modal').find('button[id="btn_account_history"]').prop('disabled', false);

    id = $(invoker).data('id');
    type = $(invoker).data('type');
    amount = $(invoker).data('amount');

    $('input[name="id"]').val(id);
    $('input[name="type"]').val(type);
    $('input[name="amount"]').val(amount);

    requestGet('accounting/get_data_convert/' + id + '/' + type).done(function(response) {
        response = JSON.parse(response);

        $('#div_info').html(response.html);

        init_selectpicker();
        $('#payment_account_insurance').selectpicker('refresh');
        $('#deposit_to_insurance').selectpicker('refresh');

        $('#payment_account_tax_paye').selectpicker('refresh');
        $('#deposit_to_tax_paye').selectpicker('refresh');

        $('#payment_account_net_pay').selectpicker('refresh');
        $('#deposit_to_net_pay').selectpicker('refresh');

        $('input[name^="profit_mapping"]').on('change', function() {
            if($(this).is(':checked') == true){
              $('#div_profit_mapping_'+$(this).data('id')).removeClass('hide');
            }else{
              $('#div_profit_mapping_'+$(this).data('id')).addClass('hide');
            }
        });
    });

  $('#convert-modal').modal('show');
}

function delete_convert(id,type) {
  "use strict";
    if (confirm("Are you sure?")) {
      var url = admin_url + 'accounting/delete_convert/'+id+'/'+type;

      requestGet(url).done(function(response){
          response = JSON.parse(response);
          if (response.success === true || response.success == 'true') { 
            alert_float('success', response.message); 
            init_warehouse_table();
          }else{
            alert_float('danger', response.message); 
          }
      });
    }
    return false;
}

function convert_form_handler(form) {
    "use strict";
    $('#convert-modal').find('button[id="btn_account_history"]').prop('disabled', true);

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
            init_warehouse_table();
        }else{
          alert_float('danger', response.message);
        }
        $('#convert-modal').modal('hide');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    return false;
}

function init_warehouse_table() {
"use strict";

  if ($.fn.DataTable.isDataTable('.table-stock-import')) {
    $('.table-stock-import').DataTable().destroy();
  }
  initDataTable('.table-stock-import', admin_url + 'accounting/stock_import_table', [0], [0], fnServerParams, [1, 'desc']);

  if ($.fn.DataTable.isDataTable('.table-stock-export')) {
    $('.table-stock-export').DataTable().destroy();
  }
  initDataTable('.table-stock-export', admin_url + 'accounting/stock_export_table', [0], [0], fnServerParams, [1, 'desc']);

  if ($.fn.DataTable.isDataTable('.table-loss-adjustment')) {
    $('.table-loss-adjustment').DataTable().destroy();
  }
  initDataTable('.table-loss-adjustment', admin_url + 'accounting/loss_adjustment_table', [0], [0], fnServerParams, [1, 'desc']);

  if ($.fn.DataTable.isDataTable('.table-opening-stock')) {
    $('.table-opening-stock').DataTable().destroy();
  }
  initDataTable('.table-opening-stock', admin_url + 'accounting/opening_stock_table', [0], [0], fnServerParams, [1, 'desc']);

}

// stock_import bulk actions action
function bulk_action(event) {
  "use strict";
    if (confirm_delete()) {
        var ids = [],
            data = {};
            data.type = $('input[name="bulk_actions_type"]').val();
            data.mass_convert = $('#mass_convert').prop('checked');
            data.mass_delete_convert = $('#mass_delete_convert').prop('checked');

        if($('input[name="bulk_actions_type"]').val() == 'stock_import'){
          var rows = $($('#stock_import_bulk_actions').attr('data-table')).find('tbody tr');
        }else if($('input[name="bulk_actions_type"]').val() == 'stock_export'){
          var rows = $($('#stock_export_bulk_actions').attr('data-table')).find('tbody tr');
        }else if($('input[name="bulk_actions_type"]').val() == 'loss_adjustment'){
          var rows = $($('#loss_adjustment_bulk_actions').attr('data-table')).find('tbody tr');
        }else if($('input[name="bulk_actions_type"]').val() == 'opening_stock'){
          var rows = $($('#opening_stock_bulk_actions').attr('data-table')).find('tbody tr');
        }

        $.each(rows, function() {
            var checkbox = $($(this).find('td').eq(0)).find('input');
            if (checkbox.prop('checked') === true) {
                ids.push(checkbox.val());
            }
        });
        data.ids = ids;
        $(event).addClass('disabled');
        setTimeout(function() {
            $.post(admin_url + 'accounting/transaction_bulk_action', data).done(function() {
                window.location.reload();
            });
        }, 200);
    }
}