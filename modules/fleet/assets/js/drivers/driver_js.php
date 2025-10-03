<script type="text/javascript">
  var documentsParams = {
    'driverid': '[name="driverid"]',
  };
	var assignmentsParams = {
    'driverid': '[name="driverid"]',
  };
  var table_education = $('table.table-table_education');

  var benefit_and_penaltyParams;
(function($) {
		"use strict";
    appValidateForm($('.save_update_education'), {
      training_programs_name: 'required',
      training_places: 'required',
      training_time_from: 'required',
      training_time_to: 'required',
    }, education_form_handler);

    appValidateForm($('#vehicle-assignment-form'), {
      vehicle_id: 'required',
      driver_id: 'required',
    },vehicle_assignment_form_handler);

    init_driver_documents_table();
    init_vehicle_assignments_table();

    appValidateForm($('#benefit_and_penalty-form'), {
      subject: 'required',
      type: 'required',
      date: 'required',
      driver_id: 'required',
    },benefit_and_penalty_form_handler);

    benefit_and_penaltyParams = {
      'driverid': '[name="driverid"]',
      "type": '[name="_type"]',
      "from_date": '[name="from_date"]',
      "to_date": '[name="to_date"]',
    };

    $('.add-new-benefit_and_penalty').on('click', function(){
    $('#benefit_and_penalty-modal').find('button[type="submit"]').prop('disabled', false);
      $('#benefit_and_penalty-modal').modal('show');
      $('input[name="id"]').val('');
      $('select[name="driver_id"]').val('').change();
      $('select[name="criteria_id"]').val('').change();
      $('input[name="subject"]').val('');
      $('input[name="date"]').val('');
      $('input[name="amount_of_damage"]').val('');
      $('input[name="amount_of_compensation"]').val('');
      $('input[name="reward"]').val('');
      $('textarea[name="notes"]').val('');
    });

    $('select[name="_type"]').on('change', function() {
      init_benefit_and_penalty_table();
    });

    $('input[name="from_date"]').on('change', function() {
      init_benefit_and_penalty_table();
    });

    $('input[name="to_date"]').on('change', function() {
      init_benefit_and_penalty_table();
    });



    $('select[name="type"]').on('change', function() {
      if($(this).val() == 'penalty'){
        $('.benefit_type').addClass('hide');
        $('.penalty_type').removeClass('hide');
      }else{
        $('.benefit_type').removeClass('hide');
        $('.penalty_type').addClass('hide');
      }
    });

    $('select[name="benefit_formality"]').on('change', function() {
      if($(this).val() == 'commend'){
        $('.benefit_amount_div').addClass('hide');
      }else{
        $('.benefit_amount_div').removeClass('hide');
      }
    });

    $('select[name="penalty_formality"]').on('change', function() {
      if($(this).val() == 'remind'){
        $('.penalty_amount_div').addClass('hide');
      }else{
        $('.penalty_amount_div').removeClass('hide');
      }
    });

    init_benefit_and_penalty_table();

  $("input[data-type='currency']").on({
      keyup: function() {
        formatCurrency($(this));
      },
      blur: function() {
        formatCurrency($(this), "blur");
      }
  });

    var staff_id = $('input[name="driverid"]').val();
    initDataTable(table_education,admin_url + 'hr_profile/table_education/'+staff_id, [0], [0], '');
})(jQuery);

function init_driver_documents_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-driver-documents')) {
    $('.table-driver-documents').DataTable().destroy();
  }
  initDataTable('.table-driver-documents', admin_url + 'fleet/driver_documents_table', [0], [0], documentsParams, [1, 'desc']);
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
    $('#vehicle-assignment-modal select[name="vehicle_id"]').val('').change();
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


function init_benefit_and_penalty_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-benefit_and_penalty')) {
    $('.table-benefit_and_penalty').DataTable().destroy();
  }
  initDataTable('.table-benefit_and_penalty', admin_url + 'fleet/benefit_and_penalty_table', [0], [0], benefit_and_penaltyParams, [1, 'desc']);
  $('.dataTables_filter').addClass('hide');
}

