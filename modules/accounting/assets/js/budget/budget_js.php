<script>
var workload,
data_budget,
columns,
nestedheaders;

(function($) {
"use strict";
  // Init datepickers
  columns = <?php echo new_html_entity_decode(json_encode($columns)); ?>;
  data_budget = <?php echo new_html_entity_decode(json_encode($data_budget)); ?>;
  nestedheaders = <?php echo new_html_entity_decode(json_encode($nestedheaders)); ?>;
  var workloadElement = document.querySelector('#workload');
  var workloadSettings = {
    data: <?php echo new_html_entity_decode(json_encode($data_budget)); ?>,
    columns: <?php echo new_html_entity_decode(json_encode($columns)); ?>,
    stretchH: 'all',
    autoWrapRow: true,
    rowHeaders: true,
    colHeaders: <?php echo new_html_entity_decode(json_encode($nestedheaders)); ?>,
      columnSorting: {
      indicator: true
    },
    licenseKey: 'non-commercial-and-evaluation',
    autoColumnSize: true,
    width: '100%',
    height: 400,
    dropdownMenu: true,
    mergeCells: true,
    contextMenu: true,
    manualRowMove: true,
    manualColumnMove: true,
    multiColumnSorting: {
      indicator: true
    },
     hiddenColumns: {
      columns: [1],
      indicators: true
    },
    filters: true,
    manualRowResize: true,
    manualColumnResize: true,
    comments: true,
      minRows: 10,
    
  };

  workload = new Handsontable(workloadElement, workloadSettings);

  $('.fc-wl-yearly-button').on('click', function () {

    $('input[name="view_type"]').val('yearly');
    $('.fc-wl-yearly-button').addClass('active');
    $('.fc-wl-quarterly-button').removeClass('active');
    $('.fc-wl-monthly-button').removeClass('active');

    get_data_budget();
  });

  $('.fc-wl-quarterly-button').on('click', function () {

    $('input[name="view_type"]').val('quarterly');
    $('.fc-wl-yearly-button').removeClass('active');
    $('.fc-wl-quarterly-button').addClass('active');
    $('.fc-wl-monthly-button').removeClass('active');

    get_data_budget();
  });

  $('.fc-wl-monthly-button').on('click', function () {

    $('input[name="view_type"]').val('monthly');
    $('.fc-wl-yearly-button').removeClass('active');
    $('.fc-wl-quarterly-button').removeClass('active');
    $('.fc-wl-monthly-button').addClass('active');
    
    get_data_budget();
  });

  $('select[name="budget"]').on('change', function() {
    get_data_budget();
  });

  appValidateForm($('#budget-form'),{budget:'required'},manage_budget);

  <?php if(isset($hide_handson)){ ?>
    $('#workload').addClass('hide');
    $('.budget-notifi').removeClass('hide');

  <?php }else{ ?>
    $('#workload').removeClass('hide');
    $('.budget-notifi').addClass('hide');
  <?php } ?>

})(jQuery);

function get_data_budget() {
  "use strict";

  $('#budget-exists-modal').modal('hide');

  if($('select[name="budget"]').val() == '' || $('select[name="budget"]').val() == null){
    $('#workload').addClass('hide');
    $('.budget-notifi').removeClass('hide');
  }else{
    $('#workload').removeClass('hide');
    $('.budget-notifi').addClass('hide');
    var data = {};
    data.budget = $('select[name="budget"]').val();

    //show box loading
    var html = '';
    html += '<div class="Box">';
    html += '<span>';
    html += '<span></span>';
    html += '</span>';
    html += '</div>';
    $('#box-loading').html(html);

      data.view_type = $('input[name="view_type"]').val();
      $.post(admin_url + 'accounting/get_data_budget', data).done(function(response) {
        response = JSON.parse(response);
        data_budget = response.data_budget;
        columns = response.columns;
        nestedheaders = response.nestedheaders;

        workload.updateSettings({
          data: data_budget,
          columns: columns,
          colHeaders: nestedheaders,
          });

        //hide boxloading
        $('#box-loading').html('');
      });
  }

};

function creating_a_budget(){
    "use strict";

    $('#creating-modal').modal('show');
}


function year_and_type(){
    "use strict";
    
    $('#creating-modal').modal('hide');

    $('#year-and-type-modal').modal('show');
}

