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
	    init_fixed_equipment_table();
	});

	$('input[name="from_date"]').on('change', function() {
		init_fixed_equipment_table();
	});

	$('input[name="to_date"]').on('change', function() {
		init_fixed_equipment_table();
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
  init_fixed_equipment_table();
  
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
        
        if(response.debit != 0){
          $('select[name="deposit_to"]').val(response.debit).change();
        }

        if(response.credit != 0){
          $('select[name="payment_account"]').val(response.credit).change();
        }

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
            init_fixed_equipment_table();
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
            init_fixed_equipment_table();
        }else{
          alert_float('danger', response.message);
        }
        $('#convert-modal').modal('hide');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    return false;
}

function init_fixed_equipment_table() {
"use strict";

  if ($.fn.DataTable.isDataTable('.table-assets')) {
    $('.table-assets').DataTable().destroy();
  }
  initDataTable('.table-assets', admin_url + 'accounting/fe_assets_table', [0], [0], fnServerParams, [1, 'desc']);

  if ($.fn.DataTable.isDataTable('.table-licenses')) {
    $('.table-licenses').DataTable().destroy();
  }
  initDataTable('.table-licenses', admin_url + 'accounting/fe_licenses_table', [0], [0], fnServerParams, [1, 'desc']);

  if ($.fn.DataTable.isDataTable('.table-consumables')) {
    $('.table-consumables').DataTable().destroy();
  }
  initDataTable('.table-consumables', admin_url + 'accounting/fe_consumables_table', [0], [0], fnServerParams, [1, 'desc']);

  if ($.fn.DataTable.isDataTable('.table-components')) {
    $('.table-components').DataTable().destroy();
  }
  initDataTable('.table-components', admin_url + 'accounting/fe_components_table', [0], [0], fnServerParams, [1, 'desc']);

  if ($.fn.DataTable.isDataTable('.table-maintenances')) {
    $('.table-maintenances').DataTable().destroy();
  }
  initDataTable('.table-maintenances', admin_url + 'accounting/fe_maintenances_table', [0], [0], fnServerParams, [1, 'desc']);

  if ($.fn.DataTable.isDataTable('.table-depreciations')) {
    $('.table-depreciations').DataTable().destroy();
  }
  initDataTable('.table-depreciations', admin_url + 'accounting/fe_depreciations_table', [0], [0], fnServerParams, [1, 'desc']);
}

// purchase_order bulk actions action
function bulk_action(event) {
  "use strict";
    if (confirm_delete()) {
        var ids = [],
            data = {};
            data.type = $('input[name="bulk_actions_type"]').val();
            data.mass_convert = $('#mass_convert').prop('checked');
            data.mass_delete_convert = $('#mass_delete_convert').prop('checked');

        if($('input[name="bulk_actions_type"]').val() == 'fe_asset'){
          var rows = $($('#assets_bulk_actions').attr('data-table')).find('tbody tr');
        }else if($('input[name="bulk_actions_type"]').val() == 'fe_license'){
          var rows = $($('#licenses_bulk_actions').attr('data-table')).find('tbody tr');
        }else if($('input[name="bulk_actions_type"]').val() == 'fe_component'){
          var rows = $($('#components_bulk_actions').attr('data-table')).find('tbody tr');
        }else if($('input[name="bulk_actions_type"]').val() == 'fe_consumable'){
          var rows = $($('#consumables_bulk_actions').attr('data-table')).find('tbody tr');
        }else if($('input[name="bulk_actions_type"]').val() == 'fe_maintenance'){
          var rows = $($('#maintenances_bulk_actions').attr('data-table')).find('tbody tr');
        }else if($('input[name="bulk_actions_type"]').val() == 'fe_depreciation'){
          var rows = $($('#depreciations_bulk_actions').attr('data-table')).find('tbody tr');
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