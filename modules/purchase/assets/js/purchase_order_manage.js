var hidden_columns = [2,3,4,5], table_rec_campaign;
Dropzone.autoDiscover = false;
var expenseDropzone;
(function($) {
"use strict"; 
    table_rec_campaign = $('.table-table_pur_order');

    var Params = {
        "from_date": 'input[name="from_date"]',
        "to_date": 'input[name="to_date"]',
        "vendor": "[name='vendor_ft[]']",
        "status": "[name='status[]']",
        "item_filter": "[name='item_filter[]']",
        "type": "[name='type[]']",
        "project": "[name='project[]']",
        "department": "[name='department[]']",
        "delivery_status": "[name='delivery_status[]']",
        "purchase_request": "[name='pur_request[]']"
    };

    initDataTable('.table-table_pur_order', admin_url+'purchase/table_pur_order', [], [], Params,[2, 'desc']);
	init_pur_order();
    $.each(Params, function(i, obj) {
        $('select' + obj).on('change', function() {  
            table_rec_campaign.DataTable().ajax.reload()
                .columns.adjust()
                .responsive.recalc();
        });
    });

    $('input[name="from_date"]').on('change', function() {
        table_rec_campaign.DataTable().ajax.reload()
                .columns.adjust()
                .responsive.recalc();
    });
    $('input[name="to_date"]').on('change', function() {
        table_rec_campaign.DataTable().ajax.reload()
                .columns.adjust()
                .responsive.recalc();
    });

    
    if ($('#pur_order-expense-form').length > 0) {
          expenseDropzone = new Dropzone("#pur_order-expense-form", appCreateDropzoneOptions({
              autoProcessQueue: false,
              clickable: '#dropzoneDragArea',
              previewsContainer: '.dropzone-previews',
              addRemoveLinks: true,
              maxFiles: 1,
              success: function(file, response) {
                  if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                      window.location.reload();
                  }
              }
        }));
    }

    appValidateForm($('#pur_order-expense-form'), {
          category: 'required',
          date: 'required',
          amount: 'required'
    }, projectExpenseSubmitHandler);


})(jQuery);
function init_pur_order(id) {
    "use strict";
    load_small_pur_order_table_item(id, '#pur_order', 'pur_orderid', 'purchase/get_pur_order_data_ajax', '.table-table_pur_order');
}
function load_small_pur_order_table_item(id, selector, input_name, url, table) {
    "use strict";
    var _tmpID = $('input[name="' + input_name + '"]').val();
    // Check if id passed from url, hash is prioritized becuase is last
    if (_tmpID !== '' && !window.location.hash) {
        id = _tmpID;
        // Clear the current id value in case user click on the left sidebar credit_note_ids
        $('input[name="' + input_name + '"]').val('');
    } else {
        // check first if hash exists and not id is passed, becuase id is prioritized
        if (window.location.hash && !id) {
            id = window.location.hash.substring(1); //Puts hash in variable, and removes the # character
        }
    }
    if (typeof(id) == 'undefined' || id === '') { return; }
    destroy_dynamic_scripts_in_element($(selector))
    if (!$("body").hasClass('small-table')) { toggle_small_pur_order_view(table, selector); }
    $('input[name="' + input_name + '"]').val(id);
    do_hash_helper(id);
    $(selector).load(admin_url + url + '/' + id);
    if (is_mobile()) {
        $('html, body').animate({
            scrollTop: $(selector).offset().top + 150
        }, 600);
    }
}

function toggle_small_pur_order_view(table, main_data) {
        "use strict";
        $("body").toggleClass('small-table');
        var tablewrap = $('#small-table');
        if (tablewrap.length === 0) { return; }
        var _visible = false;
        if (tablewrap.hasClass('col-md-5')) {
            tablewrap.removeClass('col-md-5').addClass('col-md-12');
            _visible = true;
            $('.toggle-small-view').find('i').removeClass('fa fa-angle-double-right').addClass('fa fa-angle-double-left');
        } else {
            tablewrap.addClass('col-md-5').removeClass('col-md-12');
            $('.toggle-small-view').find('i').removeClass('fa fa-angle-double-left').addClass('fa fa-angle-double-right');
        }
        var _table = $(table).DataTable();
        // Show hide hidden columns
        _table.columns(hidden_columns).visible(_visible, false);
        _table.columns.adjust();
        $(main_data).toggleClass('hide');
        $(window).trigger('resize');
}

function convert_expense(pur_order,total){
    "use strict";

    $.post(admin_url + 'purchase/get_project_info/'+pur_order).done(function(response){
      response = JSON.parse(response);
      $('select[name="project_id"]').val(response.project_id).change();
      $('select[name="clientid"]').val(response.customer).change();
      $('select[name="currency"]').val(response.currency).change();
      $('input[name="vendor"]').val(response.vendor);
    });

    $('#pur_order_expense').modal('show');
    $('input[id="amount"]').val(total);
    $('#pur_order_additional').html('');
    $('#pur_order_additional').append(hidden_input('pur_order',pur_order));
}

function projectExpenseSubmitHandler(form) {
    "use strict";
      $.post(form.action, $(form).serialize()).done(function(response) {
          response = JSON.parse(response);
          if (response.expenseid) {
              if (typeof(expenseDropzone) !== 'undefined') {
                  if (expenseDropzone.getQueuedFiles().length > 0) {
                      expenseDropzone.options.url = admin_url + 'expenses/add_expense_attachment/' + response.expenseid;
                      expenseDropzone.processQueue();
                  } else {
                      window.location.assign(response.url);
                  }
              } else {
                  window.location.assign(response.url);
              }
          } else {
              window.location.assign(response.url);
          }
      });
      return false;
}

function change_delivery_status(status, id){
  "use strict";
  if(id > 0){
    $.post(admin_url + 'purchase/change_delivery_status/'+status+'/'+id).done(function(response){
      response = JSON.parse(response);
      if(response.success == true){
        if($('#status_span_'+id).hasClass('label-danger')){
          $('#status_span_'+id).removeClass('label-danger');
          $('#status_span_'+id).addClass(response.class);
          $('#status_span_'+id).html(response.status_str+' '+response.html);
        }else if($('#status_span_'+id).hasClass('label-success')){
          $('#status_span_'+id).removeClass('label-success');
          $('#status_span_'+id).addClass(response.class);
          $('#status_span_'+id).html(response.status_str+' '+response.html);
        }else if($('#status_span_'+id).hasClass('label-info')){
          $('#status_span_'+id).removeClass('label-info');
          $('#status_span_'+id).addClass(response.class);
          $('#status_span_'+id).html(response.status_str+' '+response.html);
        }else if($('#status_span_'+id).hasClass('label-warning')){
          $('#status_span_'+id).removeClass('label-warning');
          $('#status_span_'+id).addClass(response.class);
          $('#status_span_'+id).html(response.status_str+' '+response.html);
        }
        alert_float('success', response.mess);
      }else{
        alert_float('warning', response.mess);
      }
    });
  }
}
