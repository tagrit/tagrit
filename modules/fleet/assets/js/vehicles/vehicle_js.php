<script type="text/javascript">
var inspectionsParams;
var maintenancesParams;
var fuelParams;
var partParams;
var insuranceParams;
var assignmentsParams = {
    "id": "[name='vehicleid']",
};
var documentsParams = {
    'vehicleid': '[name="vehicleid"]',
};
(function($) {
	"use strict";
    maintenancesParams = {
        "id": "[name='vehicleid']",
        "vehicle_id": "[name='vehicle_id']",
        "maintenance_type": "[name='maintenance_type_filter']",
        "from_date": "[name='from_date_filter']",
        "to_date": "[name='to_date_filter']"
    }

    $('select[name="maintenance_type_filter"], input[name="from_date_filter"], input[name="to_date_filter"]').change(function(){
        $('.table-maintenances').DataTable().ajax.reload();
    });
    appValidateForm($('#maintenances-form'), {
        'vehicle_id': 'required',
        'garage_id': 'required',
        'maintenance_type': 'required',
        'start_date': 'required',
        'title': 'required'
    }, maintenances_form_handler);

    $("input[data-type='currency']").on({
        keyup: function() {        
            formatCurrency($(this));
        },
        blur: function() { 
            formatCurrency($(this), "blur");
        }
    });

	appValidateForm($('.vehicle-form'), {
        name: 'required',
        vehicle_type_id: 'required',
        ownership: 'required',
		status: 'required',
    });

    appValidateForm($('#vehicle-assignment-form'), {
      vehicle_id: 'required',
      driver_id: 'required',
    },vehicle_assignment_form_handler);

    init_vehicle_assignments_table();

    $(document).on("click","#mass_select_all",function() {
        var favorite = [];
        if($(this).is(':checked')){
            $('.individual').prop('checked', true);
            $.each($(".individual"), function(){ 
                favorite.push($(this).data('id'));
            });
        }else{
            $('.individual').prop('checked', false);
            favorite = [];
        }

        $("input[name='check']").val(favorite);
    });

    appValidateForm($('#fuel-form'), {
      vehicle_id: 'required',
      fuel_time: 'required',
      gallons: 'required',
      price: 'required',
    },fuel_form_handler);

    fuelParams = {
      "id": '[name="vehicleid"]',
      "fuel_type": '[name="_fuel_type"]',
      "from_date": '[name="from_date"]',
      "to_date": '[name="to_date"]',
    };

    $('.add-new-fuel').on('click', function(){
        $('#fuel-modal').find('button[type="submit"]').prop('disabled', false);
      $('#fuel-modal').modal('show');
      $('input[name="id"]').val('');
      $('select[name="vehicle_id"]').val('').change();
      $('select[name="vendor_id"]').val('').change();
      $('select[name="fuel_type"]').val('').change();
      $('input[name="fuel_time"]').val('');
      $('input[name="odometer"]').val('');
      $('input[name="gallons"]').val('');
      $('input[name="price"]').val('');
      $('input[name="reference"]').val('');
      $('textarea[name="notes"]').val('');
    });

    $('select[name="_fuel_type"]').on('change', function() {
      init_fuel_table();
    });

    $('input[name="from_date"]').on('change', function() {
      init_fuel_table();
    });

    $('input[name="to_date"]').on('change', function() {
      init_fuel_table();
    });

    init_fuel_table();

    init_maintenances_table();

    inspectionsParams = {
        "id": "[name='vehicleid']",
        "from_date": "[name='from_date_filter']",
        "to_date": "[name='to_date_filter']"
    }
    initDataTable('.table-inspections', admin_url + 'fleet/inspections_table', '', '', inspectionsParams, [1, 'desc']);
    $('input[name="from_date_filter"], input[name="to_date_filter"]').change(function(){
        $('.table-inspections').DataTable().ajax.reload();
    });

    appValidateForm($('#inspections-form'), {
        'vehicle_id': 'required',
        'inspection_form_id': 'required',
    });

    $('select[name="inspection_form_id"]').on('change', function() {
        var id = $('#add_new_inspections input[name="id"]').val();
        requestGet('fleet/get_inspection_form_content/' + $(this).val()+'/'+id).done(function(response) {
            $('.inspection-form-content').html(response);

             var survey_fields_required = $('#inspections-form').find('[data-required="1"]');
             $.each(survey_fields_required, function() {
               $(this).rules("add", {
                 required: true
               });
               var name = $(this).data('for');
               var label = $(this).parents('.form-group').find('[for="' + name + '"]');
               if (label.length > 0) {
                 if (label.find('.req').length == 0) {
                   label.prepend(' <small class="req text-danger">* </small>');
                 }
               }
             });
        });
    });

    $('.vehicle-form-submiter').on('click', function() {
        var form = $('.vehicle-form');
        if (form.valid()) {
            form.find('.additional').html('');
            form.submit();
        }
    });

    appValidateForm($('#insurance-form'), {
        vehicle_id: 'required',
        name: 'required',
      start_date: 'required',
      end_date: 'required',
      amount: 'required',
      insurance_company_id: 'required',
      insurance_status_id: 'required',
    },insurances_form_handler);

    insuranceParams = {
      "vehicleid": '[name="vehicleid"]',
      "status": '[name="status"]',
      "from_date": '[name="from_date"]',
      "to_date": '[name="to_date"]',
    };

    $('input[name="from_date_filter"]').on('change', function() {
      init_insurances_table();
    });

    $('input[name="to_date_filter"]').on('change', function() {
      init_insurances_table();
    });

    init_insurances_table();

    init_driver_documents_table();

    /* Customer profile reminders table */
    initDataTable('.table-reminders', admin_url + 'misc/get_reminders/' + $('input[name="vehicleid"]').val() + '/' + 'vehicle',
        undefined, undefined, undefined, [1, 'asc']);

    partParams = {
        'vehicleid': '[name="vehicleid"]',
      "status": '[name="status"]',
      "type": '[name="type"]',
      "group": '[name="to_date"]',
    };


    $('select[name="status"]').on('change', function() {
      init_part_table();
    });

    $('select[name="type"]').on('change', function() {
      init_part_table();
    });

    $('select[name="group"]').on('change', function() {
      init_part_table();
    });

    init_part_table();
})(jQuery);