function edit_benefit_and_penalty(id) {
  "use strict";
    $('#benefit_and_penalty-modal').find('button[type="submit"]').prop('disabled', false);

  requestGetJSON(admin_url + 'fleet/get_data_benefit_and_penalty/'+id).done(function(response) {

      $('select[name="driver_id"]').val(response.driver_id).change();
      $('select[name="criteria_id"]').val(response.criteria_id).change();
      $('select[name="type"]').val(response.type).change();
      $('input[name="subject"]').val(response.subject);
      $('input[name="id"]').val(id);
      $('input[name="date"]').val(response.date);
      $('select[name="benefit_formality"]').val(response.benefit_formality).change();
      $('input[name="reward"]').val(response.reward);
      $('select[name="penalty_formality"]').val(response.penalty_formality).change();
      $('input[name="amount_of_damage"]').val(response.amount_of_damage);
      $('input[name="amount_of_compensation"]').val(response.amount_of_compensation);
      $('textarea[name="notes"]').val(response.notes);
      $('#benefit_and_penalty-modal').modal('show');

  });

}

function benefit_and_penalty_form_handler(form) {
    "use strict";
    $('#benefit_and_penalty-modal').find('button[type="submit"]').prop('disabled', true);

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
          init_benefit_and_penalty_table();
        }else {
          alert_float('danger', response.message);
        }
        $('#benefit_and_penalty-modal').modal('hide');
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

function create_trainings(){
    'use strict';
    $('#education_sidebar').find('button[type="submit"]').prop('disabled', false);
    $('#education_sidebar').modal('show');
    $('#education_sidebar input[name="id"]').val('');
    $('#education_sidebar input[name="training_programs_name"]').val('');
    $('#education_sidebar input[name="training_places"]').val('');
    $('#education_sidebar input[name="training_time_from"]').val('');
    $('#education_sidebar input[name="training_time_to"]').val('');
    $('#education_sidebar textarea[name="training_result"]').val('');
    $('#education_sidebar input[name="degree"]').val('');
    $('#education_sidebar textarea[name="notes"]').val('');
    $('#education_sidebar .education_sidebar').addClass('sidebar-open');
    $('#education_sidebar .edit-title-training').hide();
    $('#education_sidebar .add-title-training').show();
  }


  function delete_education(el){
    'use strict';
    var id = $(el).data('id');
    var table_education = $('table.table-table_education');

    $.post(admin_url+'hr_profile/delete_education',{'id':id}).done(function(response){
      response = JSON.parse(response);
      if(response.success == true) {
        alert_float('success',response.message);
        table_education.DataTable().ajax.reload();
      }
      else{
        alert_float('danger',response.message);
        table_education.DataTable().ajax.reload();
      }
    });
  }

  function update_education(el){
    'use strict';
    $('#education_sidebar').modal('show');
    $('#education_sidebar').find('button[type="submit"]').prop('disabled', false);
    var id = $(el).data('id');
    $('#education_sidebar input[name="id"]').val(id);
    $('#education_sidebar input[name="training_programs_name"]').val($(el).data('name_programe'));
    $('#education_sidebar input[name="training_places"]').val($(el).data('training_pl'));
    $('#education_sidebar input[name="training_time_from"]').val($(el).data('time_from'));
    $('#education_sidebar input[name="training_time_to"]').val($(el).data('time_to'));
    $('#education_sidebar input[name="degree"]').val($(el).data('degree'));
    $('#education_sidebar textarea[name="notes"]').val($(el).data('notes'));
    $('#education_sidebar .education_sidebar').addClass('sidebar-open');
    $('#education_sidebar .edit-title-training').show();
    $('#education_sidebar .add-title-training').hide();
    tinyMCE.activeEditor.setContent($(el).data('result'));
  }


function education_form_handler(form) {
    "use strict";
    $('#education_sidebar').find('button[type="submit"]').prop('disabled', true);

      tinymce.triggerSave();
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
          table_education.DataTable().ajax.reload();
        }else {
          alert_float('danger', response.message);
        }
        
        $('#education_sidebar').modal('hide');
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.mesage));
    });

    return false;
}
</script>