function data_source(){
    "use strict";
    var data = {};
    data.year = $('input[name="fiscal_year_for_this_budget"]').val();
    data.type = $('input[name=budget_type]:checked').val();

    $.post(admin_url + 'accounting/check_budget', data).done(function(response) {
      response = JSON.parse(response);

      if($.isNumeric(response.success)){
        $('select[name="budget"]').val(response.success).change();

        $('#year-and-type-modal').modal('hide');

        $('#budget-exists-modal').modal('show');
      }else{
        if($('input[name=budget_type]:checked').val() == 'profit_and_loss_accounts'){
          $('#year-and-type-modal').modal('hide');
          $('#data-source-modal').modal('show');
        }else{
          $('#year-and-type-modal').modal('hide');

          $('#finish-modal').modal('show');
        }
      }
    });


    
}

function previous_creating_a_budget(){
    "use strict";

    $('#year-and-type-modal').modal('hide');
    $('#creating-modal').modal('show');
}


function previous_year_and_type(){
    "use strict";

    $('#data-source-modal').modal('hide');
    $('#budget-exists-modal').modal('hide');
    $('#finish-modal').modal('hide');
    
    $('#year-and-type-modal').modal('show');
}

function new_budget(){
    "use strict";

    var data = {};
    data.year = $('input[name="fiscal_year_for_this_budget"]').val();
    data.data_source = $('input[name="data_source"]:checked').val();
    data.type = $('input[name=budget_type]:checked').val();

    $.post(admin_url + 'accounting/add_budget', data).done(function(response) {
      response = JSON.parse(response);

      if(response.success === true || response.success === 'true'){
          alert_float('success',response.message);

          var category = $('select[name="budget"]');
          category.append('<option value="'+response.id+'">'+response.name+'</option>');
          category.selectpicker('val',response.id);
          category.selectpicker('refresh');
      }

      get_data_budget();
      $('.budget-notifi').addClass('hide');
      $('#workload').removeClass('hide');
    });
    $('#data-source-modal').modal('hide');
    $('#finish-modal').modal('hide');

}

function save_budget(){
    "use strict";
    
    $('input[name="budget_data"]').val(JSON.stringify(workload.getData()));
      

    $('#budget-form').submit();
}

function clear_budget(){
    "use strict";

    if($('select[name="budget"]').val() == ''){
      $('#div_data').addClass('hide');
    }else{
      $('#div_data').removeClass('hide');
    }

    var data = {};
    data.budget = $('select[name="budget"]').val();
    data.clear = true;

    //show box loading
    var html = '';
    html += '<div class="Box">';
    html += '<span>';
    html += '<span></span>';
    html += '</span>';
    html += '</div>';
    $('#box-loading').html(html);

      data.view_type = $('input[name="view_type"]').val();
      $.post(admin_url + 'accounting/get_data_budget', data).done(function(response) {
        response = JSON.parse(response);
        data_budget = response.data_budget;
        columns = response.columns;
        nestedheaders = response.nestedheaders;

        workload.updateSettings({
          data: data_budget,
          columns: columns,
          colHeaders: nestedheaders,
          });

        //hide boxloading
        $('#box-loading').html('');
      });
}

function manage_budget(form) {
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function(response) {
        response = JSON.parse(response);

        if(response.success == true){
            alert_float('success',response.message);
        }

    });
    return false;
}

function update_budget(){
    "use strict";

    var data = {};
    data.budget = $('select[name="budget"]').val();
    data.name = $('input[name="name"]').val();

    $.post(admin_url + 'accounting/update_budget', data).done(function(response) {
      response = JSON.parse(response);

      if(response.success == true){
        var category = $('select[name="budget"]');
        category.find('option:selected').text($('input[name="name"]').val());
        category.selectpicker('refresh');
        get_data_budget();
      }else{
        get_data_budget();
      }

    });
}

function delete_budget() {
    if (confirm("Are you sure?")) {
    requestGetJSON(admin_url + 'accounting/delete_budget/'+$('select[name="budget"]').val()).done(function (response) {
        if(response.success == true){
          alert_float('success',response.message);

          var category = $('select[name="budget"]');
          category.find('option:selected').remove();
          category.selectpicker('refresh');
          get_data_budget();
        }else{
          alert_float('warning',response.message);

          get_data_budget();
        }

      });
    }
    return false;
}
</script>