function init_driver_documents_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-driver-documents')) {
    $('.table-driver-documents').DataTable().destroy();
  }
  initDataTable('.table-driver-documents', admin_url + 'fleet/driver_documents_table', [0], [0], documentsParams, [1, 'desc']);
}

function init_maintenances_table() {
    "use strict";

    if ($.fn.DataTable.isDataTable('.table-maintenances')) {
        $('.table-maintenances').DataTable().destroy();
    }

    initDataTable('.table-maintenances', admin_url + 'fleet/maintenances_table', '', '', maintenancesParams, [1, 'desc']);
}

function init_fuel_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-fuel')) {
    $('.table-fuel').DataTable().destroy();
  }
  initDataTable('.table-fuel', admin_url + 'fleet/fuel_history_table', [0], [0], fuelParams, [1, 'desc']);
  $('.dataTables_filter').addClass('hide');
}

function init_vehicle_assignments_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-vehicle-assignments')) {
    $('.table-vehicle-assignments').DataTable().destroy();
  }
  initDataTable('.table-vehicle-assignments', admin_url + 'fleet/vehicle_assignments_table', [0], [0], assignmentsParams, [1, 'desc']);
}

/**
 * add vehicle assign
 */
 function add_vehicle_assignment(){
    "use strict";
    $('#vehicle-assignment-modal').find('button[type="submit"]').prop('disabled', false);
    $('#vehicle-assignment-modal').modal('show');
    $('#vehicle-assignment-modal .add-title').removeClass('hide');
    $('#vehicle-assignment-modal .edit-title').addClass('hide');
    $('#vehicle-assignment-modal input[name="id"]').val('');
    $('#vehicle-assignment-modal input[name="start_time"]').val('');
    $('#vehicle-assignment-modal input[name="starting_odometer"]').val('');
    $('#vehicle-assignment-modal input[name="end_time"]').val('');
    $('#vehicle-assignment-modal input[name="ending_odometer"]').val('');
    $('#vehicle-assignment-modal select[name="driver_id"]').val('').change();
 }

/**
 * edit
 */
 function edit_vehicle_assignment(id){
    "use strict";
    $('#vehicle-assignment-modal').find('button[type="submit"]').prop('disabled', false);
    $('#vehicle-assignment-modal').modal('show');
    $('#vehicle-assignment-modal .add-title').addClass('hide');
    $('#vehicle-assignment-modal .edit-title').removeClass('hide');
    $('#vehicle-assignment-modal input[name="id"]').val(id);
    var requestURL = admin_url+'fleet/get_data_vehicle_assignment/' + (typeof(id) != 'undefined' ? id : '');
    requestGetJSON(requestURL).done(function(response) {

        $('#vehicle-assignment-modal select[name="vehicle_id"]').val(response.vehicle_id).change();
        $('#vehicle-assignment-modal select[name="driver_id"]').val(response.driver_id).change();
        $('#vehicle-assignment-modal input[name="start_time"]').val(response.start_time);
        $('#vehicle-assignment-modal input[name="starting_odometer"]').val(response.starting_odometer);
        $('#vehicle-assignment-modal input[name="end_time"]').val(response.end_time);
        $('#vehicle-assignment-modal input[name="ending_odometer"]').val(response.ending_odometer);

    }).fail(function(data) {
        alert_float('danger', 'Error');
    });
 }


function vehicle_assignment_form_handler(form) {
    "use strict";
    $('#vehicle-assignment-modal').find('button[type="submit"]').prop('disabled', true);

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
          init_vehicle_assignments_table();
        }else {
          alert_float('danger', response.message);
        }
        
        $('#vehicle-assignment-modal').modal('hide');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    return false;
}

/**
 * add asset
 */
 function add_inspections(){
    "use strict";
        $('#add_new_inspections').find('button[type="submit"]').prop('disabled', false);
    $('#add_new_inspections').modal('show');
    $('#add_new_inspections .add-title').removeClass('hide');
    $('#add_new_inspections .edit-title').addClass('hide');
    $('#add_new_inspections input[name="id"]').val('');
    $('#add_new_inspections select').val('').change();
 }

/**
 * edit
 */
 function edit_inspections(id){
    "use strict";
        $('#add_new_inspections').find('button[type="submit"]').prop('disabled', false);
    $('#add_new_inspections').modal('show');
    $('#add_new_inspections .add-title').addClass('hide');
    $('#add_new_inspections .edit-title').removeClass('hide');
    $('#add_new_inspections input[name="id"]').val(id);
    var requestURL = admin_url+'fleet/get_data_inspections/' + (typeof(id) != 'undefined' ? id : '');
    requestGetJSON(requestURL).done(function(response) {

        $('select[name="vehicle_id"]').val(response.vehicle_id).change();
        $('select[name="inspection_form_id"]').val(response.inspection_form_id).change();

    }).fail(function(data) {
        alert_float('danger', 'Error');
    });
 }

/**
 * add asset maintenances
 */
 function add_maintenances(){
    "use strict";
        $('#add_new_maintenances').find('button[type="submit"]').prop('disabled', false);
    $('#add_new_maintenances').modal('show');
    $('#add_new_maintenances .add-title').removeClass('hide');
    $('#add_new_maintenances .edit-title').addClass('hide');
    $('#add_new_maintenances input[name="id"]').val('');
    $('#add_new_maintenances input[type="text"]').val('');
    $('#add_new_maintenances select').val('').change();
    $('#add_new_maintenances textarea').val('');
    $('input[name="cost"]').val('');
    $('#add_new_maintenances input[type="checkbox"]').prop('checked', false);
 }

/**
 * edit maintenances
 */
 function edit_maintenances(id){
    "use strict";
        $('#add_new_maintenances').find('button[type="submit"]').prop('disabled', false);
    $('#add_new_maintenances').modal('show');
    $('#add_new_maintenances .add-title').addClass('hide');
    $('#add_new_maintenances .edit-title').removeClass('hide');
    $('#add_new_maintenances input[name="id"]').val(id);
    var requestURL = admin_url+'fleet/get_data_maintenances/' + (typeof(id) != 'undefined' ? id : '');
    requestGetJSON(requestURL).done(function(response) {

        $('select[name="vehicle_id"]').val(response.vehicle_id).change();
        $('select[name="garage_id"]').val(response.garage_id).change();
        $('select[name="maintenance_type"]').val(response.maintenance_type).change();

        if(response.parts){
         $('select[name="parts[]"]').val(response.parts.split(',')).change();
        } else {
         $('select[name="parts[]"]').val('').change();
        }

        $('input[name="title"]').val(response.title);
        $('input[name="start_date"]').val(response.start_date);
        $('input[name="completion_date"]').val(response.completion_date);
        $('input[name="cost"]').val(response.cost);
        $('textarea[name="notes"]').val(response.notes);

        if(response.warranty_improvement == 1){
            $('input[name="warranty_improvement"]').prop('checked', true);
        }
        else{
            $('input[name="warranty_improvement"]').prop('checked', false);
        }
    }).fail(function(data) {
        alert_float('danger', 'Error');
    });
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

 function bulk_delete(){
    "use strict";
    var print_id = $('input[name="check"]').val();
    if(print_id != ''){
        if(confirm($('input[name="are_you_sure_you_want_to_delete_these_items"]').val()) == true){
            window.location.href = admin_url+"fixed_equipment/delete_all_maintenance/"+encodeURIComponent(print_id);
        }
    }
    else{
        alert_float('danger', $('input[name="please_select_at_least_one_item_from_the_list"]').val());
    }
 }

 function checked_add(el){
    "use strict";
    var id = $(el).data("id");
    var id_product = $(el).data("product");
    if ($(".individual").length == $(".individual:checked").length) {
        $("#mass_select_all").attr("checked", "checked");
        var value = $("input[name='check']").val();
        if(value != ''){
            value = value + ',' + id;
        }else{
            value = id;
        }
    } else {
        $("#mass_select_all").removeAttr("checked");
        var value = $("input[name='check']").val();
        var arr_val = value.split(',');
        if(arr_val.length > 0){
            $.each( arr_val, function( key, value ) {
                if(value == id){
                    arr_val.splice(key, 1);
                    value = arr_val.toString();
                    $("input[name='check']").val(value);
                }
            });
        }
    }
    if($(el).is(':checked')){
        var value = $("input[name='check']").val();
        if(value != ''){
            value = value + ',' + id;
        }else{
            value = id;
        }
        $("input[name='check']").val(value);
    }else{
        var value = $("input[name='check']").val();
        var arr_val = value.split(',');
        if(arr_val.length > 0){
            $.each( arr_val, function( key, value ) {
                if(value == id){
                    arr_val.splice(key, 1);
                    value = arr_val.toString();
                    $("input[name='check']").val(value);
                }
            });
        }
    }
 }

function edit_fuel(id) {
  "use strict";
    $('#fuel-modal').find('button[type="submit"]').prop('disabled', false);

  requestGetJSON(admin_url + 'fleet/get_data_fuel/'+id).done(function(response) {
      $('#fuel-modal').modal('show');

      $('select[name="vehicle_id"]').val(response.vehicle_id).change();
      $('select[name="vendor_id"]').val(response.vendor_id).change();
      $('select[name="fuel_type"]').val(response.fuel_type).change();
      $('input[name="fuel_time"]').val(response.fuel_time);
      $('input[name="id"]').val(id);
      $('input[name="odometer"]').val(response.odometer);
      $('input[name="gallons"]').val(response.gallons);
      $('input[name="price"]').val(response.price);
      $('input[name="reference"]').val(response.reference);
      $('textarea[name="notes"]').val(response.notes);

  });
}

function fuel_form_handler(form) {
    "use strict";
    $('#fuel-modal').find('button[type="submit"]').prop('disabled', true);

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
        $('#fuel-modal').modal('hide');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    return false;
}


function maintenances_form_handler(form) {
    "use strict";
    $('#add_new_maintenances').find('button[type="submit"]').prop('disabled', true);

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
            init_maintenances_table();
        }else {
          alert_float('danger', response.message);
        }
        $('#add_new_maintenances').modal('hide');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    return false;
}


function edit_insurance(id) {
  "use strict";
    $('#insurance-modal').find('button[type="submit"]').prop('disabled', false);

  requestGetJSON(admin_url + 'fleet/get_data_insurance/'+id).done(function(response) {
      $('#insurance-modal').modal('show');

      $('#insurance-modal input[name="id"]').val(id);
      $('#insurance-modal select[name="vehicle_id"]').val(response.vehicle_id).change();
      $('#insurance-modal select[name="insurance_category_id"]').val(response.insurance_category_id).change();
      $('#insurance-modal select[name="insurance_type_id"]').val(response.insurance_type_id).change();
      $('#insurance-modal select[name="insurance_company_id"]').val(response.insurance_company_id).change();
      $('#insurance-modal select[name="insurance_status_id"]').val(response.insurance_status_id).change();
      $('#insurance-modal input[name="name"]').val(response.name);
      $('#insurance-modal input[name="amount"]').val(response.amount);
      $('#insurance-modal input[name="start_date"]').val(response.start_date);
      $('#insurance-modal input[name="end_date"]').val(response.end_date);
      $('#insurance-modal textarea[name="description"]').val(response.description);

  });
}


function init_insurances_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-insurances')) {
    $('.table-insurances').DataTable().destroy();
  }
  initDataTable('.table-insurances', admin_url + 'fleet/insurances_table', [0], [0], insuranceParams, [1, 'desc']);
  $('.dataTables_filter').addClass('hide');
}

function insurances_form_handler(form) {
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
                init_insurances_table();
        }else {
          alert_float('danger', response.message);
        }
        $('#insurance-modal').modal('hide');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    return false;
}

function add_insurances(id) {
  "use strict";
  
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
}

function init_part_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-parts')) {
    $('.table-parts').DataTable().destroy();
  }
  initDataTable('.table-parts', admin_url + 'fleet/parts_table', [0], [0], partParams, [1, 'desc']);
  $('.dataTables_filter').addClass('hide');
}
</script>